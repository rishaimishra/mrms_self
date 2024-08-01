<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyGeoRegistry;
use App\Models\District;
use App\Jobs\PropertyStickers;
use App\Models\PropertyPayment;
use App\Models\PropertyReversePayment;
use App\Notifications\PaymentSMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function show(Request $request)
    {
        // return "sd";
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];

        if (
            $request->input('digital_address')
            || $request->input('old_digital_address')
            || $request->input('property_id')
        ) {

            $address = explode('%', $request->input('digital_address') ? $request->input('digital_address') : $request->input('old_digital_address'));

            if ($request->filled('property_id')) {
                $address[0] = $request->input('property_id');
            }

            $PropertyGeoRegistry = PropertyGeoRegistry::with(['property'])->whereHas('property', function ($query) use ($request, $address) {
                return $query->where('id', $address[0]);
            })->first();

            if ($PropertyGeoRegistry && $request->input('old_digital_address') && $address[1] != $PropertyGeoRegistry->digital_address && $PropertyGeoRegistry->old_digital_address != $PropertyGeoRegistry->digital_address) {
                return redirect()->route('admin.payment')->with($this->setMessage('Digital Address has been updated. Please print new demand draft and search by new digital address.', self::MESSAGE_SUCCESS));
            }
            if (request()->user()->hasRole('Super Admin')) {
                if ($PropertyGeoRegistry) {
                    $property = Property::with([
                        'landlord',
                        'occupancy',
                        'assessment',
                        'geoRegistry',
                        'assessmentHistory'
                    ])->find($PropertyGeoRegistry->property->id);
                    if ($property) {
                        $paymentInQuarter = $property->getPaymentsInQuarter();
                    }
                } else {
                    $property = new Property();
                    $paymentInQuarter = array();
                }
            } else {
                if ($PropertyGeoRegistry) {
                    $property = Property::where('district', request()->user()->assign_district)->with([
                        'landlord',
                        'occupancy',
                        'assessment',
                        'geoRegistry',
                        'assessmentHistory'
                    ])->find($PropertyGeoRegistry->property->id);
                    if ($property) {
                        $paymentInQuarter = $property->getPaymentsInQuarter();
                    }
                } else {
                    $property = new Property();
                    $paymentInQuarter = array();
                }
            }
        }

        $propertyId = $request->input('property_id');

        $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

        $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();


        $digital_address = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select Digital Address', '');
        // return "asdfdsa";
        // return $property;
        return view('admin.payments.view', compact('property', 'digital_address', 'paymentInQuarter', 'history','pensioner_image_path','disability_image_path'));
    }

    public function store($id, Request $request)
    {
        // return $request;
        $property = Property::with('landlord')->findOrFail($id);

        $this->validate($request, [
            'amount' => 'required',
            'penalty' => 'nullable',
            'payment_type' => 'required|in:cash,cheque',
            'cheque_number' => 'nullable|required_if:payment_type,cheque|digits_between:5,10',
            'payee_name' => 'required|max:250'
        ]);

         $t_amount = $request->amount;
        //$t_penalty = intval(str_replace(',', '', $request->input('penalty', 0)));

        $t_penalty = 0;

         $balance = $property->getBalance();

        //        if ($property->payments()->count() >= 3) {
        //            $amount = $t_amount;
        //
        //            if ( $balance != $amount && $balance > 0) {
        //                return redirect()->back()->with($this->setMessage('You should deposit all the remaining amount in this month only.', self::MESSAGE_ERROR))->withInput();
        //            }
        //        }

        $admin = $request->user('admin');

        $data = $request->only([
            'payment_type',
            'cheque_number',
            'payee_name',
            'payment_fulfilment_type'
        ]);

        $value = $property->assessment->getCurrentYearTotalDue();

        // Ensure the value is numeric
        if (!is_numeric($value)) {
            $value = 0;
        }
      
        // Format the value
        $data['assessment'] = $value;
        $data['admin_user_id'] = $admin->id;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        $data['balance'] = $data['assessment']; // For Activity log tracking
        //$data['penalty'] = $t_penalty;
      $data['payment_fulfilment'] = $request->payment_fulfilment_type;
    //   return $data;
        $payment = $property->payments()->create($data);
        $payment->save();
        // return $payment;
         $property2 = Property::with('landlord')->findOrFail($id);
         $t_balance = $property2->assessment->getCurrentYearTotalDue();

         $payment->balance = $t_balance;

        $payment->save();

        if ($mobile_number = $property->landlord->mobile_1) {
            //$property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $mobile_number)) {
                $property->landlord->notify(new PaymentSMSNotification($property, $mobile_number, $payment));
            }
        }

        return redirect()->route('admin.payment.pos.receipt', ['id' => $property->id, 'payment_id' => $payment->id]);
    }

    public function getReceipt($id, $year = null)
    {
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

        $assessment->setPrinted();

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter($year);
        $district = District::where('name', $property->district)->first();
        $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));


        return view('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));

        //return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');


    }

    public function emailReceipt($id, $year = null)
    {
        $year = !$year ? date('Y') : $year;

        $property = Property::with('assessment', 'occupancy', 'types', 'geoRegistry', 'user')->findOrFail($id);
        if ($property->landlord->email) {
            Mail::send('vendor.mail.html.receipt', ['name' => $property->landlord->getName(), 'year' => $year], function ($message) use ($property, $year) {

                $assessment = $property->assessments()->whereYear('created_at', $year)->firstOrFail();

                $assessment->setPrinted();

                $paymentInQuarter = $property->getPaymentsInQuarter($year);
                $district = District::where('name', $property->district)->first();
                $pdf = \PDF::loadView('admin.payments.receipt', compact('property', 'paymentInQuarter', 'assessment', 'district'));

                $message->to($property->landlord->email, $property->landlord->getName())->subject('WARDC - Demand Note');

                $message->from('no-reply@sigmaventuressl.com', 'WARDC');

                $message->attachData($pdf->output(), 'DemandNote.pdf');
            });
            return back()->with('success', 'Email successfully sent.');
        } else {
            return back()->with('error', 'Email address not found.');
        }
    }

    public function getStickers($id, $year = null, Request $request)
    {
        $year = !$year ? date('Y') : $year;

        $request->request->set('demand_draft_year', $year);

        $nProperty = Property::with([
            'user',
            'landlord',
            'assessment' => function ($query) use ($year) {
                if ($year) {
                    $query->whereYear('created_at', $year);
                }
            },
            'geoRegistry',
            'user',
            'occupancies',
            'propertyInaccessible',
            'payments'
        ])->where('properties.id', $id)
            ->withAssessmentCalculation($year)
            ->having('total_payable_due', 0)
            ->orderBy('total_payable_due')->get();

        $stickers = new PropertyStickers();
        //dd($nProperty);
        return $stickers->handle($nProperty, $request);
    }

    public function getPosReceipt($id, $payment_id)
    {
        $property = Property::findOrFail($id);

        //        $pdf = \PDF::loadView('admin.payments.receipt');
        //        return $pdf->download('invoice.pdf');

        $paymentInQuarter = $property->getPaymentsInQuarter();

        $payment = $property->payments()->findOrFail($payment_id);

        $property->load([
            'assessment' => function ($query) use ($payment) {
                $query->whereYear('created_at', $payment->created_at->format('Y'));
            },
            'occupancy',
            'types',
            'geoRegistry',
            'landlord'
        ]);
        $district = District::where('name', $property->district)->first();
        return view('admin.payments.pos-receipt', compact('property', 'paymentInQuarter', 'payment','district'));
    }

    public function edit($id)
    {
        $payment = PropertyPayment::findOrFail($id);

        $propertyId = PropertyPayment::where('id',$id)->value('property_id');
        
        $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

        $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();

        $property = $payment->property;

        return view('admin.payments.edit', compact('payment', 'property','pensioner_image_path','disability_image_path'));
    }
    public function reverse_edit($id)
    {
        // return $id;
        $payment = PropertyReversePayment::where('transaction_id',$id)->first();

        $propertyId = $payment->property_id;
        
        $pensioner_image_path = PropertyReversePayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

        $disability_image_path = PropertyReversePayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();

        $property = $payment->property;

        return view('admin.payments.edit', compact('payment', 'property','pensioner_image_path','disability_image_path'));
    }

    public function verify($id)
    {
        $payment = PropertyPayment::findOrFail($id);

        $property = $payment->property;

        return view('admin.payments.verify', compact('payment', 'property'));
    }
    public function update($id, Request $request)
    {
        $payment = PropertyPayment::findOrFail($id);

        $property = $payment->property;


        $this->validate($request, [
            'assessment' => 'required',
            'amount' => 'required',
            //'penalty' => 'nullable',
            'payment_type' => 'required|in:cash,cheque',
            'cheque_number' => 'nullable|required_if:payment_type,cheque|digits_between:5,10',
            'payee_name' => 'required|max:250',
            'created_at' => 'required',
        ]);

        $t_amount = intval(str_replace(',', '', $request->amount));
        //$t_penalty = intval(str_replace(',', '', $request->penalty));
        $t_penalty = 0;
        $t_assessment = intval(str_replace(',', '', $request->assessment));
        $admin = $request->user('admin');

        $data = $request->only([
            'payment_type',
            'cheque_number',
            'payee_name',
            'pensioner_discount_approve',
            'disability_discount_approve'
        ]);


        $data['admin_user_id'] = $admin->id;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        $data['assessment'] = $t_assessment;
        $data['created_at'] = $request->created_at;
        $data['updated_at'] = $request->created_at;
        $data['balance'] = $t_assessment - ($t_amount + $t_penalty);


        $payment->fill($data);
        $payment->created_at = $request->created_at;
        $payment->updated_at = $request->created_at;
        $payment->save(['timestamps' => false]);

        //$this->updatePayments($property, $payment);
        return redirect()->route('admin.payment', ['property_id' => $property->id])->with($this->setMessage('Transaction successfully done.', self::MESSAGE_SUCCESS));
        //return back()->with($this->setMessage('Transaction successfully done.', self::MESSAGE_SUCCESS));
    }

    public function updatePayments($property, $payment)
    {
        $totalPaid = $property->payments()->orderBy('created_at', 'asc')->where('id', '<=', $payment->id)->sum('amount');
        $balance = $property->assessments->sum('property_rate_without_gst') - $totalPaid;

        $payment->balance = $balance;
        $payment->save();


        //        if ($payments->count() && $payments->count() != 1) {
        //            foreach ($payments as  $payment) {
        //                $previous = $this->getPreviousPayment($property, $payment);
        //
        //                if ($previous) {
        //                    $payment->balance = $previous->balance - $payment->amount;
        //                    $payment->save();
        //                } else {
        //                    $payment->balance = $payment->assessment - $payment->amount;
        //                }
        //
        //                $payment->save();
        //            }
        //        }

        return;
    }

    public function getPreviousPayment($property, $payment)
    {
        $previous = $property->payments()->where('id', '>', $payment->id)->orderBy('id', 'desc')->first();

        return $previous;
    }

    public function delete($id)
    {
        $payment = PropertyPayment::findOrFail($id);

        $property = $payment->property;

        $payment->delete();

        //$this->updatePayments($property, $id);

        return back()->with($this->setMessage('Payment successfully deleted', 2));
    }
    public function reverse(Request $request, $id){
        
       $payment = PropertyPayment::findOrFail($id);
       if($payment){
        $r_payment = new PropertyReversePayment();
        $property = $payment->property;

        $t_amount = intval(str_replace(',', '', $payment->amount));
        //$t_penalty = intval(str_replace(',', '', $payment->penalty));
        $t_penalty = 0;
        $t_assessment = intval(str_replace(',', '', $payment->assessment));
        $admin = $request->user('admin');

        $data = $request->only([
            '',
            '',
            '',
            '',
            ''
        ]);
        // return $property->id;
        $data['property_id'] = $property->id;
        $data['transaction_id'] = $payment->id;
        $data['payment_type'] = $payment->payment_type;
        $data['cheque_number'] = $payment->cheque_number;
        $data['payee_name'] = $payment->payee_name;
        $data['pensioner_discount_approve'] = $payment->pensioner_discount_approve;
        $data['disability_discount_approve'] = $payment->disability_discount_approve;
        $data['physical_receipt_image'] = $payment->physical_receipt_image;
        $data['pensioner_discount_image'] = $payment->pensioner_discount_image;
        $data['disability_discount_image'] = $payment->disability_discount_image;
        $data['admin_user_id'] = $admin->id;
        $data['total'] = $t_amount + $t_penalty;
        $data['amount'] = $t_amount;
        $data['assessment'] = $t_assessment;
        $data['created_at'] = $payment->created_at;
        $data['updated_at'] = $payment->created_at;
        $data['balance'] = $t_assessment - ($t_amount + $t_penalty);


        $r_payment->fill($data);
        // $payment->created_at = $request->created_at;
        // $payment->updated_at = $request->created_at;
        $r_payment->save(['timestamps' => false]);
        $payment->delete();
        return redirect()->route('admin.payment', ['property_id' => $property->id])->with($this->setMessage('Transaction reverse successfully.', self::MESSAGE_SUCCESS));
        // return back()->with($this->setMessage('Payment reverse successfully', 1));
       }
       else{
        return back()->with($this->setMessage('Payment reverse failed', 2));
       }
  
       
    }
    public function show_reverse(Request $request){
        $property = [];
        $last_payment = null;
        $paymentInQuarter = [];
        $history = [];

        if (
            $request->input('digital_address')
            || $request->input('old_digital_address')
            || $request->input('property_id')
        ) {

            $address = explode('%', $request->input('digital_address') ? $request->input('digital_address') : $request->input('old_digital_address'));

            if ($request->filled('property_id')) {
                $address[0] = $request->input('property_id');
            }

            $PropertyGeoRegistry = PropertyGeoRegistry::with(['property'])->whereHas('property', function ($query) use ($request, $address) {
                return $query->where('id', $address[0]);
            })->first();

            if ($PropertyGeoRegistry && $request->input('old_digital_address') && $address[1] != $PropertyGeoRegistry->digital_address && $PropertyGeoRegistry->old_digital_address != $PropertyGeoRegistry->digital_address) {
                return redirect()->route('admin.payment')->with($this->setMessage('Digital Address has been updated. Please print new demand draft and search by new digital address.', self::MESSAGE_SUCCESS));
            }
            if (request()->user()->hasRole('Super Admin')) {
                if ($PropertyGeoRegistry) {
                    $property = Property::with([
                        'landlord',
                        'occupancy',
                        'assessment',
                        'geoRegistry',
                        'assessmentHistory'
                    ])->find($PropertyGeoRegistry->property->id);
                    if ($property) {
                        $paymentInQuarter = $property->getPaymentsInQuarter();
                    }
                } else {
                    $property = new Property();
                    $paymentInQuarter = array();
                }
            } else {
                if ($PropertyGeoRegistry) {
                    $property = Property::where('district', request()->user()->assign_district)->with([
                        'landlord',
                        'occupancy',
                        'assessment',
                        'geoRegistry',
                        'assessmentHistory'
                    ])->find($PropertyGeoRegistry->property->id);
                    if ($property) {
                        $paymentInQuarter = $property->getPaymentsInQuarter();
                    }
                } else {
                    $property = new Property();
                    $paymentInQuarter = array();
                }
            }
        }

        $propertyId = $request->input('property_id');

        $pensioner_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('pensioner_discount_image')->orderBy('created_at','desc')->first();

        $disability_image_path = PropertyPayment::where('property_id','=',$propertyId)->whereNotNull('disability_discount_image')->orderBy('created_at','desc')->first();


        $digital_address = PropertyGeoRegistry::distinct()->orderBy('property_id')->pluck('digital_address', 'digital_address')->sort()->prepend('Select Digital Address', '');
        $reverse_payments = PropertyReversePayment::get();
        return view('admin.payments.reverse_view', compact('property', 'digital_address', 'paymentInQuarter', 'history','pensioner_image_path','disability_image_path','reverse_payments'));
    }
}

<?php

namespace App\Http\Controllers\APIV2\General;

use App\Http\Controllers\API\ApiController;
use App\Models\User;
use App\Models\Property;
use App\Models\UserTitleTypes;
use App\Types\ApiStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\PropertyPayment;

class AppUserController extends ApiController
{
    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $user = $request->user();

        if ($request->hasFile('image'))
            $user->image = $request->file('image')->store(User::USER_IMAGE);

        $user_name = $request->input('name');
        $user_title = UserTitleTypes::where('id',$request->input('user_title_id'))->value('label');
        $user->name = $user_title.". ".$user_name;
        $user->save();

        return $this->success([
            'user' => $user,
        ]);

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

        return view('admin.payments.pos-receipt', compact('property', 'paymentInQuarter', 'payment'));
    }

    public function getallrecipt($id){
        $data=PropertyPayment::where('property_id',$id)->orderBy('id','desc')->pluck('id')->toArray();
        $unique=[];
        foreach($data as $val){
            $url['url']="http://3.134.197.245/apiv2/payment/pos/receipt/".$id."/".$val;
            $url['id']=$val;
            array_push($unique, $url);
        }

        return $this->success([
            "datas" => $unique
        ]);

       // dd($unique);
    }
    public function getOcupencyType(){
        $data['occupancy_type'] = [ 'Owned Tenancy',  'Rented House',  'Unoccupied House'];
         $data['titles'] = UserTitleTypes::get();
        return $this->success([
             "datas" => $data
         ]);
     }

     public function editOcupency(Request $request){

        $up1= \DB::table('occupancy_details')
             ->where('property_id',$request->property_id)
             ->update(['tenant_first_name' => $request->tenant_first_name, 'middle_name' => $request->middle_name, 'surname' => $request->surname, 'mobile_1' => $request->mobile_1, 'mobile_2' => $request->mobile_2, 'ownerTenantTitle'=>$request->ownerTenantTitle ]);    
 
        $up2= \DB::table('property_occupancies')
             ->where('property_id',$request->property_id)
             ->update(['occupancy_type' => $request->occupancy_type ]);  
 
             return $this->success([
               "success" => "updated"
             ]);   
     }

}

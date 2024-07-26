<?php

namespace App\Http\Controllers\APIV2\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Logic\SystemConfig;
use App\Models\FormAndResourse;
use App\Models\Complaint;
use App\Models\EmergencyAndService;
use App\Models\EmergencyAndServiceImages;
use App\Models\Headlines;
use App\Models\HeadlineImages;
use App\Models\GarbageCollection;
use App\Models\GarbageDate;
use App\Models\GarbageDateSlot;

class EcpController extends Controller
{
    public function get_formresources(){
        $form_data = FormAndResourse::get();
        // dd($form_data[0]->form_image);
        foreach ($form_data as $key => $value) {
            # code...
            $path = $value->form_image;
            $pdfPath = url('storage/' . $path);
            $value->form_image = $pdfPath;
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Form and Resourses get successfully',
            'data' => $form_data
        ]);
    }
    public function get_complaints(){
        $complaints = Complaint::get();
        return response()->json([
            'status' => 'success',
            'message' => 'Form and Resourses get successfully',
            'data' => $complaints
        ]);
    }
    public function add_complain(Request $request){
        // return $request;
        $complain = new EmergencyAndService();
        $complain->information = $request->information;
        $complain->reason = $request->reason;
        $complain->additional_description = $request->additional_information;
        $complain->tag = $request->tag;
        $complain->user_id = $request->user_id;
        $complain->type = $request->type;
        $complain->save();
        if ($request->hasFile('complain_image')) {
            foreach ($request->File('complain_image') as $key => $file) {

                $path = $file->store('complain_images', 'public');
                    $complain_image = new EmergencyAndServiceImages();
                    $complain_image->complain_image = $path;
                    $complain_image->form_and_resourses_id = $complain->id;
                    $complain_image->save();
                }
         }
         return response()->json([
            'status' => 'success',
            'message' => 'Complain added successfully',
        ]);
    }
    public function get_newsletter(){
        // return "newsleter";
        $news_letter = Headlines::where('status',1)->with('HeadingImages')->get();
       return response()->json([
        'status' => 'success',
        'message' => 'Form and Resourses get successfully',
        'data' => $news_letter
    ]);
    }
    public function garbage_collection(Request $request){
        // return $request;
        $gc = new GarbageCollection();
        $gc->date = date('Y-m-d', strtotime($request->date));
        $gc->slot = $request->slot;
        $gc->latlng = $request->latlng;
        $gc->request = 0;
        $gc->user_id = $request->user_id;
        if ($request->hasFile('garbage_image_1')) {
            $file = $request->file('garbage_image_1');
            $path = $file->store('garbage_images', 'public');
            $gc->garbage_image_1 = $path;
        }
        if ($request->hasFile('garbage_image_2')) {
            $file = $request->file('garbage_image_2');
            $path = $file->store('garbage_images', 'public');
            $gc->garbage_image_2 = $path;
        }
        $gc->garbage_date_id = $request->garbage_date_id;
        $gc->garbage_date_slot_id = $request->garbage_date_slot_id;
        $gc->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Garbage collection slot added successfully',
        ]);
    }
    public function get_admin_dates(){
        // return "called";
       return  $dates = GarbageDate::select('id','date')->where('date','>',now())->with(['get_slot'=> function($query){
            $query->select('id','garbage_date_id','slots');
        }])->get();
        if ($dates) {
            return response()->json([
                'status' => 'success',
                'message' => 'Garbage collection dates and slots found successfully',
                'data' => $dates
            ]);
        }
        else{
            return response()->json([
                'status' => 'success',
                'message' => 'No dates found'
            ]);
        }
    }
}


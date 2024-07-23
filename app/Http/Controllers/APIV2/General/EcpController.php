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
       return $news_letter = Headlines::where('status',1)->with('HeadingImages')->get();
    }
    public function garbage_collection(Request $request){
        // return $request;
        $gc = new GarbageCollection();
        $gc->date = date('Y-m-d', strtotime($request->date));
        $gc->slot = $request->slot;
        $gc->latlng = $request->latlng;
        $gc->request = 0;
        $gc->user_id = $request->user_id;
        $gc->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Garbage collection slot added successfully',
        ]);
    }
  
}


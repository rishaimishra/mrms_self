<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\DistrictsGrid;
use App\Grids\ComplaintGrid;
use App\Grids\GarbageGrid;
use App\Grids\NewsletterGrid;
use App\Grids\InformationGrid;
use App\Models\AdminUser;
use App\Models\District;
use App\Models\Property;
use App\Models\Headlines;
use App\Models\HeadlineImages;
use App\Models\Complaint;
use App\Models\EmergencyAndService;
use App\Models\FormAndResourse;
use App\Models\GarbageCollection;
use App\Models\User;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\GarbageDate;
use App\Models\GarbageDateSlot;
use App\Models\InformationTip;
use DateTime;

class FormAndResourseController extends Controller
{
    public function index(){
        $form_resources = FormAndResourse::all();
       return view('admin.cep.formsandresourses',compact('form_resources'));
    }
    public function form_store(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'form_name' => 'required|string|max:255',
            'form_img' => 'required',
        ]);
        $form_data = new FormAndResourse();
        $form_data->form_name = $request->form_name;
        $form_data->user_id = request()->user()->id;
       $form_data->status = 1;
       if ($request->hasFile('form_img')) {
        $file = $request->file('form_img');
        $path = $file->store('form_images', 'public');
        $form_data->form_image = $path;
         }
         $form_data->save();
         return back()->with('success', 'form added successfully.');
    }
    public function information_tips_index(InformationGrid $informationGrid,Request $request){
        $this->information = InformationTip::with('get_user');

        $data['title'] = 'Information and tips';

        $data['request'] = $request;
        return $informationGrid->create(['query' => $this->information, 'request' => $request])->renderOn('admin.cep.informationandtips', $data);
        // return view('admin.cep.informationandtips');
    }
    public function add_information_tips(Request $request){
        // return $request;
        $tip = new InformationTip();
        $tip->tip = $request->information_tip;
        $tip->user_id=request()->user()->id;
        $tip->status=1;
        $tip->save();
        return back()->with('success', 'information and tips added successfully.');
    }
    public function information_tips_show(Request $request){
        // return $request->information_collection;
        $information = InformationTip::where('id',$request->information_collection)->with('get_user')->first();
      return view('admin.cep.information_and_tips_show',compact('information'));
    }
    public function information_tips_delete(Request $request){
         $information = InformationTip::where('id',$request->information_collection)->with('get_user')->first();
        $information = InformationTip::where('id',$request->information_collection)->first();
       
        $information->delete();
        return redirect()->back()->with($this->setMessage('Information and tips successfully deleted', true));
    }
    public function newsletter(NewsletterGrid $newsGrid, Request $request){
        // return "hello";
        $this->headlines = Headlines::with('HeadingImages');

        $data['title'] = 'Details';

        $data['request'] = $request;
        // return view('admin.cep.newsletter');
        return $newsGrid->create(['query' => $this->headlines, 'request' => $request])->renderOn('admin.cep.newsletter', $data);
    }
    public function newsletter_show(Request $request){
        // return $request->newsletter_collection;
         $hd = Headlines::where('id',$request->newsletter_collection)->with('HeadingImages')->first();
        
        return view('admin.cep.newsletter_show',compact('hd'));
    }
    public function newsletter_delete(Request $request){
        // return $request->newsletter_collection;
        $newsletter = Headlines::where('id',$request->newsletter_collection)->first();
        if ($newsletter->id) {
            $newsletter_images = HeadlineImages::where('headline_id',$newsletter->id)->delete();
        }
        $newsletter->delete();
        return redirect()->back()->with($this->setMessage('Newsletter successfully deleted', true));
    }
    public function garbage_collection(Request $request){
        // return $request->garbage_collection;
         $gc = GarbageCollection::where('id',$request->garbage_collection)->first();
         $gc_date = $gc->date;
         $formatted_date = date('D M d Y', strtotime($gc_date));
        return view('admin.cep.garbageCollection',compact('gc','formatted_date'));
    }
    public function complaint_listing(ComplaintGrid $complainGrid, Request $request){
        // $this->complaints = Complaint::paginate(15);
         $this->complaints = EmergencyAndService::with('get_user');

        $data['title'] = 'Details';

        $data['request'] = $request;
        // return "sdf";
        return $complainGrid->create(['query' => $this->complaints, 'request' => $request])->renderOn('admin.cep.complaintListing', $data);
    }
    public function newsletter_listing(NewsletterGrid $newsGrid, Request $request){
        // $this->complaints = Complaint::paginate(15);
        return "hello";
         $this->headlines = Headline::with('get_user');

        $data['title'] = 'Details';

        $data['request'] = $request;
        // return "sdf";
        return $newsGrid->create(['query' => $this->headlines, 'request' => $request])->renderOn('admin.cep.newsletter', $data);
    }
    public function headline_store(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'headline' => 'required|string|max:255',
            'story_board' => 'required',
            'headline_image' => 'required',
            'headlineimg' => 'required',
            'editor_name' => 'required'
        ]);
         $headline = new Headlines();
        $headline->headline = $request->headline;
        $headline->editor = $request->editor_name;
        if ($request->hasFile('headline_image')) {
            $file = $request->file('headline_image');
            $path = $file->store('form_images', 'public');
            $headline->headline_img = $path;
             }
             $headline->user_id = request()->user()->id;
             $headline->status = 1;
             $headline->story = $request->story_board;
             $headline->save();
             if ($request->hasFile('headlineimg')) {
                foreach ($request->File('headlineimg') as $key => $file) {
                    $path = $file->store('headlineimg', 'public');
                        $headline_image = new HeadlineImages();
                        $headline_image->images = $path;
                        $headline_image->headline_id = $headline->id;
                        $headline_image->save();
                    }
             }
             return back()->with('success', 'headline added successfully.');
    }
    public function complaint_listing_show(Request $request){
        // return $request->complaint;
         $complaint = EmergencyAndService::where('id',$request->complaint)->with('get_user','ComplainImages')->first();
        // return "sad";
        // return $complaint_images = $complaint->complain_images;
        $title = 'Details';
        return view('admin.cep.compliantView',compact('complaint','title'));
    }
    public function complaint_listing_delete(Request $request){
        // return $request->complaint;
        $complaint = EmergencyAndService::where('id',$request->complaint)->first();
        $complaint->delete();
        return redirect()->back()->with($this->setMessage('Complaint successfully deleted', true));
    }
    public function delete_form_resources($id){
        // return $id;
        if($id){
            $form_resourse = FormAndResourse::where('id',$id)->first();
            $form_resourse->delete();
            return back()->with('success', 'form deleted successfully.');
        }
      else{
        return back()->with('error', 'form not found.');
      }
    }
    public function edit_form_resources($id){
        // return $id;
        $form_resourse = FormAndResourse::where('id',$id)->first();
      return view('admin.cep.editformandresourses',compact('form_resourse'));
    }
    public function update_form_resources(Request $request){
        // return $request;
         $form_resourse = FormAndResourse::where('id',$request->form_id)->first();
            $form_resourse->form_name = $request->form_name;
            if ($request->hasFile('form_img')) {
                $file = $request->file('form_img');
                $path = $file->store('form_images', 'public');
                $form_resourse->form_image = $path;
                 }
            else{
                $form_resourse->form_image = $form_resourse->form_image;
            }
            $form_resourse->save();
            return redirect()->route('admin.forms-resourses')->with('success', 'form updated successfully.');
    }
    public function garbage_collection_list(GarbageGrid $garbageGrid, Request $request){
         
        $garbage_dates = GarbageDate::select('date')->distinct()->get();
        $formatted_date = [];
        
        foreach ($garbage_dates as $date) {
            // Access the 'date' property and format it
            $gc_date = $date->date;
            $formatted_date[] = date('D M d Y', strtotime($gc_date));
        }
        
        // return $formatted_date;
        $this->gc = GarbageCollection::with('get_user');

        $data['title'] = 'Garbage Collection Detail';

        $data['request'] = $request;
        $data['formatted_date'] = $formatted_date ;
        // return "sdf";
        return $garbageGrid->create(['query' => $this->gc, 'request' => $request])->renderOn('admin.cep.garbagetListing', $data);
    }
    public function change_garbage_collection(Request $request){
        // return $request;
        $selectedDatesTimes = $request->input('selectedDatesTimes');

        foreach ($selectedDatesTimes as $dateTime) {
            $date = DateTime::createFromFormat('m/d/Y', $dateTime['date'])->format('Y-m-d');
            $times = $dateTime['times'];
    
            // Check if the date already exists in the GarbageDate model for the current user
            $existingGarbageDate = GarbageDate::where('date', $date)
                                              ->where('user_id', $request->user()->id)
                                              ->first();
    
            if (!$existingGarbageDate) {
                // Store the date in the GarbageDate model
                $garbageDate = new GarbageDate();
                $garbageDate->date = $date;
                $garbageDate->user_id = $request->user()->id;
                $garbageDate->status = 1;
                $garbageDate->save();
    
                // Store each time slot in the GarbageDateSlot model with the date ID
                foreach ($times as $time) {
                    $garbageDateSlot = new GarbageDateSlot();
                    $garbageDateSlot->garbage_date_id = $garbageDate->id;
                    $garbageDateSlot->slots = $time;
                    $garbageDateSlot->user_id = $request->user()->id;
                    $garbageDateSlot->status = 1;
                    $garbageDateSlot->save();
                }
            }
            else{
                GarbageDateSlot::where('garbage_date_id',$existingGarbageDate->id)->delete();
                foreach ($times as $time) {
                    $garbageDateSlot = new GarbageDateSlot();
                    $garbageDateSlot->garbage_date_id = $existingGarbageDate->id;
                    $garbageDateSlot->slots = $time;
                    $garbageDateSlot->user_id = $request->user()->id;
                    $garbageDateSlot->status = 1;
                    $garbageDateSlot->save();
                }
            }
        }
    }
    public function check_garbage_collection(Request $request){
        // return $request;
        // $selectedDate = $request->input('date');
        $response = [];
        $date = DateTime::createFromFormat('m/d/Y', $request->input('date'))->format('Y-m-d');
       $existingGarbageDate = GarbageDate::where('date', $date)->with('get_slot')
       ->where('user_id', $request->user()->id)
       ->first();
       if($existingGarbageDate){
        $response['status'] = 'success';
        $response['data'] = $existingGarbageDate;
       }else{
        $response['status'] = 'error';
        $response['msg'] = 'No slot found.';
       }


       return $response;

    }
    function upload(Request $request)
    {
        $image = $request->upload;
        $imagename = str_replace(' ', '', 'tuorial' . time() . $image->getClientOriginalName());
        $image->move(public_path('uploads/post/'), $imagename);
        $url = asset('uploads/post/' . $imagename);
        $CKEditorFuncNum = $request->input('CKEditorFuncNum');
        $response = "<script>window.parent.CKEDITOR.tools.callFunction( $CKEditorFuncNum  ,  '$url' ,'uploaded')</script>";
        @header('Content-Type:text/html;charset-utf-8');
        echo $response;
    }
}

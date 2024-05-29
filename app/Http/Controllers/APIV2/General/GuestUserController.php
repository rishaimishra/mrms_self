<?php

namespace App\Http\Controllers\APIV2\General;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserTitleTypes;
use App\Models\District;
use App\Types\ApiStatusCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
class GuestUserController extends Controller
{

    public $successStatus = 200;

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required',
            
        ]);

        if ($validator->fails()) { 
            return response()->json(['success'=>false,'message'=>"Username or Email Already Taken"], 200);      
        }

        $input = $request->all();

       $input['name'] = $request->input('name');
       $input['image'] = 'NA';
       $input['ward'] = 'NA';
       $input['constituency'] = 'NA';
       $input['section'] = 'NA';
       $input['chiefdom'] = 'NA';
       $input['district'] = 'NA';
       $input['province'] = 'NA';
       $input['gender'] = 'NA';
       $input['is_active'] = 0;
       $input['username'] = $request->input('username');
       $input['email'] = $request->input('email');
       $input['phone'] = $request->input('phone');
       $input['password'] = bcrypt($request->input('password'));
    //    $input['save']();

        $user = User::create($input); 

        $success['name'] =  $user->name;
        $success['email'] =  $user->email;
        $success['phone'] =  $user->phone;
        return response()->json(['success'=>$success], $this-> successStatus);

       

    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>'username or password not found'], 201); 
        }

    //    dd(request('password'));

        if (Auth::attempt(['username' => request('username'), 'password' => request('password')])) {

            /* @var $user User */

            $user = Auth::user();
            $tokenResult = $user->createToken('Person Access Token');
            

            return response()->json([
                'token' => $tokenResult->accessToken,
                'auth_type' => "Bearer",
                'expired_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => $user->toArray()
            ]);
        } else {
            return response()->json(['error'=>'Invalid credentials'], 201);
        }
    }

    public function areaSearch(Request $request){
        // Fetching users where the data contains a specific value
        $searchValue = $request->search;
        // dd($searchValue);
        $filteredUsers = District::hasJob($searchValue)->get();
        // dd($filteredUsers);

        // Return the filtered names in JSON format
        foreach ($filteredUsers as $district) {
           
            $district['primary_logo'] = $district->getPrimaryLogoUrl();
              
        }
        return response()->json(['result'=>$filteredUsers], 201);
        // return response()->json($filteredUsers);
    }

    public function areaNames(){
        $district = District::pluck('area');
       // Remove the square brackets from the beginning and end of the JSON string
        $jsonString = trim($district, '[]');

        // Split the string by comma to get individual elements
        $elements = explode(',', $jsonString);

        $cleanArray = [];

        foreach ($elements as $item) {
            // Replace unwanted characters and trim whitespace
            $cleanItem = trim(str_replace(['"', '[', '\\', ']', '','n   '], '', $item));
            
            // Add the clean item to the clean array
            $cleanArray[] = $cleanItem;
        }

        return response()->json(['result'=>$cleanArray], 201); 
    }

}

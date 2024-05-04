<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Types\ApiStatusCode;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const MESSAGE_SUCCESS = 1;
    const MESSAGE_ERROR = 2;
    public function __construct() {
        // Device id check start
        // $routePrefix = ltrim(\Route::getCurrentRoute()->getPrefix(),'/');
        // list($prefix) = explode('/', $routePrefix);        
        // if($prefix=='apiv2'){
        //     // $routeArray = \Route::getCurrentRoute()->getAction();   
        //     // $controllerAction = class_basename($routeArray['controller']);
        //     // list($controller, $action) = explode('@', $controllerAction); 
        //     //dd($routeArray);
        //     //echo \Route::getCurrentRoute()->getAction();   
        //     $uriPath = \Route::current()->uri();
        //     $actionUri = str_replace($prefix.'/', '', $uriPath);
        //     $ignoreActionsArr = ['login'];
        //     if(!in_array($actionUri, $ignoreActionsArr)){
        //         $this->middleware(function ($request, $next) {
                  
        //         //dd(auth()->user()->device_id); 
        //         // dd(\Request()->device_id);   
        //         // dd(auth()->user());

        //         if(\Request()->device_id==''){
        //             return $this->error(ApiStatusCode::VALIDATION_ERROR, [
        //                 'errors' => 'Device id not found'
        //             ]);
        //         }elseif(auth()->user()->device_id!=\Request()->device_id){
        //             return $this->error(501, [
        //                 'errors' => 'Logout'
        //             ]);
        //         }   
        //             // if(auth()->user()->hasRole('frontuser')){
        //             //     return redirect()->route('home')->withFlashMessage('You are not authorized to access that page.')->withFlashType('warning');
        //             // }
        //             return $next($request);
        //         });                
        //     }
 
        // }
        // Device id check end


    }
    protected function setMessage($message, $type = 1)
    {
        return [
            'alert' => [
                'type' => $type,
                'msg' => $message
            ]
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends AdminController
{
    public function __invoke()
    {
        return view('admin.auth.reset-password');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed'
        ]);

        $admin = $request->user('admin');


        if (Hash::check($request->old_password, $admin->password)) {

            $admin->password = Hash::make($request->password);

            $admin->save();

            return redirect()->back()->with($this->setMessage('Password successfully updated', self::MESSAGE_SUCCESS));


        }else{
            return redirect()->back()->withErrors(['old_password' => 'Invalid Password.']);
        }

    }



}

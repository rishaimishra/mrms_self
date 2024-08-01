<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\District;
use App\Grids\UsersGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppUserController extends Controller
{
    public function create()
    {
        $district = District::pluck('name', 'id');
        return view('admin.users.app_user_create', compact('district'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'assign_district' => 'required'
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->ward = $request->ward;
        $user->constituency = $request->constituency;
        $user->section = $request->section;
        $user->chiefdom = $request->chiefdom;
        $user->district = $request->district;
        $user->province = $request->province;
        $user->street_name = $request->street_name;
        $user->street_number = $request->street_number;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_active = $request->is_active ?: false;
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $user->assign_district_id = $district->id ?: null;
                $user->assign_district = $district->name ?: null;
            }
        }
        $user->save();

        return Redirect()->route('admin.app-user.list')->with('success', 'User Created Successfully !');
    }

    public function list(UsersGrid $usersGrid, Request $request)
    {
        $query = User::where('ward', '!=', 'NA')
            ->where('section', '!=', 'NA');
    
        if (!request()->user()->hasRole('Super Admin')) {
            $query->where('assign_district', request()->user()->assign_district);
        }
    
        return $usersGrid
            ->create(['query' => $query->latest(), 'request' => $request])
            ->renderOn('admin.users.app_user_list');
    }
    public function cep_user_list(UsersGrid $usersGrid, Request $request)
    {
        $query = User::where('ward', '=', 'NA')
            ->where('section', '=', 'NA');
    
        if (!request()->user()->hasRole('Super Admin')) {
            $query->where('assign_district', request()->user()->assign_district);
        }
    
        return $usersGrid
            ->create(['query' => $query->latest(), 'request' => $request])
            ->renderOn('admin.users.app_user_list');
    }

    public function show(Request $request)
    {
        //dd($request->adminuser);

        $data['app_user'] = User::find($request->user);
        $data['district'] =  District::pluck('name', 'id');
        return view('admin.users.app_user_update', $data);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        //dd($request->all());
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            'assign_district' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $update_data = [
            'name' => $request->name,
            'email' => $request->email,
            'ward' => $request->ward,
            'constituency' => $request->constituency,
            'section' => $request->section,
            'chiefdom' => $request->chiefdom,
            'district' => $request->district,
            'province' => $request->province,
            'street_name' => $request->street_name,
            'street_number' => $request->street_number,
            'gender' => $request->gender,
        ];
        $update_data['is_active'] = $request->is_active ?: false;

        if ($request->password != '') {
            $update_data['password'] =  Hash::make($request->password);
        }
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $update_data['assign_district_id'] = $district->id ?: null;
                $update_data['assign_district'] = $district->name ?: null;
            }
        }
        $user->fill($update_data);
        $user->save();

        //dd($admin_user);

        return Redirect()->back()->with('success', 'Updated Successful !');
    }

    public function destroy(Request $request)
    {

        $user = User::find($request->user);
        if (!$user->properties()->count()) {
            $user->delete();
            return Redirect()->route('admin.app-user.list')->with('success', 'User Deleted Successfully !');
        }

        return Redirect()->route('admin.app-user.list')->with('success', 'You can not delete the user. User is associated with the properties.');
    }
    public function resetPassword(Request $request)
    {

        //dd($request);
        $v = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'id' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }

        $user =  User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset = PasswordResetRequest::where('user_id', $request->id)->update(['process' => 1]);
        return redirect()->back()->with('success', 'Password Update Successfully !');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\District;
use App\Grids\GuestUsersGrid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GuestUserController extends Controller
{
    public function create()
    {
        $district = District::pluck('name', 'id');
        return view('admin.users.guest_user_create', compact('district'));
    }
    public function list(Request $request){
        $query = User::where([['user_type', '!=', 'simple_user']]);
        //dd($query);
        if (request()->user()->hasRole('Super Admin')) {
        } else {
            $query->where('assign_district', request()->user()->assign_district);
        }

        $user = $request->user();

        return (new GuestUsersGrid(['user' => $user]))
            ->create(['query' => $query->latest(), 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.users.guest_user_list');
    }
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            'user_role' => 'required',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:6',
            'username' => 'required|unique:admin_users,username',
            'assign_district' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors())->withInput();
        }
        $user = new User();

        $user->name = $request->first_name . $request->last_name;
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
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->is_active = $request->is_active ?: false;
        $user->user_type = "guest_user";
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $user->assign_district_id = $district->id ?: null;
                $user->assign_district = $district->name ?: null;
            }
        }

        $user->save();
        // $user->assignRole($request->user_role);

        return Redirect()->route('admin.guest-user.list')->with('success', 'Guest User Created Successfully !');
    }
    public function show(Request $request)
    {
        //dd($request->adminuser);

        $data['admin_user'] = User::find($request->guest_user);
        $data['district'] =  District::pluck('name', 'id');

        return view('admin.users.guest_user_update', $data);
    }
    public function update(Request $request)
    {
        // return $request;
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'gender' => 'required',
            'ward' => 'required',
            'constituency' => 'required',
            'section' => 'required',
            'chiefdom' => 'required',
            'district' => 'required',
            'province' => 'required',
            'street_name' => 'nullable|string|max:254',
            'street_number' => 'nullable|string|max:254',
            // 'user_role' => 'required',
            'assign_district' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        if ($request->password != '') {
            $update_data = [
                'name' => $request->first_name . $request->last_name,
                'ward' => $request->ward,
                'constituency' => $request->constituency,
                'section' => $request->section,
                'chiefdom' => $request->chiefdom,
                'district' => $request->district,
                'province' => $request->province,
                'street_name' => $request->street_name,
                'street_number' => $request->street_number,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
                'is_active' => $request->is_active ?: false
            ];
        } else {
            $update_data = [
                'name' => $request->first_name . $request->last_name,
                'ward' => $request->ward,
                'constituency' => $request->constituency,
                'section' => $request->section,
                'chiefdom' => $request->chiefdom,
                'district' => $request->district,
                'province' => $request->province,
                'street_name' => $request->street_name,
                'street_number' => $request->street_number,
                'gender' => $request->gender,
                'is_active' => $request->is_active ?: false
            ];
        }
        //dd($update_data);
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $update_data['assign_district_id'] = $district->id ?: null;
                $update_data['assign_district'] = $district->name ?: null;
            }
        }
        $user = User::findOrFail($request->id);
        $user->fill($update_data);
        $user->save();
        //dd($user);

        //dd($admin_user);
        // $user->syncRoles([$request->user_role]);

        return Redirect()->back()->with('success', 'Updated Successful !');
    }
    public function destroy(Request $request)
    {
        $admin_user = User::find($request->guest_user);

        // if (!$admin_user->payments()->count()) {
        //     $admin_user->delete();
        //     return Redirect()->route('admin.guest-user.list')->with('success', 'User Deleted Successfully !');
        // }
        if ($admin_user) {
            $admin_user->delete();
            return Redirect()->route('admin.guest-user.list')->with('success', 'User Deleted Successfully !');
        }

        return Redirect()->route('admin.guest-user.list')->with('success', 'You can not delete the user. User is associate with payments.');
    }
}

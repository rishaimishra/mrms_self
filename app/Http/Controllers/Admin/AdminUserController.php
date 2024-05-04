<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function showUserForm()
    {
        $district = District::pluck('name', 'id');
        return view('admin.users.system_user_create', compact('district'));
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' =>  'string',
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
        $user = new AdminUser();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
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
        if ($request->assign_district) {
            $district = District::where('id', $request->assign_district)->first();
            if ($district) {
                $user->assign_district_id = $district->id ?: null;
                $user->assign_district = $district->name ?: null;
            }
        }

        $user->save();
        $user->assignRole($request->user_role);

        return Redirect()->route('admin.system-user.list')->with('success', 'User Created Successfully !');
    }

    public function list(Request $request)
    {
        //$userRole = implode('', json_decode(request()->user()->getRoleNames(), true));

        $query = AdminUser::where([['id', '!=', 1], ['id', '!=', request()->user()->id]]);
        //dd($query);
        if (request()->user()->hasRole('Super Admin')) {
        } else {
            $query->where('assign_district', request()->user()->assign_district);
        }

        $user = $request->user();

        return (new AdminUsersGrid(['user' => $user]))
            ->create(['query' => $query->latest(), 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.users.system_user_list');
    }

    public function show(Request $request)
    {
        //dd($request->adminuser);

        $data['admin_user'] = AdminUser::find($request->admin_user);
        $data['district'] =  District::pluck('name', 'id');

        return view('admin.users.system_user_update', $data);
    }

    public function update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'string',
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
            'assign_district' => 'required'
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        if ($request->password != '') {
            $update_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
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
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
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
        $user = AdminUser::findOrFail($request->id);
        $user->fill($update_data);
        $user->save();
        //dd($user);

        //dd($admin_user);
        $user->syncRoles([$request->user_role]);

        return Redirect()->back()->with('success', 'Updated Successful !');
    }

    public function destroy(Request $request)
    {
        $admin_user = AdminUser::find($request->admin_user);

        if (!$admin_user->payments()->count()) {
            $admin_user->delete();
            return Redirect()->route('admin.system-user.list')->with('success', 'User Deleted Successfully !');
        }

        return Redirect()->route('admin.system-user.list')->with('success', 'You can not delete the user. User is associate with payments.');
    }

    public function profilePhoto()
    {

        return view('admin.profile-photo');
    }
    public function storeProfilePhoto(Request $request)
    {
        //dd($request);

        $validator = Validator::make($request->all(), [

            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $user = $request->user();

        if ($request->hasFile('image')) {
            if ($user->hasImage()) {
                unlink($user->getAdminImage());
            }
            $user->image = $request->file('image')->store(AdminUser::USER_IMAGE);
        }

        $user->save();
        return Redirect()->back()->with('success', 'Updated Successful !');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\District;
use App\Grids\UsersGrid;
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
}

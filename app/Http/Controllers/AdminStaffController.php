<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;


class AdminStaffController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.staff_list', compact('users'));
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.staff_attendance_list', compact('user'));
    }

}

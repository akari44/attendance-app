<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.attendance_list', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.attendance_detail', compact('user'));
    }
}
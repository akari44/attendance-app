<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index()
    {
        return view('user.attendance_list');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.attendance_detail', compact('user'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show()
    {
        $status = '休憩中';
        return view('user.attendance', compact('status'));
    }
}

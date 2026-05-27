<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('user_id', auth()->id())
            ->with('breakTimes')
            ->orderBy('date', 'desc')
            ->get();

        $today = Carbon::now()->locale('ja')->isoFormat('Y年M月D日(ddd)');

        return view('user.attendance_list', compact('attendances', 'today'));
    }
    
    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('user.attendance_detail', compact('attendance'));
    }
}

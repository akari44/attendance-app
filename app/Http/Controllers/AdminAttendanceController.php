<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with('user')
            ->where('date', Carbon::parse($date)->toDateString())
            ->get();

        $today_title = Carbon::today()->locale('ja')->isoFormat('Y年M月D日');
        $today = Carbon::parse($date)->format('Y/m/d');
        $prevDay = Carbon::parse($date)->subDay()->format('Y-m-d');
        $nextDay = Carbon::parse($date)->addDay()->format('Y-m-d');



        return view('admin.attendance_list', compact('attendances', 'today_title', 'today', 'prevDay', 'nextDay'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $user = $attendance->user;
        return view('admin.attendance_detail', compact('attendance', 'user'));
    }
}
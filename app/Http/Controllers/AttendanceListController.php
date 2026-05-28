<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $attendances = Attendance::where('user_id', auth()->id())
            ->with('breakTimes')
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc')
            ->get();

        $today = Carbon::parse($month)->locale('ja')->isoFormat('Y年M月');
        $prevMonth = Carbon::parse($month)->subMonth()->format('Y-m');
        $nextMonth = Carbon::parse($month)->addMonth()->format('Y-m');

        return view('user.attendance_list', compact('attendances', 'today', 'prevMonth', 'nextMonth'));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        $user = $attendance->user;
        return view('user.attendance_detail', compact('attendance', 'user'));
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\BreakTime;
use Carbon\Carbon;
use App\Models\Attendance;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show()
    {
        $dt = Carbon::now()->locale('ja');
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', Carbon::today())
            ->first();
        $status = $attendance ? $attendance->status : '勤務外';
        return view('user.attendance', compact('status', 'dt'));
    }

    public function store(Request $request)
    {
        if ($request->action === 'clock_in') {
            Attendance::create([
                'user_id' => auth()->id(),
                'date' => Carbon::today(),
                'clock_in' => Carbon::now()->format('H:i:s'),
                'status' => '出勤中',
            ]);
            return redirect()->route('user.attendance');

        } elseif ($request->action === 'clock_out') {
            Attendance::where('user_id', auth()->id())
            ->where('date', Carbon::today())
            ->update([
            'clock_out' => Carbon::now()->format('H:i:s'),
            'status'    => '退勤済',
            ]);
            return redirect()->route('user.attendance');

        } elseif ($request->action === 'break_start') {
            $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', Carbon::today())
            ->first();
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::now()->format('H:i:s')
            ]);
            Attendance::where('user_id', auth()->id())
            ->where('date', Carbon::today())
            ->update([
            'status'    => '休憩中',
            ]);
            return redirect()->route('user.attendance');

        } elseif ($request->action === 'break_end') {
            $attendance = Attendance::where('user_id', auth()->id())
                ->where('date', Carbon::today())
                ->first();
            BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->update([
                    'break_end' => Carbon::now()->format('H:i:s'),
                ]);
            Attendance::where('user_id', auth()->id())
                ->where('date', Carbon::today())
                ->update([
                    'status' => '出勤中',
                ]);
            return redirect()->route('user.attendance');
        }
    }
}

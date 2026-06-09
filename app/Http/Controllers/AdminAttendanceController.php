<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceCorrectionRequest;

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
        $today = Carbon::parse($attendance->getRawOriginal('date'));

        $attendanceRequest = AttendanceRequest::where('attendance_id', $id)
            ->with('breakRequests')
            ->latest()
            ->first();

        return view('admin.attendance_detail', compact('attendance', 'user', 'today', 'attendanceRequest'));
    }

    public function update(AttendanceCorrectionRequest $request, $id)
    {
        Attendance::where('id', $id)->update([
            'clock_in' => $request->requested_clock_in,
            'clock_out' => $request->requested_clock_out,
        ]);
        BreakTime::where('attendance_id', $id)->delete();

        foreach ($request->requested_break_start ?? [] as $index => $breakStart) {
            BreakTime::create([
                'attendance_id' => $id,
                'break_start' => $breakStart,
                'break_end' => $request->requested_break_end[$index],
            ]);
        }

        return redirect()->route('admin.attendance.detail', $id)->with('flashSuccess', '修正完了しました');
    }
}
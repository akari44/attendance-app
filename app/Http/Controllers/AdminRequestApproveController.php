<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\AttendanceRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
class AdminRequestApproveController extends Controller
{
    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with('breakRequests', 'attendance.user')
            ->findOrFail($id);
        $attendance = $attendanceRequest->attendance;
        $user = $attendance->user;
        $today = Carbon::parse($attendance->getRawOriginal('date'));

        return view('admin.request_approve', compact('attendance', 'user', 'today', 'attendanceRequest'));
    }

    public function update($id)
    {
        $attendanceRequest = AttendanceRequest::with('breakRequests')->findOrFail($id);

        Attendance::where('id', $attendanceRequest->attendance_id)->update([
            'clock_in' => $attendanceRequest->requested_clock_in,
            'clock_out' => $attendanceRequest->requested_clock_out,
        ]);

        BreakTime::where('attendance_id', $attendanceRequest->attendance_id)->delete();
        foreach ($attendanceRequest->breakRequests as $breakRequest) {
            BreakTime::create([
                'attendance_id' => $attendanceRequest->attendance_id,
                'break_start' => $breakRequest->requested_break_start,
                'break_end' => $breakRequest->requested_break_end,
            ]);
        }AttendanceRequest::where('id', $id)->update(['status' => '承認済み']);
            return redirect()->back()->with('flashSuccess', '承認しました');
    }


}

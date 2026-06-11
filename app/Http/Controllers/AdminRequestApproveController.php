<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\AttendanceRequest;

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
        AttendanceRequest::where('id', $id)->update(['status' => '承認済み']);
        return redirect()->back()->with('flashSuccess', '承認しました');
    }


}

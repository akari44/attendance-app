<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRequest;
use App\Models\BreakRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceCorrectionRequest;

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
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $user = $attendance->user;
        $today = Carbon::parse($attendance->getRawOriginal('date'));

        $attendanceRequest = AttendanceRequest::where('attendance_id', $id)
            ->with('breakRequests')
            ->latest()
            ->first();

        return view('user.attendance_detail', compact('attendance', 'user', 'today', 'attendanceRequest'));
    }

    public function store(AttendanceCorrectionRequest $request, $id)
    {
        $attendanceRequest = AttendanceRequest::create([
            'attendance_id' => $id,
            'reason' => $request->reason,
            'status' => '承認待ち',
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
        ]);

        // 休憩は配列で来るのでループ
        foreach ($request->requested_break_start ?? [] as $index => $breakStart) {
            BreakRequest::create([
                'attendance_request_id' => $attendanceRequest->id,
                'requested_break_start' => $breakStart,
                'requested_break_end' => $request->requested_break_end[$index],
            ]);
        }

        return redirect()->route('user.attendance.detail', $id)->with('flashSuccess', '申請完了しました');
    }
}

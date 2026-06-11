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
        $monthCarbon = Carbon::parse($month);

        // その月の全日付を生成
        $allDates = [];
        $start = $monthCarbon->copy()->startOfMonth();
        $end = $monthCarbon->copy()->endOfMonth();
        while ($start->lte($end)) {
            $allDates[] = $start->copy();
            $start->addDay();
        }

        // 出勤データを取得してdate別にまとめる
        $attendances = Attendance::where('user_id', auth()->id())
            ->with('breakTimes')
            ->whereYear('date', $monthCarbon->year)
            ->whereMonth('date', $monthCarbon->month)
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->getRawOriginal('date');
            });

        $today = $monthCarbon->locale('ja')->isoFormat('Y年M月');
        $prevMonth = $monthCarbon->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthCarbon->copy()->addMonth()->format('Y-m');

        return view('user.attendance_list', compact('allDates', 'attendances', 'today', 'prevMonth', 'nextMonth'));
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

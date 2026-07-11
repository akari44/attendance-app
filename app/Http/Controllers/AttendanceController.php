<?php

namespace App\Http\Controllers;
use App\Models\BreakTime;
use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;


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
                    'status' => '退勤済',
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
                    'status' => '休憩中',
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

    /**
     * マイ勤怠レポートを表示する
     *
     * @param Request $request
     * @return View
     */
    public function report(Request $request): View
    {
        $attendances = Attendance::with('breakTimes')
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->where('user_id', auth()->id())
            ->get();

        $totalWorkMinutes = $attendances->sum(function ($attendance) {
            return Carbon::parse($attendance->getRawOriginal('clock_out'))
                ->diffInMinutes(Carbon::parse($attendance->getRawOriginal('clock_in')));
        });

        $totalBreakMinutes = $attendances->sum(function ($attendance) {
            return $attendance->breakTimes->sum(function ($break) {
                return Carbon::parse($break->getRawOriginal('break_end'))
                    ->diffInMinutes(Carbon::parse($break->getRawOriginal('break_start')));
            });
        });

        $totalMinutes = $totalWorkMinutes - $totalBreakMinutes;

        $totalOvertimeMinutes = $attendances->sum(function ($attendance) {
            $work = Carbon::parse($attendance->getRawOriginal('clock_out'))
                ->diffInMinutes(Carbon::parse($attendance->getRawOriginal('clock_in')));
            $break = $attendance->breakTimes->sum(function ($break) {
                return Carbon::parse($break->getRawOriginal('break_end'))
                    ->diffInMinutes(Carbon::parse($break->getRawOriginal('break_start')));
            });
            return max(0, $work - $break - 480);
        });

        $workDays = $attendances->count();
        $averageMinutes = $workDays > 0 ? $totalMinutes / $workDays : 0;

        $monthlyData = $attendances->groupBy(function ($attendance) {
            return Carbon::parse($attendance->getRawOriginal('date'))->format('Y-m');
        })->map(function ($monthAttendances) {
            $workMinutes = $monthAttendances->sum(function ($attendance) {
                $work = Carbon::parse($attendance->getRawOriginal('clock_out'))
                    ->diffInMinutes(Carbon::parse($attendance->getRawOriginal('clock_in')));
                $break = $attendance->breakTimes->sum(function ($break) {
                    return Carbon::parse($break->getRawOriginal('break_end'))
                        ->diffInMinutes(Carbon::parse($break->getRawOriginal('break_start')));
                });
                return $work - $break;
            });

            $overtimeMinutes = $monthAttendances->sum(function ($attendance) {
                $work = Carbon::parse($attendance->getRawOriginal('clock_out'))
                    ->diffInMinutes(Carbon::parse($attendance->getRawOriginal('clock_in')));
                $break = $attendance->breakTimes->sum(function ($break) {
                    return Carbon::parse($break->getRawOriginal('break_end'))
                        ->diffInMinutes(Carbon::parse($break->getRawOriginal('break_start')));
                });
                return max(0, $work - $break - 480);
            });

            return [
                'work' => $workMinutes,
                'overtime' => $overtimeMinutes,
            ];
        });

        $lateCount = $attendances->filter(function ($attendance) {
            return Carbon::parse($attendance->getRawOriginal('clock_in'))->gt(Carbon::parse('09:00:00'));
        })->count();

        $earlyCount = $attendances->filter(function ($attendance) {
            return Carbon::parse($attendance->getRawOriginal('clock_out'))->lt(Carbon::parse('18:00:00'));
        })->count();

        $longWorkCount = $attendances->filter(function ($attendance) {
            $work = Carbon::parse($attendance->getRawOriginal('clock_out'))
                ->diffInMinutes(Carbon::parse($attendance->getRawOriginal('clock_in')));
            $break = $attendance->breakTimes->sum(function ($break) {
                return Carbon::parse($break->getRawOriginal('break_end'))
                    ->diffInMinutes(Carbon::parse($break->getRawOriginal('break_start')));
            });
            return ($work - $break) > 600;
        })->count();

        return view('user.attendance_report', compact(
            'totalMinutes',
            'totalOvertimeMinutes',
            'averageMinutes',
            'monthlyData',
            'lateCount',
            'earlyCount',
            'longWorkCount'
        ));
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;




class AdminStaffController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.staff_list', compact('users'));
    }
    public function show(Request $request, $id)
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

        // 出勤データを日付をキーにして取得
        $attendances = Attendance::where('user_id', $id)
            ->with('breakTimes')
            ->whereYear('date', $monthCarbon->year)
            ->whereMonth('date', $monthCarbon->month)
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->getRawOriginal('date');
            });

        $user = User::findOrFail($id);
        $today = $monthCarbon->locale('ja')->isoFormat('Y年M月');
        $prevMonth = $monthCarbon->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthCarbon->copy()->addMonth()->format('Y-m');

        return view('admin.staff_attendance_list', compact('user', 'allDates', 'attendances', 'today', 'prevMonth', 'nextMonth'));
    }

}

<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return view('admin.staff_attendance_list', compact('user', 'allDates', 'attendances', 'today', 'prevMonth', 'nextMonth', 'month'));
    }

    public function exportCsv(Request $request,$id){

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

        // CSV
        $headers = ['日付', '出勤', '退勤', '休憩', '合計'];

        $rows = [];
        foreach ($allDates as $date) {
            $attendance = $attendances[$date->toDateString()] ?? null;
            
            if ($attendance) {
                $rows[] = [
                    $date->locale('ja')->isoFormat('M月D日(ddd)'),
                    $attendance->clock_in,
                    $attendance->clock_out,
                    $attendance->total_break_time,
                    $attendance->total_work_time,
                ];
            } else {
                $rows[] = [
                    $date->locale('ja')->isoFormat('M月D日(ddd)'),
                    '', '', '', '',
                ];
            }
        }

        $response = new StreamedResponse(function () use ($headers, $rows) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM追加
            
            // ヘッダー行
            fputcsv($stream, $headers);
            
            // データ行
            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);
        });

        $user = User::findOrFail($id);
        $filename = $user->name . '_月次勤怠_' . $month . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

}

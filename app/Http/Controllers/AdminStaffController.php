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

        $attendances = Attendance::where('user_id', $id)
            ->with('breakTimes')
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc')
            ->get();

        $user = User::findOrFail($id);

        $today = Carbon::parse($month)->locale('ja')->isoFormat('Y年M月');
        $prevMonth = Carbon::parse($month)->subMonth()->format('Y-m');
        $nextMonth = Carbon::parse($month)->addMonth()->format('Y-m');

        return view('admin.staff_attendance_list', compact('user', 'attendances', 'today', 'prevMonth', 'nextMonth'));
    }

}

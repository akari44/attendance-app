<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRequest;
use Illuminate\Http\Request;


class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->tab ?? 'pending';
        $status = $tab === 'pending' ? '承認待ち' : '承認済み';

        if (Auth::guard('admin')->check()) {
            $requests = AttendanceRequest::where('status', $status)->with('attendance.user')->get();
            return view('common.request_list', compact('requests', 'tab') + ['isAdmin' => true]);
        } else {
            $requests = AttendanceRequest::where('status', $status)
                ->whereHas('attendance', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->with('attendance.user')
                ->get();
            return view('common.request_list', compact('requests', 'tab') + ['isAdmin' => false]);
        }
    }
}

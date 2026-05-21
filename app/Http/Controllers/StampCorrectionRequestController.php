<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->check()) {
            return view('admin.request_list', ['tab' => 'pending']);
        }
        return view('user.request_list');
    }
}

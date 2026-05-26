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
            return view('common.request_list', ['isAdmin' => true]);
        }
        return view('common.request_list', ['isAdmin' => false]);
    }
}

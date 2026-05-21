<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRequestApproveController extends Controller
{
    public function show()
    {
        return view('admin.request_approve');
    }
}

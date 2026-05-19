<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRequestApproveController extends Controller
{
    public function index()
    {
        return view('admin.request_list', ['tab' => 'pending']);
    }
}

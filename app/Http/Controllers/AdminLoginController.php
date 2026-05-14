<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    public function create()
    {
        return view('admin.login');
    }

     public function store(AdminLoginRequest $request)
    {
        return view('admin.login');
    }
}

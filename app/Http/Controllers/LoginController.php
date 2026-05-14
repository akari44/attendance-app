<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class LoginController extends Controller
{
    public function create()
    {
        return view('user.login');
    }
     public function store(LoginRequest $request)
    {
        return view('login');
    }
}

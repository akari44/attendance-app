<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function create()
    {
        return view('user.register');
    }
     public function store(AuthRequest $request)
    {
        return view('register');
    }
}

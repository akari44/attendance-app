<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminLoginController extends Controller
{
        public function create()
    {
        return view('admin.login');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();          // web ガードからログアウト
        $request->session()->invalidate();     // セッション破棄
        $request->session()->regenerateToken(); // CSRFトークン再生成
        return redirect('/admin/login')->with('flashSuccess','ログアウトしました');
    }
}

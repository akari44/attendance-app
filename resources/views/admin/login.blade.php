@extends('layouts.default')
{{-- タイトル --}}
@section('title','ログイン（管理者）')
{{-- css --}}
@section('css')
<link rel="stylesheet" href="{{ asset('/css/auth.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_admin')
<form action="/login" method="post" class="form__center">
    @csrf
    <input type="hidden" name="type" value="admin">
    <h1 class="page__title">管理者ログイン</h1>
    <label for="mail" class="entry__name">メールアドレス</label>
    <input name="email" id="mail" type="email" class="input" value="{{ old('email') }}">
    <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <label for="password" class="entry__name">パスワード</label>
    <input name="password" id="password" type="password" class="input">
    <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="btn btn--big" type="submit">管理者ログインする</button>
   
</form>
@endsection
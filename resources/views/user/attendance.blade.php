@extends('layouts.default')
{{-- タイトル --}}
@section('title','勤怠登録画面（一般ユーザー）')
{{-- css --}}
@section('css')
<link rel="stylesheet" href="{{ asset('attendance/css/.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_user')
<p>勤怠登録</p>
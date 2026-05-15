@extends('layouts.default')
{{-- タイトル --}}
@section('title','勤怠一覧画面（管理者）')
{{-- css --}}
@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_admin')
<p>ログイン成功</p>
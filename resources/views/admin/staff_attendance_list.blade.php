@extends('layouts.default')
{{-- タイトル --}}
@section('title', 'スタッフ別勤怠一覧画面（管理者）')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/list.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_admin')
<main class="main-content">
    <div class="page-title">
        <h1>{{ $user->name }}さんの勤怠</h1>
    </div>

    <div class="calender">
        <div class="prev">前日</div>
        <div class="now">2026/09/09</div>
        <div class="next">翌日</div>
    </div>

    <table class="main-list">
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        {{-- @foreach ( as ) --}}
        <tr>
            <td>06/01(木）</td>
            <td>０９：００</td>
            <td>１８：００</td>
            <td>１：００</td>
            <td>８：００</td>
            <td>
                <a href="{{route('admin.attendance.detail', $user->id)}}">詳細</a>
            </td>
        </tr>
        {{-- @endforeach --}}
    </table>

    <div class="button-wrapper">
        <form action="#" method="get">
            <button type="submit" class="submit-btn">CSV出力</button>
        </form>
    </div>

</main>
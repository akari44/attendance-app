@extends('layouts.default')
{{-- タイトル --}}
@section('title', '勤怠一覧画面（管理者）')
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
        <h1>{{ $today_title }}の勤怠</h1>
    </div>

    <div class="calendar">
        <div class="prev">
            <a href="?date={{ $prevDay }}">前日</a>
        </div>
        <div class="now">{{ $today }}</div>
        <div class="next">
            <a href="?date={{ $nextDay }}">翌日</a>
        </div>
    </div>

    <table class="main-list">
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach ($attendances as $attendance)
            <tr>
                <td>{{$attendance->user->name}}</td>
                <td>{{$attendance->clock_in}}</td>
                <td>{{$attendance->clock_out}}</td>
                <td>{{ $attendance->total_break_time }}</td>
                <td>{{ $attendance->total_work_time }}</td>
                <td>
                    <a href="{{route('admin.attendance.detail', $attendance->id)}}">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
</main>
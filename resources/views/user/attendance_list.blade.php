@extends('layouts.default')
{{-- タイトル --}}
@section('title', '勤怠一覧画面（一般ユーザー）')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/list.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_user')
<main class="attendance">
    <div class="page-title">
        <h1>勤怠一覧</h1>
    </div>

    <div class="calendar">
        <div class="yesterday">前日</div>
        <div class="today">{{ $today }}</div>
        <div class="tomorrow">翌日</div>
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
        @foreach ($attendances as $attendance)
        <tr>
            <td>{{$attendance->date}}</td>
            <td>{{$attendance->clock_in}}</td>
            <td>{{$attendance->clock_out}}</td>
            <td>{{ $attendance->total_break_time }}</td>
            <td>{{ $attendance->total_work_time }}</td>
            <td>
                <a href="{{ route('user.attendance.detail', $attendance->id) }}">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</main>
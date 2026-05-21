@extends('layouts.default')
{{-- タイトル --}}
@section('title', '勤怠登録画面（一般ユーザー）')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/attendance.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_user')
<main class="attendance">
    @if ($status === '勤務外')
        <div class="attendance__status">
            <span>勤務外</span>
        </div>
        <div class="attendance__date">
            <time>2023年6月1日(木)</time>
        </div>
        <div class="attendance__time">
            <time>08:00</time>
        </div>
        <div class="attendance__actions">
            <button type="submit" class="attendance__btn attendance__btn--primary">出勤</button>
        </div> --}}

    @elseif ($status === '出勤中')
        <div class="attendance__status">
            <span>出勤中</span>
        </div>
        <div class="attendance__date">
            <time>2023年6月1日(木)</time>
        </div>
        <div class="attendance__time">
            <time>09:00</time>
        </div>
        <div class="attendance__actions">
            <button type="submit" class="attendance__btn attendance__btn--primary">退勤</button>
            <button type="submit" class="attendance__btn attendance__btn--secondary">休憩入</button>
        </div>

    @elseif ($status === '休憩中')
        <div class="attendance__status">
            <span>休憩中</span>
        </div>
        <div class="attendance__date">
            <time>2023年6月1日(木)</time>
        </div>
        <div class="attendance__time">
            <time>13:00</time>
        </div>
        <div class="attendance__actions">
            <button type="submit" class="attendance__btn attendance__btn--secondary">休憩戻</button>
        </div>

    @elseif ($status === '退勤済')
        <div class="attendance__status">
            <span>退勤済</span>
        </div>
        <div class="attendance__date">
            <time>2023年6月1日(木)</time>
        </div>
        <div class="attendance__time">
            <time>18:00</time>
        </div>
        <div class="attendance__actions">
            <span class="attendance__message">お疲れ様でした。</span>
        </div>
    @endif

</main>
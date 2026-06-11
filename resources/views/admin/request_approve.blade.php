@extends('layouts.default')
{{-- タイトル --}}
@section('title', '修正申請承認画面（管理者）')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/detail.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_admin')
<main class="main-content">
    <div class="page-title">
        <h1>勤怠詳細</h1>
    </div>

    <form action="{{route('admin.request.approve', $attendanceRequest->id)}}" method="POST">
        @csrf
        @method('PUT')
        <table class="main-table">
            <colgroup>
                <col style="width: 250px;">
                <col style="width: 150px;">
                <col style="width: 50px;">
                <col style="width: 450px;">
            </colgroup>
            <tr>
                <th>名前</th>
                <td colspan="3" class="name">{{$user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="year">{{ $today->year . '年'}}</td>
                <td class="tilde"></td>
                <td class="date">{{ $today->month . '月' . $today->day . '日' }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="start_time">{{ $attendanceRequest->requested_clock_in}}</td>
                <td class="tilde">～</td>
                <td class="end_time">{{ $attendanceRequest->requested_clock_out }}</td>
            </tr>
            @if($attendanceRequest->breakRequests->isEmpty())
                <tr>
                    <th>休憩</th>
                    <td class="start_time"></td>
                    <td class="tilde">～</td>
                    <td class="end_time"></td>
                </tr>
            @else
                @foreach ($attendanceRequest->breakRequests as $breakRequest)
                    <tr>
                        <th>休憩{{ $loop->iteration > 1 ? $loop->iteration : '' }}</th>
                        <td class="start_time">{{$breakRequest->requested_break_start }}
                        </td>
                        <td class="tilde">～</td>
                        <td class="end_time">
                            {{$breakRequest->requested_break_end }}
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <th>備考</th>
                <td colspan="3" class="wrapper_reason">{{$attendanceRequest->reason}}
                </td>
            </tr>
        </table>
        <div class="button-wrapper">
            <button type="submit" class="submit-btn">承認</button>
        </div>
    </form>

</main>
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

    <div class="calendar">
        <div class="prev">
            <a href="?month={{ $prevMonth }}">前月</a>
        </div>
        <div class="now">{{ $today }}</div>
        <div class="next">
            <a href="?month={{ $nextMonth }}">翌月</a>
        </div>
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
        @foreach ($allDates as $date)
            <tr>
                <td>{{ $date->locale('ja')->isoFormat('M月D日(ddd)') }}</td>
                @if (isset($attendances[$date->toDateString()]))
                    @php $attendance = $attendances[$date->toDateString()] @endphp
                    <td>{{ $attendance->clock_in }}</td>
                    <td>{{ $attendance->clock_out }}</td>
                    <td>{{ $attendance->total_break_time }}</td>
                    <td>{{ $attendance->total_work_time }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
                    </td>
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
            </tr>
        @endforeach
    </table>

    <div class="button-wrapper">
        <form action="#" method="get">
            <button type="submit" class="submit-btn">CSV出力</button>
        </form>
    </div>

</main>
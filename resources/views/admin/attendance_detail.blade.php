@extends('layouts.default')
{{-- タイトル --}}
@section('title', '勤怠詳細画面（管理者）')
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

    <form action="" method="POST">
        @csrf
        <table class="main-table">
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
                    <td class="start_time">
                        <input type="text" class="time-input" name="requested_clock_in"
                            value="{{  old('requested_clock_in', $attendance->clock_in) }}">
                    </td>
                    <td class="tilde">～</td>
                    <td class="end_time">
                        <input type="text" class="time-input" name="requested_clock_out" value="{{old('requested_clock_out', $attendance->clock_out) }}
                    </td>
                    <td class=" error-cell">
                        @error('requested_clock_out')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                @foreach($attendance->breakTimes as $break)
                    <tr>
                        <th>休憩{{ $loop->iteration > 1 ? $loop->iteration : '' }}</th>
                        <td class="start_time">
                            <input type="text" class="time-input" name="requested_break_start[]"
                                value="{{ old('requested_break_start' . $loop->index, $break->break_start) }}">
                        </td>
                        <td class="tilde">～</td>
                        <td class="end_time">
                            <input type="text" class="time-input" name="requested_break_end[]"
                                value="{{ old('requested_break_end' . $loop->index, $break->break_end) }}">
                        </td>
                        <td class="error-cell">
                            @if($errors->has('break_error'))
                                <p>{{ $errors->first('break_error') }}</p>
                            @endif
                        </td>

                    </tr>
                @endforeach
                <tr>
                    <th>備考</th>
                    <td colspan="3" class="wrapper_reason">
                        <input type="text" class="reason" name="reason">
                    </td>
                    <td class="error-cell">
                        @error('reason')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
            </table>
            <div class="button-wrapper">
                <button type="submit" class="submit-btn">修正</button>
            </div>
    </form>

</main>
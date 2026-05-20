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
                    <td class="year">２０２６年</td>
                    <td class="tilde"></td>
                    <td class="date">４月６日</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="start_time">
                        <input type="text" class="time-input" name="clock_in" value="09:00">
                    </td>
                    <td class="tilde">～</td>
                    <td class="end_time">
                        <input type="text" class="time-input" name="clock_out" value="17:00">
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td class="start_time">
                        <input type="text" class="time-input" name="break_start[]" value="12:00">
                    </td>
                    <td class="tilde">～</td>
                    <td class="end_time">
                        <input type="text" class="time-input" name="break_end[]" value="13:00">
                    </td>
                </tr>
                <tr>
                    <th>休憩２</th>
                    <td class="start_time">
                        <input type="text" class="time-input" name="break_start[]" value="12:00">
                    </td>
                    <td class="tilde">～</td>
                    <td class="end_time">
                        <input type="text" class="time-input" name="break_end[]" value="13:00">
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td colspan="3" class="wrapper_reason">
                        <input type="text" class="reason" name="reason">
                    </td>
                </tr>
            </table>
            <div class="button-wrapper">
                <button type="submit" class="submit-btn">修正</button>
            </div>
    </form>

</main>
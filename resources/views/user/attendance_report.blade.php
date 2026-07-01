@extends('layouts.default')
{{-- タイトル --}}
@section('title', 'マイ勤怠レポート')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{asset('/css/report.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@include('components.header_user')
<div class="report">
    <h2>マイ勤怠レポート</h2>
    <p>過去6ヶ月の勤怠データから集計しています。</p>

    <section class="report__summary">
        <h3>基本サマリー</h3>
        <div class="report__summary-cards">
            <div class="report__card">
                <p>総労働時間</p>
                <h4>{{ intdiv($totalMinutes, 60) . 'h ' . ($totalMinutes % 60) . 'm' }}</h4>
            </div>
            <div class="report__card">
                <p>総残業時間</p>
                <h4>{{ intdiv($totalOvertimeMinutes, 60) . 'h ' . ($totalOvertimeMinutes % 60) . 'm' }}</h4>
            </div>
            <div class="report__card">
                <p>平均労働時間/日</p>
                <h4>{{ intdiv($averageMinutes, 60) . 'h ' . ($averageMinutes % 60) . 'm' }}</h4>
            </div>
        </div>
    </section>

    <section class="report__monthly">
        <h3>月次推移（過去6ヶ月）</h3>
        <table class="report__table">
            <thead>
                <tr>
                    <th>月</th>
                    <th>労働時間</th>
                    <th>残業時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyData as $month => $data)
                    <tr>
                        <td>{{$month}}</td>
                        <td>{{intdiv($data['work'], 60) . 'h ' . ($data['work'] % 60) . 'm'}}</td>
                        <td>{{intdiv($data['overtime'], 60) . 'h ' . ($data['overtime'] % 60) . 'm'}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="report__anomaly">
        <h3>今月の異常検知</h3>
        <p>基準: 始業 09:00 / 終業 18:00 / 長時間労働は1日10時間超</p>
        <div class="report__anomaly-cards">
            <div class="report__card">
                <p>遅刻回数</p>
                <h4>{{$lateCount}}回</h4>
            </div>
            <div class="report__card">
                <p>早退回数</p>
                <h4>{{$earlyCount}}回</h4>
            </div>
            <div class="report__card">
                <p>長時間労働日数</p>
                <h4>{{$longWorkCount}}日</h4>
            </div>
        </div>
    </section>
</div>
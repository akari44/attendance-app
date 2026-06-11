@php
    $tab = $tab ?? 'pending'
@endphp
@extends('layouts.default')
{{-- タイトル --}}
@section('title', '申請一覧画面.')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/list.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
{{-- ヘッダー --}}
@if ($isAdmin)
    @include('components.header_admin')
@else
    @include('components.header_user')
@endif
<main class="main-content">
    <div class="page-title">
        <h1>申請一覧</h1>
    </div>

    <div class="tabs">
        <a href="{{ route('common.request.list', ['tab' => 'pending']) }}"
            class="tab {{ $tab === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('common.request.list', ['tab' => 'approved']) }}"
            class="tab {{ $tab === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <table class="main-list">
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>
        @foreach ($requests as $request)
            <tr>
                <td>{{$request->status}}</td>
                <td>{{$request->attendance->user->name}}</td>
                <td>{{ date('Y/m/d', strtotime($request->attendance->getRawOriginal('date'))) }}</td>
                <td class="reason">{{$request->reason}}</td>
                <td>{{$request->created_at->format('Y/m/d')}}</td>
                <td>
                    @if ($isAdmin)
                        <a href="{{ route('admin.request.approve', $request->id) }}">詳細</a>
                    @else
                        <a href="{{ route('user.attendance.detail', $request->attendance->id) }}">詳細</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</main>
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
        {{-- @foreach ( as ) --}}
        <tr>
            <td>承認待ち</td>
            <td>なまえ</td>
            <td>2026/04/09</td>
            <td>遅延のため</td>
            <td>2026/04/09</td>
            <td>
                @if ($isAdmin)
                    <a href="{{ route('admin.request.approve', 1) }}">詳細管理者</a>
                @else
                    <a href="{{ route('user.attendance.detail', 1) }}">詳細ユーザー</a>
                @endif
            </td>
        </tr>
        {{-- @endforeach --}}
    </table>
</main>
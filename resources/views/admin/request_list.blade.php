@extends('layouts.default')
{{-- タイトル --}}
@section('title', '申請一覧画面（管理者）')
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
        <h1>申請一覧</h1>
    </div>

    <div class="tabs">
        <a href="{{ route('admin.request.list', ['tab' => 'pending']) }}"
            class="tab {{ $tab === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('admin.request.list', ['tab' => 'approved']) }}"
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
            <td>山田とか</td>
            <td>2026/04/09</td>
            <td>遅延のため</td>
            <td>2026/04/09</td>
            <td>
                <a href="#">詳細</a>
            </td>
        </tr>
        {{-- @endforeach --}}
    </table>
</main>
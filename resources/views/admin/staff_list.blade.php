@extends('layouts.default')
{{-- タイトル --}}
@section('title', 'スタッフ一覧画面（管理者）')
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
        <h1>スタッフ一覧</h1>
    </div>

    <table class="main-list">
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        @foreach ($users as $user)
            <tr>
                <td>{{$user->name}}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('admin.attendance.staff', $user->id)}}">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
</main>
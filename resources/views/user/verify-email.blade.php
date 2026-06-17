@extends('layouts.default')
{{-- タイトル --}}
@section('title', 'メール認証のお願い')
{{-- css --}}
@section('css')
    <link rel="stylesheet" href="{{asset('/css/verify.css')}}">
@endsection
{{-- 本体 --}}
@section('content')
    <div class="verify_email">
        <div class="verify_email__info">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </div>

        <div class="verify_email__actions">
            <a href="mailto:" target="_blank" class="verify-email__button">
                認証はこちらから
            </a>
        </div>

        <div class="verify_email__again">
            <form method="POST" action="{{ route('verification.send') }}" class="verify_form">
                @csrf
                <button type="submit" class="verify_email__resend">
                    認証メールを再送する
                </button>
            </form>
        </div>


    </div>
@endsection
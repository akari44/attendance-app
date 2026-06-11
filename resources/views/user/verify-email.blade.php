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
            <h1 class="verify_email__info--title"><span>メール認証</span>のお願い</h1>

            <h2>登録したメールアドレスに認証メールを送信しました。</h2>
            <p>「認証メールを確認」ボタンを押してメールアプリを開き、<br>
                <span>届いた認証メール内のリンクをクリック</span>してください。
            </p>
        </div>

        <div class="verify_email__actions">
            <a href="mailto:" target="_blank" class="verify-email__button">
                認証メールを確認
            </a>
            <p>※メールアプリが開かない場合は、ご自身でメールアプリを起動して、<br> 認証メールを確認してください。
            </p>

        </div>

        <div class="verify_email__again">
            <h3>メールアドレスに認証メールが届かない場合
            </h3>
            <p>認証メールが届かない場合は、迷惑メールフォルダをご確認いただくか、<br>
                以下のボタンから再送してください。</p>

            <form method="POST" action="{{ route('verification.send') }}" class="verify_form">
                @csrf
                <button type="submit" class="verify_email__resend">
                    認証メールを再送する
                </button>
            </form>
        </div>


    </div>
@endsection
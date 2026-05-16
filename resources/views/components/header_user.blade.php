<header class="header-user">
    <div class="header-user__inner">
        <div class="header-user__logo">
            <a href="/attendance">
                <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" class="header-user__logo-img">
            </a>
        </div>
        @auth
        {{-- メール認証実装後@if(Auth::user()->hasVerifiedEmail()) --}}
        <nav class="header-user__nav">
            <ul class="header-user__nav-list">
                <li class="header-user__nav-item">
                    <a href="/attendance" class="header-user__nav-link">勤怠</a>
                </li>
                <li class="header-user__nav-item">
                    <a href="/attendance/list" class="header-user__nav-link">勤怠一覧</a>
                </li>
                <li class="header-user__nav-item">
                    <a href="/stamp_correction_request/list" class="header-user__nav-link">申請</a>
                </li>
                <li class="header-user__nav-item">
                    <form action="/logout" method="POST" class="header-user__logout-form">
                        @csrf
                        <button type="submit" class="header-user__nav-link header-user__logout-btn">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        {{-- メール認証実装後　@endif --}}
        @endauth
    </div>
</header>

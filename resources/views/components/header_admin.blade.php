<header class="header-admin">
    <div class="header-admin__inner">
        <div class="header-admin__logo">
            <a href="/admin/attendance/list">
                <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" class="header-admin__logo-img">
            </a>
        </div>
        {{-- @auth --}}
        {{-- メール認証実装後@if(Auth::user()->hasVerifiedEmail()) --}}
        <nav class="header-admin__nav">
            <ul class="header-admin__nav-list">
                <li class="header-admin__nav-item">
                    <a href="/admin/attendance/list" class="header-admin__nav-link">勤怠一覧</a>
                </li>
                <li class="header-admin__nav-item">
                    <a href="/admin/staff/list" class="header-admin__nav-link">スタッフ一覧</a>
                </li>
                <li class="header-admin__nav-item">
                    <a href="/stamp_correction_request/list" class="header-admin__nav-link">申請一覧</a>
                </li>
                <li class="header-admin__nav-item">
                    <form action="/admin/logout" method="POST" class="header-admin__logout-form">
                        @csrf
                        <button type="submit" class="header-admin__nav-link header-admin__logout-btn">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        {{-- メール認証実装後　@endif --}}
        {{-- @endauth --}}
    </div>
</header>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>模擬案件</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <h1 class="header__title">
                <a href="{{ route('item.index') }}">
                    <img src="{{ asset('images/header-logo.png') }}" alt="coachtech" class="header-logo">
                </a>
            </h1>

            <form action="{{ route('item.index') }}" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                <input type="hidden" name="filter" value="{{ request('filter', 'recommend') }}">
                <button type="submit" style="display: none;"></button> 
            </form>

            <nav>
                <ul class="header-nav">
                    @if (!request()->is('login') && !request()->is('register'))
                        @auth
                            <li>
                                <form action="/logout" method="POST" class="logout-form">
                                    @csrf
                                    <button type="submit" class="logout-btn">ログアウト</button>
                                </form>
                            </li>
                            <li><a href="/mypage">マイページ</a></li>
                            <li><a href="/sell" class="sell-btn">出品</a></li>
                        @endauth

                        @guest
                            <li><a href="/login" class="login-btn">ログイン</a></li>
                            <li><a href="/mypage">マイページ</a></li>
                            {{-- <li><a href="/register" class="register-btn">会員登録</a></li> --}}
                            <li><a href="/sell" class="sell-btn">出品</a></li>
                        @endguest
                        
                    @endif
                </ul>
            </nav>

        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>
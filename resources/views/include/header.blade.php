
    <button class="hamburger" type="button">
        <span class="hamburger__icon">Показать меню</span>
    </button>

    <a href="{{ route('dashboard') }}" class="header__logo" title="DA Laboratory">
        <img class="header__logo-img" src="{{ asset("assets/images/logo_init.png") }}">
    </a>

    <div class="header__profile">
        <div class="dropdown">
            <a href="#" class="dropdown__trigger" title="Меню пользователя">
                @if(empty($user->avatar))
                    <img class="header__profile-photo" data-src-default="{{ asset("assets/images/no-photo.jpg") }}" src="{{ asset("assets/images/no-photo.jpg") }}" alt="{{ $user->email }}">
                @else
                    <img class="header__profile-photo" data-src-default="{{ asset("assets/images/no-photo.jpg") }}" src="{{ asset(image_path($user->avatar, 'thumbnail')) }}" alt="{{ $user->email }}">
                @endif
            </a>
            <ul class="dropdown__menu dropdown__menu--right">
                <li class="dropdown__menu-item">
                    <a href="{{ route("users.profile") }}" class="dropdown__menu-link" title="Профиль">
                        <i class="fas fa-user dropdown__menu-icon"></i>
                        Профиль
                    </a>
                </li>
                <li class="dropdown__menu-item">
                    <form action="{{ route("logout") }}" method="post">
                        @csrf
                        <button type="submit" class="dropdown__menu-link">
                            <i class="fas fa-sign-out-alt dropdown__menu-icon"></i>
                            Выход
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

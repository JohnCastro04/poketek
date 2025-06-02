<header class="px-4 py-3"
    style="background: rgba(18,22,33,0.85); box-shadow: 0 2px 16px rgba(0,0,0,0.25); position: fixed; width: 100%; z-index: 1050;">
    <div class="container-fluid">
        <nav>
            <ul class="d-flex align-items-center justify-content-between list-unstyled mb-0" style="width: 100%;">
                {{-- Menú lateral --}}
                <li class="d-flex align-items-center justify-content-start" style="min-width: 160px;">
                    <button class="btn p-0 border-0 bg-transparent" id="sidenavToggle" aria-label="Abrir menú lateral"
                        style="color: var(--golden-bloom);">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </button>
                </li>

                {{-- Logo centrado --}}
                <li class="d-flex justify-content-center flex-grow-1">
                    <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="44"
                            style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.18);">
                    </a>
                </li>

                {{-- Auth section --}}
                <li class="d-flex align-items-center justify-content-end" style="min-width: 160px;">
                    @guest
                        <a href="{{ route('login') }}" class="btn d-flex align-items-center"
                            style="border: 2px solid var(--golden-bloom); color: var(--golden-bloom); background: transparent; font-family: var(--font-title); font-weight: 700;">
                            Iniciar Sesión
                        </a>
                    @else
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userMenu"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                style="color: var(--golden-bloom); font-family: var(--font-title); font-weight: 700; ">
                                <img src="{{ asset('images/default-user.png') }}" alt="avatar" width="40" height="40">
                                <span style="margin-left: 2px;">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2 shadow" aria-labelledby="userMenu"
                                style="background: linear-gradient(145deg, var(--midnight-steel), var(--midnight-abyss-light)); border-radius: 14px; border: 2px solid var(--golden-bloom); min-width: 180px;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}"
                                        style="color: var(--golden-bloom); font-family: var(--font-title); font-weight: 700;">
                                        Perfil
                                    </a>
                                </li>

                                @if (Auth::user()->permission)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"
                                            style="color: var(--golden-bloom); font-family: var(--font-title); font-weight: 700;">
                                            Panel de Admin
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" style="border-color: var(--golden-bloom);">
                                    </li>
                                @endif

                                <li>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <a href="#" class="dropdown-item py-2"
                                            style="color: var(--golden-bloom); font-family: var(--font-title); font-weight: 700;"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Cerrar sesión
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </li>
            </ul>
        </nav>
    </div>
</header>
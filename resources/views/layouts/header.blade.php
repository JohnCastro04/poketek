<header class="px-4 py-3"
    style="background: rgba(18,22,33,0.85); box-shadow: 0 2px 16px rgba(0,0,0,0.25); position: fixed; width: 100%; z-index: 1050;">
    <div class="container-fluid">
        <nav>
            <ul class="d-flex align-items-center justify-content-between list-unstyled mb-0" style="width: 100%;">
                {{-- Menú lateral (izquierda) --}}
                <li class="d-flex align-items-center justify-content-start" style="min-width: 160px;">
                    <a class="border-0 bg-transparent" id="sidenavToggle" aria-label="Abrir menú lateral"
                        style="color: var(--golden-bloom);">
                        <i class="bi bi-list" style="font-size: 32px;"></i>
                    </a>
                </li>

                {{-- Logo centrado --}}
                <li class="d-flex justify-content-center flex-grow-1">
                    <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="44"
                            style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.18);">
                    </a>
                </li>

                {{-- Sección de autenticación (derecha) --}}
                <li class="d-flex align-items-center justify-content-end" style="min-width: 160px;">
                    @guest
                        <a href="{{ route('login') }}" class="btn d-flex align-items-center"
                            style="border: 2px solid var(--golden-bloom); color: var(--golden-bloom); background: transparent; font-family: var(--font-title); font-weight: 700;">
                            Iniciar Sesión
                        </a>
                    @else
                        <div class="dropdown">
                            {{-- Enlace del usuario sin flecha y con hover --}}
                            <a href="#" class="d-flex align-items-center text-decoration-none p-2" id="userMenu"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                style="color: var(--golden-bloom); font-family: var(--font-title); font-weight: 700; border-radius: 8px; transition: background-color 0.2s ease;">
                                <img src="{{ Auth::user()->profile_picture_url ?? asset('images/default-user.png') }}"
                                    alt="Avatar de {{ Auth::user()->name }}"
                                    width="36" height="36" class="rounded me-2"
                                    style="object-fit: cover; border: none;">
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                {{-- Tag de administrador --}}
                                @if (Auth::user()->permission)
                                    <span class="badge ms-2" style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-size: 0.75em; padding: 0.3em 0.6em; border-radius: 0.3rem;">
                                        ADMIN
                                    </span>
                                @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sidenavToggle = document.getElementById('sidenavToggle');
            const mySidenav = document.getElementById('mySidenav');
            const closeSidenav = document.getElementById('closeSidenav');

            // Crea el overlay dinámicamente
            let overlay = document.createElement('div');
            overlay.id = 'sidenavOverlay';
            document.body.appendChild(overlay);

            // Función para abrir Sidenav
            function openNav() {
                mySidenav.classList.add('open');
                overlay.classList.add('show');
            }

            // Función para cerrar Sidenav
            function closeNav() {
                mySidenav.classList.remove('open');
                overlay.classList.remove('show');
            }

            // Event Listener para el botón de hamburguesa
            if (sidenavToggle) {
                sidenavToggle.addEventListener('click', openNav);
            }

            // Event Listener para el botón de cerrar
            if (closeSidenav) {
                closeSidenav.addEventListener('click', closeNav);
            }

            // Event Listener para cerrar haciendo clic en el overlay
            overlay.addEventListener('click', closeNav);

        });
    </script>
</header>
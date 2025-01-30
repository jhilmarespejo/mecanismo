



<div class="container">
    <style>
        /* Mostrar dropdown en hover */
        .formularios:hover .dropdown-menu {
            display: block;
            margin-top: 0; /* Evita el salto en el efecto */
        }
    </style>
    <nav id="nav1" class="navbar navbar-expand-md navbar-secondary border-bottom sticky-top bg-secondary ">
        <div class="container text-light">
            <!-- Logo -->
            <span class="navbar-brandx col" href="/">
                <x-jet-application-mark width="36" />
            </span>
            @isset( Auth::user()->rol )
                <button class="navbar-toggler text-light text-shadow btn btn-ligth" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <i class="bi bi-menu-button-wide-fill"></i>
                </button>
    
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto fs-5 ">
    
                            <x-jet-nav-link href="{{ route('panel') }}" class="text-light text-shadow" :active="request()->routeIs('panel')">
                                {{ __('Inicio') }}
                            </x-jet-nav-link>
    
                                @if( Auth::user()->rol == 'Administrador' )
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle text-light text-shadow" href="#" id="menuIndicadores" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Idicadores
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="menuIndicadores">
                                            <li><a class="dropdown-item" href="/indicadores/panel">Panel de datos</a></li>
                                            <li><a class="dropdown-item" href="/indicadores/actualizar">Actualizar datos</a></li>
    
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link  text-light text-shadow" aria-current="page" href="/formularios">Formularios </a>
                                    </li>@endif
                                    
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link  text-light text-shadow" aria-current="page" href="/visita/resumen">Visitas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-light text-shadow" href="/recomendacionesEstatales">Recomendaciones</a>
                                    </li>
                                    @if( Auth::user()->rol == 'Administrador' )
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle text-light text-shadow" href="#" id="meniInteroperabilidad" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Administración
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="meniInteroperabilidad">
                                            {{-- <li><a class="dropdown-item" href="/interoperabilidad">Módulo de Interoperablidad</a></li> --}}
                                            
                                            <li><a class="dropdown-item" href="/asesoramientos">Módulo de Asesoramiento</a></li>
                                            <li><a class="dropdown-item" href="/educacion">Modulo Educativo</a></li>
                                            <li><a class="dropdown-item" href="/bancoDePreguntas">Banco de preguntas</a></li>
                                            <li><a class="dropdown-item" href="/establecimientos/index">Lugares de detención</a></li>
                                            <li class="nav-item dropdown formularios">
                                                <a class="nav-link dropdown-toggle text-light text-shadow" href="#" id="menuFormularios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Formularios
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="menuFormularios">
                                                    <li><a class="dropdown-item"  href="/formulario/nuevo">Crear</a></li>
                                                    <li><a class="dropdown-item" href="#">Asignar</a></li>
                                                </ul>
                                            </li>
                                            
                                            {{-- <li><a class="dropdown-item" href="/">Lugares de detención</a></li>
                                            <li><a class="dropdown-item" href="/">Tipos de Lugares de detención</a></li> --}}
                                        </ul>
                                    </li>
                                    @endif
                                    {{-- <li class="nav-item">
                                        <a class="nav-link  text-light text-shadow" aria-current="page" href="/categorias">Categorías</a>
                                    </li>
    
                                    <li class="nav-item">
                                        <a class="nav-link  text-light text-shadow" aria-current="page" href="/cuestionario">Cuestionarios </a>
                                    </li> --}}
                                    @if( Auth::user()->rol == 'Administrador' )
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle text-light text-shadow" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                          Usuarios
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                          <li><a class="dropdown-item" href="/register">Nuevo usuario</a></li>
                                          <li><a class="dropdown-item" href="/users/list">Verificar usuarios</a></li>
                                        </ul>
                                    </li>
    
                                   
                                    {{--<li class="nav-item">
                                        <a class="nav-link " aria-current="page" href="/interoperabilidad">* </a>
                                    </li>--}}
                                    {{-- @else --}}
    
                                {{-- <li class="nav-item">
                                    <a class="nav-link " aria-current="page" href="/reportes">Reportes</a>
                                </li> --}}
                                {{-- <li>
                                    <a href="#" id="refresh">###</a>
                                </li> --}}
                        </ul>
    
    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav align-items-baseline">
                        <!-- Teams Dropdown -->
                        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                            <x-jet-dropdown id="teamManagementDropdown">
                                <x-slot name="trigger">
                                    {{ Auth::user()->currentTeam->name }}
    
                                    <svg class="ms-2" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </x-slot>
    
                                <x-slot name="content">
                                    <!-- Team Management -->
                                    <h6 class="dropdown-header">
                                        {{ __('Manage Team') }}
                                    </h6>
    
                                    <!-- Team Settings -->
                                    <x-jet-dropdown-link  href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-jet-dropdown-link>
    
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-jet-dropdown-link>
                                    @endcan
    
                                    <hr class="dropdown-divider">
    
                                    <!-- Team Switcher -->
                                    <h6 class="dropdown-header">
                                        {{ __('Switch Teams') }}
                                    </h6>
    
                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-jet-switchable-team :team="$team" />
                                    @endforeach
                                </x-slot>
                            </x-jet-dropdown>
                        @endif
    
                        <!-- Settings Dropdown -->
                        @auth
                            <x-jet-dropdown id="settingsDropdown" class="btn text-light text-shadow bg-dark">
                                <x-slot name="trigger">
                                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                        <img class="rounded-circle" width="32" height="32" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    @else
                                        {{ Auth::user()->name }}
    
                                        <svg class="ms-2" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </x-slot>
    
                                <x-slot name="content">
                                    <!-- Account Management -->
                                    <h6 class="dropdown-header small text-muted">
                                        {{ __('Administrar Cuenta') }}
                                    </h6>
    
                                    <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Perfil') }}
                                    </x-jet-dropdown-link>
    
                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                            {{ __('API Tokens') }}
                                        </x-jet-dropdown-link>
                                    @endif
    
                                    <hr class="dropdown-divider">
    
                                    <!-- Authentication -->
                                    <x-jet-dropdown-link href="{{ route('logout') }}"
                                                        onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">
                                        {{ __('Salir') }}
                                    </x-jet-dropdown-link>
                                    {{-- <x-jet-dropdown-link href="{{ route('acceso.finalizar') }}" >
                                        {{ __('Salir') }}
                                    </x-jet-dropdown-link> --}}
                                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                                        @csrf
                                    </form>
                                </x-slot>
                            </x-jet-dropdown>
                        @endauth
                    </ul>
                </div>
            @endisset
        
        </div>
    </nav>
    
</div>


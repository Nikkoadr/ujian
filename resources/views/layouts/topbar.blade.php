<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

    <!-- Sidebar Toggle -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">

        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- 👤 USER -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::user()->nama ?? 'User' }}
                </span>
                <img class="img-profile rounded-circle"
                     src="{{ asset('assets/img/undraw_profile.svg') }}">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow">
                <a class="dropdown-item" href="#"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
            </div>
        </li>

    </ul>

</nav>
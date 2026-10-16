<div class="container-fluid">
    <a class="navbar-brand" href="{{ route('dashboard.dashboard') }}">Dashboard</a>

    <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Cost and Contract</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Work Package</a></li>
                    <li><a class="dropdown-item" href="#">Budget Plan</a></li>
                    <li><a class="dropdown-item" href="#">Owner Estimate</a></li>
                    <li><a class="dropdown-item" href="#">Value Engineering</a></li>
                </ul>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Administration</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('administration.site-instruction.main') }}">Site Instruction</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
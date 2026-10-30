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
                    <li><a class="dropdown-item" href="{{ route('cost-and-contract.budget-plan.main') }}">Budget Plan</a></li>
                    <li><a class="dropdown-item" href="{{ route('cost-and-contract.change-order.main') }}">Change Order</a></li>
                    <li><a class="dropdown-item" href="{{ route('cost-and-contract.certificate-of-payment.main') }}">Certificate of Payment</a></li>
                    <li><a class="dropdown-item" href="#">Owner Estimate</a></li>
                    <li><a class="dropdown-item" href="#">Value Engineering</a></li>
                </ul>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Administration</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('administration.site-instruction.main') }}">Site Instruction</a></li>
                    <li><a class="dropdown-item" href="{{ route('administration.minutes-of-meeting.main') }}">Minutes of Meeting</a></li>
                    <li><a class="dropdown-item" href="{{ route('administration.site-memo.main') }}">Site Memo</a></li>
                    <li><a class="dropdown-item" href="{{ route('administration.certificate-of-completion.main') }}">Certificate of Completion</a></li>
                    <li><a class="dropdown-item" href="{{ route('administration.closing-document-checklist.main') }}">Closing Document Checklist</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Compliance</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('compliance.project-execution-plan.main') }}">Project Execution Plan</a></li>
                    <li><a class="dropdown-item" href="{{ route('compliance.non-conformance-report.main') }}">Non-Conformance Report</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>

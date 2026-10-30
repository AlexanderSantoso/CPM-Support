@php
    // Isi tabel tetap mengikuti template acuan. Sel yang tidak punya sumber data dibiarkan kosong.
    $criteria = [
        'Project Info' => $projectName,
        'Area' => $projectArea,
        'Seat count' => null,
        'Density' => null,
        'User(s)' => null,
        'Budget' => null,
        'Construction Start' => null,
        'FDOB' => null,
    ];

    $coreShell = ['Building Address', 'Landlord', 'Condition', 'Sustainability', 'Number of Floors', 'Age:', 'Floor-to-Floor Heights', 'Electrical', 'HVAC', 'Parking'];

    $roles = [
        'Project Oversight' => [['Client', 'TBD', 'Project Executive']],
        'Stakeholder' => [['Client', 'TBD', 'Facility Manager'], ['Client', 'TBD', 'AV'], ['Client', 'TBD', 'IT']],
        'Project Management Oversight' => [['Colliers'], ['Colliers'], ['Colliers']],
        'Cost manager' => [['QS', 'Tender Awardee']],
        'Design' => [['TBD', 'Tender Awardee']],
        'Café Kitchen Consultant' => [['TBD Client', 'Tender Awardee']],
        'Engineering (MEP)' => [['TBD', 'Tender Awardee']],
        'Construction' => [['TBD', 'Tender Awardee']],
        'Landlord' => [['LL', 'TBD']],
    ];

    $budgetComponents = ['Building Total', 'Soft Costs', 'Base Building Shell & Core', 'Base Building MEP', 'Tenant Improvement', 'Specialty Lab Space', 'Cafe', 'Furniture & Equipment', 'Contingency'];

    $milestones = [
        'Leasing / Due Diligence', 'Preconstruction Services', 'PIF approved', 'Pre-CAF', 'Design Kick-off',
        'Concept Design (Test Fit)', 'Scheme Design (Completion)', 'Detail Design (Completion)', 'Tender Documents',
        'Construction Documents (Completion)', 'General Contractor Selected', 'Construction Kick-off',
        'Construction Plan/Permits Approved', 'Start of Construction', 'Practical Completion',
        'Client AV/IT, Security Fit-Out', 'First Day of Business (FDOB)', 'Project Completion (Closeout Approved)',
    ];

    $communications = [
        ['Tender', 'Monthly Meeting', ['Client', 'PM'], 'Monthly Progress Reporting'],
        ['Construction', 'Weekly Meeting', ['Client', 'PM', 'Consultant'], ''],
        ['', 'Weekly Meeting', ['PM', 'ID', 'MEP', 'GC', 'Kitchen', 'Café', 'WS', 'Carpet'], ''],
        ['', 'Visitation', ['Client', 'PM', 'ID', 'MEP', 'GC', 'Kitchen', 'Café', 'WS', 'Carpet'], 'If necessary, Client to visit the project site'],
    ];

    $references = ['Facility Condition Assessment', 'Risk Register (Pending)', 'Detail Budget Estimate (Pending)', 'Contractors CSI Budget Breakdown (Pending)', 'Landlord Responsibility Schedule (Pending)', 'Work Letter (Pending)', 'Tenant Improvement Allowance (Pending)', 'Weekly Report', 'Monthly Report'];
    $asOf = filled($issuedDate) ? ' – As established on ' . $issuedDate : '';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Execution Plan</title>
    @include('pdf.shared.custom-css')
    <style>
        /* Margin mengikuti posisi teks pada dokumen acuan: halaman 1 dan seterusnya mulai di bawah kop. */
        @page { margin: 192px 96px 135px 96px; }
        body { font-family: Helvetica, sans-serif; font-size: 14px; color: #000; line-height: 1.5; }
        table { width: 100%; }

        /* Elemen tetap di setiap halaman: logo kanan atas, garis biru, tagline. */
        .logo { position: fixed; top: -143px; right: -44px; height: 48px; }
        .vline { position: fixed; top: -192px; left: -47px; height: 1073px; border-left: 1px solid #1f3d7a; }
        .hline1 { position: fixed; top: 881px; left: -47px; width: 572px; border-top: 1px solid #1f3d7a; }
        .hline2 { position: fixed; top: 881px; left: 658px; width: 40px; border-top: 1px solid #1f3d7a; }
        .tagline { position: fixed; top: 875px; left: 535px; width: 125px; white-space: nowrap; font-size: 11px; line-height: 12px; color: #1f3d7a; }

        /* Hanya halaman 1: alamat kop dan disclaimer. Elemen absolut di dalam alur tampil di halaman tempatnya berada. */
        .addr { position: absolute; top: -144px; left: 0; width: 330px; font-size: 11px; line-height: 1.5; color: #1f3d7a; }
        .disclaimer { position: absolute; top: 810px; left: 0; width: 603px; font-size: 9.5px; line-height: 1.4; color: #9a9a9a; }

        .doc-title { color: #2e74b5; font-size: 22px; margin: 0; padding-top: 4px; }
        h1 { font-size: 15px; font-weight: normal; margin: 34px 0 12px 0; page-break-after: avoid; }
        h2 { font-size: 15px; font-weight: normal; margin: 26px 0 10px 0; page-break-after: avoid; }
        /* Awal halaman paksa, mengikuti dokumen acuan: 3.2, 4, 5, dan 8 selalu mulai di halaman baru. */
        h1.pb, h2.pb { page-break-before: always; margin-top: 0; }
        h1.first { margin-top: 28px; }
        .n { display: inline-block; width: 48px; }
        p { margin: 0 0 10px 0; }

        table.t { width: 582px; margin-left: 21px; margin-bottom: 8px; page-break-inside: auto; }
        table.t th, table.t td { font-size: 12px; padding: 4px 6px; vertical-align: top; text-align: left; line-height: 1.3; }
        table.t th { font-weight: bold; }
        table.t tr { page-break-inside: avoid; }
        table.t td.r, table.t th.r { text-align: right; }
        table.t td.c, table.t th.c { text-align: center; }
        table.t td.blank { height: 14px; }
        table.t tr.grp td { background: #f2f2f2; font-weight: bold; }
        table.t thead th { text-align: left; }
        table.t.hd-grey thead th { background: #d9d9d9; }
        table.t.hd-dark thead th { background: #bfbfbf; }
        table.t ul { margin: 0; padding-left: 18px; }
        .wide { width: 100%; margin-left: 0; }
        .space { height: 230px; }
    </style>
</head>
<body>
    <div class="vline"></div>
    <div class="hline1"></div>
    <div class="tagline">Accelerating success.</div>
    <div class="hline2"></div>
    @if ($accountLogo)
        <img class="logo" src="{{ $accountLogo }}" alt="Logo">
    @endif

    <div class="addr">
        @if (filled($accountAddress)){!! nl2br(e($accountAddress)) !!}<br>@endif
        @if (filled($accountPhone)){{ $accountPhone }}<br>@endif
        @if (filled($accountEmail)){{ $accountEmail }}@endif
    </div>
    <div class="disclaimer">
        PT. Colliers International Indonesia and certain members of their subsidiaries are independently owned and operated businesses and
        member firms of Colliers (NASDAQ, TSX: CIGI), a leading diversified professional services and investment management company with
        operations in 67 countries.
    </div>

    <p class="doc-title">Project Execution Plan</p>

    <h1 class="first">EXECUTIVE SUMMARY</h1>
    <p>{!! nl2br(e($executiveSummary ?? '')) !!}</p>

    <table class="t" style="margin-top: 22px;">
        <thead>
            <tr>
                <th style="width: 22%;">Criteria</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($criteria as $label => $value)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="blank">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1><span class="n">1.</span>PROJECT DESCRIPTION</h1>
    <table class="t">
        <thead>
            <tr>
                <th style="width: 30%;">Existing Core &amp; Shell</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coreShell as $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="blank"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2><span class="n">1.1.</span>Project Scope</h2>

    <h2><span class="n">1.2.</span>Project Goals</h2>
    <table class="t">
        <thead>
            <tr>
                <th style="width: 9%;">Rank</th>
                <th style="width: 40%;">Goals<br><i style="font-weight: normal;">High level aspirations</i></th>
                <th>Objectives<br><i style="font-weight: normal;">Measurable Criteria</i></th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 6; $i++)
                <tr><td class="blank"></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <p style="margin-top: 26px;">Scheme Design Gateway is the point at which Client sign off on final project objectives, design approach, budget, program, and procurement strategy.</p>

    <h2><span class="n">1.3.</span>Key Programming Assumptions{{ $asOf }}</h2>
    <table class="t">
        <thead>
            <tr>
                <th style="width: 15%;">Programing Line Item</th>
                <th>Space Type</th>
                <th class="r" style="width: 17%;">Area (Sqm)</th>
                <th class="r" style="width: 17%;">Percent of Building Area (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach (range('A', 'J') as $letter)
                <tr>
                    <td>{{ $letter }}.</td>
                    <td class="blank"></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2><span class="n">1.4.</span>Constraints</h2>

    <h2><span class="n">1.5.</span>Assumptions</h2>

    <h1><span class="n">2.</span>USERS AND STAKEHOLDERS</h1>
    <p>The following is an executive summary of the Stakeholder Engagement plan. Reference the Stakeholder Engagement Plan for detailed information</p>

    <h2><span class="n">2.1.</span>Key Users</h2>
    <table class="t">
        <tbody>
            @for ($i = 0; $i < 4; $i++)
                <tr>
                    <td class="blank" style="width: 8%;"></td>
                    <td style="width: 18%;"></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <h1><span class="n">3.</span>CORE PROJECT TEAM</h1>

    <h2><span class="n">3.1.</span>Organization Chart</h2>
    <div class="space"></div>

    <h2 class="pb"><span class="n">3.2.</span>Roles and Responsibilities</h2>
    <table class="t hd-grey">
        <thead>
            <tr>
                <th style="width: 20%;">Organization</th>
                <th style="width: 22%;">Name</th>
                <th style="width: 28%;">Role</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $group => $rows)
                <tr class="grp"><td colspan="4">{{ $group }}</td></tr>
                @foreach ($rows as $row)
                    <tr>
                        <td class="blank">{{ $row[0] ?? '' }}</td>
                        <td>{{ $row[1] ?? '' }}</td>
                        <td>{{ $row[2] ?? '' }}</td>
                        <td></td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <h2><span class="n">3.3.</span>Client Project Team</h2>

    <h1 class="pb"><span class="n">4.</span>BUDGET</h1>

    <h2><span class="n">4.1.</span>Scope of Work{{ $asOf }}</h2>
    <table class="t hd-dark">
        <thead>
            <tr>
                <th style="width: 34%;">Scope</th>
                <th>Description</th>
                <th class="r" style="width: 15%;">Rate</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 3; $i++)
                <tr><td class="blank"></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <h2><span class="n">4.2.</span>Project (PIF) Budget*{{ $asOf }}</h2>
    <table class="t hd-dark">
        <thead>
            <tr>
                <th style="width: 40%;">Project Component</th>
                <th class="r">Value ($ US)</th>
                <th class="r" style="width: 28%;">Cost/sqft</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($budgetComponents as $component)
                <tr>
                    <td class="blank" @if ($loop->first) style="font-weight: bold;" @endif>{{ $component }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1 class="pb"><span class="n">5.</span>MILESTONE SCHEDULE{{ $asOf }}</h1>
    <table class="t hd-dark">
        <thead>
            <tr>
                <th>Project Milestone</th>
                <th class="c" style="width: 16%;">Start Date</th>
                <th class="c" style="width: 16%;">End Date</th>
                <th class="c" style="width: 12%;">Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($milestones as $milestone)
                <tr>
                    <td class="blank" @if ($loop->last || $milestone === 'First Day of Business (FDOB)') style="font-weight: bold;" @endif>{{ $milestone }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1><span class="n">6.</span>COMMUNICATIONS PLAN EXECUTIVE SUMMARY</h1>
    <p>The communications plan summary will outline high level recurring project meetings and designated attendees. This plan is pending development of project team and schedule.</p>

    <p style="margin-top: 26px;">Communication Plan Summary</p>
    <table class="t">
        <thead>
            <tr>
                <th style="width: 19%;">Phase</th>
                <th style="width: 16%;">Event /Meeting</th>
                <th style="width: 26%;">Attendees</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($communications as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>
                        <ul>
                            @foreach ($row[2] as $attendee)
                                <li>{{ $attendee }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $row[3] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h1><span class="n">7.</span>RISKS</h1>
    <h2><span class="n">7.1.</span>Schedule Constraints</h2>
    <h2><span class="n">7.2.</span>Budget Constraints</h2>
    <h2><span class="n">7.3.</span>Architect / Contractor Constraints</h2>

    <h1 class="pb"><span class="n">8.</span>REFERENCES AND LINKS</h1>
    @foreach ($references as $reference)
        <p style="margin-bottom: 6px;"><span class="n">8.{{ $loop->iteration }}.</span>{{ $reference }}</p>
    @endforeach

    <h1><span class="n">9.</span>AUTHORIZATION</h1>
    <table class="t wide" style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Organization</th>
                <th>Role</th>
                <th>Signature</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 2; $i++)
                <tr>
                    <td class="blank" style="width: 20%;"></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>

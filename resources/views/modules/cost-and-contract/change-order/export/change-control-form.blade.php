@php
    $name = fn ($u) => $u ? trim($u->first_name . ' ' . $u->last_name) : '';
    $date = fn ($d) => $d ? $d->format('d-M-y') : '-';
    $rp = fn ($v) => $v !== null ? 'Rp' . number_format($v, 2, ',', '.') : '-';
    $text = fn ($v) => filled($v) ? nl2br(e($v)) : '-';
    $pm = $pmActivity?->creator;

    $roleList = ['Architect / Lead Designer', 'M&E Consultant', 'Structural Engineer', 'Quantity Surveyor', 'Other Vendor'];
    $distRows = max(count($roleList), $distribution->count());
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 38px; }
        body { font-family: Helvetica, sans-serif; font-size: 9px; color: #000; }

        table.top { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.top td { vertical-align: top; }
        .proj { color: #4a6a9a; font-size: 11px; }
        .cat { font-weight: bold; color: #4a6a9a; font-size: 8px; margin-top: 2px; }
        .acc { text-align: center; font-size: 7px; }
        .logo { text-align: right; }

        h1 { color: #1f4e8c; font-size: 14px; margin: 0 0 6px; padding-bottom: 4px; border-bottom: 1px solid #000; }

        table.box { width: 100%; border-collapse: collapse; border: 1pt solid #eee; border-top: none; page-break-inside: avoid; }
        table.box.first { border-top: 1pt solid #eee; }
        table.box td { border: 1pt solid #eee; padding: 3px 5px; vertical-align: top; }

        .lbl { font-weight: bold; }
        .val { text-align: center; color: #444; margin-top: 3px; }
        .big { font-weight: bold; font-size: 11px; text-align: center; margin-top: 2px; }
        .grey { background: #d9d9d9; }
        .right { text-align: right; font-weight: bold; }
        .tall { height: 70px; }
        .chk { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    <table class="top">
        <tr>
            <td style="width: 35%;">
                <div class="proj">{{ $co->project?->project_name }}</div>
                @if ($projectCategory)
                    <div class="cat">{{ $projectCategory }}</div>
                @endif
            </td>
            <td style="width: 30%;" class="acc">{{ $accountName }}</td>
            <td style="width: 35%;" class="logo">
                @if ($logo)
                    <img src="{{ $logo }}" style="height: 40px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <h1>Change Control Form</h1>

    {{-- Baris identitas --}}
    <table class="box first">
        <tr>
            <td style="width: 18%;">
                <div class="lbl">Date Raised:</div>
                <div class="val">{{ $date($co->date_raised) }}</div>
            </td>
            <td style="width: 38%;">
                <div class="lbl">Change Order Number:</div>
                <div class="big">{{ $docNo }}</div>
            </td>
            <td style="width: 24%;">
                <div class="lbl">Design Stage</div>
                <div class="val">{{ $co->phase?->name ?? '-' }}</div>
            </td>
            <td style="width: 20%;">
                <div class="lbl">Building / Phase:</div>
                <div class="val">-</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="lbl">Type of Change:</div>
                <div class="val">{{ $co->type?->name ?? '-' }}</div>
            </td>
            <td>
                <div class="lbl">Originator of Change :</div>
                <div class="val">{{ $co->origin?->name ?? '-' }}</div>
            </td>
            <td>
                <div class="lbl">Submitted to Client:</div>
                <div class="val">{{ $date($co->date_raised) }}</div>
            </td>
            <td>
                <div class="lbl">Decision Required By:</div>
                <div class="val">{{ $date($decisionBy) }}</div>
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td class="tall">
                <div class="lbl">Proposed Change :</div>
                {!! $text($co->description) !!}
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td style="height: 40px;">
                <div class="lbl">Reasons for change :</div>
                {!! $text($co->potential_impact_on_project) !!}
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td style="width: 20%;" class="lbl">Method of Request:</td>
            <td class="grey">&nbsp;</td>
        </tr>
    </table>

    {{-- Cost assessment --}}
    <table class="box">
        <tr>
            <td rowspan="3" style="width: 20%;" class="lbl">Cost assessment:</td>
            <td style="width: 59%;">
                (QS Estimate)
                @if (filled($co->qs_comment))
                    <div>{{ $co->qs_comment }}</div>
                @endif
            </td>
            <td style="width: 21%;" class="right">{{ $rp($co->cost_estimation) }}</td>
        </tr>
        <tr>
            <td>Fee</td>
            <td class="right">-</td>
        </tr>
        <tr>
            <td>Drawdown from</td>
            <td class="right">
                Variation Order of Contractor<br>
                {{ $rp($co->estimated_variation) }}
            </td>
        </tr>
    </table>

    {{-- Programme assessment --}}
    <table class="box">
        <tr>
            <td rowspan="2" style="width: 20%;" class="lbl">Programme assessment:</td>
            <td style="width: 59%;">(Colliers Estimate)</td>
            <td style="width: 21%;" class="right">{{ $co->time_estimation !== null ? $co->time_estimation . 'd' : '-' }}</td>
        </tr>
        <tr>
            <td>Time impact agreed with Contractor</td>
            <td class="right">-</td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td class="tall">
                <div class="lbl">Design Matters Comments (RE: Performance / Quality / Health &amp; Safety)</div>
                {!! $text($co->consultant_design_specialist_comment) !!}
            </td>
        </tr>
    </table>

    {{-- Project Management Recommendation --}}
    <table class="box">
        <tr>
            <td colspan="5" class="lbl">Project Management Recommendation</td>
            <td colspan="2" class="right">Current Status:</td>
        </tr>
        @if (filled($co->pm_comment))
            <tr>
                <td colspan="7">{!! $text($co->pm_comment) !!}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="lbl">Approved / Not Approved</td>
            <td colspan="2" class="right">{{ $pmActivity?->logStatus?->status ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 24%;" class="lbl">Project Manager</td>
            <td style="width: 6%;">Name</td>
            <td style="width: 20%;" class="grey">{{ $name($pm) }}</td>
            <td style="width: 10%;">Signature:</td>
            <td style="width: 20%;" class="grey">&nbsp;</td>
            <td style="width: 6%;">Date:</td>
            <td style="width: 14%;" class="grey">{{ $pmActivity ? $date($pmActivity->created_at) : '' }}</td>
        </tr>
    </table>

    {{-- Client Decision --}}
    <table class="box">
        <tr>
            <td colspan="5" class="lbl">Client Decision</td>
            <td colspan="2" class="right">Current Status:</td>
        </tr>
        @if (filled($co->client_comment))
            <tr>
                <td colspan="7">{!! $text($co->client_comment) !!}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="lbl">Approved / Not Approved</td>
            <td colspan="2" class="right">{{ $co->logStatus?->status ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 24%;" class="lbl">Authorized Signatory on behalf of the Employer</td>
            <td style="width: 6%;">Name</td>
            <td style="width: 20%;" class="grey">&nbsp;</td>
            <td style="width: 10%;">Signature:</td>
            <td style="width: 20%;" class="grey">&nbsp;</td>
            <td style="width: 6%;">Date:</td>
            <td style="width: 14%;" class="grey">&nbsp;</td>
        </tr>
    </table>

    {{-- Distribution --}}
    <table class="box">
        <tr>
            <td colspan="6" class="lbl">Distribution :</td>
        </tr>
        @for ($i = 0; $i < $distRows; $i++)
            @php $u = $distribution->values()->get($i); @endphp
            <tr>
                <td style="width: 16%;">{{ $u?->job_title }}</td>
                <td style="width: 5%;" class="chk">@if ($u)&#9745;@endif</td>
                <td style="width: 25%;" class="grey"><b>{{ $name($u) }}</b></td>
                <td style="width: 26%;">{{ $roleList[$i] ?? '' }}</td>
                <td style="width: 5%;" class="chk">@if (isset($roleList[$i]))&#9744;@endif</td>
                <td style="width: 23%;" class="grey">&nbsp;</td>
            </tr>
        @endfor
    </table>
</body>
</html>

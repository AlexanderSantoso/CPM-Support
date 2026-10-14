<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Minutes of Meeting {{ $docNo }}</title>
    <style>
        @page { margin: 125px 40px 70px 40px; }

        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; }

        header { position: fixed; top: -105px; left: 0; right: 0; }
        header table { width: 100%; border-collapse: collapse; }
        header td { vertical-align: top; padding: 0; }
        .title { font-size: 22px; font-weight: bold; }
        .project { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .docno { font-size: 9px; font-weight: bold; margin-top: 2px; }
        .logo { text-align: right; }
        .rule { border-bottom: 1px solid #000; margin-top: 6px; }

        footer { position: fixed; bottom: -45px; left: 0; right: 0; font-size: 9px; font-style: italic; }
        footer table { width: 100%; border-collapse: collapse; }
        footer td { padding: 0; }
        .pagenum:before { content: counter(page); }

        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th, table.grid td { border: 1pt solid #ccc; padding: 3px 5px; vertical-align: top; }        table.grid th { text-align: center; font-weight: bold; }
        table.grid tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .gap { height: 14px; }
    </style>
</head>
<body>

<header>
    <table>
        <tr>
            <td>
                <div class="title">MINUTES OF MEETING</div>
                <div class="project">{{ strtoupper($projectName ?? '') }}</div>
                <div class="docno">{{ $docNo }}</div>
            </td>
            <td class="logo" style="width: 35%;">
                @if ($logo)
                    <img src="{{ $logo }}" style="height: 45px;" alt="Logo">
                @endif
                @if ($projectLogo)
                    <img src="{{ $projectLogo }}" style="height: 45px; margin-left: 10px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>
    <div class="rule"></div>
</header>

<footer>
    <table>
        <tr>
            <td>003/PM-Doc/2020</td>
            <td style="text-align: right;">Colliers | <span class="pagenum"></span></td>
        </tr>
    </table>
</footer>

<table class="grid">
    <tr>
        <td style="width: 16%;">Date :</td>
        <td style="width: 26%;">{{ $issuedDate }}</td>
        <td style="width: 22%;">Minutes of Meeting Number :</td>
        <td style="width: 36%;">{{ $docNo }}</td>
    </tr>
    <tr>
        <td>Subject :</td>
        <td colspan="3">{{ $subject }}</td>
    </tr>
    <tr>
        <td>Venue &amp; Time :</td>
        <td colspan="3">{{ $venue }}, {{ $time }}</td>
    </tr>
    @foreach ($attendeeGroups as $group)
        <tr>
            @if ($loop->first)
                <td rowspan="{{ $attendeeGroups->count() }}">Attendances :</td>
            @endif
            <td colspan="2">{{ $group['company'] }}</td>
            <td>
                @foreach ($group['names'] as $name)
                    {{ $loop->iteration }}. {{ $name }}<br>
                @endforeach
            </td>
        </tr>
    @endforeach
</table>

<div class="gap"></div>

<table class="grid">
    <thead>
        <tr>
            <th style="width: 8%;">Number</th>
            <th style="width: 17%;">Subject</th>
            <th style="width: 40%;">Description</th>
            <th style="width: 18%;">Action By</th>
            <th style="width: 17%;">Due Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['subject'] }}</td>
                <td>{!! nl2br(e($row['description'])) !!}</td>
                <td>{{ $row['action_by'] }}&nbsp;</td>
                <td>{{ $row['due_date'] }}&nbsp;</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>

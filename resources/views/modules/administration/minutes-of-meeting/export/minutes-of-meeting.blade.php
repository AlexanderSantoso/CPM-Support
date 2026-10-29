<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Minutes of Meeting {{ $docNo }}</title>
    @include('pdf.shared.custom-css')
    <style>
        @page { margin: 125px 40px 70px 40px; }

        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; }

        header { position: fixed; top: -105px; left: 0; right: 0; }
        header table { width: 100%; }
        header td { vertical-align: top; padding: 0; }
        .title { font-size: 22px; font-weight: bold; }
        .project { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .docno { font-size: 9px; font-weight: bold; margin-top: 2px; }
        .logo { text-align: right; }

        /* Penutup putih di halaman 1 saja: menutup sel logo project pada header tetap, sehingga halaman 1 hanya menampilkan logo account. */
        .cover-project-logo { position: absolute; top: -105px; right: 100px; width: 100px; height: 52px; background: #fff; z-index: 10; }
        .rule { border-bottom: 1px solid #000; margin-top: 6px; }

        footer { position: fixed; bottom: -45px; left: 0; right: 0; font-size: 9px; font-style: italic; }
        footer table { width: 100%; }
        footer td { padding: 0; }
        .pagenum:before { content: counter(page); }

        table.grid { width: 100%; }
        table.grid th, table.grid td { padding: 3px 5px; vertical-align: top; }
        table.grid th { text-align: center; font-weight: bold; }
        table.grid tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .gap { height: 14px; }
    </style>
</head>
<body>

<div class="cover-project-logo"></div>

<header>
    <table class="no-border">
        <tr>
            <td>
                <div class="title">MINUTES OF MEETING</div>
                <div class="project">{{ strtoupper($projectName ?? '') }}</div>
                <div class="docno">{{ $docNo }}</div>
            </td>
            <td class="logo" style="width: 100px;">
                @if ($projectLogo)
                    <img src="{{ $projectLogo }}" style="height: 45px;" alt="Logo">
                @endif
            </td>
            <td class="logo" style="width: 100px;">
                @if ($logo)
                    <img src="{{ $logo }}" style="height: 45px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>
    <div class="rule"></div>
</header>

<footer>
    <table class="no-border">
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
        <td style="width: 22%;">No Minutes :</td>
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

<div style="page-break-before: always;">
<table class="grid">
    <thead>
        <tr>
            <th style="width: 6%;">No</th>
            <th style="width: 17%;">Subject</th>
            <th style="width: 42%;">Description</th>
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
</div>

</body>
</html>

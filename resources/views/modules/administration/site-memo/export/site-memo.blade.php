@php
    $date = fn ($d) => $d ? $d->format('F, d – Y') : '-';
    $text = fn ($v) => filled($v) ? nl2br(e($v)) : '-';
    $box = fn ($on) => $on ? '&#9745;' : '&#9744;';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Site Memo {{ $docNo }}</title>
    @include('pdf.shared.custom-css')
    <style>
        @page { margin: 155px 40px 75px 40px; }
        body { font-family: Helvetica, sans-serif; font-size: 10px; color: #000; }

        /* Header dan footer diulang di setiap halaman. */
        header { position: fixed; top: -135px; left: 0; right: 0; }
        footer { position: fixed; bottom: -55px; left: 0; right: 0; font-size: 7px; font-style: italic; }
        .docno { text-align: right; font-size: 7px; font-style: italic; margin-bottom: 3px; }

        table { width: 100%; }
        table.hd td { text-align: center; vertical-align: middle; }
        .pname { font-size: 13px; font-weight: bold; }
        .ploc { font-size: 9px; font-weight: bold; font-style: italic; margin-top: 3px; }
        .brand { font-size: 9px; font-weight: bold; }
        .title { font-size: 15px; font-weight: bold; padding: 5px 0; }

        /* Kotak identitas. */
        table.box { margin-top: 14px; }
        table.box td { padding: 5px 6px; vertical-align: top; }
        .chk { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; }
        .opt { white-space: nowrap; margin-right: 6px; }
        .row { margin-top: 5px; }

        /* Badan memo. */
        table.meta { margin-top: 18px; }
        table.meta td { padding: 4px 0; vertical-align: top; }
        .indent { margin-left: 40px; }
        .body { margin-top: 12px; line-height: 1.5; }
        .sign { margin-top: 30px; page-break-inside: avoid; }
        .sign .gap { height: 50px; }
        .cc { margin-top: 26px; page-break-inside: avoid; }
    </style>
</head>
<body>
    <header>
        <div class="docno">Doc No : 010/PM-DOC/2020</div>
        <table class="hd">
            <tr>
                <td style="width: 22%; height: 70px;">
                    @if ($accountLogo)
                        <img src="{{ $accountLogo }}" style="height: 50px;" alt="Logo">
                    @else
                        <span class="brand">{{ $accountName }}</span>
                    @endif
                </td>
                <td style="width: 56%;">
                    <div class="pname">{{ $projectName }}</div>
                    @if (filled($projectLocation))
                        <div class="ploc">{{ $projectLocation }}</div>
                    @endif
                </td>
                <td style="width: 22%;">
                    @if ($clientLogo)
                        <img src="{{ $clientLogo }}" style="height: 50px;" alt="Logo">
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="3" class="title">SITE MEMO</td>
            </tr>
        </table>
    </header>

    <footer>
        Copy right of PT. Colliers International Indonesia<br>
        Project Management division
    </footer>

    {{-- Identitas dokumen --}}
    <table class="box">
        <tr>
            <td style="width: 36%;">{{ $clientName ?? '-' }}</td>
            <td style="width: 28%;">
                <div>Contract No :</div>
                <div class="row">{{ $contractNo ?? '-' }}</div>
            </td>
            <td style="width: 36%;">
                <div>From :</div>
                <div class="row">{{ $fromName ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div>Number : {{ $docNo }}</div>
                <div class="row">Date : {{ $date($memo->issued_date) }}</div>
                <div class="row">
                    @foreach ($categories as $c)
                        <span class="opt"><span class="chk">{!! $box((int) $memo->site_memo_category_id === (int) $c->id) !!}</span> {{ $c->name }}</span>
                    @endforeach
                </div>
            </td>
            <td>
                <div>To :</div>
                <div class="row">{{ $toName ?? '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- To, Subject, Location --}}
    <table class="meta no-border">
        <tr>
            <td style="width: 12%;">To</td>
            <td style="width: 3%;">:</td>
            <td>{{ $toName ?? '-' }}</td>
        </tr>
        <tr>
            <td>Subject</td>
            <td>:</td>
            <td>{{ filled($memo->subject) ? $memo->subject : '-' }}</td>
        </tr>
        <tr>
            <td>Location</td>
            <td>:</td>
            <td>{{ filled($memo->location) ? $memo->location : '-' }}</td>
        </tr>
    </table>

    {{-- Isi memo --}}
    <div class="indent body">
        <div>Dear {{ $salutation }},</div>
        <div style="margin-top: 10px;">Herewith we would like to inform you</div>
        <div style="margin-top: 8px;">{!! $text($memo->memo_content) !!}</div>
    </div>

    {{-- Penandatangan --}}
    <div class="indent sign">
        <div>Site Manager / Project Leader</div>
        <div class="gap">&nbsp;</div>
        <div>{{ filled($signerName) ? $signerName : '-' }}</div>
    </div>

    <div class="indent cc">
        <div>Cc :</div>
        <div style="margin: 4px 0 0 18px;">- Client's PM</div>
    </div>
</body>
</html>

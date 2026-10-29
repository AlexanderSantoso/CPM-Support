@php
    // Daftar bernomor 1., 2., 3. mengikuti template. Kosong kalau tidak ada data.
    $numbered = function (array $items) {
        if (! $items) {
            return '';
        }
        $html = '<ol class="nums">';
        foreach ($items as $item) {
            $html .= '<li>' . e($item) . '</li>';
        }
        return $html . '</ol>';
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Non-Conformance Report {{ $ncrNo }}</title>
    @include('pdf.shared.custom-css')
    <style>
        @page { margin: 40px 47px 40px 47px; }
        body { font-family: Helvetica, sans-serif; font-size: 12px; color: #000; line-height: 1.25; }
        table { width: 100%; table-layout: fixed; }
        td, th { padding: 2px 4px; vertical-align: top; }

        .docno { text-align: right; font-size: 10px; font-style: italic; margin-bottom: 6px; }
        table.hd td { vertical-align: middle; text-align: center; }
        .pname { font-size: 17px; font-weight: bold; }
        .ploc { font-size: 14px; font-weight: bold; font-style: italic; margin-top: 4px; }
        .bar { background: #243f8f; color: #fff; font-weight: bold; text-align: center; }
        table.title td { font-size: 15px; padding: 6px 4px; }
        .gap { height: 52px; }

        /* Kotak form disambung tanpa celah. Margin -1px mencegah garis dobel di sambungan antar tabel. */
        table.box + table.box { margin-top: -1px; }
        table.box { page-break-inside: avoid; }
        table.box td.lbl { font-weight: bold; }
        table.box td.c, table.box th.c { text-align: center; }
        table.sig td { height: 58px; }
        table.sig b { display: block; text-align: center; }
        table.sig td.first b { text-align: left; }

        ol.nums { margin: 0; padding-left: 20px; }
        ol.nums li { padding-left: 2px; }
        .photos td { text-align: center; vertical-align: middle; padding: 2px; }
    </style>
</head>
<body>
    <div class="docno">Doc No: 012/PM-Doc/2020</div>

    {{-- Kop dokumen: logo, nama proyek, lokasi, lalu judul dalam bar biru --}}
    <table class="box hd">
        <tr>
            <td style="width: 21%; height: 100px;">
                @if ($accountLogo)
                    <img src="{{ $accountLogo }}" style="width: 105px;" alt="Logo">
                @else
                    <b>{{ $accountName }}</b>
                @endif
            </td>
            <td style="width: 79%;">
                <div class="pname">{{ $projectName }}</div>
                @if (filled($projectLocation))
                    <div class="ploc">{{ $projectLocation }}</div>
                @endif
            </td>
        </tr>
    </table>
    <table class="box title">
        <tr><td class="bar">NON-CONFORMANCE REPORT (NCR)</td></tr>
    </table>

    <div class="gap"></div>

    {{-- Detail NCR --}}
    {{-- Bar judul dibuat tabel sendiri: baris dengan colspan di awal membuat DOMPDF mengabaikan lebar kolom di bawahnya. --}}
    <table class="box">
        <tr><td class="bar" style="font-size: 13px; padding: 4px;">Details of Non-Conformance Report</td></tr>
    </table>
    <table class="box">
        <tr>
            <td class="lbl" style="width: 36%;">Type</td>
            <td style="width: 34%;">Corrective Action</td>
            <td style="width: 30%;"></td>
        </tr>
        <tr>
            <td class="lbl">NCR No</td>
            <td colspan="2">{{ $ncrNo }}</td>
        </tr>
        <tr>
            <td class="lbl">From</td>
            <td colspan="2">PT. Colliers International Indonesia</td>
        </tr>
        <tr>
            <td class="lbl">To</td>
            <td colspan="2">{{ $contractor }}</td>
        </tr>
        <tr>
            <td class="lbl">Process</td>
            <td colspan="2">{{ $process }}</td>
        </tr>
        <tr>
            <td class="lbl" style="height: 78px;">Detailed Description of<br>Non-Conformance Report</td>
            <td colspan="2" style="padding-left: 22px;">{!! $numbered($descriptionItems) !!}</td>
        </tr>
    </table>

    {{-- Foto kondisi di lapangan --}}
    <table class="box">
        <tr>
            <td style="height: 150px;">
                <b style="font-size: 10px;">PHOTO</b>
                @if ($ncrPhotos->isNotEmpty())
                    <table class="photos no-border">
                        @foreach ($ncrPhotos->chunk(3) as $row)
                            <tr>
                                @foreach ($row as $src)
                                    <td style="width: {{ 100 / 3 }}%;"><img src="{{ $src }}" style="height: 125px;" alt="Photo"></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
        </tr>
    </table>

    <table class="box sig">
        <tr>
            <td class="first" style="width: 26%;"><b>Date :</b>{{ $inspectionDate }}</td>
            <td class="c" style="width: 25%;"><b>Endorsed by,</b>{{ $endorsedBy }}</td>
            <td class="c" style="width: 23%;"><b>Signature</b></td>
            <td class="c" style="width: 26%;"><b>Received By,</b></td>
        </tr>
    </table>

    {{-- Tindakan korektif --}}
    <table class="box">
        <tr><td class="bar" style="font-size: 13px; padding: 4px;">Corrective Action</td></tr>
    </table>
    <table class="box">
        <tr>
            <td class="lbl" style="width: 26.5%;">Corrective action to<br>be implemented</td>
            <td style="width: 73.5%; height: 140px;">
                {!! $numbered($correctiveItems) !!}
                @if ($rectificationPhotos->isNotEmpty())
                    <table class="photos no-border" style="margin-top: 6px;">
                        @foreach ($rectificationPhotos->chunk(4) as $row)
                            <tr>
                                @foreach ($row as $src)
                                    <td style="width: 25%;"><img src="{{ $src }}" style="height: 70px;" alt="Photo"></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
        </tr>
    </table>
    <table class="box">
        <tr>
            <td class="lbl" style="width: 26.5%;">Result</td>
            <td style="width: 73.5%; font-weight: bold; height: 20px;">{{ $result }}</td>
        </tr>
    </table>
    <table class="box">
        <tr>
            <td class="lbl" style="width: 26.5%; height: 52px;">Additional Note</td>
            <td style="width: 73.5%;"></td>
        </tr>
    </table>

    <table class="box sig">
        <tr>
            <td class="first" style="width: 26%;"><b>Closed Date :</b>{{ $closedDate }}</td>
            <td class="c" style="width: 25%;"><b>Corrective action by,</b></td>
            <td class="c" style="width: 23%;"><b>Checked by,</b></td>
            <td class="c" style="width: 26%;"><b>Approved by,</b></td>
        </tr>
    </table>
</body>
</html>

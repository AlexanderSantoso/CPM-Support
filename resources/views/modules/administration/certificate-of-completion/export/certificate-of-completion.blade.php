<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    @include('pdf.shared.custom-css')
    <style>
        /* Margin kiri 93px dan kanan 120px mengikuti posisi teks pada dokumen acuan. */
        @page { margin: 130px 120px 75px 93px; }
        body { font-family: Helvetica, sans-serif; font-size: 12px; color: #000; line-height: 1.55; }
        table { width: 100%; }

        /* Elemen tetap yang diulang di setiap halaman: kop, garis biru, footer. */
        header { position: fixed; top: -95px; left: 0; right: 0; }
        .addr { font-size: 11px; color: #1f3d7a; line-height: 1.5; }
        .vline { position: fixed; top: -90px; left: -46px; height: 1000px; border-left: 1px solid #1f3d7a; }
        .hline1 { position: fixed; top: 910px; left: -46px; width: 557px; border-top: 1px solid #1f3d7a; }
        .hline2 { position: fixed; top: 910px; left: 634px; width: 43px; border-top: 1px solid #1f3d7a; }
        .tagline { position: fixed; top: 904px; left: 519px; width: 125px; white-space: nowrap; font-size: 10px; line-height: 12px; color: #1f3d7a; }
        footer { position: fixed; bottom: 40px; left: -17px; width: 580px; font-size: 9px; color: #333; line-height: 1.35; }

        .title { text-align: center; font-size: 12px; font-weight: bold; margin-top: 68px; }
        .lead { margin-top: 45px; }

        table.rows td { vertical-align: top; padding: 0 0 21px 0; }
        table.rows tr.r2 td { padding-bottom: 15px; }
        table.rows tr.r3 td { padding-bottom: 32px; }
        table.rows tr.r4 td { padding-bottom: 0; }
        table.rows td.lbl { width: 140px; white-space: nowrap; }
        table.rows td.col { width: 47px; }
        table.rows td.val { font-weight: bold; }

        .para { margin-top: 31px; }

        table.sign { margin-top: 47px; page-break-inside: avoid; }
        table.sign td { width: 50%; text-align: center; vertical-align: top; padding: 0; }
        table.sign td.co { padding-top: 12px; }
        table.sign td.gap { height: 130px; }
    </style>
</head>
<body>
    <div class="vline"></div>
    <div class="hline1"></div>
    <div class="tagline">Accelerating success.</div>
    <div class="hline2"></div>

    <header>
        <table class="no-border">
            <tr>
                <td style="width: 70%; vertical-align: top;">
                    <div class="addr">
                        @if (filled($accountAddress)){!! nl2br(e($accountAddress)) !!}<br>@endif
                        @if (filled($accountPhone)){{ $accountPhone }}<br>@endif
                        @if (filled($accountEmail)){{ $accountEmail }}@endif
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    @if ($accountLogo)
                        <img src="{{ $accountLogo }}" style="height: 45px;" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <footer>
        PT. Colliers International Indonesia and certain members of their subsidiaries are independently owned and operated businesses and
        member firms of Colliers (NASDAQ, TSX: CIGI), a leading diversified professional services and investment management company with
        operations in 67 countries.
    </footer>

    <div class="title">CERTIFICATE OF COMPLETION</div>

    <div class="lead">In this day, we declare that we have done project progress as the contract between:</div>

    <table class="rows no-border">
        <tr>
            <td class="lbl">{{ $contractorLabel }}</td>
            <td class="col">:</td>
            <td class="val">
                {{ $contractorName ?? '' }}
                @if (filled($contractorAddress))
                    <br>{!! nl2br(e($contractorAddress)) !!}
                @endif
            </td>
        </tr>
        <tr class="r2">
            <td class="lbl">Client</td>
            <td class="col">:</td>
            <td class="val">
                {{ $clientName ?? '' }}
                @if (filled($clientAddress))
                    <br>{!! nl2br(e($clientAddress)) !!}
                @endif
            </td>
        </tr>
        <tr class="r3">
            <td class="lbl">Day / Date</td>
            <td class="col">:</td>
            <td class="val">{{ $dayDate ?? '' }}</td>
        </tr>
        <tr class="r4">
            <td class="lbl">Project</td>
            <td class="col">:</td>
            <td class="val">{{ $projectName ?? '' }}</td>
        </tr>
    </table>

    <div class="para">
        The project has reached {{ $coc->certificate_of }} in accordance with the contract agreed by the two parties.
    </div>

    {{-- Tabel tanpa border: dua kolom sama lebar, semua isi rata tengah supaya Prepared by, nama perusahaan, dan garis tanda tangan lurus. --}}
    <table class="sign no-border">
        <tr>
            <td>Prepared by:</td>
            <td>Acceptance by:</td>
        </tr>
        <tr>
            <td class="co">{{ $contractorName ?? '' }}</td>
            <td class="co">{{ $clientName ?? '' }}</td>
        </tr>
        <tr>
            <td class="gap">&nbsp;</td>
            <td class="gap">&nbsp;</td>
        </tr>
        <tr>
            <td>( .................................................. )</td>
            <td>( .................................................. )</td>
        </tr>
    </table>
</body>
</html>

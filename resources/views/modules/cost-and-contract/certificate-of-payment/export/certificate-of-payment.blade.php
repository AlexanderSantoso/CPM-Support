<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Payment {{ $docNo }}</title>
    @include('pdf.shared.custom-css')
    <style>
        @page { margin: 36px 36px 70px 36px; }
        body { font-family: Helvetica, sans-serif; font-size: 11px; color: #000; line-height: 1.45; }
        table { width: 100%; }
        td, th { padding: 0; vertical-align: top; }

        footer { position: fixed; bottom: -48px; left: 0; right: 0; text-align: center; font-size: 7px; line-height: 1.3; color: #3a4bb5; }

        table.hd td { vertical-align: middle; }
        .title { text-align: center; font-weight: bold; font-size: 12px; line-height: 1.5; }
        p.lead { margin: 16px 0 6px 0; }

        table.kv td { padding: 1px 0; }

        /* Tabel nilai: kolom label, titik dua, persen, X, dasar, =, nilai. Lebar ditentukan oleh baris pertama. */
        table.amt td { padding: 1px 0; }
        table.amt td.r { text-align: right; }
        table.amt .gap td { height: 9px; }
        table.amt .rule { border-bottom: 1px solid #000; }
        table.amt .sign td { font-size: 9px; padding: 0; line-height: 1.1; }

        table.box td { padding: 4px 6px; }
        .remarks-body { height: 44px; }
        .sig { margin-top: 24px; }
        .sig td.right { width: 42%; text-align: center; }
    </style>
</head>
<body>
    <footer>
        PT. Colliers International Indonesia and certain members of their subsidiaries are independently owned and operated businesses and member firms of Colliers (NASDAQ, TSX: CIGI), a leading
        diversified professional services and investment management company with operations in 66 countries.
    </footer>

    {{-- Kop: logo kiri, judul dan nomor di tengah --}}
    <table class="box hd">
        <tr>
            <td style="width: 18%; height: 64px; text-align: center;">
                @if ($accountLogo)
                    <img src="{{ $accountLogo }}" style="height: 54px;" alt="Logo">
                @else
                    <b>{{ $accountName }}</b>
                @endif
            </td>
            <td style="width: 64%;" class="title">Certificate of Payment<br>No. {{ $docNo }}</td>
            <td style="width: 18%;"></td>
        </tr>
    </table>

    <p class="lead">Based on term and clause which have been agreed before on Contract Document, then with the undersigned below :</p>

    <table class="kv no-border">
        <tr><td style="width: 12%;">Name</td><td style="width: 2%;">:</td><td>{{ $signerName }}</td></tr>
        <tr><td>Position</td><td>:</td><td>{{ $signerPosition }}</td></tr>
        <tr><td>Company</td><td>:</td><td>{{ $accountName }}</td></tr>
        <tr><td>Address</td><td>:</td><td>{{ $accountAddress }}</td></tr>
    </table>

    <p class="lead">In this point, represent for and on behalf {{ $projectText }}, recommend value of payment for :</p>

    <table class="kv no-border">
        <tr><td style="width: 12%;">Company</td><td style="width: 2%;">:</td><td>{{ $vendorName }}</td></tr>
        <tr><td>Address</td><td>:</td><td>{{ $vendorAddress }}</td></tr>
    </table>

    <p class="lead">Then with this, {{ $vendorName }} entitled to received payment in the amount of :</p>

    <table class="amt no-border">
        <tr>
            <td style="width: 31%;">Total Contract Value (Excl. PPn)</td>
            <td style="width: 3%;">:</td>
            <td style="width: 11%;"></td>
            <td style="width: 4%;"></td>
            <td class="r" style="width: 22%;">{{ $contractValue }}</td>
            <td style="width: 4%;"></td>
            <td class="r" style="width: 25%;"></td>
        </tr>
        <tr>
            <td>Down Payment</td><td>:</td>
            <td class="r">{{ $downPercent }}</td><td class="c">&nbsp;X</td>
            <td class="r">{{ $contractValue }}</td><td>&nbsp;=</td>
            <td class="r">{{ $downValue }}</td>
        </tr>
        <tr>
            <td>Progress (Excl. PPn) - as per term payment</td><td>:</td>
            <td class="r">{{ $progressPercent }}</td><td>&nbsp;X</td>
            <td class="r">{{ $contractValue }}</td><td>&nbsp;=</td>
            <td class="r">{{ $progressValue }}</td>
        </tr>
        <tr>
            <td>Additional - Deductional Works</td><td></td><td></td><td></td><td></td><td></td>
            <td class="r rule">{{ $additional }}</td>
        </tr>
        <tr class="sign"><td colspan="7" class="r">(+)</td></tr>
        <tr>
            <td>Value of Progress</td><td>:</td><td></td><td></td><td></td><td>&nbsp;=</td>
            <td class="r">{{ $valueOfProgress }}</td>
        </tr>
        <tr>
            <td>Rounded</td><td>:</td><td></td><td></td><td></td><td>&nbsp;=</td>
            <td class="r">{{ $rounded }}</td>
        </tr>
        <tr class="gap"><td colspan="7"></td></tr>
        <tr><td colspan="7">Deduction</td></tr>
        <tr>
            <td>Retention</td><td>:</td>
            <td class="r">{{ $retentionPercent }}</td><td>&nbsp;X</td>
            <td class="r">{{ $progressValue }}</td><td>&nbsp;=</td>
            <td class="r">{{ $retentionValue }}</td>
        </tr>
        <tr>
            <td>Other (Progress before)</td><td>:</td><td></td><td></td><td></td><td>&nbsp;=</td>
            <td class="r rule">{{ $previouslyPaid }}</td>
        </tr>
        <tr class="sign"><td colspan="7" class="r">(-)</td></tr>
        <tr>
            <td>Total Deduction</td><td>:</td><td></td><td></td><td></td><td>&nbsp;=</td>
            <td class="r">{{ $totalDeduction }}</td>
        </tr>
        <tr class="gap"><td colspan="7"></td></tr>
        <tr>
            <td colspan="5">Total for {{ $vendorName }} entitled to receive payment (Excl. PPn) (d - e )</td>
            <td>&nbsp;=</td>
            <td class="r">{{ $totalToReceive }}</td>
        </tr>
        <tr>
            <td colspan="5">Others</td><td>&nbsp;=</td>
            <td class="r">{{ $others }}</td>
        </tr>
        <tr>
            <td colspan="5">Amount to be paid (Excl PPn)</td><td>&nbsp;=</td>
            <td class="r">{{ $amountToBePaid }}</td>
        </tr>
    </table>

    <table class="box" style="margin-top: 14px;">
        <tr><td>Remarks</td></tr>
    </table>
    <table class="box" style="margin-top: -1px;">
        <tr><td class="remarks-body">{!! nl2br(e($remarks ?? '')) !!}</td></tr>
    </table>

    <p style="margin: 10px 0 0 0;">This Certificate of Payment is made to be used as needed</p>

    <table class="sig no-border">
        <tr>
            <td></td>
            <td class="right">
                For and on Behalf<br><br>
                {{ $accountName }}<br><br><br><br>
                Project Manager
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Closing Documents Checklist</title>
    @include('pdf.shared.custom-css')
    <style>
        @page { margin: 50px 40px; }
        body { font-family: Helvetica, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; }

        /* Kotak judul: dua baris, garis bawah tebal sebelum tabel. */
                table.hd td { vertical-align: top; }
        .title { text-align: center; font-size: 13px; font-weight: bold; padding: 6px 0 4px 0; }
        table.meta td { padding: 2px 0; font-size: 10px; }
        .brand { font-size: 10px; font-weight: bold; text-align: right; padding: 8px 14px 0 0; }

        /* Tabel checklist. */
        table.items { font-family: 'Times New Roman', Times, serif; }
        table.items th { background: #b7e4f4; text-align: left; padding: 2px 3px; font-size: 10px; }
        table.items td { padding: 2px 3px; font-size: 9.5px; vertical-align: middle; height: 13px; }
        td.no { text-align: right; }
        td.done { background: #c6efce; color: #006100; }
        td.wait { background: #ffff00; }
        td.fail { background: #ffc7ce; color: #9c0006; }
        td.date { text-align: right; }
    </style>
</head>
<body>
    <table class="hd no-border">
        <tr>
            <td colspan="2" class="title">CLOSING DOCUMENTS CHECKLIST</td>
        </tr>
        <tr>
            <td style="width: 78%; padding: 0 0 8px 18px;">
                <table class="meta no-border">
                    <tr>
                        <td style="width: 19%;">Project Name</td>
                        <td>: {{ $projectName ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Client Name</td>
                        <td>: {{ $clientName ?? '' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 22%; text-align: right; padding: 0 14px 8px 0;">
                @if ($accountLogo)
                    <img src="{{ $accountLogo }}" style="height: 40px;" alt="Logo">
                @else
                    <span class="brand">{{ $accountName }}</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 52%;">Item</th>
                <th style="width: 31%;">Status</th>
                <th style="width: 12%;">Target Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="{{ $item['status_class'] ?? '' }}">{{ $item['status'] ?? '' }}</td>
                    <td class="date">{{ $item['target_date'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

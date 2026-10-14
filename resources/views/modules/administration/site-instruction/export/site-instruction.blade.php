@php
    $name = fn ($u) => $u ? trim($u->first_name . ' ' . $u->last_name) : '-';
    $date = fn ($d) => $d ? $d->format('d/m/Y') : '-';
    $qty = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ',');
    $text = fn ($v) => filled($v) ? nl2br(e($v)) : '-';
    $box = fn ($on) => $on ? '&#9745;' : '&#9744;';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Site Instruction {{ $docNo }}</title>
    <style>
        @page { margin: 155px 40px 75px 40px; }
        body { font-family: Helvetica, sans-serif; font-size: 9px; color: #000; }

        /* Header dan footer diulang di setiap halaman. */
        header { position: fixed; top: -135px; left: 0; right: 0; }
        footer { position: fixed; bottom: -55px; left: 0; right: 0; font-size: 7px; font-style: italic; }
        .docno { text-align: right; font-size: 7px; font-style: italic; margin-bottom: 3px; }

        table { width: 100%; border-collapse: collapse; }
        table.hd td { border: 0.5pt solid #555; text-align: center; vertical-align: middle; }
        .pname { font-size: 13px; font-weight: bold; }
        .ploc { font-size: 9px; font-weight: bold; margin-top: 3px; }
        .brand { font-size: 16px; font-weight: bold; color: #1f3f7f; }
        .title { font-size: 15px; font-weight: bold; padding: 5px 0; }

        /* Kotak form: border atas dihilangkan supaya tidak dobel di sambungan antar tabel. */
        table.box { border: 0.5pt solid #555; border-top: none; page-break-inside: avoid; }
        table.box.first { border-top: 0.5pt solid #555; }
        table.box td, table.box th { border: 0.5pt solid #555; padding: 4px 5px; vertical-align: top; }
        table.box table.plain td { border: none; padding: 2px 3px; }

        table.items { border: 0.5pt solid #555; border-top: none; }
        table.items th, table.items td { border: 0.5pt solid #555; padding: 4px 5px; vertical-align: top; }
        table.items thead { display: table-header-group; }
        table.items tr { page-break-inside: avoid; }

        .lbl { font-weight: bold; }
        .c { text-align: center; }
        .r { text-align: right; }
        .chk { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; }
        .sig { height: 50px; }
        .signame { border-top: 0.5pt solid #555; padding-top: 3px; text-align: center; }
        .cc { font-style: italic; font-size: 8px; margin: 4px 0 0 30px; }
    </style>
</head>
<body>
    <header>
        <div class="docno">Doc No : 011/PM-DOC/2020</div>
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
                <td colspan="3" class="title">SITE INSTRUCTION</td>
            </tr>
        </table>
    </header>

    <footer>
        Copy right of PT. Colliers International Indonesia<br>
        Project Management division
    </footer>

    {{-- Identitas dokumen --}}
    <table class="box first">
        <tr>
            <td style="width: 17%;" class="lbl">Contractor</td>
            <td style="width: 33%;">{{ $contractor ?? '-' }}</td>
            <td style="width: 14%;" class="lbl">SI Number</td>
            <td style="width: 36%;">{{ $docNo }}</td>
        </tr>
        <tr>
            <td class="lbl">Contract / PO Number</td>
            <td>{{ $contractNo ?? '-' }}</td>
            <td class="lbl">Issued Date</td>
            <td>{{ $date($si->issued_date) }}</td>
        </tr>
    </table>

    {{-- Type of Works, Issued By, Received By --}}
    <table class="box">
        <tr>
            <td style="width: 56%;" class="lbl">Type of Works</td>
            <td style="width: 22%;" class="lbl c">Issued By</td>
            <td style="width: 22%;" class="lbl c">Received By</td>
        </tr>
        <tr>
            <td>
                <table class="plain">
                    @foreach ($typeRows as $row)
                        <tr>
                            @foreach ($row as $t)
                                <td style="width: 50%;">
                                    @if ($t)
                                        <span class="chk">{!! $box((int) $si->type_of_work_id === (int) $t->id) !!}</span> {{ $t->name }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </td>
            <td>
                <div class="sig">&nbsp;</div>
                <div class="signame">{{ $name($issuedBy) }}</div>
            </td>
            <td>
                <div class="sig">&nbsp;</div>
                <div class="signame">{{ $name($receivedBy) }}</div>
            </td>
        </tr>
    </table>

    <table class="box">
        <tr>
            <td style="width: 17%;" class="lbl">Refer to Dwg</td>
            <td>{{ filled($si->refer_to_dwg) ? $si->refer_to_dwg : '-' }}</td>
        </tr>
    </table>

    {{-- Location, Attachment, Potential Cost & Time --}}
    <table class="box">
        <tr>
            <td style="width: 44%;" class="lbl c">Location</td>
            <td style="width: 28%;" class="lbl c">Attachment</td>
            <td style="width: 28%;" class="lbl c">Potential Cost &amp; Time</td>
        </tr>
        <tr>
            <td style="height: 60px;" class="c">{{ filled($si->location) ? $si->location : '-' }}</td>
            <td>
                @foreach ($attachmentTypes as $a)
                    <div><span class="chk">{!! $box((int) $si->attachment_type_id === (int) $a->id) !!}</span> {{ $a->name }}</div>
                @endforeach
            </td>
            <td>
                @foreach ($potentialCostTimes as $p)
                    <div><span class="chk">{!! $box((int) $si->potential_cost_time_id === (int) $p->id) !!}</span> {{ $p->name }}</div>
                @endforeach
            </td>
        </tr>
    </table>

    {{-- Instruction --}}
    <table class="items">
        <thead>
            <tr>
                <th colspan="5" class="c">INSTRUCTION</th>
            </tr>
            <tr>
                <th style="width: 8%;" class="c">Number</th>
                <th style="width: 42%;" class="c">Description</th>
                <th style="width: 9%;" class="c">Unit</th>
                <th style="width: 11%;" class="c">Qty</th>
                <th style="width: 30%;" class="c">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td>{!! $text($item->description) !!}</td>
                    <td class="c">{{ filled($item->unit) ? $item->unit : '-' }}</td>
                    <td class="r">{{ $qty($item->quantity) }}</td>
                    <td>{!! $text($item->remarks) !!}</td>
                </tr>
            @empty
                <tr>
                    <td class="c">-</td>
                    <td>-</td>
                    <td class="c">-</td>
                    <td class="r">-</td>
                    <td>-</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="cc">Cc : 1. Owner &nbsp; 2. Consultant &nbsp; 3. QS</div>
</body>
</html>

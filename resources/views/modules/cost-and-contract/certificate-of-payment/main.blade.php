@extends('layouts.main')

@section('content')
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Certificate of<br>Payment Number</th>
                        <th>Issued<br>Date</th>
                        <th>Contract<br>Number</th>
                        <th>Contractor</th>
                        <th>Amount to be Paid<br>(Excl. PPn)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row['number'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['contract'] }}</td>
                            <td>{{ $row['contractor'] }}</td>
                            <td>{{ $row['amount'] }}</td>
                            <td>{{ $row['status'] }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('cost-and-contract.certificate-of-payment.export.pdf', $row['encrypted_id']) }}" class="btn btn-secondary" target="_blank">Print</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.main')

@section('content')
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Certificate<br>Number</th>
                        <th>Issued<br>Date</th>
                        <th>Contract<br>Number</th>
                        <th>Company</th>
                        <th>Certificate Of</th>
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
                            <td>{{ $row['company'] }}</td>
                            <td>{{ $row['certificate_of'] }}</td>
                            <td>{{ $row['status'] }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('administration.certificate-of-completion.export.pdf', $row['encrypted_id']) }}" class="btn btn-secondary" target="_blank">Print</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

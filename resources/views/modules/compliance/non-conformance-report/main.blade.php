@extends('layouts.main')

@section('content')
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NCR<br>Number</th>
                        <th>Inspection<br>Date</th>
                        <th>Contractor</th>
                        <th>Area</th>
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
                            <td>{{ $row['contractor'] }}</td>
                            <td>{{ $row['area'] }}</td>
                            <td>{{ $row['status'] }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('compliance.non-conformance-report.export.pdf', $row['encrypted_id']) }}" class="btn btn-secondary" target="_blank">Print</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

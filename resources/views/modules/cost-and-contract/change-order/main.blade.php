@extends('layouts.main')

@section('content')
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Change Order<br>Number</th>
                        <th>Date<br>Raised</th>
                        <th>Contract<br>Number</th>
                        <th>Type</th>
                        <th>Description</th>
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
                            <td>{{ $row['type'] }}</td>
                            <td>{{ $row['description'] }}</td>
                            <td>{{ $row['status'] }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('cost-and-contract.change-order.export.pdf', $row['encrypted_id']) }}" class="btn btn-secondary" target="_blank">Print</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

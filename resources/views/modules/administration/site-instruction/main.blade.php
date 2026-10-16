@extends('layouts.main')

@section('content')
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Site Instruction<br>Number</th>
                        <th>Issued<br>Date</th>
                        <th>Contract<br>Number</th>
                        <th>Contractor</th>
                        <th>Change Order<br>Number</th>
                        <th>Type of<br>Works</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siteInstructions as $siteInstruction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $siteInstruction->siteInstructionNumber }}</td>
                            <td>{{ $siteInstruction->issued_date }}</td>
                            <td>{{ $siteInstruction->contractDocument?->contract_no ?? '' }}</td>
                            <td>{{ $siteInstruction->contractDocument?->vendor?->company_name ?? '' }}</td>
                            <td>{{ $siteInstruction->changeOrder?->change_order_number ?? '' }}</td>
                            <td>{{ $siteInstruction->typeOfWork?->name ?? '' }}</td>
                            <td>{{ $siteInstruction->logStatus->status }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('administration.site-instruction.showDetailPage', $siteInstruction->encrypted_id) }}" class="btn btn-primary">Print</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@extends('adminlte::page')

@section('title', 'Saved Transactions')

@section('content_header')
    <h1>Saved Transactions</h1>
    <style>
    /* Fixed width for Date & Time to save space */
    .action-col { width: 1%; white-space: nowrap; }

    /* Flexible truncation for Assured name */
    .truncate-assured {
        display: inline-block;
        max-width: 500px; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        /* Optimize columns for mobile fit */
        .truncate-assured { max-width: 110px; }
        .table td { padding: 0.5rem 0.25rem !important; vertical-align: middle !important; }
        .btn-responsive span { display: none !important; }
        
        /* Ensure the stacked info doesn't overflow */
        .small { font-size: 75% !important; }
    }
    </style>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover text-nowrap" id="transTable">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Agent</th>
                        <th>Assured</th>
                        <th>Plate No.</th>
                        <th class="d-none d-md-table-cell">Denomination</th>
                        <th class="text-center action-col">Action</th>
                    </tr>
                </thead>
<tbody>
    @foreach($issuance as $item)
    <tr>
        <td>
            <span style="display:none;">{{ $item->created_at->timestamp }}</span>
            <div class="font-weight-bold">{{ $item->created_at->format('M d, Y') }}</div>
            <div class="small text-muted">{{ $item->created_at->format('h:i A') }}</div>
        </td>
        
        <td>
            <div class="truncate-assured font-weight-bold text-uppercase">
                {{ $item->agent ?? 'N/A' }}
            </div>
            <div class="small text-muted">
                {{-- Ensure the 'coc' relationship is defined in your CtplIssuance model --}}
                COC No: <span class="text-danger font-weight-bold">{{ $item->coc->coc_no ?? 'N/A' }}</span>
            </div>
        </td>

        <td>
            <div class="truncate-assured font-weight-bold text-uppercase">
                {{ $item->vehicle->assured ?? 'N/A' }}
            </div>
            <div class="small text-muted">
                MV File: <span class="text-dark">{{ $item->vehicle->file_no ?? 'N/A' }}</span>
            </div>
        </td>
        
        <td class="font-weight-bold">
            {{ $item->vehicle->plate_no ?? 'N/A' }}
        </td>
        
        <td class="d-none d-md-table-cell font-weight-bold">
            {{ $item->vehicle->denomination ?? 'N/A' }}
        </td>
        
        <td class="text-center action-col">
            <a href="{{ route('admin.ctpl.print', ['id' => $item->transaction_id, 'tab' => 'coc']) }}" 
               class="btn btn-sm btn-info shadow-sm" 
               title="Print COC">
                <i class="fas fa-print"></i>
                <span class="ml-1 d-none d-md-inline">Print</span>
            </a>
        </td>
    </tr>
    @endforeach
</tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // We use 'destroy: true' to prevent the "Cannot reinitialise" warning
        $('#transTable').DataTable({ 
            "destroy": true, 
            "order": [[ 0, "desc" ]], // Sorts the 1st column (Date & Time) descending
            "responsive": false, 
            "autoWidth": false,
            "pageLength": 10,
            "columnDefs": [
                { "width": "120px", "targets": 0 }, 
                { "orderable": false, "targets": 5 } // Disable sorting on 'Action' column
            ]
        });
    });
</script>
@stop
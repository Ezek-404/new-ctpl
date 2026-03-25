@extends('adminlte::page')

@section('title', 'Vehicle Records')

@section('content_header')
    <h1>Vehicle Records</h1>
<style>
    /* Minimal Loading Indicator */
    div.dataTables_processing {
        background: rgba(255, 255, 255, 0.9) !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        color: #007bff !important;
        font-weight: bold !important;
        top: 50% !important;
        z-index: 1000 !important;
    }

    /* Column Sizing & Truncation */
    .id-col { width: 150px; white-space: nowrap; line-height: 1.2; }
    .assured-col { min-width: 250px; }
    
    .truncate-name, .truncate-address {
        display: block;
        max-width: 350px; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; /* Adds the "..." */
    }

    /* Mobile Tweaks */
    @media (max-width: 768px) {
        .truncate-name, .truncate-address { max-width: 150px; }
        .hide-mobile { display: none !important; }
        .table td { padding: 0.5rem !important; }
    }

    /* In your Blade <style> block */
    .action-col { 
        width: 70px !important; /* Reduced from 100px */
        text-align: center;
    }
    .btn-group .btn {
        margin-right: 2px; /* Small gap between buttons */
    }

    /* Force the table to respect our defined widths */
    #vehicleTable {
        table-layout: fixed !important;
        width: 100% !important;
    }

    /* Define equal widths for the first three columns */
    #vehicleTable th:nth-child(1),
    #vehicleTable th:nth-child(2),
    #vehicleTable th:nth-child(3) {
        width: 30% !important;
    }

    /* Keep the Action column small */
    #vehicleTable th:nth-child(4) {
        width: 10% !important;
    }

    /* Ensure long text wraps or truncates instead of breaking the layout */
    #vehicleTable td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Maintain equal widths */
    #vehicleTable th:nth-child(1),
    #vehicleTable th:nth-child(2),
    #vehicleTable th:nth-child(3) {
        width: 30% !important;
    }

    #vehicleTable th:nth-child(4) {
        width: 10% !important;
    }

    /* Optional: Ensure the monospaced MV File doesn't look too small when centered */
    .text-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        letter-spacing: 0.5px;
    }
</style>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover text-nowrap mb-0" id="vehicleTable">
                <thead>
                    <tr>
                        <th>Assured Name</th>
                        <th class="text-center">Plate No.</th>
                        <th class="text-center">MV File No.</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#vehicleTable').DataTable({
            processing: true, 
            serverSide: true,
            autoWidth: false, 
            ajax: "{{ route('admin.vehicles.index') }}",
            columns: [
                { 
                    data: 'assured', 
                    name: 'assured', 
                    render: function(data) {
                        return '<span class="font-weight-bold text-uppercase" title="'+data+'">' + (data || 'N/A') + '</span>';
                    }
                },
                { 
                    data: 'plate_no', 
                    name: 'plate_no',
                    className: 'text-center', // Centers the content
                    render: function(data) {
                        return '<strong>' + (data || '---') + '</strong>';
                    }
                },
                { 
                    data: 'file_no', 
                    name: 'file_no',
                    className: 'text-center', // Centers the content
                    render: function(data) {
                        return '<span class="text-monospace">' + (data || '---') + '</span>';
                    }
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center' 
                }
            ]
        });
    });
</script>
@stop
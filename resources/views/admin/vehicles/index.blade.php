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
</style>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover text-nowrap mb-0" id="vehicleTable">
                <thead>
                    <tr>
                        <th>Vehicle Ident.</th>
                        <th class="assured-col">Assured / Address</th>
                        <th class="hide-mobile">Engine / Chassis</th>
                        <th>Type/Color</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
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
            serverSide: true, // Only loads 10 rows at a time
            ajax: "{{ route('admin.vehicles.index') }}",
            columns: [
                { data: 'file_no', name: 'file_no', render: function(data, type, row) {
                    return '<strong>'+data+'</strong><br><small class="text-muted">Plate: '+(row.plate_no || '')+'</small>';
                }},
                { data: 'assured', name: 'assured', render: function(data, type, row) {
                    // Hover over "..." to see full text
                    return '<div class="truncate-name font-weight-bold" title="'+data+'">'+data+'</div>' +
                        '<div class="truncate-address small text-muted" title="'+row.address+'">'+(row.address || '')+'</div>';
                }},
                { data: 'engine_no', name: 'engine_no', render: function(data, type, row) {
                    return '<small>E: '+data+'<br>C: '+row.chassis_no+'</small>';
                }},
                { data: 'denomination', name: 'denomination', render: function(data, type, row) {
                    return '<strong>'+data+'</strong><br><small class="text-muted text-uppercase">'+row.color+'</small>';
                }},
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false, 
                    className: 'action-col' // Updated class name here
                }
            ]
        });
    });
</script>
@stop
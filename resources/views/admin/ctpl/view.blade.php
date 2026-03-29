@extends('adminlte::page')

@section('title', 'Vehicle Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold text-dark">Vehicle & Policy Details</h1>
        <a href="{{ route('admin.saved_transactions') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-angle-left mr-1"></i> Back to List
        </a>
    </div>
@stop

@section('content')
<div class="row">
    {{-- Left Column: Policy & Owner Information --}}
    <div class="col-md-5">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h3 class="card-title font-weight-bold text-muted small text-uppercase">
                    <i class="fas fa-file-invoice mr-2"></i> Policy & Owner Information
                </h3>
            </div>
            <div class="card-body pt-0">
                {{-- COC Header --}}
                <div class="text-center mb-4 p-3 bg-light rounded border-dashed">
                    <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">Certificate of Cover No.</div>
                    <h2 class="text-danger font-weight-bold mb-0" style="letter-spacing: 2px;">
                        {{ $issuance->coc->coc_no ?? 'N/A' }}
                    </h2>
                    <div class="text-xs text-primary font-weight-bold mt-2">
                        POLICY NO: <span class="text-dark">{{ $issuance->policy_no ?? 'PENDING' }}</span>
                    </div>
                </div>

                {{-- Information Fields --}}
                <div class="info-group mb-3">
                    <label class="info-label">Assured Name</label>
                    <div class="info-value text-primary">{{ $issuance->vehicle->assured }}</div>
                </div>
                
                <div class="info-group mb-3">
                    <label class="info-label">Address</label>
                    <div class="info-value text-muted text-sm">{{ $issuance->vehicle->address ?? 'NO ADDRESS ON RECORD' }}</div>
                </div>

                <div class="row mt-4">
                    <div class="col-6">
                        <label class="info-label">Agent / Branch</label>
                        <div class="text-dark font-weight-bold">{{ $issuance->agent }}</div>
                    </div>
                    <div class="col-6 text-right">
                        <label class="info-label">Transaction ID</label>
                        <div class="text-dark font-weight-bold">#{{ $issuance->transaction_id }}</div>
                    </div>
                </div>

                <div class="border-top mt-4 pt-3 text-center">
                    <div class="text-xs text-muted text-uppercase mb-2">Issuance Date: {{ $issuance->created_at->format('M d, Y | h:i A') }}</div>
                    <a href="{{ route('admin.ctpl.print', $issuance->transaction_id) }}" class="btn btn-info btn-block shadow-sm py-2">
                        <i class="fas fa-print mr-2"></i> <strong>PRINT POLICY CERTIFICATE</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Technical Specifications --}}
    <div class="col-md-7">
        <div class="card card-outline card-dark shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold text-muted small text-uppercase">
                    <i class="fas fa-car mr-2"></i> Technical Specifications
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <th class="spec-label">Plate Number</th>
                            <td class="spec-value text-primary font-weight-bold">{{ $issuance->vehicle->plate_no ?? 'NO PLATE' }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">MV File Number</th>
                            <td class="spec-value">{{ $issuance->vehicle->file_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">Make & Series</th>
                            <td class="spec-value text-uppercase">{{ ($issuance->vehicle->make ?? 'N/A') . ' ' . ($issuance->vehicle->series ?? '') }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">Color</th>
                            <td class="spec-value text-uppercase">{{ $issuance->vehicle->color ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">Engine Number</th>
                            <td class="spec-value text-monospace-large">{{ $issuance->vehicle->engine_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">Chassis Number</th>
                            <td class="spec-value text-monospace-large">{{ $issuance->vehicle->chassis_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="spec-label">Denomination</th>
                            <td class="spec-value text-uppercase">
                                    {{ $issuance->vehicle->denomination ?? 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0 pb-4 pr-4">
                <a href="{{ route('admin.ctpl.edit', $issuance->transaction_id) }}" class="btn btn-warning shadow-sm px-4 font-weight-bold">
                    <i class="fas fa-pen mr-2"></i> EDIT DETAILS
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Global Overrides */
    .border-dashed { border: 1px dashed #dee2e6 !important; }
    
    /* Policy Section Styling */
    .info-label { 
        display: block;
        font-size: 0.7rem; 
        text-transform: uppercase; 
        color: #999; 
        font-weight: bold;
        margin-bottom: 2px;
        letter-spacing: 0.5px;
    }
    .info-value { 
        font-size: 1.1rem; 
        font-weight: 700; 
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 4px;
        text-transform: uppercase;
    }

    /* Technical Specification Table Styling */
    .spec-label {
        width: 35%;
        background-color: #f8f9fa;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        vertical-align: middle !important;
        padding: 1.25rem 1.25rem !important;
        border-right: 1px solid #eee;
    }

    .spec-value {
        font-size: 1.25rem; /* Increased for better visibility */
        vertical-align: middle !important;
        padding-left: 1.5rem !important;
        color: #212529;
    }

    /* Monospace for numbers */
    .text-monospace-large {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        font-weight: 800;
        font-size: 1.3rem;
        letter-spacing: 1.5px;
        color: #000;
    }

    /* Action Buttons */
    .btn-info { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; border: none; font-size: 0.9rem; }
</style>
@stop
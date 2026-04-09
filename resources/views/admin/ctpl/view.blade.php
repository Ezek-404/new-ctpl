@extends('adminlte::page')

@section('title', 'Vehicle Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="font-weight-bold text-light mb-0">Vehicle & Policy Details</h1>
        </div>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.saved_transactions') }}" class="btn btn-outline-light btn-sm px-3">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
            <a href="{{ route('admin.ctpl.edit', $issuance->transaction_id) }}" class="btn btn-warning btn-sm px-3 text-dark font-weight-bold">
                <i class="fas fa-edit mr-1"></i> Quick Edit
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="dark-mode container-fluid">
    <div class="row">
        {{-- Left Column: Policy & Owner Information --}}
        <div class="col-md-6">
            <div class="card card-outline card-primary shadow bg-dark h-100">
                <div class="card-header border-bottom-0 pt-3">
                    <h3 class="card-title font-weight-bold text-muted small text-uppercase">
                        <i class="fas fa-file-invoice mr-2 text-primary"></i> Policy & Owner Information
                    </h3>
                </div>
                <div class="card-body px-4 d-flex flex-column">
                    <div class="text-center mb-4 p-4 coc-box-dark rounded shadow-sm">
                        <div class="text-xs text-uppercase text-muted font-weight-bold mb-1">Certificate of Cover No.</div>
                        <h2 class="text-danger display-4 font-weight-bold mb-0">{{ $issuance->coc->coc_no ?? 'N/A' }}</h2>
                        <div class="policy-pill mt-3">
                            <span class="text-muted small mr-2">POLICY:</span>
                            <span class="font-weight-bold text-light">{{ $issuance->policy_no ?? 'PENDING' }}</span>
                        </div>
                    </div>

                    <div class="detail-group mb-4 pl-3 border-left-primary">
                        <label class="info-label text-primary">Assured Name</label>
                        <div class="info-value h5 font-weight-bold mb-0">{{ $issuance->vehicle->assured }}</div>
                    </div>
                    
                    <div class="detail-group mb-4 pl-3 border-left-muted">
                        <label class="info-label text-primary">Primary Address</label>
                        <p class="text-muted mb-0 h6">{{ $issuance->vehicle->address ?? 'No address provided' }}</p>
                    </div>

                    <div class="row py-3 bg-soft-dark rounded mx-0 mb-4">
                        <div class="col-6 border-right border-secondary">
                            <label class="info-label mb-0 text-primary">Issuing Agent</label>
                            <div class="font-weight-bold text-light h6">{{ $issuance->agent }}</div>
                        </div>
                        <div class="col-6">
                            <label class="info-label mb-0 text-primary">Date Issued</label>
                            <div class="font-weight-bold text-light h6">{{ $issuance->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('admin.ctpl.print', $issuance->transaction_id) }}" class="btn btn-primary btn-block py-3 font-weight-bold">
                            <i class="fas fa-print mr-2"></i> GENERATE PRINTABLE PDF
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right Column: Technical Specifications (Applied Policy Design) --}}
        <div class="col-md-6">
            <div class="card card-outline card-info shadow bg-dark h-100">
                <div class="card-header border-bottom-0 pt-3">
                    <h3 class="card-title font-weight-bold text-muted small text-uppercase">
                        <i class="fas fa-microchip mr-2 text-info"></i> Vehicle Specifications
                    </h3>
                </div>
                <div class="card-body px-4 d-flex flex-column">
                    {{-- Plate Number Styled like Assured Name --}}
                    <div class="detail-group mb-4 pl-3 border-left-info">
                        <label class="info-label text-info">Plate Number</label>
                        <div class="info-value h5 font-weight-bold mb-0">
                            {{ $issuance->vehicle->plate_no ?? 'NO PLATE' }}
                        </div>
                    </div>

                    {{-- MV File Number --}}
                    <div class="detail-group mb-4 pl-3 border-left-muted">
                        <label class="info-label text-info">MV File Number</label>
                        <div class="font-weight-bold text-monospace-spec">
                            {{ $issuance->vehicle->file_no ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Denomination --}}
                    <div class="detail-group mb-4 pl-3 border-left-muted">
                        <label class="info-label text-info">Body Type</label>
                        <div class="font-weight-bold text-monospace-spec">
                            {{ $issuance->vehicle->denomination ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Brand / Make --}}
                    <div class="detail-group mb-4 pl-3 border-left-muted">
                        <label class="info-label text-info">Model & Manufacturer</label>
                        <div class="font-weight-bold text-monospace-spec">
                            {{ $issuance->vehicle->year_model ?? 'N/A' }} {{ $issuance->vehicle->make ?? 'N/A' }} {{ $issuance->vehicle->series ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Engine & Chassis Numbers with Monospace --}}
                    <div class="row mb-4">
                        <div class="col-12 mb-4">
                            <div class="pl-3 border-left-muted">
                                <label class="info-label text-info">Engine Number</label>
                                <div class="font-weight-bold text-monospace-spec h5">{{ $issuance->vehicle->engine_no ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="pl-3 border-left-muted">
                                <label class="info-label text-info">Chassis Number</label>
                                <div class="font-weight-bold text-monospace-spec h5">{{ $issuance->vehicle->chassis_no ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <div class="btn btn-secondary btn-block py-3 font-weight-bold disabled shadow-sm border-dashed-light">
                            <i class="fas fa-calendar-check mr-2"></i> 
                            DATE ADDED: {{ $issuance->created_at->format('M d, Y | h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Global Hierarchy & Indicators */
    .border-left-primary { border-left: 3px solid #007bff; }
    .border-left-info { border-left: 3px solid #17a2b8; }
    .border-left-muted { border-left: 3px solid #444; }
    .border-dashed { border-style: dashed !important; }

    /* Layout Alignment: Ensures both cards maintain identical height and button placement */
    .card { border-radius: 0.75rem; }
    .card-body.d-flex.flex-column { min-height: 520px; } 

    /* Policy Header Logic */
    .coc-box-dark { background: rgba(255,255,255, 0.03); border: 1px solid rgba(255,255,255,0.05); }
    .policy-pill { display: inline-block; padding: 4px 16px; background: rgba(0,0,0,0.2); border-radius: 20px; border: 1px solid #333; }
    
    /* Text Styles */
    .info-label { font-size: 0.65rem; text-transform: uppercase; color: #666; font-weight: 800; letter-spacing: 0.8px; display: block; margin-bottom: 2px; }
    .bg-soft-dark { background: rgba(0,0,0,0.15); }
    .text-monospace-spec { font-family: 'Courier New', Courier, monospace; letter-spacing: 1.5px; }

    /* Footer Button Styling: Specifically for the "Date Added" section */
    .border-dashed-light { 
        border: 1px dashed rgba(255,255,255,0.2) !important;
        background-color: rgba(255,255,255,0.02) !important;
        opacity: 1 !important;
        cursor: default;
        color: #adb5bd !important;
    }

    /* Badges */
    .badge-outline-secondary { 
        border: 1px solid #555; 
        background: transparent; 
        color: #888; 
        padding: 4px 10px; 
        font-weight: normal;
        font-size: 0.7rem; 
    }
</style>
@stop
@extends('adminlte::page')

@section('title', 'Vehicle Details')

@section('content_header')
    <h1 class="ml-2" style="font-weight: 400; color: #333;">Vehicle Information</h1>

    <style>
        /* Card with defined border and subtle shadow */
        .vehicle-card { 
            background: #fff; 
            border: 2px solid #dee2e6; /* More defined border */
            padding: 35px; 
            border-radius: 4px; 
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
        }

        .v-icon { font-size: 45px; color: #0056b3; margin-right: 35px; }
        
        /* Typography matching the theme */
        .v-label { 
            font-size: 0.65rem; 
            color: #000; 
            text-transform: uppercase; 
            margin-bottom: 4px; 
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .v-sub-label { 
            font-size: 0.65rem; 
            color: #000; 
            text-transform: uppercase; 
            margin-top: 18px; 
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .v-value { font-size: 1.25rem; font-weight: 800; color: #0056b3; line-height: 1.1; }
        .v-sub-value { font-size: 1rem; font-weight: 700; color: #212529; margin-bottom: 2px; }
        
        .text-muted-custom { color: #6c757d; font-weight: 500; }
        .text-active-blue { color: #0056b3; font-weight: 800; text-transform: uppercase; }

        hr { border-top: 1px solid #dee2e6; margin: 25px 0; }
        
            /* Refined Button Sizes */
        .btn-custom-size {
            padding: 6px 16px; /* Reduced padding for a sleeker look */
            font-size: 0.85rem;   /* Smaller, professional font size */
            font-weight: 600;
            border-radius: 4px;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .btn-close-theme {
            background-color: #6c757d;
            border: 1px solid #6c757d;
            color: #fff;
        }

        .btn-edit-theme {
            background-color: #ffc107;
            border: 1px solid #ffc107;
            color: #212529;
        }

        /* Subtle hover effect */
        .btn-custom-size:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            filter: brightness(95%);
        }
    </style>
@stop

@section('content')
<div class="container-fluid">
    <div class="vehicle-card">
        <div class="d-flex align-items-center mb-5">
            <div class="v-icon">
                <i class="fas fa-car-side"></i>
            </div>
            <div class="row flex-grow-1">
                <div class="col-md-3 border-right">
                    <div class="v-label">Plate No.</div>
                    <div class="v-value">{{ $vehicle->plate_no ?? 'NONE' }}</div>
                </div>
                <div class="col-md-3 border-right">
                    <div class="v-label">MV File Number</div>
                    <div class="v-value">{{ $vehicle->file_no }}</div>
                </div>
                <div class="col-md-3 border-right">
                    <div class="v-label">Chassis Number</div>
                    <div class="v-value">{{ $vehicle->chassis_no }}</div>
                </div>
                <div class="col-md-3">
                    <div class="v-label">Engine Number</div>
                    <div class="v-value">{{ $vehicle->engine_no }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="v-sub-label">Assured Name</div>
                <div class="v-sub-value text-active-blue">{{ $vehicle->assured }}</div>
                
                <div class="v-sub-label">Address</div>
                <div class="v-sub-value text-muted-custom">{{ $vehicle->address ?? 'N/A' }}</div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3">
                <div class="v-sub-label">Category / Denomination</div>
                <div class="v-sub-value">{{ $vehicle->denomination }}</div>
            </div>
            <div class="col-md-3 border-left">
                <div class="v-sub-label">Make</div>
                <div class="v-sub-value text-uppercase">{{ $vehicle->make ?? 'N/A' }}</div>
            </div>
            <div class="col-md-3 border-left">
                <div class="v-sub-label">Year Model</div>
                <div class="v-sub-value">{{ $vehicle->year_model ?? 'N/A' }}</div>
            </div>
            <div class="col-md-3 border-left">
                <div class="v-sub-label">Color</div>
                <div class="v-sub-value text-uppercase">{{ $vehicle->color }}</div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <div class="v-sub-label">Policy Status</div>
                @if($issuance)
                    <div class="text-active-blue mb-1">
                        ACTIVE 
                        <span class="ml-2" style="font-size: 0.9rem; color: #0056b3; font-weight: 700;">
                            {{ \Carbon\Carbon::parse($issuance->created_at)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($issuance->created_at)->addYear()->format('M d, Y') }}
                        </span>
                    </div>
                @else
                    <div class="text-danger font-weight-bold mb-1">INACTIVE</div>
                @endif

                <div class="mt-3">
                    <div class="v-sub-label" style="margin-top: 0;">Last Updated</div>
                    <div class="v-sub-value small text-muted-custom">
                        {{-- Added 'h:i A' to include the time --}}
                        @if($issuance)
                            {{ \Carbon\Carbon::parse($issuance->created_at)->format('M d, Y h:i A') }}
                        @else
                            {{ $vehicle->updated_at ? $vehicle->updated_at->format('M d, Y h:i A') : 'N/A' }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-flex align-items-end justify-content-end">
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-custom-size btn-close-theme shadow-sm mr-2">
                        <i class="fas fa-times mr-2"></i> Close
                    </a>
                    <button type="button" 
                            class="btn btn-custom-size btn-edit-theme shadow-sm" 
                            data-toggle="modal" 
                            data-target="#editVehicleModal">
                        <i class="fas fa-edit mr-2"></i> Edit Record
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@include('admin.vehicles.modal')
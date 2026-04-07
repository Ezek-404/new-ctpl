@extends('adminlte::page')

@section('title', 'Print COC - Commercial Vehicle')

@section('content')
<div class="container-fluid mt-3">
    {{-- Navigation and Actions (Hidden during print) --}}
    <div class="card card-primary card-outline no-print">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_coc" data-toggle="tab">COC</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_policy" data-toggle="tab">POLICY</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_invoice" data-toggle="tab">SERVICE INVOICE</a></li>
            </ul>
            <div class="ml-auto p-2 action-buttons">
                <button onclick="window.print()" class="btn btn-success shadow-sm"><i class="fas fa-print"></i> Print Current</button>
                <a href="{{ route('admin.ctpl.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-plus"></i> New Issuance</a>
            </div>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div class="tab-content">
        <div class="tab-pane active" id="tab_coc">
            {{-- This wrapper applies the Excel margins during print --}}
            <div class="excel-margin-wrapper">
                <div class="print-paper">
                    {{-- Header Section --}}

                    <div class="field policy-no">{{ $issuance->policy_no }}</div>
                    <div class="field assured">{{ $issuance->vehicle->assured }}</div>
                    <div class="field address">{{ $issuance->vehicle->address }}</div>
                    <div class="field date-issued">{{ $issuance->created_at->format('M-d-y') }}</div>
                    <div class="field date-from">{{ $issuance->created_at->format('M-d-y') }}</div>
                    <div class="field date-to">{{ $issuance->created_at->copy()->addYear()->format('M-d-y') }}</div>

                    {{-- Vehicle Row --}}
                    <div class="field year">{{ $issuance->vehicle->year_model }}</div>
                    <div class="field make">{{ $issuance->vehicle->make }}</div>
                    <div class="field type">{{ $issuance->vehicle->denomination }}</div>
                    <div class="field color"><div>{{ $issuance->vehicle->color }}</div></div>
                    <div class="field file">
                        {{ preg_replace('/^(\d{6})0+(\d+)/', '$1-$2', $issuance->vehicle->file_no) }}
                    </div>

                    {{-- Identification Row --}}
                    <div class="field plate">{{ $issuance->vehicle->plate_no }}</div>
                    <div class="field chassis">{{ $issuance->vehicle->chassis_no }}</div>
                    <div class="field engine">{{ $issuance->vehicle->engine_no }}</div>
                </div>
            </div>
        </div>

        {{-- Policy Tab --}}
        <div class="tab-pane" id="tab_policy">
            @include("admin.ctpl.print.{$folder}.policy")
        </div>
        
        {{-- Invoice Tab --}}
        <div class="tab-pane" id="tab_invoice">
            @include("admin.ctpl.print.{$folder}.invoice")
        </div>
    </div>
</div>

<style>
    /* 1. PHYSICAL PAPER DIMENSIONS */
    .print-paper {
        background-color: white;
        background-image: url('/images/coc_{{ $folder }}.png');
        background-size: contain;
        background-repeat: no-repeat;
        width: 8.5in;
        height: 6in;
        position: relative;
        text-transform: uppercase;
        font-family: "Times New Roman", Times, serif;
    }

    .field { 
        position: absolute; 
        color: black;
        font-size: 18px;
        font-weight: bold; /* Extra bold for impact printing */
        line-height: 1; 
    }

    /* 2. JEFFREY ALIGNMENT COORDINATES */
    .coc-no      { top: 0in; left: 0in; color: #d9534f; font-size: 24px; font-family: "Arial", sans-serif;}
    .policy-no   { top: 1.21in; left: 7.43in; }
    .assured     { top: 1.65in; left: 0.03in; width: 4in; font-size: 17px; white-space: normal; line-height: 1.1;}
    .address     { top: 2.06in; left: 0.03in; width: 3.5in; font-size: 16px; white-space: normal; line-height: 1.1; }
    .date-issued { top: 2.01in; left: 5.60in; }
    .date-from   { top: 2.65in; left: 5.60in; }
    .date-to     { top: 2.65in; left: 7.33in; }
    .year        { top: 3.30in; left: 0.03in; }
    .make        { top: 3.30in; left: 1.50in; }
    .type        { top: 3.30in; left: 3.54in; }
    .color       { top: 3.30in; left: 5.54in; width: 1.3in; display: block; overflow: hidden;}
    .color div   { white-space: normal; line-height: 0.9; }
    .file        { top: 3.30in; left: 7.12in; }
    .plate       { top: 3.68in; left: 0.03in; }
    .chassis     { top: 3.68in; left: 1.50in; }
    .engine      { top: 3.68in; left: 3.6in; }

    /* 3. EXCEL MARGIN APPLICATION (@media print) */
    @media print {
        @page {
            size: letter portrait;
            margin: 0; /* Let CSS handle the margins */
        }
        
        body { margin: 0; padding: 0; }
        .no-print { display: none !important; }

        /* The actual translation of your Excel Margins */
        .excel-margin-wrapper {
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
        }

        .print-paper {
            background-image: none !important;
            box-shadow: none;
            margin: 0;
            transform: scale(1) !important;
        }
    }

    /* Screen Preview Scaling */
    @media screen and (max-width: 992px) {
        .print-paper {
            transform: scale(0.5); 
            transform-origin: top left;
            margin-bottom: -3in;
        }
    }
</style>
@stop
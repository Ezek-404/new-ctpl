@extends('adminlte::page')

@section('title', 'Print COC - Tricycle')

@section('content')
<div class="container-fluid mt-3">
    {{-- Navigation and Actions --}}
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
            <div class="preview-container">
                <div class="excel-margin-wrapper">
                    <div class="print-paper">
                        <div class="field coc-no">{{ $issuance->coc->coc_no }}</div>
                        <div class="field policy-no">{{ $issuance->policy_no }}</div>
                        <div class="field assured">{{ $issuance->vehicle->assured }}</div>
                        <div class="field address">{{ $issuance->vehicle->address }}</div>
                        <div class="field date-issued">{{ $issuance->created_at->format('M-d-y') }}</div>
                        <div class="field date-from">{{ $issuance->created_at->format('M-d-y') }}</div>
                        <div class="field date-to">{{ $issuance->created_at->copy()->addYear()->format('M-d-y') }}</div>

                        <div class="field year">{{ $issuance->vehicle->year_model }}</div>
                        <div class="field make">{{ $issuance->vehicle->make }}</div>
                        <div class="field type">{{ $issuance->vehicle->denomination }}</div>
                        <div class="field color"><div>{{ $issuance->vehicle->color }}</div></div>
                        <div class="field file">{{ preg_replace('/^(\d{6})0+(\d+)/', '$1-$2', $issuance->vehicle->file_no) }}</div>

                        <div class="field plate">{{ $issuance->vehicle->plate_no }}</div>
                        <div class="field chassis">{{ $issuance->vehicle->chassis_no }}</div>
                        <div class="field engine">{{ $issuance->vehicle->engine_no }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab_policy">
            @include("admin.ctpl.print.{$folder}.policy")
        </div>
        
        <div class="tab-pane" id="tab_invoice">
            @include("admin.ctpl.print.{$folder}.invoice")
        </div>
    </div>
</div>

<style>
    /* 1. BASE STYLES (Shared) */
    .preview-container {
        display: flex;
        justify-content: center;
        background-color: #f4f6f9;
        padding: 40px 0;
    }

    .print-paper {
        background-color: white;
        width: 8.5in;
        height: 6in;
        position: relative;
        text-transform: uppercase;
        /* Forced Times New Roman at 18px */
        font-family: "Times New Roman", Times, serif !important;
    }

    .field { 
        position: absolute; 
        color: black;
        font-weight: bold;
        font-family: "Times New Roman", Times, serif !important;
        font-size: 21px; 
        line-height: 1; 
        z-index: 10;
    }

    /* 2. SCREEN VIEW STYLES (Properly Aligned to the Image) */
    @media screen {
        .print-paper {
            background-image: url('/images/coc_{{ $folder }}.png');
            background-size: 100% 100%; 
            /* Increased size for better visibility on monitor */
            width: 11in; 
            height: 7.7in; 
            position: relative;
            text-transform: uppercase;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
        }

        /* Nudged Screen Coordinates to fit white boxes */
        .coc-no      { top: 1.53in; left: 8.65in;  color: #d9534f; font-size: 34px; }
        .policy-no   { top: 2.27in; left: 9.67in; }
        .assured     { top: 2.85in; left: 0.60in; width: 5.5in; }
        .address     { top: 3.55in; left: 0.60in; width: 5.5in; } /* Slightly smaller for address overflow */
        .date-issued { top: 3.25in; left: 7.15in; }
        .date-from   { top: 4.07in; left: 7.15in; }
        .date-to     { top: 4.07in; left: 9.37in; }
        
        .year        { top: 4.75in; left: 0.60in; }
        .make        { top: 4.75in; left: 2.35in; }
        .type        { top: 4.75in; left: 4.65in; }
        .color       { top: 4.75in; left: 6.85in; width: 1.9in; }
        .file        { top: 4.75in; left: 8.80in;}
        
        .plate       { top: 5.23in; left: 0.60in; }
        .chassis     { top: 5.23in; left: 2.35in; }
        .engine      { top: 5.23in; left: 5.25in; }
    }

    /* 3. PRINT VIEW STYLES */
    @media print {
        @page { size: letter portrait; margin: 0; }
        
        body { margin: 0; padding: 0; background: none !important; }
        .no-print { display: none !important; }
        .preview-container { padding: 0; background: none; display: block; }

        /* HIDE THE COC NUMBER IN PRINTING */
        .coc-no { display: none !important; }

        .excel-margin-wrapper {
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
        }

        .print-paper {
            background-image: none !important;
            box-shadow: none;
        }

        /* Your exact coordinates for physical alignment */
        .policy-no   { top: 1.12in; left: 7.43in; }
        .assured     { top: 1.65in; left: 0.23in; width: 4in; }
        .address     { top: 2.06in; left: 0.23in; width: 3.5in; }
        .date-issued { top: 1.92in; left: 5.60in; }
        .date-from   { top: 2.52in; left: 5.60in; }
        .date-to     { top: 2.52in; left: 7.33in; }
        
        .year        { top: 3.08in; left: 0.35in; }
        .make        { top: 3.08in; left: 1.57in; }
        .type        { top: 3.08in; left: 4.1in; }
        .color       { top: 3.08in; left: 5.54in; width: 1.3in; }
        .file        { top: 3.08in; left: 7.12in; }
        
        .plate       { top: 3.50in; left: 0.23in; }
        .chassis     { top: 3.50in; left: 1.75in; }
        .engine      { top: 3.50in; left: 4.05in; }

        .field { font-size: 18px !important; }
    }
</style>
@stop

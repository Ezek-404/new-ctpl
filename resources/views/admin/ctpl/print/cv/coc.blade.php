@extends('adminlte::page')

@section('title', 'Print COC & Policy')

@section('content')
<div class="container-fluid mt-3">
    {{-- Navigation and Actions --}}
    <div class="card card-primary card-outline no-print">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item">
                    <a class="nav-link active" href="#tab_merged" data-toggle="tab">COC & POLICY</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tab_invoice" data-toggle="tab">SERVICE INVOICE</a>
                </li>
            </ul>
            <div class="ml-auto p-2 action-buttons">
                <button onclick="window.print()" class="btn btn-success shadow-sm">
                    <i class="fas fa-print"></i> Print Documents
                </button>
                <a href="{{ route('admin.ctpl.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-plus"></i> New Issuance
                </a>
            </div>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div class="tab-content">
        <div class="tab-pane active" id="tab_merged">
            
            {{-- SECTION 1: COC --}}
            <div class="document-section coc-section">
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

            {{-- SECTION 2: POLICY --}}
            <div class="document-section policy-section">
                <div class="policy-container">
                    <div class="excel-margin-wrapper">
                        <div class="policy-print-paper">
                            <div class="p_field p_policy-no">{{ $issuance->policy_no }}</div>
                            <div class="p_field p_assured">{{ $issuance->vehicle->assured }}</div>
                            <div class="p_field p_address">{{ $issuance->vehicle->address }}</div>
                            <div class="p_field p_date-issued">{{ $issuance->created_at->format('M-d-y') }}</div>
                            <div class="p_field p_date-from">{{ $issuance->created_at->format('M-d-y') }}</div>
                            <div class="p_field p_date-to">{{ $issuance->created_at->copy()->addYear()->format('M-d-y') }}</div>

                            <div class="p_field p_year">{{ $issuance->vehicle->year_model }}</div>
                            <div class="p_field p_make">{{ $issuance->vehicle->make }}</div>
                            <div class="p_field p_type">{{ $issuance->vehicle->denomination }}</div>
                            <div class="p_field p_color"><div>{{ $issuance->vehicle->color }}</div></div>
                            <div class="p_field p_file">{{ preg_replace('/^(\d{6})0+(\d+)/', '$1-$2', $issuance->vehicle->file_no) }}</div>

                            <div class="p_field p_plate">{{ $issuance->vehicle->plate_no }}</div>
                            <div class="p_field p_chassis">{{ $issuance->vehicle->chassis_no }}</div>
                            <div class="p_field p_engine">{{ $issuance->vehicle->engine_no }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="tab-pane" id="tab_invoice">
            @include("admin.ctpl.print.{$folder}.invoice")
        </div>
    </div>
</div>

<style>
    /* 1. SCREEN STYLES */
    .preview-container, .policy-container {
        display: flex;
        justify-content: center;
        background-color: #343a40; 
        padding: 20px 0;
    }

    .print-paper, .policy-print-paper {
        background-color: white;
        position: relative;
        text-transform: uppercase;
        font-family: "Times New Roman", Times, serif !important;
        box-shadow: 0 0 25px rgba(0,0,0,0.3);
    }

    .field, .p_field { 
        position: absolute; 
        color: black;
        font-weight: bold;
        line-height: 1; 
        z-index: 10;
    }

    @media screen {
        .print-paper {
            background-image: url('/images/coc_{{ $folder }}.png');
            background-size: 100% 100%; 
            width: 11in; height: 7.7in; 
        }
        .policy-print-paper {
            background-image: url('/images/cv_policy.jpg');
            background-size: 100% 100%; 
            width: 12in; height: 18in; 
        }

        /* All your Screen Coordinates here (Kept same as before) */
        .coc-no      { top: 1.68in; left: 8.62in; color: #d9534f; font-size: 34px; }
        .policy-no   { top: 2.40in; left: 9.67in; font-size: 21px; }
        .assured     { top: 2.90in; left: 0.55in; width: 5.5in; font-size: 21px;}
        .address     { top: 3.60in; left: 0.55in; width: 5.5in; font-size: 21px;}
        .date-issued { top: 3.37in; left: 7.10in; font-size: 21px;}
        .date-from   { top: 4.16in; left: 7.10in; font-size: 21px;}
        .date-to     { top: 4.16in; left: 9.30in; font-size: 21px;}
        .year { top: 4.98in; left: 0.55in; font-size: 21px;}
        .make { top: 4.98in; left: 2.30in; font-size: 21px;}
        .type { top: 4.98in; left: 4.57in; font-size: 21px;}
        .color { top: 4.98in; left: 6.80in; width: 1.9in; font-size: 21px;}
        .file { top: 4.98in; left: 8.80in; font-size: 21px;}
        .plate { top: 5.43in; left: 0.55in; font-size: 21px;}
        .chassis { top: 5.43in; left: 2.30in; font-size: 21px;}
        .engine { top: 5.43in; left: 4.98in; font-size: 21px;}

        /* Policy Screen Coordinates */
        .p_policy-no   { top: 2.90in; left: 9.97in; font-size: 16px;}
        .p_assured     { top: 3.35in; left: 1.15in; width: 5.5in; font-size: 16px;}
        .p_address     { top: 3.75in; left: 1.15in; width: 5.5in; font-size: 16px;}
        .p_date-issued { top: 3.62in; left: 7.50in; font-size: 16px;}
        .p_date-from   { top: 4.26in; left: 7.50in; font-size: 16px;}
        .p_date-to     { top: 4.26in; left: 9.97in; font-size: 16px;}
        .p_year        { top: 4.78in; left: 1.15in; font-size: 16px;}
        .p_make        { top: 4.78in; left: 3.07in; font-size: 16px;}
        .p_type        { top: 4.80in; left: 5.11in; font-size: 16px;}
        .p_color       { top: 4.80in; left: 7.11in; width: 1.9in; font-size: 16px;}
        .p_file        { top: 4.80in; left: 9.31in; font-size: 16px;}
        .p_plate       { top: 5.13in; left: 1.15in; font-size: 16px;}
        .p_chassis     { top: 5.16in; left: 3.07in; font-size: 16px;}
        .p_engine      { top: 5.16in; left: 5.11in; font-size: 16px;}
    }

    /* 2. PRINT STYLES */
    @media print {
        @page { size: letter portrait; margin: 0; }
        body { margin: 0; padding: 0; background: none !important; }
        .no-print { display: none !important; }

        /* CRITICAL: Force Policy to Page 2 */
        .coc-section { 
            page-break-after: always !important; 
            break-after: page !important;
            display: block !important;
        }

        .preview-container, .policy-container { 
            padding: 0; background: none; display: block; 
        }

        .excel-margin-wrapper {
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
        }

        .print-paper, .policy-print-paper {
            background-image: none !important;
            box-shadow: none;
            width: 8.5in; height: 6in;
        }

        /* COC Print Coordinates */
        .coc-no { display: none !important; }
        .policy-no   { top: 1.21in; left: 7.43in; font-size: 18px !important; }
        .assured     { top: 1.65in; left: 0.03in; width: 5in; font-size: 18px !important; }
        .address     { top: 2.06in; left: 0.03in; width: 4in; font-size: 18px !important; }
        .date-issued { top: 2.01in; left: 5.60in; font-size: 18px !important; }
        .date-from   { top: 2.65in; left: 5.60in; font-size: 18px !important; }
        .date-to     { top: 2.65in; left: 7.33in; font-size: 18px !important; }
        .year { top: 3.33in; left: 0.03in; font-size: 18px !important; }
        .make { top: 3.33in; left: 1.50in; font-size: 18px !important; }
        .type { top: 3.33in; left: 3.54in; font-size: 18px !important; }
        .color { top: 3.33in; left: 5.54in; width: 1.3in; font-size: 18px !important; }
        .file { top: 3.33in; left: 7.12in; font-size: 18px !important; }
        .plate { top: 3.71in; left: 0.03in; font-size: 18px !important; }
        .chassis { top: 3.71in; left: 1.50in; font-size: 18px !important; }
        .engine { top: 3.71in; left: 3.8in; font-size: 18px !important; }

        /* POLICY Print Coordinates */
        .p_policy-no   { top: 1.78in; left: 7.43in; }
        .p_assured     { top: 2.20in; left: 0.40in; width: 5in; }
        .p_address     { top: 2.52in; left: 0.40in; width: 4in; }
        .p_date-issued { top: 2.45in; left: 5.60in; }
        .p_date-from   { top: 2.94in; left: 5.60in; }
        .p_date-to     { top: 2.94in; left: 7.43in; }
        .p_year        { top: 3.37in; left: 0.40in; }
        .p_make        { top: 3.37in; left: 1.95in; }
        .p_type        { top: 3.37in; left: 3.8in; }
        .p_color       { top: 3.37in; left: 5.54in; width: 1.3in; }
        .p_file        { top: 3.37in; left: 7.12in; }
        .p_plate       { top: 3.71in; left: 0.40in; }
        .p_chassis     { top: 3.71in; left: 1.95in; }
        .p_engine      { top: 3.71in; left: 3.8in; }

        .p_field { font-size: 16px !important; }
    }
</style>
@stop
<style>
    /* 1. BASE STYLES (Shared) */
    .policy-container {
        display: flex;
        justify-content: center;
        /* Dark dashboard background */
        background-color: #343a40; 
        padding: 40px 0;
        border-radius: 0 0 .25rem .25rem;
    }

    /* Dark theme for the navigation card */
    .card.no-print {
        background-color: #3f474e;
        border-color: #4b545c;
    }
    
    .nav-pills .nav-link {
        color: #ced4da;
    }

    .policy-print-paper {
        background-color: white;
        width: 8.5in;
        height: 6in;
        position: relative;
        text-transform: uppercase;
        /* Forced Times New Roman at 18px */
        font-family: "Times New Roman", Times, serif !important;
    }

    .p_field { 
        position: absolute; 
        color: black;
        font-weight: bold;
        font-family: "Times New Roman", Times, serif !important;
        font-size: 16px; 
        line-height: 1; 
        z-index: 10;
    }

    /* 2. SCREEN VIEW STYLES (Properly Aligned to the Image) */
    @media screen {
        .policy-print-paper {
            background-image: url('/images/pc_policy.jpg');
            background-size: 100% 100%; 
            /* Increased size for better visibility on monitor */
            width: 12in; 
            height: 18in; 
            position: relative;
            text-transform: uppercase;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
        }

        /* Nudged Screen Coordinates to fit white boxes */
        .p_policy-no   { top: 2.90in; left: 9.97in; }
        .p_assured     { top: 3.35in; left: 1.15in; width: 5.5in; }
        .p_address     { top: 3.75in; left: 1.15in; width: 5.5in; } /* Slightly smaller for address overflow */
        .p_date-issued { top: 3.62in; left: 7.50in; }
        .p_date-from   { top: 4.26in; left: 7.50in; }
        .p_date-to     { top: 4.26in; left: 9.97in; }
        
        .p_year        { top: 4.78in; left: 1.15in; }
        .p_make        { top: 4.78in; left: 3.07in; }
        .p_type        { top: 4.80in; left: 5.11in; }
        .p_color       { top: 4.80in; left: 7.11in; width: 1.9in; }
        .p_file        { top: 4.80in; left: 9.31in;}
        
        .p_plate       { top: 5.13in; left: 1.15in; }
        .p_chassis     { top: 5.16in; left: 3.07in; }
        .p_engine      { top: 5.16in; left: 5.11in; }
    }

    /* 3. PRINT VIEW STYLES */
    @media print {
        @page { size: letter portrait; margin: 0; }
        
        body { margin: 0; padding: 0; background: none !important; }
        .no-print { display: none !important; }
        .policy-container { padding: 0; background: none; display: block; }

        /* HIDE THE COC NUMBER IN PRINTING */
        .coc-no { display: none !important; }

        .excel-margin-wrapper {
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
        }

        .policy-print-paper {
            background-image: none !important;
            box-shadow: none;
        }

        /* Your exact coordinates for physical alignment */
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
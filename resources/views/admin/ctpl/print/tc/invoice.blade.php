<style>
    /* 1. SHARED BASE STYLES & DARK THEME OVERRIDES */
    .invoice-container {
        display: flex;
        justify-content: center;
        /* Matches your dark dashboard background */
        background-color: #343a40; 
        padding: 40px 0;
        border-radius: 0 0 .25rem .25rem;
    }

    .invoice-wrapper {
        background-color: white;
        position: relative;
        font-family: "Times New Roman", Times, serif !important;
        text-transform: uppercase;
        /* Enhanced shadow for dark theme visibility */
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .field { 
        position: absolute; 
        color: black;
        font-weight: bold;
        line-height: 1; 
        z-index: 10;
    }

    /* 2. DASHBOARD VIEW (ON SCREEN) */
    @media screen {
        .invoice-wrapper {
            background-image: url('/images/invoice.jpg'); 
            background-size: 100% 100%;
            width: 7in; 
            height: 11in; 
        }

        .invoice-date          { top: 2.12in; left: 4.72in; font-size: 16px; }
        .invoice-received-from { top: 2.38in; left: 1.70in; width: 5in; text-align: center; font-size: 16px; }
        .invoice-plate         { top: 2.83in; left: 5.5in; font-size: 16px; }
        .invoice-amount-sub    { top: 3.05in; left: 2.62in; font-size: 18px; }
        .invoice-amount-total  { top: 8.60in; left: 5.4in; font-size: 30px; }
    }

    /* 3. PRINT VIEW (RESETS TO WHITE) */
    @media print {
        @page { size: letter portrait; margin: 0; }
        body { margin: 0; padding: 0; overflow: hidden !important; background: white !important; }
        .no-print { display: none !important; }
        
        .invoice-container { padding: 0; background: none; display: block; }

        .invoice-wrapper {
            background-image: none !important; 
            width: 8.5in;
            height: 10.5in;
            box-shadow: none;
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
        }

        .invoice-date          { top: 1.83in; left: 3.72in; }
        .invoice-received-from { top: 2.11in; left: 1in; width: 4.5in; text-align: center; }
        .invoice-plate         { top: 2.55in; left: 4.2in; }
        .invoice-amount-sub    { top: 2.75in; left: 2.42in; }
        .invoice-amount-total  { top: 7.95in; left: 4.4in; }

        .field { font-size: 18px !important; color: black !important; }
    }
</style>

<div class="invoice-container">
    <div class="invoice-wrapper">
        {{-- Top Section --}}
        <div class="field invoice-date">
            {{ $issuance->created_at->format('M-d') }} &nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{ $issuance->created_at->format('y') }}
        </div>
        <div class="field invoice-received-from">
            {{ $issuance->vehicle->assured }}
        </div>

        {{-- Middle Section --}}
        <div class="field invoice-plate">
            {{ $issuance->vehicle->plate_no }}
        </div>
        
        {{-- Pulled directly from database --}}
        <div class="field invoice-amount-sub">
            {{ number_format($issuance->amount, 2) }}
        </div>

        {{-- Bottom Section --}}
        <div class="field invoice-amount-total">
            {{ number_format($issuance->amount, 2) }}
        </div>
    </div>
</div>
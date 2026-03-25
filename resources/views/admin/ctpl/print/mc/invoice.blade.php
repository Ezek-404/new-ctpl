<style>
    /* 1. PHYSICAL PAPER DIMENSIONS (Letter: 8.5 x 11) */
    .invoice-wrapper {
        background-color: white;
        background-image: url('/images/invoice_page-0001.jpg');
        background-size: contain;
        background-repeat: no-repeat;
        width: 8.5in;
        height: 10.5in;
        position: relative;
        font-family: "Times New Roman", Times, serif;
        text-transform: uppercase;
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .field { 
        position: absolute; 
        color: black;
        font-size: 16px;
        font-weight: bold; 
        line-height: 1; 
    }

    /* 2. ALIGNMENT COORDINATES */
    .invoice-date          { top: 1.86in; left: 3.72in; }
    .invoice-received-from { top: 2.17in; left: 1.8in; width: 3in; text-align: center;}
    .invoice-plate         { top: 2.58in; left: 2.2in; }
    .invoice-amount-sub    { top: 2.81in; left: 2.42in; font-size: 18px; }
    .invoice-amount-total  { top: 8in; left: 4.6in; font-size: 26px; }
    .display_amount_val    { font-size: 24px; }

    /* Input Styling (Floating UI) */
    .amount-input-container {
        position: fixed;
        top: 80px; /* Adjusted to sit below AdminLTE navbar */
        right: 30px;
        background: #ffffff;
        padding: 20px;
        border: 2px solid #28a745;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        width: 200px;
    }

    /* 3. PRINT ENGINE FIXES */
    @media print {
        @page { size: letter portrait; margin: 0; }
        body { margin: 0; padding: 0; overflow: hidden !important; }
        
        .invoice-wrapper {
            background-image: none !important; /* Hide background for impact printing */
            padding-top: 0.75in !important;
            padding-left: 0.33in !important;
            box-shadow: none;
            margin: 0;
        }
        
        .no-print { display: none !important; }
    }
</style>

{{-- UI Input Section --}}
<div class="amount-input-container no-print">
    <label for="manual_amount"><strong>Invoice Amount:</strong></label>
    <input type="number" id="manual_amount" class="form-control" 
           value="550.00" step="0.01" 
           oninput="updatePrintAmount(this.value)"
           autofocus
           style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">
    
    <button onclick="window.print()" class="btn btn-success btn-block shadow-sm">
        <i class="fas fa-print"></i> Print Invoice
    </button>
    <small class="text-muted d-block mt-2 text-center">Press <b>Enter</b> to Print</small>
</div>

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
    <div class="field invoice-amount-sub">
        <span class="display_amount_val">-</span>
    </div>
    {{-- Bottom Section --}}
    <div class="field invoice-amount-total">
        <span class="display_amount_val">-</span>
    </div>
</div>

<script>
    // 1. Function to update the text on the page as you type
    function updatePrintAmount(val) {
        const displayElements = document.querySelectorAll('.display_amount_val');
        if (!val || val === "") {
            displayElements.forEach(el => el.innerText = "0.00");
            return;
        }
        const formatted = parseFloat(val).toFixed(2);
        displayElements.forEach(el => {
            el.innerText = isNaN(formatted) ? val : formatted;
        });
    }

    // 2. Helper function to focus and select the input text
    function focusInvoiceInput() {
        const amountInput = document.getElementById('manual_amount');
        if (amountInput) {
            // Short delay ensures the tab animation is finished before focusing
            setTimeout(() => {
                amountInput.focus();
                amountInput.select();
            }, 100); 
        }
    }

    // 3. Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('manual_amount');
        
        // Sync the initial value (550.00) so it's not a "-" on load
        if (amountInput) updatePrintAmount(amountInput.value);

        // Focus if this tab is already active on page load
        if (window.location.hash === '#tab_invoice' || document.querySelector('#tab_invoice.active')) {
            focusInvoiceInput();
        }

        // Trigger Print when pressing "Enter"
        amountInput?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                window.print();
            }
        });
    });

    // 4. FIX: Vanilla JS Tab Listener (Replaces the broken jQuery code)
    // This listens for Bootstrap tab changes without needing the '$' symbol
    document.addEventListener('shown.bs.tab', function (e) {
        // Check if the tab being shown is the Service Invoice tab
        if (e.target.getAttribute('href') === '#tab_invoice' || e.target.innerText.includes("SERVICE INVOICE")) {
            focusInvoiceInput();
        }
    });
</script>
@extends('adminlte::page')

@section('title', 'Comprehensive Insurance')

@section('css')
<style>
    /* Card and Sidebar Styling */
    .card { background-color: #343a40; color: #fff; border: 1px solid #4b545c; }
    
    /* 1. Input Label and Field Sizes */
    .form-group label { 
        font-size: 0.95rem; /* Increased from 0.9rem */
        font-weight: 600; 
        color: #e9ecef; 
        margin-bottom: 0.4rem;
    }
    .form-control {
        font-size: 1rem; /* Standard readable size for inputs */
        height: calc(2.25rem + 4px);
    }

    /* 2. Summary Table Breakdown Sizes */
    .summary-table td { 
        font-size: 1rem; /* Increased from 0.95rem for better visibility */
        padding: 6px 0; 
    }
    .summary-table td strong {
        font-size: 1.05rem;
    }

    /* 3. Amount Due (Bottom Section) */
    .summary-table .total-label { 
        font-size: 1.2rem; 
        letter-spacing: 0.5px; 
    }
    .summary-table h3 { 
        font-size: 2rem; /* Made larger for impact */
        margin-bottom: 0; 
        font-weight: 800; 
    }

    /* 4. Section Headers */
    h5.border-bottom {
        font-size: 1.25rem;
        font-weight: 700;
        padding-bottom: 10px;
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="text-white">Comprehensive Insurance</h1>
        <div>
            <x-adminlte-button label="Create Quotation" theme="success" icon="fas fa-file-invoice-dollar" 
                class="shadow-sm" data-toggle="modal" data-target="#modalQuotationXl"/>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary shadow">
        <div class="card-body">
            <p>Comprehensive Insurance Content Loaded.</p>
        </div>
    </div>

    <x-adminlte-modal id="modalQuotationXl" title="Comprehensive Insurance Quotation" theme="success" size="xl" v-centered static-backdrop>
        <form id="quotationForm">
            <div class="row">
                {{-- Left Side: Inputs --}}
                <div class="col-md-6 border-right">
                    <h5 class="text-success border-bottom pb-2 mb-3"><i class="fas fa-calculator mr-2"></i>Quotation Inputs</h5>
                    <div class="row">
                        <x-adminlte-input name="tsi" id="tsi" type="text" label="Vehicle Price (TSI)" fgroup-class="col-md-6" placeholder="0.00"/>
                        <x-adminlte-input name="od_rate" id="od_rate" type="number" label="OD/Theft Rate (%)" value="" fgroup-class="col-md-6" step="0.01" placeholder="0.00"/>
                        
                        <x-adminlte-select name="bi_limit" id="bi_limit" label="Section IV-A: Bodily Injury (BI)" fgroup-class="col-md-6">
                            <option value="100000" data-premium="270">100,000</option>
                            <option value="200000" data-premium="420">200,000</option>
                        </x-adminlte-select>

                        <x-adminlte-select name="pd_limit" id="pd_limit" label="Section IV-B: Property Damage (PD)" fgroup-class="col-md-6">
                            <option value="100000" data-premium="1095">100,000</option>
                            <option value="200000" data-premium="1245">200,000</option>
                        </x-adminlte-select>

                        <x-adminlte-input name="pa_premium" id="pa_premium" type="number" label="P.A. Premium" value="250" readonly fgroup-class="col-md-6"/>
                        <x-adminlte-input name="aog_rate" id="aog_rate" type="number" label="AOG Rate (%)" value="0.50" fgroup-class="col-md-6" step="0.01"/>
                    </div>
                </div>

                {{-- Right Side: Summary Breakdown --}}
                <div class="col-md-6 bg-light rounded p-3 text-dark">
                    <h5 class="border-bottom pb-2 mb-3 text-dark font-weight-bold" style="font-size: 1.1rem;">Premium Breakdown</h5>
                    <table class="table table-sm table-borderless mb-0 text-dark summary-table">
                        <tr><td><strong>Total Sum Insured</strong></td><td class="text-right font-weight-bold" id="out_tsi_total">0.00</td></tr>
                        <tr class="border-bottom"><td colspan="2"></td></tr>
                        <tr><td>Own Damage</td><td class="text-right font-weight-bold" id="out_od">0.00</td></tr>
                        <tr><td>Theft</td><td class="text-right font-weight-bold" id="out_theft">0.00</td></tr>
                        <tr><td>Act of God</td><td class="text-right font-weight-bold" id="out_aog">0.00</td></tr>
                        <tr><td>Section IV-A</td><td class="text-right font-weight-bold" id="out_bi">0.00</td></tr>
                        <tr><td>Section IV-B</td><td class="text-right font-weight-bold" id="out_pd">0.00</td></tr>
                        <tr><td>P.A.</td><td class="text-right font-weight-bold" id="out_pa">0.00</td></tr>
                        
                        <tr><td><strong>Premium</strong></td><td class="text-right font-weight-bold" id="out_subtotal">0.00</td></tr>
                        <tr><td>V.A.T. (12%)</td><td class="text-right font-weight-bold" id="out_vat">0.00</td></tr>
                        
                        <tr class="border-top text-primary font-weight-bold"><td><strong>Net Premium</strong></td><td class="text-right font-weight-bold" id="out_net">0.00</td></tr>
                        
                        <tr><td>Doc. Stamps (12.50%)</td><td class="text-right font-weight-bold" id="out_ds">0.00</td></tr>
                        <tr><td>Municipal Tax (.11%)</td><td class="text-right font-weight-bold" id="out_lgt">0.00</td></tr>
                        
                        <tr class="border-top bg-dark text-white">
                            <td class="p-2 align-middle"><span class="total-label"><strong>AMOUNT DUE</strong></span></td>
                            <td class="p-2 text-right"><h3>₱ <span id="out_total">0.00</span></h3></td>
                        </tr>
                    </table>
                </div>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="secondary" label="Cancel" data-dismiss="modal"/>
                <x-adminlte-button theme="success" label="Save & Generate Quotation" icon="fas fa-print"/>
            </x-slot>
        </form>
    </x-adminlte-modal>
@stop

@section('js')
<script>
$(document).ready(function() {
    function formatMoney(num) {
        return num.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function runQuotation() {
        // 1. Clean Inputs
        const vehiclePrice = parseFloat($('#tsi').val().replace(/,/g, '')) || 0;
        const odRate = parseFloat($('#od_rate').val()) || 0;
        
        // Dynamically get the AOG rate from the input field
        const aogRate = parseFloat($('#aog_rate').val()) || 0;

        const biLimit = parseFloat($('#bi_limit').val()) || 0;
        const biPremium = parseFloat($('#bi_limit').find(':selected').data('premium')) || 0;
        const pdLimit = parseFloat($('#pd_limit').val()) || 0;
        const pdPremium = parseFloat($('#pd_limit').find(':selected').data('premium')) || 0;
        const paPremium = 250; 

        // 2. Calculations
        const totalSumInsured = vehiclePrice + biLimit + pdLimit + 50000;

        const ownDamage = vehiclePrice * (odRate / 100) * 0.60;
        const theft = vehiclePrice * (odRate / 100) * 0.40;
        
        // Calculate AOG using the editable rate
        const aog = vehiclePrice * (aogRate / 100);

        const basePremium = ownDamage + theft + aog + biPremium + pdPremium + paPremium;

        // 3. Taxes & Net Logic
        const vat = basePremium * 0.12;
        const netPremium = basePremium + vat;
        const docStamps = Math.round((basePremium / 4) + 0.49) * 0.5;
        const municipalTax = basePremium * 0.0011;

        const amountDue = netPremium + docStamps + municipalTax;

        // 4. Update UI
        $('#out_tsi_total').text(formatMoney(totalSumInsured));
        $('#out_od').text(formatMoney(ownDamage));
        $('#out_theft').text(formatMoney(theft));
        $('#out_aog').text(formatMoney(aog));
        $('#out_bi').text(formatMoney(biPremium));
        $('#out_pd').text(formatMoney(pdPremium));
        $('#out_pa').text(formatMoney(paPremium));
        $('#out_subtotal').text(formatMoney(basePremium));
        $('#out_vat').text(formatMoney(vat));
        $('#out_net').text(formatMoney(netPremium));
        $('#out_ds').text(formatMoney(docStamps));
        $('#out_lgt').text(formatMoney(municipalTax));
        $('#out_total').text(formatMoney(amountDue));
    }

    // Ensure the calculation triggers when AOG rate is changed
    $(document).on('input change', '#aog_rate', function() {
        runQuotation();
    });

    // Modal behavior and real-time comma formatting
    $('#modalQuotationXl').on('shown.bs.modal', function () { $('#tsi').focus(); });
    $(document).on('input', '#tsi', function() {
        let val = $(this).val().replace(/,/g, '');
        if (!isNaN(val) && val.length > 0) {
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
        runQuotation();
    });

    $(document).on('input change', 'input, select', function() { runQuotation(); });
    runQuotation();
});
</script>
@stop
@extends('adminlte::page')

@section('title', 'Comprehensive Insurance')

@section('css')
<style>
    /* Card and Sidebar Styling */
    .card { background-color: #343a40; color: #fff; border: 1px solid #4b545c; }
    
    /* Summary Table Font Adjustments */
    .summary-table td { font-size: 0.95rem; padding: 4px 0; }
    .summary-table .total-label { font-size: 1.1rem; letter-spacing: 0.5px; }
    .summary-table h3 { font-size: 1.75rem; margin-bottom: 0; font-weight: 700; }
    
    /* Input Label Adjustments */
    .form-group label { font-size: 0.9rem; font-weight: 600; color: #e9ecef; }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="text-white">Comprehensive Insurance</h1>
        <div>
            {{-- Button to trigger the Extra Large Modal --}}
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
                <div class="col-md-7 border-right">
                    <h5 class="text-success border-bottom pb-2 mb-3"><i class="fas fa-calculator mr-2"></i>Quotation Inputs</h5>
                    <div class="row">
                        {{-- TSI changed to type="text" for comma support --}}
                        <x-adminlte-input name="tsi" id="tsi" type="text" label="TSI (Total Sum Insured)" fgroup-class="col-md-6" placeholder="0.00" value=""/>
                        
                        <x-adminlte-input name="od_rate" id="od_rate" type="number" label="OD/Theft Rate (%)" value="" fgroup-class="col-md-6" step="0.01" placeholder="0.00"/>
                        
                        <x-adminlte-select name="bi_limit" id="bi_limit" label="Excess Bodily Injury (BI)" fgroup-class="col-md-6">
                            <option value="100000" data-premium="270">100,000</option>
                            <option value="200000" data-premium="420">200,000</option>
                        </x-adminlte-select>

                        <x-adminlte-select name="pd_limit" id="pd_limit" label="Property Damage (PD)" fgroup-class="col-md-6">
                            <option value="100000" data-premium="1095">100,000</option>
                            <option value="200000" data-premium="1245">200,000</option>
                        </x-adminlte-select>

                        <x-adminlte-input name="pa_premium" id="pa_premium" type="number" label="PA Premium" value="250" readonly fgroup-class="col-md-6"/>
                        <x-adminlte-input name="aon_rate" id="aon_rate" type="number" label="AON Rate (%)" value="0.50" fgroup-class="col-md-6" step="0.01"/>
                    </div>
                </div>

               <div class="col-md-5 bg-light rounded p-3 text-dark">
                    <h5 class="border-bottom pb-2 mb-3 text-dark font-weight-bold" style="font-size: 1.1rem;">Premium Breakdown</h5>
                    <table class="table table-sm table-borderless mb-0 text-dark summary-table">
                        <tr><td>OD / Theft</td><td class="text-right font-weight-bold" id="out_od">0.00</td></tr>
                        <tr><td>AOG (Acts of God)</td><td class="text-right font-weight-bold" id="out_aog">0.00</td></tr>
                        <tr><td>Bodily Injury (BI)</td><td class="text-right font-weight-bold" id="out_bi">0.00</td></tr>
                        <tr><td>Property Damage (PD)</td><td class="text-right font-weight-bold" id="out_pd">0.00</td></tr>
                        <tr><td>Personal Accident (PA)</td><td class="text-right font-weight-bold" id="out_pa">0.00</td></tr>
                        <tr class="border-top">
                            <td class="font-weight-bold text-primary">Net Premium</td>
                            <td class="text-right font-weight-bold text-primary" id="out_net">0.00</td>
                        </tr>
                        {{-- DS Row added --}}
                        <tr><td>Doc. Stamp (DS)</td><td class="text-right font-weight-bold" id="out_ds">0.00</td></tr>
                        <tr><td>E-Vat (12%)</td><td class="text-right font-weight-bold" id="out_vat">0.00</td></tr>
                        <tr><td>L.G.T</td><td class="text-right font-weight-bold" id="out_lgt">0.00</td></tr>
                        <tr class="border-top bg-dark text-white">
                            <td class="p-2 align-middle"><span class="total-label"><strong>TOTAL PREMIUM</strong></span></td>
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

    // Helper to format TSI with commas while typing
    function applyCommas(input) {
        let value = input.val().replace(/,/g, ''); // Remove existing commas
        if (!isNaN(value) && value.length > 0) {
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            input.val(parts.join('.'));
        }
    }

    function runQuotation() {
        // 1. Inputs & Cleaning
        const tsiRaw = $('#tsi').val().replace(/,/g, '');
        const tsi = parseFloat(tsiRaw) || 0;
        const odRate = parseFloat($('#od_rate').val()) || 0;
        const aonRate = parseFloat($('#aon_rate').val()) || 0;
        const biPremium = parseFloat($('#bi_limit').find(':selected').data('premium')) || 0;
        const pdPremium = parseFloat($('#pd_limit').find(':selected').data('premium')) || 0;
        const paPremium = parseFloat($('#pa_premium').val()) || 0; 

        // 2. Core Calculations
        const odTotal = tsi * (odRate / 100);
        const aogTotal = tsi * (aonRate / 100);
        const netPremium = odTotal + aogTotal + biPremium + pdPremium + paPremium;

        // 3. Document Stamp (DS) Formula
        // ROUND((Net / 4) + 0.49, 0) * 0.5
        const ds = Math.round((netPremium / 4) + 0.49) * 0.5;
        
        // 4. Taxes and Fees
        const vat = netPremium * 0.12;
        const lgt = netPremium * 0.0011; // 0.11%

        const totalAmount = netPremium + ds + vat + lgt;

        // 5. Update UI
        $('#out_od').text(formatMoney(odTotal));
        $('#out_aog').text(formatMoney(aogTotal));
        $('#out_bi').text(formatMoney(biPremium));
        $('#out_pd').text(formatMoney(pdPremium));
        $('#out_pa').text(formatMoney(paPremium));
        $('#out_net').text(formatMoney(netPremium));
        $('#out_ds').text(formatMoney(ds)); 
        $('#out_vat').text(formatMoney(vat));
        $('#out_lgt').text(formatMoney(lgt));
        $('#out_total').text(formatMoney(totalAmount));
    }

    // Focus and Reset Logic
    $('#modalQuotationXl').on('shown.bs.modal', function () {
        $('#tsi').focus(); // Autofocus on open
    });

    $('#modalQuotationXl').on('show.bs.modal', function () {
        $('#tsi').val('');
        $('#od_rate').val('');
        $('#bi_limit').val('100000').trigger('change');
        $('#pd_limit').val('100000').trigger('change');
        $('#pa_premium').val('250');
        $('#aon_rate').val('0.50');
        runQuotation(); 
    });

    // Handle Input Events
    $(document).on('input', '#tsi', function() {
        applyCommas($(this)); // Apply commas as user types
        runQuotation();
    });

    $(document).on('input change', '#od_rate, #aon_rate, #bi_limit, #pd_limit', function() {
        runQuotation();
    });

    runQuotation();
});
</script>
@stop
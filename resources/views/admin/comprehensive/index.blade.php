@extends('adminlte::page')

@section('title', 'Comprehensive Insurance')

@section('css')
<style>
    /* Card and Sidebar Styling */
    .card { background-color: #343a40; color: #fff; border: 1px solid #4b545c; }
    
    /* Input Label and Field Sizes */
    .form-group label { 
        font-size: 0.95rem; 
        font-weight: 600; 
        color: #e9ecef; 
        margin-bottom: 0.4rem;
    }
    .form-control {
        font-size: 1rem; 
        height: calc(2.25rem + 4px);
    }

    /* Summary Table Breakdown Sizes */
    .summary-table td { 
        font-size: 1rem; 
        padding: 6px 0; 
    }
    .summary-table td strong {
        font-size: 1.05rem;
    }

    /* Amount Due (Bottom Section) */
    .summary-table .total-label { 
        font-size: 1.2rem; 
        letter-spacing: 0.5px; 
    }
    .summary-table h3 { 
        font-size: 2rem; 
        margin-bottom: 0; 
        font-weight: 800; 
    }

    /* Section Headers */
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
            <x-adminlte-button label="New Comprehensive" theme="primary" icon="fas fa-plus-circle" 
                class="shadow-sm mr-2" data-toggle="modal" data-target="#modalNewPolicyXl"/>
                
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

    {{-- MODAL 1: Create Quotation --}}
    <x-adminlte-modal id="modalQuotationXl" title="Comprehensive Insurance Quotation" theme="success" size="xl" v-centered static-backdrop>
        <form id="quotationForm">
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="text-success border-bottom pb-2 mb-3"><i class="fas fa-calculator mr-2"></i>Quotation Inputs</h5>
                    <div class="row">
                        <x-adminlte-input name="tsi" id="tsi" type="text" label="Vehicle Price (TSI)" fgroup-class="col-md-6" placeholder="0.00"/>
                        <x-adminlte-input name="od_rate" id="od_rate" type="number" label="OD/Theft Rate (%)" value="" fgroup-class="col-md-6" step="0.01" placeholder="0.00"/>
                        
                        <x-adminlte-select name="bi_limit" id="bi_limit" label="Bodily Injury (BI)" fgroup-class="col-md-6">
                            <option value="50000" data-premium="195">50,000</option>
                            <option value="75000" data-premium="225">75,000</option>
                            <option value="100000" data-premium="270">100,000</option>
                            <option value="150000" data-premium="345">150,000</option>
                            <option value="200000" data-premium="420">200,000</option>
                            <option value="250000" data-premium="510">250,000</option>
                            <option value="300000" data-premium="585">300,000</option>
                            <option value="400000" data-premium="675">400,000</option>
                            <option value="500000" data-premium="780">500,000</option>
                            <option value="750000" data-premium="915">750,000</option>
                            <option value="1000000" data-premium="1050">1,000,000</option>
                        </x-adminlte-select>

                        <x-adminlte-select name="pd_limit" id="pd_limit" label="Property Damage (PD)" fgroup-class="col-md-6">
                            <option value="50000" data-premium="975">50,000</option>
                            <option value="75000" data-premium="1035">75,000</option>
                            <option value="100000" data-premium="1095">100,000</option>
                            <option value="150000" data-premium="1170">150,000</option>
                            <option value="200000" data-premium="1245">200,000</option>
                            <option value="250000" data-premium="1320">250,000</option>
                            <option value="300000" data-premium="1395">300,000</option>
                            <option value="400000" data-premium="1515">400,000</option>
                            <option value="500000" data-premium="1635">500,000</option>
                            <option value="750000" data-premium="1920">750,000</option>
                            <option value="1000000" data-premium="2235">1,000,000</option>
                        </x-adminlte-select>

                        <x-adminlte-input name="pa_premium" id="pa_premium" type="number" label="P.A. Premium" value="250" readonly fgroup-class="col-md-6"/>
                        <x-adminlte-input name="aog_rate" id="aog_rate" type="number" label="AOG Rate (%)" value="0.50" fgroup-class="col-md-6" step="0.01"/>
                    </div>
                </div>

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


    {{-- MODAL 2: New Comprehensive Policy Registration --}}
    <x-adminlte-modal id="modalNewPolicyXl" title="New Comprehensive Policy Registration" theme="primary" size="xl" v-centered static-backdrop>
        <form id="newPolicyForm">
            @csrf
            <div class="row">
                
                {{-- Left Column: Policy & Assured Details --}}
                <div class="col-md-6 border-right">
                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-id-card mr-2"></i>Policy & Client Information</h5>
                    <div class="row">
                        <x-adminlte-input name="policy_no" label="Policy Number" fgroup-class="col-md-6" placeholder="Enter Policy No." required/>
                        <x-adminlte-input name="soa_no" label="SOA Number" fgroup-class="col-md-6" placeholder="Enter SOA No."/>
                        
                        <x-adminlte-input name="assured" label="Assured / Client Name" fgroup-class="col-md-12" placeholder="Full Name" required/>
                        <x-adminlte-input name="address" label="Address" fgroup-class="col-md-12" placeholder="Complete Address"/>
                        
                        <x-adminlte-input name="mortgagee" label="Mortgagee (Optional)" fgroup-class="col-md-12" placeholder="e.g., BDO, BPI (Leave blank if none)"/>
                        
                        <x-adminlte-input name="value" id="new_vehicle_price" label="Vehicle Price" fgroup-class="col-md-6" placeholder="0.00" required/>
                        <x-adminlte-input name="rate" id="new_rate" label="Rate (%)" type="number" step="0.01" fgroup-class="col-md-6" placeholder="0.00" required/>

                        <x-adminlte-select name="bi" id="new_bi" label="Bodily Injury (BI)" fgroup-class="col-md-6">
                            <option value="50000" data-premium="195">50,000</option>
                            <option value="75000" data-premium="225">75,000</option>
                            <option value="100000" data-premium="270">100,000</option>
                            <option value="150000" data-premium="345">150,000</option>
                            <option value="200000" data-premium="420">200,000</option>
                            <option value="250000" data-premium="510">250,000</option>
                            <option value="300000" data-premium="585">300,000</option>
                            <option value="400000" data-premium="675">400,000</option>
                            <option value="500000" data-premium="780">500,000</option>
                            <option value="750000" data-premium="915">750,000</option>
                            <option value="1000000" data-premium="1050">1,000,000</option>
                        </x-adminlte-select>

                        <x-adminlte-select name="pd" id="new_pd" label="Property Damage (PD)" fgroup-class="col-md-6">
                            <option value="50000" data-premium="975">50,000</option>
                            <option value="75000" data-premium="1035">75,000</option>
                            <option value="100000" data-premium="1095">100,000</option>
                            <option value="150000" data-premium="1170">150,000</option>
                            <option value="200000" data-premium="1245">200,000</option>
                            <option value="250000" data-premium="1320">250,000</option>
                            <option value="300000" data-premium="1395">300,000</option>
                            <option value="400000" data-premium="1515">400,000</option>
                            <option value="500000" data-premium="1635">500,000</option>
                            <option value="750000" data-premium="1920">750,000</option>
                            <option value="1000000" data-premium="2235">1,000,000</option>
                        </x-adminlte-select>
                    </div>
                </div>

                {{-- Right Column: Vehicle Specific Details --}}
                <div class="col-md-6">
                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-car mr-2"></i>Vehicle Specifications</h5>
                    <div class="row">
                        <x-adminlte-input name="model" label="Model" fgroup-class="col-md-4" />
                        <x-adminlte-input name="brand" label="Brand" fgroup-class="col-md-4" />
                        <x-adminlte-input name="type" label="Type / Body Type" fgroup-class="col-md-4" />
                        
                        <x-adminlte-input name="color" label="Color" fgroup-class="col-md-6" />
                        <x-adminlte-input name="plate_no" label="Plate Number" fgroup-class="col-md-6" />
                        
                        <x-adminlte-input name="chassis_no" label="Chassis Number" fgroup-class="col-md-12" />
                        <x-adminlte-input name="engine_no" label="Engine Number" fgroup-class="col-md-6" />
                        <x-adminlte-input name="file_no" label="File Number" fgroup-class="col-md-6" />
                    </div>
                </div>

            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="secondary" label="Cancel" data-dismiss="modal"/>
                <x-adminlte-button theme="primary" id="btnSavePolicy" label="Save Policy" icon="fas fa-save"/>
            </x-slot>
        </form>
    </x-adminlte-modal>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    function formatMoney(num) {
        return num.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculateValues(priceId, rateId, biId, pdId, aogRateVal) {
        const vehiclePrice = parseFloat($(priceId).val().replace(/,/g, '')) || 0;
        const odRate = parseFloat($(rateId).val()) || 0;
        const aogRate = parseFloat(aogRateVal) || 0.50;

        const biLimit = parseFloat($(biId).val()) || 0;
        const biPremium = parseFloat($(biId).find(':selected').data('premium')) || 0;
        const pdLimit = parseFloat($(pdId).val()) || 0;
        const pdPremium = parseFloat($(pdId).find(':selected').data('premium')) || 0;
        const paPremium = 250; 

        const totalSumInsured = vehiclePrice + biLimit + pdLimit + 50000;
        const ownDamage = vehiclePrice * (odRate / 100) * 0.60;
        const theft = vehiclePrice * (odRate / 100) * 0.40;
        const aog = vehiclePrice * (aogRate / 100);

        const basePremium = ownDamage + theft + aog + biPremium + pdPremium + paPremium;
        const vat = basePremium * 0.12;
        const netPremium = basePremium + vat;
        const docStamps = Math.round((basePremium / 4) + 0.49) * 0.5;
        const municipalTax = basePremium * 0.0011;
        const amountDue = netPremium + docStamps + municipalTax;

        return { totalSumInsured, ownDamage, theft, aog, biPremium, pdPremium, paPremium, basePremium, vat, netPremium, docStamps, municipalTax, amountDue };
    }

    function runQuotation() {
        let data = calculateValues('#tsi', '#od_rate', '#bi_limit', '#pd_limit', $('#aog_rate').val());

        $('#out_tsi_total').text(formatMoney(data.totalSumInsured));
        $('#out_od').text(formatMoney(data.ownDamage));
        $('#out_theft').text(formatMoney(data.theft));
        $('#out_aog').text(formatMoney(data.aog));
        $('#out_bi').text(formatMoney(data.biPremium));
        $('#out_pd').text(formatMoney(data.pdPremium));
        $('#out_pa').text(formatMoney(data.paPremium));
        $('#out_subtotal').text(formatMoney(data.basePremium));
        $('#out_vat').text(formatMoney(data.vat));
        $('#out_net').text(formatMoney(data.netPremium));
        $('#out_ds').text(formatMoney(data.docStamps));
        $('#out_lgt').text(formatMoney(data.municipalTax));
        $('#out_total').text(formatMoney(data.amountDue));
    }

    $('#modalQuotationXl').on('shown.bs.modal', function () { $('#tsi').focus(); });
    $(document).on('input', '#tsi', function() {
        let val = $(this).val().replace(/,/g, '');
        if (!isNaN(val) && val.length > 0) {
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
        runQuotation();
    });
    $(document).on('input change', '#quotationForm input, #quotationForm select', function() { 
        runQuotation(); 
    });

    $('#modalNewPolicyXl').on('shown.bs.modal', function () { $('input[name="policy_no"]').focus(); });
    $(document).on('input', '#new_vehicle_price', function() {
        let val = $(this).val().replace(/,/g, '');
        if (!isNaN(val) && val.length > 0) {
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
    });

    // --- SAVE POLICY AJAX HANDLER ---
    $('#btnSavePolicy').on('click', function(e) {
        e.preventDefault();
        
        // Front-end verification (Excluded mortgagee from validation rules checks)
        if(!$('input[name="policy_no"]').val() || !$('input[name="assured"]').val() || !$('#new_vehicle_price').val()) {
            Swal.fire('Error', 'Please fill in all required fields (Policy No, Assured, and Vehicle Price).', 'error');
            return;
        }

        let cleanedPrice = $('#new_vehicle_price').val().replace(/,/g, '');
        let formData = $('#newPolicyForm').serializeArray();
        
        formData.forEach(function(item) {
            if (item.name === 'value') {
                item.value = cleanedPrice;
            }
        });

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.comprehensive.store') }}", // <-- Fixed route target name string reference mapping pattern layout
            method: "POST",
            data: $.param(formData),
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Policy');
                if(response.success) {
                    Swal.fire('Success!', 'Policy successfully saved.', 'success');
                    $('#modalNewPolicyXl').modal('hide');
                    $('#newPolicyForm')[0].reset();
                } else {
                    Swal.fire('Failed', response.message || 'Something went wrong.', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Policy');
                let errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                let errMsg = 'An error occurred while saving.';
                if(errors) {
                    errMsg = Object.values(errors).map(val => val.join('<br>')).join('<br>');
                }
                Swal.fire('Database Error', errMsg, 'error');
            }
        });
    });

    runQuotation();
});
</script>
@stop
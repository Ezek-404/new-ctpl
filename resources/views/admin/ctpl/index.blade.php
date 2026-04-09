@extends('adminlte::page')

@section('title', 'CTPL Issuance')
<style>
    /* 1. Prevent White Background on Autocomplete/Selection */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus,
    textarea:-webkit-autofill,
    textarea:-webkit-autofill:hover,
    textarea:-webkit-autofill:focus {
        -webkit-text-fill-color: #ced4da !important; /* Your light text color */
        -webkit-box-shadow: 0 0 0px 1000px #3f474e inset !important; /* Matches your dark field color */
        transition: background-color 5000s ease-in-out 0s;
    }

    /* 2. Fix the specific focus state for the search/autofill fields */
    .form-control:focus, 
    #assured_name:focus, 
    #address:focus {
        background-color: #3f474e !important;
        color: #ffffff !important;
        border-color: #4b545c !important;
        box-shadow: none !important;
    }

    /* 3. Style the suggestion dropdown itself (if using a custom list) */
    .autocomplete-suggestions {
        background: #343a40 !important;
        border: 1px solid #4b545c;
        color: #ffffff;
    }
</style>
@section('content')
<br>
<div class="card card-outline card-primary">
    <div class="card card-outline card-info">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Vehicle Quick Search</h3>
            <div class="ml-auto">
                <span id="latest-trans-container" class="badge badge-warning p-2" style="display:none;">
                    <i class="fas fa-history"></i> Last Transaction: <span id="latest-date-display"></span>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="input-group">
                <input type="text" id="vehicle_query" class="form-control" placeholder="Type Plate Number or MV File...">
                <div class="input-group-append">
                    <button type="button" id="btn-search-vehicle" class="btn btn-info">
                        <i class="fas fa-search"></i> Search & Autofill
                    </button>
                </div>
            </div>
        </div>
    </div>
    <form id="issuanceForm" action="{{ route('admin.ctpl.store') }}" method="POST">
        @csrf
        <div class="card-body">
            {{-- Section 1: Agent & Owner --}}
            <div class="row">
                <x-adminlte-input name="assured" label="Assured Name" placeholder="Assured Name" fgroup-class="col-md-6" required/>
                <x-adminlte-input name="address" label="Address" placeholder="# Street, Barangay, City" fgroup-class="col-md-6" required/>
            </div>

            {{-- Section 2: Vehicle Info --}}
            <div class="row">
                <x-adminlte-input name="year_model" type="number" placeholder="Year Model" label="Year Model" fgroup-class="col-md-3" required/>
                <x-adminlte-input name="make" label="Make" placeholder="Make/ Manufacturer" fgroup-class="col-md-3" required/>
                <x-adminlte-input name="series" label="Series" placeholder="Series" fgroup-class="col-md-3" required/>
                <x-adminlte-input name="color" label="Color" placeholder="Color" fgroup-class="col-md-3" required/>
            </div>

            <div class="row">
                <x-adminlte-input name="file_no" label="MV File" placeholder="File Number" fgroup-class="col-md-3" required/>
                <x-adminlte-input name="plate_no" label="Plate Number" placeholder="Plate Number" fgroup-class="col-md-3" required/>
                <x-adminlte-input name="engine_no" label="Motor/Engine No." placeholder="Motor/Engine No." fgroup-class="col-md-3" required/>
                <x-adminlte-input name="chassis_no" label="Serial/Chassis No." placeholder="Serial/Chassis No." fgroup-class="col-md-3" required/>
            </div>

            <hr>

            {{-- Section 3: Policy & Validation --}}
            <div class="row">
                <div class="col-md-3">
                    <label>Denomination</label>
                    <select name="denomination" id="denomination" class="form-control" required>
                        <option value="">-- Select Denomination --</option>
                        <optgroup label="MC Type">
                            <option value="MC" data-type="MC">MC</option>
                            <option value="MTC" data-type="MC">MTC</option>
                        </optgroup>
                        <optgroup label="PC Type">
                            <option value="CAR" data-type="PC">Car</option>
                            <option value="UTILITY VEHICLE" data-type="PC">Utility Vehicle</option>
                            <option value="SEDAN" data-type="PC">Sedan</option>
                            <option value="SUV" data-type="PC">SUV</option>
                            <option value="HATCHBACK" data-type="PC">Hatchback</option>
                            <option value="COUPE" data-type="PC">Coupe</option>
                            <option value="PASSENGER CAR" data-type="PC">Passenger Car</option>
                        </optgroup>
                        <optgroup label="TC Type">
                            <option value="TRICYCLE" data-type="TC">Tricycle</option>
                        </optgroup>
                        <optgroup label="CV Type">
                            <option value="TRUCK" data-type="CV">Truck</option>
                            <option value="TRAILER" data-type="CV">Trailer</option>
                        </optgroup>
                    </select>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="coc_no" id="coc_no" label="COC Number" placeholder="Type COC Number" disabled required>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-light" id="coc-status-icon">
                                <i class="fas fa-question-circle text-muted"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    <input type="hidden" name="vehicle_id_hidden" id="vehicle_id_hidden">
                    <input type="hidden" name="coc_id_hidden" id="coc_id_hidden">
                    {{-- Error Messages --}}
                    <small id="err-missing" class="text-danger" style="display:none;">
                        <i class="fas fa-times-circle"></i> COC not found for this vehicle type.
                    </small>
                    <small id="err-used" class="text-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle"></i> This COC number is already used.
                    </small>
                </div>

                <div class="col-md-2">
                    <x-adminlte-input name="policy_no" id="policy_no" label="Policy Number" placeholder="Type Policy Number" required disabled/>
                </div>
                <div class="col-md-2">
                    <x-adminlte-input name="agent" label="Agent" required/>
                </div>
                <div class="col-md-2">
                    <x-adminlte-input name="amount" label="Policy Amount" type="number" step="0.01" placeholder="0.00" required>
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-olive">
                                <b>₱</b>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="button" id="btnClear" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-eraser"></i> Clear Form
            </button>
            <button type="submit" id="saveButton" class="btn btn-success" disabled>
                <i class="fas fa-save"></i> Save Issuance
            </button>
        </div>
    </form>
</div>

{{-- Hidden list for JS validation --}}
<div id="coc-data-container" style="display: none;">
    @foreach($allCocs as $coc)
        <span class="coc-item" 
              data-id="{{ $coc->coc_id }}" 
              data-no="{{ $coc->coc_no }}" 
              data-type="{{ $coc->coc_type }}" 
              data-status="{{ $coc->coc_status }}">
        </span>
    @endforeach
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    const denomSelect = $('#denomination');
    const cocInput = $('#coc_no');
    const cocIcon = $('#coc-status-icon');
    const errMissing = $('#err-missing');
    const errUsed = $('#err-used');
    const policyInput = $('#policy_no');
    const amountInput = $('#amount'); 
    const saveButton = $('#saveButton');
    const hiddenCocId = $('#coc_id_hidden');
    const hiddenVehicleId = $('#vehicle_id_hidden'); 
    const searchField = $('#vehicle_query');
    const form = $('#issuanceForm'); 
    const assuredInput = $('#assured');

    // --- 0. PRICE CONFIGURATION (MIN/MAX RANGES) ---
    const priceConfig = {
        // MC Type: 550 - 650
        'MC': { min: 550, max: 650 }, 
        'MTC': { min: 550, max: 650 }, 
        'TRICYCLE': { min: 550, max: 650 },
        
        // PC Type: 950 - 1380
        'CAR': { min: 950, max: 1380 }, 
        'SEDAN': { min: 950, max: 1380 }, 
        'HATCHBACK': { min: 950, max: 1380 }, 
        'PASSENGER CAR': { min: 950, max: 1380 }, 
        'COUPE': { min: 950, max: 1380 },
        
        // UV Type: 1050 - 1380
        'UTILITY VEHICLE': { min: 1050, max: 1380 }, 
        'SUV': { min: 1050, max: 1380 },
        
        // CV Type: 1500 - 2000
        'TRUCK': { min: 1500, max: 2000 }, 
        'TRAILER': { min: 1500, max: 2000 }
    };

    toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right" };

    $('input.form-control, textarea.form-control').on('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // --- 1. VALIDATION LOGIC ---
    function checkForm() {
        const cocId = hiddenCocId.val();
        const policy = policyInput.val() ? policyInput.val().trim() : "";
        const assured = assuredInput.val() ? assuredInput.val().trim() : "";
        const amount = amountInput.val() ? parseFloat(amountInput.val()) : 0;

        const isReady = (cocId !== "" && policy !== "" && assured !== "" && amount > 0);
        saveButton.prop('disabled', !isReady);
    }

    // --- 2. VEHICLE SEARCH ---
    function performVehicleSearch() {
        const query = searchField.val().trim();
        if (!query) {
            toastr.info('Please enter a Plate Number or MV File.');
            return;
        }

        const searchBtn = $('#btn-search-vehicle');
        searchBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('admin.ctpl.search_vehicle') }}",
            method: "GET",
            data: { query: query },
            success: function(response) {
                $('#latest-trans-container').hide();
                if (response.success) {
                    const match = response.data;
                    hiddenVehicleId.val(match.vehicle_id); 
                    assuredInput.val(match.assured);
                    $('#address').val(match.address);
                    $('#year_model').val(match.year_model);
                    $('#make').val(match.make);
                    $('#series').val(match.series);
                    $('#color').val(match.color);
                    $('#plate_no').val(match.plate_no);
                    $('#file_no').val(match.file_no);
                    $('#engine_no').val(match.engine_no);
                    $('#chassis_no').val(match.chassis_no);

                    if (response.latest_transaction) {
                        $('#latest-date-display').text(response.latest_transaction);
                        $('#latest-trans-container').fadeIn();
                    }

                    if (match.denomination) {
                        denomSelect.val(match.denomination).trigger('change');
                    }
                    toastr.success('Vehicle record loaded!');
                    setTimeout(() => { cocInput.focus(); }, 150); 
                } else {
                    hiddenVehicleId.val(''); 
                    toastr.info('New vehicle detected.');
                    $('#plate_no').val(query.toUpperCase());
                    setTimeout(() => { assuredInput.focus(); }, 150);
                }
                checkForm();
            },
            error: function() { toastr.error('Search failed.'); },
            complete: function() { searchBtn.prop('disabled', false).html('<i class="fas fa-search"></i>'); }
        });
    }

    // --- 3. COC VALIDATION ---
    cocInput.on('input', function() {
        const typedNo = $(this).val().trim();
        const selectedType = denomSelect.find(':selected').data('type');
        
        hiddenCocId.val(''); 
        errMissing.hide();
        errUsed.hide();
        cocIcon.html('<i class="fas fa-question-circle text-muted"></i>'); 
        policyInput.prop('disabled', true);

        if (typedNo.length > 0) {
            let matchFound = false;
            $('.coc-item').each(function() {
                const itemNo = String($(this).data('no'));
                const itemType = $(this).data('type');
                const itemStatus = $(this).data('status');

                if (itemNo === typedNo && itemType === selectedType) {
                    matchFound = true;
                    if (itemStatus === 'Available') {
                        cocIcon.html('<i class="fas fa-check-circle text-success"></i>');
                        hiddenCocId.val($(this).data('id')); 
                        policyInput.prop('disabled', false);
                    } else {
                        cocIcon.html('<i class="fas fa-exclamation-triangle text-warning"></i>');
                        errUsed.show();
                        policyInput.val('').prop('disabled', true);
                    }
                    return false; 
                }
            });

            if (!matchFound) {
                cocIcon.html('<i class="fas fa-times-circle text-danger"></i>');
                errMissing.show();
                policyInput.val('').prop('disabled', true);
            }
        }
        checkForm();
    });

    // --- 4. EVENT LISTENERS ---
    $('#btn-search-vehicle').on('click', performVehicleSearch);
    searchField.on('keydown', function(e) {
        if (e.keyCode === 13) { e.preventDefault(); performVehicleSearch(); }
    });

    policyInput.on('input', checkForm);
    assuredInput.on('input', checkForm);
    amountInput.on('input', checkForm);

    // Apply Price Rules on Denomination Change
    denomSelect.on('change', function() {
        const val = $(this).val();
        cocInput.val('').prop('disabled', !val);
        policyInput.val('').prop('disabled', true);
        hiddenCocId.val('');
        errMissing.hide();
        errUsed.hide();

        if (val && priceConfig[val]) {
            const config = priceConfig[val];
            amountInput.val(config.min.toFixed(2)); // Set default to min
            amountInput.attr('min', config.min);    // Prevent values below min
            amountInput.attr('max', config.max);    // Suggest max boundary
        } else {
            amountInput.val('').removeAttr('min').removeAttr('max');
        }

        checkForm();
    });

    $('#btnClear').on('click', function() {
        form[0].reset(); 
        searchField.val('');
        hiddenCocId.val('');
        hiddenVehicleId.val('');
        amountInput.val('').removeAttr('min').removeAttr('max');
        $('#latest-trans-container').hide(); 
        cocIcon.html('<i class="fas fa-question-circle text-muted"></i>');
        errMissing.hide();
        errUsed.hide();
        policyInput.val('').prop('disabled', true);
        cocInput.prop('disabled', true);
        checkForm(); 
        searchField.focus(); 
    });

    saveButton.on('click', function(e) {
        e.preventDefault(); 
        
        const currentDenom = denomSelect.val();
        const currentAmount = parseFloat(amountInput.val());
        const config = priceConfig[currentDenom];

        // Validate Range before submission
        if (config) {
            if (currentAmount < config.min || currentAmount > config.max) {
                toastr.error(`Invalid Amount! For ${currentDenom}, price must be between ${config.min} and ${config.max}.`);
                return;
            }
        }

        Swal.fire({
            title: 'Confirm Issuance',
            text: `Save transaction with amount ₱${currentAmount.toFixed(2)}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                form.submit();
            }
        });
    });

    searchField.focus();
});
</script>
@stop
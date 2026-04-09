@extends('adminlte::page')

@section('title', 'Edit Policy Details')
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
@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Edit Transaction #{{ $issuance->transaction_id }}</h1>
        <a href="{{ route('admin.ctpl.view', $issuance->transaction_id) }}" class="btn btn-default shadow-sm">
            <i class="fas fa-angle-left mr-1"></i> Back to View
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.ctpl.update', $issuance->transaction_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Left Side: Policy & Metadata (READ ONLY) --}}
                <div class="col-md-4">
                    <div class="card card-outline card-secondary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Policy & Agent Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>COC Number</label>
                                <input type="text" class="form-control bg-light font-weight-bold text-danger" 
                                       value="{{ $issuance->coc->coc_no }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Agent / Branch</label>
                                <input type="text" class="form-control bg-light text-uppercase" 
                                       value="{{ $issuance->agent }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Date Issued</label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ $issuance->created_at->format('M d, Y | h:i A') }}" readonly>
                            </div>
                            <div class="form-group mb-0">
                                <label>Vehicle Denomination</label>
                                <input type="text" class="form-control bg-light text-uppercase" 
                                       value="{{ $issuance->vehicle->denomination }}" readonly>
                                <small class="text-info mt-2 d-block">
                                    <i class="fas fa-info-circle mr-1"></i> Locked metadata based on COC series.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Editable Vehicle Details --}}
                <div class="col-md-8">
                    <div class="card card-outline card-warning shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold text-white">Update Technical Specifications</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="assured">Assured Name</label>
                                        <input type="text" name="assured" id="assured" class="form-control text-uppercase font-weight-bold" 
                                            value="{{ old('assured', $issuance->vehicle->assured) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address" class="form-control text-uppercase" rows="2">{{ old('address', $issuance->vehicle->address) }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="plate_no">Plate Number</label>
                                        <input type="text" name="plate_no" id="plate_no" class="form-control text-uppercase text-primary font-weight-bold" 
                                            value="{{ old('plate_no', $issuance->vehicle->plate_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year_model">Year Model</label>
                                        <input type="text" name="year_model" id="year_model" class="form-control" 
                                            value="{{ old('year_model', $issuance->vehicle->year_model) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="color">Color</label>
                                        <input type="text" name="color" id="color" class="form-control text-uppercase" 
                                            value="{{ old('color', $issuance->vehicle->color) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="make">Make & Series</label>
                                        <input type="text" name="make" id="make" class="form-control text-uppercase" 
                                            value="{{ old('make', $issuance->vehicle->make) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file_no">MV File Number</label>
                                        <input type="text" name="file_no" id="file_no" class="form-control" 
                                            value="{{ old('file_no', $issuance->vehicle->file_no) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="engine_no">Engine Number</label>
                                        <input type="text" name="engine_no" id="engine_no" class="form-control font-weight-bold" 
                                            value="{{ old('engine_no', $issuance->vehicle->engine_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chassis_no">Chassis Number</label>
                                        <input type="text" name="chassis_no" id="chassis_no" class="form-control font-weight-bold" 
                                            value="{{ old('chassis_no', $issuance->vehicle->chassis_no) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Footer fixed to match dark theme --}}
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning float-right shadow-sm px-4 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> UPDATE VEHICLE DETAILS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
@extends('adminlte::page')

@section('title', 'Edit Policy Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Edit Transaction #{{ $issuance->transaction_id }}</h1>
        <a href="{{ route('admin.ctpl.view', $issuance->transaction_id) }}" class="btn btn-default">
            <i class="fas fa-angle-left"></i> Back to View
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
                {{-- Left Side: Policy Info (Read Only) --}}
                <div class="col-md-4">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Policy Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>COC Number</label>
                                <input type="text" class="form-control" value="{{ $issuance->coc->coc_no }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Agent</label>
                                <input type="text" class="form-control text-uppercase" value="{{ $issuance->agent }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Date Issued</label>
                                <input type="text" class="form-control" value="{{ $issuance->created_at->format('M d, Y') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Editable Vehicle Details --}}
                <div class="col-md-8">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Update Vehicle Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="assured">Assured Name</label>
                                        <input type="text" name="assured" id="assured" class="form-control text-uppercase font-weight-bold" 
                                               value="{{ old('assured', $issuance->vehicle->assured) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plate_no">Plate Number</label>
                                        <input type="text" name="plate_no" id="plate_no" class="form-control text-uppercase" 
                                               value="{{ old('plate_no', $issuance->vehicle->plate_no) }}">
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
                                        <input type="text" name="engine_no" id="engine_no" class="form-control" 
                                               value="{{ old('engine_no', $issuance->vehicle->engine_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chassis_no">Chassis Number</label>
                                        <input type="text" name="chassis_no" id="chassis_no" class="form-control" 
                                               value="{{ old('chassis_no', $issuance->vehicle->chassis_no) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning float-right shadow-sm">
                                <i class="fas fa-save mr-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
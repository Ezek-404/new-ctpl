@extends('adminlte::page')

@section('title', 'COC Management')

@section('css')
<style>
    /* Custom styles for Dark Mode consistency */
    .card { background-color: #343a40; color: #fff; }
    .table { color: #fff !important; }
    .page-link { background-color: #454d55; border-color: #6c757d; color: #fff; }
    .page-item.disabled .page-link { background-color: #343a40; border-color: #6c757d; }
    .dataTables_info, .dataTables_length, .dataTables_filter { color: #fff !important; }
    
    /* Ensuring modals match the dark theme */
    .modal-content { background-color: #343a40; color: #fff; }
    .modal-header { border-bottom: 1px solid #4b545c; }
    .modal-footer { border-top: 1px solid #4b545c; }
    .nav-tabs .nav-link.active { background-color: #454d55; color: #fff; border-color: #6c757d; }

    /* Custom Scrollbar for the DataTable container */
    .dataTables_scrollBody::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-track {
        background: #343a40; 
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 4px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
        background: #495057; 
    }

    /* Fix to prevent header misalignment in dark mode */
    .dataTables_scrollHead {
        background-color: #343a40 !important;
    }
</style>
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-6">
            <h1 class="text-white">COC Management</h1>
        </div>
        <div class="col-6 text-right">
            <x-adminlte-button label="Upload Series" theme="success" icon="fas fa-plus" data-toggle="modal" data-target="#modalAddCoc" class="shadow-sm"/>
            <x-adminlte-button label="Delete Series" theme="danger" icon="fas fa-trash-alt" data-toggle="modal" data-target="#modalDeleteSeries" class="shadow-sm"/>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary shadow">
        <div class="card-body">
            @php 
                $heads = ['COC Number', 'Type', 'Status', 'Date Created']; 
                
                $config = [
                    'processing' => true,
                    'serverSide' => true,
                    'ajax' => route('admin.coc.index'), 
                    'columns' => [
                        ['data' => 'coc_no', 'name' => 'coc_no'],
                        ['data' => 'coc_type', 'name' => 'coc_type'],
                        ['data' => 'coc_status', 'name' => 'coc_status'],
                        ['data' => 'created_at', 'name' => 'created_at'],
                    ],
                    'order' => [[3, 'desc']], 
                    'autoWidth' => false,
                    
                    /* --- Scrolling & Pagination Settings --- */
                    'scrollY' => '450px',            // Sets the vertical height of the table body
                    'scrollX' => true,              // Enables horizontal scrolling
                    'scrollCollapse' => true,       // Table shrinks if there are fewer rows
                    'paging' => true,               // Keeps your pagination active
                    /* --------------------------------------- */

                    'lengthMenu' => [ [10, 50, 100, 500, 1000], [10, 50, 100, 500, 1000] ],
                    'pageLength' => 10, 
                    'columnDefs' => [
                        [
                            'targets' => [0, 1, 2, 3], 
                            'className' => 'text-left align-middle' 
                        ]
                    ],
                ];
            @endphp

            {{-- Datatable with Dark theme settings --}}
            <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" 
                striped hoverable bordered compressed theme="dark" />
        </div>
    </div>

    {{-- Modal Add COC --}}
    <x-adminlte-modal id="modalAddCoc" title="Upload COC Series" theme="success" size='lg' v-centered static-backdrop>
        <div class="nav-tabs-custom bg-dark">
            <ul class="nav nav-tabs border-secondary">
                <li class="nav-item"><a class="nav-link active" href="#manual" data-toggle="tab">Manual Upload</a></li>
                <li class="nav-item"><a class="nav-link" href="#csv" data-toggle="tab">CSV Upload</a></li>
            </ul>
            <div class="tab-content p-3 bg-dark">
                <div class="tab-pane active" id="manual">
                    <form id="manualAddForm" action="{{ route('admin.coc.seriesUpload') }}" method="POST">
                        @csrf
                        <div class="row">
                            <x-adminlte-input name="start_no" id="add_start" type="number" label="Start Number" fgroup-class="col-md-4" placeholder="e.g. 1001" required/>
                            <x-adminlte-input name="end_no" id="add_end" type="number" label="End Number" fgroup-class="col-md-4" placeholder="e.g. 1100" required/>
                            <x-adminlte-select name="coc_type" label="COC Type" fgroup-class="col-md-4" required>
                                <option value="PC">PC (Private Car)</option>
                                <option value="TC">TC (Tricycle)</option>
                                <option value="MC">MC (Motorcycle)</option>
                                <option value="CV">CV (Commercial)</option>
                            </x-adminlte-select>
                        </div>
                        <div class="text-right mt-3">
                            <x-adminlte-button type="button" id="btnCheckAdd" label="Check Availability" theme="info" class="mr-2">
                                <span id="checkSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                <span id="checkText">Check Availability</span>
                            </x-adminlte-button>
                            <x-adminlte-button type="submit" id="btnSubmitAdd" label="Generate Series" theme="success" disabled/>
                        </div>
                    </form>
                </div>

                <div class="tab-pane" id="csv">
                    <form action="{{ route('admin.coc.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-adminlte-input-file name="file" label="Upload CSV File" placeholder="Choose a file..." required/>
                        <div class="text-right">
                            <x-adminlte-button type="submit" label="Upload CSV" theme="info"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="secondary" label="Close" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>

    {{-- Modal Delete --}}
    <x-adminlte-modal id="modalDeleteSeries" title="Delete COC Series" theme="danger" v-centered static-backdrop>
        <form id="seriesDeleteForm" action="{{ route('admin.coc.seriesDelete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="row p-2">
                <x-adminlte-input name="start_no" id="del_start" label="Start Number" type="number" fgroup-class="col-md-6" required/>
                <x-adminlte-input name="end_no" id="del_end" label="End Number" type="number" fgroup-class="col-md-6" required/>
            </div>
            <div id="seriesPreview" class="alert bg-gray-dark border-secondary d-none mx-2">
                <ul class="mb-0 list-unstyled">
                    <li>Total found: <strong id="totalFound">0</strong></li>
                    <li>Available to delete: <span class="badge badge-success" id="availCount">0</span></li>
                    <li>Used (Locked): <span class="badge badge-danger" id="usedCount">0</span></li>
                </ul>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button theme="info" label="Check Range" id="btnPreviewDelete"/>
                <x-adminlte-button theme="danger" label="Confirm Delete" id="confirmSeriesDelete" class="d-none"/>
            </x-slot>
        </form>
    </x-adminlte-modal>
@stop

@section('js')
<script>
    $(document).ready(function() {
        const MAX_LIMIT = 500;
        const table = $('#table1').DataTable();

        // Staggered fade-in effect for the main card
        $('.card').hide().fadeIn(1000);

        $('#modalAddCoc').on('shown.bs.modal', function () {
            setTimeout(() => { $('#add_start').trigger('focus'); }, 150);
        });
        
        $('#modalDeleteSeries').on('shown.bs.modal', function () {
            setTimeout(() => { $('#del_start').trigger('focus'); }, 150);
        });

        function setValidationUI(isValid) {
            $('#add_start, #add_end').toggleClass('is-valid', isValid).toggleClass('is-invalid', !isValid);
            $('#btnSubmitAdd').prop('disabled', !isValid);
        }

        $('#add_start, #add_end').on('input', function() {
            $(this).removeClass('is-valid is-invalid');
            $('#btnSubmitAdd').prop('disabled', true);
        });

        $('#btnCheckAdd').on('click', function() {
            let startVal = $('#add_start').val();
            let endVal = $('#add_end').val();

            if (!/^\d+$/.test(startVal) || !/^\d+$/.test(endVal)) {
                setValidationUI(false);
                return Swal.fire({ icon: 'error', title: 'Invalid', text: 'Digits only please.', background: '#343a40', color: '#fff' });
            }

            let start = parseInt(startVal);
            let end = parseInt(endVal);

            if (end < start) {
                setValidationUI(false);
                return Swal.fire({ icon: 'error', title: 'Invalid Range', text: 'End must be > Start.', background: '#343a40', color: '#fff' });
            }

            if ((end - start) + 1 > MAX_LIMIT) {
                setValidationUI(false);
                return Swal.fire({ icon: 'warning', title: 'Limit Exceeded', text: 'Max ' + MAX_LIMIT + ' allowed.', background: '#343a40', color: '#fff' });
            }

            $('#checkSpinner').removeClass('d-none');
            $('#checkText').text(' Checking...');

            $.get("{{ route('admin.coc.previewSeries') }}", {start_no: start, end_no: end}, function(data) {
                if (parseInt(data.total) > 0) {
                    setValidationUI(false);
                    Swal.fire({ icon: 'error', title: 'Error', text: data.total + ' COC already exist.', background: '#343a40', color: '#fff' });
                } else {
                    setValidationUI(true);
                    Swal.fire({ icon: 'success', title: 'Available', text: 'Series is clear.', background: '#343a40', color: '#fff' });
                }
            }).always(function() {
                $('#checkSpinner').addClass('d-none');
                $('#checkText').text('Check Availability');
            });
        });

        $('#btnPreviewDelete').on('click', function() {
            let start = $('#del_start').val();
            let end = $('#del_end').val();

            $.get("{{ route('admin.coc.previewSeries') }}", {start_no: start, end_no: end}, function(data) {
                $('#totalFound').text(data.total);
                $('#availCount').text(data.available);
                $('#usedCount').text(data.used);
                $('#seriesPreview').removeClass('d-none');

                if (parseInt(data.used) > 0) {
                    Swal.fire({ icon: 'error', title: 'Locked', text: 'Contains "Used" records.', background: '#343a40', color: '#fff' });
                    $('#confirmSeriesDelete').addClass('d-none');
                } else if (parseInt(data.available) > 0) {
                    $('#confirmSeriesDelete').removeClass('d-none');
                }
            });
        });
        
        $('#confirmSeriesDelete').on('click', function() {
            Swal.fire({
                title: 'Confirm Delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                background: '#343a40',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) $('#seriesDeleteForm').submit();
            });
        });
    });
</script>
@stop
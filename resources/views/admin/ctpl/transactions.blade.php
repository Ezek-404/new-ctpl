@extends('adminlte::page')

@section('title', 'Saved Transactions')

@section('content_header')
    <h1>Saved Transactions</h1>
    <style>
        /* General Column Shrinking */
        .shrink-col {
            width: 1% !important;
            white-space: nowrap !important;
        }

        /* Specific style for Date & Time */
        #transTable td:first-child {
            font-weight: 600;
            color: #444;
            padding-right: 20px !important;
        }

        /* Highlight COC Number */
        #transTable td:nth-child(2) {
            font-weight: bold;
            color: #d9534f; /* Matches the red in your screenshot */
        }

        /* Assured Name Truncation */
        .truncate-assured {
            display: inline-block;
            max-width: 400px; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        /* Action Column Polish */
        #transTable td:last-child {
            width: 80px !important;
            text-align: center !important;
        }

        .action-buttons .btn {
            border: none !important;
            background: transparent !important;
            transition: transform 0.2s;
            padding: 2px 5px;
        }

        .action-buttons .btn:hover {
            transform: scale(1.2);
        }

        @media (max-width: 768px) {
            .truncate-assured { max-width: 150px; }
        }
    </style>
@stop

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            @php
                $heads = [
                    ['label' => 'Date & Time', 'class' => 'shrink-col'],
                    ['label' => 'COC No', 'class' => 'shrink-col'],
                    'Agent',
                    'Assured',
                    'Plate No.',
                    'Denomination',
                    ['label' => 'Action', 'no-export' => true, 'width' => '80px', 'class' => 'text-center']
                ];

                $config = [
                    'processing' => true,
                    'serverSide' => true,
                    'ajax' => route('admin.saved_transactions'),
                    'columns' => [
                        ['data' => 'created_at', 'name' => 'created_at', 'className' => 'shrink-col'],
                        ['data' => 'coc_no', 'name' => 'coc.coc_no', 'className' => 'shrink-col'],
                        ['data' => 'agent', 'name' => 'agent'],
                        ['data' => 'vehicle.assured', 'name' => 'vehicle.assured'],
                        ['data' => 'vehicle.plate_no', 'name' => 'vehicle.plate_no'],
                        ['data' => 'vehicle.denomination', 'name' => 'vehicle.denomination'],
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false, 'className' => 'text-center align-middle action-buttons'],
                        ['data' => 'vehicle.file_no', 'name' => 'vehicle.file_no', 'visible' => false, 'searchable' => true],
                    ],
                    'order' => [[0, 'desc']],
                    'autoWidth' => false, // Critical: stops DataTables from assigning widths automatically
                ];
            @endphp

            <x-adminlte-datatable id="transTable" :heads="$heads" :config="$config" 
                class="text-nowrap" striped hoverable bordered compressed theme="light" />
        </div>
    </div>
@stop
@section('js')
    {{-- No extra script needed as <x-adminlte-datatable> handles initialization via :config --}}
@stop
@extends('adminlte::page')

@section('title', 'Saved Transactions')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="text-white">Saved Transactions</h1>
        <button id="btnBatchAuthenticate" class="btn btn-success shadow-sm">
            <i class="fas fa-file-csv mr-1"></i> Batch Authenticate (ISAP CSV)
        </button>
    </div>

<style>
    /* Dark Theme Core Adjustments */
    .card { background-color: #343a40; color: #fff; border: 1px solid #4b545c; }
    .table { color: #fff !important; }
    
    /* Table Header - Keeps headers slightly distinct but not heavy */
    #transTable thead th { 
        vertical-align: middle; 
        background-color: #454d55; 
        color: #fff;
        border-bottom: 2px solid #4b545c;
        font-weight: 600 !important; 
    }

    /* GLOBAL UNBOLD - Forces all table cells to normal weight */
    #transTable tbody td, 
    #transTable tbody td * {
        font-weight: 400 !important; /* This targets text and nested elements like spans */
    }

    /* THE ONLY EXCEPTION - Bold COC Number */
    #transTable tbody td.coc-red { 
        font-weight: 700 !important; 
        color: #ff6b6b !important; 
    }

    /* Keep row height and borders consistent */
    #transTable td {
        vertical-align: middle !important;
        height: 50px; 
        border-top: 1px solid #4b545c;
    }

    /* Pagination & Filter visibility */
    .dataTables_info, .dataTables_length, .dataTables_filter { color: #fff !important; }
    .page-link { background-color: #454d55; border-color: #6c757d; color: #fff; }
    
    .truncate {
        max-width: 180px; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    input[type="checkbox"] {
        transform: scale(1.1);
        filter: invert(100%) hue-rotate(180deg) brightness(1.5);
    }
    #transTable .action-buttons i {
        display: inline-block !important;
        visibility: visible !important;
    }
</style>
@stop

@section('content')
    <div class="card card-outline card-primary shadow">
        <div class="card-body">
            @php
            $heads = [
                ['label' => '', 'no-export' => true, 'width' => '1%'], 
                ['label' => 'Date & Time', 'width' => '15%'],
                ['label' => 'COC No', 'width' => '10%'],
                ['label' => 'Agent', 'width' => '12%'],
                ['label' => 'Assured', 'width' => '23%'],
                ['label' => 'Plate No.', 'width' => '10%'],
                ['label' => 'Denomination', 'width' => '15%'],
                ['label' => 'Action', 'no-export' => true, 'width' => '8%']
            ];

            $config = [
                'processing' => true,
                'serverSide' => true,
                'ajax' => route('admin.saved_transactions'),
                'columns' => [
                    ['data' => 'checkbox', 'name' => 'checkbox', 'orderable' => false, 'className' => 'text-center'],
                    ['data' => 'created_at', 'name' => 'created_at'],
                    ['data' => 'coc_no', 'name' => 'coc_no', 'className' => 'coc-red'], // Remains bold
                    ['data' => 'agent', 'name' => 'agent', 'className' => 'truncate'], // Unbolded
                    ['data' => 'vehicle.assured', 'name' => 'vehicle.assured', 'className' => 'truncate'], // Unbolded (removed font-weight-bold)
                    ['data' => 'vehicle.plate_no', 'name' => 'vehicle.plate_no'],
                    ['data' => 'vehicle.denomination', 'name' => 'vehicle.denomination'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'className' => 'text-center action-buttons'],
                ],
                'order' => [[1, 'desc']],
                'autoWidth' => false,
                'lengthMenu' => [ [10, 50, 100, 500, 1000], [10, 50, 100, 500, 1000] ],
                'pageLength' => 10,
                'responsive' => true,
            ];
            @endphp

            <x-adminlte-datatable id="transTable" :heads="$heads" :config="$config" 
                class="text-nowrap" striped hoverable bordered compressed theme="dark" />
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#transTable').DataTable();

    // Initial animation for the card
    $('.card').hide().fadeIn(800);

    function clean(value, removeAllSpaces = false) {
        if (!value) return '';
        let cleaned = value.toString()
            .replace(/<[^>]*>?/gm, '') 
            .replace(/,/g, '') 
            .trim();
        if (removeAllSpaces) {
            cleaned = cleaned.replace(/\s+/g, ''); 
        }
        return cleaned;
    }

    $('#transTable thead th:first-child').html('<input type="checkbox" id="selectAll" style="cursor:pointer;">');

    $(document).on('click', '#selectAll', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    $('#btnBatchAuthenticate').on('click', function() {
        const checked = $('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Selection',
                text: 'Please select rows.',
                background: '#343a40', // Dark mode alert background
                color: '#fff'
            });
            return;
        }

        let excelData = [];
        excelData.push(["ISAP","ISAP","","","","","","","","","","",""]);
        excelData.push([
            "COC_NO","PLATE_NO","MV FILE_NO","MOTOR_NO","CHASSIS_NO",
            "INCE_DATE","EXPI_DATE","PREM_TYPE","REG_TYPE","TAX_TYPE",
            "ASSURED NAME","ASSURED TIN","MV_TYPE"
        ]);

        checked.each(function() {
            const rowData = table.row($(this).closest('tr')).data();
            let rawCOC = clean(rowData.coc_no, true);
            let formattedCOC = "010" + rawCOC;

            let dateStr = clean(rowData.created_at).split('|')[0].trim();
            let inceDateObj = new Date(dateStr);

            let inceDate = ((inceDateObj.getMonth()+1)+'').padStart(2,'0') + '/' +
                           (inceDateObj.getDate()+'').padStart(2,'0') + '/' +
                           inceDateObj.getFullYear();

            let expiDateObj = new Date(inceDateObj);
            expiDateObj.setFullYear(expiDateObj.getFullYear() + 1);

            let expiDate = ((expiDateObj.getMonth()+1)+'').padStart(2,'0') + '/' +
                           (expiDateObj.getDate()+'').padStart(2,'0') + '/' +
                           expiDateObj.getFullYear();

            let denom = clean(rowData.vehicle.denomination).toUpperCase();
            let mvType = "";
            let premType = "1";

            if (["CAR", "SEDAN", "PASSENGER CAR", "COUPE", "HATCHBACK"].includes(denom)) mvType = "C";
            else if (denom === "UTILITY VEHICLE") mvType = "UV";
            else if (denom === "SUV") mvType = "SV";
            else if (denom === "MC") mvType = "M";
            else if (denom === "MTC") mvType = "MS";
            else if (denom === "TRICYCLE") mvType = "TC";
            else if (denom === "TRUCK") mvType = "TK";
            else if (denom === "TRAILER") mvType = "TL";
            else mvType = denom;

            if (mvType === "C" || mvType === "SV" || mvType === "UV") premType = "1";
            else if (mvType === "TK") premType = "3";
            else if (["M","MS","TC","TL"].includes(mvType)) premType = "7";

            excelData.push([
                formattedCOC,
                clean(rowData.vehicle.plate_no, true),
                clean(rowData.vehicle.file_no, true),
                clean(rowData.vehicle.engine_no, true),
                clean(rowData.vehicle.chassis_no, true),
                inceDate,
                expiDate,
                premType,
                "R",
                "0",
                clean(rowData.vehicle.assured),
                "111-111-111-11111",
                mvType
            ]);
        });

        let wb = XLSX.utils.book_new();
        let ws = XLSX.utils.aoa_to_sheet(excelData);
        XLSX.utils.book_append_sheet(wb, ws, "ISAP Upload");
        XLSX.writeFile(wb, "ISAP_Upload.xlsx");
    });
});
</script>
@stop
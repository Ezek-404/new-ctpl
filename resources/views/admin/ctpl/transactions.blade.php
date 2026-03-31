@extends('adminlte::page')

@section('title', 'Saved Transactions')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Saved Transactions</h1>
        <button id="btnBatchAuthenticate" class="btn btn-success shadow-sm">
            <i class="fas fa-file-csv mr-1"></i> Batch Authenticate (ISAP CSV)
        </button>
    </div>
    <style>
        /* Table Header Alignment */
        #transTable thead th { 
            vertical-align: middle; 
            background-color: #f8f9fa; 
        }

        /* Action button hover effect */
        .action-buttons .btn {
            transition: transform 0.2s;
            padding: 2px 5px;
        }
        .action-buttons .btn:hover { transform: scale(1.2); }

        /* Prevent stacking and force ellipses (...) */
        .truncate {
            max-width: 180px; /* Adjust based on preference */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Keep row height consistent */
        #transTable td {
            vertical-align: middle !important;
            height: 50px; 
        }

        /* Fixed COC Red Style */
        .coc-red { 
            font-weight: bold; 
            color: #d9534f !important; 
        }
    </style>
@stop

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            @php
            $heads = [
                ['label' => '', 'no-export' => true, 'width' => '1%'], 
                ['label' => 'Date & Time', 'width' => '15%'],
                ['label' => 'COC No', 'width' => '10%'],
                ['label' => 'Agent', 'width' => '12%'],      // Separate column
                ['label' => 'Assured', 'width' => '23%'],    // Separate column
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
                    ['data' => 'coc_no', 'name' => 'coc_no', 'className' => 'coc-red'], 
                    ['data' => 'agent', 'name' => 'agent', 'className' => 'truncate'], // Ellipsis applied
                    ['data' => 'vehicle.assured', 'name' => 'vehicle.assured', 'className' => 'truncate font-weight-bold'], // Ellipsis applied
                    ['data' => 'vehicle.plate_no', 'name' => 'vehicle.plate_no'],
                    ['data' => 'vehicle.denomination', 'name' => 'vehicle.denomination'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => false, 'className' => 'text-center action-buttons'],
                ],
                'order' => [[1, 'desc']],
                'autoWidth' => false,
                'responsive' => true,
            ];
        @endphp

            <x-adminlte-datatable id="transTable" :heads="$heads" :config="$config" 
                class="text-nowrap" striped hoverable bordered compressed theme="light" />
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#transTable').DataTable();

    // Helper function to clean values
    function clean(value, removeAllSpaces = false) {
        if (!value) return '';

        let cleaned = value.toString()
            .replace(/<[^>]*>?/gm, '') // Remove HTML
            .replace(/,/g, '')         // Remove commas
            .trim();

        if (removeAllSpaces) {
            cleaned = cleaned.replace(/\s+/g, ''); // Remove ALL spaces
        }

        return cleaned;
    }

    // Inject checkbox into header
    $('#transTable thead th:first-child').html('<input type="checkbox" id="selectAll" style="cursor:pointer;">');

    // Select All Toggle
    $(document).on('click', '#selectAll', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // XLSX Export Logic
    $('#btnBatchAuthenticate').on('click', function() {
        const checked = $('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire('No Selection', 'Please select rows.', 'info');
            return;
        }

        let excelData = [];

        // Header rows
        excelData.push(["ISAP","ISAP","","","","","","","","","","",""]);
        excelData.push([
            "COC_NO","PLATE_NO","MV FILE_NO","MOTOR_NO","CHASSIS_NO",
            "INCE_DATE","EXPI_DATE","PREM_TYPE","REG_TYPE","TAX_TYPE",
            "ASSURED NAME","ASSURED TIN","MV_TYPE"
        ]);

        checked.each(function() {
            const rowData = table.row($(this).closest('tr')).data();

            // COC
            let rawCOC = clean(rowData.coc_no, true);
            let formattedCOC = "010" + rawCOC;

            // Date Logic
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

            // MV Type Mapping
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

            // Add row to Excel
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

        // Create Excel file
        let wb = XLSX.utils.book_new();
        let ws = XLSX.utils.aoa_to_sheet(excelData);
        XLSX.utils.book_append_sheet(wb, ws, "ISAP Upload");

        // Download XLSX
        XLSX.writeFile(wb, "ISAP_Upload.xlsx");
    });
});
</script>
@stop
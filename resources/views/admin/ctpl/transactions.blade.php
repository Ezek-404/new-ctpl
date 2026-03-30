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
<script>
$(document).ready(function() {
    var table = $('#transTable').DataTable();

    // 1. Correctly inject the checkbox into the header
    $('#transTable thead th:first-child').html('<input type="checkbox" id="selectAll" style="cursor:pointer;">');

    // 2. Select All Toggle
    $(document).on('click', '#selectAll', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // 3. CSV Export Logic (Remains unchanged with \t for Excel formatting)
    $('#btnBatchAuthenticate').on('click', function() {
        const checked = $('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire('No Selection', 'Please select rows.', 'info');
            return;
        }

        // ISAP Template Headers
        let csvContent = "ISAP,ISAP,,,,,,,,,,\n";
        csvContent += "COC_NO,PLATE_NO,MV FILE_NO,MOTOR_NO,CHASSIS_NO,INCE_DATE,EXPI_DATE,PREM_TYPE,REG_TYPE,TAX_TYPE,ASSURED NAME,ASSURED TIN,MV_TYPE\n";

        checked.each(function() {
            const rowData = table.row($(this).closest('tr')).data();
            
            // --- COC Logic: Prepend 010 and force Text format ---
            let rawCOC = rowData.coc_no.replace(/<[^>]*>?/gm, '').trim();
            let formattedCOC = `010${rawCOC}`;

            // --- Date Logic: MM/DD/YYYY ---
            let dateStr = rowData.created_at.replace(/<[^>]*>?/gm, '').split('|')[0].trim();
            let inceDateObj = new Date(dateStr);
            let inceDate = ((inceDateObj.getMonth() + 1) + '').padStart(2, '0') + '/' + 
                           (inceDateObj.getDate() + '').padStart(2, '0') + '/' + 
                           inceDateObj.getFullYear();

            // Expiry Logic: +1 Year -1 Day
            let expiDateObj = new Date(inceDateObj);
            expiDateObj.setFullYear(expiDateObj.getFullYear() + 1);
            expiDateObj.setDate(expiDateObj.getDate() - 1);
            let expiDate = ((expiDateObj.getMonth() + 1) + '').padStart(2, '0') + '/' + 
                           (expiDateObj.getDate() + '').padStart(2, '0') + '/' + 
                           expiDateObj.getFullYear();

            // --- MV_TYPE & PREMIUM_TYPE Mapping Logic ---
            let denom = (rowData.vehicle.denomination || "").toUpperCase().trim();
            let mvType = "";
            let premType = "1"; // Default fallback

            // Step 1: Map Denomination to ISAP MV_TYPE Code
            if (["CAR", "SEDAN", "PASSENGER CAR", "COUPE", "HATCHBACK"].includes(denom)) {
                mvType = "C";
            } else if (denom === "UTILITY VEHICLE") {
                mvType = "UV";
            } else if (denom === "SUV") {
                mvType = "SV";
            } else if (denom === "MC") {
                mvType = "M";
            } else if (denom === "MTC") {
                mvType = "MS";
            } else if (denom === "TRICYCLE") {
                mvType = "TC";
            } else if (denom === "TRUCK") {
                mvType = "TK";
            } else if (denom === "TRAILER") {
                mvType = "TL";
            } else {
                mvType = denom; 
            }

            // Step 2: Map Code to ISAP PREM_TYPE
            if (mvType === "C" || mvType === "SV" || mvType === "UV") {
                premType = "1";
            } else if (mvType === "TK") {
                premType = "3";
            } else if (mvType === "M" || mvType === "MS" || mvType === "TC" || mvType === "TL") {
                premType = "7";
            }

            // --- Constructing CSV Row with Excel formatting fix ---
            const row = [
                `"\t${formattedCOC}"`,                   // Force Text format for Excel
                `"\t${rowData.vehicle.plate_no}"`,
                `"\t${rowData.vehicle.file_no || ''}"`,  // Force Text format for Excel
                `"${rowData.vehicle.engine_no || ''}"`,
                `"${rowData.vehicle.chassis_no || ''}"`,
                `"${inceDate}"`,
                `"${expiDate}"`,
                `"${premType}"`,                         // Mapped Premium Type
                `"R"`,                                   // REG_TYPE
                `"0"`,                                   // TAX_TYPE
                `"${rowData.vehicle.assured.replace(/<[^>]*>?/gm, '').replace(/,/g, '')}"`,
                `"111-111-111-11111"`, 
                `"${mvType}"`                            // Mapped MV_TYPE
            ];
            csvContent += row.join(",") + "\n";
        });

        // Trigger CSV File Download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `ISAP_Upload_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();
        
        // Final cleanup and user feedback
        setTimeout(() => URL.revokeObjectURL(link.href), 100);
    });
});
</script>
@stop
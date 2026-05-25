<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Premium Sheet - {{ $policy->policy_no }}</title>
    <style>
        /* Base Reset */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            background-color: #525659; /* Dark background for professional fullscreen workspace preview */
            font-family: "Times New Roman", Times, serif !important;
            font-size: 10pt;
            font-weight: bold;
            color: #111111;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Screen Control Bar (Hidden on Print) */
        .preview-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #202124;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 9999;
        }
        .preview-title {
            color: #ffffff;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: normal;
        }
        .action-btn {
            background-color: #00fa9a;
            color: #111111;
            border: none;
            padding: 10px 20px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .action-btn:hover {
            background-color: #00c77b;
        }
        .secondary-btn {
            background-color: #3c4043;
            color: #ffffff;
            margin-right: 10px;
        }
        .secondary-btn:hover {
            background-color: #4f5357;
        }

        /* Sheet Workspace Preview container */
        .workspace-container {
            padding-top: 80px;
            padding-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        /* =========================================================================
           DYNAMIC MULTI-PAGE ORIENTATION ENGINE
        ========================================================================= */
        @page policy-layout {
            size: A4 portrait;
            margin: 0;
        }

        @page soa-layout {
            size: A4 landscape;
            margin: 0;
        }

        @page invoice-layout {
            size: A4 portrait;
            margin: 0;
        }

        /* Container Assignments & Screen Layout Mocking */
        .page-portrait, .page-landscape, .page-invoice {
            position: relative;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .page-portrait {
            page: policy-layout;
            width: 297mm;
            height: 460mm;
            page-break-after: always;
        }

        .page-landscape {
            page: soa-layout;
            width: 297mm;
            height: 210mm;
            page-break-after: always;
        }

        .page-invoice {
            page: invoice-layout;
            width: 210mm;
            height: 297mm;
            page-break-after: avoid;
        }

        /* Helper alignment line guide visibility class (toggled via button) */
        .show-bounds .page-portrait, 
        .show-bounds .page-landscape, 
        .show-bounds .page-invoice {
            outline: 2px dashed #ff0000;
        }

        /* Image Resource Definitions From public/images/ */
        .bg-policy {
            background: url("{{ asset('images/compre-policy.jpg') }}") no-repeat center top;
            background-size: 100% 100%;
        }

        .bg-soa {
            background: url("{{ asset('images/compre-soa.jpg') }}") no-repeat center top;
            background-size: 100% 100%;
        }

        .bg-invoice {
            background: url("{{ asset('images/invoice.jpg') }}") no-repeat center top;
            background-size: 100% 100%;
        }

        .print-field {
            position: absolute;
            white-space: nowrap;
        }

        /* =========================================================================
           CRITICAL PRINT-ONLY OVERRIDES
        ========================================================================= */
        @media print {
            html, body {
                background-color: #ffffff !important;
            }
            .preview-header {
                display: none !important;
            }
            .workspace-container {
                padding: 0 !important;
                gap: 0 !important;
                display: block !important;
            }
            .page-portrait, .page-landscape, .page-invoice {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important; /* Forces the browser to scale completely across sheet bounds */
            }
            .page-portrait, .page-invoice {
                height: 297mm !important;
            }
            .page-landscape {
                height: 210mm !important;
            }
        }
    </style>
</head>
<body>

    <div class="preview-header">
        <div class="preview-title">Document Preview Engine &mdash; Policy #PC-IM-{{ $policy->policy_no }}/26</div>
        <div>
            <button class="action-btn secondary-btn" onclick="toggleBoundaries()">Toggle Grid Alignment</button>
            <button class="action-btn" onclick="window.print();">Print Premium Sheet</button>
        </div>
    </div>

    @php
        // Financial & Calculation Rules Workspace
        $vehiclePrice = $policy->value ?? 0;
        $odRate = $policy->rate ?? 0;
        $aogRate = 0.50;

        $biLimitClean = (int)str_replace(',', '', $policy->bi ?? 0);
        $pdLimitClean = (int)str_replace(',', '', $policy->pd ?? 0);

        $biPremiumMap = [50000 => 195, 75000 => 225, 100000 => 270, 150000 => 345, 200000 => 420, 250000 => 510, 300000 => 585, 400000 => 675, 500000 => 780, 750000 => 915, 1000000 => 1050];
        $pdPremiumMap = [50000 => 975, 75000 => 1035, 100000 => 1095, 150000 => 1170, 200000 => 1245, 250000 => 1320, 300000 => 1395, 400000 => 1515, 500000 => 1635, 750000 => 1920, 1000000 => 2235];
        
        $biPremium = $biPremiumMap[$biLimitClean] ?? 0;
        $pdPremium = $pdPremiumMap[$pdLimitClean] ?? 0;
        $paPremium = 250.00;

        $ownDamage = $vehiclePrice * ($odRate / 100) * 0.60;
        $theft = $vehiclePrice * ($odRate / 100) * 0.40;
        $aog = $vehiclePrice * ($aogRate / 100);

        $totalSumInsured = (float)$vehiclePrice + $biLimitClean + $pdLimitClean + 50000;
        $basePremium = $ownDamage + $theft + $aog + $biPremium + $pdPremium + $paPremium;
        $vat = $basePremium * 0.12;
        $netPremium = $basePremium + $vat;
        
        $docStamps = ceil(($basePremium / 4) + 0.49) * 0.5;
        $municipalTax = $basePremium * 0.0011;
        $amountDue = $netPremium + $docStamps + $municipalTax;
    @endphp

    <div class="workspace-container" id="workspace">
        
        <div class="page-portrait bg-policy">
            <div class="print-field" style="top: 13.5%; left: 82.5%;">PC-IM-{{ $policy->policy_no }}/26</div>
            <div class="print-field" style="top: 26.2%; left: 74.5%;">{{ $policy->confirmation_no ?? 'N/A' }}</div>
            
            <div class="print-field" style="top: 29.5%; left: 5.5%; white-space: normal; width: 44%; line-height: 1.4;">
                <strong>{{ $policy->assured }}</strong><br>
                <span style="font-size: 8.5pt;">{{ $policy->address }}</span>
            </div>

            <div class="print-field" style="top: 42.8%; left: 5.5%;">{{ $policy->model }}</div>
            <div class="print-field" style="top: 42.8%; left: 21.5%;">{{ $policy->brand }}</div>
            <div class="print-field" style="top: 42.8%; left: 40.5%;">{{ $policy->type }}</div>
            <div class="print-field" style="top: 42.8%; left: 59.0%;">{{ $policy->color }}</div>
            <div class="print-field" style="top: 42.8%; left: 76.5%;">{{ $policy->file_no ?? 'NEW' }}</div>
            
            <div class="print-field" style="top: 46.2%; left: 5.5%;">{{ $policy->plate_no }}</div>
            <div class="print-field" style="top: 46.2%; left: 21.5%;">{{ $policy->chassis_no }}</div>
            <div class="print-field" style="top: 46.2%; left: 50.0%;">{{ $policy->engine_no ?? $policy->motor_no }}</div>

            <div class="print-field" style="top: 59.4%; left: 37.0%;">₱ {{ number_format($vehiclePrice, 2) }}</div>

            <div class="print-field" style="top: 81.8%; left: 10.0%;">₱ {{ number_format($biPremium, 2) }}</div>
            <div class="print-field" style="top: 81.8%; left: 47.0%;">₱ {{ number_format($pdPremium, 2) }}</div>
            <div class="print-field" style="top: 83.8%; left: 5.5%;">{{ $policy->mortgagee ?: 'NONE (PRIVATE OWNED)' }}</div>

            <div class="print-field" style="top: 59.2%; left: 81.5%;">{{ number_format($ownDamage, 2) }}</div>
            <div class="print-field" style="top: 60.6%; left: 81.5%;">{{ number_format($theft, 2) }}</div>
            <div class="print-field" style="top: 62.0%; left: 81.5%;">{{ number_format($biPremium, 2) }}</div>
            <div class="print-field" style="top: 63.4%; left: 81.5%;">{{ number_format($pdPremium, 2) }}</div>
            <div class="print-field" style="top: 66.2%; left: 81.5%;">{{ number_format($aog, 2) }}</div>
            <div class="print-field" style="top: 67.6%; left: 81.5%;">{{ number_format($paPremium, 2) }}</div>
            
            <div class="print-field" style="top: 71.0%; left: 81.5%;">{{ number_format($basePremium, 2) }}</div>
            <div class="print-field" style="top: 72.4%; left: 81.5%;">{{ number_format($docStamps, 2) }}</div>
            <div class="print-field" style="top: 73.8%; left: 81.5%;">{{ number_format($vat, 2) }}</div>
            <div class="print-field" style="top: 75.2%; left: 81.5%;">{{ number_format($municipalTax, 2) }}</div>
            
            <div class="print-field" style="top: 78.4%; left: 81.5%; font-size: 11pt;"><strong>{{ number_format($amountDue, 2) }}</strong></div>
        </div>

        <div class="page-landscape bg-soa">
            <div class="print-field" style="top: 21.2%; left: 72.0%;">SOA-{{ $policy->soa_no ?? '0974649' }}</div>
            
            <div class="print-field" style="top: 40.5%; left: 23.0%;">{{ $policy->policy_no }}</div>
            <div class="print-field" style="top: 43.8%; left: 28.5%;">₱ {{ number_format($totalSumInsured, 2) }}</div>
            
            <div class="print-field" style="top: 40.8%; left: 73.0%;">{{ number_format($basePremium, 2) }}</div>
            <div class="print-field" style="top: 44.6%; left: 73.0%;">{{ number_format($docStamps, 2) }}</div>
            <div class="print-field" style="top: 48.2%; left: 73.0%;">{{ number_format($vat, 2) }}</div>
            <div class="print-field" style="top: 52.0%; left: 73.0%;">{{ number_format($municipalTax, 2) }}</div>
            
            <div class="print-field" style="top: 71.0%; left: 73.0%; font-size: 11pt;"><strong>{{ number_format($amountDue, 2) }}</strong></div>
        </div>

        <div class="page-invoice bg-invoice">
            <div class="print-field" style="top: 8.5%; left: 74.0%;">{{ now()->format('m/d/Y') }}</div>
            
            <div class="print-field" style="top: 12.0%; left: 20.0%;">{{ $policy->assured }}</div>
            <div class="print-field" style="top: 14.8%; left: 15.0%; font-size: 9pt;">{{ $policy->address }}</div>
            
            <div class="print-field" style="top: 39.5%; left: 26.0%;">{{ $policy->policy_no }}</div>
            <div class="print-field" style="top: 39.5%; left: 66.0%;">{{ number_format($basePremium, 2) }}</div>
            <div class="print-field" style="top: 39.5%; left: 81.5%;">{{ number_format($basePremium, 2) }}</div>

            <div class="print-field" style="top: 47.8%; left: 81.5%;">{{ number_format($docStamps, 2) }}</div>
            <div class="print-field" style="top: 53.5%; left: 81.5%;">{{ number_format($municipalTax, 2) }}</div>
            
            <div class="print-field" style="top: 65.0%; left: 81.5%;">{{ number_format($basePremium, 2) }}</div>
            <div class="print-field" style="top: 70.8%; left: 81.5%;">{{ number_format($vat, 2) }}</div>
            <div class="print-field" style="top: 73.8%; left: 81.5%;">{{ number_format($basePremium, 2) }}</div>
            <div class="print-field" style="top: 76.5%; left: 81.5%;">{{ number_format($vat, 2) }}</div>

            <div class="print-field" style="top: 92.5%; left: 81.5%; font-size: 11pt;"><strong>{{ number_format($amountDue, 2) }}</strong></div>
        </div>

    </div>

    <script>
        // Diagnostic tool function to check alignment bounds on-screen
        function toggleBoundaries() {
            document.getElementById('workspace').classList.toggle('show-bounds');
        }
    </script>
</body>
</html>
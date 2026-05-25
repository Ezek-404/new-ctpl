@extends('adminlte::page')

@section('title', 'View Comprehensive Insurance Policy')

@section('css')
<style>
    /* Dark Theme Core Styles */
    .card-custom { background-color: #343a40; color: #fff; border: 1px solid #4b545c; }
    .text-muted-custom { color: #a8b2bc !important; }
    
    /* Table Calculations Sheet Styles */
    .premium-sheet-table td {
        padding: 7px 12px;
        font-size: 0.95rem;
        vertical-align: middle;
    }
    .premium-sheet-table tr.border-double-bottom td {
        border-bottom: 3px double #6c757d;
    }
    .amount-due-box {
        background-color: #212529;
        border-left: 4px solid #28a745;
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="text-white">Policy Profile: <span class="text-info">{{ $policy->policy_no }}</span></h1>
            <p class="text-muted mb-0">Manage details, values, and tax matrices for this account.</p>
        </div>
        <div>
            <a href="{{ route('admin.comprehensive.index') }}" class="btn btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Directory
            </a>
            <button class="btn btn-success shadow-sm" onclick="printPremiumSheet('{{ route('admin.comprehensive.print', $policy->id) }}')">
                <i class="fas fa-print mr-1"></i> Print Premium Sheet
            </button>
        </div>
    </div>
@stop

@section('content')
    @php
        // PHP-Side Re-computation block to mirror your insurance calculations sheet exactly
        $vehiclePrice = $policy->value ?? 0;
        $odRate = $policy->rate ?? 0;
        $aogRate = 0.50; // Reference policy fixed at 0.50%

        // FORCE CAST TO INTEGERS: Safely handles decimals, commas, or string formatting
        $biLimitClean = (int)str_replace(',', '', $policy->bi ?? 0);
        $pdLimitClean = (int)str_replace(',', '', $policy->pd ?? 0);

        // Map definitions utilizing clean integer keys matching your database numeric limits
        $biPremiumMap = [
            50000   => 195,
            75000   => 225,
            100000  => 270,
            150000  => 345,
            200000  => 420,
            250000  => 510,
            300000  => 585,
            400000  => 675,
            500000  => 780,
            750000  => 915,
            1000000 => 1050
        ];

        $pdPremiumMap = [
            50000   => 975,
            75000   => 1035,
            100000  => 1095,
            150000  => 1170,
            200000  => 1245,
            250000  => 1320,
            300000  => 1395,
            400000  => 1515,
            500000  => 1635,
            750000  => 1920,
            1000000 => 2235
        ];
        
        // Exact matrix value lookups matching the integer value
        $biPremium = $biPremiumMap[$biLimitClean] ?? 0;
        $pdPremium = $pdPremiumMap[$pdLimitClean] ?? 0;
        $paPremium = 250.00; // Fixed reference baseline premium

        // Own Damage, Theft, and Act of God breakdowns
        $ownDamage = $vehiclePrice * ($odRate / 100) * 0.60;
        $theft = $vehiclePrice * ($odRate / 100) * 0.40;
        $aog = $vehiclePrice * ($aogRate / 100);

        // Summation Totals
        $totalSumInsured = (float)$vehiclePrice + $biLimitClean + $pdLimitClean + 50000;
        $basePremium = $ownDamage + $theft + $aog + $biPremium + $pdPremium + $paPremium;
        $vat = $basePremium * 0.12;
        $netPremium = $basePremium + $vat;
        
        // Document Stamp Rounding Rule matching reference sheet criteria
        $docStamps = ceil(($basePremium / 4) + 0.49) * 0.5;
        $municipalTax = $basePremium * 0.0011;
        $amountDue = $netPremium + $docStamps + $municipalTax;
    @endphp

    <div class="row">
        {{-- LEFT COLUMN: Vehicle Details and Account Specs --}}
        <div class="col-lg-7">
            
            {{-- Account Information Card --}}
            <div class="card card-custom shadow mb-4">
                <div class="card-header border-bottom border-secondary">
                    <h3 class="card-title text-info font-weight-bold">
                        <i class="fas fa-file-invoice mr-2"></i>Primary Policy Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold text-muted-custom">Assured Name:</div>
                        <div class="col-sm-8 text-white font-weight-bold" style="font-size: 1.1rem;">{{ $policy->assured }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold text-muted-custom">Address Path:</div>
                        <div class="col-sm-8 text-white-50">{{ $policy->address }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 font-weight-bold text-muted-custom">Statement of Account:</div>
                        <div class="col-sm-8"><span class="badge badge-info px-2 py-1">SOA-{{ $policy->soa_no }}</span></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-sm-4 font-weight-bold text-muted-custom">Mortgagee Bank:</div>
                        <div class="col-sm-8 text-warning font-weight-bold">{{ $policy->mortgagee ?: 'NONE (PRIVATE OWNED)' }}</div>
                    </div>
                </div>
            </div>

            {{-- Vehicle Specifications Card --}}
            <div class="card card-custom shadow mb-4">
                <div class="card-header border-bottom border-secondary">
                    <h3 class="card-title text-primary font-weight-bold">
                        <i class="fas fa-car mr-2"></i>Scheduled Vehicle Specifications
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped table-dark m-0">
                        <tbody>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold w-33">Make & Model</td>
                                <td class="text-white font-weight-bold">{{ $policy->brand }} {{ $policy->model }}</td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold">Body Type Classification</td>
                                <td>{{ $policy->type }}</td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold">Chassis / Serial No.</td>
                                <td class="text-monospace text-info font-weight-bold">{{ $policy->chassis_no }}</td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold">Engine / Motor No.</td>
                                <td class="text-monospace text-info font-weight-bold">{{ $policy->engine_no }}</td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold">Plate Number Label</td>
                                <td><span class="badge badge-warning px-2 font-weight-bold">{{ $policy->plate_no }}</span></td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold">Color Finishes</td>
                                <td>{{ $policy->color }}</td>
                            </tr>
                            <tr>
                                <td class="pl-3 text-muted-custom font-weight-bold text-bottom">MV File Assignment</td>
                                <td>{{ $policy->file_no }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Premium Accounting Breakdown Sheet --}}
        <div class="col-lg-5">
            <div class="card card-custom shadow">
                <div class="card-header border-bottom border-secondary bg-dark">
                    <h3 class="card-title text-success font-weight-bold">
                        <i class="fas fa-calculator mr-2"></i>Premium Computation Breakdown
                    </h3>
                </div>
                <div class="card-body p-0 bg-light text-dark">
                    <table class="table table-sm table-borderless premium-sheet-table mb-0 text-dark">
                        <tbody>
                            {{-- Header Metrics --}}
                            <tr class="bg-secondary text-white font-weight-bold">
                                <td>Total Sum Insured</td>
                                <td class="text-right">₱ {{ number_format($totalSumInsured, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">Estimated Vehicle Value</td>
                                <td class="text-right text-muted font-weight-bold">₱ {{ number_format($vehiclePrice, 2) }}</td>
                            </tr>
                            <tr class="border-bottom"><td colspan="2"></td></tr>

                            {{-- Section Risks --}}
                            <tr>
                                <td>Own Damage</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($ownDamage, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Theft Risk Exposure</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($theft, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Act of God Hazard coverage ({{ number_format($aogRate, 2) }}%)</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($aog, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Section IV-A: Bodily Injury (BI Limit: {{ number_format($biLimitClean) }})</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($biPremium, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Section IV-B: Property Damage (PD Limit: {{ number_format($pdLimitClean) }})</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($pdPremium, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td>Personal Accident</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($paPremium, 2) }}</td>
                            </tr>

                            {{-- Operational Totals --}}
                            <tr class="font-weight-bold text-dark">
                                <td>Gross Base Premium Subtotal</td>
                                <td class="text-right">₱ {{ number_format($basePremium, 2) }}</td>
                            </tr>
                            <tr class="text-muted">
                                <td>Value Added Tax (V.A.T. 12%)</td>
                                <td class="text-right">₱ {{ number_format($vat, 2) }}</td>
                            </tr>
                            <tr class="border-top text-primary font-weight-bold">
                                <td>Net Premium Value</td>
                                <td class="text-right">₱ {{ number_format($netPremium, 2) }}</td>
                            </tr>
                            
                            {{-- Regional Tax Attachments --}}
                            <tr>
                                <td>Documentary Stamps Tax Matrix</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($docStamps, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td>Municipal Local Government Tax (0.11%)</td>
                                <td class="text-right font-weight-bold">₱ {{ number_format($municipalTax, 2) }}</td>
                            </tr>

                            {{-- Final Amount Due Output --}}
                            <tr class="amount-due-box text-white">
                                <td class="p-3 align-middle text-uppercase tracking-wider font-weight-bold" style="font-size: 1.1rem;">
                                    <span class="text-success"><i class="fas fa-coins mr-2"></i>Amount Due</span>
                                </td>
                                <td class="p-3 text-right align-middle">
                                    <h3 class="text-success font-weight-bold mb-0" style="font-size: 1.85rem;">
                                        ₱ {{ number_format($amountDue, 2) }}
                                    </h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

<script>
function printPremiumSheet(url) {
    // Generates a background hidden printing sandbox frame instance
    let printWindow = window.open(url, '_blank', 'width=900,height=1000');
    printWindow.focus();
}
</script>
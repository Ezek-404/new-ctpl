<div class="policy-container">
    <div class="excel-margin-wrapper">
        <div class="policy-print-paper">
            <div class="p_field p_policy-no">{{ $issuance->policy_no }}</div>
            <div class="p_field p_assured">{{ $issuance->vehicle->assured }}</div>
            <div class="p_field p_address">{{ $issuance->vehicle->address }}</div>
            <div class="p_field p_date-issued">{{ $issuance->created_at->format('M-d-y') }}</div>
            <div class="p_field p_date-from">{{ $issuance->created_at->format('M-d-y') }}</div>
            <div class="p_field p_date-to">{{ $issuance->created_at->copy()->addYear()->format('M-d-y') }}</div>

            <div class="p_field p_year">{{ $issuance->vehicle->year_model }}</div>
            <div class="p_field p_make">{{ $issuance->vehicle->make }}</div>
            <div class="p_field p_type">{{ $issuance->vehicle->denomination }}</div>
            <div class="p_field p_color"><div>{{ $issuance->vehicle->color }}</div></div>
            <div class="p_field p_file">{{ preg_replace('/^(\d{6})0+(\d+)/', '$1-$2', $issuance->vehicle->file_no) }}</div>

            <div class="p_field p_plate">{{ $issuance->vehicle->plate_no }}</div>
            <div class="p_field p_chassis">{{ $issuance->vehicle->chassis_no }}</div>
            <div class="p_field p_engine">{{ $issuance->vehicle->engine_no }}</div>
        </div>
    </div>
</div>
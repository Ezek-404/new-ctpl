{{-- resources/views/admin/ctpl/print/pc/policy.blade.php --}}
<div class="print-paper letter-size">
    <div class="text-center">
        <h3>CERTIFICATE OF POLICY</h3>
        <p>Motorcycle (MC)</p>
    </div>
    <hr>
    <table class="table table-borderless">
        <tr>
            <td><strong>Policy No:</strong> {{ $issuance->policy_no }}</td>
            <td class="text-right"><strong>COC No:</strong> {{ $issuance->coc->coc_no }}</td>
        </tr>
        <tr>
            <td><strong>Assured:</strong> {{ $issuance->vehicle->assured }}</td>
            <td class="text-right"><strong>Plate:</strong> {{ $issuance->vehicle->plate_no }}</td>
        </tr>
    </table>
    {{-- Add more policy-specific text here --}}
</div>
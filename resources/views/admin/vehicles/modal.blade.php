<div class="modal fade" id="editVehicleModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 4px; border: none;">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editVehicleModalLabel" style="font-weight: 700; color: #333; text-transform: uppercase; font-size: 0.9rem;">
                    <i class="fas fa-edit mr-2 text-warning"></i> Edit Vehicle Record
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.vehicles.update', $vehicle->vehicle_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="v-label">Plate No.</label>
                            <input type="text" name="plate_no" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->plate_no }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="v-label">MV File Number</label>
                            <input type="text" name="file_no" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->file_no }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="v-label">Chassis Number</label>
                            <input type="text" name="chassis_no" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->chassis_no }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="v-label">Engine Number</label>
                            <input type="text" name="engine_no" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->engine_no }}">
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-md-12 mb-3">
                            <label class="v-label">Assured Name</label>
                            <input type="text" name="assured" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->assured }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="v-label">Make</label>
                            <input type="text" name="make" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->make }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="v-label">Year Model</label>
                            <input type="text" name="year_model" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->year_model }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="v-label">Color</label>
                            <input type="text" name="color" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->color }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="v-label">Denomination</label>
                            <input type="text" name="denomination" class="form-control form-control-sm font-weight-bold" value="{{ $vehicle->denomination }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-custom-size btn-close-theme shadow-sm" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-custom-size btn-edit-theme shadow-sm">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
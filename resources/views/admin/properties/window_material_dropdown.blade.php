<div class="modal fade" id="windowmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Select window material type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select name="window_type" class="form-control"  id="window_material_append" onchange="get_material_values_window(this)">
                    <option value="G" data-percentage="{{ $window_material->good_value }}">Good {{ $window_material->good_value }}</option>
                    <option value="A" data-percentage="{{ $window_material->avg_value }}">Avarage {{ $window_material->avg_value }}</option>
                    <option value="B" data-percentage="{{ $window_material->bad_value }}">Bad {{ $window_material->bad_value }}</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
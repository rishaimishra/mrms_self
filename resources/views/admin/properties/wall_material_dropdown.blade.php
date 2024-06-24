<!-- wall material Modal Structure -->
<div class="modal fade" id="wallmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Select wall material type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select name="wall_type" class="form-control" id="wall_material_append" onchange="get_material_values(this)">
                    <option value="">Please Select any option</option>
                    <option value="G" data-percentage="{{ $wall_material->good_value }}">Good {{ $wall_material->good_value }}</option>
                    <option value="A" data-percentage="{{ $wall_material->avg_value }}">Avarage {{ $wall_material->avg_value }}</option>
                    <option value="B" data-percentage="{{ $wall_material->bad_value }}">Bad {{ $wall_material->bad_value }}</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {{--  <button type="button" class="btn btn-primary">Save changes</button>  --}}
            </div>
        </div>
    </div>
</div>

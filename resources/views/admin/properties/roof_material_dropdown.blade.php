<div class="modal fade" id="roofmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Select roof material type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select name="roof_type" class="form-control" id="roof_material_append" onchange="get_material_values_roof(this)">
                    <option value="G" data-percentage="{{ $roof_material->good_value }}">Good {{ $roof_material->good_value }}</option>
                    <option value="A" data-percentage="{{ $roof_material->avg_value }}">Avarage {{ $roof_material->avg_value }}</option>
                    <option value="B" data-percentage="{{ $roof_material->bad_value }}">Bad {{ $roof_material->bad_value }}</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {{--  <button type="button" class="btn btn-primary">Save changes</button>  --}}
            </div>
        </div>
    </div>
</div>

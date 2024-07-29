<div class="modal fade" id="categorymyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Select window material type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select name="window_type" class="form-control"  id="property_category_append" onchange="get_property_category(this)">
                    <option value="G" data-percentage="{{ $property_category->good_value }}">Good {{ $property_category->good_value }}</option>
                    <option value="A" data-percentage="{{ $property_category->avg_value }}">Avarage {{ $property_category->avg_value }}</option>
                    <option value="B" data-percentage="{{ $property_category->bad_value }}">Bad {{ $property_category->bad_value }}</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
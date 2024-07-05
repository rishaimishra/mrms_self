<div class="modal fade" id="valueaddedmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Select value added type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select name="window_type" class="form-control"  id="window_material_append" onchange="get_value_addedd_multiple(this)">
                    @if ($value_added)
                    <option value="G" data-percentage="{{ $value_added->good_value }}" data-vid="{{ $value_added->id }}">Good {{ $value_added->good_value }}</option>
                    <option value="A" data-percentage="{{ $value_added->avg_value }}" data-vid="{{ $value_added->id }}">Avarage {{ $value_added->avg_value }}</option>
                    <option value="B" data-percentage="{{ $value_added->bad_value }}" data-vid="{{ $value_added->id }}">Bad {{ $value_added->bad_value }}</option>
                    @endif
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
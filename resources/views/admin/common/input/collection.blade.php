<div class="form-group">
    <div class="add-more-field">
        <h5 for="">Other Collection Point{!! isset($isRequired)?'<view class="red">*</view>':'' !!}</h5>
        @if(isset($key) && $key != 0)
            <span class="pull-right remove-more btn btn-lg btn-primary pull-right">-</span>
        @else
            <span class="pull-right add-more btn btn-lg btn-primary pull-right">+</span>
            <span class="pull-right remove-more btn btn-lg btn-primary pull-right" style="display: none;">-</span>
        @endif
    </div>
    @if(isset($key) && $key > 0)
        <div class="form-line" style="width:90%">
            <input name="collection_point[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point">
        </div>
    @else
        <div class="form-line" style="width:90%">
            <input name="collection_point[]" type="text" value="{{ !isset($collection_point) ? '' : $collection_point }}" class="form-control" placeholder="Other Collection Point">
        </div>
    @endif
</div>

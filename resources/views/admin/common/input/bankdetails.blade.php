<div class="col-sm-3">
    <div class="form-group form-float">
        <div class="form-line">
            <input type="text" class="form-control" value="{{ !isset($name) ? '' : $name }}" name="bank_details[{{$key}}][name]" >
            <label class="form-label">Bank Name</label>
            @if ($errors->has('bank_details.$key.name'))
                <label class="error">{{ $errors->first('bank_details.$key.name') }}</label>
            @endif
        </div>
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group form-float">
        <div class="form-line">
            <input type="text" class="form-control" value="{{ !isset($account_name) ? '' : $account_name }}" name="bank_details[{{$key}}][account_name]" >
            <label class="form-label">Account Name</label>
            @if ($errors->has('bank_details.$key.account_name'))
                <label class="error">{{ $errors->first('bank_details.$key.account_name') }}</label>
            @endif
        </div>
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group form-float">
        <div class="form-line">
            <input type="text" class="form-control" value="{{ !isset($account_number) ? '' : $account_number }}" name="bank_details[{{$key}}][account_number]" >
            <label class="form-label">Account Number</label>
            @if ($errors->has('bank_details.$key.account_number'))
                <label class="error">{{ $errors->first('bank_details.$key.account_number') }}</label>
            @endif
        </div>
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group form-float">
        <div class="form-line">
            <input type="text" class="form-control" value="{{ !isset($location) ? '' : $location }}" name="bank_details[{{$key}}][location]" >
            <label class="form-label">Location</label>
            @if ($errors->has('bank_details.$key.location'))
                <label class="error">{{ $errors->first('bank_details.$key.location') }}</label>
            @endif
        </div>
    </div>
</div>

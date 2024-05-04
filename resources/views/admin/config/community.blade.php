@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>SYSTEM SETTING</h2>
    </div>
@endsection

@section('form')

    {!! Form::open() !!}

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="body">
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">Gated Community</label>
                            {!! Form::text('gated_community', old(\App\Logic\SystemConfig::OPTION_GATED_COMMUNITY, $optionGroup->{\App\Logic\SystemConfig::OPTION_GATED_COMMUNITY}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('gated_community', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">CURRENCY RATE (1 USD IN Le.)</label>
                            {!! Form::text('currency_rate', old(\App\Logic\SystemConfig::CURRENCY_RATE, $optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('currency_rate', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="gated_community">SIGMA PAY URL</label>
                            {!! Form::text('sigma_pay_url', old(\App\Logic\SystemConfig::SIGMA_PAY_URL, $optionGroup->{\App\Logic\SystemConfig::SIGMA_PAY_URL}), ['class' => 'form-control input'] ) !!}
                            {!! $errors->first('sigma_pay_url', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection

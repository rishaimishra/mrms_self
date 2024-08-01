@extends('admin.layout.main')

@section('content')

    @include('admin.layout.partial.alert')

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Reverse Payments</h2>

                </div>
                <div class="body">
                    {!! Form::open(['id' => 'forgot_password', 'route' => ['admin.payment.update', $payment->id]]) !!}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="amount">Assessment Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            {{--  <input type="text" id="assessment"
                                                   value="{{ old('assessment') ? old('assessment') : number_format($payment->assessment,0,'',',') }}"
                                                   name="assessment" class="form-control"
                                                   placeholder="Enter assessment Amount"
                                                   @if($payment->reverse == 0)
                                                   disabled
                                                   @endif
                                                   onkeyup = "javascript:this.value=Comma(this.value);"
                                            >  --}}
                                            <input type="text" id="assessment"
                                                    value="{{ old('assessment') ? old('assessment') : number_format($payment->assessment, 0, '', ',') }}"
                                                    name="assessment" class="form-control"
                                                    placeholder="Enter assessment Amount"
                                                    @if($payment->reverse == 1) disabled @endif
                                                    onkeyup="javascript:this.value=Comma(this.value);">
                                        </div>
                                        {!! $errors->first('assessment', '<span class="error">:message</span>') !!}
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <label for="amount">Paying Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="amount"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->amount,0,'',',') }}"
                                                   name="amount" class="form-control"
                                                   placeholder="Enter Amount Paying"
                                                   @if($payment->reverse == 1) disabled @endif
                                                   onkeyup = "javascript:this.value=Comma(this.value);"
                                            >
                                        </div>
                                        {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                    </div>

                                </div>

                                <div class="col-md-3">
                                    <label for="amount">Penalty</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="penalty"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->penalty,0,'',',') }}"
                                                   name="penalty" class="form-control"
                                                   @if($payment->reverse == 1) disabled @endif
                                                   placeholder="Enter Amount Paying"  onkeyup = "javascript:this.value=Comma(this.value);">
                                        </div>
                                        {!! $errors->first('penalty', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="amount">Total Amount</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="total"
                                                   value="{{ old('amount') ? old('amount') : number_format($payment->total,0,'',',') }}" disabled
                                                   class="form-control" style="background-color: #eee;padding-left: 5px;" placeholder="">
                                        </div>
                                        {!! $errors->first('amount', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">


                                <div class="col-md-3">
                                    <label for="payment_type">Payment Type</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            {!! Form::select('payment_type', ['' => 'Select', 'cash' => 'Cash', 'cheque'=> 'Cheque', 'online'=> 'Online'], old('payment_type', $payment->payment_type), ['class' => 'form-control']) !!}
                                        </div>
                                        {!! $errors->first('payment_type', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label for="email_address">Cheque No</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="cheque_number" value="{{ old('cheque_number', $payment->cheque_number) }}"
                                                   name="cheque_number" class="form-control"
                                                   @if($payment->reverse == 1) disabled @endif
                                                   placeholder="Cheque Number">
                                        </div>
                                        {!! $errors->first('cheque_number', '<span class="error">:message</span>') !!}
                                    </div>
                                </div>



                        <div class="col-sm-3">
                            <label for="email_address">Payee Name</label>
                            <div class="form-group">
                                <div class="form-line" id="payee_name">
                                    <input type="text" id="payee_name"
                                    @if($payment->reverse == 1) disabled @endif
                                     value="{{ old('payee_name', $payment->payee_name) }}"
                                           name="payee_name" class="form-control"
                                           placeholder="Payee Name">
                                </div>
                                {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">

                            <div class="form-group">
                                <div class="form-line" id="created_at">
                                    <label>Created At</label>
                                    {!! Form::text('created_at', \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'), ['class' => 'form-control datepicker']) !!}

                                </div>
                                {!! $errors->first('created_at', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="email_address">Physical Receipt Image</label>
                            <div class="form-group">
                                <div class="form-line" id="payee_name">
                                <img  src="{{ $payment->physical_receipt_image_path }}" alt="" width="200" height="100">
                                    <!-- <input type="text" id="payee_name" value="{{ old('payee_name', $payment->physical_receipt_image_path) }}"
                                           name="payee_name" class="form-control"
                                           placeholder="Payee Name"> -->
                                

                                </div>
                                {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="email_address">Disability Discount Image</label>
                            <div class="form-group">
                                <div class="form-line" id="payee_name">
                                {{--  @if(($property->assessment->disability_discount) )
                                    <img  src="{{ $disability_image_path->disability_discount_image_path  }}" alt="" width="200" height="100">
                                @endif  --}}

                                @if(!is_null($disability_image_path) && is_object($disability_image_path) && isset($disability_image_path->disability_discount_image_path))
                                    <img src="{{ $disability_image_path->disability_discount_image_path }}" alt="" width="200" height="100">
                                @else
                                    <p>Disability discount image not available</p>
                                @endif


                                    <!-- <input type="text" id="payee_name" value="{{ old('payee_name', $payment->disability_discount_image_path) }}"
                                           name="payee_name" class="form-control"
                                           placeholder="Payee Name"> -->

                                           <div class="col-sm-3">
                                    <!-- <div class="form-group">
                                        <label>Approval </label>
                                        <div class="demo-radio-button" style="display: inline-block;">
                                            <input name="disability_discount_approve"
                                                class="input-inaccessible"
                                                type="radio" id="disability_radio_1" value="1"/>
                                            <label for="disability_radio_1" style="min-width: auto">Yes</label>
                                            <input name="disability_discount_approve"
                                                class="input-inaccessible"
                                                type="radio" id="disability_radio_2" value="0"/>
                                            <label for="disability_radio_2" style="min-width: auto">No</label>
                                        </div>
                                    </div> -->
                                </div>


                                </div>
                                {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="email_address">Pensioner Discount Image</label>
                            <div class="form-group">
                                <div class="form-line" id="payee_name">
                                {{--  @if(($property->assessment->pensioner_discount) )
                                    <img  src="{{ $pensioner_image_path->pensioner_discount_image_path }}" alt="" width="200" height="100">
                                @endif  --}}


                                @if(!is_null($pensioner_image_path) && is_object($pensioner_image_path) && isset($pensioner_image_path->pensioner_discount_image_path))
                                    <img src="{{ $pensioner_image_path->pensioner_discount_image_path }}" alt="" width="200" height="100">
                                @else
                                    <p>Pensioner discount image not available</p>
                                @endif
                                
                                    <!-- <input type="text" id="payee_name" value="{{ old('payee_name', $payment->pensioner_discount_image_path) }}"
                                           name="payee_name" class="form-control"
                                           placeholder="Payee Name"> -->

                                           <div class="col-sm-3">
                                    <!-- <div class="form-group">
                                        <label>Approval </label>
                                        <div class="demo-radio-button row" style="display: inline-block;">
                                            <input name="pensioner_discount_approve"
                                                class="input-inaccessible"
                                                type="radio" id="radio_1" value="1"/>
                                            <label for="radio_1" style="min-width: auto">Yes</label>
                                            <input name="pensioner_discount_approve"
                                                class="input-inaccessible"
                                                type="radio" id="radio_2" value="0"/>
                                            <label for="radio_2" style="min-width: auto">No</label>
                                        </div>
                                    </div> -->
                                </div>
                                </div>
                                {!! $errors->first('payee_name', '<span class="error">:message</span>') !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-sm-6 text-left">
                        </div>
                        <div class="col-sm-6 text-right">
                            @if($payment->reverse == 0)
                            <a href="{{route('admin.reverse', $payment->id)}}" class="btn btn-primary m-t-15 waves-effect btn-lg">Reverse Payment</a>
                            @endif
                            <!-- <button type="submit" class="btn btn-primary m-t-15 waves-effect btn-lg">Save</button> -->
                        </div>
                    </div>


                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>

    <script>
        jQuery("#amount, #penalty").on('keyup', function () {
            var amount = jQuery("#amount").val();
            var penalty = jQuery("#penalty").val();

            amount = amount.replace(/,/g, '');
            penalty = penalty.replace(/,/g, '');

            if(amount=='')
            {
                amount = 0;
            }
            if(penalty=='')
            {
                penalty = 0;
            }
            var total = parseInt(amount) + parseInt(penalty);

            jQuery("#total").val(Comma(total));

        });


        function Comma(Num) { //function to add commas to textboxes
            Num += '';
            Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
            Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
            x = Num.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1))
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            return x1 + x2;
        }

    </script>
@endpush

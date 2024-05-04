@extends('admin.layout.edit')

@section('content')

    <div class="block-header">
        <h2>Send Notification</h2>
    </div>


        {!! Form::open(['method' => 'post', 'route' => 'admin.notification.store','id'=>'sms']) !!}


    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="header">

                </div>
                <div class="body">


                        <label for="email_address">SMS Text</label>
                        <div class="form-group">
                            <div class="form-line" id="sms_text">
                                <input type="text" id="sms_text" value="{{ old('sms_text') }}"
                                       name="sms_text" class="form-control"
                                       placeholder="SMS Text">
                            </div>
                            <p>(Maximan 50 characters.)</p>
                            {!! $errors->first('sms_text', '<span class="error">:message</span>') !!}
                        </div>


                </div>
            </div>

        </div>
        <div class="col-sm-4">
            <div class="card">
                <div class="header">

                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label">Recipient</label>
                            {!! Form::select('type', $type, '', ['class' => 'form-control', 'id' => 'type','required'=>'required']) !!}
                            {!! $errors->first('type', '<p class="error">:message</p>') !!}
                        </div>

                    </div>
                    <button id="send" type="button" class="btn btn-primary btn-lg waves-effect">SEND</button>

                </div>
            </div>



        </div>
    </div>
    {!! Form::close() !!}

@endsection

@push('scripts')



    <script type="text/javascript">

        $(document).ready(function () {

            // $("#textBody").bind("keyup change",function () {
            //     var len =  $("#textBody").val().length;
            //     if (len >= 160) {
            //         $('#charNum').text("(Maximum " + (160 - len) + " characters)");
            //     } else {
            //         $('#charNum').text("(Maximum " + (160 - len) + " characters)");
            //     }
            // });

            $("#send").on('click', function () {

                var _self = $(this);

                swal({
                    title: "Are you sure!",
                    text: "You are about to send text sms",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Send it",
                    closeOnConfirm: false
                }, function () {
                    $('form#sms').submit();
                });
            });

        });

    </script>
@endpush

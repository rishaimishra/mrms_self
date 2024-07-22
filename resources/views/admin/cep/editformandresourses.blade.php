@extends('admin.layout.main')
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/leantony/grid/css/grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/properties_grid.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/dhtmlxcombo.css') }}" />
@endpush


@section('content')
    <style type="text/css">
        #search-property-grid {
            display: none;
        }

        div.laravel-grid {
            margin-top: 10px !important;
        }

        .p_2 {
            padding: 20px;
        }

        .img_pre {
            width: 100px;
            height: 100px;
            border: 1px dashed gray;
            border-radius: 10px;
            margin-top: 10px;
            text-align: center;
        }

        .border_cus {
            border: 1px solid rgb(210, 210, 210);
            padding: 10px;
        }

        #imgPreview img {
            height: 95px;
            width: 95px;
            border-radius: 10px;
        }
        .ecp_submit{
            background: #0070c0 !important;
            border-radius: 5px !important;
            font-size: 16px !important;
            color: white !important;
            font-weight: bold !important;
            padding: 7px 40px !important;
            margin-right: 20px;
        }
        .ecp_publish{
            background: #15e12d !important;
            border-radius: 5px !important;
            font-size: 16px !important;
            color: white !important;
            font-weight: bold !important;
            padding: 7px 40px !important;
        }
    </style>
    <div class="card p_2" style="height: 100%;">
        <form action="{{ route('admin.update-form-resources') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="form_id" value="{{ $form_resourse->id }}">
            <div class="border_cus">
                <div class="row">
                    <div class="col-md-2"><label for="">FORM NAME</label></div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" value="{{ $form_resourse->form_name }}" name="form_name" placeholder="enter form name here .......">
                        {!! $errors->first('form_name', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">FORM UPLOAD</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="img_pre" id="imgPreview">
                            @if ($form_resourse->form_image)
                                <img style="width: 100px;height:100px;" src="{{ asset('storage/'.$form_resourse->form_image) }}" alt="">
                            @endif
                        </div>
                        <input type="file" name="form_img" id="fileInput" accept="image/*" style="display: none;">
                        <label for="fileInput" id="label" style="margin-top: 20px;color:#2196F3;cursor: pointer;">Upload
                            from here</label>
                            {!! $errors->first('form_img', '<span class="error">:message</span>') !!}
                    </div>

                </div>
            </div>
            <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px;">
                <button type="submit" class="btn btn-primary ecp_submit">SUBMIT</button>
                <button type="button" class="btn btn-success ecp_publish">Publish</button>
            </div>
        </form>
        
@stop
<script>
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

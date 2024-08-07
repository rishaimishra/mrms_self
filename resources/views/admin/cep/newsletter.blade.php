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
        #imgPreview2 img {
            height: 95px;
            width: 95px;
            border-radius: 10px;
        }
        #imgPreview3 img {
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
    <div class="card p_2">
        <form action="{{ route('admin.head-line-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border_cus">
                <div class="row">
                    <div class="col-md-2"><label for="">HEADLINE</label></div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="headline" placeholder="enter headline here .......">
                        {!! $errors->first('headline', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row"style="margin-top:10px;">
                    <div class="col-md-2"><label for="">Editor name</label></div>
                    <div class="col-md-10">
                        {{--  <input type="text" class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <input type="text" class="form-control" name="editor_name" placeholder="enter editor name here .......">
                        {!! $errors->first('editor_name', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">HEADLINE PHOTO</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="img_pre" id="imgPreview">

                        </div>
                        <input type="file" name="headline_image" id="fileInput" style="display: none;">
                        <label for="fileInput" id="label" style="margin-top: 20px;color:#2196F3;cursor: pointer;">Upload
                            Cover Photo</label>
                            {!! $errors->first('headline_image', '<span class="error">:message</span>') !!}
                    </div>

                </div>
            </div>
            <div class="border_cus" style="margin-top: 20px;">
                <div class="row">
                    <div class="col-md-2"><label for="">STORYBOARD</label></div>
                    <div class="col-md-10">
                        {{--  <input type="text" class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <textarea name="story_board" id="editor"></textarea>
                        {!! $errors->first('story_board', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">ADDITIONAL PHOTO(S)</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2">
                        <div class="img_pre" id="imgPreview2">

                        </div>
                        <input type="file" name="headlineimg[]" id="fileInput2" style="display: none;">
                        <label for="fileInput2" id="label" style="margin-top: 20px;color:#2196F3;cursor: pointer;">Additional Photo</label>
                        {!! $errors->first('headlineimg', '<span class="error">:message</span>') !!}
                    </div>
                    <div class="col-md-2">
                        <div class="img_pre" id="imgPreview3">

                        </div>
                        <input type="file" name="headlineimg[]" id="fileInput3" style="display: none;">
                        <label for="fileInput3" id="label" style="margin-top: 20px;color:#2196F3;cursor: pointer;">Additional Photo</label>
                        {!! $errors->first('headlineimg', '<span class="error">:message</span>') !!}
                    </div>

                </div>
            </div>
            <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px;">
                <button type="submit" class="btn btn-primary ecp_submit">SUBMIT</button>
                <button type="button" class="btn btn-success ecp_publish">Publish</button>
            </div>
        </form>
    </div>
    <div>
        {!! $grid !!}
    </div>
@stop
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize CKEditor after the DOM is fully loaded
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
    });
</script>
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
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput2').addEventListener('change', function() {

            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview2');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });


    });
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput3').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview3');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });


    });
</script>

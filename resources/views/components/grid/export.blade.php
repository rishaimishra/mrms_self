<div class="rs-select2--dark rs-select2--sm rs-select2--dark2">
    <select class="js-select2" name="type" id="export_type">
        <option value="" selected="selected">{{ __("Export") }}</option>
        <option value="{{ $excelExportLink }}">{{ __("Excel") }}</option>
        <option value="{{ $csvExportLink }}">{{ __("CSV") }}</option>
{{--        <option value="{{ $pdfExportLink }}">PDF</option>--}}
    </select>
    <div class="dropDownSelect2"></div>
</div>

@push('before_body_close')
    <script>
        $(document).ready(function () {
            $("#export_type").on('change', function () {

                if ($(this).val() !== '') {
                    window.location = $(this).val();
                }
            });
        });
    </script>
@endpush

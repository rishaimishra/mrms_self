{!! Form::text("grid[filter][{$column['field']}]", request('grid.filter.' . $column['field']), ['class' => 'form-control form-control-sm', 'id' => 'grid-date-range-' . $column['field'], 'placeholder' => "Select Date Range", 'autocomplete' => 'off', 'readonly' => 'readonly']) !!}

@push('before_body_close')
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery("#{{'grid-date-range-' . $column['field']}}").daterangepicker({
                "opens": "left",
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            jQuery("#{{'grid-date-range-' . $column['field']}}").on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            jQuery("#{{'grid-date-range-' . $column['field']}}").on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush

<div class="card">
    <div class="body">
        @if(count($items) > 0 || request()->has('grid.filter'))

            {!! Form::open(['method' => 'get', 'url' => request()->url()]) !!}

            @if(isset($sort['field']) && isset($sort['order']))
                <input type="hidden" name="grid[sort][field]" value="{{ $sort['field'] }}"/>
                <input type="hidden" name="grid[sort][order]" value="{{ $sort['order'] }}"/>
            @endif

            <div class="table-data__tool">
                <div class="table-responsive table-responsive-data2">
                    <table class="table table-data2">
                        <thead>
                        <tr>
                            <th>#</th>
                            @foreach($columns as $column)
                                <th>{!! $column['label'] !!}</th>
                            @endforeach

                            @if(count($buttons) > 0)
                                <th>&nbsp;</th>
                            @endif
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            @foreach($columns as $column)
                                @if($column['filterable'] ?? false)
                                    <td>
                                        @if($column['filterable']['options'] ?? false)
                                            @include('components.grid.select', ['column' => $column])
                                        @elseif(isset($column['filterable']['type']) && $column['filterable']['type'] === 'date-range')
                                            @include('components.grid.date-range', ['column' => $column])
                                        @else
                                            @include('components.grid.input', ['column' => $column])
                                        @endif
                                    </td>
                                @else
                                    <td>&nbsp;</td>
                                @endif
                            @endforeach

                            <td align="right">&nbsp;
                                <button name="filter" class="btn btn-primary">{{ __("Filter") }}</button>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr class="tr-shadow">

                                <td valign="center">{{ ++$pageStart }}</td>

                                @foreach($columns as $column)
                                    <td>{!! $item[$column['field']] !!}</td>
                                @endforeach

                                @if(count($buttons) > 0)
                                    <td>
                                        <div class="table-data-feature">
                                            @foreach($item['_buttons'] as $button)
                                                {!! $button !!}
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            <tr class="spacer"></tr>
                        @empty
                            <tr>
                                <td colspan="50">
                                    <div class="alert alert-info">{{ __('No search result found.') }}</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </form>

            <div class="row">
                <div class="col-xs-5">
                    <span style="display: inline-block; font-size: 12pt; padding: 10px" class="label label-primary">Result {{ $data->firstItem() }} - {{ $data->lastItem() }} of {{ $data->total() }}</span>
                </div>

                <div class="col-xs-7 text-right">
                    {!! $links !!}
                </div>
            </div>


        @else
            <div class="alert alert-info">{{ __('Nothing found here.') }}</div>
        @endif
    </div>
</div>

@push('before_head_close')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush

@push('before_body_close')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush

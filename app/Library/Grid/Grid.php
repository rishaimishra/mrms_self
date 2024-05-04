<?php

namespace App\Library\Grid;

use Carbon\Carbon;

class Grid
{
    protected $queryBuilder;
    protected $columns;
    protected $perPage = 10;
    protected $buttons = [];
    protected $exports = ['xlsx', 'csv', 'pdf'];

    protected $name = 'grid';

    const DATE_FORMAT = 'm/d/Y';

    protected $columnTypes = [ColumnType::BOOLEAN];

    protected $defaultSortColumn = 'created_at';

    public function setQuery($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setDefaultSortColumn($column)
    {
        $this->defaultSortColumn = $column;
        return $this;
    }

    public function setColumns(array $columns)
    {
        $this->columns = collect($columns);
        return $this;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
        return $this;
    }

    protected function getColumns()
    {
        $columns = $this->columns->map(function ($column) {

            $column['orig_label'] = $column['label'];

            if ($column['sortable'] ?? false) {
                $column['label'] = $this->applySortingAttributes($column['label'], $column['field']);
            }

            return $column;
        });

        return $columns;
    }

    public function runExport()
    {
        $format = request('export.format');

        $this->applySorting();
        $this->applyFilters();

        $data = $this->queryBuilder->get();
        $data = $this->applyFormatters($data, true);

        $columns = $this->getColumns();

        return \Excel::download(new GridExport($data, $columns), $this->name . '.' . $format);
    }

    public function renderButtons()
    {
        $gridFields = request()->only('grid');

        $excelExportLink = request()->url() . '?' . http_build_query([
                'grid' => $gridFields,
                'export' => ['format' => 'xlsx']
            ]);

        $csvExportLink = request()->url() . '?' . http_build_query([
                'grid' => $gridFields,
                'export' => ['format' => 'csv']
            ]);

        $pdfExportLink = request()->url() . '?' . http_build_query([
                'grid' => $gridFields,
                'export' => ['format' => 'pdf']
            ]);

        return view('components.grid.export', compact('excelExportLink', 'csvExportLink', 'pdfExportLink'));
    }

    public function hasExport()
    {
        return request()->has('export.format');
    }

    protected function applyFilters()
    {
        $filterable = $this->columns->filter(function ($item) {
            return isset($item['filterable']);
        });

        foreach ($filterable as $item) {

            $inputKey = 'grid.filter.' . $item['field'];
            $filledValue = request()->filled($inputKey);

            $value = request($inputKey);

            if (isset($item['filterable']['callback']) && is_callable($item['filterable']['callback'])) {

                if ($filledValue) {
                    $item['filterable']['callback']($this->queryBuilder, $value);
                }

                continue;
            }

            $type = $item['filterable']['type'] ?? 'like';

            if ($filledValue) {
                switch ($type) {
                    case 'eq':
                    {

                        break;
                    }
                    case 'date-range':
                    {

                        $dates = explode(' - ', $value);

                        if (count($dates) === 2) {

                            list($startDate, $endDate) = $dates;

                            try {
                                $this->queryBuilder->whereBetween($item['field'], [
                                    Carbon::createFromFormat(self::DATE_FORMAT, $startDate)->startOfDay(),
                                    Carbon::createFromFormat(self::DATE_FORMAT, $endDate)->endOfDay()
                                ]);
                            } catch (\Exception $exception) {

                            }
                        }
                        break;
                    }
                    case 'like':
                    {
                        $this->queryBuilder->where($item['field'], 'like', "%{$value}%");
                        break;
                    }
                }
            }
        }
    }

    protected function applySorting()
    {
        $field = request('grid.sort.field');
        $order = request('grid.sort.order');

        $sortables = $this->columns->where('sortable', true)->pluck('field')->toArray();

        // is valid field
        if (!$field || !$order || !in_array($order, ['asc', 'desc']) || !in_array($field, $sortables)) {
            $this->queryBuilder->latest($this->defaultSortColumn);
            return;
        }

        $this->queryBuilder->orderBy($field, $order);
    }

    protected function applySortingAttributes($label, $field)
    {
        $inputs = request()->except($field);

        if (request('grid.sort.field') == $field) {
            $inputs['grid']['sort']['order'] = request('grid.sort.order') === 'asc' ? 'desc' : 'asc';
        } else {
            $inputs['grid']['sort']['field'] = $field;
            $inputs['grid']['sort']['order'] = 'asc';
        }

        $params = http_build_query($inputs);
        return link_to(request()->url() . '?' . $params, $label);
    }

    protected function applyFormatters($data, $forExport = false)
    {
        $items = [];

        foreach ($data as $item) {

            $temp = [];

            foreach ($this->columns as $column) {

                if (isset($column['formatter']) && is_callable($column['formatter'])) {
                    $temp[$column['field']] = $column['formatter']($item->{$column['field']}, $item);

                } else if ($item->{$column['field']} instanceof Carbon) {
                    $temp[$column['field']] = $forExport ? $item->{$column['field']}->toDateTimeString() : $item->{$column['field']}->toDayDateTimeString();
                } else if (isset($column['formatter']) && in_array($column['formatter'], $this->columnTypes)) {
                    $temp[$column['field']] = ColumnType::boolean($item, $column['field']);
                }
            }

            if (!$forExport && $this->buttons) {
                foreach ($this->buttons as $button) {
                    $temp['_buttons'][] = view('components.grid.button', compact('item', 'button'));
                }
            }

            $temp = array_merge($item->toArray(), $temp);
            $items[] = $temp;
        }

        return $items;
    }

    public function generate()
    {
        $this->applySorting();
        $this->applyFilters();

        $data = $this->queryBuilder->paginate($this->perPage);

        $items = $this->applyFormatters($data);

        $columns = $this->getColumns();

        $perPage = $this->perPage;
        $pageStart = (request('page', 1) - 1) * $perPage;
        $buttons = $this->buttons;

        $links = $data->appends(['grid' => request('grid')])->links();

        return view('components.grid.table', \compact(
            'items',
            'columns',
            'pageStart',
            'buttons',
            'links',
            'data'
        ));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;


use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use ReflectionClass;


trait DataProvider
{
    protected function prepareModel(Request $request, EloquentBuilder|Builder $query, bool $isJoin = false)
    {
        if ($request->filter) {
            $this->filterModel($query, $isJoin, $request->filter);
        }

        if (is_string($request->sort) && $request->sort !== '') {
            $this->sortModel($query, $request->sort);
        }

        return $query;
    }

    protected function filterModel($query, bool $isJoin, array $filter)
    {
        $isQueryBuilder = $isJoin && $query instanceof Builder;

        if ($isQueryBuilder) {
            $columns = [];
            foreach ($query->columns as $column) {

                $parts = explode(' ', $column);
                if (count($parts) > 1) {
                    $columns[$parts[count($parts) - 1]] = $parts[0];
                } else  {
                    $parts = explode('.', $column,2);
                    $columns[$parts[1]] = $column;
                }
            }
        }


        foreach ($filter as $filterKey => $param) {
            $col = $isQueryBuilder ? $columns[$filterKey] : $filterKey;
            $this->addCondition($query, $col, $param);
        }
    }

    protected function sortModel($query, string $sort)
    {
        if ($sort[0] === '-') {
            $sort = substr($sort, 1);
            $query->orderByDesc($sort);
        } else {
            $query->orderBy($sort);
        }
    }

    protected function addCondition($query, string $column, string|array $param)
    {

        if (is_string($param)) {
            $query->where($column, $param);
        } else if(is_array($param)) {
            $query->where(function ($query) use ($column, $param) {
                foreach ($param as $key => $value) {
                    if (is_string($value)) {
                        if ($key === 'like') {
                            $query->orWhere($column, 'like', "%$value%");
                        } else {
                            $query->orWhere($column, $value);
                        }
                    }
                }
            });
        }
    }
}

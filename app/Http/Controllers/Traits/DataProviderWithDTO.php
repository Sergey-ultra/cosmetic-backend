<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

trait DataProviderWithDTO
{
    private EloquentBuilder|Builder $query;

    private array $columns;

    private bool $isJoinQuery = false;
    protected function prepareModel(ParamsDTO $params, EloquentBuilder|Builder $query): EloquentBuilder|Builder
    {
        $this->query = $query;
        $this->setColumns();

        if ($params->filter) {
            $this->filterQuery($params->filter);
        }

        if (is_string($params->sort) && $params->sort !== '') {
            $this->sortQuery($params->sort);
        }

        return $this->query;
    }

    private function setColumns(): void
    {
        if ($this->query instanceof EloquentBuilder) {
            $query = $this->query->getQuery();
        } else {
            $query = $this->query;
        }

        $this->columns = $query->columns;

        if (is_array($query->joins) && count($query->joins)) {
            $this->isJoinQuery = true;
        }
    }

    private function filterQuery(array $filter): void
    {
        if ($this->isJoinQuery) {
            $this->filterJoinedQuery($filter);
        } else {
            foreach ($filter as $filterKey => $param) {
                $this->addCondition($filterKey, $param);
            }
        }
    }

    private function filterJoinedQuery(array $filter): void
    {
        $availableColumns = [];
        foreach ($this->columns as $column) {
            if (is_string($column)) {
                $parts = explode(' ', $column);
                if (count($parts) > 1) {
                    $availableColumns[$parts[count($parts) - 1]] = $parts[0];
                } else {
                    $parts = explode('.', $column, 2);
                    $availableColumns[$parts[1]] = $column;
                }
            } else if ($column instanceof Expression) {
                $parts = explode(' ', $column->getValue());
                $availableColumns[$parts[count($parts) - 1]] = $parts[count($parts) - 1];
            }
        }

        foreach ($filter as $filterKey => $param) {
            $this->addCondition($availableColumns[$filterKey], $param);
        }
    }

    private function sortQuery(string $sort): void
    {
        if ($sort[0] === '-') {
            $sort = substr($sort, 1);
            $this->query->orderByDesc($sort);
        } else {
            $this->query->orderBy($sort);
        }
    }

    private function addCondition(string $column, string|array $param): void
    {
        if (is_string($param)) {
            $this->query->where($column, $param);
        } else if(is_array($param)) {
            $this->query->where(function ($query) use ($column, $param) {
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

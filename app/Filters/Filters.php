<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;

    protected $filters = [];

    /**
     * ThreadFilters constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $builder
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        // Using Collections
//        $this->getFilters()
//            ->filter(function ($filter) {
//                return method_exists($this, $filter);
//            })
//            ->each(function ($filter, $value) {
//                $this->$filter($value);
//            });
//
//        return $this->builder;
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return array_filter($this->request->only($this->filters));

        // If using collections and flipping
//        return collect($this->request->intersect($this->filters))->flip();
    }
}
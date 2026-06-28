<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HasTableFilters
{
    protected function getFilterParams(Request $request, array $allowedSorts = ['name', 'created_at']): array
    {
        return [
            'search'    => $request->string('search')->trim()->value(),
            'sort'      => in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'name',
            'direction' => in_array($request->get('direction'), ['asc', 'desc']) ? $request->get('direction') : 'asc',
            'status'    => in_array($request->get('status'), ['active', 'inactive', '']) ? $request->get('status') : '',
            'type'      => $request->get('type', ''),
            'per_page'  => in_array($request->get('per_page'), [15, 30, 50]) ? (int) $request->get('per_page') : 15,
        ];
    }
}
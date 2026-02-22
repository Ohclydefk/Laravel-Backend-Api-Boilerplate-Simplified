<?php

namespace App\Traits\BaseQuery;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

trait BaseQueryTrait
{
    protected function applyQueryFeatures(
        Builder $query,
        Request $request,
        array $searchable = [],
        array $sortable = []
    ): JsonResponse {

        /**
         * SEARCHING - whitelist protected
         * - Client can specify ?search=keyword to search across multiple columns
         * - Only columns in the $searchable array will be included in the search to prevent abuse
         * - If search is provided but $searchable is empty, the search will be ignored to prevent abuse
         */
        if ($request->filled('search') && !empty($searchable)) {
            $search = $request->search;

            $query->where(function ($q) use ($searchable, $search) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        /**
         * SORTING - whitelist protected
         * - Client can specify ?sort_by=column_name&sort_direction=asc|desc
         * - Only columns in the $sortable array can be sorted on to prevent abuse
         * - If sort_by is not in the $sortable array, it will be ignored and default sorting will be applied
         */
        $sortBy = $request->get('sort_by');
        $sortDirection = strtolower($request->get('sort_direction', 'asc'));

        if ($sortBy && in_array($sortBy, $sortable)) {
            $sortDirection = in_array($sortDirection, ['asc', 'desc'])
                ? $sortDirection
                : 'asc';

            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest(); // default fallback
        }

        /**
         * PAGINATION
         * - Default: 10 items per page
         * - Max: 100 items per page to prevent abuse
         * - Client can specify ?per_page=20 to change the number of items per page
         * - If not specified, the default is 10 items per page
         **/
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 100 ? 100 : $perPage;

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }
}

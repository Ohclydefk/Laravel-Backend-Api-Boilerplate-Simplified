<?php

namespace App\Traits\BaseQuery;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

trait BaseQueryTrait
{
    /**
     * APPLY QUERY FEATURES (Generalized List Endpoint Helper)
     * -----------------------------------------------------------------------------
     * Purpose:
     * - Centralize common "list endpoint" features like:
     *   - eager-loading relationships
     *   - filtering
     *   - searching
     *   - sorting
     *   - pagination
     *
     * Why this exists:
     * - Prevent repeating the same query logic in every repository/controller.
     * - Keep request-driven query controls consistent and abuse-safe.
     * - Only allow the client to query fields you explicitly whitelist.
     *
     * How to use:
     * - Your repository builds the "base" query (usually Model::query()).
     * - You pass whitelists for search/sort/filter/relations.
     * - You optionally pass a $customQuery callback for advanced logic.
     *
     * -----------------------------------------------------------------------------
     * Parameters (What each variable is for)
     * -----------------------------------------------------------------------------
     * @param Builder $query
     *   - The Eloquent query builder instance you're modifying.
     *   - Usually created via: Model::query()
     *   - This is what ultimately becomes your SQL query.
     *
     * @param Request $request
     *   - The HTTP request input (query params like search/sort/per_page/etc).
     *   - Example query params:
     *       ?search=foo
     *       ?sort_by=city&sort_direction=asc
     *       ?filters[user_id]=1
     *       ?per_page=25
     *       ?no_include=1
     *
     * @param array $searchable
     *   - Whitelist of DB columns that are allowed to be searched via ?search=
     *   - Example:
     *       ['city', 'province', 'street']
     *   - If empty: search is ignored (safe by default).
     *
     * @param array $sortable
     *   - Whitelist of DB columns allowed to be sorted via ?sort_by=
     *   - Example:
     *       ['city', 'created_at']
     *   - If sort_by is not in this list, we apply a safe default sort (latest()).
     *
     * @param array $allowedRelationships
     *   - Whitelist of relationship names that can be eager-loaded (with()).
     *   - ALSO acts as the default eager loads for the endpoint.
     *   - Example:
     *       ['user', 'company']
     *   - If not empty and no_include != 1, we always load them.
     *
     * @param array $filterable
     *   - Whitelist of DB columns that can be filtered via ?filters[field]=value
     *   - Example:
     *       ['user_id', 'city', 'is_default']
     *   - The filtering here is exact match (where field = value).
     *
     * @param callable|null $customQuery
     *   - Optional hook where you can apply ANY custom query logic.
     *   - This is the primary extension point for:
     *     - date ranges
     *     - relationship constraints (whereHas)
     *     - computed/complex filters
     *     - permissions/scoping (e.g. only current user's records)
     *
     *   Signature:
     *     function (Builder $query, Request $request) { ... }
     *
     * -----------------------------------------------------------------------------
     * Where to customize (Most important part)
     * -----------------------------------------------------------------------------
     * 1) In the Repository (recommended for most customization)
     *    - Add/remove allowed relationships:
     *        $allowedRelationships = ['user', ...]
     *    - Add/remove searchable/sortable/filterable columns
     *    - Add complex filters using $customQuery callback
     *
     * 2) In this Trait (framework-level customization)
     *    - Change global behavior for all repositories/endpoints:
     *      e.g. change max per_page, default sorting rules, add global filter formats
     *
     * -----------------------------------------------------------------------------
     * Where the customization happens (Order matters)
     * -----------------------------------------------------------------------------
     * The query is modified in this order:
     *   1) Relationships eager load (with())
     *   2) Simple exact filters (where())
     *   3) Custom query hook (customQuery callback)
     *   4) Search (where LIKE across searchable columns)
     *   5) Sorting (orderBy or latest fallback)
     *   6) Pagination (paginate)
     *
     * The earlier a condition is applied, the earlier it affects the result set.
     * For example:
     * - If your customQuery adds whereHas('user'), it limits results BEFORE search/sort.
     */
    protected function applyQueryFeatures(
        Builder $query,
        Request $request,
        array $searchable = [],
        array $sortable = [],
        array $allowedRelationships = [],   // whitelist relationships (ALSO default includes)
        array $filterable = [],
        ?callable $customQuery = null
    ): JsonResponse {

        /**
         * 1) RELATIONSHIPS (auto include by default)
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Avoid N+1 queries by eager-loading related models.
         * - Ensure consistent API output when the endpoint always expects relations.
         *
         * Behavior:
         * - If $allowedRelationships is not empty:
         *     -> we eager load ALL of them by default
         * - If client sends ?include=...:
         *     -> we merge in requested relations (still whitelisted)
         * - If client sends ?no_include=1:
         *     -> we skip eager loading (performance / smaller payload)
         *
         * How the whitelist protects you:
         * - Clients cannot request arbitrary relations like ?include=adminSecrets
         * - Only relations explicitly listed in $allowedRelationships are allowed.
         */
        if (!empty($allowedRelationships) && !$request->boolean('no_include', false)) {
            $includes = $allowedRelationships;

            // Optional includes from client (still only from allowed list)
            if ($request->filled('include')) {
                $requested = array_filter(array_map('trim', explode(',', (string) $request->input('include'))));
                $requested = array_values(array_intersect($requested, $allowedRelationships));
                $includes = array_values(array_unique(array_merge($includes, $requested)));
            }

            $query->with($includes);
        }

        /**
         * 2) SIMPLE FILTERS (exact match)
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Allow safe client filtering without exposing your DB to arbitrary conditions.
         *
         * Usage:
         * - Client can send:
         *     ?filters[user_id]=10&filters[city]=Davao
         *
         * Behavior:
         * - For each filters[field] => value
         * - If field is in $filterable, apply:
         *     where(field, value)
         *
         * Notes:
         * - This is EXACT match only.
         * - For ranges, partial matches, IN queries, date filtering, etc:
         *   use $customQuery.
         */
        $filters = $request->input('filters', []);
        if (is_array($filters) && !empty($filterable)) {
            foreach ($filters as $field => $value) {
                // whitelist check
                if (!in_array($field, $filterable, true)) {
                    continue;
                }

                // ignore empty values
                if ($value === null || $value === '') {
                    continue;
                }

                $query->where($field, $value);
            }
        }

        /**
         * 3) CUSTOM QUERY HOOK (advanced customization point)
         * -----------------------------------------------------------------------------
         * Purpose:
         * - This is where you implement everything that isn't covered by "simple filters".
         *
         * Examples of what belongs here:
         * - Date ranges:
         *     if ($request->filled('from')) $query->whereDate('created_at', '>=', ...)
         * - Relationship constraints:
         *     $query->whereHas('user', fn($q) => $q->where('status', 'active'))
         * - Security scoping:
         *     $query->where('user_id', auth()->id())
         * - Complex logic:
         *     if ($request->boolean('has_default')) ...
         *
         * Where it runs:
         * - After simple exact filters
         * - Before search and sorting
         */
        if (is_callable($customQuery)) {
            $customQuery($query, $request);
        }

        /**
         * 4) SEARCHING (LIKE search across whitelisted columns)
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Provide quick keyword searching via ?search=
         *
         * Behavior:
         * - Builds a grouped WHERE:
         *     (col1 LIKE %search% OR col2 LIKE %search% OR ...)
         *
         * Safety:
         * - Only columns in $searchable are searched.
         * - If $searchable is empty, search is ignored.
         *
         * Tip:
         * - If you want relationship searching (user.name), that requires whereHas logic
         *   and should be handled in $customQuery, or by extending this trait further.
         */
        if ($request->filled('search') && !empty($searchable)) {
            $search = (string) $request->input('search');

            $query->where(function ($q) use ($searchable, $search) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        /**
         * 5) SORTING (orderBy with whitelist)
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Allow sorting via:
         *     ?sort_by=city&sort_direction=asc
         *
         * Safety:
         * - sort_by must be in the $sortable whitelist.
         * - sort_direction limited to asc/desc only.
         * - If invalid or missing, defaults to latest() (created_at desc).
         *
         * Tip:
         * - Relationship sorting typically requires JOINs and is more complex.
         *   That belongs in $customQuery (or a future extension).
         */
        $sortBy = $request->input('sort_by');
        $sortDirection = strtolower((string) $request->input('sort_direction', 'asc'));

        if ($sortBy && in_array($sortBy, $sortable, true)) {
            $sortDirection = in_array($sortDirection, ['asc', 'desc'], true)
                ? $sortDirection
                : 'asc';

            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest();
        }

        /**
         * 6) PAGINATION
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Control response size and support paging.
         *
         * Params:
         * - ?per_page=10 (default)
         *
         * Protection:
         * - Maximum per_page capped to 100 to avoid abuse.
         */
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $data = $query->paginate($perPage);

        /**
         * RESPONSE SHAPE
         * -----------------------------------------------------------------------------
         * Purpose:
         * - Standardize response format across endpoints.
         *
         * Note on getCollection():
         * - Returning $data->getCollection() ensures we return the Eloquent models as a
         *   collection (including loaded relationships) rather than a plain array.
         * - If you want the default Laravel paginator structure (data, links, meta),
         *   you can simply return $data instead and let Laravel serialize it.
         */
        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data->getCollection(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * PAGINATE LIST (Convenience Wrapper)
     * -----------------------------------------------------------------------------
     * Purpose:
     * - Provide a simple "one-liner" wrapper for index/list endpoints.
     *
     * Usage in repository:
     *   return $this->paginateList(
     *       $request,
     *       new Address,
     *       $this->searchable,
     *       $this->sortable,
     *       $this->allowedRelationships,
     *       $this->filterable,
     *       function ($query, $request) {
     *           // custom logic here
     *       }
     *   );
     *
     * What it does:
     * - Creates the base query: $model::query()
     * - Passes everything to applyQueryFeatures()
     */
    protected function paginateList(
        Request $request,
        Model $model,
        array $searchable = [],
        array $sortable = [],
        array $allowedRelationships = [],
        array $filterable = [],
        ?callable $customQuery = null
    ): JsonResponse {
        $query = $model::query();

        return $this->applyQueryFeatures(
            $query,
            $request,
            $searchable,
            $sortable,
            $allowedRelationships,
            $filterable,
            $customQuery
        );
    }
}

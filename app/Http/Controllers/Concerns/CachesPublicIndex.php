<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait CachesPublicIndex
{
    /**
     * Met en cache la première page des listes publiques (TTL court) pour limiter la charge MySQL.
     */
    protected function cachedPaginate(Request $request, string $cachePrefix, Builder $query, int $perPage = 20): \Illuminate\Http\JsonResponse
    {
        $page = (int) $request->query('page', 1);
        if ($page !== 1) {
            return response()->json($query->paginate($perPage));
        }

        $key = $cachePrefix.'.'.md5(json_encode($request->query()));

        $payload = Cache::remember($key, 45, function () use ($query, $perPage) {
            return $query->clone()->paginate($perPage)->toArray();
        });

        return response()->json($payload);
    }
}

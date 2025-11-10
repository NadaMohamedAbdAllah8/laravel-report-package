<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait RespondsWithJson
{
    public function returnErrorMessage($message, $code): HttpJsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function returnSuccessMessage($message = '', $code = Response::HTTP_OK): HttpJsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $code);
    }

    public function returnItemWithSuccessMessage($item, $message): HttpJsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'item' => $item,
        ], Response::HTTP_OK);
    }

    public function returnItemsWithSuccessMessage($items, $message): HttpJsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'items' => $items,
        ], Response::HTTP_OK);
    }

    public function returnItemWithErrorMessage($item, $message, $validationCode, $code): HttpJsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $validationCode,
            'item' => $item,
        ], $code);
    }

    public function returnPaginatedData($item, $message, $resourcePath, $groupBy = null): HttpJsonResponse
    {
        $items = $resourcePath::collection($item);

        if ($groupBy) {
            $items = $items->groupBy($groupBy);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'items' => $items,
            'size' => $item->count(),
            'page' => $item->currentPage(),
            'total_pages' => $item->lastPage(),
            'total_size' => $item->total(),
            'per_page' => $item->perPage(),
        ]);
    }

    public function returnPaginatedCollection($item, $message, $resourcePath, $groupBy = null): HttpJsonResponse
    {
        $items = new $resourcePath($item);

        if ($groupBy) {
            $items = $items->groupBy($groupBy);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'items' => $items,
            'size' => $item->count(),
            'page' => $item->currentPage(),
            'total_pages' => $item->lastPage(),
            'total_size' => $item->total(),
            'per_page' => $item->perPage(),
        ]);
    }
}

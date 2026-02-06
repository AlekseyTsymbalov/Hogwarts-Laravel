<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wishlist\AddToWishlistRequest;
use App\Http\Requests\Wishlist\WishlistIndexRequest;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function add(AddToWishlistRequest $request)
    {
        $userId = $request->user()->id;
        $productId = (int)$request->validated()['product_id'];

        $wishlist = Wishlist::query()->firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return $this->createdResponse([
            'id' => $wishlist->id,
            'product_id' => $wishlist->product_id,
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $userId = $request->user()->id;

        Wishlist::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        return $this->okResponse([
            'deleted' => true,
        ]);
    }

    public function list(WishlistIndexRequest $request)
    {
        $userId = $request->user()->id;
        $params = $request->params();

        $baseQuery = Wishlist::query()->where('user_id', $userId);

        $total = (clone $baseQuery)->count();

        $productIds = (clone $baseQuery)
            ->orderBy('id', 'desc')
            ->limit($params['limit'])
            ->offset($params['offset'])
            ->pluck('product_id')
            ->values();

        if ($productIds->isEmpty()) {
            return $this->okResponse([
                'items' => [],
                'meta' => [
                    'total' => $total,
                    'limit' => $params['limit'],
                    'offset' => $params['offset'],
                ],
            ]);
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $orderedProducts = collect($productIds)
            ->map(fn($id) => $products->get($id))
            ->filter()
            ->values();

        return $this->okResponse([
            'items' => ProductListResource::collection($orderedProducts),
            'meta' => [
                'total' => $total,
                'limit' => $params['limit'],
                'offset' => $params['offset'],
            ],
        ]);
    }

    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        Wishlist::query()
            ->where('user_id', $userId)
            ->delete();

        return $this->okResponse([
            'cleared' => true,
        ]);
    }
}

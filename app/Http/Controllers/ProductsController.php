<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\ProductsIndexRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductListResource;
use App\Models\Product;

class ProductsController extends Controller
{
    public function list(ProductsIndexRequest $request)
    {
        $params = $request->params();

        $query = Product::query()
            ->when($params['section_id'] !== null, fn ($q) => $q->where('section_id', $params['section_id']))
            ->orderBy($params['sort'], $params['order']);

        $total = (clone $query)->count();

        $products = $query
            ->limit($params['limit'])
            ->offset($params['offset'])
            ->get();

        return $this->okResponse([
            'items' => ProductListResource::collection($products),
            'meta' => [
                'total'  => $total,
                'limit'  => $params['limit'],
                'offset' => $params['offset'],
                'sort'   => $params['sort'],
                'order'  => $params['order'],
            ],
        ]);
    }
    public function detail(int $id)
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return $this->notFoundResponse();
        }

        return $this->okResponse(new ProductDetailResource($product));
    }
}

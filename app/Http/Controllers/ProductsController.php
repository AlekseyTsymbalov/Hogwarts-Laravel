<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailResource;
use App\Models\Product;

class ProductsController extends Controller
{
    public function detail(int $id)
    {
        $product = Product::query()->find($id);

        if (!$product) {
            return $this->notFoundResponse();
        }

        return $this->okResponse(new ProductDetailResource($product));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Basket\BasketAddRequest;
use App\Http\Requests\Basket\BasketIndexRequest;
use App\Http\Requests\Basket\BasketUpdateRequest;
use App\Http\Resources\BasketItemResource;
use App\Models\BasketItem;
use App\Services\BasketPriceService;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function add(BasketAddRequest $request)
    {
        $userId = $request->user()->id;
        $params = $request->params();

        $item = BasketItem::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $params['product_id'],
            ],
            [
                'quantity' => 1,
            ]
        );

        return $this->createdResponse([
            'id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => (int) $item->quantity,
        ]);
    }

    public function update(BasketUpdateRequest $request, int $id)
    {
        $userId = $request->user()->id;
        $params = $request->params();

        $item = BasketItem::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$item) {
            return $this->okResponse([
                'updated' => false,
            ]);
        }

        $item->quantity = $params['quantity'];
        $item->save();

        return $this->okResponse([
            'updated'  => true,
            'id'       => $item->id,
            'quantity' => (int) $item->quantity,
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $userId = $request->user()->id;

        BasketItem::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        return $this->okResponse([
            'deleted' => true,
        ]);
    }

    public function list(
        BasketIndexRequest $request,
        BasketPriceService $priceService
    ) {
        $userId = $request->user()->id;
        $params = $request->params();

        $baseQuery = BasketItem::query()
            ->where('user_id', $userId);

        $total = BasketItem::query()
            ->where('user_id', $userId)
            ->count();

        $items = BasketItem::query()
            ->where('user_id', $userId)
            ->with('product')
            ->orderBy('id', 'desc')
            ->limit($params['limit'])
            ->offset($params['offset'])
            ->get();

        $allItems = BasketItem::query()
            ->where('user_id', $userId)
            ->with('product:id,price')
            ->get();

        $summary = $priceService->summary($allItems);
        //dd($summary);
        return $this->okResponse([
            'items' => BasketItemResource::collection($items),
            'meta'  => [
                'total'  => $total,
                'limit'  => $params['limit'],
                'offset' => $params['offset'],
            ],
            'summary' => $summary,
        ]);
    }

    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        BasketItem::query()
            ->where('user_id', $userId)
            ->delete();

        return $this->okResponse([
            'cleared' => true,
        ]);
    }
}
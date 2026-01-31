<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use Illuminate\Http\Request;
use App\Models\Section;

class SectionsController extends Controller
{
    public function list()
    {
        $sections = Section::query()
            ->where('active', true)
            ->orderBy('sort')
            ->get();

        return $this->okResponse($sections);
    }

    public function detail(Request $request, int $id)
    {
        $section = Section::query()
            ->where('active', true)
            ->find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        $productsQuery = $section->products()->orderBy('id');

        $categoryId = $request->query('category_id');

        if ($categoryId !== null) {
            $productsQuery->where('category_id', (int)$categoryId);
        }

        $products = $productsQuery->get();

        return $this->okResponse([
            'section' => $section,
            'products' => ProductListResource::collection($products),
        ]);
    }
}

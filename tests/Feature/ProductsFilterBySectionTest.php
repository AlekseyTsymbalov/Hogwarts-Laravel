<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductsFilterBySectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_products_from_given_section(): void
    {
        // arrange: создаём 2 секции
        $sectionA = Section::query()->create([
            'name' => 'Books',
            'slug' => 'books',
            'sort' => 10,
            'active' => true,
        ]);

        $sectionB = Section::query()->create([
            'name' => 'Wands',
            'slug' => 'wands',
            'sort' => 20,
            'active' => true,
        ]);

        // товары секции A
        $productA1 = Product::query()->create([
            'name' => 'Book 1',
            'price' => 10,
            'section_id' => $sectionA->id,
        ]);

        $productA2 = Product::query()->create([
            'name' => 'Book 2',
            'price' => 20,
            'section_id' => $sectionA->id,
        ]);

        // товар секции B
        $productB = Product::query()->create([
            'name' => 'Wand',
            'price' => 50,
            'section_id' => $sectionB->id,
        ]);

        // act: запрашиваем товары ТОЛЬКО секции A
        $response = $this->getJson('/api/products?section_id=' . $sectionA->id);

        // assert: запрос успешен
        $response->assertStatus(200);

        // assert: всего 2 товара
        $response->assertJsonPath('data.meta.total', 2);

        // assert: ids товаров — только из секции A
        $ids = array_column($response->json('data.items'), 'id');

        $this->assertContains($productA1->id, $ids);
        $this->assertContains($productA2->id, $ids);
        $this->assertNotContains($productB->id, $ids);
    }
}

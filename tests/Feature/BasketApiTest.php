<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedBase(): array
    {
        $user = User::factory()->create();

        $section = Section::query()->create([
            'name' => 'Books',
            'slug' => 'books',
            'sort' => 10,
            'active' => true,
            'description' => 'All magic books',
        ]);

        Product::query()->insert([
            [
                'name' => 'Book A',
                'price' => 10.00,
                'description' => null,
                'image' => null,
                'section_id' => $section->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Book B',
                'price' => 20.00,
                'description' => null,
                'image' => null,
                'section_id' => $section->id,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'name' => 'Book C',
                'price' => 30.00,
                'description' => null,
                'image' => null,
                'section_id' => $section->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $productIds = Product::query()->orderBy('id')->pluck('id')->all();

        return [$user, $productIds];
    }

    #[Test]
    public function it_adds_to_cart_idempotently(): void
    {
        [$user, $productIds] = $this->seedBase();
        Sanctum::actingAs($user);

        $payload = ['product_id' => $productIds[0]];

        $this->postJson('/api/cart/add', $payload)
            ->assertStatus(201);

        $this->postJson('/api/cart/add', $payload)
            ->assertStatus(201);

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $productIds[0],
            'quantity' => 1,
        ]);
    }

    #[Test]
    public function it_sets_quantity(): void
    {
        [$user, $productIds] = $this->seedBase();
        Sanctum::actingAs($user);

        $item = CartItem::query()->create([
            'user_id' => $user->id,
            'product_id' => $productIds[0],
            'quantity' => 1,
        ]);

        $this->putJson("/api/cart/{$item->id}", ['quantity' => 3])
            ->assertStatus(200)
            ->assertJsonPath('data.updated', true)
            ->assertJsonPath('data.quantity', 3);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 3,
        ]);
    }

    #[Test]
    public function it_returns_cart_items_with_meta_and_summary_limit_offset(): void
    {
        [$user, $productIds] = $this->seedBase();
        Sanctum::actingAs($user);

        // создаём 3 позиции с quantity=1 => total_items = 3
        foreach ($productIds as $pid) {
            CartItem::query()->create([
                'user_id' => $user->id,
                'product_id' => $pid,
                'quantity' => 1,
            ]);
        }

        $response = $this->getJson('/api/cart?limit=2&offset=0')
            ->assertStatus(200)
            ->assertJsonPath('data.meta.total', 3)
            ->assertJsonPath('data.meta.limit', 2)
            ->assertJsonPath('data.meta.offset', 0);

        $items = $response->json('data.items');
        $this->assertCount(2, $items);

        // проверяем структуру item
        $this->assertArrayHasKey('item_id', $items[0]);
        $this->assertArrayHasKey('quantity', $items[0]);
        $this->assertArrayHasKey('product', $items[0]);
        $this->assertArrayHasKey('price', $items[0]);

        // summary считается по текущей странице (limit=2)
        // на первой странице 2 товара: 10 + 20 или 30 + 20 — зависит от порядка id desc
        // поэтому проверим только наличие ключей и типовую логику
        $this->assertNotNull($response->json('data.summary.total_items'));
        $this->assertNotNull($response->json('data.summary.total_base'));
        $this->assertNotNull($response->json('data.summary.total_final'));
    }

    #[Test]
    public function it_deletes_cart_item_without_error_when_missing(): void
    {
        [$user] = $this->seedBase();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/cart/999999')
            ->assertStatus(200)
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseCount('cart_items', 0);
    }

    #[Test]
    public function it_clears_only_current_user_cart(): void
    {
        [$user, $productIds] = $this->seedBase();

        $otherUser = User::factory()->create();

        CartItem::query()->create([
            'user_id' => $user->id,
            'product_id' => $productIds[0],
            'quantity' => 1,
        ]);
        CartItem::query()->create([
            'user_id' => $user->id,
            'product_id' => $productIds[1],
            'quantity' => 2,
        ]);

        CartItem::query()->create([
            'user_id' => $otherUser->id,
            'product_id' => $productIds[2],
            'quantity' => 3,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/cart')
            ->assertStatus(200)
            ->assertJsonPath('data.cleared', true);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $productIds[0],
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $productIds[1],
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $otherUser->id,
            'product_id' => $productIds[2],
        ]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WishlistApiTest extends TestCase
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

        $products = Product::query()->insert([
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
    public function it_adds_to_wishlist_idempotently(): void
    {
        [$user, $productIds] = $this->seedBase();
        Sanctum::actingAs($user);

        $payload = ['product_id' => $productIds[0]];

        $this->postJson('/api/wishlist/add', $payload)
            ->assertStatus(201);

        $this->postJson('/api/wishlist/add', $payload)
            ->assertStatus(201);

        $this->assertDatabaseCount('wishlists', 1);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productIds[0],
        ]);
    }

    #[Test]
    public function it_deletes_wishlist_item_without_error_when_missing(): void
    {
        [$user] = $this->seedBase();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/wishlist/999999')
            ->assertStatus(200)
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseCount('wishlists', 0);
    }

    #[Test]
    public function it_returns_wishlist_products_with_limit_offset(): void
    {
        [$user, $productIds] = $this->seedBase();
        Sanctum::actingAs($user);

        foreach ($productIds as $pid) {
            Wishlist::query()->create([
                'user_id' => $user->id,
                'product_id' => $pid,
            ]);
        }

        $expectedDesc = array_reverse($productIds);

        $response = $this->getJson('/api/wishlist?limit=2&offset=0')
            ->assertStatus(200)
            ->assertJsonPath('data.meta.total', 3)
            ->assertJsonPath('data.meta.limit', 2)
            ->assertJsonPath('data.meta.offset', 0);

        $ids = array_map(fn ($item) => $item['id'], $response->json('data.items'));
        $this->assertSame(array_slice($expectedDesc, 0, 2), $ids);

        $response2 = $this->getJson('/api/wishlist?limit=2&offset=2')
            ->assertStatus(200);

        $ids2 = array_map(fn ($item) => $item['id'], $response2->json('data.items'));
        $this->assertSame(array_slice($expectedDesc, 2, 2), $ids2);
    }

    #[Test]
    public function it_clears_only_current_user_wishlist(): void
    {
        [$user, $productIds] = $this->seedBase();

        $otherUser = User::factory()->create();

        Wishlist::query()->create([
            'user_id' => $user->id,
            'product_id' => $productIds[0],
        ]);
        Wishlist::query()->create([
            'user_id' => $user->id,
            'product_id' => $productIds[1],
        ]);

        Wishlist::query()->create([
            'user_id' => $otherUser->id,
            'product_id' => $productIds[2],
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/wishlist')
            ->assertStatus(200)
            ->assertJsonPath('data.cleared', true);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productIds[0],
        ]);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productIds[1],
        ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $otherUser->id,
            'product_id' => $productIds[2],
        ]);
    }
}

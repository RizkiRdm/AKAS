<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Models\Product;
use App\Domain\Models\Shift;
use App\Domain\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class SalesTransactionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_create_sale_transaction(): void
    {
        $user = User::factory()->create();
        $shift = Shift::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $product = Product::factory()->create(['stok' => 10, 'price' => 10000]);

        $response = $this->actingAs($user)
            ->postJson(route('sales.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 2],
                ],
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('sales', [
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'total' => 20000,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 10000,
            'subtotal' => 20000,
        ]);

        $this->assertEquals(8, $product->fresh()->stok);
    }

    public function test_cannot_create_sale_without_active_shift(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stok' => 10]);

        $response = $this->actingAs($user)
            ->postJson(route('sales.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 1],
                ],
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'No active shift found. Please open a shift first.');
    }

    public function test_cannot_create_sale_with_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $shift = Shift::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $product = Product::factory()->create(['stok' => 5]);

        $response = $this->actingAs($user)
            ->postJson(route('sales.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 10],
                ],
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => "Insufficient stock for {$product->name}. Requested: 10, Available: 5"]);
    }

    public function test_can_create_sale_with_payment_ref(): void
    {
        $user = User::factory()->create();
        $shift = Shift::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        $product = Product::factory()->create(['stok' => 10]);

        $response = $this->actingAs($user)
            ->postJson(route('sales.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 1],
                ],
                'payment_method' => 'qris',
                'payment_ref' => 'REF-12345',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sales', [
            'payment_method' => 'qris',
            'payment_ref' => 'REF-12345',
        ]);
    }
}

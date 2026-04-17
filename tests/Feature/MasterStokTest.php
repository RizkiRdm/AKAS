<?php

namespace Tests\Feature;

use App\Domain\Models\Category;
use App\Domain\Models\Product;
use App\Domain\Models\Unit;
use App\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterStokTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_master_stok_page()
    {
        $response = $this->actingAs($this->user)->get(route('master-stok.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_product()
    {
        $category = Category::factory()->create();
        $unit = Unit::factory()->create();

        $data = [
            'name' => 'Kopi Kapal Api',
            'sku' => 'KOPI-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 4000,
            'price' => 5000,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('master-stok.products.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Kopi Kapal Api']);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create(['name' => 'Original Name']);

        $data = [
            'name' => 'Updated Name',
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'unit_id' => $product->unit_id,
            'purchase_price' => 5000,
            'price' => 6000,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('master-stok.products.update', $product), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('master-stok.products.destroy', $product));

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_stock_auto_updates_on_stock_in()
    {
        $product = Product::factory()->create(['stok' => 10]);

        $data = [
            'product_id' => $product->id,
            'qty' => 5,
            'note' => 'Restock',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('master-stok.stock-in.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stok' => 15]);
        $this->assertDatabaseHas('stock_in', ['product_id' => $product->id, 'qty' => 5]);
    }

    public function test_can_create_category()
    {
        $data = ['name' => 'Snacks'];
        $response = $this->actingAs($this->user)->post(route('master-stok.categories.store'), $data);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Snacks']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function makeSeller(): User
    {
        return User::factory()->create(['role' => 'seller', 'deposit' => 0]);
    }

    private function makeBuyer(): User
    {
        return User::factory()->create(['role' => 'buyer', 'deposit' => 0]);
    }

    private function validProductPayload(array $overrides = []): array
    {
        return array_merge([
            'productName'     => 'Cola',
            'cost'            => 50,
            'amountAvailable' => 10,
        ], $overrides);
    }

    public function test_seller_can_create_product(): void
    {
        $seller = $this->makeSeller();

        $this->actingAs($seller, 'sanctum')
            ->postJson('/api/products', $this->validProductPayload())
            ->assertCreated()
            ->assertJsonPath('productName', 'Cola')
            ->assertJsonPath('sellerId', $seller->id);
    }

    public function test_buyer_cannot_create_product(): void
    {
        $buyer = $this->makeBuyer();

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/products', $this->validProductPayload())
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_product(): void
    {
        $this->postJson('/api/products', $this->validProductPayload())
            ->assertUnauthorized();
    }

    public function test_product_cost_must_be_multiple_of_5(): void
    {
        $seller = $this->makeSeller();

        $this->actingAs($seller, 'sanctum')
            ->postJson('/api/products', $this->validProductPayload(['cost' => 33]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cost']);
    }

    public function test_product_name_is_required(): void
    {
        $seller = $this->makeSeller();

        $this->actingAs($seller, 'sanctum')
            ->postJson('/api/products', $this->validProductPayload(['productName' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['productName']);
    }

    public function test_seller_can_update_own_product(): void
    {
        $seller  = $this->makeSeller();
        $product = Product::create([
            'productName' => 'Cola', 'cost' => 50, 'amountAvailable' => 10, 'sellerId' => $seller->id,
        ]);

        $this->actingAs($seller, 'sanctum')
            ->putJson("/api/products/{$product->id}", ['productName' => 'Pepsi'])
            ->assertOk()
            ->assertJsonPath('productName', 'Pepsi');
    }

    public function test_seller_cannot_update_others_product(): void
    {
        $seller1 = $this->makeSeller();
        $seller2 = $this->makeSeller();
        $product = Product::create([
            'productName' => 'Cola', 'cost' => 50, 'amountAvailable' => 10, 'sellerId' => $seller1->id,
        ]);

        $this->actingAs($seller2, 'sanctum')
            ->putJson("/api/products/{$product->id}", ['productName' => 'Pepsi'])
            ->assertForbidden();
    }

    public function test_seller_can_delete_own_product(): void
    {
        $seller  = $this->makeSeller();
        $product = Product::create([
            'productName' => 'Cola', 'cost' => 50, 'amountAvailable' => 10, 'sellerId' => $seller->id,
        ]);

        $this->actingAs($seller, 'sanctum')
            ->deleteJson("/api/products/{$product->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyTest extends TestCase
{
    use RefreshDatabase;

    private function makeBuyer(int $deposit = 0): User
    {
        return User::factory()->create(['role' => 'buyer', 'deposit' => $deposit]);
    }

    private function makeSeller(): User
    {
        return User::factory()->create(['role' => 'seller', 'deposit' => 0]);
    }

    private function makeProduct(User $seller, int $cost = 100, int $stock = 10): Product
    {
        return Product::create([
            'productName'     => 'Cola',
            'cost'            => $cost,
            'amountAvailable' => $stock,
            'sellerId'        => $seller->id,
        ]);
    }

    public function test_buyer_can_buy_product_successfully(): void
    {
        $seller  = $this->makeSeller();
        $buyer   = $this->makeBuyer(200);
        $product = $this->makeProduct($seller, 100, 5);

        $response = $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/buy', ['productId' => $product->id, 'amount' => 1]);

        $response->assertOk()
            ->assertJsonPath('totalSpent', 100)
            ->assertJsonStructure(['totalSpent', 'products', 'change']);

        $this->assertDatabaseHas('users', ['id' => $buyer->id, 'deposit' => 0]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'amountAvailable' => 4]);
    }

    public function test_change_is_calculated_correctly(): void
    {
        $seller  = $this->makeSeller();
        $buyer   = $this->makeBuyer(200);
        $product = $this->makeProduct($seller, 65, 5);

        $response = $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/buy', ['productId' => $product->id, 'amount' => 1]);

        $response->assertOk()->assertJsonPath('totalSpent', 65);

        $change = $response->json('change');
        $this->assertEquals(1, $change['100']);
        $this->assertEquals(1, $change['20']);
        $this->assertEquals(1, $change['10']);
        $this->assertEquals(1, $change['5']);
    }

    public function test_insufficient_deposit_returns_400(): void
    {
        $seller  = $this->makeSeller();
        $buyer   = $this->makeBuyer(50);
        $product = $this->makeProduct($seller, 100, 5);

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/buy', ['productId' => $product->id, 'amount' => 1])
            ->assertStatus(400)
            ->assertJsonPath('message', 'Insufficient deposit.');
    }

    public function test_insufficient_stock_returns_400(): void
    {
        $seller  = $this->makeSeller();
        $buyer   = $this->makeBuyer(500);
        $product = $this->makeProduct($seller, 100, 1);

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/buy', ['productId' => $product->id, 'amount' => 2])
            ->assertStatus(400)
            ->assertJsonPath('message', 'Insufficient stock.');
    }

    public function test_buy_nonexistent_product_returns_422(): void
    {
        $buyer = $this->makeBuyer(500);

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/buy', ['productId' => 9999, 'amount' => 1])
            ->assertUnprocessable();
    }

    public function test_seller_cannot_buy(): void
    {
        $seller  = $this->makeSeller();
        $product = $this->makeProduct($seller, 100, 5);

        $this->actingAs($seller, 'sanctum')
            ->postJson('/api/buy', ['productId' => $product->id, 'amount' => 1])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_buy(): void
    {
        $this->postJson('/api/buy', ['productId' => 1, 'amount' => 1])
            ->assertUnauthorized();
    }
}

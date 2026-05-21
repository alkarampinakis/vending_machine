<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    private function makeBuyer(): User
    {
        return User::factory()->create(['role' => 'buyer', 'deposit' => 0]);
    }

    private function makeSeller(): User
    {
        return User::factory()->create(['role' => 'seller', 'deposit' => 0]);
    }

    public function test_buyer_can_deposit_valid_coin(): void
    {
        $buyer = $this->makeBuyer();

        $response = $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/deposit', ['amount' => 50]);

        $response->assertOk()->assertJson(['deposit' => 50]);
        $this->assertDatabaseHas('users', ['id' => $buyer->id, 'deposit' => 50]);
    }

    public function test_buyer_deposit_accumulates(): void
    {
        $buyer = $this->makeBuyer();

        $this->actingAs($buyer, 'sanctum')->postJson('/api/deposit', ['amount' => 20]);
        $response = $this->actingAs($buyer, 'sanctum')->postJson('/api/deposit', ['amount' => 20]);

        $response->assertOk()->assertJson(['deposit' => 40]);
    }

    public function test_invalid_coin_amount_rejected(): void
    {
        $buyer = $this->makeBuyer();

        $this->actingAs($buyer, 'sanctum')
            ->postJson('/api/deposit', ['amount' => 15])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_deposit_requires_authentication(): void
    {
        $this->postJson('/api/deposit', ['amount' => 50])
            ->assertUnauthorized();
    }

    public function test_seller_cannot_deposit(): void
    {
        $seller = $this->makeSeller();

        $this->actingAs($seller, 'sanctum')
            ->postJson('/api/deposit', ['amount' => 50])
            ->assertForbidden();
    }
}

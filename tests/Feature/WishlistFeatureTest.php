<?php

use App\Models\User;
use App\Models\WishList;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->product = \App\Models\Product::factory()->create();
});

describe('Wishlist API', function () {
    it('requires authentication to access wishlist', function () {
        $response = $this->getJson('/api/v1/wishlist');
        $response->assertStatus(401);
    });

    it('can get user wishlist', function () {
        $this->actingAs($this->user);
        WishList::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
        $response = $this->getJson('/api/v1/wishlist');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    });

    it('can add product to wishlist', function () {
        $this->actingAs($this->user);
        $response = $this->postJson('/api/v1/wishlist', [
            'product_id' => $this->product->id,
        ]);
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product added to wishlist successfully',
            ])
            ->assertJsonStructure([
                'data',
                'count',
            ]);
        $this->assertDatabaseHas('wish_lists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    });

    it('cannot add non-existent product to wishlist', function () {
        $this->actingAs($this->user);
        $response = $this->postJson('/api/v1/wishlist', [
            'product_id' => 999,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    });

    it('cannot add duplicate product to wishlist', function () {
        $this->actingAs($this->user);
        $this->postJson('/api/v1/wishlist', [
            'product_id' => $this->product->id,
        ]);
        $response = $this->postJson('/api/v1/wishlist', [
            'product_id' => $this->product->id,
        ]);
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Failed to add product to wishlist',
            ]);
    });

    it('can remove product from wishlist', function () {
        $this->actingAs($this->user);
        WishList::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
        $response = $this->deleteJson('/api/v1/wishlist', [
            'product_id' => $this->product->id,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product removed from wishlist successfully',
            ]);
        $this->assertDatabaseMissing('wish_lists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    });

    it('can toggle product in wishlist - add', function () {
        $this->actingAs($this->user);
        $response = $this->postJson('/api/v1/wishlist/toggle', [
            'product_id' => $this->product->id,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product added from wishlist successfully',
                'data' => [
                    'action' => 'added',
                    'is_in_wishlist' => true,
                ],
            ]);
        $this->assertDatabaseHas('wish_lists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    });

    it('can toggle product in wishlist - remove', function () {
        $this->actingAs($this->user);
        WishList::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
        $response = $this->postJson('/api/v1/wishlist/toggle', [
            'product_id' => $this->product->id,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product removed from wishlist successfully',
                'data' => [
                    'action' => 'removed',
                    'is_in_wishlist' => false,
                ],
            ]);
        $this->assertDatabaseMissing('wish_lists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    });

    it('can check if product is in wishlist', function () {
        $this->actingAs($this->user);
        WishList::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
        $response = $this->getJson("/api/v1/wishlist/check/{$this->product->id}");
        $response->assertStatus(200)
            ->assertJson([
                'is_in_wishlist' => true,
            ]);
    });

    it('can get wishlist count', function () {
        $this->actingAs($this->user);
        WishList::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->getJson('/api/v1/wishlist/count');
        $response->assertStatus(200)
            ->assertJson([
                'count' => 3,
            ]);
    });

    it('can clear wishlist', function () {
        $this->actingAs($this->user);
        WishList::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->deleteJson('/api/v1/wishlist/clear');
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Wishlist cleared successfully',
                'count' => 0,
            ]);
        $this->assertDatabaseCount('wish_lists', 0);
    });
});

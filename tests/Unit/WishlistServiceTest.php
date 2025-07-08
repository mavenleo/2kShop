<?php

use App\Models\User;
use App\Models\Product;
use App\Models\WishList;
use App\Services\WishlistService;
use App\Exceptions\WishlistException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('WishlistService', function () {
    it('can get user wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishList::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $result = $wishlistService->getUserWishlist($user);

        expect($result)->toHaveCount(1);
        expect($result->first()->product_id)->toBe($product->id);
    });

    it('can add product to wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = $wishlistService->addToWishlist($user, $product->id);

        expect($wishlist->user_id)->toBe($user->id);
        expect($wishlist->product_id)->toBe($product->id);
        expect(WishList::where('user_id', $user->id)->where('product_id', $product->id)->exists())->toBeTrue();
    });

    it('cannot add non-existent product to wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $invalidProductId = 999;

        expect(fn () => $wishlistService->addToWishlist($user, $invalidProductId))
            ->toThrow(WishlistException::class, 'Product not found.');
    });

    it('cannot add duplicate product to wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishList::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        expect(fn () => $wishlistService->addToWishlist($user, $product->id))
            ->toThrow(WishlistException::class, 'Product is already in your wishlist.');
    });

    it('can remove product from wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishList::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $wishlistService->removeFromWishlist($user, $product->id);

        expect(WishList::where('user_id', $user->id)->where('product_id', $product->id)->exists())->toBeFalse();
    });

    it('can toggle product in wishlist - add', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $result = $wishlistService->toggleWishlist($user, $product->id);

        expect($result['action'])->toBe('added');
        expect($result['is_in_wishlist'])->toBeTrue();
        expect(WishList::where('user_id', $user->id)->where('product_id', $product->id)->exists())->toBeTrue();
    });

    it('can toggle product in wishlist - remove', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishList::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $result = $wishlistService->toggleWishlist($user, $product->id);

        expect($result['action'])->toBe('removed');
        expect($result['is_in_wishlist'])->toBeFalse();
        expect(WishList::where('user_id', $user->id)->where('product_id', $product->id)->exists())->toBeFalse();
    });

    it('can check if product is in wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishList::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $result = $wishlistService->isInWishlist($user, $product->id);

        expect($result)->toBeTrue();
    });

    it('can get wishlist count', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            WishList::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        $count = $wishlistService->getWishlistCount($user);

        expect($count)->toBe(3);
    });

    it('can clear wishlist', function () {
        $wishlistService = new WishlistService();
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            WishList::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        $wishlistService->clearWishlist($user);

        expect(WishList::where('user_id', $user->id)->count())->toBe(0);
    });
}); 
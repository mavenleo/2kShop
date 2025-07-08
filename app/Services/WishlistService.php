<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\WishList;
use App\Exceptions\WishlistException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class WishlistService
{
    /**
     * Get user's wishlist with pagination
     */
    public function getUserWishlistPaginated(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return WishList::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user's wishlist
     */
    public function getUserWishlist(User $user): Collection
    {
        return WishList::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist(User $user, int $productId): WishList
    {
        // Check if product exists
        $product = Product::find($productId);

        throw_if(!$product, WishlistException::class, 'Product not found.');

        // Check if already in wishlist
        throw_if($this->isInWishlist($user, $productId), WishlistException::class, 'Product is already in your wishlist.');

        return WishList::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist(User $user, int $productId): bool
    {
        // Check if product exists in wishlist
        throw_if(!$this->isInWishlist($user, $productId), WishlistException::class, 'Product is not in your wishlist.');

        return WishList::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist(User $user, int $productId): bool
    {
        return $user->wishlist()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get wishlist count
     */
    public function getWishlistCount(User $user): int
    {
        return WishList::where('user_id', $user->id)->count();
    }

    /**
     * Clear user's wishlist
     */
    public function clearWishlist(User $user): bool
    {
        return WishList::where('user_id', $user->id)->delete() > 0;
    }

    /**
     * Toggle product in wishlist
     */
    public function toggleWishlist(User $user, int $productId): array
    {
        $isInWishlist = $this->isInWishlist($user, $productId);

        if ($isInWishlist) {
            $this->removeFromWishlist($user, $productId);
            $action = 'removed';
        } else {
            $this->addToWishlist($user, $productId);
            $action = 'added';
        }

        return [
            'action' => $action,
            'is_in_wishlist' => !$isInWishlist,
            'count' => $this->getWishlistCount($user),
        ];
    }

    /**
     * Get wishlist summary
     */
    public function getWishlistSummary(User $user): array
    {
        $wishlist = $this->getUserWishlist($user);
        $count = $this->getWishlistCount($user);

        return [
            'count' => $count,
            'items' => $wishlist,
            'total_value' => $wishlist->sum('product.price'),
        ];
    }
}

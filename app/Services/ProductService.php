<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getPaginatedProducts(?User $user, int $perPage = 12): LengthAwarePaginator
    {
        $products = Product::paginate($perPage);

        $pageProductIds = $products->pluck('id')->toArray();

        $wishlistedIds = [];
        if ($user) {
            $wishlistedIds = $user->wishlist()
                ->whereIn('product_id', $pageProductIds)
                ->pluck('product_id')
                ->toArray();
        }

        $products->getCollection()->transform(function ($product) use ($wishlistedIds) {
            $product->is_wishlisted = in_array($product->id, $wishlistedIds);
            return $product;
        });

        return $products;
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }
}

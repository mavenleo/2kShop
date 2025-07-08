<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistRequest;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\WishlistResource;
use App\Http\Resources\ProductResource;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlistService
    ) {}

    /**
     * Get user's wishlist
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $wishlist = $this->wishlistService->getUserWishlistPaginated(request()->user(), request()->perPage ?? 15);

            // Map wishlist items to their products
            $products = collect($wishlist->items())->map(function ($wishlistItem) {
                return $wishlistItem->product;
            })->filter();

            return response()->json([
                'data' => WishlistResource::collection($wishlist->items()),
                'pagination' => [
                    'current_page' => $wishlist->currentPage(),
                    'last_page' => $wishlist->lastPage(),
                    'per_page' => $wishlist->perPage(),
                    'total' => $wishlist->total(),
                ],
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to fetch wishlist'
            ], 500);
        }
    }

    /**
     * Add product to wishlist
     *
     * @param WishlistRequest $request
     *
     * @return JsonResponse
     */
    public function store(WishlistRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $productId = $request->validated()['product_id'];

            $wishlistItem = $this->wishlistService->addToWishlist($user, $productId);
            $count = $this->wishlistService->getWishlistCount($user);

            return response()->json([
                'message' => 'Product added to wishlist successfully',
                'data' => $wishlistItem,
                'count' => $count,
            ], 201);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to add product to wishlist',
            ], 400);
        }
    }

    /**
     * Remove product from wishlist
     *
     * @param WishlistRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(WishlistRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $productId = $request->validated()['product_id'];

            $this->wishlistService->removeFromWishlist($user, $productId);
            $count = $this->wishlistService->getWishlistCount($user);

            return response()->json([
                'message' => 'Product removed from wishlist successfully',
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to remove product from wishlist',
            ], 400);
        }
    }

    /**
     * Toggle product in wishlist
     *
     * @param WishlistRequest $request
     *
     * @return JsonResponse
     */
    public function toggle(WishlistRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $productId = $request->validated()['product_id'];

            $result = $this->wishlistService->toggleWishlist($user, $productId);

            return response()->json([
                'message' => "Product {$result['action']} from wishlist successfully",
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to toggle product in wishlist',
            ], 400);
        }
    }

    /**
     * Check if product is in wishlist
     *
     * @param int $productId
     *
     * @return JsonResponse
     */
    public function check(int $productId): JsonResponse
    {
        try {
            $user = auth()->user();
            $isInWishlist = $this->wishlistService->isInWishlist($user, $productId);

            return response()->json([
                'is_in_wishlist' => $isInWishlist,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to check wishlist status',
            ], 500);
        }
    }

    /**
     * Get wishlist count
     *
     * @return JsonResponse
     */
    public function count(): JsonResponse
    {
        try {
            $user = auth()->user();
            $count = $this->wishlistService->getWishlistCount($user);

            return response()->json([
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to get wishlist count',
            ], 500);
        }
    }

    /**
     * Clear wishlist
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        try {
            $user = auth()->user();
            $this->wishlistService->clearWishlist($user);

            return response()->json([
                'message' => 'Wishlist cleared successfully',
                'count' => 0,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to clear wishlist',
            ], 500);
        }
    }
}

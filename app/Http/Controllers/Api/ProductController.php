<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly WishlistService $wishlistService
    ) {}

    /**
     * Get all products
     * @returns AnonymousResourceCollection|JsonResponse
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $products = $this->productService->getPaginatedProducts(request()->user(), request()->perPage ?? 15);

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No product to display!']);
            }

            return ProductResource::collection($products);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to fetch products',
            ], 500);
        }
    }

    /**
     * Get single product
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $response = [
                'data' => $product,
            ];

            // Add wishlist status if user is authenticated
            if (auth()->check()) {
                $isInWishlist = $this->wishlistService->isInWishlist(auth()->user(), $id);
                $response['is_wishlisted'] = $isInWishlist;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Failed to fetch product',
            ], 500);
        }
    }
}

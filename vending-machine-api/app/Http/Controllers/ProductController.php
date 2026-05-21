<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with('seller:id,username')->paginate(15);

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with('seller:id,username')->findOrFail($id);

        return response()->json($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create([
            'productName'     => $request->productName,
            'amountAvailable' => $request->amountAvailable,
            'cost'            => $request->cost,
            'sellerId'        => $request->user()->id,
        ]);

        return response()->json($product->load('seller:id,username'), 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        if ($product->sellerId !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $product->fill($request->only(['productName', 'amountAvailable', 'cost']));
        $product->save();

        return response()->json($product->load('seller:id,username'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        if ($product->sellerId !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuyRequest;
use App\Http\Requests\DepositRequest;
use App\Models\Product;
use App\Services\ChangeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendingController extends Controller
{
    public function deposit(DepositRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->deposit += $request->amount;
        $user->save();

        return response()->json(['deposit' => $user->deposit]);
    }

    public function buy(BuyRequest $request): JsonResponse
    {
        $user    = $request->user();
        $product = Product::findOrFail($request->productId);
        $amount  = $request->amount;

        if ($amount > $product->amountAvailable) {
            return response()->json(['message' => 'Insufficient stock.'], 400);
        }

        $totalCost = $product->cost * $amount;

        if ($user->deposit < $totalCost) {
            return response()->json(['message' => 'Insufficient deposit.'], 400);
        }

        $change = DB::transaction(function () use ($user, $product, $amount, $totalCost) {
            $changeAmount = $user->deposit - $totalCost;

            $product->amountAvailable -= $amount;
            $product->save();

            $user->deposit = 0;
            $user->save();

            return $changeAmount;
        });

        return response()->json([
            'totalSpent' => $totalCost,
            'products'   => [array_merge($product->toArray(), ['quantityBought' => $amount])],
            'change'     => ChangeCalculatorService::calculate($change),
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $user          = $request->user();
        $user->deposit = 0;
        $user->save();

        return response()->json(['deposit' => 0]);
    }
}

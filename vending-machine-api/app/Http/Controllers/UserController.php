<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'role'     => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        if ($request->user()->id !== $id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $user = User::findOrFail($id);
        $user->fill($request->only(['username', 'password', 'role']));
        $user->save();

        return response()->json($user);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if ($request->user()->id !== $id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        User::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}

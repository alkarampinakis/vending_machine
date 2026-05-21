<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()?->role !== $role) {
            return response()->json(
                ['message' => "This action requires the {$role} role."],
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}

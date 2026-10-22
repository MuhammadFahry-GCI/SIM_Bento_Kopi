<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekPeran
{
    // Pemakaian: ->middleware(CekPeran::class . ':kasir,manajer')
    public function handle(Request $request, Closure $next, string ...$peran)
    {
        $user = $request->user();
        if (!$user || !in_array($user->peran, $peran, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe o painel aos administradores. Admin único do site → flag is_admin
 * no usuário (sem Spatie).
 *
 * Duas respostas diferentes, porque são duas situações diferentes:
 *
 *  - Visitante não autenticado: vai para o login, que é onde ele resolve o
 *    problema. É o comportamento normal de uma área restrita.
 *  - Usuário autenticado e válido que não é admin: 403. Antes disso ele era
 *    DESLOGADO — a sessão dele era destruída por ter navegado até uma página
 *    que não pode ver. Enquanto o site tem um usuário só isso nunca dispara,
 *    mas é a semântica errada, e o dia em que houver um segundo usuário ela
 *    aparece como "o sistema me desloga sozinho".
 */
class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Acesso restrito ao administrador.');
        }

        abort_unless(Auth::user()->is_admin, 403, 'Acesso restrito ao administrador.');

        return $next($request);
    }
}

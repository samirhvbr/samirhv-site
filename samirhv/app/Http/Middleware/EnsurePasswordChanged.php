<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Força a troca de senha quando o usuário tem must_change_password = true
 * (ex.: senha inicial do seeder). Isenta as rotas de perfil/troca de senha
 * para não criar loop de redirect.
 *
 * `logout` estava na lista e nunca foi consultado: este middleware só roda no
 * grupo `admin` (routes/admin.php) e a rota `logout` vive fora dele, em
 * routes/web.php. A isenção era resto de uma intenção de aplicar o middleware
 * globalmente, que não é o que acontece. Sair continua funcionando com a senha
 * pendente, porque o middleware nunca chega a rodar ali.
 */
class EnsurePasswordChanged
{
    private const ALLOWED_ROUTES = ['admin.profile', 'admin.profile.password'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $routeName = $request->route()?->getName();
            if (! in_array($routeName, self::ALLOWED_ROUTES, true)) {
                return redirect()->route('admin.profile')
                    ->with('error', 'Por segurança, troque a senha antes de continuar.');
            }
        }

        return $next($request);
    }
}

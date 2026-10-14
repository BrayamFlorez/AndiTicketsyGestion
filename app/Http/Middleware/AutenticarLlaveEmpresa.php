<?php

namespace App\Http\Middleware;

use App\Exceptions\AgenteException;
use App\Services\Agente\LlaveAgenteService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentica la instalación del agente con la llave de la empresa
 * ("Authorization: Bearer agk_..."). Deja la empresa en $request->attributes->get('empresa').
 * La llave solo se usa durante la instalación; después el PC usa su propio token.
 */
class AutenticarLlaveEmpresa
{
    public function __construct(private readonly LlaveAgenteService $llaves) {}

    public function handle(Request $request, Closure $next): Response
    {
        $empresa = $this->llaves->empresaPorLlave($request->bearerToken())
            ?? throw AgenteException::llaveInvalida();

        $request->attributes->set('empresa', $empresa);

        return $next($request);
    }
}

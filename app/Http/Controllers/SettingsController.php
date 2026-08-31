<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $ajustesPorCategoria = Setting::orderBy('categoria')
            ->orderBy('clave')
            ->get()
            ->groupBy('categoria');

        return view('configuracion.ajustes.index', compact('ajustesPorCategoria'));
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $reglas = match ($setting->tipo) {
            'bool'   => ['valor' => ['required', 'boolean']],
            'int'    => ['valor' => ['required', 'integer']],
            'json'   => ['valor' => ['required', 'json']],
            default  => ['valor' => ['nullable', 'string', 'max:5000']],
        };

        $datos = $request->validate($reglas);

        // Para bool, el request llega como '1'/'0' o true/false
        $valor = $setting->tipo === 'bool'
            ? filter_var($datos['valor'], FILTER_VALIDATE_BOOLEAN)
            : $datos['valor'];

        Setting::set($setting->clave, $valor, $setting->tipo);

        return back()->with('exito', "Ajuste '{$setting->clave}' actualizado.");
    }
}
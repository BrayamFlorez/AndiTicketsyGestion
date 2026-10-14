<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirmarMantenimientoRequest;
use App\Http\Requests\SubirEvidenciaMantenimientoRequest;
use App\Models\Impresora;
use App\Models\ImpresoraMantenimiento;
use App\Models\ImpresoraMantenimientoEvidencia;
use App\Services\Impresoras\EvidenciaImagen;
use App\Services\Impresoras\FirmaDigital;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detalle de un mantenimiento: evidencia (antes / después), firma del técnico y del cliente,
 * y el reporte en PDF tamaño carta una vez finalizado.
 */
class ImpresoraMantenimientoReporteController extends Controller
{
    private const DISCO = 'local';

    public function show(Impresora $impresora, ImpresoraMantenimiento $mantenimiento): View
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        Gate::authorize('view', $impresora);

        $mantenimiento->load(['realizador:id,name', 'registrador:id,name', 'finalizador:id,name',
            'evidencias.subidor:id,name', 'consumiblesInstalados', 'lecturaContador']);
        $impresora->loadMissing(['empresa:id,nombre', 'sede:id,nombre', 'modelo']);

        return view('impresoras.mantenimientos.show', [
            'impresora'     => $impresora,
            'mantenimiento' => $mantenimiento,
            'puedeGestionar' => request()->user()->can('gestionarMantenimientos', $impresora),
        ]);
    }

    public function subirEvidencia(SubirEvidenciaMantenimientoRequest $request, Impresora $impresora, ImpresoraMantenimiento $mantenimiento, EvidenciaImagen $imagenes): RedirectResponse
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        $this->exigirPendiente($mantenimiento);

        $datos = $request->validated();
        $carpeta = $mantenimiento->carpetaArchivos() . '/evidencias';

        foreach ($request->file('fotos') as $foto) {
            $mantenimiento->evidencias()->create([
                'momento'     => $datos['momento'],
                'descripcion' => $datos['descripcion'] ?? null,
                'archivo'     => $imagenes->guardar($foto, $carpeta, self::DISCO),
                'subido_por'  => $request->user()->id,
            ]);
        }

        $cantidad = count($request->file('fotos'));

        return back()->with('exito', $cantidad === 1 ? 'Foto agregada.' : "{$cantidad} fotos agregadas.");
    }

    public function eliminarEvidencia(Request $request, Impresora $impresora, ImpresoraMantenimiento $mantenimiento, ImpresoraMantenimientoEvidencia $evidencia): RedirectResponse
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        abort_if($evidencia->mantenimiento_id !== $mantenimiento->id, 404);
        Gate::authorize('gestionarMantenimientos', $impresora);
        $this->exigirPendiente($mantenimiento);

        Storage::disk(self::DISCO)->delete($evidencia->archivo);
        $evidencia->delete();

        return back()->with('exito', 'Foto eliminada.');
    }

    public function firmar(FirmarMantenimientoRequest $request, Impresora $impresora, ImpresoraMantenimiento $mantenimiento, FirmaDigital $firmas): RedirectResponse
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        $this->exigirPendiente($mantenimiento);

        $datos = $request->validated();
        $carpeta = $mantenimiento->carpetaArchivos();

        DB::transaction(function () use ($mantenimiento, $datos, $carpeta, $firmas, $request) {
            $mantenimiento->forceFill([
                'firma_tecnico'     => $firmas->guardar($datos['firma_tecnico'], "{$carpeta}/firma-tecnico.png", self::DISCO),
                'firma_tecnico_at'  => now(),
                'firma_cliente'     => $firmas->guardar($datos['firma_cliente'], "{$carpeta}/firma-cliente.png", self::DISCO),
                'cliente_nombre'    => $datos['cliente_nombre'],
                'cliente_cargo'     => $datos['cliente_cargo'] ?? null,
                'cliente_documento' => $datos['cliente_documento'] ?? null,
                'firma_cliente_at'  => now(),
                'estado'            => ImpresoraMantenimiento::ESTADO_FINALIZADO,
                'finalizado_at'     => now(),
                'finalizado_por'    => $request->user()->id,
            ])->save();
        });

        return back()->with('exito', 'Mantenimiento firmado y finalizado. Ya puede descargar el reporte en PDF.');
    }

    /** Sirve la evidencia y las firmas (son archivos privados) */
    public function archivo(Impresora $impresora, ImpresoraMantenimiento $mantenimiento, string $archivo): Response
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        Gate::authorize('view', $impresora);

        $ruta = match (true) {
            $archivo === 'firma-tecnico'          => $mantenimiento->firma_tecnico,
            $archivo === 'firma-cliente'          => $mantenimiento->firma_cliente,
            (bool) preg_match('/^evidencia-(\d+)$/', $archivo, $m)
                => $mantenimiento->evidencias()->whereKey($m[1])->value('archivo'),
            default                               => null,
        };

        abort_unless($ruta && Storage::disk(self::DISCO)->exists($ruta), 404);

        return Storage::disk(self::DISCO)->response($ruta, null, ['Cache-Control' => 'private, max-age=86400']);
    }

    public function pdf(Request $request, Impresora $impresora, ImpresoraMantenimiento $mantenimiento): Response
    {
        $this->validarPertenencia($impresora, $mantenimiento);
        Gate::authorize('view', $impresora);
        abort_unless($mantenimiento->estaFinalizado(), 422, 'El reporte se genera cuando el mantenimiento está firmado.');

        $mantenimiento->load(['realizador:id,name', 'finalizador:id,name', 'evidencias', 'consumiblesInstalados', 'lecturaContador']);
        $impresora->loadMissing(['empresa:id,nombre', 'sede:id,nombre', 'modelo']);

        $pdf = Pdf::loadView('impresoras.mantenimientos.pdf', [
            'impresora'     => $impresora,
            'mantenimiento' => $mantenimiento,
            'empresa'       => config('reportes.empresa'),
            'logo'          => $this->logoEmpresa(),
            'imagen'        => fn (?string $ruta) => $this->dataUri($ruta),
            'generado'      => now(),
        ])->setPaper('letter', 'portrait')
          ->setOption('isFontSubsettingEnabled', true); // solo los caracteres usados: el PDF pesa ~10 veces menos

        $this->numerarPaginas($pdf);

        $nombre = Str::slug("mantenimiento-{$impresora->codigo}-{$mantenimiento->id}") . '.pdf';

        return $request->boolean('descargar') ? $pdf->download($nombre) : $pdf->stream($nombre);
    }

    // ─────────────────────────────────────────────────────────────────────

    private function validarPertenencia(Impresora $impresora, ImpresoraMantenimiento $mantenimiento): void
    {
        abort_if($mantenimiento->impresora_id !== $impresora->id, 404);
    }

    private function exigirPendiente(ImpresoraMantenimiento $mantenimiento): void
    {
        abort_if($mantenimiento->estaFinalizado(), 422, 'El mantenimiento ya está finalizado: la evidencia y las firmas no se pueden modificar.');
    }

    /** Las imágenes van embebidas: dompdf no necesita acceso a URLs ni a la carpeta storage */
    private function dataUri(?string $ruta): ?string
    {
        if (! $ruta || ! Storage::disk(self::DISCO)->exists($ruta)) {
            return null;
        }

        $mime = Storage::disk(self::DISCO)->mimeType($ruta) ?: 'image/jpeg';

        return "data:{$mime};base64," . base64_encode(Storage::disk(self::DISCO)->get($ruta));
    }

    /** "Página X de Y" en el pie de cada hoja (tamaño carta: 612 × 792 pt) */
    private function numerarPaginas(\Barryvdh\DomPDF\PDF $pdf): void
    {
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $fuente = $dompdf->getFontMetrics()->getFont('DejaVu Sans');

        $dompdf->getCanvas()->page_text(508, 763, 'Página {PAGE_NUM} de {PAGE_COUNT}', $fuente, 7, [0.42, 0.45, 0.5]);
    }

    private function logoEmpresa(): ?string
    {
        $ruta = config('reportes.empresa.logo');
        $archivo = $ruta ? public_path($ruta) : null;

        if (! $archivo || ! is_file($archivo)) {
            return null;
        }

        $mime = mime_content_type($archivo) ?: 'image/png';

        return "data:{$mime};base64," . base64_encode(file_get_contents($archivo));
    }
}

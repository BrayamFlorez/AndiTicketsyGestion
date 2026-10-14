<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirmarEquipoMantenimientoRequest;
use App\Http\Requests\SubirEvidenciaEquipoMantenimientoRequest;
use App\Models\Equipo;
use App\Models\EquipoMantenimiento;
use App\Models\EquipoMantenimientoEvidencia;
use App\Services\Impresoras\EvidenciaImagen;
use App\Services\Impresoras\FirmaDigital;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/** Fotos, firmas (cliente a la salida y técnico) y reporte PDF de un servicio a un equipo. */
class EquipoMantenimientoReporteController extends Controller
{
    private const DISCO = 'local';

    public function subirEvidencia(SubirEvidenciaEquipoMantenimientoRequest $request, Equipo $equipo, EquipoMantenimiento $mantenimiento, EvidenciaImagen $imagenes): RedirectResponse
    {
        $this->validarPertenencia($equipo, $mantenimiento);
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

        return back()->with('success', $cantidad === 1 ? 'Foto agregada.' : "{$cantidad} fotos agregadas.");
    }

    public function eliminarEvidencia(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento, EquipoMantenimientoEvidencia $evidencia): RedirectResponse
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        abort_if((int) $evidencia->mantenimiento_id !== (int) $mantenimiento->id, 404);
        abort_unless($request->user()->can('equipos.mantenimientos.gestionar') && $equipo->puedeSerGestionadoPor($request->user()), 403);
        $this->exigirPendiente($mantenimiento);

        Storage::disk(self::DISCO)->delete($evidencia->archivo);
        $evidencia->delete();

        return back()->with('success', 'Foto eliminada.');
    }

    public function firmar(FirmarEquipoMantenimientoRequest $request, Equipo $equipo, EquipoMantenimiento $mantenimiento, FirmaDigital $firmas): RedirectResponse
    {
        $this->validarPertenencia($equipo, $mantenimiento);
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
                'hora_fin'          => $mantenimiento->hora_fin ?? now()->format('H:i'),
                'estado'            => EquipoMantenimiento::ESTADO_FINALIZADO,
                'finalizado_at'     => now(),
                'finalizado_por'    => $request->user()->id,
            ])->save();
        });

        return back()->with('success', 'Servicio firmado y finalizado. Ya puede descargar el reporte en PDF.');
    }

    /** Fotos y firmas: archivos privados que se sirven validando el acceso */
    public function archivo(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento, string $archivo): Response
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        $this->autorizarVista($request, $equipo);

        $ruta = match (true) {
            $archivo === 'firma-tecnico' => $mantenimiento->firma_tecnico,
            $archivo === 'firma-cliente' => $mantenimiento->firma_cliente,
            (bool) preg_match('/^evidencia-(\d+)$/', $archivo, $m)
                => $mantenimiento->evidencias()->whereKey($m[1])->value('archivo'),
            default => null,
        };

        abort_unless($ruta && Storage::disk(self::DISCO)->exists($ruta), 404);

        return Storage::disk(self::DISCO)->response($ruta, null, ['Cache-Control' => 'private, max-age=86400']);
    }

    public function pdf(Request $request, Equipo $equipo, EquipoMantenimiento $mantenimiento): Response
    {
        $this->validarPertenencia($equipo, $mantenimiento);
        $this->autorizarVista($request, $equipo);
        abort_unless($mantenimiento->estaFinalizado(), 422, 'El reporte se genera cuando el servicio está firmado.');

        $mantenimiento->load(['tecnico:id,name', 'registrador:id,name,email', 'finalizador:id,name,email', 'ticket:id', 'unidades', 'partes', 'evidencias']);
        $equipo->loadMissing(['empresa', 'sede', 'responsable:id,name']);

        $pdf = Pdf::loadView('equipos.mantenimientos.pdf', [
            'equipo'        => $equipo,
            'mantenimiento' => $mantenimiento,
            'empresa'       => config('reportes.empresa'),
            'formato'       => config('reportes.equipos'),
            'logo'          => $this->logoEmpresa(),
            'imagen'        => fn (?string $ruta) => $this->dataUri($ruta),
            'generado'      => now(),
        ])->setPaper('letter', 'portrait')
          ->setOption('isFontSubsettingEnabled', true);

        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $dompdf->getCanvas()->page_text(520, 770, 'Página {PAGE_NUM} de {PAGE_COUNT}',
            $dompdf->getFontMetrics()->getFont('DejaVu Sans'), 6.5, [0.42, 0.45, 0.5]);

        $nombre = Str::slug("servicio-{$mantenimiento->numero_orden}-{$equipo->nombre}") . '.pdf';

        return $request->boolean('descargar') ? $pdf->download($nombre) : $pdf->stream($nombre);
    }

    // ─────────────────────────────────────────────────────────────────────

    private function validarPertenencia(Equipo $equipo, EquipoMantenimiento $mantenimiento): void
    {
        abort_if((int) $mantenimiento->equipo_id !== (int) $equipo->id, 404);
    }

    private function exigirPendiente(EquipoMantenimiento $mantenimiento): void
    {
        abort_if($mantenimiento->estaFinalizado(), 422, 'El servicio ya está finalizado: las fotos y las firmas no se pueden modificar.');
    }

    private function autorizarVista(Request $request, Equipo $equipo): void
    {
        abort_unless($request->user()->can('equipos.mantenimientos.ver') && $equipo->puedeSerGestionadoPor($request->user()), 403);
    }

    private function dataUri(?string $ruta): ?string
    {
        if (! $ruta || ! Storage::disk(self::DISCO)->exists($ruta)) {
            return null;
        }

        $mime = Storage::disk(self::DISCO)->mimeType($ruta) ?: 'image/jpeg';

        return "data:{$mime};base64," . base64_encode(Storage::disk(self::DISCO)->get($ruta));
    }

    private function logoEmpresa(): ?string
    {
        $ruta = config('reportes.empresa.logo');
        $archivo = $ruta ? public_path($ruta) : null;

        if (! $archivo || ! is_file($archivo)) {
            return null;
        }

        return 'data:' . (mime_content_type($archivo) ?: 'image/png') . ';base64,' . base64_encode(file_get_contents($archivo));
    }
}

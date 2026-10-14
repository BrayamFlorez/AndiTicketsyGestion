{{-- resources/views/tickets/partials/form_usuario.blade.php --}}

@php
    $vinculoInicial ??= null;
    $activos ??= collect();
    $vinculoElegido = old('vinculo', $vinculoInicial['valor'] ?? '');
@endphp

<form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
    @csrf

    <div class="form-card-header">
        <h3>Información del ticket</h3>
        <p>Sé claro y específico — esto ayuda al técnico a resolver más rápido</p>
    </div>

    <div class="form-card-body">

        {{-- Título --}}
        <div class="form-section">
            <div class="form-group">
                <label class="form-label" for="titulo">
                    Título <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    value="{{ old('titulo') }}"
                    placeholder="Ej: No puedo imprimir desde mi equipo"
                    maxlength="150"
                    required
                    class="form-input @error('titulo') is-invalid @enderror">
                @error('titulo')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Categoría + Prioridad --}}
        <div class="form-section">
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label" for="categoria">
                        Categoría <span class="required">*</span>
                    </label>
                    <select id="categoria" name="categoria" required
                            class="form-select @error('categoria') is-invalid @enderror">
                        <option value="">— Selecciona —</option>
                        @foreach (\App\Models\Ticket::CATEGORIAS as $key => $label)
                            <option value="{{ $key }}" @selected(old('categoria', $vinculoInicial['categoria'] ?? null) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categoria')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="prioridad">
                        Prioridad <span class="required">*</span>
                    </label>
                    <select id="prioridad" name="prioridad" required
                            class="form-select @error('prioridad') is-invalid @enderror">
                        @foreach (\App\Models\Ticket::PRIORIDADES as $key => $label)
                            <option value="{{ $key }}" @selected(old('prioridad', 'media') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="form-help">Usa "Crítica" solo si tu trabajo está completamente bloqueado.</p>
                    @error('prioridad')<p class="form-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Equipo o impresora con el problema (queda en su historial) --}}
        @if ($activos->isNotEmpty())
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label" for="vinculo">Equipo relacionado (opcional)</label>
                    <select id="vinculo" name="vinculo" class="form-select @error('vinculo') is-invalid @enderror">
                        <option value="">— Ninguno —</option>
                        @foreach (['equipo' => 'Equipos', 'impresora' => 'Impresoras'] as $tipo => $grupo)
                            @php $delTipo = $activos->where('tipo', $tipo); @endphp
                            @if ($delTipo->isNotEmpty())
                                <optgroup label="{{ $grupo }}">
                                    @foreach ($delTipo as $activo)
                                        <option value="{{ $activo['valor'] }}" @selected($vinculoElegido === $activo['valor'])>
                                            {{ $activo['nombre'] }}{{ $activo['detalle'] ? ' — ' . $activo['detalle'] : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <p class="form-help">Si el problema es de un equipo o impresora puntual, selecciónelo.</p>
                    @error('vinculo')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        {{-- Descripción --}}
        <div class="form-section">
            <div class="form-group">
                <label class="form-label" for="descripcion">
                    Descripción <span class="required">*</span>
                </label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    rows="5"
                    placeholder="Describe qué pasó, cuándo empezó y qué has intentado hacer para resolverlo."
                    required
                    class="form-textarea @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                @error('descripcion')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Foto adjunta --}}
        <div class="form-section">
            <div class="form-group" x-data="{ nombre: '' }">
                <label class="form-label">Foto adjunta (opcional)</label>
                <label class="form-file-wrap @error('foto_solicitud') is-invalid @enderror">
                    <div class="form-file-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <div class="form-file-text" x-text="nombre || 'Seleccionar imagen'"></div>
                        <div class="form-file-hint">JPG, PNG — máx. 5MB</div>
                    </div>
                    <input
                        type="file"
                        name="foto_solicitud"
                        accept="image/*"
                        @change="nombre = $event.target.files[0]?.name ?? ''">
                </label>
                @error('foto_solicitud')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    <div class="form-card-footer">
        <a href="{{ route('dashboard') }}" class="db-btn ghost lg">Cancelar</a>
        <button type="submit" class="db-btn solid lg">
            <i class="fas fa-paper-plane"></i> Enviar ticket
        </button>
    </div>

</form>
{{--
    Cuadrícula interactiva de estaciones de cómputo (PC-01 a PC-20) · MetalNet.
    Parámetros:
      $lab      Laboratorio del catálogo (App\Support\CatalogoLaboratorios)
      $variant  'full' (tarjetas de estación con monitor + teclado + gabinete) | 'mini' (estaciones compactas con icono visual)
      $picker   true para usar la cuadrícula como selector interactivo
--}}
@php
    $variant = $variant ?? 'full';
    $picker = $picker ?? false;
    $labData = $lab ?? ['id' => '0', 'codigo' => 'LAB', 'pcs' => $pcs ?? []];
    $pcsList = $labData['pcs'] ?? $pcs ?? [];
@endphp
<div class="pc-grid pc-grid--{{ $variant }}"
     data-lab-grid="{{ $labData['id'] ?? '0' }}"
     @if($picker) data-pc-picker="1" @endif>
    @foreach($pcsList as $pc)
        @php
            $svgAsset = match($pc['estado']) {
                'disponible' => asset('images/equipos/pc-libre.svg'),
                'mantenimiento' => asset('images/equipos/pc-no-disponible.svg'),
                default => asset('images/equipos/pc-ocupada.svg'),
            };
            $estadoLabelUi = match($pc['estado']) {
                'disponible' => 'Disponible',
                'mantenimiento' => 'No disponible',
                default => 'Ocupado',
            };
        @endphp
        <button type="button"
                class="pc-tile pc-tile--{{ $variant }}"
                data-pc-id="{{ $pc['id'] }}"
                data-estado="{{ $pc['estado'] }}"
                aria-label="{{ $pc['codigo'] }} · {{ $estadoLabelUi }} · {{ $labData['codigo'] ?? 'LAB' }}">
            @if($variant === 'mini')
                <img src="{{ $svgAsset }}"
                     alt="{{ $pc['codigo'] }}"
                     class="w-7 h-5 object-contain pointer-events-none select-none pc-tile__img"
                     data-img-libre="{{ asset('images/equipos/pc-libre.svg') }}"
                     data-img-ocupado="{{ asset('images/equipos/pc-ocupada.svg') }}"
                     data-img-mantenimiento="{{ asset('images/equipos/pc-no-disponible.svg') }}">
                <span class="pc-tile__num">{{ $pc['numero'] }}</span>
            @else
                <span class="pc-tile__top">
                    <span class="pc-tile__code">{{ $pc['codigo'] }}</span>
                    <span class="pc-tile__led"></span>
                </span>
                <img src="{{ $svgAsset }}"
                     alt="{{ $pc['codigo'] }}"
                     class="w-14 h-11 object-contain pointer-events-none select-none pc-tile__img"
                     data-img-libre="{{ asset('images/equipos/pc-libre.svg') }}"
                     data-img-ocupado="{{ asset('images/equipos/pc-ocupada.svg') }}"
                     data-img-mantenimiento="{{ asset('images/equipos/pc-no-disponible.svg') }}">
                <span class="pc-tile__status">{{ $estadoLabelUi }}</span>
            @endif
        </button>
    @endforeach
</div>

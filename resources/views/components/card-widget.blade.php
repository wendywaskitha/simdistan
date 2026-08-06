@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'subtitle' => null
])

<div class="card custom-card border-0 p-4 h-100">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted fs-6 d-block mb-1">{{ $title }}</span>
            <h3 class="fw-bold text-dark mb-0">{{ $value }}</h3>
        </div>
        <div class="bg-{{ $color }} bg-opacity-10 text-{{ $color }} p-3 rounded-3">
            <i class="bi {{ $icon }} fs-2"></i>
        </div>
    </div>
    @if($subtitle)
        <hr class="text-muted opacity-25 my-3">
        <small class="text-muted">{{ $subtitle }}</small>
    @endif
</div>

@props([
    'items' => []
])

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 shadow-sm border border-light mb-0">
        <li class="breadcrumb-item d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none d-flex align-items-center gap-1">
                <i class="bi bi-house-door-fill text-success"></i>
                <span>Beranda</span>
            </a>
        </li>
        @foreach($items as $item)
            @if(isset($item['url']) && $item['url'])
                <li class="breadcrumb-item d-flex align-items-center">
                    <a href="{{ $item['url'] }}" class="text-secondary text-decoration-none">{{ $item['label'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active text-dark fw-medium d-flex align-items-center" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>

@props([
    'headers' => [],
    'id' => 'datatable'
])

<div class="table-responsive bg-white rounded-3 border border-light p-3 shadow-sm">
    <table id="{{ $id }}" {{ $attributes->merge(['class' => 'table table-hover align-middle w-100']) }}>
        <thead class="table-light text-secondary uppercase fs-7">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="py-3 px-4">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-dark">
            {{ $slot }}
        </tbody>
    </table>
</div>

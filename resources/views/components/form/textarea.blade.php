@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'rows' => 3,
    'required' => false,
    'disabled' => false
])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold text-secondary">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <textarea 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

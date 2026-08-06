@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
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

    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'form-select ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >
        @if($placeholder)
            <option value="" disabled {{ is_null(old($name, $selected)) ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $labelOption)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $labelOption }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

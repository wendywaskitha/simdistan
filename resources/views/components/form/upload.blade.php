@props([
    'name',
    'label' => null,
    'required' => false,
    'accept' => 'image/*',
    'helperText' => 'Pilih file gambar (Max. 2MB)',
    'preview' => null
])

<div class="mb-3" x-data="fileUpload('{{ $preview }}')">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-semibold text-secondary">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="d-flex flex-column gap-2">
        <!-- Preview Container -->
        <template x-if="imageUrl">
            <div class="position-relative border rounded-3 p-1 bg-light" style="width: 150px; height: 150px; overflow: hidden;">
                <img :src="imageUrl" class="w-100 h-100 object-fit-cover rounded-2" alt="Preview">
                <button type="button" @click="clearFile" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1" style="line-height: 1;">
                    <i class="bi bi-x fs-6"></i>
                </button>
            </div>
        </template>
        
        <template x-if="fileName && !imageUrl">
            <div class="d-flex align-items-center justify-content-between border rounded-3 p-2 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                    <span x-text="fileName" class="small text-truncate" style="max-width: 200px;"></span>
                </div>
                <button type="button" @click="clearFile" class="btn btn-link text-danger p-0">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </template>

        <!-- Input File -->
        <input 
            type="file" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            accept="{{ $accept }}"
            @change="fileChosen"
            ref="fileInput"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
        >
        
        @if($helperText)
            <div class="form-text">{{ $helperText }}</div>
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileUpload', (initialPreview) => ({
            imageUrl: initialPreview || null,
            fileName: '',
            fileChosen(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.fileName = file.name;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.imageUrl = null;
                }
            },
            clearFile() {
                this.imageUrl = null;
                this.fileName = '';
                this.$refs.fileInput.value = '';
            }
        }));
    });
</script>

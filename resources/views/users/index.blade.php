@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Manajemen Pengguna']
]" />

<div class="card custom-card border-0 shadow-sm p-4" style="border-radius:16px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-person-gear me-2 text-primary"></i>Manajemen Pengguna</h5>
            <p class="text-muted small mb-0">Kelola akun operator bidang, kepala dinas, dan super admin dalam sistem.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary rounded-3 px-4 py-2 shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-table :headers="['No', 'Nama Pengguna', 'Email', 'Role / Peran', 'Aksi']" id="usersTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'roles_list', name: 'roles.name', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '12%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

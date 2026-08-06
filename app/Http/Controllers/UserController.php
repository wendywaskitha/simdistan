<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna (DataTables)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('roles')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('roles_list', function($row) {
                    return $row->roles->pluck('name')->map(function($role) {
                        return '<span class="badge bg-primary px-2 py-1 rounded-pill">' . $role . '</span>';
                    })->implode(' ');
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('users.edit', $row->id);
                    $deleteUrl = route('users.destroy', $row->id);
                    // Cegah menghapus diri sendiri
                    $deleteButton = auth()->id() !== $row->id 
                        ? '<form action="'.$deleteUrl.'" method="POST" class="d-inline form-delete">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="button" class="btn btn-sm btn-danger btn-delete-trigger" title="Hapus"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    // Impersonate button (hanya jika target bukan diri sendiri)
                    $impersonateButton = auth()->id() !== $row->id
                        ? '<a href="'.route('users.impersonate', $row->id).'" class="btn btn-sm btn-warning text-white" title="Masuk sebagai user ini"><i class="bi bi-box-arrow-in-right"></i></a>'
                        : '';
                    return '
                        <div class="d-flex gap-2">
                            '.$impersonateButton.'
                            <a href="'.$editUrl.'" class="btn btn-sm btn-info text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            '.$deleteButton.'
                        </div>
                    ';
                })
                ->rawColumns(['roles_list', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Form tambah pengguna baru
     */
    public function create(): View
    {
        $roles = Role::all()->pluck('name', 'id')->toArray();
        return view('users.create', compact('roles'));
    }

    /**
     * Simpan pengguna baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles'    => ['required', 'array']
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit pengguna
     */
    public function edit(int $id): View
    {
        $user = User::findOrFail($id);
        $roles = Role::all()->pluck('name', 'id')->toArray();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update data pengguna
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'roles' => ['required', 'array']
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Mulai Impersonate (Masuk sebagai user lain)
     */
    public function impersonate(int $id): RedirectResponse
    {
        $userToImpersonate = User::findOrFail($id);

        // Simpan id admin asli di session
        session(['impersonator_id' => auth()->id()]);

        // Login sebagai user target
        auth()->login($userToImpersonate);

        return redirect()->route('dashboard')
            ->with('success', 'Anda sekarang masuk sebagai ' . $userToImpersonate->name);
    }

    /**
     * Selesai Impersonate (Kembali ke admin asli)
     */
    public function leaveImpersonate(): RedirectResponse
    {
        if (!session()->has('impersonator_id')) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $originalAdminId = session()->pull('impersonator_id');
        $originalAdmin = User::findOrFail($originalAdminId);

        // Login kembali sebagai admin asli
        auth()->login($originalAdmin);

        return redirect()->route('users.index')
            ->with('success', 'Kembali ke sesi admin asli.');
    }
}

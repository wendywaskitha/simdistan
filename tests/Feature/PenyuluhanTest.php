<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bpp;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Gapoktan;
use App\Models\KelompokTani;
use App\Models\Penyuluh;
use App\Models\Petani;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyuluhanTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles, permissions, dan data wilayah awal
        $this->seed(RolePermissionSeeder::class);
        $this->seed(MasterDataSeeder::class);

        // Ambil super admin untuk login
        $this->adminUser = User::where('email', 'superadmin@simdistan.test')->first();
    }

    public function test_super_admin_can_access_penyuluh_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('penyuluhs.index'));
        $response->assertStatus(200);
    }

    public function test_super_admin_can_create_penyuluh(): void
    {
        $bpp = Bpp::first();

        $response = $this->actingAs($this->adminUser)->post(route('penyuluhs.store'), [
            'nama' => 'Penyuluh Uji Coba',
            'nip' => '199508152026071001',
            'telepon' => '081234567890',
            'bpp_id' => $bpp->id
        ]);

        $response->assertRedirect(route('penyuluhs.index'));
        $this->assertDatabaseHas('penyuluhs', ['nama' => 'Penyuluh Uji Coba']);
    }

    public function test_super_admin_can_create_gapoktan_and_poktan(): void
    {
        $kecamatan = Kecamatan::first();
        $desa = Desa::first();

        // 1. Create Gapoktan
        $responseGap = $this->actingAs($this->adminUser)->post(route('gapoktans.store'), [
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Gapoktan Baru Uji',
            'ketua' => 'Bpk. Ahmad'
        ]);
        $responseGap->assertRedirect(route('gapoktans.index'));
        $this->assertDatabaseHas('gapoktans', ['nama' => 'Gapoktan Baru Uji']);
        $gapoktan = Gapoktan::where('nama', 'Gapoktan Baru Uji')->first();

        // 2. Create Poktan
        $responsePok = $this->actingAs($this->adminUser)->post(route('kelompok-tanis.store'), [
            'desa_id' => $desa->id,
            'gapoktan_id' => $gapoktan->id,
            'nama' => 'Poktan Baru Uji',
            'ketua' => 'Bpk. Budi'
        ]);
        $responsePok->assertRedirect(route('kelompok-tanis.index'));
        $this->assertDatabaseHas('kelompok_tanis', ['nama' => 'Poktan Baru Uji']);
    }

    public function test_super_admin_can_create_petani(): void
    {
        $poktan = KelompokTani::first();

        $response = $this->actingAs($this->adminUser)->post(route('petanis.store'), [
            'kelompok_tani_id' => $poktan->id,
            'nama' => 'Petani Sukses Uji',
            'nik' => '7402031508920003',
            'telepon' => '081234567899',
            'alamat' => 'Desa Sukamaju'
        ]);

        $response->assertRedirect(route('petanis.index'));
        $this->assertDatabaseHas('petanis', ['nik' => '7402031508920003']);
    }
}

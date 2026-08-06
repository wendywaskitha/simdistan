<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bidang;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Bpp;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Ambil super admin untuk login
        $this->adminUser = User::where('email', 'superadmin@simdistan.test')->first();
    }

    public function test_super_admin_can_access_bidang_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('bidangs.index'));
        $response->assertStatus(200);
    }

    public function test_super_admin_can_create_bidang(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('bidangs.store'), [
            'nama' => 'Bidang Uji Coba Pertanian'
        ]);

        $response->assertRedirect(route('bidangs.index'));
        $this->assertDatabaseHas('bidangs', ['nama' => 'Bidang Uji Coba Pertanian']);
    }

    public function test_super_admin_can_create_kecamatan_and_desa(): void
    {
        // 1. Create Kecamatan
        $responseKec = $this->actingAs($this->adminUser)->post(route('kecamatans.store'), [
            'nama' => 'Kecamatan Uji'
        ]);
        $responseKec->assertRedirect(route('kecamatans.index'));
        $kecamatan = Kecamatan::where('nama', 'Kecamatan Uji')->first();
        $this->assertNotNull($kecamatan);

        // 2. Create Desa
        $responseDesa = $this->actingAs($this->adminUser)->post(route('desas.store'), [
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Desa Uji'
        ]);
        $responseDesa->assertRedirect(route('desas.index'));
        $this->assertDatabaseHas('desas', [
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Desa Uji'
        ]);
    }
}

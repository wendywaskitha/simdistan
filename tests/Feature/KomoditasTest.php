<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KategoriKomoditas;
use App\Models\Komoditas;
use App\Models\Varietas;
use App\Models\Satuan;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KomoditasTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles, permissions, dan data awal
        $this->seed(RolePermissionSeeder::class);
        $this->seed(MasterDataSeeder::class);

        // Ambil super admin untuk login
        $this->adminUser = User::where('email', 'superadmin@simdistan.test')->first();
    }

    public function test_super_admin_can_access_kategori_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('kategori-komoditas.index'));
        $response->assertStatus(200);
    }

    public function test_super_admin_can_create_kategori(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('kategori-komoditas.store'), [
            'nama' => 'Peternakan Uji'
        ]);

        $response->assertRedirect(route('kategori-komoditas.index'));
        $this->assertDatabaseHas('kategori_komoditas', ['nama' => 'Peternakan Uji']);
    }

    public function test_super_admin_can_create_komoditas_and_varietas(): void
    {
        $kategori = KategoriKomoditas::first();

        // 1. Create Komoditas
        $responseKom = $this->actingAs($this->adminUser)->post(route('komoditas.store'), [
            'kategori_komoditas_id' => $kategori->id,
            'nama' => 'Ubi Jalar Uji'
        ]);
        $responseKom->assertRedirect(route('komoditas.index'));
        $this->assertDatabaseHas('komoditas', ['nama' => 'Ubi Jalar Uji']);
        $komoditas = Komoditas::where('nama', 'Ubi Jalar Uji')->first();

        // 2. Create Varietas
        $responseVar = $this->actingAs($this->adminUser)->post(route('varietas.store'), [
            'komoditas_id' => $komoditas->id,
            'nama' => 'Varietas Madu Uji'
        ]);
        $responseVar->assertRedirect(route('varietas.index'));
        $this->assertDatabaseHas('varietas', ['nama' => 'Varietas Madu Uji']);
    }

    public function test_super_admin_can_create_satuan(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('satuans.store'), [
            'nama' => 'Ikat'
        ]);

        $response->assertRedirect(route('satuans.index'));
        $this->assertDatabaseHas('satuans', ['nama' => 'Ikat']);
    }
}

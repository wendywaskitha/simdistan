<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kecamatan;
use App\Models\KategoriKomoditas;
use App\Models\Komoditas;
use App\Models\Satuan;
use App\Models\LaporanProduksi;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanProduksiTest extends TestCase
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

    public function test_super_admin_can_access_tanaman_pangan_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('tanaman-pangan.index'));
        $response->assertStatus(200);
    }

    public function test_super_admin_can_create_laporan_tanaman_pangan_with_weekly_nested_form(): void
    {
        $kategoriPangan = KategoriKomoditas::where('nama', 'Tanaman Pangan')->first();
        $kecamatan = Kecamatan::first();
        $komoditas = Komoditas::where('kategori_komoditas_id', $kategoriPangan->id)->first();
        $satuan = Satuan::first();

        $response = $this->actingAs($this->adminUser)->post(route('tanaman-pangan.store'), [
            'kategori_komoditas_id' => $kategoriPangan->id,
            'kecamatan_id' => $kecamatan->id,
            'satuan_id' => $satuan->id,
            'bulan' => 5,
            'tahun' => 2026,
            'komoditas' => [
                $komoditas->id => [
                    'mingguans' => [
                        ['luas_lahan' => 10.0, 'luas_tanam' => 10.0, 'luas_panen' => 5.0, 'produktivitas' => 5.0, 'produksi' => 25.0],
                        ['luas_lahan' => 12.0, 'luas_tanam' => 12.0, 'luas_panen' => 8.0, 'produktivitas' => 5.0, 'produksi' => 40.0],
                        ['luas_lahan' => 15.0, 'luas_tanam' => 15.0, 'luas_panen' => 10.0, 'produktivitas' => 5.0, 'produksi' => 50.0],
                        ['luas_lahan' => 8.0, 'luas_tanam' => 8.0, 'luas_panen' => 4.0, 'produktivitas' => 5.0, 'produksi' => 20.0],
                    ]
                ]
            ]
        ]);

        $response->assertRedirect();
        
        // Cek total akumulasi di database
        $this->assertDatabaseHas('laporan_produksis', [
            'kecamatan_id' => $kecamatan->id,
            'komoditas_id' => $komoditas->id,
            'bulan' => 5,
            'tahun' => 2026,
            'luas_lahan' => 45.00,
            'luas_tanam' => 45.00,
            'luas_panen' => 27.00,
            'produksi' => 135.00,
        ]);

        // Cek rincian mingguan di database
        $laporan = LaporanProduksi::where('tahun', 2026)->first();
        $this->assertEquals(4, $laporan->mingguans()->count());
    }

    public function test_super_admin_can_create_laporan_hortikultura_directly(): void
    {
        $kategoriHorti = KategoriKomoditas::where('nama', 'Hortikultura')->first();
        $kecamatan = Kecamatan::first();
        $komoditas = Komoditas::where('kategori_komoditas_id', $kategoriHorti->id)->first();
        $satuan = Satuan::first();

        $response = $this->actingAs($this->adminUser)->post(route('hortikultura.store'), [
            'kategori_komoditas_id' => $kategoriHorti->id,
            'kecamatan_id' => $kecamatan->id,
            'satuan_id' => $satuan->id,
            'bulan' => 5,
            'tahun' => 2026,
            'komoditas' => [
                $komoditas->id => [
                    'luas_tanam' => 50.0,
                    'luas_panen' => 40.0,
                    'produksi' => 200.0,
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('laporan_produksis', [
            'kategori_komoditas_id' => $kategoriHorti->id,
            'luas_tanam' => 50.00,
            'luas_panen' => 40.00,
            'produksi' => 200.00,
            'produktivitas' => 5.00
        ]);
    }

    public function test_super_admin_can_access_kelola_matrix(): void
    {
        $komoditas = Komoditas::first();

        $response = $this->actingAs($this->adminUser)->get(route('tanaman-pangan.kelola', [
            'komoditas_id' => $komoditas->id,
            'tahun' => 2026
        ]));

        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_input_mingguan(): void
    {
        $kecamatan = Kecamatan::first();
        $komoditas = Komoditas::first();

        $response = $this->actingAs($this->adminUser)->get(route('tanaman-pangan.input-mingguan', [
            'kecamatan_id' => $kecamatan->id,
            'komoditas_id' => $komoditas->id,
            'tahun' => 2026,
            'bulan' => 5
        ]));

        $response->assertStatus(200);
    }

    public function test_super_admin_can_save_weekly_reports_via_simpan_mingguan(): void
    {
        $kecamatan = Kecamatan::first();
        $komoditas = Komoditas::first();
        $satuan = Satuan::first();

        $response = $this->actingAs($this->adminUser)->post(route('tanaman-pangan.simpan-mingguan'), [
            'kecamatan_id' => $kecamatan->id,
            'komoditas_id' => $komoditas->id,
            'tahun' => 2026,
            'bulan' => 5,
            'satuan_id' => $satuan->id,
            'mingguans' => [
                ['luas_lahan' => 10.0, 'luas_tanam' => 10.0, 'luas_panen' => 5.0, 'produktivitas' => 5.0, 'produksi' => 25.0],
                ['luas_lahan' => 10.0, 'luas_tanam' => 10.0, 'luas_panen' => 5.0, 'produktivitas' => 5.0, 'produksi' => 25.0],
                ['luas_lahan' => 10.0, 'luas_tanam' => 10.0, 'luas_panen' => 5.0, 'produktivitas' => 5.0, 'produksi' => 25.0],
                ['luas_lahan' => 10.0, 'luas_tanam' => 10.0, 'luas_panen' => 5.0, 'produktivitas' => 5.0, 'produksi' => 25.0],
            ]
        ]);

        $response->assertRedirect(route('tanaman-pangan.kelola', [
            'komoditas_id' => $komoditas->id,
            'tahun' => 2026
        ]));

        $this->assertDatabaseHas('laporan_produksis', [
            'kecamatan_id' => $kecamatan->id,
            'komoditas_id' => $komoditas->id,
            'tahun' => 2026,
            'bulan' => 5,
            'luas_lahan' => 40.00,
            'luas_tanam' => 40.00,
            'luas_panen' => 20.00,
            'produksi' => 100.00,
        ]);
    }

    public function test_super_admin_can_fetch_chart_data(): void
    {
        $komoditas = Komoditas::first();

        $response = $this->actingAs($this->adminUser)->get(route('tanaman-pangan.data-grafik', [
            'komoditas_id' => $komoditas->id
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'years', 'luas_lahan', 'luas_tanam', 'luas_panen', 'produksi',
            'months', 'bulanan_lahan', 'bulanan_tanam', 'bulanan_panen', 'bulanan_produksi'
        ]);
    }
}

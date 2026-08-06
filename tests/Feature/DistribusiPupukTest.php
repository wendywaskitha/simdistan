<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kecamatan;
use App\Models\TokoPupuk;
use App\Models\JenisPupuk;
use App\Models\LaporanPupuk;
use App\Models\PengalihanPupuk;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistribusiPupukTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(MasterDataSeeder::class);

        $this->adminUser = User::where('email', 'superadmin@simdistan.test')->first();
    }

    public function test_can_access_distribution_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('distribusi-pupuk.index'));
        $response->assertStatus(200);
    }

    public function test_can_store_monthly_distribution_report(): void
    {
        $toko = TokoPupuk::first();
        $kecamatan = $toko->kecamatans()->first();
        $jenis = JenisPupuk::first();
        $satuan = \App\Models\Satuan::first();

        $response = $this->actingAs($this->adminUser)->post(route('distribusi-pupuk.simpan-bulanan'), [
            'toko_pupuk_id' => $toko->id,
            'satuan_id' => $satuan->id,
            'bulan' => 6,
            'tahun' => 2026,
            'data' => [
                $kecamatan->id => [
                    $jenis->id => [
                        'penebusan' => 60.0
                    ]
                ]
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('laporan_pupuks', [
            'toko_pupuk_id' => $toko->id,
            'satuan_id' => $satuan->id,
            'bulan' => 6,
            'tahun' => 2026
        ]);

        $this->assertDatabaseHas('laporan_pupuk_details', [
            'kecamatan_id' => $kecamatan->id,
            'jenis_pupuk_id' => $jenis->id,
            'penebusan' => 60.00
        ]);
    }

    public function test_can_save_quota_reallocation_under_75_percent_rule(): void
    {
        $toko = TokoPupuk::first();
        $kecamatans = Kecamatan::all();
        $kecAsal = $kecamatans[0];
        $kecTujuan = $kecamatans[1];
        
        // Ensure both subdistricts are mapped to the store
        $toko->kecamatans()->sync([$kecAsal->id, $kecTujuan->id]);
        
        $jenis = JenisPupuk::first();
        $satuan = \App\Models\Satuan::first();

        // Create annual quotas
        \App\Models\KuotaTahunanPupuk::create([
            'tahun' => 2026,
            'kecamatan_id' => $kecAsal->id,
            'jenis_pupuk_id' => $jenis->id,
            'jumlah' => 1200.00
        ]);
        \App\Models\KuotaTahunanPupuk::create([
            'tahun' => 2026,
            'kecamatan_id' => $kecTujuan->id,
            'jenis_pupuk_id' => $jenis->id,
            'jumlah' => 1200.00
        ]);

        // Create initial monthly reports
        $laporan = LaporanPupuk::create([
            'toko_pupuk_id' => $toko->id,
            'satuan_id' => $satuan->id,
            'bulan' => 6,
            'tahun' => 2026
        ]);

        // Asal: 60 redemption. Cumulative quota share for month 6 = (6/12)*1200 = 600. 60/600 = 10% (Below 75%)
        $laporan->details()->create([
            'kecamatan_id' => $kecAsal->id,
            'jenis_pupuk_id' => $jenis->id,
            'penebusan' => 60.0
        ]);

        // Tujuan: 480 redemption. Cumulative quota share for month 6 = (6/12)*1200 = 600. 480/600 = 80% (Above 75%)
        $laporan->details()->create([
            'kecamatan_id' => $kecTujuan->id,
            'jenis_pupuk_id' => $jenis->id,
            'penebusan' => 480.0
        ]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('sk_relokasi_1.pdf', 500);

        $response = $this->actingAs($this->adminUser)->post(route('distribusi-pupuk.pengalihan.store'), [
            'tahun' => 2026,
            'bulan' => 6,
            'jenis_pupuk_id' => $jenis->id,
            'kecamatan_asal_id' => $kecAsal->id,
            'kecamatan_tujuan_id' => $kecTujuan->id,
            'jumlah' => 20.0,
            'nama_sk' => 'SK Relokasi 1',
            'bukti_sk' => $file,
            'keterangan' => 'Pengalihan kuota pupuk karena realisasi rendah'
        ]);

        $response->assertRedirect(route('distribusi-pupuk.pengalihan.index'));

        $this->assertDatabaseHas('pengalihan_pupuks', [
            'tahun' => 2026,
            'bulan' => 6,
            'jenis_pupuk_id' => $jenis->id,
            'kecamatan_asal_id' => $kecAsal->id,
            'kecamatan_tujuan_id' => $kecTujuan->id,
            'jumlah' => 20.00,
            'nama_sk' => 'SK Relokasi 1'
        ]);
    }

    public function test_cannot_reallocate_if_source_above_75_percent(): void
    {
        $toko = TokoPupuk::first();
        $kecamatans = Kecamatan::all();
        $kecAsal = $kecamatans[0];
        $kecTujuan = $kecamatans[1];
        
        $toko->kecamatans()->sync([$kecAsal->id, $kecTujuan->id]);
        $jenis = JenisPupuk::first();
        $satuan = \App\Models\Satuan::first();

        // Create annual quotas
        \App\Models\KuotaTahunanPupuk::create([
            'tahun' => 2026,
            'kecamatan_id' => $kecAsal->id,
            'jenis_pupuk_id' => $jenis->id,
            'jumlah' => 1200.00
        ]);
        \App\Models\KuotaTahunanPupuk::create([
            'tahun' => 2026,
            'kecamatan_id' => $kecTujuan->id,
            'jenis_pupuk_id' => $jenis->id,
            'jumlah' => 1200.00
        ]);

        $laporan = LaporanPupuk::create([
            'toko_pupuk_id' => $toko->id,
            'satuan_id' => $satuan->id,
            'bulan' => 6,
            'tahun' => 2026
        ]);

        // Asal: 480 redemption. Cumulative quota share for month 6 = (6/12)*1200 = 600. 480/600 = 80% (Above 75% -> ineligible)
        $laporan->details()->create([
            'kecamatan_id' => $kecAsal->id,
            'jenis_pupuk_id' => $jenis->id,
            'penebusan' => 480.0
        ]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('sk_relokasi_illegal.pdf', 500);

        $response = $this->actingAs($this->adminUser)->post(route('distribusi-pupuk.pengalihan.store'), [
            'tahun' => 2026,
            'bulan' => 6,
            'jenis_pupuk_id' => $jenis->id,
            'kecamatan_asal_id' => $kecAsal->id,
            'kecamatan_tujuan_id' => $kecTujuan->id,
            'jumlah' => 10.0,
            'nama_sk' => 'SK Relokasi Ilegal',
            'bukti_sk' => $file,
            'keterangan' => 'Illegal transfer'
        ]);

        $response->assertSessionHasErrors(['kecamatan_asal_id']);
    }

    public function test_can_upload_annual_sk_document(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->create('sk_alokasi_2026.pdf', 500);

        $response = $this->actingAs($this->adminUser)->post(route('kuota-tahunan.store'), [
            'tahun' => 2026,
            'bukti_sk' => $file,
            'data' => [
                Kecamatan::first()->id => [
                    JenisPupuk::first()->id => 5000.00
                ]
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('dokumen_alokasi_tahunans', [
            'tahun' => 2026
        ]);

        $dokumen = \App\Models\DokumenAlokasiTahunan::where('tahun', 2026)->first();
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($dokumen->file_path);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Bpp;
use App\Models\Penyuluh;
use App\Models\Gapoktan;
use App\Models\KelompokTani;
use App\Models\Petani;
use App\Models\KategoriKomoditas;
use App\Models\Komoditas;
use App\Models\Varietas;
use App\Models\Satuan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Bidang
        $bidangs = [
            'Bidang Penyuluhan Pertanian (Bank Data)',
            'Bidang Produksi Tanaman Pangan',
            'Bidang Perkebunan & Hortikultura',
            'Bidang Prasarana dan Sarana Pertanian (PSP)'
        ];

        foreach ($bidangs as $bidangName) {
            Bidang::firstOrCreate(['nama' => $bidangName]);
        }

        $wilayah = [
            'Barangka' => ['Barangka', 'Bungkolo', 'Lafinde', 'Lapolea', 'Sawerigadi', 'Walelei', 'Waulai', 'Wuna'],
            'Kusambi' => ['Bakeramba', 'Guali', 'Kasakamu', 'Kusambi', 'Lakawoghe', 'Lapokainse', 'Lemoambo', 'Sidamangura', 'Tanjung Pinang', 'Konawe'],
            'Lawa' => ['Lagadi', 'Lalemba', 'Latompe', 'Latugho', 'Madampi', 'Watumela', 'Lapadaku', 'Wamelai'],
            'Maginti' => ['Abadi Jaya', 'Bangko', 'Gala', 'Kangkunawe', 'Kembar Maminasa', 'Maginti', 'Pajala', 'Pasipadangan'],
            'Napano Kusambi' => ['Kombikuno', 'Lahaji', 'Latawe', 'Masara', 'Tangkumaho', 'Umba'],
            'Sawerigadi' => ['Kampobalano', 'Lakalamba', 'Lawada Jaya', 'Lombu Jaya', 'Maperaha', 'Marobea', 'Nihi', 'Ondoke', 'Wakoila', 'Waukuni'],
            'Tiworo Kepulauan' => ['Katela', 'Lasama', 'Laworo', 'Sidomakmur', 'Wandoke', 'Waturempe', 'Wulanga Jaya', 'Tiworo', 'Waumere'],
            'Tiworo Selatan' => ['Barakkah', 'Kasimpa Jaya', 'Katangana', 'Parura Jaya', 'Sangia Tiworo'],
            'Tiworo Tengah' => ['Labokolo', 'Lakabu', 'Langku Langku', 'Mekar Jaya', 'Momuntu', 'Suka Damai', 'Wanseriwu', 'Wapae'],
            'Tiworo Utara' => ['Bero', 'Mandike', 'Santigi', 'Santiri', 'Tasipi', 'Tiga', 'Tondasi'],
            'Wadaga' => ['Kampani', 'Katobu', 'Lailangga', 'Lakanaha', 'Lasosodo', 'Lindo', 'Wakontu']
        ];

        foreach ($wilayah as $kecamatanName => $desas) {
            $kecamatan = Kecamatan::firstOrCreate(['nama' => $kecamatanName]);

            foreach ($desas as $desaName) {
                Desa::firstOrCreate([
                    'kecamatan_id' => $kecamatan->id,
                    'nama' => $desaName
                ]);
            }

            // Seed BPP untuk setiap kecamatan
            $bpp = Bpp::firstOrCreate([
                'kecamatan_id' => $kecamatan->id,
                'nama' => 'BPP ' . $kecamatanName,
                'alamat' => 'Jl. Poros Raya Kecamatan ' . $kecamatanName
            ]);

            // 3. Seed Penyuluh (di setiap BPP)
            Penyuluh::firstOrCreate(
                ['nama' => 'Penyuluh ' . $kecamatanName],
                [
                    'nip' => '19920815' . rand(100000, 999999),
                    'telepon' => '08123456' . rand(100, 999),
                    'bpp_id' => $bpp->id
                ]
            );

            // 4. Seed Gapoktan (di setiap Kecamatan)
            $gapoktan = Gapoktan::firstOrCreate(
                [
                    'kecamatan_id' => $kecamatan->id,
                    'nama' => 'Gapoktan ' . $kecamatanName . ' Lestari'
                ],
                [
                    'ketua' => 'Bpk. Ketua Gapoktan ' . $kecamatanName
                ]
            );

            // Ambil salah satu desa untuk dikaitkan dengan Kelompok Tani
            $firstDesa = Desa::where('kecamatan_id', $kecamatan->id)->first();
            if ($firstDesa) {
                // 5. Seed Kelompok Tani (di desa pertama)
                $poktan = KelompokTani::firstOrCreate(
                    [
                        'desa_id' => $firstDesa->id,
                        'nama' => 'Poktan ' . $firstDesa->name . ' Makmur'
                    ],
                    [
                        'gapoktan_id' => $gapoktan->id,
                        'ketua' => 'Bpk. Ketua Poktan ' . $firstDesa->name
                    ]
                );

                // 6. Seed Petani (di kelompok tani tersebut)
                Petani::firstOrCreate(
                    ['nik' => '740203' . rand(1000000000, 9999999999)],
                    [
                        'kelompok_tani_id' => $poktan->id,
                        'nama' => 'Petani Uji ' . $firstDesa->name,
                        'telepon' => '08234567' . rand(100, 999),
                        'alamat' => 'Desa ' . $firstDesa->name . ', Kec. ' . $kecamatanName
                    ]
                );
            }
        }

        // 7. Seed Kategori Komoditas, Komoditas, & Varietas
        $komoditasData = [
            'Tanaman Pangan' => [
                'Padi Sawah' => [
                    'varietas' => ['Ciherang', 'Mekongga', 'Inpari 32'],
                    'periode' => 'Bulanan',
                    'form' => null,
                    'durasi' => 4
                ],
                'Jagung Hibrida' => [
                    'varietas' => ['Bima 20', 'Pioneer P35', 'DK 771'],
                    'periode' => 'Bulanan',
                    'form' => null,
                    'durasi' => 4
                ],
            ],
            'Hortikultura Sayuran' => [
                'Bawang Merah' => [
                    'varietas' => ['Bima Brebes', 'Maja Cipanas'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Cabai Rawit' => [
                    'varietas' => ['Cakra Hijau', 'Bara'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Tomat' => [
                    'varietas' => ['Gondol', 'Permata'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Kubis' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Petai' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 3
                ],
                'Jengkol' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 3
                ],
                'Melinjo' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 3
                ],
            ],
            'Hortikultura Buah' => [
                'Semangka' => [
                    'varietas' => ['Hibrida'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Melon' => [
                    'varietas' => ['Action'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 3
                ],
                'Stroberi' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Bulanan',
                    'form' => 'SPH-SBS',
                    'durasi' => 6
                ],
                'Mangga' => [
                    'varietas' => ['Harum Manis', 'Manalagi'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 12
                ],
                'Pisang' => [
                    'varietas' => ['Kepok', 'Ambon'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 9
                ],
                'Durian' => [
                    'varietas' => ['Montong'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 12
                ],
                'Jeruk' => [
                    'varietas' => ['Siam'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-BST',
                    'durasi' => 9
                ],
            ],
            'Hortikultura Biofarmaka' => [
                'Jahe' => [
                    'varietas' => ['Gajah', 'Merah'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-TBF',
                    'durasi' => 9
                ],
                'Kunyit' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-TBF',
                    'durasi' => 9
                ],
                'Temulawak' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-TBF',
                    'durasi' => 9
                ],
                'Kencur' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-TBF',
                    'durasi' => 6
                ],
                'Serai' => [
                    'varietas' => ['Lokal'],
                    'periode' => 'Triwulanan',
                    'form' => 'SPH-TBF',
                    'durasi' => 6
                ],
            ],
            'Perkebunan' => [
                'Kakao' => [
                    'varietas' => ['Sulawesi 1', 'Sulawesi 2'],
                    'periode' => 'Bulanan',
                    'form' => null,
                    'durasi' => 12
                ],
                'Kelapa Dalam' => [
                    'varietas' => ['Mapanget', 'Tenga'],
                    'periode' => 'Bulanan',
                    'form' => null,
                    'durasi' => 12
                ],
            ]
        ];

        foreach ($komoditasData as $kategoriName => $items) {
            $kategori = KategoriKomoditas::firstOrCreate(['nama' => $kategoriName]);

            foreach ($items as $komoditasName => $info) {
                $durasi = $info['durasi'] ?? 4;
                $komoditas = Komoditas::firstOrCreate([
                    'kategori_komoditas_id' => $kategori->id,
                    'nama' => $komoditasName
                ], [
                    'jenis_periode' => $info['periode'],
                    'form_type' => $info['form'],
                    'durasi_panen_bulan' => $durasi,
                ]);

                // Selalu update durasi_panen_bulan ke nilai terbaru dari seeder
                $komoditas->update(['durasi_panen_bulan' => $durasi]);

                foreach ($info['varietas'] as $varietasName) {
                    Varietas::firstOrCreate([
                        'komoditas_id' => $komoditas->id,
                        'nama' => $varietasName
                    ]);
                }
            }
        }

        // 8. Seed Satuan
        $satuans = ['Ton', 'Kwintal', 'Kg', 'Liter'];
        foreach ($satuans as $satuanName) {
            Satuan::firstOrCreate(['nama' => $satuanName]);
        }

        // 9. Seed Jenis Pupuk
        $jenisPupuk = ['Urea', 'NPK Phonska', 'SP-36', 'ZA'];
        foreach ($jenisPupuk as $nama) {
            \App\Models\JenisPupuk::firstOrCreate([
                'nama' => $nama,
                'deskripsi' => 'Pupuk bersubsidi jenis ' . $nama
            ]);
        }

        // 10. Seed Toko/Distributor Pupuk
        $tokoData = [
            'Toko Tani Makmur' => [
                'pemilik' => 'H. Sudirman',
                'alamat' => 'Kec. Lawa, Muna Barat',
                'telepon' => '081234567890',
                'kecamatans' => ['Lawa', 'Sawerigadi']
            ],
            'UD Subur Abadi' => [
                'pemilik' => 'Bpk. Handoko',
                'alamat' => 'Kec. Kusambi, Muna Barat',
                'telepon' => '081298765432',
                'kecamatans' => ['Kusambi', 'Barangka']
            ],
            'Toko Pupuk Tiworo' => [
                'pemilik' => 'Ibu Rahmawati',
                'alamat' => 'Kec. Tiworo Tengah, Muna Barat',
                'telepon' => '082155667788',
                'kecamatans' => ['Tiworo Tengah']
            ]
        ];

        foreach ($tokoData as $nama => $info) {
            $toko = \App\Models\TokoPupuk::firstOrCreate(
                ['nama' => $nama],
                [
                    'pemilik' => $info['pemilik'],
                    'alamat' => $info['alamat'],
                    'telepon' => $info['telepon']
                ]
            );

            // Sync kecamatan
            $kecamatanIds = \App\Models\Kecamatan::whereIn('nama', $info['kecamatans'])->pluck('id')->toArray();
            $toko->kecamatans()->sync($kecamatanIds);
        }

        // 11. Seed Jenis Alat Alsintan
        $jenisAlat = ['Traktor Roda 2', 'Traktor Roda 4', 'Pompa Air', 'Cultivator', 'Combine Harvester', 'Power Thresher'];
        foreach ($jenisAlat as $nama) {
            \App\Models\JenisAlat::firstOrCreate([
                'nama' => $nama,
                'deskripsi' => 'Alat mesin pertanian bantuan jenis ' . $nama
            ]);
        }

        // 12. Seed Bantuan Benih & Bibit
        $poktans = KelompokTani::take(3)->get();
        if ($poktans->count() >= 3) {
            // Benih Pangan (Padi Sawah)
            $padi = Komoditas::where('nama', 'Padi Sawah')->first();
            $varietas = $padi ? $padi->varietas()->first() : null;
            if ($padi) {
                \App\Models\BantuanBenihPangan::firstOrCreate(
                    [
                        'kelompok_tani_id' => $poktans[0]->id,
                        'komoditas_id' => $padi->id,
                        'tahun_bantuan' => 2026
                    ],
                    [
                        'varietas_id' => $varietas ? $varietas->id : null,
                        'jumlah_bantuan' => 500,
                        'satuan' => 'Kg',
                        'sumber_dana' => 'APBN',
                        'keterangan' => 'Bantuan benih padi untuk musim tanam gadu.'
                    ]
                );
            }

            // Bibit Horti (Bawang Merah)
            $bawang = Komoditas::where('nama', 'Bawang Merah')->first();
            if ($bawang) {
                \App\Models\BantuanBibitHorti::firstOrCreate(
                    [
                        'kelompok_tani_id' => $poktans[1]->id,
                        'komoditas_id' => $bawang->id,
                        'tahun_bantuan' => 2026
                    ],
                    [
                        'jumlah_bantuan' => 200,
                        'satuan' => 'Kg',
                        'sumber_dana' => 'APBD Kabupaten',
                        'keterangan' => 'Bantuan bibit bawang merah unggulan daerah.'
                    ]
                );
            }

            // Bibit Perkebunan (Kakao)
            $kakao = Komoditas::where('nama', 'Kakao')->first();
            if (!$kakao) {
                $kategoriPerkebunan = KategoriKomoditas::where('nama', 'like', '%Perkebunan%')->first();
                if ($kategoriPerkebunan) {
                    $kakao = Komoditas::firstOrCreate([
                        'kategori_komoditas_id' => $kategoriPerkebunan->id,
                        'nama' => 'Kakao',
                        'periode' => 'Semesteran',
                        'durasi_panen_bulan' => 6
                    ]);
                }
            }
            if ($kakao) {
                \App\Models\BantuanBibitPerkebunan::firstOrCreate(
                    [
                        'kelompok_tani_id' => $poktans[2]->id,
                        'komoditas_id' => $kakao->id,
                        'tahun_bantuan' => 2025
                    ],
                    [
                        'jumlah_bantuan' => 1000,
                        'satuan' => 'Batang',
                        'sumber_dana' => 'DAK',
                        'keterangan' => 'Bantuan bibit kakao untuk perluasan lahan.'
                    ]
                );
            }
        }
    }
}

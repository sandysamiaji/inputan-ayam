<?php

namespace App\Services;

use App\Models\FarmStock;
use App\Models\HealthTreatment;
use Illuminate\Support\Facades\Schema;

class MedicineCatalogService
{
    /**
     * Daftar Kategori Resmi Farmasi & Medis Peternakan Ayam Petelur
     */
    public static function getCategories(): array
    {
        return [
            'obat_cacing' => [
                'key' => 'obat_cacing',
                'label' => 'Obat Cacing (Anthelmintik)',
                'icon' => '🪱',
                'examples' => 'Vermixon, Wormzol, Levamisol, Cestocide',
                'description' => 'Membasmi cacing gilig (Ascaridia galli), cacing pita, dan cacing sekum di usus ayam.',
                'badge_color' => 'bg-amber-100 text-amber-800',
            ],
            'antibiotik' => [
                'key' => 'antibiotik',
                'label' => 'Antibiotik / Antibakteri',
                'icon' => '💊',
                'examples' => 'Neomeditril, Amoxitin, Doxyvet, Theranest, Koleridin, Trimycin',
                'description' => 'Mengobati infeksi bakteri saluran pernapasan (CRD/ngorok, Coryza/snot) dan pencernaan (Kolera/berak hijau, Coli).',
                'badge_color' => 'bg-rose-100 text-rose-800',
            ],
            'antikoksidia' => [
                'key' => 'antikoksidia',
                'label' => 'Antikoksidiosis (Obat Berak Darah)',
                'icon' => '🩸',
                'examples' => 'Toltrazuril 2.5%, Coccilin, Amprolin-300',
                'description' => 'Mengobati dan mencegah infeksi koksidiosis (berak darah/cokelat berlendir pada usus ayam).',
                'badge_color' => 'bg-red-100 text-red-800',
            ],
            'vitamin' => [
                'key' => 'vitamin',
                'label' => 'Vitamin & Suplemen Antistres',
                'icon' => '🍊',
                'examples' => 'Vita Stress, Egg Stimulant, Vitamin B Complex, Fortevit, Vita Chicks',
                'description' => 'Mencegah stres cuaca panas (heat stress), memacu nafsu makan, dan menjaga stabilitas puncak produksi telur.',
                'badge_color' => 'bg-emerald-100 text-emerald-800',
            ],
            'vaksin' => [
                'key' => 'vaksin',
                'label' => 'Vaksin (Pencegahan Virus Unggas)',
                'icon' => '💉',
                'examples' => 'ND Lasota, ND IB, ND Clone G7, Medivac Coryza, AI (Flu Burung), Gumboro',
                'description' => 'Program vaksinasi wajib pembentukan antibodi terhadap virus ganas ayam petelur.',
                'badge_color' => 'bg-purple-100 text-purple-800',
            ],
            'mineral' => [
                'key' => 'mineral',
                'label' => 'Mineral, Kalsium & Premix Layer',
                'icon' => '🧱',
                'examples' => 'Kalsium & Mineral Premix Layer, Egg Shell Booster (CaCO3), DCP',
                'description' => 'Menyuplai kalsium, fosfor, dan trace mineral untuk kekuatan cangkang telur dan mencegah kelumpuhan.',
                'badge_color' => 'bg-sky-100 text-sky-800',
            ],
            'disinfektan' => [
                'key' => 'disinfektan',
                'label' => 'Disinfektan & Sanitasi Kandang',
                'icon' => '🧪',
                'examples' => 'Medisep, Antisep, Rodalon, Formades, Destan',
                'description' => 'Sterilisasi kandang, peralatan minum/pakan, sanitasi air minum, dan lingkungan luar kandang.',
                'badge_color' => 'bg-cyan-100 text-cyan-800',
            ],
            'obat' => [
                'key' => 'obat',
                'label' => 'Obat Lainnya / Herbal Unggas',
                'icon' => '🌿',
                'examples' => 'Kunyit Ekstrak, Temulawak Unggas, Minyak Ikan, Salep Kaki',
                'description' => 'Perlakuan medis alternatif, herbal pencegahan, dan salep pengobatan lokal.',
                'badge_color' => 'bg-slate-100 text-slate-800',
            ],
        ];
    }

    /**
     * Master Data Katalog Dasar Obat & Vaksin Peternakan Ayam Petelur
     */
    public static function getBaseMedicines(): array
    {
        return [
            // =========================================================================
            // 1. OBAT CACING (ANTHELMINTIK)
            // =========================================================================
            [
                'id' => 1,
                'name' => 'Vermixon',
                'category_key' => 'obat_cacing',
                'category' => 'Obat Cacing (Anthelmintik)',
                'stock' => 12,
                'unit' => 'Botol',
                'dosage' => '15–30 ml / 1 L air minum',
                'application' => 'Air minum',
                'schedule' => 'Setiap 1–2 bulan sekali / saat gejala cacingan',
                'indication' => 'Ayam kurus, bulu kusam, nafsu makan tinggi tapi BB drop, ada cacing di feses',
                'notes' => 'Obat cacing cair Piperazine efektif membasmi cacing gilig (Ascaridia galli) pada ayam petelur tanpa menurunkan produksi telur.',
            ],
            [
                'id' => 2,
                'name' => 'Wormzol-K / Wormzol-B',
                'category_key' => 'obat_cacing',
                'category' => 'Obat Cacing (Anthelmintik)',
                'stock' => 8,
                'unit' => 'Box',
                'dosage' => '1 kaplet / 1–2 kg BB atau 1 g / 2 L air',
                'application' => 'Air minum / Suap langsung',
                'schedule' => 'Rutin setiap 6–8 minggu',
                'indication' => 'Cacing gilig, cacing pita (Raillietina sp.), dan cacing sekum (Heterakis gallinarum)',
                'notes' => 'Kombinasi Albendazole / Niclosamide berdaya kerja luas membunuh cacing dari fase telur hingga cacing dewasa.',
            ],
            [
                'id' => 3,
                'name' => 'Levamisol (Cestocide / Levavit)',
                'category_key' => 'obat_cacing',
                'category' => 'Obat Cacing (Anthelmintik)',
                'stock' => 5,
                'unit' => 'Gram',
                'dosage' => '1 g / 2 L air minum (25–30 mg/kg BB)',
                'application' => 'Air minum pagi',
                'schedule' => '1 hari pemberian, ulangi setelah 3 minggu',
                'indication' => 'Cacing saluran cerna dan pernapasan unggas',
                'notes' => 'Membasmi cacing sekaligus memiliki efek imunostimulan meningkatkan daya tahan tubuh ayam.',
            ],

            // =========================================================================
            // 2. ANTIBIOTIK & ANTIBAKTERI
            // =========================================================================
            [
                'id' => 4,
                'name' => 'Neomeditril',
                'category_key' => 'antibiotik',
                'category' => 'Antibiotik / Antibakteri',
                'stock' => 15,
                'unit' => 'Botol',
                'dosage' => '0,1 ml / kg BB (0,5–1 ml / 2 L air)',
                'application' => 'Air minum',
                'schedule' => '3–5 hari berturut-turut saat gejala CRD/Snot',
                'indication' => 'CRD (ngorok), Korisa/Snot (muka bengkak, pilek), Kolera (berak hijau), Colibacillosis',
                'notes' => 'Antibiotik Enrofloxacin spektrum luas Medion, bekerja cepat membunuh bakteri Mycoplasma dan gram positif/negatif.',
            ],
            [
                'id' => 5,
                'name' => 'Amoxitin',
                'category_key' => 'antibiotik',
                'category' => 'Antibiotik / Antibakteri',
                'stock' => 10,
                'unit' => 'Box',
                'dosage' => '1 g / 2 L air minum (10–20 mg/kg BB)',
                'application' => 'Air minum',
                'schedule' => '3–5 hari berturut-turut',
                'indication' => 'Infeksi saluran pernapasan dan pencernaan, Colibacillosis, Necrotic Enteritis',
                'notes' => 'Antibiotik Amoxicillin bakterisidal daya serap tinggi, aman untuk ayam petelur produktif.',
            ],
            [
                'id' => 6,
                'name' => 'Doxyvet',
                'category_key' => 'antibiotik',
                'category' => 'Antibiotik / Antibakteri',
                'stock' => 8,
                'unit' => 'Box',
                'dosage' => '1 g / 2 L air minum',
                'application' => 'Air minum',
                'schedule' => '3 hari berturut-turut',
                'indication' => 'CRD Kompleks (ngorok kronis + infeksi E. coli), Coryza, radang kantung udara',
                'notes' => 'Doxycycline Hyclate konsentrasi tinggi untuk penanganan cepat infeksi saluran pernapasan unggas.',
            ],
            [
                'id' => 7,
                'name' => 'Theranest / Koleridin',
                'category_key' => 'antibiotik',
                'category' => 'Antibiotik / Antibakteri',
                'stock' => 7,
                'unit' => 'Botol',
                'dosage' => '1 g / 1 L air minum',
                'application' => 'Air minum',
                'schedule' => '3–5 hari saat serangan kolera',
                'indication' => 'Fowl Cholera (Kolera ayam/berak hijau pekat, jengger membiru) dan Snot ganas',
                'notes' => 'Kombinasi Sulfadimethoxine dan Trimethoprim dengan efek sinergis membunuh kuman Pasteurella multocida.',
            ],
            [
                'id' => 8,
                'name' => 'Trimycin',
                'category_key' => 'antibiotik',
                'category' => 'Antibiotik / Antibakteri',
                'stock' => 6,
                'unit' => 'Box',
                'dosage' => '1 g / 1–2 L air minum',
                'application' => 'Air minum',
                'schedule' => '3–5 hari berturut-turut',
                'indication' => 'Mycoplasmosis (CRD), Snot, luka infeksi pasca kanibalisme',
                'notes' => 'Kombinasi Tylosin dan Erythromycin spesialis jaringan pernapasan atas ayam.',
            ],

            // =========================================================================
            // 3. ANTIKOKSIDIOSIS (OBAT BERAK DARAH)
            // =========================================================================
            [
                'id' => 9,
                'name' => 'Toltrazuril 2.5% (Baycox / Toltracox)',
                'category_key' => 'antikoksidia',
                'category' => 'Antikoksidiosis (Berak Darah)',
                'stock' => 6,
                'unit' => 'Botol',
                'dosage' => '1 ml / 1 L air minum (25 ppm)',
                'application' => 'Air minum selama 48 jam berturut-turut',
                'schedule' => '2 hari berturut-turut saat terindikasi koksidia',
                'indication' => 'Kotoran bercampur darah, feses cokelat berlendir, ayam pucat sayap terkulai',
                'notes' => 'Obat koksidiosis standar emas mematikan semua stadium parasit Eimeria tenella & E. necatrix di usus ayam.',
            ],
            [
                'id' => 10,
                'name' => 'Coccilin / Amprolin-300',
                'category_key' => 'antikoksidia',
                'category' => 'Antikoksidiosis (Berak Darah)',
                'stock' => 8,
                'unit' => 'Box',
                'dosage' => '1 g / 1 L air minum',
                'application' => 'Air minum',
                'schedule' => 'Pola 3-2-3 (3 hari obat, 2 hari air biasa, 3 hari obat)',
                'indication' => 'Koksidiosis cecal dan usus halus',
                'notes' => 'Kombinasi Amprolium dan Sulfaquinoxaline bekerja menghentikan reproduksi parasit koksidia.',
            ],

            // =========================================================================
            // 4. VITAMIN & SUPLEMEN ANTISTRES
            // =========================================================================
            [
                'id' => 11,
                'name' => 'Vita Stress',
                'category_key' => 'vitamin',
                'category' => 'Vitamin & Suplemen',
                'stock' => 20,
                'unit' => 'Box',
                'dosage' => '1 g / 1–2 L air minum',
                'application' => 'Air minum pagi hari',
                'schedule' => 'Rutin 2 hari/minggu, pasca vaksinasi, atau cuaca panas',
                'indication' => 'Heat stress (cuaca panas ekstrem), pasca vaksinasi, pindah kandang',
                'notes' => 'Multivitamin lengkap plus elektrolit penyeimbang cairan tubuh unggas agar produksi telur stabil.',
            ],
            [
                'id' => 12,
                'name' => 'Egg Stimulant',
                'category_key' => 'vitamin',
                'category' => 'Vitamin & Suplemen',
                'stock' => 15,
                'unit' => 'Box',
                'dosage' => '1 g / 2 L air minum',
                'application' => 'Air minum pagi',
                'schedule' => 'Awal bertelur, drop produksi, atau puncak bertelur',
                'indication' => 'Produksi telur drop, awal bertelur, memperpanjang masa afkir',
                'notes' => 'Kombinasi vitamin, asam amino essensial, dan antibiotik konsentrasi mikro untuk mendongkrak persentase HD produksi telur.',
            ],
            [
                'id' => 13,
                'name' => 'Vitamin B Complex + Elektrolit',
                'category_key' => 'vitamin',
                'category' => 'Vitamin & Suplemen',
                'stock' => 18,
                'unit' => 'Botol',
                'dosage' => '1 g / 2 L air minum',
                'application' => 'Air minum pagi',
                'schedule' => 'Rutin 2× seminggu atau saat cuaca terik',
                'indication' => 'Mencegah kelemahan kaki, stres panas, gangguan metabolisme karbohidrat/protein',
                'notes' => 'Menjaga fungsi saraf motorik, merangsang nafsu makan, dan mengoptimalkan penyerapan sari pakan.',
            ],
            [
                'id' => 14,
                'name' => 'Fortevit',
                'category_key' => 'vitamin',
                'category' => 'Vitamin & Suplemen',
                'stock' => 10,
                'unit' => 'Box',
                'dosage' => '1 g / 10 L air minum',
                'application' => 'Air minum',
                'schedule' => 'Saat masa kritis, drop produksi, pergantian pakan',
                'indication' => 'Pemulihan pasca sakit, peningkatan fertilitas dan daya tahan',
                'notes' => 'Multivitamin konsentrat tinggi dosis hemat, cepat memulihkan stamina ayam lemas.',
            ],
            [
                'id' => 15,
                'name' => 'Vita Chicks',
                'category_key' => 'vitamin',
                'category' => 'Vitamin & Suplemen',
                'stock' => 12,
                'unit' => 'Box',
                'dosage' => '5 g / 7 L air minum',
                'application' => 'Air minum',
                'schedule' => 'Fase starter hingga grower / pullet',
                'indication' => 'Mempercepat pertumbuhan, mencegah kematian bibit, memperkuat kerangka',
                'notes' => 'Vitamin & mineral harian untuk bibit dan pullet ayam petelur.',
            ],

            // =========================================================================
            // 5. VAKSIN (PENCEGAHAN VIRUS)
            // =========================================================================
            [
                'id' => 16,
                'name' => 'ND Lasota',
                'category_key' => 'vaksin',
                'category' => 'Vaksin Unggas',
                'stock' => 6,
                'unit' => 'Botol',
                'dosage' => '1 botol / 1.000 ekor dosis',
                'application' => 'Tetes mata / air minum',
                'schedule' => 'Umur 18–20 minggu (Booster) & diulang tiap 2–3 bulan',
                'indication' => 'Pencegahan penyakit Tetelo / Sampar ayam (Newcastle Disease)',
                'notes' => 'Vaksin aktif strain LaSota menghasilkan titer antibodi tinggi pelindung fase produksi telur.',
            ],
            [
                'id' => 17,
                'name' => 'ND IB Vaccine',
                'category_key' => 'vaksin',
                'category' => 'Vaksin Unggas',
                'stock' => 8,
                'unit' => 'Botol',
                'dosage' => '1.000–2.000 dosis per botol',
                'application' => 'Tetes mata / air minum',
                'schedule' => 'Umur 4, 16, 24, 40 minggu',
                'indication' => 'Pencegahan ganda Tetelo (ND) dan Bronkitis pernapasan (IB)',
                'notes' => 'Kombinasi virus aktif ND dan Infectious Bronchitis mencegah penurunan kualitas kerabang dan telur lembek.',
            ],
            [
                'id' => 18,
                'name' => 'ND Clone 45 / G7',
                'category_key' => 'vaksin',
                'category' => 'Vaksin Unggas',
                'stock' => 5,
                'unit' => 'Botol',
                'dosage' => '1 botol / 1.000 ekor dosis',
                'application' => 'Tetes mata / air minum / suntik',
                'schedule' => 'Sesuai jadwal program vaksinasi farm',
                'indication' => 'Proteksi terhadap varian virus ND genotipe VII liar yang ganas',
                'notes' => 'Vaksin strain klon dengan reaksi pasca vaksinasi minimal tapi proteksi maksimal.',
            ],
            [
                'id' => 19,
                'name' => 'Medivac Coryza (Vaksin Snot)',
                'category_key' => 'vaksin',
                'category' => 'Vaksin Unggas',
                'stock' => 4,
                'unit' => 'Botol',
                'dosage' => '0,5 ml per ekor suntik intramuskuler',
                'application' => 'Suntik paha / dada',
                'schedule' => 'Umur 8 & 16 minggu sebelum masuk baterai',
                'indication' => 'Pencegahan penyakit Korisa / Snot menahun (Avibacterium paragallinarum)',
                'notes' => 'Vaksin inaktif emulsi minyak memberikan kekebalan humoral tahan lama.',
            ],
            [
                'id' => 20,
                'name' => 'Medivac AI (Avian Influenza / Flu Burung)',
                'category_key' => 'vaksin',
                'category' => 'Vaksin Unggas',
                'stock' => 4,
                'unit' => 'Botol',
                'dosage' => '0,5 ml per ekor suntik intramuskuler',
                'application' => 'Suntik dada / paha',
                'schedule' => 'Umur 16 minggu & booster berkala tiap 4–6 bulan',
                'indication' => 'Pencegahan virus Flu Burung (Avian Influenza) subtipe H5N1 & H9N2',
                'notes' => 'Vaksin inaktif emulsi minyak proteksi esensial terhadap wabah flu burung mematikan.',
            ],

            // =========================================================================
            // 6. MINERAL, KALSIUM & PREMIX LAYER
            // =========================================================================
            [
                'id' => 21,
                'name' => 'Kalsium & Mineral Premix Layer',
                'category_key' => 'mineral',
                'category' => 'Mineral & Premix Layer',
                'stock' => 25,
                'unit' => 'Kg',
                'dosage' => '2 kg per 100 kg pakan (2%)',
                'application' => 'Campur pakan kering',
                'schedule' => 'Setiap hari selama fase bertelur',
                'indication' => 'Kerabang telur tipis, telur retak tinggi, kelumpuhan kandang baterai',
                'notes' => 'Menyuplai kalsium, fosfor, magnesium, zinc, dan vitamin D3 untuk pembentukan cangkang telur tebal dan kokoh.',
            ],
            [
                'id' => 22,
                'name' => 'Egg Shell Booster (CaCO3 Murni)',
                'category_key' => 'mineral',
                'category' => 'Mineral & Premix Layer',
                'stock' => 30,
                'unit' => 'Kg',
                'dosage' => '1–2 kg / 100 kg pakan',
                'application' => 'Campur pakan sore hari',
                'schedule' => 'Sore hari saat proses kalsifikasi kerabang telur',
                'indication' => 'Mencegah telur mudah pecah saat panen dan pengiriman',
                'notes' => 'Pecahan kalsium berbutir kasar yang diserap perlahan semalaman di tembolok ayam.',
            ],
            [
                'id' => 23,
                'name' => 'DCP (Dicalcium Phosphate)',
                'category_key' => 'mineral',
                'category' => 'Mineral & Premix Layer',
                'stock' => 20,
                'unit' => 'Kg',
                'dosage' => '1 kg / 100 kg pakan',
                'application' => 'Campur pakan kering',
                'schedule' => 'Setiap pencampuran pakan mandiri (self-mixing)',
                'indication' => 'Sumber fosfor dan kalsium anorganik murni ketersediaan biologis tinggi',
                'notes' => 'Menjaga rasio Ca:P ideal 4:1 pada ayam petelur aktif berproduksi.',
            ],

            // =========================================================================
            // 7. DISINFEKTAN & SANITASI KANDANG
            // =========================================================================
            [
                'id' => 24,
                'name' => 'Medisep',
                'category_key' => 'disinfektan',
                'category' => 'Disinfektan & Sanitasi',
                'stock' => 14,
                'unit' => 'Botol',
                'dosage' => '15 ml / 10 L air',
                'application' => 'Semprot kandang, celup kaki (foot dip), cuci peralatan',
                'schedule' => '1–2× seminggu atau setiap saat masuk kandang',
                'indication' => 'Sterilisasi bakteri, jamur, dan virus di udara dan permukaan kandang',
                'notes' => 'Quaternary Ammonium Compound (QAC) tidak korosif, aman disemprotkan saat ada ayam di kandang.',
            ],
            [
                'id' => 25,
                'name' => 'Antisep',
                'category_key' => 'disinfektan',
                'category' => 'Disinfektan & Sanitasi',
                'stock' => 10,
                'unit' => 'Botol',
                'dosage' => '3–5 ml / 10 L air minum, 10 ml / 5 L air semprot',
                'application' => 'Sanitasi air minum & semprot kandang',
                'schedule' => 'Rutin sanitasi air minum atau saat pengobatan luka kanibalisme',
                'indication' => 'Mencegah lumut dan bakteri E. coli di saluran nipple minum unggas',
                'notes' => 'Antiseptik iodophor berspektrum luas aman diminum ayam dosis rendah.',
            ],
            [
                'id' => 26,
                'name' => 'Rodalon',
                'category_key' => 'disinfektan',
                'category' => 'Disinfektan & Sanitasi',
                'stock' => 8,
                'unit' => 'Botol',
                'dosage' => '15 ml per 10 Liter air',
                'application' => 'Semprot lingkungan kandang & cuci egg tray',
                'schedule' => '1× seminggu dan saat cuci tray telur',
                'indication' => 'Membunuh bakteri patogen Salmonella dan virus di lingkungan peternakan',
                'notes' => 'Desinfektan konsentrat wangi tidak mengiritasi pernapasan ayam dan pekerja kandang.',
            ],
        ];
    }

    /**
     * Dapatkan Katalog Lengkap dengan Sinkronisasi Stok Real-time (Masuk, Terpakai, Sisa)
     */
    public static function getAllMedicines(bool $withLiveStock = true, $startDate = null, $endDate = null): array
    {
        $baseMedicines = self::getBaseMedicines();

        // Siapkan atribut kalkulasi stok untuk setiap obat dasar
        foreach ($baseMedicines as &$med) {
            $initial = (float) ($med['stock'] ?? 0);
            $med['initial_stock'] = $initial;
            $med['total_masuk'] = $initial; // Stok awal dianggap sebagai stok masuk mula-mula
            $med['total_keluar'] = 0.0;
            $med['current_stock'] = $initial;
            $med['status'] = $initial > 5 ? 'aman' : ($initial > 0 ? 'menipis' : 'habis');
            $med['status_label'] = $initial > 5 ? 'Stok Aman' : ($initial > 0 ? 'Menipis' : 'Habis');
            $med['status_color'] = $initial > 5 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($initial > 0 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
        }
        unset($med);

        if (!$withLiveStock) {
            return $baseMedicines;
        }

        try {
            // Index array berdasarkan id
            $medicinesById = [];
            foreach ($baseMedicines as $med) {
                $medicinesById[$med['id']] = $med;
            }

            // 1. Tarik Data Transaksi FarmStock (Masuk & Keluar)
            if (Schema::hasTable('farm_stocks')) {
                $fsQuery = FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin', 'disinfektan', 'mineral'])
                    ->where(function ($q) {
                        $q->whereNull('notes')->orWhere('notes', 'NOT LIKE', '[NONAKTIF]%');
                    });

                if ($startDate && $endDate) {
                    $fsQuery->whereBetween('date', [$startDate, $endDate]);
                }

                $farmStocks = $fsQuery->get();
                foreach ($farmStocks as $fs) {
                    $qty = (float) $fs->quantity;
                    if ($qty <= 0) continue;

                    $matchedId = self::matchMedicineId($fs->item_name, $baseMedicines);
                    if ($matchedId && isset($medicinesById[$matchedId])) {
                        if ($fs->type === 'masuk') {
                            $medicinesById[$matchedId]['total_masuk'] += $qty;
                        } elseif ($fs->type === 'keluar') {
                            $medicinesById[$matchedId]['total_keluar'] += $qty;
                        }
                    } else {
                        // Produk kustom / di luar 26 obat standar
                        $customKey = 'custom_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fs->item_name));
                        if (!isset($medicinesById[$customKey])) {
                            $medicinesById[$customKey] = [
                                'id' => count($medicinesById) + 1,
                                'name' => $fs->item_name,
                                'category_key' => $fs->category ?: 'obat',
                                'category' => ucfirst($fs->category ?: 'Obat'),
                                'initial_stock' => 0.0,
                                'total_masuk' => 0.0,
                                'total_keluar' => 0.0,
                                'stock' => 0.0,
                                'current_stock' => 0.0,
                                'unit' => $fs->unit ?: 'Botol',
                                'dosage' => 'Sesuai aturan pakai',
                                'application' => 'Air minum / Pakan',
                                'schedule' => 'Sesuai anjuran',
                                'indication' => 'Obat / Suplemen peternakan khusus',
                                'notes' => 'Tercatat otomatis dari transaksi gudang',
                                'status' => 'habis',
                                'status_label' => 'Habis',
                                'status_color' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        }
                        if ($fs->type === 'masuk') {
                            $medicinesById[$customKey]['total_masuk'] += $qty;
                        } elseif ($fs->type === 'keluar') {
                            $medicinesById[$customKey]['total_keluar'] += $qty;
                        }
                    }
                }
            }

            // 2. Tarik Data Pemakaian dari HealthTreatment (Selalu Keluar / Pemakaian Kandang)
            if (Schema::hasTable('health_treatments')) {
                $htQuery = HealthTreatment::where(function ($q) {
                    $q->whereNull('notes')->orWhere('notes', 'NOT LIKE', '[NONAKTIF]%');
                });

                if ($startDate && $endDate) {
                    $htQuery->whereBetween('date', [$startDate, $endDate]);
                }

                $healthTreatments = $htQuery->get();
                foreach ($healthTreatments as $ht) {
                    $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
                    if ($val <= 0) $val = 1.0;

                    $matchedId = self::matchMedicineId($ht->medicine_name, $baseMedicines);
                    if ($matchedId && isset($medicinesById[$matchedId])) {
                        $medicinesById[$matchedId]['total_keluar'] += $val;
                    } else {
                        $customKey = 'custom_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $ht->medicine_name));
                        if (!isset($medicinesById[$customKey])) {
                            $unit = 'Botol';
                            if (preg_match('/(botol|box|kg|gram|liter|dosis|ampul|sachet)/i', (string) $ht->dosage, $mUnit)) {
                                $unit = ucfirst(strtolower($mUnit[1]));
                            }
                            $medicinesById[$customKey] = [
                                'id' => count($medicinesById) + 1,
                                'name' => $ht->medicine_name,
                                'category_key' => $ht->type ?: 'obat',
                                'category' => ucfirst($ht->type ?: 'Obat'),
                                'initial_stock' => 0.0,
                                'total_masuk' => 0.0,
                                'total_keluar' => 0.0,
                                'stock' => 0.0,
                                'current_stock' => 0.0,
                                'unit' => $unit,
                                'dosage' => $ht->dosage ?: 'Sesuai aturan pakai',
                                'application' => $ht->application_method ?: 'Air minum',
                                'schedule' => 'Sesuai anjuran',
                                'indication' => 'Obat / Suplemen peternakan khusus',
                                'notes' => 'Tercatat otomatis dari pemakaian kandang',
                                'status' => 'habis',
                                'status_label' => 'Habis',
                                'status_color' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        }
                        $medicinesById[$customKey]['total_keluar'] += $val;
                    }
                }
            }

            // 3. Hitung Sisa Stok Akhir & Status untuk Setiap Obat
            foreach ($medicinesById as &$item) {
                $item['total_masuk'] = round((float) $item['total_masuk'], 2);
                $item['total_keluar'] = round((float) $item['total_keluar'], 2);
                $rem = max(0.0, round($item['total_masuk'] - $item['total_keluar'], 2));
                $item['stock'] = $rem;
                $item['current_stock'] = $rem;

                if ($rem > 5) {
                    $item['status'] = 'aman';
                    $item['status_label'] = 'Stok Aman';
                    $item['status_color'] = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                } elseif ($rem > 0) {
                    $item['status'] = 'menipis';
                    $item['status_label'] = 'Menipis';
                    $item['status_color'] = 'bg-amber-50 text-amber-700 border-amber-200';
                } else {
                    $item['status'] = 'habis';
                    $item['status_label'] = 'Habis';
                    $item['status_color'] = 'bg-rose-50 text-rose-700 border-rose-200';
                }
            }
            unset($item);

            return array_values($medicinesById);

        } catch (\Throwable $e) {
            // Jika DB belum siap atau offline, kembalikan base catalog aman
            return $baseMedicines;
        }
    }

    /**
     * Ringkasan Total Inventaris Obat & Vaksin
     */
    public static function getMedicineSummary(?array $medicines = null): array
    {
        $meds = $medicines ?? self::getAllMedicines(true);
        $totalInitial = 0.0;
        $totalMasuk = 0.0;
        $totalKeluar = 0.0;
        $totalStock = 0.0;
        $safeCount = 0;
        $lowCount = 0;
        $emptyCount = 0;

        foreach ($meds as $m) {
            $totalInitial += (float) ($m['initial_stock'] ?? 0);
            $totalMasuk += (float) ($m['total_masuk'] ?? 0);
            $totalKeluar += (float) ($m['total_keluar'] ?? 0);
            $stock = (float) ($m['stock'] ?? 0);
            $totalStock += $stock;

            $status = $m['status'] ?? 'aman';
            if ($status === 'aman') {
                $safeCount++;
            } elseif ($status === 'menipis') {
                $lowCount++;
            } else {
                $emptyCount++;
            }
        }

        return [
            'total_initial' => round($totalInitial, 1),
            'total_masuk' => round($totalMasuk, 1),
            'total_keluar' => round($totalKeluar, 1),
            'total_stock' => round($totalStock, 1),
            'total_products' => count($meds),
            'safe_count' => $safeCount,
            'low_count' => $lowCount,
            'empty_count' => $emptyCount,
        ];
    }

    /**
     * Matching Nama Item Transaksi ke ID Obat Katalog
     */
    public static function matchMedicineId(string $itemName, array $baseMedicines): ?int
    {
        $normalizedItem = strtolower(trim($itemName));
        if (empty($normalizedItem)) return null;

        // 1. Direct exact or substring match with catalog name
        foreach ($baseMedicines as $med) {
            $normMedName = strtolower(trim($med['name']));
            if ($normalizedItem === $normMedName) {
                return $med['id'];
            }
        }

        // 2. Specific alias/keyword map
        $keywordMap = [
            1  => ['vermixon'],
            2  => ['wormzol'],
            3  => ['levamisol', 'cestocide', 'levavit'],
            4  => ['neomeditril'],
            5  => ['amoxitin'],
            6  => ['doxyvet'],
            7  => ['theranest', 'koleridin'],
            8  => ['trimycin'],
            9  => ['toltrazuril', 'baycox', 'toltracox'],
            10 => ['coccilin', 'amprolin'],
            11 => ['vita stress', 'vitastress'],
            12 => ['egg stimulant', 'eggstimulant'],
            13 => ['vitamin b complex', 'b complex', 'b-complex'],
            14 => ['fortevit'],
            15 => ['vita chicks', 'vitachicks'],
            16 => ['lasota', 'nd lasota'],
            17 => ['nd ib', 'ib vaccine'],
            18 => ['clone 45', 'clone g7', 'nd clone'],
            19 => ['coryza', 'vaksin snot', 'medivac coryza'],
            20 => ['medivac ai', 'avian influenza', 'flu burung'],
            21 => ['kalsium & mineral', 'mineral premix', 'kalsium premix'],
            22 => ['egg shell booster', 'eggshell', 'caco3'],
            23 => ['dcp', 'dicalcium phosphate'],
            24 => ['medisep'],
            25 => ['antisep'],
            26 => ['rodalon'],
        ];

        foreach ($keywordMap as $medId => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($normalizedItem, $kw)) {
                    return $medId;
                }
            }
        }

        // 3. Fallback: check if med name starts with or is contained in item name
        foreach ($baseMedicines as $med) {
            $baseNameOnly = explode(' (', $med['name'])[0];
            $baseNameOnly = explode(' /', $baseNameOnly)[0];
            $normBase = strtolower(trim($baseNameOnly));
            if (strlen($normBase) >= 4 && (str_contains($normalizedItem, $normBase) || str_contains($normBase, $normalizedItem))) {
                return $med['id'];
            }
        }

        return null;
    }

    /**
     * Cari detail obat berdasarkan nama
     */
    public static function findMedicineByName(string $name): ?array
    {
        $all = self::getAllMedicines(true);
        $norm = strtolower(trim($name));
        foreach ($all as $med) {
            if (strtolower(trim($med['name'])) === $norm) {
                return $med;
            }
        }
        $matchedId = self::matchMedicineId($name, self::getBaseMedicines());
        if ($matchedId) {
            foreach ($all as $med) {
                if ($med['id'] === $matchedId) return $med;
            }
        }
        return null;
    }
}

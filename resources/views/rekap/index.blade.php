@extends('layouts.app')

@section('content')
<style>
/* Rekap v6 Mobile-First & Desktop Responsive Styles */
.rekap-v6 {
  max-width: 580px;
  margin: 0 auto;
  padding-bottom: 24px;
}
.rekap-v6 .hero {
  background: #fffafa;
  border: 1px solid #f2ccd8;
  border-radius: 20px;
  padding: 16px 18px 15px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(176, 0, 58, 0.04);
}
.rekap-v6 .hero-title {
  font-size: 22px;
  font-weight: 800;
  color: #17171a;
  letter-spacing: -0.02em;
}
.rekap-v6 .status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-left: 8px;
  font-size: 11px;
  font-weight: 700;
  color: #15945d;
  background: #e9f8f0;
  border-radius: 20px;
  padding: 5px 11px;
  vertical-align: middle;
}
.rekap-v6 .dot {
  width: 6px;
  height: 6px;
  background: #15945d;
  border-radius: 50%;
  box-shadow: 0 0 6px rgba(21, 148, 93, 0.6);
}
.rekap-v6 .date {
  font-size: 11.5px;
  color: #718096;
  margin-top: 6px;
}
.rekap-v6 .period-row {
  margin-top: 13px;
  border: 1px solid #d8e0e8;
  border-radius: 12px;
  background: white;
  padding: 10px 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11.5px;
  cursor: pointer;
  transition: all .15s ease;
}
.rekap-v6 .period-row:hover {
  border-color: #b0003a;
  box-shadow: 0 2px 6px rgba(176, 0, 58, 0.06);
}
.rekap-v6 .period-row b {
  flex: 1;
  font-size: 11.5px;
  color: #1e293b;
}
.rekap-v6 .period-row .chev {
  font-size: 13px;
  color: #64748b;
}
.rekap-v6 .preset-row {
  display: flex;
  gap: 6px;
  overflow-x: auto;
  margin-top: 8px;
  padding-bottom: 2px;
  scrollbar-width: none;
}
.rekap-v6 .preset-row::-webkit-scrollbar {
  display: none;
}
.rekap-v6 .preset {
  white-space: nowrap;
  border: 1px solid #d8e0e8;
  background: white;
  border-radius: 15px;
  padding: 6px 12px;
  font-size: 10px;
  color: #4a5568;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all .15s ease;
}
.rekap-v6 .preset:hover {
  border-color: #b0003a;
  color: #b0003a;
}
.rekap-v6 .preset.active {
  background: #b0003a;
  border-color: #b0003a;
  color: white;
  font-weight: 800;
  box-shadow: 0 2px 6px rgba(176, 0, 58, 0.2);
}
.rekap-v6 .section-title {
  font-size: 11px;
  font-weight: 900;
  color: #64748b;
  margin: 0 4px 10px;
  letter-spacing: .06em;
  text-transform: uppercase;
}
.rekap-v6 .tabs {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-bottom: 14px;
}
.rekap-v6 .tab {
  min-width: 0;
  height: 82px;
  border: 1px solid #d9e0e8;
  background: #fff;
  border-radius: 14px;
  padding: 10px 12px;
  text-align: left;
  font-size: 11px;
  font-weight: 800;
  color: #1e293b;
  cursor: pointer;
  transition: all .18s ease;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.rekap-v6 .tab:hover {
  border-color: #ff8fab;
  transform: translateY(-1px);
}
.rekap-v6 .tab.active {
  border-color: #ff6b8d;
  background: #fff8fa;
  color: #b0003a;
  box-shadow: 0 3px 10px rgba(176, 0, 58, 0.08);
}
.rekap-v6 .tab-ico {
  font-size: 19px;
  margin-bottom: 6px;
  line-height: 1;
}
.rekap-v6 .card {
  background: white;
  border-radius: 16px;
  padding: 15px 16px;
  margin-bottom: 12px;
  border: 1px solid #e1e5ea;
  box-shadow: 0 1px 4px rgba(0,0,0,0.02);
}
.rekap-v6 .card-title {
  font-size: 13.5px;
  font-weight: 900;
  color: #17171a;
  margin-bottom: 12px;
  letter-spacing: .02em;
}
.rekap-v6 .card-title:first-letter {
  color: #b0003a;
}
.rekap-v6 .metrics {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.rekap-v6 .metric {
  background: #f8fafc;
  border: 1px solid #edf2f7;
  border-radius: 12px;
  padding: 11px 12px;
}
.rekap-v6 .metric .label {
  font-size: 9.5px;
  color: #718096;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .04em;
}
.rekap-v6 .metric .value {
  font-size: 21px;
  font-weight: 900;
  margin-top: 4px;
  color: #92002f;
  line-height: 1.1;
}
.rekap-v6 .metric .unit {
  font-size: 10px;
  color: #64748b;
  margin-top: 3px;
  font-weight: 600;
}
.rekap-v6 .weekly-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
.rekap-v6 .weekly-box {
  border: 1px solid #e2e8f0;
  border-radius: 13px;
  padding: 12px;
  background: #fafbfc;
  min-height: 124px;
  transition: border-color .15s;
}
.rekap-v6 .weekly-box:hover {
  border-color: #cbd5e1;
}
.rekap-v6 .weekly-box b {
  display: block;
  font-size: 16px;
  color: #92002f;
  font-weight: 900;
}
.rekap-v6 .weekly-box small {
  display: block;
  font-size: 10px;
  color: #64748b;
  margin-top: 2px;
  font-weight: 600;
}
.rekap-v6 .weekly-box strong {
  display: block;
  font-size: 20px;
  margin-top: 9px;
  line-height: 1.1;
  font-weight: 900;
  color: #0f172a;
}
.rekap-v6 .weekly-box span {
  display: block;
  font-size: 10px;
  color: #64748b;
  margin-top: 3px;
  font-weight: 600;
}
.rekap-v6 .weekly-box em {
  display: block;
  font-style: normal;
  font-size: 11px;
  font-weight: 800;
  color: #059669;
  margin-top: 8px;
}
.rekap-v6 .row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  font-size: 12px;
}
.rekap-v6 .row:last-child {
  border-bottom: 0;
}
.rekap-v6 .badge {
  background: #ffe4e6;
  color: #9f1239;
  border-radius: 8px;
  padding: 4px 8px;
  font-size: 10px;
  font-weight: 800;
}
.rekap-v6 .readonly-note {
  font-size: 10.5px;
  color: #64748b;
  margin: -2px 4px 12px;
  font-weight: 600;
}
</style>

<div class="rekap-v6">

    <!-- 1. HERO CARD (SESUAI MOCKUP REKAP DATA) -->
    <section class="hero">
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="display:inline-block; background:#fff0f4; color:#b0003a; border-radius:18px; padding:6px 12px; font-size:10px; font-weight:800; letter-spacing:.03em;">
                REKAP
            </span>
            <span class="status">
                <span class="dot"></span>
                <span>Terhubung</span>
            </span>
        </div>

        <div class="hero-title" style="margin-top:10px;">
            Rekap Data
        </div>
        <div class="date">
            Laporan & ringkasan data kandang peternakan.
        </div>

        <!-- Period Selector Row (Click to open date modal) -->
        <div class="period-row" onclick="openModalPeriodPicker()" title="Klik untuk ganti rentang tanggal">
            <span>📅</span>
            <b>{{ $formattedRange }}</b>
            <span class="chev">⌄</span>
        </div>

        <!-- Preset Chips -->
        <div class="preset-row">
            @php
                $presetOptions = [
                    'hari_ini' => 'Hari Ini',
                    'kemarin' => 'Kemarin',
                    '7_hari' => '7 Hari',
                    '30_hari' => '30 Hari',
                    'bulan_ini' => 'Bulan Ini',
                    'custom' => 'Custom'
                ];
            @endphp
            @foreach($presetOptions as $k => $lbl)
                <a href="{{ route('rekap.index', array_merge(request()->query(), ['preset' => $k, 'tab' => $activeTab])) }}" 
                   class="preset {{ $preset === $k ? 'active' : '' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        <!-- Filter Kloter Dropdown -->
        <div class="period-row" style="margin-top:9px; position:relative; background:#fff;">
            <span>🐔</span>
            <select onchange="filterKloter(this.value)" style="flex:1; border:none; background:transparent; font-size:11.5px; font-weight:800; color:#1e293b; outline:none; cursor:pointer;">
                <option value="" {{ empty($flockId) ? 'selected' : '' }}>
                    Kloter · Semua Kloter ({{ number_format($totalFarmPopulation, 0, ',', '.') }} ekor aktif)
                </option>
                @foreach($allFlocks as $flk)
                    <option value="{{ $flk->id }}" {{ ($flockId == $flk->id) ? 'selected' : '' }}>
                        {{ $flk->name }} · {{ $flk->code }} ({{ number_format($flk->coops->sum('active_chickens'), 0, ',', '.') }} ekor)
                    </option>
                @endforeach
            </select>
            <span class="chev" style="pointer-events:none;">⌄</span>
        </div>
    </section>

    <!-- 2. PILIH REKAP (4 TABS) -->
    <div class="section-title">PILIH REKAP</div>
    <div class="tabs">
        <button type="button" class="tab {{ $activeTab === 'produksi' ? 'active' : '' }}" data-tab="produksi" onclick="switchRekapTab('produksi')">
            <div class="tab-ico">🥚</div>
            <span>Rekap Produksi</span>
        </button>
        <button type="button" class="tab {{ $activeTab === 'pakan' ? 'active' : '' }}" data-tab="pakan" onclick="switchRekapTab('pakan')">
            <div class="tab-ico">🌾</div>
            <span>Rekap Pakan</span>
        </button>
        <button type="button" class="tab {{ $activeTab === 'mortalitas' ? 'active' : '' }}" data-tab="mortalitas" onclick="switchRekapTab('mortalitas')">
            <div class="tab-ico">☠️</div>
            <span>Rekap Mortalitas</span>
        </button>
        <button type="button" class="tab {{ $activeTab === 'kesehatan' ? 'active' : '' }}" data-tab="kesehatan" onclick="switchRekapTab('kesehatan')">
            <div class="tab-ico">💊</div>
            <span>Rekap Kesehatan</span>
        </button>
    </div>

    <div class="readonly-note">Semua data di halaman ini hanya untuk melihat hasil rekap.</div>

    <!-- ========================================== -->
    <!-- TAB 1: REKAP PRODUKSI TELUR -->
    <!-- ========================================== -->
    <div id="tab-produksi" style="{{ $activeTab === 'produksi' ? '' : 'display:none;' }}">

        <!-- Card: Ringkasan Produksi -->
        <div class="card">
            <div class="card-title">RINGKASAN PRODUKSI</div>
            <div class="metrics">
                <!-- 1. Total Produksi -->
                <div class="metric">
                    <div class="label">TOTAL PRODUKSI</div>
                    <div class="value">{{ number_format($totalTelurButir, 0, ',', '.') }}</div>
                    <div class="unit">butir · {{ number_format($totalTelurPeti, 0, ',', '.') }} peti</div>
                </div>

                <!-- 2. HDP -->
                <div class="metric">
                    <div class="label">HDP</div>
                    <div class="value">{{ number_format($hdp, 1, ',', '.') }}</div>
                    <div class="unit">%</div>
                </div>

                <!-- 3. Reject -->
                <div class="metric">
                    <div class="label">REJECT</div>
                    <div class="value">{{ number_format($rejectRate, 1, ',', '.') }}</div>
                    <div class="unit">%</div>
                </div>

                <!-- 4. Berat Badan -->
                <div class="metric">
                    <div class="label">BERAT BADAN</div>
                    <div class="value">{{ number_format($avgBobot, 2, ',', '.') }}</div>
                    <div class="unit">kg/ekor</div>
                </div>
            </div>
        </div>

        <!-- Card: STATUS BLOK KANDANG AKTIF (Produksi per Blok & Kloter) -->
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div class="card-title" style="margin-bottom:0;">STATUS BLOK KANDANG AKTIF</div>
                <span style="font-size:10px; font-weight:800; color:#059669; background:#ecfdf5; padding:3px 8px; border-radius:10px;">
                    {{ count($blokRekap) }} Blok Aktif
                </span>
            </div>

            <!-- Ringkasan per Kloter -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                @foreach($flockRekap as $fr)
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:11px; padding:8px 10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <b style="font-size:11px; color:#1e293b;">{{ $fr['name'] }}</b>
                            <span style="font-size:9.5px; font-weight:800; color:#059669;">HDP {{ number_format($fr['hdp'], 1, ',', '.') }}%</span>
                        </div>
                        <div style="font-size:12.5px; font-weight:900; color:#92002f; margin-top:2px;">
                            {{ number_format($fr['eggs'], 0, ',', '.') }} <span style="font-size:9px; font-weight:700; color:#64748b;">butir · {{ number_format($fr['crates'], 0, ',', '.') }} peti</span>
                        </div>
                        <div style="font-size:9.5px; color:#64748b; margin-top:1px;">
                            Populasi: {{ number_format($fr['chickens'], 0, ',', '.') }} ekor
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List Blok Kandang Aktif -->
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($blokRekap as $br)
                    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:10px 12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <b style="font-size:12.5px; color:#0f172a;">{{ $br['name'] }}</b>
                                    <span style="font-size:9px; font-weight:800; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:6px;">{{ $br['flock_name'] }}</span>
                                    <span style="font-size:9px; font-weight:700; color:#059669;">{{ $br['chicken_age_weeks'] }} Mgg</span>
                                </div>
                                <div style="font-size:10px; color:#64748b; margin-top:3px;">
                                    Populasi: <b>{{ number_format($br['active_chickens'], 0, ',', '.') }}</b> / {{ number_format($br['capacity'], 0, ',', '.') }} ekor
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <b style="font-size:13px; color:#92002f;">{{ number_format($br['eggs'], 0, ',', '.') }} <span style="font-size:9.5px; font-weight:700; color:#64748b;">butir</span></b>
                                <div style="font-size:10px; font-weight:700; color:#059669; margin-top:1px;">
                                    {{ number_format($br['crates'], 0, ',', '.') }} peti · HDP {{ number_format($br['hdp'], 1, ',', '.') }}%
                                </div>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div style="height:5px; background:#f1f5f9; border-radius:4px; overflow:hidden; margin-top:8px;">
                            <div style="height:100%; width:{{ min(100, $br['hdp']) }}%; background:#92002f; border-radius:4px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card: Rekap Mingguan -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="card" style="margin-bottom: 12px;">
            <div class="card-title">REKAP MINGGUAN - {{ strtoupper($flockRekapData['flock_name']) }}</div>
            <div class="weekly-grid">
                @foreach($flockRekapData['data'] as $w)
                    <div class="weekly-box">
                        <b>Umur M{{ $w['age_week'] }}</b>
                        <small>{{ $w['date_range'] }}</small>
                        <strong>{{ number_format($w['eggs'], 0, ',', '.') }}</strong>
                        <span>butir · {{ number_format($w['crates'], 0, ',', '.') }} peti</span>
                        <em>HDP {{ number_format($w['hdp'], 1, ',', '.') }}%</em>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Card: Grafik Tren Masuk vs Keluar -->
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div class="card-title" style="margin-bottom:0;" id="chartTitle">GRAFIK TREN TELUR</div>
                <select id="selectChartMetric" onchange="updateChartMetric(this.value)" style="padding:4px 8px; font-size:10px; font-weight:800; border:1px solid #dce2eb; border-radius:8px; background:#fff; color:#334155; outline:none;">
                    <option value="egg_peti" selected>Peti Masuk vs Keluar</option>
                    <option value="egg_kg">Kg Masuk vs Keluar</option>
                    <option value="egg_butir">Butir Masuk vs Keluar</option>
                </select>
            </div>
            <div style="position:relative; width:100%; height:210px;">
                <canvas id="rekapTrendChart"></canvas>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- TAB 2: REKAP PAKAN -->
    <!-- ========================================== -->
    <div id="tab-pakan" style="{{ $activeTab === 'pakan' ? '' : 'display:none;' }}">

        <!-- Card: Ringkasan Pakan -->
        <div class="card">
            <div class="card-title">RINGKASAN PAKAN</div>
            <div class="metrics" style="grid-template-columns:1fr;">
                <div class="metric">
                    <div class="label">TOTAL PEMAKAIAN</div>
                    <div class="value">{{ number_format($totalPakanKg, 0, ',', '.') }}</div>
                    <div class="unit">kg · {{ $totalPakanKarungStr }}</div>
                </div>
            </div>
        </div>

        <!-- Card: Rekap Mingguan Pakan -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="card" style="margin-bottom: 12px;">
            <div class="card-title">REKAP MINGGUAN - {{ strtoupper($flockRekapData['flock_name']) }}</div>
            <div class="weekly-grid">
                @foreach($flockRekapData['data'] as $w)
                    <div class="weekly-box">
                        <b>Umur M{{ $w['age_week'] }}</b>
                        <small>{{ $w['date_range'] }}</small>
                        <strong>{{ number_format($w['feed_kg'], 0, ',', '.') }}</strong>
                        <span>kg · {{ $w['feed_karung_str'] }}</span>
                        <em style="color:#b97400;">{{ $w['feed_fase'] }}</em>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Card: Pemakaian per Blok -->
        <div class="card">
            <div class="card-title">PEMAKAIAN PER BLOK <span style="font-size:9px; font-weight:normal; color:#64748b; float:right;">Aktual vs Master</span></div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($blokRekap as $br)
                    @php
                        $diff = $br['feed_kg'] - $br['master_feed_kg'];
                        $isOver = $diff > 0;
                        $isUnder = $diff < 0;
                        $diffColor = $isOver ? '#be123c' : ($isUnder ? '#b97400' : '#059669');
                        $diffText = $isOver ? '+'.number_format($diff, 1, ',', '.') : ($isUnder ? number_format($diff, 1, ',', '.') : 'Sesuai');
                    @endphp
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:12px; font-weight:800; color:#1e293b;">{{ $br['name'] }} <span style="font-size:9px; font-weight:600; color:#64748b;">({{ $br['flock_name'] }})</span></div>
                            <div style="font-size:10px; font-weight:600; color:#64748b; margin-top:2px;">Hitungan Master: <b>{{ number_format($br['master_feed_kg'], 1, ',', '.') }} kg</b></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:10px; font-weight:600; color:#64748b;">Total Inputan</div>
                            <div style="font-size:13px; font-weight:900; color:#1e293b; margin-top:1px;">{{ number_format($br['feed_kg'], 1, ',', '.') }} kg</div>
                            <div style="font-size:10px; font-weight:800; color:{{ $diffColor }}; margin-top:2px;">
                                Selisih: {{ $diffText }} {{ $diffText !== 'Sesuai' ? 'kg' : '' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- TAB 3: REKAP MORTALITAS -->
    <!-- ========================================== -->
    <div id="tab-mortalitas" style="{{ $activeTab === 'mortalitas' ? '' : 'display:none;' }}">

        <!-- Card: Ringkasan Mortalitas -->
        <div class="card">
            <div class="card-title">RINGKASAN MORTALITAS</div>
            <div class="metrics">
                <div class="metric">
                    <div class="label">TOTAL KEMATIAN</div>
                    <div class="value" style="color:#be123c;">{{ number_format($totalMortalitas, 0, ',', '.') }}</div>
                    <div class="unit">ekor</div>
                </div>
                <div class="metric">
                    <div class="label">MORTALITAS</div>
                    <div class="value" style="color:#be123c;">{{ number_format($mortalitasRate, 2, ',', '.') }}</div>
                    <div class="unit">%</div>
                </div>
            </div>
        </div>

        <!-- Card: Rekap Mingguan Mortalitas -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="card" style="margin-bottom: 12px;">
            <div class="card-title">REKAP MINGGUAN - {{ strtoupper($flockRekapData['flock_name']) }}</div>
            <div class="weekly-grid">
                @foreach($flockRekapData['data'] as $w)
                    <div class="weekly-box">
                        <b>Umur M{{ $w['age_week'] }}</b>
                        <small>{{ $w['date_range'] }}</small>
                        <strong style="color:#be123c;">{{ number_format($w['mortality_count'], 0, ',', '.') }}</strong>
                        <span>ekor</span>
                        <em style="color:#be123c;">{{ number_format($w['mortality_rate'], 2, ',', '.') }}%</em>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Card: Detail per Blok -->
        <div class="card">
            <div class="card-title">DETAIL PER BLOK</div>
            @foreach($blokRekap as $br)
                <div class="row">
                    <div>
                        <span style="font-weight:800; color:#1e293b;">{{ $br['name'] }}</span>
                        <small style="color:#64748b; margin-left:6px;">({{ $br['flock_name'] }})</small>
                    </div>
                    <b style="color:#be123c;">{{ number_format($br['mortality_count'], 0, ',', '.') }} ekor</b>
                </div>
            @endforeach
        </div>

    </div>

    <!-- ========================================== -->
    <!-- TAB 4: REKAP KESEHATAN -->
    <!-- ========================================== -->
    <div id="tab-kesehatan" style="{{ $activeTab === 'kesehatan' ? '' : 'display:none;' }}">

        <!-- Card: Ringkasan Kesehatan Ayam -->
        <div class="card">
            <div class="card-title">RINGKASAN KESEHATAN AYAM</div>
            <div class="metrics" style="grid-template-columns:repeat(3, 1fr);">
                <div class="metric" style="text-align:center;">
                    <div class="label">VAKSIN</div>
                    <div class="value" style="color:#6d28d9;">{{ $totalVaksin }}</div>
                    <div class="unit">kegiatan</div>
                </div>
                <div class="metric" style="text-align:center;">
                    <div class="label">OBAT</div>
                    <div class="value" style="color:#6d28d9;">{{ $totalObat }}</div>
                    <div class="unit">kegiatan</div>
                </div>
                <div class="metric" style="text-align:center;">
                    <div class="label">VITAMIN</div>
                    <div class="value" style="color:#6d28d9;">{{ $totalVitamin }}</div>
                    <div class="unit">kegiatan</div>
                </div>
            </div>
        </div>

        <!-- Card: Rekap Kesehatan -->
        <div class="card">
            <div class="card-title">REKAP KESEHATAN</div>
            <div class="row">
                <span>Vaksin</span>
                <span class="badge">{{ $totalVaksin }} kegiatan</span>
            </div>
            <div class="row">
                <span>Vitamin</span>
                <span class="badge" style="background:#fef3c7; color:#b45309;">{{ $totalVitamin }} kegiatan</span>
            </div>
            <div class="row">
                <span>Obat-obatan Kandang</span>
                <span class="badge" style="background:#ede9fe; color:#6d28d9;">{{ $totalObat }} kegiatan</span>
            </div>
        </div>

        @if($healthTreatments->count() > 0)
            <div class="card">
                <div class="card-title">RIWAYAT TINDAKAN MEDIS TERAKHIR</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($healthTreatments as $ht)
                        <div style="border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <b style="font-size:11.5px; color:#1e293b;">{{ $ht->medicine_name }}</b>
                                <span style="font-size:9.5px; font-weight:800; background:#f1f5f9; color:#475569; padding:2px 7px; border-radius:6px;">
                                    {{ ucfirst($ht->type) }}
                                </span>
                            </div>
                            <div style="font-size:10px; color:#64748b; margin-top:2px;">
                                {{ $ht->date ? $ht->date->format('d M Y') : '-' }} · {{ $ht->coop ? $ht->coop->name : 'Semua Blok' }} · Dosis: {{ $ht->dosage }} ({{ $ht->application_method }})
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- 3. ACTION LINK: TABEL DETAIL & EKSPOR -->
    <div style="margin-top:16px; text-align:center;">
        <a href="{{ route('rekap.detail', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
           style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:800; color:#b0003a; background:#fff; border:1px solid #f2ccd8; border-radius:12px; padding:10px 16px; text-decoration:none; box-shadow:0 1px 4px rgba(176,0,58,0.05); transition:all .15s;">
            <i data-lucide="table" style="width:14px; height:14px;"></i>
            <span>Buka Tabel Rekapitulasi Detail & Ekspor Excel →</span>
        </a>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL / DRAWER PILIH PERIODE & KALENDER -->
<!-- ========================================================================= -->
<div id="modalPeriodPicker" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Pilih Periode Laporan</h3>
            <button onclick="closeModalPeriodPicker()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                ✕
            </button>
        </div>

        <form method="GET" action="{{ route('rekap.index') }}" class="mt-4 space-y-4">
            <input type="hidden" name="tab" id="modalInputTab" value="{{ $activeTab }}">
            @if($flockId)
                <input type="hidden" name="flock_id" value="{{ $flockId }}">
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
            </div>

            <input type="hidden" name="preset" value="custom">

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModalPeriodPicker()" class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-xs font-bold">
                    Batal
                </button>
                <button type="submit" class="flex-2 py-2.5 px-4 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold shadow-md">
                    Terapkan Rentang Tanggal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentTab = '{{ $activeTab }}';

function switchRekapTab(tabName) {
    currentTab = tabName;
    const allTabs = ['produksi', 'pakan', 'mortalitas', 'kesehatan'];
    allTabs.forEach(t => {
        const el = document.getElementById('tab-' + t);
        const btn = document.querySelector(`.tab[data-tab="${t}"]`);
        if (el) el.style.display = (t === tabName) ? 'block' : 'none';
        if (btn) btn.classList.toggle('active', t === tabName);
    });

    const modalTabInput = document.getElementById('modalInputTab');
    if (modalTabInput) modalTabInput.value = tabName;

    // Update URL param without refreshing
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

function filterKloter(flockId) {
    const url = new URL(window.location);
    if (flockId) {
        url.searchParams.set('flock_id', flockId);
    } else {
        url.searchParams.delete('flock_id');
    }
    url.searchParams.set('tab', currentTab);
    window.location.href = url.toString();
}

// Modal Period Picker
function openModalPeriodPicker() {
    const modal = document.getElementById('modalPeriodPicker');
    if (!modal) return;
    modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
    const content = modal.querySelector('div');
    if (content) content.classList.remove('translate-y-full');
}

function closeModalPeriodPicker() {
    const modal = document.getElementById('modalPeriodPicker');
    if (!modal) return;
    modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
    const content = modal.querySelector('div');
    if (content) content.classList.add('translate-y-full');
}

// Chart.js initialization
let trendChartInstance = null;
const chartLabels = {!! json_encode($chartLabels) !!};
const chartDataSets = {
    egg_peti: {
        title: 'GRAFIK TREN TELUR (PETI)',
        unit: 'Peti',
        masuk: {!! json_encode($chartEggPetiMasuk) !!},
        keluar: {!! json_encode($chartEggPetiKeluar) !!},
        colorMasuk: '#b0003a',
        colorKeluar: '#15945d'
    },
    egg_kg: {
        title: 'GRAFIK TREN TELUR (KG)',
        unit: 'Kg',
        masuk: {!! json_encode($chartEggKgMasuk) !!},
        keluar: {!! json_encode($chartEggKgKeluar) !!},
        colorMasuk: '#b0003a',
        colorKeluar: '#15945d'
    },
    egg_butir: {
        title: 'GRAFIK TREN TELUR (BUTIR)',
        unit: 'Butir',
        masuk: {!! json_encode($chartEggButirMasuk) !!},
        keluar: {!! json_encode($chartEggButirKeluar) !!},
        colorMasuk: '#b0003a',
        colorKeluar: '#15945d'
    }
};

function renderTrendChart(key) {
    const ctx = document.getElementById('rekapTrendChart');
    if (!ctx) return;
    const metric = chartDataSets[key] || chartDataSets.egg_peti;

    if (trendChartInstance) {
        trendChartInstance.destroy();
    }

    trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Produksi Masuk',
                    data: metric.masuk,
                    borderColor: metric.colorMasuk,
                    backgroundColor: metric.colorMasuk + '15',
                    borderWidth: 2.2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2.5
                },
                {
                    label: 'Penjualan Keluar',
                    data: metric.keluar,
                    borderColor: metric.colorKeluar,
                    backgroundColor: metric.colorKeluar + '15',
                    borderWidth: 2.2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { boxWidth: 10, font: { size: 10, weight: '700' } }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 }, color: '#94a3b8' } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 }, color: '#94a3b8' } }
            }
        }
    });
}

function updateChartMetric(val) {
    const metric = chartDataSets[val];
    if (metric) {
        document.getElementById('chartTitle').textContent = metric.title;
    }
    renderTrendChart(val);
}

document.addEventListener('DOMContentLoaded', () => {
    renderTrendChart('egg_peti');
});
</script>
@endpush
@endsection

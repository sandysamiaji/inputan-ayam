@extends('layouts.app')

@section('content')
<style>
:root {
  --m: #92002f;
  --m2: #730023;
  --o: #f29a00;
  --bg: #f5f7fa;
  --t: #172033;
  --mut: #8491a6;
  --line: #e5e9ef;
  --g: #10b981;
  --b: #2a78ed;
  --p: #8846e7;
  --r: #e95363;
}

.master-v6-container {
  max-width: 440px;
  margin: 0 auto;
  font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  color: var(--t);
  padding-bottom: 20px;
}

.master-v6-container button,
.master-v6-container input,
.master-v6-container select {
  font: inherit;
}

/* Head Bar */
.master-v6-container .head {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}
.master-v6-container .back {
  width: 36px;
  height: 36px;
  border: 1px solid var(--line);
  background: #fff;
  border-radius: 10px;
  font-size: 21px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--t);
  text-decoration: none;
  font-weight: 800;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  transition: all .15s ease;
  cursor: pointer;
}
.master-v6-container .back:hover {
  border-color: var(--m);
  color: var(--m);
  transform: scale(1.02);
}
.master-v6-container .head h1 {
  font-size: 21px;
  font-weight: 800;
  margin: 0;
  line-height: 1.1;
  letter-spacing: -0.02em;
}
.master-v6-container .head p {
  font-size: 10px;
  color: var(--mut);
  margin: 2px 0 0;
  font-weight: 500;
}
.master-v6-container .status {
  margin-left: auto;
  background: #fff;
  border: 1px solid var(--line);
  padding: 7px 10px;
  border-radius: 16px;
  font-size: 8.5px;
  color: var(--mut);
  font-weight: 700;
  white-space: nowrap;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.master-v6-container .status b {
  color: var(--m);
  font-size: 9.5px;
  font-weight: 800;
}

/* Hero Section */
.master-v6-container .hero {
  margin-top: 4px;
  background: linear-gradient(115deg, var(--m2), #a3093e);
  color: #fff;
  border-radius: 17px;
  padding: 17px;
  box-shadow: 0 7px 17px rgba(112, 0, 34, 0.18);
  position: relative;
  overflow: hidden;
}
.master-v6-container .hero::after {
  content: '';
  position: absolute;
  top: -40%;
  right: -20%;
  width: 160px;
  height: 160px;
  background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
  pointer-events: none;
}
.master-v6-container .hero .tag {
  display: inline-block;
  font-size: 8px;
  font-weight: 900;
  background: rgba(255, 255, 255, 0.18);
  border-radius: 7px;
  padding: 5px 8px;
  letter-spacing: .05em;
  text-transform: uppercase;
}
.master-v6-container .hero h2 {
  font-size: 18px;
  font-weight: 900;
  margin: 9px 0 4px;
  letter-spacing: .02em;
}
.master-v6-container .hero p {
  font-size: 9.5px;
  line-height: 1.5;
  margin: 0;
  opacity: .92;
  font-weight: 400;
}
.master-v6-container .hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  margin-top: 12px;
  border: 0;
  background: #fff;
  color: var(--m);
  border-radius: 9px;
  padding: 9px 13px;
  font-size: 9px;
  font-weight: 900;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: transform .15s, background .15s;
}
.master-v6-container .hero-btn:hover {
  background: #fff8fa;
  transform: translateY(-1px);
}

/* Section Labels */
.master-v6-container .label {
  font-size: 9.5px;
  text-transform: uppercase;
  letter-spacing: .06em;
  font-weight: 900;
  color: #75839a;
  margin: 18px 2px 9px;
}

/* 7 Menu Cards */
.master-v6-container .menus {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.master-v6-container .menu {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 12px;
  min-height: 96px;
  position: relative;
  box-shadow: 0 2px 5px rgba(23, 32, 51, 0.04);
  text-decoration: none;
  color: inherit;
  display: block;
  transition: all .18s ease;
  cursor: pointer;
}
.master-v6-container .menu:hover {
  transform: translateY(-2px);
  border-color: #dca0b5;
  box-shadow: 0 6px 14px rgba(146, 0, 47, 0.08);
}
.master-v6-container .ico {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: grid;
  place-items: center;
  font-size: 15px;
  margin-bottom: 8px;
}
.master-v6-container .blue { background: #eaf2ff; color: var(--b); }
.master-v6-container .purple { background: #f2e9ff; color: var(--p); }
.master-v6-container .green { background: #e1faf1; color: var(--g); }
.master-v6-container .orange { background: #fff1d7; color: #b97400; }
.master-v6-container .pink { background: #ffe9ed; color: var(--r); }
.master-v6-container .gray { background: #edf1f5; color: #526077; }

.master-v6-container .menu b {
  font-size: 10.5px;
  font-weight: 800;
  display: block;
  color: var(--t);
  line-height: 1.25;
}
.master-v6-container .menu p {
  font-size: 8px;
  color: var(--mut);
  line-height: 1.35;
  margin: 3px 12px 0 0;
}
.master-v6-container .arr {
  position: absolute;
  right: 10px;
  top: 10px;
  color: #9ca8b8;
  font-size: 14px;
  font-weight: 700;
}

/* Base Card */
.master-v6-container .card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 15px;
  padding: 14px;
  margin-bottom: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}
.master-v6-container .ctop {
  display: flex;
  align-items: center;
  gap: 9px;
}
.master-v6-container .ctop .round {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: #f1e8ff;
  color: var(--p);
  display: grid;
  place-items: center;
  font-size: 15px;
  flex-shrink: 0;
}
.master-v6-container .ctop h3 {
  font-size: 12.5px;
  font-weight: 800;
  margin: 0;
  color: var(--t);
}
.master-v6-container .ctop small {
  font-size: 8px;
  color: var(--mut);
  display: block;
  margin-top: 2px;
}
.master-v6-container .chev {
  margin-left: auto;
  color: #98a5b7;
  font-size: 16px;
  font-weight: 700;
  text-decoration: none;
  transition: color .15s;
}
.master-v6-container .chev:hover {
  color: var(--m);
}

/* Select Box */
.master-v6-container .selectbox {
  margin-top: 11px;
  border: 1px solid var(--line);
  border-radius: 11px;
  padding: 10px;
  background: #fafbfc;
}
.master-v6-container .selectbox label {
  font-size: 7.5px;
  font-weight: 900;
  color: var(--mut);
  display: block;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: .06em;
}
.master-v6-container .selectbox select {
  width: 100%;
  border: 1px solid #dce2eb;
  background: #fff;
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 10.5px;
  color: var(--t);
  font-weight: 700;
  outline: none;
  cursor: pointer;
  transition: border-color .15s;
}
.master-v6-container .selectbox select:focus {
  border-color: var(--m);
}

/* Detail Box */
.master-v6-container .detail {
  margin-top: 9px;
  border: 1px solid var(--line);
  border-radius: 11px;
  padding: 11px;
  background: #fff;
}
.master-v6-container .detailhead {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.master-v6-container .detailhead b {
  font-size: 13px;
  font-weight: 800;
  color: var(--t);
}
.master-v6-container .pill {
  font-size: 7px;
  font-weight: 900;
  border-radius: 12px;
  padding: 4px 7px;
  background: #e1faf0;
  color: #078f68;
  letter-spacing: .03em;
  text-transform: uppercase;
}

/* Metrics Grid */
.master-v6-container .metrics {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 6px;
  margin-top: 9px;
}
.master-v6-container .metric {
  background: #fafbfc;
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 8px;
}
.master-v6-container .metric small {
  display: block;
  color: var(--mut);
  font-size: 7.5px;
  font-weight: 600;
}
.master-v6-container .metric b {
  display: block;
  font-size: 10px;
  font-weight: 800;
  margin-top: 2px;
  color: var(--t);
}

/* Edit Button */
.master-v6-container .edit {
  width: 100%;
  border: 0;
  background: var(--m);
  color: #fff;
  border-radius: 8px;
  padding: 9px;
  font-size: 9px;
  font-weight: 900;
  margin-top: 9px;
  display: block;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(146, 0, 47, 0.15);
  transition: all .15s ease;
}
.master-v6-container .edit:hover {
  background: var(--m2);
  transform: translateY(-1px);
}

/* Feed Box */
.master-v6-container .feed {
  border: 1px solid var(--line);
  border-radius: 11px;
  padding: 11px;
  margin-top: 8px;
  background: #fff;
}
.master-v6-container .feedtop {
  display: flex;
  align-items: center;
}
.master-v6-container .feedicon {
  width: 31px;
  height: 31px;
  border-radius: 9px;
  background: #fff1d7;
  color: #ad6c00;
  display: grid;
  place-items: center;
  margin-right: 8px;
  font-size: 15px;
  flex-shrink: 0;
}
.master-v6-container .feed b {
  font-size: 10px;
  font-weight: 800;
  color: var(--t);
}
.master-v6-container .feed small {
  display: block;
  color: var(--mut);
  font-size: 7.5px;
  margin-top: 2px;
}
.master-v6-container .feedval {
  margin-top: 9px;
  background: #fafbfc;
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 8px 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.master-v6-container .feedval span {
  font-size: 8px;
  color: var(--mut);
  font-weight: 700;
}
.master-v6-container .feedval strong {
  font-size: 13px;
  font-weight: 900;
  color: var(--t);
}

/* Flock Box */
.master-v6-container .kbox {
  border: 1px solid #eadcfa;
  background: #fdfaff;
  border-radius: 10px;
  padding: 10px;
  margin-top: 8px;
}
.master-v6-container .khead {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.master-v6-container .khead b {
  font-size: 10px;
  font-weight: 800;
  color: var(--t);
}
.master-v6-container .kmeta {
  font-size: 7.5px;
  color: var(--mut);
  margin-top: 3px;
  font-weight: 600;
}
.master-v6-container .blocks {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 5px;
  margin-top: 8px;
}
.master-v6-container .block {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 7px;
  padding: 7px;
  text-align: center;
}
.master-v6-container .block b {
  font-size: 8.5px;
  font-weight: 800;
  display: block;
  color: var(--t);
}
.master-v6-container .block p {
  font-size: 7px;
  color: var(--mut);
  margin: 3px 0;
  font-weight: 700;
}
.master-v6-container .bar {
  height: 4px;
  background: #edf0f4;
  border-radius: 4px;
  overflow: hidden;
}
.master-v6-container .bar i {
  display: block;
  height: 100%;
  background: var(--m);
  border-radius: 4px;
  transition: width .3s ease;
}

/* Medicine Item */
.master-v6-container .med {
  display: flex;
  gap: 8px;
  padding: 9px 0;
  border-bottom: 1px solid var(--line);
  align-items: flex-start;
}
.master-v6-container .med:last-child {
  border: 0;
  padding-bottom: 0;
}
.master-v6-container .medico {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: #ffe9ed;
  color: var(--r);
  display: grid;
  place-items: center;
  font-size: 14px;
  flex-shrink: 0;
}
.master-v6-container .medtext {
  flex: 1;
}
.master-v6-container .medtext b {
  font-size: 9.5px;
  font-weight: 800;
  color: var(--t);
  display: block;
}
.master-v6-container .medtext p {
  font-size: 7.5px;
  color: var(--mut);
  line-height: 1.4;
  margin: 2px 0;
}
.master-v6-container .medtext span {
  font-size: 7.5px;
  font-weight: 800;
  color: #647187;
  display: block;
}

/* System Settings Item */
.master-v6-container .setting {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 9px 0;
  border-bottom: 1px solid var(--line);
}
.master-v6-container .setting:last-child {
  border: 0;
}
.master-v6-container .setting b {
  font-size: 9.5px;
  font-weight: 800;
  color: var(--t);
}
.master-v6-container .setting small {
  display: block;
  font-size: 7.5px;
  color: var(--mut);
  margin-top: 2px;
}
.master-v6-container .val {
  font-size: 9px;
  font-weight: 900;
  color: var(--m);
}

/* Footnotes */
.master-v6-container .note {
  font-size: 8px;
  color: var(--mut);
  line-height: 1.45;
  margin-top: 9px;
  background: #fafbfc;
  border: 1px dashed #dce2eb;
  border-radius: 9px;
  padding: 8px 10px;
}
</style>

<div class="master-v6-container">

    <!-- ========================================== -->
    <!-- 1. VIEW: MASTER HUB (UTAMA / 7 MENU) -->
    <!-- ========================================== -->
    <div id="view-hub" style="{{ in_array($section, ['hub', 'all', '']) ? '' : 'display:none;' }}">
        <!-- Top Navigation Header -->
        <div class="head">
            <a href="{{ route('dashboard') }}" class="back" title="Kembali ke Dashboard">‹</a>
            <div>
                <h1>Master</h1>
                <p>Data acuan & konfigurasi farm</p>
            </div>
            <div class="status">
                <b>{{ number_format($totalChickens, 0, ',', '.') }}</b> ekor · {{ $flocks->count() }} kloter
            </div>
        </div>

        <!-- Hero Card -->
        <section class="hero">
            <span class="tag">✦ PUSAT MASTER FARM</span>
            <h2>{{ $farmName }}</h2>
            <p>Master menyimpan seluruh data acuan. Data ini dibaca oleh Input, Rekap dan Dashboard untuk perhitungan otomatis.</p>
            <a href="{{ route('master.info-farm') }}" class="hero-btn">
                <span>✎ Edit Info Farm</span>
            </a>
        </section>

        <!-- SECTION: Data Utama (7 Menus) -->
        <div class="label">Data Utama</div>
        <div class="menus">
            <!-- 1. Info Farm -->
            <a href="{{ route('master.info-farm') }}" class="menu" id="menu-info-farm">
                <div class="ico blue">ⓘ</div>
                <b>Info Farm</b>
                <p>Identitas & tampilan dashboard</p>
                <span class="arr">›</span>
            </a>

            <!-- 2. Master Flock -->
            <a href="#card-master-flock" onclick="openSection('master-flock'); return false;" class="menu" id="menu-master-flock">
                <div class="ico purple">🐔</div>
                <b>Master Flock</b>
                <p>Kloter, blok & populasi</p>
                <span class="arr">›</span>
            </a>

            <!-- 3. Standar Produksi -->
            <a href="#card-standar-produksi" onclick="openSection('standar-produksi'); return false;" class="menu" id="menu-standar-produksi">
                <div class="ico green">🥚</div>
                <b>Standar Produksi</b>
                <p>Umur 13–90 minggu</p>
                <span class="arr">›</span>
            </a>

            <!-- 4. Standar Pakan -->
            <a href="#card-standar-pakan" onclick="openSection('standar-pakan'); return false;" class="menu" id="menu-standar-pakan">
                <div class="ico orange">🌾</div>
                <b>Standar Pakan</b>
                <p>Umur 13–90 minggu</p>
                <span class="arr">›</span>
            </a>

            <!-- 5. Standar BB -->
            <a href="#card-standar-bb" onclick="openSection('standar-bb'); return false;" class="menu" id="menu-standar-bb">
                <div class="ico orange">⚖</div>
                <b>Standar BB</b>
                <p>Min · target · max</p>
                <span class="arr">›</span>
            </a>

            <!-- 6. Vaksin & Obat -->
            <a href="#card-vaksin-obat" onclick="openSection('vaksin-obat'); return false;" class="menu" id="menu-vaksin-obat">
                <div class="ico pink">💊</div>
                <b>Vaksin & Obat</b>
                <p>Jadwal & perlakuan</p>
                <span class="arr">›</span>
            </a>

            <!-- 7. Pengaturan Sistem -->
            <a href="#card-pengaturan" onclick="openSection('pengaturan'); return false;" class="menu" id="menu-pengaturan">
                <div class="ico gray">⚙</div>
                <b>Pengaturan</b>
                <p>Parameter sistem</p>
                <span class="arr">›</span>
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. VIEW: STANDAR PRODUKSI (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-standar-produksi" style="{{ $section === 'standar-produksi' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Standar Produksi</h1>
                <p>Master per umur · 13 sampai 90 minggu</p>
            </div>
        </div>

        <div class="label">Acuan Umur</div>
        <section class="card">
            <div class="ctop">
                <div class="round">🥚</div>
                <div>
                    <h3>Standar Produksi</h3>
                    <small>Master per umur · 13 sampai 90 minggu</small>
                </div>
                <a href="{{ route('master.standards') }}" class="chev" title="Kelola Standar">›</a>
            </div>

            <div class="selectbox">
                <label>PILIH UMUR UNTUK DILIHAT / DIEDIT</label>
                <select id="selectUmurProduksi">
                    @for($w = 13; $w <= 90; $w++)
                        <option value="{{ $w }}" {{ $w == $avgAgeWeeks ? 'selected' : '' }}>{{ $w }} minggu</option>
                    @endfor
                </select>
            </div>

            <div class="detail">
                <div class="detailhead">
                    <b id="prodUmurTitle">{{ $avgAgeWeeks }} Minggu</b>
                    <span class="pill" id="prodFasePill">PRODUKSI NAIK</span>
                </div>
                <div class="metrics">
                    <div class="metric">
                        <small>Berat Telur</small>
                        <b id="prodBeratTelur">60 g</b>
                    </div>
                    <div class="metric">
                        <small>Fase</small>
                        <b id="prodFaseText">Produksi Naik</b>
                    </div>
                    <div class="metric">
                        <small>Keterangan</small>
                        <b id="prodKetText">Produksi meningkat pesat menuju puncak</b>
                    </div>
                    <div class="metric">
                        <small>Record Master</small>
                        <b>Aktif</b>
                    </div>
                </div>
                <a href="{{ route('master.standards') }}" class="edit" id="btnEditProduksi">
                    ✎ Edit Standar Minggu <span class="lbl-edit-prod">{{ $avgAgeWeeks }}</span>
                </a>
            </div>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 3. VIEW: STANDAR PAKAN (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-standar-pakan" style="{{ $section === 'standar-pakan' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Standar Pakan</h1>
                <p>Master per umur · 13 sampai 90 minggu</p>
            </div>
        </div>

        <div class="label">Acuan Umur</div>
        <section class="card">
            <div class="ctop">
                <div class="round" style="background:#fff1d7;color:#b97400">🌾</div>
                <div>
                    <h3>Standar Pakan</h3>
                    <small>Master per umur · 13 sampai 90 minggu</small>
                </div>
                <a href="{{ route('master.standards') }}" class="chev" title="Kelola Standar Pakan">›</a>
            </div>

            <div class="selectbox">
                <label>PILIH UMUR</label>
                <select id="selectUmurPakan">
                    @for($w = 13; $w <= 90; $w++)
                        <option value="{{ $w }}" {{ $w == $avgAgeWeeks ? 'selected' : '' }}>{{ $w }} minggu</option>
                    @endfor
                </select>
            </div>

            <div class="feed">
                <div class="feedtop">
                    <div class="feedicon">🌾</div>
                    <div>
                        <b id="feedUmurTitle">{{ $avgAgeWeeks }} Minggu · Layer</b>
                        <small>Standar konsumsi per ekor / hari</small>
                    </div>
                </div>
                <div class="feedval">
                    <span>Gram / ekor / hari</span>
                    <strong id="feedGramVal">105 g</strong>
                </div>

                <!-- Dynamic Block Calculations Grid -->
                <div class="metrics" id="feedMetricsGrid">
                    <div class="metric">
                        <small>Standar Pagi</small>
                        <b id="feedPagiVal">52,5 g</b>
                    </div>
                    <div class="metric">
                        <small>Standar Sore</small>
                        <b id="feedSoreVal">52,5 g</b>
                    </div>
                    @foreach($coops as $coop)
                        <div class="metric">
                            <small>{{ $coop->name }}</small>
                            <b id="feedCoop_{{ $coop->id }}">-</b>
                        </div>
                    @endforeach
                    <div class="metric" style="background:#fef7ea; border-color:#fce0b0;">
                        <small style="color:#b46900;">Total Pakan</small>
                        <b id="feedTotalVal" style="color:#92002f;">-</b>
                    </div>
                </div>

                <a href="{{ route('master.standards') }}" class="edit" id="btnEditPakan">
                    ✎ Edit Standar Pakan Minggu <span class="lbl-edit-feed">{{ $avgAgeWeeks }}</span>
                </a>
            </div>

            <div class="note">
                Perhitungan Blok kandang mengikuti populasi aktif masing-masing blok. Angka standar umur 13–90 tetap disimpan sebagai master; layar hanya menampilkan umur yang dipilih.
            </div>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 4. VIEW: STANDAR BB (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-standar-bb" style="{{ $section === 'standar-bb' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Standar BB</h1>
                <p>Master minimum · target · maksimum per umur</p>
            </div>
        </div>

        <div class="label">Acuan Umur</div>
        <section class="card">
            <div class="ctop">
                <div class="round" style="background:#fff1d7;color:#b97400">⚖</div>
                <div>
                    <h3>Standar BB</h3>
                    <small>Master minimum · target · maksimum per umur</small>
                </div>
                <a href="{{ route('master.standards') }}" class="chev" title="Kelola Standar BB">›</a>
            </div>

            <div class="selectbox">
                <label>PILIH UMUR</label>
                <select id="selectUmurBB">
                    @for($w = 13; $w <= 90; $w++)
                        <option value="{{ $w }}" {{ $w == $avgAgeWeeks ? 'selected' : '' }}>{{ $w }} minggu</option>
                    @endfor
                </select>
            </div>

            <div class="metrics">
                <div class="metric">
                    <small>Minimum BB</small>
                    <b id="bbMinVal">1,55 kg</b>
                </div>
                <div class="metric">
                    <small>Target BB</small>
                    <b id="bbTargetVal">1,62 kg</b>
                </div>
                <div class="metric">
                    <small>Maksimum BB</small>
                    <b id="bbMaxVal">1,68 kg</b>
                </div>
                <div class="metric">
                    <small>Fase</small>
                    <b id="bbFaseVal">Layer Produktif</b>
                </div>
            </div>

            <a href="{{ route('master.standards') }}" class="edit">
                ✎ Edit Standar BB
            </a>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 5. VIEW: MASTER FLOCK (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-master-flock" style="{{ $section === 'master-flock' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Master Flock</h1>
                <p>{{ $flocks->count() }} kloter · {{ $coops->count() }} blok</p>
            </div>
        </div>

        <div class="label">Data Pendukung</div>
        <section class="card">
            <div class="ctop">
                <div class="round" style="background:#f2e9ff;color:var(--p)">🐔</div>
                <div>
                    <h3>Master Flock</h3>
                    <small>{{ $flocks->count() }} kloter · {{ $coops->count() }} blok</small>
                </div>
                <a href="{{ route('master.flocks') }}" class="chev" title="Kelola Flock">›</a>
            </div>

            @forelse($flocks as $flock)
                @php
                    $activeInFlock = $flock->coops->sum('active_chickens');
                    $initialInFlock = $flock->initial_population ?: $activeInFlock;
                    $coopNames = $flock->coops->pluck('code')->filter()->implode(' / ') ?: $flock->coops->pluck('name')->implode(' / ');
                @endphp
                <div class="kbox">
                    <div class="khead">
                        <b>{{ $flock->code ?? 'K' . $loop->iteration }} · {{ $flock->breed ?? 'Lohmann Brown' }}</b>
                        <span class="pill" style="{{ $flock->is_active ? '' : 'background:#fee2e2;color:#ef4444;' }}">
                            {{ $flock->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>
                    <div class="kmeta">
                        {{ number_format($initialInFlock, 0, ',', '.') }} awal · {{ number_format($activeInFlock, 0, ',', '.') }} aktif · {{ $coopNames }}
                    </div>
                    <div class="blocks">
                        @foreach($flock->coops as $coop)
                            @php
                                $capacity = $coop->capacity > 0 ? $coop->capacity : $coop->active_chickens;
                                $percent = $capacity > 0 ? min(100, round(($coop->active_chickens / $capacity) * 100)) : 100;
                                $shortName = $coop->code ?: str_replace('Blok ', '', $coop->name);
                            @endphp
                            <div class="block">
                                <b>{{ $shortName }}</b>
                                <p>{{ $coop->active_chickens }} / {{ $capacity }}</p>
                                <div class="bar">
                                    <i style="width: {{ $percent }}%;"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="kbox">
                    <div class="khead"><b>Belum ada data Flock</b></div>
                    <p class="kmeta">Tambahkan flock dan blok kandang pada menu Master Flock.</p>
                </div>
            @endforelse

            <a href="{{ route('master.flocks') }}" class="edit">
                + Kelola Blok & Kloter
            </a>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 6. VIEW: VAKSIN & OBAT (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-vaksin-obat" style="{{ $section === 'vaksin-obat' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Vaksin & Obat</h1>
                <p>Master produk, dosis & jadwal</p>
            </div>
        </div>

        <div class="label">Data Pendukung</div>
        <section class="card">
            <div class="ctop">
                <div class="round" style="background:#ffe9ed;color:var(--r)">💊</div>
                <div>
                    <h3>Vaksin & Obat</h3>
                    <small>Master produk, dosis & jadwal</small>
                </div>
                <a href="{{ route('master.medicines') }}" class="chev" title="Kelola Vaksin & Obat">›</a>
            </div>

            @foreach($medicines as $med)
                <div class="med">
                    <div class="medico">
                        @if(str_contains(strtolower($med['category']), 'vaksin'))
                            💉
                        @elseif(str_contains(strtolower($med['category']), 'vitamin'))
                            ＋
                        @else
                            💊
                        @endif
                    </div>
                    <div class="medtext">
                        <b>{{ $med['name'] }}</b>
                        <p>{{ $med['dosage'] }} · {{ $med['application'] }}</p>
                        <span>{{ $med['schedule'] }}</span>
                    </div>
                </div>
            @endforeach

            <a href="{{ route('master.medicines') }}" class="edit" style="background:#fff; color:var(--m); border:1px solid var(--line); box-shadow:none;">
                + Kelola Vaksin & Obat
            </a>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 7. VIEW: PENGATURAN SISTEM (DEDICATED PAGE) -->
    <!-- ========================================== -->
    <div id="view-pengaturan" style="{{ $section === 'pengaturan' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Pengaturan Sistem</h1>
                <p>Parameter yang dipakai engine</p>
            </div>
        </div>

        <div class="label">Data Pendukung</div>
        <section class="card">
            <div class="ctop" style="margin-bottom: 8px;">
                <div class="round" style="background:#edf1f5;color:#526077">⚙</div>
                <div>
                    <h3>Pengaturan Sistem</h3>
                    <small>Parameter yang dipakai engine</small>
                </div>
                <button type="button" onclick="togglePengaturanEdit()" id="btn-toggle-pengaturan" style="margin-left:auto; background:#f0f4f9; border:1px solid #dce2eb; color:#41506b; border-radius:8px; padding:5px 11px; font-size:10px; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:5px; transition:all .15s;">
                    <span id="btn-toggle-icon">✎</span> <span id="btn-toggle-text">Edit Nilai</span>
                </button>
            </div>

            <!-- 1. VIEW MODE -->
            <div id="pengaturan-view-mode">
                <div class="setting">
                    <div>
                        <b>Isi Tray</b>
                        <small>Konversi butir → tray</small>
                    </div>
                    <span class="val">{{ $systemSettings['isi_tray'] ?? '30 butir' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>Berat Telur</b>
                        <small>Acuan berat per butir</small>
                    </div>
                    <span class="val">{{ $systemSettings['berat_telur'] ?? '0,06 kg' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>Berat Karung Pakan</b>
                        <small>Konversi kg → karung</small>
                    </div>
                    <span class="val">{{ $systemSettings['berat_per_karung'] ?? '50 kg' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>HD Target</b>
                        <small>Target performa</small>
                    </div>
                    <span class="val">{{ $systemSettings['hd_target'] ?? '95%' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>HD Warning</b>
                        <small>Batas peringatan</small>
                    </div>
                    <span class="val">{{ $systemSettings['hd_warning'] ?? '90%' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>HD Minimum</b>
                        <small>Batas minimum</small>
                    </div>
                    <span class="val">{{ $systemSettings['hd_minimum'] ?? '88%' }}</span>
                </div>
                <div class="setting">
                    <div>
                        <b>Reject Maksimum</b>
                        <small>Batas reject</small>
                    </div>
                    <span class="val">{{ $systemSettings['reject_maximum'] ?? '2%' }}</span>
                </div>

                <div style="display:flex; flex-direction:column; gap:6px; margin-top:12px;">
                    <button type="button" onclick="togglePengaturanEdit(true)" class="edit" style="margin-top:0;">
                        ✎ Edit Data Pengaturan Ini
                    </button>
                    <a href="{{ route('master.settings') }}" style="text-align:center; font-size:9.5px; font-weight:700; color:#64748b; padding:8px; border:1px solid #e2e8f0; border-radius:8px; text-decoration:none; background:#f8fafc; transition:all .15s;">
                        ⚙ Buka Halaman Preferensi Aplikasi Lengkap
                    </a>
                </div>
            </div>

            <!-- 2. EDIT FORM MODE -->
            <form id="pengaturan-edit-mode" method="POST" action="{{ route('master.settings.update') }}" style="display:none; margin-top:4px;">
                @csrf
                <input type="hidden" name="from_section" value="pengaturan">

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>Isi Tray</b>
                        <small>Konversi butir → tray</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="1" min="1" name="isi_tray" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['isi_tray'] ?? '30')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">butir</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>Berat Telur</b>
                        <small>Acuan berat per butir</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.001" min="0.001" name="berat_telur" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['berat_telur'] ?? '0.06')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">kg</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>Berat Karung Pakan</b>
                        <small>Konversi kg → karung</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.5" min="1" name="berat_per_karung" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['berat_per_karung'] ?? '50')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">kg</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>HD Target</b>
                        <small>Target performa</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.1" min="0" max="100" name="hd_target" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_target'] ?? '95')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">%</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>HD Warning</b>
                        <small>Batas peringatan</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.1" min="0" max="100" name="hd_warning" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_warning'] ?? '90')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">%</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>HD Minimum</b>
                        <small>Batas minimum</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.1" min="0" max="100" name="hd_minimum" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_minimum'] ?? '88')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">%</span>
                    </div>
                </div>

                <div class="setting" style="padding:7px 0;">
                    <div>
                        <b>Reject Maksimum</b>
                        <small>Batas reject</small>
                    </div>
                    <div style="display:flex; align-items:center; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; width:130px;">
                        <input type="number" step="0.1" min="0" max="100" name="reject_maximum" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['reject_maximum'] ?? '2')) }}" required style="width:100%; border:none; padding:6px 8px; font-size:11px; font-weight:800; color:#1e293b; text-align:right; background:transparent; outline:none;">
                        <span style="font-size:9px; font-weight:800; color:#64748b; padding:0 8px 0 2px; white-space:nowrap; background:#f1f5f9; height:100%; display:flex; align-items:center;">%</span>
                    </div>
                </div>

                <div style="display:flex; gap:8px; margin-top:14px;">
                    <button type="submit" class="edit" style="flex:2; margin-top:0; background:#078f68; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <span>💾</span> Simpan Parameter
                    </button>
                    <button type="button" onclick="togglePengaturanEdit(false)" style="flex:1; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; font-size:9.5px; font-weight:800; color:#475569; cursor:pointer; padding:9px;">
                        Batal
                    </button>
                </div>
            </form>
        </section>
    </div>

</div>

<!-- Interactive Engine for Age-based Standards (13 - 90 Weeks) & Section Switching -->
<script>
// 1. Navigation & Section Switching Engine
let currentSection = '{{ $section ?? "hub" }}';

function getSectionFromUrl() {
    const hash = (window.location.hash || '').replace(/^#/, '');
    const params = new URLSearchParams(window.location.search);
    const qSec = params.get('section');
    
    const raw = qSec || hash;
    if (!raw) return '{{ $section ?? "hub" }}';
    
    // Clean up #card- prefix if present (e.g. card-standar-produksi -> standar-produksi)
    const cleaned = raw.replace(/^card-/, '');
    
    const valid = ['hub', 'standar-produksi', 'standar-pakan', 'standar-bb', 'master-flock', 'vaksin-obat', 'pengaturan'];
    if (valid.includes(cleaned)) {
        return cleaned;
    }
    return 'hub';
}

function openSection(sec, push = true) {
    currentSection = sec;
    const allSections = ['hub', 'standar-produksi', 'standar-pakan', 'standar-bb', 'master-flock', 'vaksin-obat', 'pengaturan'];
    
    allSections.forEach(s => {
        const el = document.getElementById('view-' + s);
        if (el) {
            el.style.display = (s === sec) ? 'block' : 'none';
        }
    });

    if (push) {
        const targetHash = (sec === 'hub') ? '' : '#card-' + sec;
        const targetUrl = window.location.pathname + targetHash;
        window.history.pushState({ section: sec }, '', targetUrl);
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('popstate', function () {
    openSection(getSectionFromUrl(), false);
});

window.addEventListener('hashchange', function () {
    openSection(getSectionFromUrl(), false);
});

// Toggle edit mode untuk Pengaturan Sistem
function togglePengaturanEdit(forceState) {
    const viewEl = document.getElementById('pengaturan-view-mode');
    const editEl = document.getElementById('pengaturan-edit-mode');
    const textEl = document.getElementById('btn-toggle-text');
    const iconEl = document.getElementById('btn-toggle-icon');

    if (!viewEl || !editEl) return;
    const isCurrentlyEditing = (editEl.style.display !== 'none');
    const targetState = (typeof forceState === 'boolean') ? forceState : !isCurrentlyEditing;

    if (targetState) {
        viewEl.style.display = 'none';
        editEl.style.display = 'block';
        if (textEl) textEl.textContent = 'Batal';
        if (iconEl) iconEl.textContent = '✕';
    } else {
        viewEl.style.display = 'block';
        editEl.style.display = 'none';
        if (textEl) textEl.textContent = 'Edit Nilai';
        if (iconEl) iconEl.textContent = '✎';
    }
}

// Run immediately to activate requested section on page load
(function() {
    const initSec = getSectionFromUrl();
    if (initSec && initSec !== 'hub') {
        openSection(initSec, false);
    }
    if (window.location.hash.includes('edit-pengaturan') || (new URLSearchParams(window.location.search)).get('edit') === 'pengaturan') {
        togglePengaturanEdit(true);
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    // 2. Data Populasi Blok Kandang Riil dari Database
    const coops = {!! json_encode($coopSummary) !!};

    // 3. Lookup Dataset Standar Ayam Layer Umur 13 - 90 Minggu
    const standards = {};

    for (let w = 13; w <= 90; w++) {
        let gram, beratTelur, fase, pill, ket, bbMin, bbTarget, bbMax;

        if (w >= 13 && w <= 15) {
            const progress = (w - 13) / 2;
            gram = Math.round(75 + progress * 5);
            beratTelur = '-';
            fase = 'Grower Akhir (Pra-Laying)';
            pill = 'GROWER';
            ket = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam. Jangan menaikkan pakan terlalu ekstrem.';
            bbTarget = 1.10 + progress * 0.33;
            bbMin = bbTarget - 0.07;
            bbMax = bbTarget + 0.07;
        } else if (w >= 16 && w <= 17) {
            const progress = (w - 16) / 1;
            gram = Math.round(85 + progress * 5);
            beratTelur = '-';
            fase = 'Persiapan Bertelur (Pre-Lay)';
            pill = 'PRE-LAY';
            ket = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam.';
            bbTarget = 1.48 + progress * 0.10;
            bbMin = bbTarget - 0.06;
            bbMax = bbTarget + 0.06;
        } else if (w >= 18 && w <= 20) {
            const progress = (w - 18) / 2;
            gram = Math.round(95 + progress * 5);
            beratTelur = +(46 + progress * 9).toFixed(1);
            fase = 'Awal Bertelur (Puncak Naik)';
            pill = 'AWAL BERTELUR';
            ket = 'Ayam membutuhkan energi dan nutrisi tertinggi untuk pembentukan telur pertama dan mencapai puncak.';
            bbTarget = 1.62 + progress * 0.10;
            bbMin = bbTarget - 0.07;
            bbMax = bbTarget + 0.07;
        } else if (w >= 21 && w <= 40) {
            const progress = (w - 21) / 19;
            gram = Math.round(110 + progress * 5);
            beratTelur = +(59.5 + progress * 4).toFixed(1);
            fase = 'Puncak Produksi (Egg Peak)';
            pill = 'PUNCAK PRODUKSI';
            ket = 'Konsumsi pakan stabil di kisaran 110-115 gram. Energi tertinggi dibutuhkan.';
            bbTarget = 1.74 + progress * 0.08;
            bbMin = bbTarget - 0.08;
            bbMax = bbTarget + 0.08;
        } else if (w >= 41 && w <= 60) {
            const progress = (w - 41) / 19;
            gram = Math.round(115 + progress * 5);
            beratTelur = +(64.5 + progress * 0.8).toFixed(1);
            fase = 'Laying Phase 2 (Pasca Puncak)';
            pill = 'PASCA PUNCAK';
            ket = 'Persentase bertelur mulai menurun secara perlahan, namun ukuran telur bertambah besar.';
            bbTarget = 1.83 + progress * 0.10;
            bbMin = bbTarget - 0.09;
            bbMax = bbTarget + 0.09;
        } else {
            const progress = (w - 61) / 29;
            gram = Math.round(115 + progress * 5);
            beratTelur = +(65.3 + progress * 0.7).toFixed(1);
            fase = 'Laying Phase 3 (Fase Akhir) / Afkir';
            pill = 'FASE AKHIR';
            ket = 'Fase akhir produksi sebelum peremajaan. Persentase turun, ukuran besar.';
            bbTarget = 1.94 + progress * 0.08;
            bbMin = bbTarget - 0.09;
            bbMax = bbTarget + 0.09;
        }

        standards[w] = {
            week: w,
            gram: gram,
            beratTelur: beratTelur,
            fase: fase,
            pill: pill,
            ket: ket,
            bbMin: bbMin,
            bbTarget: bbTarget,
            bbMax: bbMax
        };
    }

    // 4. Elemen-elemen DOM
    const selectUmurProduksi = document.getElementById('selectUmurProduksi');
    const selectUmurPakan = document.getElementById('selectUmurPakan');
    const selectUmurBB = document.getElementById('selectUmurBB');

    // Standar Produksi DOM
    const prodUmurTitle = document.getElementById('prodUmurTitle');
    const prodFasePill = document.getElementById('prodFasePill');
    const prodBeratTelur = document.getElementById('prodBeratTelur');
    const prodFaseText = document.getElementById('prodFaseText');
    const prodKetText = document.getElementById('prodKetText');
    const lblEditProd = document.querySelectorAll('.lbl-edit-prod');

    // Standar Pakan DOM
    const feedUmurTitle = document.getElementById('feedUmurTitle');
    const feedGramVal = document.getElementById('feedGramVal');
    const feedPagiVal = document.getElementById('feedPagiVal');
    const feedSoreVal = document.getElementById('feedSoreVal');
    const feedTotalVal = document.getElementById('feedTotalVal');
    const lblEditFeed = document.querySelectorAll('.lbl-edit-feed');

    // Standar BB DOM
    const bbMinVal = document.getElementById('bbMinVal');
    const bbTargetVal = document.getElementById('bbTargetVal');
    const bbMaxVal = document.getElementById('bbMaxVal');
    const bbFaseVal = document.getElementById('bbFaseVal');

    // 5. Update Fungsi Render Standar
    function updateStandardsView(week, source) {
        const std = standards[week] || standards[21];

        // Sinkronisasi dropdown lain jika dipicu dari salah satu
        if (source !== 'prod' && selectUmurProduksi) selectUmurProduksi.value = week;
        if (source !== 'pakan' && selectUmurPakan) selectUmurPakan.value = week;
        if (source !== 'bb' && selectUmurBB) selectUmurBB.value = week;

        // Render Standar Produksi
        if (prodUmurTitle) prodUmurTitle.textContent = `${week} Minggu`;
        if (prodFasePill) {
            prodFasePill.textContent = std.pill;
            if (std.pill === 'GROWER') {
                prodFasePill.style.background = '#eaf2ff';
                prodFasePill.style.color = '#2a78ed';
            } else if (std.pill === 'AWAL BERTELUR') {
                prodFasePill.style.background = '#fef3c7';
                prodFasePill.style.color = '#b45309';
            } else if (std.pill === 'PRODUKSI NAIK' || std.pill === 'PUNCAK PRODUKSI') {
                prodFasePill.style.background = '#e1faf0';
                prodFasePill.style.color = '#078f68';
            } else {
                prodFasePill.style.background = '#f1f5f9';
                prodFasePill.style.color = '#475569';
            }
        }
        if (prodBeratTelur) {
            prodBeratTelur.textContent = std.beratTelur !== '-' ? `${std.beratTelur} g` : '—';
        }
        if (prodFaseText) prodFaseText.textContent = std.fase;
        if (prodKetText) prodKetText.textContent = std.ket;
        lblEditProd.forEach(el => el.textContent = week);

        // Render Standar Pakan
        if (feedUmurTitle) feedUmurTitle.textContent = `${week} Minggu · Layer`;
        if (feedGramVal) feedGramVal.textContent = `${std.gram} g`;
        
        const halfGram = (std.gram / 2).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        if (feedPagiVal) feedPagiVal.textContent = `${halfGram} g`;
        if (feedSoreVal) feedSoreVal.textContent = `${halfGram} g`;

        // Hitung kg pakan per blok kandang
        let totalKg = 0;
        coops.forEach(coop => {
            const coopKg = (coop.active_chickens * std.gram) / 1000;
            totalKg += coopKg;
            const el = document.getElementById(`feedCoop_${coop.id}`);
            if (el) {
                el.textContent = `${coopKg.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
            }
        });

        if (feedTotalVal) {
            feedTotalVal.textContent = `${totalKg.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        }
        lblEditFeed.forEach(el => el.textContent = week);

        // Render Standar BB
        if (bbMinVal) bbMinVal.textContent = `${std.bbMin.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbTargetVal) bbTargetVal.textContent = `${std.bbTarget.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbMaxVal) bbMaxVal.textContent = `${std.bbMax.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbFaseVal) bbFaseVal.textContent = std.fase;
    }

    // 6. Event Listeners pada Dropdown
    if (selectUmurProduksi) {
        selectUmurProduksi.addEventListener('change', function () {
            updateStandardsView(parseInt(this.value), 'prod');
        });
    }

    if (selectUmurPakan) {
        selectUmurPakan.addEventListener('change', function () {
            updateStandardsView(parseInt(this.value), 'pakan');
        });
    }

    if (selectUmurBB) {
        selectUmurBB.addEventListener('change', function () {
            updateStandardsView(parseInt(this.value), 'bb');
        });
    }

    // Inisialisasi awal saat load
    const initialWeek = parseInt(selectUmurProduksi ? selectUmurProduksi.value : 21) || 21;
    updateStandardsView(initialWeek, 'init');
});
</script>
@endsection

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

/* Styling Khusus Split 2 Kloter Standar Pakan */
.master-v6-container .flock-chips {
  display: flex;
  gap: 5px;
  flex-wrap: wrap;
  margin-top: 6px;
}
.master-v6-container .flock-chip {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #475569;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all .15s ease;
  user-select: none;
}
.master-v6-container .flock-chip:hover {
  background: #e2e8f0;
  color: #1e293b;
}
.master-v6-container .flock-chip.active {
  background: #fdf2f4;
  border-color: #f7d0d6;
  color: var(--m);
  font-weight: 800;
}
.master-v6-container .simul-tabs {
  display: flex;
  gap: 6px;
  margin-bottom: 12px;
  overflow-x: auto;
}
.master-v6-container .simul-tab {
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 9.5px;
  font-weight: 700;
  border: 1px solid var(--line);
  background: #fff;
  color: var(--mut);
  cursor: pointer;
  white-space: nowrap;
  transition: all .15s;
}
.master-v6-container .simul-tab:hover {
  border-color: var(--m);
  color: var(--m);
}
.master-v6-container .simul-tab.active {
  background: var(--m);
  color: #fff;
  border-color: var(--m);
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



        <!-- SECTION: Data Utama (7 Menus) -->
        <div class="label">Data Utama</div>
        <div class="menus">


            <!-- 2. Master Flock -->
            <a href="{{ route('master.flocks') }}" class="menu" id="menu-master-flock">
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

            <!-- 7. Hak Akses & Pengguna (Admin Only) -->
            <a href="{{ route('master.permissions') }}" class="menu" id="menu-hak-akses" style="border: 1.5px solid #fecdd3; background: #fff8f9;">
                <div class="ico red" style="background: #fdf2f4; color: #800020; font-size: 16px;">🛡</div>
                <b style="color: #800020;">Hak Akses User</b>
                <p>Toggle izin & pengguna</p>
                <span class="arr" style="color: #800020;">›</span>
            </a>

            <!-- 8. Audit Riwayat & Restore Data (Tergantung Hak Akses Admin) -->
            @if(!auth()->check() || auth()->user()->role === 'admin' || auth()->user()->canAccess('feature_master_audit'))
            <a href="{{ route('master.audit') }}" class="menu" id="menu-audit-riwayat" style="border: 1.5px solid #e9d5ff; background: #faf5ff;">
                <div class="ico purple" style="background: #f3e8ff; color: #7e22ce; font-size: 16px;">📜</div>
                <b style="color: #6b21a8;">Audit Riwayat</b>
                <p>Log aktivitas & restore</p>
                <span class="arr" style="color: #7e22ce;">›</span>
            </a>
            @endif

            <!-- 9. Restore / Import Database Operasional Excel (NF-DAT-002) -->
            <a href="javascript:void(0)" onclick="openRestoreExcelModal()" class="menu" id="menu-restore-excel" style="border: 1.5px solid #a7f3d0; background: #f0fdf4;">
                <div class="ico green" style="background: #dcfce7; color: #15803d; font-size: 16px;">📥</div>
                <b style="color: #166534;">Restore Data Excel</b>
                <p>Upload Excel NF-DAT-002</p>
                <span class="arr" style="color: #15803d;">›</span>
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
                <a href="{{ route('master.standar-produksi') }}" class="chev" title="Buka Master Tabel Standar Produksi">›</a>
            </div>

            <div class="selectbox">
                <label>PILIH UMUR UNTUK DILIHAT</label>
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
                        <small>Target Hen Day</small>
                        <b id="prodHdTargetText">90.0%</b>
                    </div>
                    <div class="metric">
                        <small>Berat Telur</small>
                        <b id="prodBeratTelur">60 g</b>
                    </div>
                    <div class="metric">
                        <small>Fase</small>
                        <b id="prodFaseText">Puncak Produksi</b>
                    </div>
                    <div class="metric">
                        <small>Jenis Pakan</small>
                        <b id="prodFeedTypeText">Layer Phase 1</b>
                    </div>
                </div>
                <div class="metric" style="margin-top:8px; width:100%;">
                    <small>Catatan Manajemen</small>
                    <span id="prodKetText" style="font-size:11px; font-weight:600; color:#475569; display:block; margin-top:3px;">Masa puncak bertelur. Konsumsi pakan stabil.</span>
                </div>
                <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
                    <button type="button" onclick="openModalEditSelectedWeek('prod')" class="edit" id="btnEditProduksi" style="width:100%; text-align:center; background:#fff; color:#92002f; border:1.5px solid #92002f; font-weight:bold; cursor:pointer;">
                        ✎ Edit Standar Minggu <span class="lbl-edit-prod">{{ $avgAgeWeeks }}</span>
                    </button>
                    <a href="{{ route('master.standar-produksi') }}" class="edit" style="width:100%; text-align:center; background:#92002f; color:#fff; font-weight:bold;">
                        📊 Buka Master Tabel Seluruh Minggu (13–90)
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- 3. VIEW: STANDAR PAKAN (DEDICATED PAGE - SPLIT 2 KLOTTER) -->
    <!-- ========================================== -->
    <div id="view-standar-pakan" style="{{ $section === 'standar-pakan' ? '' : 'display:none;' }}">
        <div class="head">
            <a href="{{ route('master.index') }}" onclick="openSection('hub'); return false;" class="back" title="Kembali ke Master Hub">‹</a>
            <div>
                <h1>Standar Pakan</h1>
                <p>Simulasi per Klotter & Blok · 13 sampai 90 minggu</p>
            </div>
        </div>

        @php
            $kgPerKarung = \App\Models\Setting::getKgPerKarung() ?: 50;

            // Kelompokkan kandang berdasarkan Klotter (Flock)
            $flockGroups = [];
            foreach ($flocks as $flock) {
                $fCoops = $flock->coops->where('is_active', true)->values();
                if ($fCoops->isNotEmpty()) {
                    $fAvgAge = (int) round($fCoops->avg('chicken_age_weeks') ?: 21);
                    $flockGroups[$flock->id] = [
                        'id' => $flock->id,
                        'name' => $flock->name,
                        'code' => $flock->code,
                        'coops' => $fCoops,
                        'total_chickens' => (int) $fCoops->sum('active_chickens'),
                        'base_age' => $fAvgAge,
                    ];
                }
            }

            $orphanCoops = $coops->whereNull('flock_id')->where('is_active', true)->values();
            if ($orphanCoops->isNotEmpty()) {
                $oAvgAge = (int) round($orphanCoops->avg('chicken_age_weeks') ?: 21);
                $flockGroups['non_kloter'] = [
                    'id' => 'non_kloter',
                    'name' => 'Non-Kloter',
                    'code' => 'NK',
                    'coops' => $orphanCoops,
                    'total_chickens' => (int) $orphanCoops->sum('active_chickens'),
                    'base_age' => $oAvgAge,
                ];
            }
        @endphp

        <!-- Filter Tab Antar Klotter -->
        <div class="simul-tabs" style="margin-top: 10px;">
            <button type="button" class="simul-tab active" id="tabPakanAll" onclick="filterPakanKloter('all')">
                Semua Klotter (Split {{ count($flockGroups) }})
            </button>
            @foreach($flockGroups as $fId => $fg)
                <button type="button" class="simul-tab" id="tabPakanFlock_{{ $fId }}" onclick="filterPakanKloter('{{ $fId }}')">
                    {{ $fg['name'] }} (Umur {{ $fg['base_age'] }} Mgg)
                </button>
            @endforeach
        </div>

        <!-- Global Future Simulation Bar -->
        <div style="background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 10px 12px; margin-bottom: 12px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px;">
            <div>
                <span style="font-size: 8px; font-weight: 800; color: var(--mut); text-transform: uppercase; letter-spacing: .04em; display: block;">Simulasi Serentak Masa Depan</span>
                <span style="font-size: 10px; font-weight: 800; color: var(--t);">Maju Beberapa Minggu Bersama:</span>
            </div>
            <div class="flock-chips" style="margin-top:0;">
                <button type="button" class="flock-chip" onclick="simulateFutureAll(0)" title="Kembalikan ke umur riil saat ini">↺ Umur Riil</button>
                <button type="button" class="flock-chip" onclick="simulateFutureAll(1)">+1 Minggu</button>
                <button type="button" class="flock-chip" onclick="simulateFutureAll(2)">+2 Minggu</button>
                <button type="button" class="flock-chip" onclick="simulateFutureAll(4)">+4 Minggu</button>
                <button type="button" class="flock-chip" onclick="simulateFutureAll(8)">+8 Minggu</button>
            </div>
        </div>

        <!-- RENDER SETIAP KLOTTER SECARA TERPISAH (SPLIT) -->
        @php
            $grandInitialFeedKg = 0;
        @endphp

        @foreach($flockGroups as $fId => $fg)
            @php
                $fAge = $fg['base_age'];
                $fStd = \App\Services\ProductionStandardService::getStandardForWeek($fAge);
                $fGram = (float) ($fStd['gram_pakan'] ?? 110);
                $fHalf = round($fGram / 2, 1);
                
                $fSubtotalKg = 0;
                $fCoopCalculations = [];
                foreach($fg['coops'] as $coop) {
                    $cAge = (int) $coop->chicken_age_weeks;
                    $cStd = \App\Services\ProductionStandardService::getStandardForWeek($cAge);
                    $cGram = (float) ($cStd['gram_pakan'] ?? 110);
                    $cPop = (int) $coop->active_chickens;
                    $cKg = round(($cPop * $cGram) / 1000, 1);
                    $fSubtotalKg += $cKg;
                    
                    $fCoopCalculations[] = [
                        'coop' => $coop,
                        'kg' => $cKg,
                        'pop' => $cPop,
                        'age' => $cAge,
                        'gram' => $cGram
                    ];
                }
                $grandInitialFeedKg += $fSubtotalKg;
                
                $fKarung = floor($fSubtotalKg / $kgPerKarung);
                $fSisaKg = round(fmod($fSubtotalKg, $kgPerKarung), 1);
                $fKarungText = $fKarung . ' krg' . ($fSisaKg > 0 ? ' + ' . number_format($fSisaKg, 1, ',', '.') . ' kg' : '');
            @endphp

            <div class="label flock-section-label flock-item-{{ $fId }}" style="display:flex; justify-content:space-between; align-items:center;">
                <span>{{ strtoupper($fg['name']) }} ({{ $fg['coops']->pluck('name')->implode(', ') }})</span>
                <span style="font-size: 8.5px; font-weight:800; color:var(--m); background:#fdf2f4; border:1px solid #f7d0d6; padding:2px 7px; border-radius:10px;">
                    Umur Riil: {{ $fg['base_age'] }} Mgg
                </span>
            </div>

            <section class="card flock-section-card flock-item-{{ $fId }}" style="margin-bottom: 16px;">
                <div class="ctop">
                    <div class="round" style="background:#fff1d7;color:#b97400">🌾</div>
                    <div>
                        <h3>{{ $fg['name'] }} · Standar Pakan</h3>
                        <small>{{ number_format($fg['total_chickens'], 0, ',', '.') }} ekor · {{ $fg['coops']->count() }} Blok ({{ $fg['coops']->pluck('name')->implode(', ') }})</small>
                    </div>
                    <a href="{{ route('master.standar-pakan') }}" class="chev" title="Buka Master Tabel Standar Pakan">›</a>
                </div>

                <!-- PILIH UMUR KHUSUS KLOTTER INI -->
                <div class="selectbox">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <label style="margin-bottom:0;">PILIH UMUR {{ strtoupper($fg['name']) }}</label>
                        <span id="flockAgeBadge_{{ $fId }}" style="font-size:9px; font-weight:800; color:#b97400; background:#fef3c7; border:1px solid #fde68a; padding:1px 6px; border-radius:6px;">
                            {{ $fAge }} Minggu
                        </span>
                    </div>
                    <select id="selectUmurPakan_flock_{{ $fId }}" class="select-flock-feed" data-flock-id="{{ $fId }}" onchange="updateFlockFeed('{{ $fId }}', parseInt(this.value))">
                        @for($w = 13; $w <= 90; $w++)
                            <option value="{{ $w }}" {{ $w == $fAge ? 'selected' : '' }}>{{ $w }} minggu {{ $w == $fg['base_age'] ? '(Umur Riil)' : '' }}</option>
                        @endfor
                    </select>

                    <!-- Quick Chips Simulasi Khusus Klotter ini -->
                    <div class="flock-chips">
                        <button type="button" class="flock-chip active" id="chip_current_{{ $fId }}" onclick="setFlockWeek('{{ $fId }}', {{ $fg['base_age'] }})">Saat Ini ({{ $fg['base_age'] }} Mgg)</button>
                        <button type="button" class="flock-chip" id="chip_plus1_{{ $fId }}" onclick="setFlockWeek('{{ $fId }}', {{ $fg['base_age'] + 1 }})">+1 Mgg ({{ $fg['base_age'] + 1 }})</button>
                        <button type="button" class="flock-chip" id="chip_plus2_{{ $fId }}" onclick="setFlockWeek('{{ $fId }}', {{ $fg['base_age'] + 2 }})">+2 Mgg ({{ $fg['base_age'] + 2 }})</button>
                        <button type="button" class="flock-chip" id="chip_plus4_{{ $fId }}" onclick="setFlockWeek('{{ $fId }}', {{ $fg['base_age'] + 4 }})">+4 Mgg ({{ $fg['base_age'] + 4 }})</button>
                        <button type="button" class="flock-chip" id="chip_plus8_{{ $fId }}" onclick="setFlockWeek('{{ $fId }}', {{ $fg['base_age'] + 8 }})">+8 Mgg ({{ $fg['base_age'] + 8 }})</button>
                    </div>
                </div>

                <div class="feed">
                    <div class="feedtop">
                        <div class="feedicon">🌾</div>
                        <div>
                            <b id="feedUmurTitle_{{ $fId }}">{{ $fAge }} Minggu · {{ $fStd['fase'] }}</b>
                            <small>Standar konsumsi {{ $fg['name'] }} per ekor / hari</small>
                        </div>
                    </div>
                    <div class="feedval">
                        <span>Gram / ekor / hari</span>
                        <strong id="feedGramVal_{{ $fId }}">{{ number_format($fGram, 1, ',', '.') }} g</strong>
                    </div>

                    <!-- Dynamic Block Calculations Grid Khusus Klotter ini -->
                    <div class="metrics" id="feedMetricsGrid_{{ $fId }}">
                        <div class="metric">
                            <small>Standar Pagi (50%)</small>
                            <b id="feedPagiVal_{{ $fId }}">{{ number_format($fHalf, 1, ',', '.') }} g</b>
                        </div>
                        <div class="metric">
                            <small>Standar Sore (50%)</small>
                            <b id="feedSoreVal_{{ $fId }}">{{ number_format($fHalf, 1, ',', '.') }} g</b>
                        </div>
                        @foreach($fCoopCalculations as $calc)
                            <div class="metric">
                                <small>{{ $calc['coop']->name }} ({{ number_format($calc['pop'], 0, ',', '.') }} ekor)</small>
                                <b id="feedCoopKg_{{ $calc['coop']->id }}">{{ number_format($calc['kg'], 1, ',', '.') }} kg</b>
                                <div id="feedCoopSub_{{ $calc['coop']->id }}" style="font-size: 9px; color: #64748b; line-height: 1.2; margin-top: 2px;">
                                    (Umur {{ $calc['age'] }} Mgg × {{ rtrim(rtrim(number_format($calc['gram'], 1, ',', '.'), '0'), ',') }} g)
                                </div>
                            </div>
                        @endforeach
                        <div class="metric" style="background:#fef7ea; border-color:#fce0b0; grid-column: span 2;">
                            <small style="color:#b46900;">Subtotal Pakan {{ $fg['name'] }} ({{ number_format($fg['total_chickens'], 0, ',', '.') }} ekor)</small>
                            <b id="flockTotalFeed_{{ $fId }}" style="color:#92002f; font-size: 11px;">
                                {{ number_format($fSubtotalKg, 1, ',', '.') }} kg ({{ $fKarungText }})
                            </b>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
                        <button type="button" onclick="openModalEditSelectedWeek('pakan', document.getElementById('selectUmurPakan_flock_{{ $fId }}').value)" class="edit" style="width:100%; text-align:center; background:#fff; color:#b97400; border:1.5px solid #f59e0b; font-weight:bold; cursor:pointer;">
                            ✎ Edit Standar Pakan Minggu <span id="lblEditFeed_{{ $fId }}">{{ $fAge }}</span>
                        </button>
                    </div>
                </div>
            </section>
        @endforeach

        <!-- KARTU GRAND TOTAL GABUNGAN SELURUH KANDANG -->
        @php
            $grandKarung = floor($grandInitialFeedKg / $kgPerKarung);
            $grandSisaKg = round(fmod($grandInitialFeedKg, $kgPerKarung), 1);
            $grandKarungText = $grandKarung . ' krg' . ($grandSisaKg > 0 ? ' + ' . number_format($grandSisaKg, 1, ',', '.') . ' kg' : '');
        @endphp
        <div class="label">Total Gabungan Seluruh Farm</div>
        <section class="card" style="background: linear-gradient(135deg, #fff 0%, #fff8f9 100%); border: 1.5px solid #f7d0d6;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:8px; border-bottom:1px solid #fee2e6;">
                <div>
                    <h3 style="font-size:12px; font-weight:800; color:#800020; margin:0;">Grand Total Pakan Seluruh Kandang</h3>
                    <small style="font-size:8.5px; color:var(--mut);">Akumulasi seluruh klotter & blok aktif ({{ number_format($totalChickens, 0, ',', '.') }} ekor)</small>
                </div>
                <span style="font-size:16px;">🌾</span>
            </div>
            <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:baseline;">
                <span style="font-size:10px; font-weight:700; color:var(--mut);">Kebutuhan Pakan Gabungan:</span>
                <strong id="grandTotalFeedVal" style="font-size:15px; font-weight:900; color:#800020;">
                    {{ number_format($grandInitialFeedKg, 1, ',', '.') }} kg ({{ $grandKarungText }})
                </strong>
            </div>
            <div style="margin-top:12px;">
                <a href="{{ route('master.standar-pakan') }}" class="edit" style="width:100%; text-align:center; background:#92002f; color:#fff; font-weight:bold; margin-top:0;">
                    📊 Buka Master Tabel Lengkap & Panduan Pakan (13–90 Mgg)
                </a>
            </div>
            <div class="note" style="margin-top:10px;">
                💡 <b>Petunjuk Simulasi:</b> Ubah <i>PILIH UMUR</i> pada Klotter 1 atau Klotter 2 untuk melihat estimasi konsumsi pakan di masa depan. Angka kg pakan masing-masing blok dan subtotal/grand total akan otomatis terhitung secara <i>real-time</i>.
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
                <a href="{{ route('master.standar-bb') }}" class="chev" title="Buka Master Tabel Standar BB">›</a>
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

            <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
                <button type="button" onclick="openModalEditSelectedWeek('bb')" class="edit" id="btnEditBB" style="width:100%; text-align:center; background:#fff; color:#2563eb; border:1.5px solid #3b82f6; font-weight:bold; cursor:pointer;">
                    ✎ Edit Standar BB Minggu <span class="lbl-edit-bb">{{ $avgAgeWeeks }}</span>
                </button>
                <a href="{{ route('master.standar-bb') }}" class="edit" style="width:100%; text-align:center; background:#92002f; color:#fff; font-weight:bold;">
                    📊 Buka Master Tabel Lengkap & Evaluasi BB
                </a>
            </div>
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
    
    const valid = ['hub', 'standar-produksi', 'standar-pakan', 'standar-bb', 'master-flock', 'vaksin-obat'];
    if (valid.includes(cleaned)) {
        return cleaned;
    }
    return 'hub';
}

function openSection(sec, push = true) {
    currentSection = sec;
    const allSections = ['hub', 'standar-produksi', 'standar-pakan', 'standar-bb', 'master-flock', 'vaksin-obat'];
    
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

// Run immediately to activate requested section on page load
(function() {
    const initSec = getSectionFromUrl();
    if (initSec && initSec !== 'hub') {
        openSection(initSec, false);
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    // 2. Data Populasi Blok Kandang Riil dari Database
    const coops = {!! json_encode($coopSummary) !!};
    window.flockCoops = coops;
    window.flockBaseAges = {!! json_encode(isset($flockGroups) ? collect($flockGroups)->mapWithKeys(fn($fg, $k) => [(string)$k => $fg['base_age']]) : []) !!};
    window.flockSimulatedTotals = {};
    window.kgPerKarung = {{ $kgPerKarung ?? 50 }};

    // 3. Lookup Dataset Standar Ayam Layer Umur 13 - 90 Minggu dari Database
    const dbStandards = {!! json_encode($weeklyStandards ?? []) !!};
    window.standards = {};

    // Selalu generate fallback default dataset 13-90 minggu terlebih dahulu
    for (let w = 13; w <= 90; w++) {
        let gram = 110;
        if (w <= 15) gram = 75 + (w - 13) * 2.5;
        else if (w <= 17) gram = 85 + (w - 16) * 5;
        else if (w <= 20) gram = 95 + (w - 18) * 2.5;
        else if (w <= 40) gram = 110 + ((w - 21) / 19) * 5;
        else gram = 115 + ((w - 41) / 49) * 5;

        window.standards[w] = {
            week: w,
            gram: Math.round(gram),
            beratTelur: w >= 18 ? (46 + (w - 18) * 0.5).toFixed(1) : '-',
            hdTarget: w >= 21 ? 94 : (w >= 18 ? 50 : 0),
            feedType: w <= 15 ? 'Grower / Pullet' : (w <= 17 ? 'Pre-Lay' : 'Layer Phase 1'),
            fase: 'Layer',
            pill: 'LAYER',
            ket: 'Standar performa pakan harian',
            bbMin: 1.66,
            bbTarget: 1.74,
            bbMax: 1.82
        };
    }

    if (Array.isArray(dbStandards) && dbStandards.length > 0) {
        dbStandards.forEach(std => {
            window.standards[std.week] = {
                week: std.week,
                gram: parseFloat(std.feed_gram) || 0,
                beratTelur: std.egg_weight && std.egg_weight !== '-' ? std.egg_weight : '-',
                hdTarget: parseFloat(std.hd_target) || 0,
                feedType: std.feed_type || 'Layer Phase 1',
                fase: std.phase,
                pill: std.pill,
                ket: std.description,
                bbMin: parseFloat(std.weight_min) || 0,
                bbTarget: parseFloat(std.weight_target) || 0,
                bbMax: parseFloat(std.weight_max) || 0
            };
        });
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
    window.updateStandardsView = function(week, source) {
        const std = window.standards[week] || window.standards[21];

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
        const prodHdTargetText = document.getElementById('prodHdTargetText');
        const prodFeedTypeText = document.getElementById('prodFeedTypeText');
        if (prodHdTargetText) {
            prodHdTargetText.textContent = std.hdTarget !== undefined ? `${parseFloat(std.hdTarget).toFixed(1)}%` : '0.0%';
        }
        if (prodFeedTypeText) {
            prodFeedTypeText.textContent = std.feedType || 'Layer Phase 1';
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

        // Catatan: feedCoop dan feedTotalVal tidak lagi di-update secara dinamis via dropdown 
        // karena harus menampilkan total berdasarkan UMUR ASLI / REAL masing-masing blok (bukan umur yang dipilih).
        lblEditFeed.forEach(el => el.textContent = week);

        // Render Standar BB
        if (bbMinVal) bbMinVal.textContent = `${std.bbMin.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbTargetVal) bbTargetVal.textContent = `${std.bbTarget.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbMaxVal) bbMaxVal.textContent = `${std.bbMax.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
        if (bbFaseVal) bbFaseVal.textContent = std.fase;
        const lblEditBB = document.querySelectorAll('.lbl-edit-bb');
        lblEditBB.forEach(el => el.textContent = week);
    }

    // 6. Event Listeners pada Dropdown
    if (selectUmurProduksi) {
        selectUmurProduksi.addEventListener('change', function () {
            window.updateStandardsView(parseInt(this.value), 'prod');
        });
    }

    if (selectUmurPakan) {
        selectUmurPakan.addEventListener('change', function () {
            window.updateStandardsView(parseInt(this.value), 'pakan');
        });
    }

    if (selectUmurBB) {
        selectUmurBB.addEventListener('change', function () {
            window.updateStandardsView(parseInt(this.value), 'bb');
        });
    }

    // Inisialisasi awal saat load
    const initialWeek = parseInt(selectUmurProduksi ? selectUmurProduksi.value : 21) || 21;
    window.updateStandardsView(initialWeek, 'init');

    // Inisialisasi simulasi pakan per-klotter saat load
    if (window.flockBaseAges) {
        Object.keys(window.flockBaseAges).forEach(function(fId) {
            const age = parseInt(window.flockBaseAges[fId]) || 21;
            updateFlockFeed(fId, age);
        });
    }
});

// Filter Tab Tampilan Klotter Standar Pakan
function filterPakanKloter(flockId) {
    const tabs = document.querySelectorAll('.simul-tab');
    tabs.forEach(function(t) { t.classList.remove('active'); });

    const activeTab = document.getElementById(flockId === 'all' ? 'tabPakanAll' : 'tabPakanFlock_' + flockId);
    if (activeTab) activeTab.classList.add('active');

    const allCards = document.querySelectorAll('.flock-section-card');
    const allLabels = document.querySelectorAll('.flock-section-label');

    if (flockId === 'all') {
        allCards.forEach(function(c) { c.style.display = 'block'; });
        allLabels.forEach(function(l) { l.style.display = 'flex'; });
    } else {
        allCards.forEach(function(c) {
            if (c.classList.contains('flock-item-' + flockId)) {
                c.style.display = 'block';
            } else {
                c.style.display = 'none';
            }
        });
        allLabels.forEach(function(l) {
            if (l.classList.contains('flock-item-' + flockId)) {
                l.style.display = 'flex';
            } else {
                l.style.display = 'none';
            }
        });
    }
}

// Simulasi Update Pakan Per-Klotter
function updateFlockFeed(flockId, week) {
    week = parseInt(week) || 21;
    const std = (window.standards && window.standards[week]) ? window.standards[week] : {
        week: week,
        gram: 110,
        fase: 'Layer',
        pill: 'LAYER'
    };

    // Update dropdown jika berbeda
    const selectEl = document.getElementById('selectUmurPakan_flock_' + flockId);
    if (selectEl && parseInt(selectEl.value) !== week) {
        selectEl.value = week;
    }

    // Update judul, badge umur, gram per ekor, serta pembagian 50-50 pagi & sore
    const titleEl = document.getElementById('feedUmurTitle_' + flockId);
    if (titleEl) titleEl.textContent = `${week} Minggu · ${std.fase || 'Layer'}`;

    const badgeEl = document.getElementById('flockAgeBadge_' + flockId);
    if (badgeEl) badgeEl.textContent = `${week} Minggu`;

    const gramValEl = document.getElementById('feedGramVal_' + flockId);
    if (gramValEl) gramValEl.textContent = `${std.gram.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} g`;

    const halfGram = (std.gram / 2).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' g';
    const pagiEl = document.getElementById('feedPagiVal_' + flockId);
    if (pagiEl) pagiEl.textContent = halfGram;
    const soreEl = document.getElementById('feedSoreVal_' + flockId);
    if (soreEl) soreEl.textContent = halfGram;

    const lblEditEl = document.getElementById('lblEditFeed_' + flockId);
    if (lblEditEl) lblEditEl.textContent = week;

    // Kalkulasi konsumsi pakan masing-masing blok di dalam klotter ini
    let flockSubtotalKg = 0;
    const flockCoops = (window.flockCoops || []).filter(function(c) {
        if (flockId === 'non_kloter') return !c.flock_id;
        return String(c.flock_id) === String(flockId);
    });

    flockCoops.forEach(function(coop) {
        const cChickens = parseInt(coop.active_chickens) || 0;
        const cKg = Math.round(((cChickens * std.gram) / 1000) * 10) / 10;
        flockSubtotalKg += cKg;

        const coopKgEl = document.getElementById('feedCoopKg_' + coop.id);
        if (coopKgEl) {
            coopKgEl.textContent = `${cKg.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} kg`;
        }
        const coopSubEl = document.getElementById('feedCoopSub_' + coop.id);
        if (coopSubEl) {
            coopSubEl.textContent = `(Umur ${week} Mgg × ${std.gram} g)`;
        }
    });

    flockSubtotalKg = Math.round(flockSubtotalKg * 10) / 10;
    if (!window.flockSimulatedTotals) window.flockSimulatedTotals = {};
    window.flockSimulatedTotals[flockId] = flockSubtotalKg;

    // Hitung konversi Karung (50 kg)
    const kgPerKarung = window.kgPerKarung || 50;
    const karung = Math.floor(flockSubtotalKg / kgPerKarung);
    const sisaKg = Math.round((flockSubtotalKg % kgPerKarung) * 10) / 10;
    const karungText = karung + ' krg' + (sisaKg > 0 ? ' + ' + sisaKg.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' kg' : '');

    const flockTotalEl = document.getElementById('flockTotalFeed_' + flockId);
    if (flockTotalEl) {
        flockTotalEl.textContent = `${flockSubtotalKg.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} kg (${karungText})`;
    }

    // Toggle active state chip simulasi cepat
    const baseAge = (window.flockBaseAges && window.flockBaseAges[flockId]) !== undefined ? parseInt(window.flockBaseAges[flockId]) : 21;
    const chipOffsets = {
        'chip_current_': 0,
        'chip_plus1_': 1,
        'chip_plus2_': 2,
        'chip_plus4_': 4,
        'chip_plus8_': 8
    };
    Object.keys(chipOffsets).forEach(function(prefix) {
        const chip = document.getElementById(prefix + flockId);
        if (chip) {
            if (week === baseAge + chipOffsets[prefix]) {
                chip.classList.add('active');
            } else {
                chip.classList.remove('active');
            }
        }
    });

    recalculateGrandTotalPakan();
}

function setFlockWeek(flockId, week) {
    updateFlockFeed(flockId, week);
}

function simulateFutureAll(weeksToAdd) {
    if (!window.flockBaseAges) return;
    Object.keys(window.flockBaseAges).forEach(function(fId) {
        const base = parseInt(window.flockBaseAges[fId]) || 21;
        const target = Math.min(90, Math.max(13, base + weeksToAdd));
        updateFlockFeed(fId, target);
    });
}

function recalculateGrandTotalPakan() {
    let grandKg = 0;
    if (window.flockSimulatedTotals) {
        Object.values(window.flockSimulatedTotals).forEach(function(kg) {
            grandKg += (parseFloat(kg) || 0);
        });
    }
    grandKg = Math.round(grandKg * 10) / 10;
    const kgPerKarung = window.kgPerKarung || 50;
    const grandKarung = Math.floor(grandKg / kgPerKarung);
    const grandSisaKg = Math.round((grandKg % kgPerKarung) * 10) / 10;
    const grandKarungText = grandKarung + ' krg' + (grandSisaKg > 0 ? ' + ' + grandSisaKg.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' kg' : '');

    const el = document.getElementById('grandTotalFeedVal');
    if (el) {
        el.textContent = `${grandKg.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} kg (${grandKarungText})`;
    }
}

// Helper Buka Modal Edit Minggu Terpilih dari Card Hub
function openModalEditSelectedWeek(type, customWeek = null) {
    let week = 21;
    if (customWeek !== null && customWeek !== undefined) {
        week = parseInt(customWeek) || 21;
    } else if (type === 'prod') {
        const sel = document.getElementById('selectUmurProduksi');
        if (sel) week = parseInt(sel.value) || 21;
    } else if (type === 'pakan') {
        const sel = document.getElementById('selectUmurPakan');
        if (sel) week = parseInt(sel.value) || 21;
    } else if (type === 'bb') {
        const sel = document.getElementById('selectUmurBB');
        if (sel) week = parseInt(sel.value) || 21;
    }

    const std = (typeof window.standards !== 'undefined' && window.standards[week]) ? window.standards[week] : {
        week: week,
        fase: 'Puncak Produksi (Egg Peak)',
        pill: 'PUNCAK PRODUKSI',
        hdTarget: 90,
        beratTelur: '59.5',
        gram: 110,
        feedType: 'Layer Phase 1',
        bbMin: 1.66,
        bbTarget: 1.74,
        bbMax: 1.82,
        ket: ''
    };

    document.getElementById('hubModalWeekBadge').textContent = week;
    document.getElementById('hubModalWeekTitle').textContent = week;
    document.getElementById('hubInputPhase').value = std.fase || '';
    document.getElementById('hubInputPill').value = std.pill || '';
    document.getElementById('hubInputHdTarget').value = std.hdTarget !== undefined ? std.hdTarget : 0;
    document.getElementById('hubInputEggWeight').value = std.beratTelur !== '-' ? std.beratTelur : '';
    document.getElementById('hubInputFeedGram').value = std.gram || 0;
    document.getElementById('hubInputFeedType').value = std.feedType || '';
    document.getElementById('hubInputWeightMin').value = std.bbMin || 0;
    document.getElementById('hubInputWeightTarget').value = std.bbTarget || 0;
    document.getElementById('hubInputWeightMax').value = std.bbMax || 0;
    document.getElementById('hubInputDescription').value = std.ket || '';

    // Tampilkan hanya form yang sesuai
    document.getElementById('hubGroupPhase').style.display = (type === 'prod' || type === 'all') ? 'grid' : 'none';
    document.getElementById('hubGroupProduksi').style.display = (type === 'prod' || type === 'all') ? 'block' : 'none';
    document.getElementById('hubGroupPakan').style.display = (type === 'pakan' || type === 'all') ? 'block' : 'none';
    document.getElementById('hubGroupBB').style.display = (type === 'bb' || type === 'all') ? 'block' : 'none';
    document.getElementById('hubGroupCatatan').style.display = (type === 'prod' || type === 'all') ? 'block' : 'none';

    const form = document.getElementById('formHubEditStandar');
    form.action = `/master/weekly-standards/${week}/update`;

    const modal = document.getElementById('modalHubEditStandar');
    modal.style.display = 'flex';
}

function closeModalHubEditStandar() {
    const modal = document.getElementById('modalHubEditStandar');
    if (modal) modal.style.display = 'none';
}

function handleHubModalSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('formHubEditStandar');
    const btn = document.getElementById('hubBtnSubmitModal');
    const origText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        return res.json().then(data => {
            if (!res.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join('\\n') : data.message || 'Gagal menyimpan';
                throw new Error(errors);
            }
            return data;
        });
    })
    .then(data => {
        btn.disabled = false;
        btn.textContent = origText;
        closeModalHubEditStandar();

        // Update in-memory dataset
        const w = data.data.week;
        if (typeof window.standards !== 'undefined') {
            window.standards[w] = {
                week: w,
                gram: parseFloat(data.data.feed_gram) || 0,
                beratTelur: data.data.egg_weight && data.data.egg_weight !== '-' ? data.data.egg_weight : '-',
                hdTarget: parseFloat(data.data.hd_target) || 0,
                feedType: data.data.feed_type || 'Layer Phase 1',
                fase: data.data.phase,
                pill: data.data.pill,
                ket: data.data.description,
                bbMin: parseFloat(data.data.weight_min) || 0,
                bbTarget: parseFloat(data.data.weight_target) || 0,
                bbMax: parseFloat(data.data.weight_max) || 0
            };
            window.updateStandardsView(w, 'modal_saved');

            // Refresh flock feed jika ada klotter yang sedang melihat minggu ini
            if (window.flockBaseAges) {
                Object.keys(window.flockBaseAges).forEach(function(fId) {
                    const sel = document.getElementById('selectUmurPakan_flock_' + fId);
                    if (sel && parseInt(sel.value) === w) {
                        updateFlockFeed(fId, w);
                    }
                });
            }
        }

        alert(`Standar minggu ke-${w} berhasil diperbarui!`);
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = origText;
        alert("Gagal Menyimpan:\n" + err.message);
    });
}

/* ========================================================
   RESTORE DATA EXCEL (NF-DAT-002) HANDLERS
======================================================== */
function openRestoreExcelModal() {
    const modal = document.getElementById('modalRestoreExcel');
    if (modal) {
        modal.style.display = 'flex';
        const form = document.getElementById('formRestoreExcel');
        if (form) form.reset();
        const fileInfo = document.getElementById('excelFileInfo');
        if (fileInfo) fileInfo.style.display = 'none';
        const dropText = document.getElementById('excelDropText');
        if (dropText) dropText.style.display = 'block';
        const progressBox = document.getElementById('excelProgressBox');
        if (progressBox) progressBox.style.display = 'none';
        const resultBox = document.getElementById('excelResultBox');
        if (resultBox) resultBox.style.display = 'none';
        const btn = document.getElementById('btnSubmitRestoreExcel');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '📥 Mulai Restore ke Database';
        }
    }
}

function closeRestoreExcelModal() {
    const modal = document.getElementById('modalRestoreExcel');
    if (modal) {
        modal.style.display = 'none';
    }
}

function handleExcelFileSelect(input) {
    const file = input.files[0];
    const infoBox = document.getElementById('excelFileInfo');
    const nameEl = document.getElementById('excelFileName');
    const sizeEl = document.getElementById('excelFileSize');
    const dropText = document.getElementById('excelDropText');

    if (file) {
        nameEl.textContent = file.name;
        sizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB';
        infoBox.style.display = 'flex';
        dropText.style.display = 'none';
    } else {
        infoBox.style.display = 'none';
        dropText.style.display = 'block';
    }
}

function handleExcelRestoreSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const fileInput = document.getElementById('excelFileInput');
    if (!fileInput.files.length) {
        alert('Silakan pilih file Excel (.xlsx, .xls) atau CSV terlebih dahulu.');
        return;
    }

    const btn = document.getElementById('btnSubmitRestoreExcel');
    const progressBox = document.getElementById('excelProgressBox');
    const resultBox = document.getElementById('excelResultBox');

    btn.disabled = true;
    btn.innerHTML = '<span style="display:inline-block; animation:spin 1s linear infinite;">⏳</span> Memproses Data...';
    progressBox.style.display = 'block';
    resultBox.style.display = 'none';

    const formData = new FormData(form);

    fetch("{{ route('master.restore-excel') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || !data.success) {
            throw new Error(data.message || 'Gagal memproses file Excel.');
        }
        return data;
    })
    .then(data => {
        progressBox.style.display = 'none';
        resultBox.style.display = 'block';
        
        // Tampilkan statistik
        document.getElementById('resProcessedRows').textContent = data.stats.processed_rows || 0;
        document.getElementById('resEggTotal').textContent = data.stats.egg_total || 0;
        document.getElementById('resFeedTotal').textContent = data.stats.feed_total || 0;
        document.getElementById('resMortalityTotal').textContent = data.stats.mortality_total || 0;
        document.getElementById('resCoopsCount').textContent = data.stats.coops_count || 0;
        document.getElementById('resDatesCount').textContent = data.stats.dates_count || 0;

        btn.disabled = false;
        btn.innerHTML = '✔ Selesai (Restore File Lain)';
    })
    .catch(err => {
        progressBox.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '📥 Mulai Restore ke Database';
        alert('Terjadi Kesalahan:\n' + err.message);
    });
}
</script>

<!-- ======================================================== -->
<!-- MODAL EDIT STANDAR UNTUK CARD MASTER HUB -->
<!-- ======================================================== -->
<div id="modalHubEditStandar" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.6); backdrop-filter:blur(3px); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:18px; max-width:480px; width:100%; padding:20px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.15); max-height:90vh; overflow-y:auto; font-family:Inter, sans-serif; box-sizing:border-box;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:38px; height:38px; border-radius:10px; background:#fdf2f4; color:#92002f; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:16px; border:1px solid #fce7ec;">
                    <span id="hubModalWeekBadge">21</span>
                </div>
                <div>
                    <h3 style="margin:0; font-size:15px; font-weight:900; color:#0f172a;">Edit Standar Minggu <span id="hubModalWeekTitle">21</span></h3>
                    <p style="margin:2px 0 0; font-size:11px; color:#64748b;">Perbarui parameter standar acuan mingguan</p>
                </div>
            </div>
            <button type="button" onclick="closeModalHubEditStandar()" style="border:none; background:#f1f5f9; color:#64748b; width:30px; height:30px; border-radius:8px; cursor:pointer; font-weight:bold; font-size:14px;">✕</button>
        </div>

        <form id="formHubEditStandar" method="POST" action="" style="margin-top:14px; display:flex; flex-direction:column; gap:12px;" onsubmit="handleHubModalSubmit(event)">
            @csrf
            <div id="hubGroupPhase" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#334155; margin-bottom:4px;">Fase *</label>
                    <input type="text" id="hubInputPhase" name="phase" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:600; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; color:#334155; margin-bottom:4px;">Pill Label *</label>
                    <input type="text" id="hubInputPill" name="pill" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:600; box-sizing:border-box;">
                </div>
            </div>

            <!-- Produksi -->
            <div id="hubGroupProduksi" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:10px;">
                <div style="font-size:11px; font-weight:800; color:#166534; margin-bottom:8px;">🥚 Standar Produksi Telur</div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Target HD (%) *</label>
                        <input type="number" step="0.1" min="0" max="100" id="hubInputHdTarget" name="hd_target" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:800; color:#15803d; box-sizing:border-box; background:#fff;">
                    </div>
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Berat Telur (Gram)</label>
                        <input type="text" id="hubInputEggWeight" name="egg_weight" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:700; box-sizing:border-box; background:#fff;">
                    </div>
                </div>
            </div>

            <!-- Pakan -->
            <div id="hubGroupPakan" style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:10px;">
                <div style="font-size:11px; font-weight:800; color:#92400e; margin-bottom:8px;">🌾 Standar Kebutuhan Pakan</div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Gram / Ekor / Hari *</label>
                        <input type="number" step="0.5" min="0" max="300" id="hubInputFeedGram" name="feed_gram" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:800; color:#b45309; box-sizing:border-box; background:#fff;">
                    </div>
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Jenis Pakan (Umum)</label>
                        <input type="text" id="hubInputFeedType" name="feed_type" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:12px; font-weight:700; box-sizing:border-box; background:#fff;">
                    </div>
                </div>
            </div>

            <!-- BB -->
            <div id="hubGroupBB" style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:10px;">
                <div style="font-size:11px; font-weight:800; color:#1e40af; margin-bottom:8px;">⚖ Standar Bobot Badan (BB Kg)</div>
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:8px;">
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Min</label>
                        <input type="number" step="0.01" min="0" max="10" id="hubInputWeightMin" name="weight_min" style="width:100%; padding:8px 6px; border-radius:8px; border:1px solid #cbd5e1; font-size:11px; font-weight:700; box-sizing:border-box; background:#fff;">
                    </div>
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Target *</label>
                        <input type="number" step="0.01" min="0" max="10" id="hubInputWeightTarget" name="weight_target" style="width:100%; padding:8px 6px; border-radius:8px; border:1px solid #cbd5e1; font-size:11px; font-weight:800; color:#1d4ed8; box-sizing:border-box; background:#fff;">
                    </div>
                    <div>
                        <label style="display:block; font-size:10px; font-weight:700; color:#334155; margin-bottom:3px;">Max</label>
                        <input type="number" step="0.01" min="0" max="10" id="hubInputWeightMax" name="weight_max" style="width:100%; padding:8px 6px; border-radius:8px; border:1px solid #cbd5e1; font-size:11px; font-weight:700; box-sizing:border-box; background:#fff;">
                    </div>
                </div>
            </div>

            <div id="hubGroupCatatan">
                <label style="display:block; font-size:11px; font-weight:700; color:#334155; margin-bottom:4px;">Catatan Manajemen</label>
                <textarea id="hubInputDescription" name="description" rows="2" style="width:100%; padding:8px 10px; border-radius:10px; border:1px solid #cbd5e1; font-size:11px; box-sizing:border-box; font-family:inherit;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
                <button type="button" onclick="closeModalHubEditStandar()" style="padding:8px 14px; border-radius:10px; border:1px solid #cbd5e1; background:#fff; font-size:12px; font-weight:700; cursor:pointer;">Batal</button>
                <button type="submit" id="hubBtnSubmitModal" style="padding:8px 18px; border-radius:10px; border:none; background:#92002f; color:#fff; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 4px rgba(146,0,47,0.2);">Simpan Standar</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL RESTORE DATA EXCEL OPERASIONAL (NF-DAT-002) -->
<!-- ======================================================== -->
<div id="modalRestoreExcel" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:20px; max-width:480px; width:100%; padding:22px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); max-height:92vh; overflow-y:auto; font-family:Inter, sans-serif; box-sizing:border-box;">
        <!-- Header Modal -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:14px; border-bottom:1px solid #e2e8f0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; border-radius:12px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:20px; border:1px solid #bbf7d0; flex-shrink:0;">
                    📥
                </div>
                <div>
                    <h3 style="margin:0; font-size:16px; font-weight:900; color:#0f172a; line-height:1.2;">Restore Data Excel</h3>
                    <p style="margin:3px 0 0; font-size:11px; color:#64748b;">Upload file database operasional (NF-DAT-002)</p>
                </div>
            </div>
            <button type="button" onclick="closeRestoreExcelModal()" style="border:none; background:#f1f5f9; color:#64748b; width:32px; height:32px; border-radius:9px; cursor:pointer; font-weight:bold; font-size:15px; display:flex; align-items:center; justify-content:center; transition:background .15s;">✕</button>
        </div>

        <!-- Kolom yang didukung -->
        <div style="margin-top:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px;">
            <div style="font-size:11px; font-weight:800; color:#334155; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                <span>📋</span> Data yang Otomatis Masuk Database:
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <span style="font-size:10px; font-weight:700; background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:6px;">📅 Tanggal</span>
                <span style="font-size:10px; font-weight:700; background:#f3e8ff; color:#7e22ce; padding:3px 8px; border-radius:6px;">🐔 Blok Kandang (A, B, C...)</span>
                <span style="font-size:10px; font-weight:700; background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:6px;">🥚 Baik · Retak · Pecah</span>
                <span style="font-size:10px; font-weight:700; background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:6px;">🌾 Pakan Pagi & Sore</span>
                <span style="font-size:10px; font-weight:700; background:#fee2e2; color:#b91c1c; padding:3px 8px; border-radius:6px;">💀 Ayam Mati</span>
                <span style="font-size:10px; font-weight:700; background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px;">⏰ Jam Input</span>
            </div>
            <p style="margin:8px 0 0; font-size:10px; color:#64748b; line-height:1.4;">
                Mendukung format template <b>NF-DAT-002</b>. Data langsung dimasukkan ke Blok Kandang yang sudah ada di sistem (Blok A, B, C otomatis ke Kloter 1), tanpa perlu mencocokkan kloter dari Excel.
            </p>
        </div>

        <!-- Form Upload -->
        <form id="formRestoreExcel" method="POST" enctype="multipart/form-data" onsubmit="handleExcelRestoreSubmit(event)" style="margin-top:14px; display:flex; flex-direction:column; gap:12px;">
            @csrf

            <!-- Dropzone Area -->
            <div style="position:relative; border:2px dashed #cbd5e1; border-radius:14px; padding:20px; text-align:center; background:#fafbfc; transition:all .2s; cursor:pointer;" onclick="document.getElementById('excelFileInput').click()">
                <input type="file" id="excelFileInput" name="file" accept=".xlsx,.xls,.csv" style="display:none;" onchange="handleExcelFileSelect(this)">
                
                <div id="excelDropText">
                    <div style="font-size:32px; margin-bottom:6px;">📂</div>
                    <div style="font-size:12px; font-weight:800; color:#1e293b;">Pilih File Excel / Tarik ke Sini</div>
                    <div style="font-size:10.5px; color:#64748b; margin-top:3px;">Format .xlsx, .xls, atau .csv (Maksimal 25MB)</div>
                </div>

                <div id="excelFileInfo" style="display:none; align-items:center; justify-content:center; gap:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:10px;">
                    <span style="font-size:20px;">📄</span>
                    <div style="text-align:left;">
                        <b id="excelFileName" style="font-size:11.5px; color:#166534; display:block; word-break:break-all;">file.xlsx</b>
                        <small id="excelFileSize" style="font-size:10px; color:#15803d;">0 KB</small>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); document.getElementById('excelFileInput').value=''; handleExcelFileSelect(document.getElementById('excelFileInput'));" style="margin-left:auto; border:none; background:#fee2e2; color:#b91c1c; border-radius:6px; padding:4px 8px; font-size:10px; font-weight:800; cursor:pointer;">Ganti</button>
                </div>
            </div>

            <!-- Opsi Timpa Data -->
            <label style="display:flex; align-items:flex-start; gap:8px; font-size:11px; color:#334155; cursor:pointer; background:#f8fafc; padding:10px; border-radius:10px; border:1px solid #e2e8f0;">
                <input type="checkbox" name="overwrite" value="1" checked style="margin-top:2px; accent-color:#15803d; width:15px; height:15px;">
                <span>
                    <b>Perbarui (Update) data jika sudah ada</b>
                    <small style="display:block; color:#64748b; font-size:9.5px; margin-top:1px;">Jika data tanggal & blok yang sama sudah ada di database, nilai akan diperbarui sesuai Excel.</small>
                </span>
            </label>

            <!-- Loading Progress Box -->
            <div id="excelProgressBox" style="display:none; background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:14px; text-align:center;">
                <div style="font-size:12px; font-weight:800; color:#166534; margin-bottom:8px;">Sedang membaca dan memproses baris Excel...</div>
                <div style="height:6px; background:#dcfce7; border-radius:6px; overflow:hidden; position:relative;">
                    <div style="height:100%; width:100%; background:linear-gradient(90deg, #15803d, #22c55e); border-radius:6px; animation:pulse 1.5s infinite;"></div>
                </div>
                <div style="font-size:10px; color:#15803d; margin-top:6px;">Harap tunggu, jangan menutup jendela ini.</div>
            </div>

            <!-- Hasil Summary Box -->
            <div id="excelResultBox" style="display:none; background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:14px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span style="font-size:18px;">🎉</span>
                    <b style="font-size:12.5px; color:#166534;">Restore Data Berhasil!</b>
                </div>
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:8px;">
                    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:8px; text-align:center;">
                        <small style="font-size:9.5px; color:#64748b; display:block;">Baris Diproses</small>
                        <b id="resProcessedRows" style="font-size:15px; color:#166534;">0</b>
                    </div>
                    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:8px; text-align:center;">
                        <small style="font-size:9.5px; color:#64748b; display:block;">Data Telur</small>
                        <b id="resEggTotal" style="font-size:15px; color:#15803d;">0</b>
                    </div>
                    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:8px; text-align:center;">
                        <small style="font-size:9.5px; color:#64748b; display:block;">Data Pakan</small>
                        <b id="resFeedTotal" style="font-size:15px; color:#b45309;">0</b>
                    </div>
                    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:8px; text-align:center;">
                        <small style="font-size:9.5px; color:#64748b; display:block;">Data Mortalitas</small>
                        <b id="resMortalityTotal" style="font-size:15px; color:#b91c1c;">0</b>
                    </div>
                </div>
                <div style="font-size:10px; color:#166534; margin-top:8px; text-align:center; font-weight:600;">
                    Terdampak: <span id="resCoopsCount">0</span> Blok Kandang & <span id="resDatesCount">0</span> Hari Transaksi
                </div>
            </div>

            <!-- Download Template & Tombol Aksi -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:6px; flex-wrap:wrap; gap:8px;">
                <a href="{{ route('master.restore-excel.template') }}" target="_blank" style="font-size:11px; font-weight:700; color:#0369a1; text-decoration:none; display:flex; align-items:center; gap:4px; padding:6px 0;">
                    <span>📥</span> Unduh Format Contoh
                </a>

                <div style="display:flex; gap:8px; margin-left:auto;">
                    <button type="button" onclick="closeRestoreExcelModal()" style="padding:9px 14px; border-radius:10px; border:1px solid #cbd5e1; background:#fff; font-size:11.5px; font-weight:700; color:#475569; cursor:pointer;">Tutup</button>
                    <button type="submit" id="btnSubmitRestoreExcel" style="padding:9px 18px; border-radius:10px; border:none; background:#15803d; color:#fff; font-size:11.5px; font-weight:800; cursor:pointer; box-shadow:0 2px 5px rgba(21,128,61,0.25); display:flex; align-items:center; gap:6px;">
                        📥 Mulai Restore ke Database
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


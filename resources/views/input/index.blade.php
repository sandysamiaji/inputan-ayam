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
  --card: #fff;
  --green: #079669;
  --red: #e11d48;
}

.input-mobile-app {
  width: 100%;
  max-width: 440px;
  margin: 0 auto;
  font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  color: var(--t);
  padding-bottom: 20px;
}

.input-mobile-app button,
.input-mobile-app input,
.input-mobile-app select,
.input-mobile-app textarea {
  font: inherit;
}

/* Hero Section */
.input-mobile-app .hero {
  background: linear-gradient(135deg, #fff, #fff8fa);
  border: 1px solid #f1dce4;
  border-radius: 17px;
  padding: 16px;
  box-shadow: 0 2px 6px rgba(146, 0, 47, 0.04);
}
.input-mobile-app .kicker {
  display: flex;
  gap: 6px;
  align-items: center;
  margin-bottom: 8px;
}
.input-mobile-app .pill {
  font-size: 9px;
  border-radius: 20px;
  padding: 5px 8px;
  background: #fff1f5;
  color: var(--m);
  font-weight: 700;
}
.input-mobile-app .pill.green {
  background: #ecfdf5;
  color: var(--green);
}
.input-mobile-app .hero h1 {
  font-size: 20px;
  font-weight: 800;
  margin: 0 0 5px;
  color: var(--t);
}
.input-mobile-app .hero p {
  font-size: 10px;
  color: var(--mut);
  margin: 0;
  line-height: 1.45;
}
.input-mobile-app .date-bar {
  margin-top: 12px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 9px 12px;
  font-size: 9px;
  color: var(--mut);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.input-mobile-app .date-bar b {
  color: var(--t);
  font-size: 11px;
}
.input-mobile-app .date-bar input[type="date"] {
  border: 0;
  background: transparent;
  font-size: 11px;
  font-weight: 700;
  color: var(--t);
  outline: none;
  cursor: pointer;
}

/* Section Title */
.input-mobile-app .section-title {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: .06em;
  margin: 20px 2px 9px;
  color: #596273;
  text-transform: uppercase;
}

/* Transaction Types Grid */
.input-mobile-app .types {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 9px;
}
@media (min-width: 640px) {
  .input-mobile-app .types {
    grid-template-columns: repeat(5, 1fr);
  }
}
.input-mobile-app .type-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 13px 12px;
  min-height: 91px;
  cursor: pointer;
  transition: all .15s ease;
  user-select: none;
}
.input-mobile-app .type-card:hover {
  transform: translateY(-1px);
  border-color: #dca0b5;
}
.input-mobile-app .type-card.active {
  border: 1.5px solid #dca0b5;
  background: #fff9fb;
  box-shadow: 0 3px 10px rgba(146, 0, 47, 0.08);
}
.input-mobile-app .type-card .icon-box {
  width: 30px;
  height: 30px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff1f5;
  font-size: 15px;
}
.input-mobile-app .type-card b {
  display: block;
  font-size: 11px;
  font-weight: 800;
  margin-top: 8px;
  color: var(--t);
}
.input-mobile-app .type-card small {
  display: block;
  color: var(--mut);
  font-size: 8px;
  margin-top: 3px;
  line-height: 1.3;
}

/* Card */
.input-mobile-app .form-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 16px;
  padding: 15px;
  margin-top: 12px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}
.input-mobile-app .cardhead {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}
.input-mobile-app .cardhead h2 {
  font-size: 14px;
  font-weight: 800;
  margin: 0;
  color: var(--t);
}
.input-mobile-app .tag {
  font-size: 8px;
  padding: 5px 7px;
  border-radius: 20px;
  background: #ecfdf5;
  color: var(--green);
  font-weight: 700;
}
.input-mobile-app .tag.maroon {
  background: #fff1f5;
  color: var(--m);
}

/* Form Controls */
.input-mobile-app .field {
  margin-bottom: 11px;
}
.input-mobile-app .field label {
  display: block;
  font-size: 9px;
  font-weight: 700;
  margin-bottom: 5px;
  color: #4f5969;
}
.input-mobile-app .field input,
.input-mobile-app .field select,
.input-mobile-app .field textarea {
  width: 100%;
  border: 1px solid #dfe4eb;
  background: #fff;
  border-radius: 10px;
  min-height: 40px;
  padding: 0 10px;
  font-size: 11px;
  color: var(--t);
  outline: none;
  transition: border-color .15s;
}
.input-mobile-app .field input:focus,
.input-mobile-app .field select:focus,
.input-mobile-app .field textarea:focus {
  border-color: var(--m);
}
.input-mobile-app .field textarea {
  padding: 10px;
  min-height: 65px;
  resize: none;
}
.input-mobile-app .row-fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 9px;
}
.input-mobile-app .readonly {
  background: #f6f7f9 !important;
  color: #687386 !important;
  font-weight: 700;
}

/* Metrics */
.input-mobile-app .metrics {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  margin-top: 4px;
}
.input-mobile-app .metric {
  border: 1px solid var(--line);
  border-radius: 11px;
  padding: 10px;
  background: #fafbfc;
}
.input-mobile-app .metric small {
  display: block;
  color: var(--mut);
  font-size: 8px;
  font-weight: 600;
}
.input-mobile-app .metric b {
  display: block;
  font-size: 15px;
  font-weight: 800;
  margin-top: 4px;
  color: var(--t);
}

/* Population Box */
.input-mobile-app .population {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  background: #f8f9fb;
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 10px;
  margin-bottom: 11px;
}
.input-mobile-app .population small {
  font-size: 8px;
  color: var(--mut);
  display: block;
}
.input-mobile-app .population b {
  font-size: 15px;
  display: block;
  margin-top: 3px;
  color: var(--t);
  font-weight: 800;
}
.input-mobile-app .population .after b {
  color: var(--green);
}

/* Quick Stats */
.input-mobile-app .quick {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 7px;
  margin-top: 9px;
}
.input-mobile-app .quick div {
  border: 1px solid var(--line);
  border-radius: 9px;
  padding: 9px;
  background: #fafbfc;
}
.input-mobile-app .quick small {
  display: block;
  color: var(--mut);
  font-size: 7.5px;
}
.input-mobile-app .quick b {
  display: block;
  font-size: 11px;
  font-weight: 800;
  margin-top: 3px;
}

/* Standard Master Feed Box */
.input-mobile-app .standard {
  background: #f8f9fb;
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 11px;
  margin-top: 1px;
}
.input-mobile-app .stdtop {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.input-mobile-app .stdtop b {
  font-size: 10px;
  font-weight: 800;
}
.input-mobile-app .stdtop span {
  font-size: 8.5px;
  color: var(--green);
  font-weight: 700;
}
.input-mobile-app .stdvalue {
  font-size: 18px;
  font-weight: 800;
  margin-top: 5px;
  color: var(--t);
}
.input-mobile-app .stdvalue small {
  font-size: 9px;
  font-weight: 500;
  color: var(--mut);
}
.input-mobile-app .calc {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 7px;
  margin-top: 9px;
}
.input-mobile-app .calc div {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 9px;
  padding: 8px;
}
.input-mobile-app .calc small {
  display: block;
  color: var(--mut);
  font-size: 7px;
}
.input-mobile-app .calc b {
  display: block;
  font-size: 10px;
  font-weight: 800;
  margin-top: 3px;
  color: var(--t);
}

/* Master Medicine Reference Box */
.input-mobile-app .master-box {
  background: #f8f9fb;
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 11px;
  margin-bottom: 11px;
}
.input-mobile-app .mastertop {
  display: flex;
  justify-content: space-between;
}
.input-mobile-app .mastertop b {
  font-size: 10px;
  font-weight: 800;
}
.input-mobile-app .mastertop span {
  font-size: 8px;
  color: var(--green);
  font-weight: 700;
}
.input-mobile-app .master-box p {
  font-size: 8px;
  color: var(--mut);
  line-height: 1.45;
  margin: 6px 0 0;
}

/* Sync Alerts */
.input-mobile-app .sync {
  margin-top: 11px;
  border-radius: 12px;
  background: #fff8ed;
  border: 1px solid #f8dfb0;
  padding: 11px;
}
.input-mobile-app .sync.green {
  background: #ecfdf5;
  border-color: #bcebdc;
}
.input-mobile-app .sync .title,
.input-mobile-app .sync b {
  font-size: 9px;
  font-weight: 800;
  color: #916000;
  display: block;
}
.input-mobile-app .sync.green b {
  color: #078f68;
}
.input-mobile-app .sync p {
  font-size: 8px;
  color: #737d8d;
  line-height: 1.45;
  margin: 4px 0 0;
}
.input-mobile-app .sync.green p {
  color: #526960;
}

.input-mobile-app .alert-box {
  margin-top: 11px;
  border-radius: 12px;
  background: #fff8ed;
  border: 1px solid #f8dfb0;
  padding: 11px;
  font-size: 8px;
  color: #75664e;
  line-height: 1.45;
}
.input-mobile-app .alert-box b {
  font-size: 9px;
  color: #9a6700;
}

/* Action Buttons */
.input-mobile-app .btn-submit {
  width: 100%;
  height: 43px;
  border: 0;
  border-radius: 11px;
  background: var(--m);
  color: #fff;
  font-weight: 800;
  font-size: 11px;
  margin-top: 11px;
  cursor: pointer;
  transition: background .15s;
}
.input-mobile-app .btn-submit:hover {
  background: var(--m2);
}
.input-mobile-app .btn-cancel {
  width: 100%;
  height: 40px;
  border: 1px solid var(--line);
  border-radius: 11px;
  background: #fff;
  color: #596273;
  font-weight: 700;
  font-size: 10px;
  margin-top: 7px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: background .15s;
}
.input-mobile-app .btn-cancel:hover {
  background: #f8f9fa;
}

/* Info Box */
.input-mobile-app .info-note {
  font-size: 8px;
  color: var(--mut);
  line-height: 1.45;
  background: #f7f8fa;
  border-radius: 10px;
  padding: 10px;
  margin-top: 10px;
}

/* Stock Card */
.input-mobile-app .stock-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid var(--line);
  font-size: 10px;
}
.input-mobile-app .stock-row:last-child {
  border: 0;
}
.input-mobile-app .stock-row b {
  font-size: 11px;
  font-weight: 800;
}
.input-mobile-app .green { color: var(--green); }
.input-mobile-app .orange { color: var(--o); }
.input-mobile-app .red { color: var(--red); }
</style>

<div class="input-mobile-app">

    <!-- Hero Card -->
    <section class="hero">
        <div class="kicker">
            <span class="pill">INPUT KANDANG</span>
            <span class="pill green">● Terhubung</span>
        </div>
        <h1>Input Transaksi</h1>
        <p id="heroSubtitle">Satu kali input. Sistem otomatis meneruskan transaksi ke gudang, populasi, rekap dan dashboard.</p>
        <div class="date-bar">
            <span>Tanggal Input</span>
            @if(!auth()->check() || auth()->user()->canAccess('input_filter_tanggal'))
            <input type="date" id="inputTxDate" value="{{ $date }}" onchange="window.location.href='?date=' + this.value + '&type=' + currentType">
            @else
            <input type="date" id="inputTxDate" value="{{ $date }}" readonly disabled style="opacity: 0.7; cursor: not-allowed;">
            @endif
        </div>
    </section>

    @if(session('error'))
        <div class="sync" style="background:#fff1f2; border-color:#fecdd3; margin-top:12px; padding:12px; border-radius:12px;">
            <b style="color:#9f1239; font-size:11px; font-weight:800;">⚠️ Double Input Tercegah</b>
            <p style="color:#be123c; font-size:10.5px; margin-top:3px; line-height:1.4;">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('success'))
        <div class="sync green" style="background:#ecfdf5; border-color:#a7f3d0; margin-top:12px; padding:12px; border-radius:12px;">
            <b style="color:#047857; font-size:11px; font-weight:800;">✓ Berhasil Disimpan</b>
            <p style="color:#065f46; font-size:10.5px; margin-top:3px; line-height:1.4;">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Section: Pilih Transaksi -->
    <div class="section-title">PILIH TRANSAKSI</div>
    <section class="types">
        @if(!auth()->check() || auth()->user()->canAccess('input_form_egg'))
        <!-- 1. Produksi Telur -->
        <div class="type-card {{ $type === 'produksi' ? 'active' : '' }}" onclick="switchType('produksi')" id="btnTypeProduksi">
            <div class="icon-box">🥚</div>
            <b>Produksi Telur</b>
            <small>Masuk otomatis ke Gudang Telur</small>
        </div>
        @endif

        @if(!auth()->check() || auth()->user()->canAccess('input_form_feed'))
        <!-- 2. Pemakaian Pakan -->
        <div class="type-card {{ $type === 'pakan' ? 'active' : '' }}" onclick="switchType('pakan')" id="btnTypePakan">
            <div class="icon-box" style="background:#ecfff8;">🌾</div>
            <b>Pemakaian Pakan</b>
            <small>Potong otomatis Gudang Pakan</small>
        </div>
        @endif

        @if(!auth()->check() || auth()->user()->canAccess('input_form_mortality'))
        <!-- 3. Mortalitas -->
        <div class="type-card {{ $type === 'mortalitas' ? 'active' : '' }}" onclick="switchType('mortalitas')" id="btnTypeMortalitas">
            <div class="icon-box">🐔</div>
            <b>Mortalitas</b>
            <small>Update populasi blok</small>
        </div>
        @endif

        @if(!auth()->check() || auth()->user()->canAccess('input_form_health'))
        <!-- 4. Vaksin & Obat -->
        <div class="type-card {{ $type === 'obat' ? 'active' : '' }}" onclick="switchType('obat')" id="btnTypeObat">
            <div class="icon-box">💊</div>
            <b>Vaksin & Obat</b>
            <small>Potong otomatis stok obat</small>
        </div>
        @endif

        @if(!auth()->check() || auth()->user()->canAccess('input_form_weight'))
        <!-- 5. Timbang Ayam + Telur -->
        <div class="type-card {{ $type === 'bobot' ? 'active' : '' }}" onclick="switchType('bobot')" id="btnTypeBobot">
            <div class="icon-box" style="background:#f0f9ff; color:#0284c7;">⚖️</div>
            <b>Timbang Ayam + Telur</b>
            <small>Input bobot mingguan</small>
        </div>
        @endif
    </section>

    <!-- FORM 1: PRODUKSI TELUR -->
    <div id="formSectionProduksi" style="{{ $type === 'produksi' ? '' : 'display:none;' }}">
        <section class="form-card">
            <div class="cardhead">
                <h2>🥚 Produksi Telur</h2>
                <span class="tag">↗ Gudang Telur</span>
            </div>

            <form method="POST" action="{{ route('production.store') }}" id="formProduksi">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="row-fields">
                    <div class="field">
                        <label>Kloter</label>
                        <select id="prodKloter" onchange="filterCoops('prod')">
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Blok</label>
                        <select name="coop_id" id="prodCoop" onchange="updateCoopPop('prod')" required>
                            @foreach($coops as $coop)
                                <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}" data-pop="{{ $coop->active_chickens }}">
                                    {{ $coop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Populasi Aktif</label>
                    <input class="readonly" id="prodPopulasi" value="762 ekor" readonly>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Telur Baik (Butir)</label>
                        <input type="number" name="good_eggs" id="prodTelurBaik" value="0" min="0" oninput="calcProduksi()" required placeholder="0">
                    </div>
                    <div class="field">
                        <label>Retak/Pecah (Butir)</label>
                        <input type="number" name="broken_eggs" id="prodRetakPecah" value="0" min="0" oninput="calcProduksi()" placeholder="0">
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Jumlah Peti (Opsional)</label>
                        <input type="number" step="1" name="crates_count" id="prodPeti" value="0" min="0" placeholder="0">
                    </div>
                    <div class="field">
                        <label>Jumlah kg (Opsional)</label>
                        <input type="number" step="0.1" name="weight_kg" id="prodKg" value="0" min="0" placeholder="0" onchange="autoConvertEggKg('prodKg', 'prodPeti')" onblur="autoConvertEggKg('prodKg', 'prodPeti')">
                    </div>
                </div>

                <div class="field">
                    <label>Total Telur (Otomatis)</label>
                    <input class="readonly" id="prodTotalTelur" value="0 butir" readonly>
                </div>

                <div class="field" style="margin-top:11px">
                    <label>Keterangan</label>
                    <textarea name="notes" placeholder="Contoh: produksi normal..."></textarea>
                </div>

                <div class="sync">
                    <div class="title">↗ Otomatis masuk Gudang Telur</div>
                    <p>Telur baik menjadi <b>stok telur tersedia</b>. Retak dan pecah tetap tercatat sebagai hasil produksi, tetapi tidak masuk stok telur baik.</p>
                </div>

                <div id="prodCompletedAlert" class="sync green" style="display:none; background:#ecfdf5; border-color:#a7f3d0; padding:11px; border-radius:12px; margin-top:11px;">
                    <b style="color:#047857; font-size:10.5px;">✓ Semua Blok di Kloter ini sudah diinput Produksi Telur untuk tanggal ini.</b>
                    <p style="color:#065f46; font-size:9.5px; margin-top:2px;">Penjagaan otomatis aktif agar tidak terjadi dobel input.</p>
                </div>

                <button type="submit" class="btn-submit" id="prodSubmitBtn">Simpan Produksi</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
            </form>
        </section>
    </div>

    <!-- FORM 2: PEMAKAIAN PAKAN -->
    <div id="formSectionPakan" style="{{ $type === 'pakan' ? '' : 'display:none;' }}">
        <section class="form-card">
            <div class="cardhead">
                <h2>🌾 Pemakaian Pakan</h2>
                <span class="tag">↘ Gudang Pakan</span>
            </div>

            <form method="POST" action="{{ route('feed.store') }}" id="formPakan">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="row-fields">
                    <div class="field">
                        <label>Kloter</label>
                        <select id="pakanKloter" onchange="filterCoops('pakan')">
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Blok</label>
                        <select name="coop_id" id="pakanCoop" onchange="updateCoopPop('pakan')" required>
                            @foreach($coops as $coop)
                                @php
                                    $cStd = $coopStandards[$coop->id] ?? [];
                                    $feedTargetGram = $cStd['feed_target_gram'] ?? $cStd['gram_pakan'] ?? 105;
                                    $tips = $cStd['tips'] ?? 'Layer';
                                @endphp
                                <option value="{{ $coop->id }}" 
                                        data-flock="{{ $coop->flock_id }}" 
                                        data-pop="{{ $coop->active_chickens }}"
                                        data-age="{{ $coop->chicken_age_weeks }}"
                                        data-feed="{{ $feedTargetGram }}"
                                        data-feedtype="{{ $tips }}">
                                    {{ $coop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Waktu Pemberian</label>
                        <select name="feeding_time" id="pakanWaktu" onchange="calcPakan()">
                            <option value="Pagi">Pagi</option>
                            <option value="Sore">Sore</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Jenis Pakan</label>
                        <select name="feed_name" id="pakanJenis">
                            <option value="Pakan Layer">Layer</option>
                            <option value="Pakan Grower">Grower</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Populasi Aktif</label>
                    <input class="readonly" id="pakanPopulasi" value="762 ekor" readonly>
                </div>

                <div hidden class="field">
                    <label>Standar Pakan Master</label>
                    <div class="standard">
                        <div class="stdtop">
                            <b id="pakanStdTitle">Umur 21 minggu · Layer</b>
                            <span>✓ Acuan Master</span>
                        </div>
                        <div class="stdvalue"><span id="pakanStdGram">105</span> <small>gram / ekor / hari</small></div>
                        <div class="calc">
                            <div>
                                <small>Standar Blok</small>
                                <b id="pakanStdBlok">80,0 Kg</b>
                            </div>
                            <div>
                                <small>Sudah Pagi</small>
                                <b id="pakanSudahPagi">40,0 Kg</b>
                            </div>
                            <div>
                                <small>Sisa Hari Ini</small>
                                <b id="pakanSisaHari">40,0 Kg</b>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label>Jumlah Pakan Aktual (Kg)</label>
                    <input type="number" step="0.1" name="quantity_kg" id="pakanAktual" value="40" placeholder="Masukkan kg" oninput="calcPakanSisa()" required>
                </div>

                <div class="field">
                    <label>Keterangan</label>
                    <input name="notes" placeholder="Opsional...">
                </div>

                @php
                    $feedSummaryInput = \App\Services\OutboundIntegrationService::getFeedOutboundSummary();
                    $stokLayerIn = $feedSummaryInput['current_stock_kg_layer'] ?? 0;
                    $stokLayerKrgIn = $feedSummaryInput['current_stock_karung_layer'] ?? 0;
                    $stokGrowerIn = $feedSummaryInput['current_stock_kg_grower'] ?? 0;
                    $stokGrowerKrgIn = $feedSummaryInput['current_stock_karung_grower'] ?? 0;
                @endphp
                <div class="sync green" style="background:#ecfff8; border-color:#a7f3d0; padding:10px; border-radius:10px;">
                    <b style="color:#047857; font-size:11px;">↘ Otomatis Potong Stok Gudang Pakan Terkait</b>
                    <p style="color:#065f46; font-size:10px; margin-top:2px;">Ketersediaan Stok Gudang Saat Ini:</p>
                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; font-size:10.5px; font-weight:700;">
                        <span style="background:#fff; padding:4px 8px; border-radius:6px; border:1px solid #a7f3d0; color:#047857;">
                            🌾 Stok Pakan Layer: {{ number_format($stokLayerIn, 0, ',', '.') }} kg ({{ \App\Models\Setting::formatKarungKg($stokLayerIn) }})
                        </span>
                        <span style="background:#fff; padding:4px 8px; border-radius:6px; border:1px solid #bae6fd; color:#0369a1;">
                            🌾 Stok Pakan Grower: {{ number_format($stokGrowerIn, 0, ',', '.') }} kg ({{ \App\Models\Setting::formatKarungKg($stokGrowerIn) }})
                        </span>
                    </div>
                </div>

                <div id="pakanCompletedAlert" class="sync green" style="display:none; background:#ecfdf5; border-color:#a7f3d0; padding:11px; border-radius:12px; margin-top:11px;">
                    <b style="color:#047857; font-size:10.5px;">✓ Semua Blok di Kloter ini sudah diinput Pemakaian Pakan (Pagi & Sore) untuk tanggal ini.</b>
                    <p style="color:#065f46; font-size:9.5px; margin-top:2px;">Penjagaan otomatis aktif agar tidak terjadi dobel input.</p>
                </div>

                <button type="submit" class="btn-submit" id="pakanSubmitBtn">Simpan Pemakaian</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
                <div class="info-note">Standar gram/ekor berasal dari Master berdasarkan umur ayam. Petugas tetap memasukkan jumlah aktual yang benar-benar diberikan.</div>
            </form>
        </section>
    </div>

    <!-- FORM 3: MORTALITAS -->
    <div id="formSectionMortalitas" style="{{ $type === 'mortalitas' ? '' : 'display:none;' }}">
        <section class="form-card">
            <div class="cardhead">
                <h2>🐔 Mortalitas</h2>
                <span class="tag maroon">↘ Populasi Blok</span>
            </div>

            <form method="POST" action="{{ route('mortality.store') }}" id="formMortalitas">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="row-fields">
                    <div class="field">
                        <label>Kloter</label>
                        <select id="mortKloter" onchange="filterCoops('mort')">
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Blok</label>
                        <select name="coop_id" id="mortCoop" onchange="updateCoopPop('mort')" required>
                            @foreach($coops as $coop)
                                <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}" data-pop="{{ $coop->active_chickens }}">
                                    {{ $coop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="population">
                    <div>
                        <small>Populasi Sebelum</small>
                        <b id="mortPopSebelum">762 ekor</b>
                    </div>
                    <div class="after">
                        <small>Populasi Setelah</small>
                        <b id="mortPopSetelah">760 ekor</b>
                    </div>
                </div>

                <div class="field">
                    <label id="mortCountLabel">Jumlah Ayam</label>
                    <input type="number" name="count" id="mortCount" value="1" min="1" placeholder="Masukkan jumlah ekor" oninput="calcMort()" required>
                </div>

                <div class="field">
                    <label>Status / Kategori</label>
                    <select name="type" id="mortType" onchange="toggleMortalityType()">
                        <option value="mati">Mati (Kematian)</option>
                        <option value="afkir">Afkir (Culling)</option>
                        @if(!auth()->check() || auth()->user()->canAccess('input_form_quarantine'))
                        <option value="sakit">Sakit (Masuk Karantina)</option>
                        <option value="sembuh">Sembuh (Kembali ke Kandang)</option>
                        @endif
                    </select>
                </div>

                <!-- Input Nomor Baterai (Muncul Dinamis jika Sakit / Sembuh) -->
                <div class="field" id="mortBatteryField" style="display: none;">
                    <label id="mortBatteryLabel">Nomor Baterai / Kandang Asal</label>
                    <input type="text" name="battery_number" id="mortBatteryNumber" placeholder="Contoh: A-12 / B-05">
                    <small style="font-size: 11px; color: #64748b; display: block; margin-top: 4px;" id="mortBatteryHint">
                        Catat nomor baterai asal ayam sakit untuk riwayat isolasi karantina.
                    </small>
                </div>

                <div class="field">
                    <label id="mortCauseLabel">Penyebab / Indikasi</label>
                    <select name="cause" id="mortCause">
                        <option value="Belum diketahui">Belum diketahui</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Prolaps">Prolaps</option>
                        <option value="Accident / Cedera">Accident / Cedera</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="field">
                    <label>Keterangan</label>
                    <input name="notes" placeholder="Opsional...">
                </div>

                <div class="quick">
                    <div>
                        <small>Mati/Afkir Hari Ini</small>
                        <b class="red">{{ $mortalitasHariIni }} ekor</b>
                    </div>
                    <div>
                        <small>Ayam di Karantina</small>
                        <b style="color: #d97706;">{{ $totalKarantinaSaatIni }} ekor</b>
                    </div>
                    <div>
                        <small>Populasi Farm</small>
                        <b>{{ number_format($totalChickens, 0, ',', '.') }}</b>
                    </div>
                </div>

                <div class="alert-box" id="mortAlertBox">
                    <b id="mortAlertTitle">⚠ Populasi otomatis diperbarui</b><br>
                    <span id="mortAlertDesc">Setelah disimpan, <span id="lblMortCount">1</span> ekor akan dikurangi dari populasi aktif blok terpilih. Populasi baru dipakai oleh Input Produksi dan Input Pakan berikutnya.</span>
                </div>

                <button type="submit" class="btn-submit" id="mortBtnSubmit">Simpan Transaksi</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
                <div class="info-note">Mortalitas & karantina tidak mengubah stok gudang umum. Transaksi ini memperbarui populasi aktif kandang dan masuk ke Rekap serta Dashboard.</div>
            </form>
        </section>

        <!-- Ringkasan Populasi -->
        <section class="form-card">
            <div class="cardhead">
                <h2>Ringkasan Populasi</h2>
                <span class="tag">Real-time</span>
            </div>
            <div class="stock-row">
                <span>Kloter 1</span>
                <b>{{ number_format($kloter1Pop, 0, ',', '.') }} ekor</b>
            </div>
            <div class="stock-row">
                <span>Kloter 2</span>
                <b>{{ number_format($kloter2Pop, 0, ',', '.') }} ekor</b>
            </div>
            <div class="stock-row">
                <span>Total Aktif</span>
                <b class="green">{{ number_format($totalChickens, 0, ',', '.') }} ekor</b>
            </div>
        </section>
    </div>

    <!-- FORM 4: VAKSIN & OBAT -->
    <div id="formSectionObat" style="{{ $type === 'obat' ? '' : 'display:none;' }}">
        <!-- Panduan Cepat Jenis Obat & Vitamin -->
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 16px; padding: 14px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="font-size:16px;">💡</span>
                    <b style="font-size:12px; color:#0369a1; text-transform:uppercase; letter-spacing:0.5px;">Panduan Kategori & Jenis Obat</b>
                </div>
                <span style="font-size:10px; background:#0284c7; color:#fff; font-weight:700; padding:2px 8px; border-radius:12px;">SOP Medis</span>
            </div>
            <div style="font-size:11px; line-height:1.6; color:#1e293b;">
                <div style="display:grid; grid-template-columns: 1fr; gap:5px;">
                    <div>• <b>Vermixon / Wormzol:</b> Kategori <span style="background:#fef3c7; color:#92400e; font-weight:700; padding:1px 6px; border-radius:6px;">🪱 Obat Cacing</span> (Ascaridia & cacing pita)</div>
                    <div>• <b>Neomeditril / Amoxitin / Doxyvet:</b> Kategori <span style="background:#ffe4e6; color:#9f1239; font-weight:700; padding:1px 6px; border-radius:6px;">💊 Antibiotik</span> (CRD ngorok, Snot, Kolera)</div>
                    <div>• <b>Toltrazuril / Coccilin:</b> Kategori <span style="background:#fee2e2; color:#991b1b; font-weight:700; padding:1px 6px; border-radius:6px;">🩸 Antikoksidia</span> (Berak Darah / Cokelat)</div>
                    <div>• <b>Vita Stress / B Complex / Egg Stimulant:</b> Kategori <span style="background:#dcfce7; color:#166534; font-weight:700; padding:1px 6px; border-radius:6px;">🍊 Vitamin & Antistres</span></div>
                    <div>• <b>ND Lasota / ND IB / Medivac AI:</b> Kategori <span style="background:#f3e8ff; color:#6b21a8; font-weight:700; padding:1px 6px; border-radius:6px;">💉 Vaksin</span> (Tetelo & Flu Burung)</div>
                    <div>• <b>Kalsium Premix / Egg Shell Booster:</b> Kategori <span style="background:#e0f2fe; color:#075985; font-weight:700; padding:1px 6px; border-radius:6px;">🧱 Mineral & Kalsium</span> (Cangkang Telur)</div>
                </div>
            </div>
        </div>

        <section class="form-card">
            <div class="cardhead">
                <h2>💊 Vaksin & Obat</h2>
                <span class="tag">↘ Gudang Obat</span>
            </div>

            <form method="POST" action="{{ route('health.store') }}" id="formObat">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="row-fields">
                    <div class="field">
                        <label>Kloter</label>
                        <select id="obatKloter" onchange="filterCoops('obat')">
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Blok</label>
                        <select name="coop_id" id="obatCoop" required>
                            @foreach($coops as $coop)
                                <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}">
                                    {{ $coop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Kategori Obat / Medis</label>
                    <select name="type" id="obatKategori" onchange="filterMedicines()">
                        <option value="all">🔍 Semua Kategori (Tampilkan Semua 26 Produk)</option>
                        <option value="obat_cacing">🪱 Obat Cacing / Anthelmintik (Vermixon, Wormzol)</option>
                        <option value="antibiotik">💊 Antibiotik / Antibakteri (Neomeditril, Amoxitin, CRD)</option>
                        <option value="antikoksidia">🩸 Antikoksidiosis (Toltrazuril, Berak Darah)</option>
                        <option value="vitamin">🍊 Vitamin & Suplemen Antistres (Vita Stress, B Complex)</option>
                        <option value="vaksin">💉 Vaksin Unggas (ND Lasota, ND IB, AI, Coryza)</option>
                        <option value="mineral">🧱 Mineral, Kalsium & Premix (Kalsium, CaCO3)</option>
                        <option value="disinfektan">🧪 Disinfektan & Sanitasi (Medisep, Antisep, Rodalon)</option>
                        <option value="obat">🌿 Obat Lainnya / Herbal Unggas</option>
                    </select>
                </div>

                <div class="field">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <label style="margin-bottom:0;">Pilih Produk</label>
                        <span id="medFilterCount" style="font-size:10px; color:#64748b; font-weight:600;">{{ count($medicines) }} produk tersedia</span>
                    </div>
                    <select name="medicine_name" id="obatProduk" onchange="updateMedDetails()">
                        @foreach($medicines as $med)
                            <option value="{{ $med['name'] }}" 
                                data-category="{{ $med['category_key'] }}" 
                                data-category-label="{{ $med['category'] }}"
                                data-dosage="{{ $med['dosage'] }}" 
                                data-app="{{ $med['application'] }}" 
                                data-sch="{{ $med['schedule'] }}" 
                                data-unit="{{ $med['unit'] }}" 
                                data-stock="{{ $med['stock'] }}"
                                data-indication="{{ $med['indication'] ?? '' }}"
                                data-notes="{{ $med['notes'] ?? '' }}">
                                {{ $med['name'] }} — [{{ $med['category'] }}]
                            </option>
                        @endforeach
                        <option value="custom" data-category="custom" data-category-label="Input Manual" data-dosage="Sesuai dosis kemasan" data-app="Air minum" data-sch="Sesuai anjuran" data-unit="Botol" data-stock="0" data-indication="Obat / vitamin khusus luar katalog" data-notes="Input manual oleh petugas">+ Tulis Nama Produk Lainnya (Input Manual)...</option>
                    </select>
                </div>

                <div class="field" id="fieldCustomMed" style="display:none; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; padding:10px; margin-top:4px;">
                    <label style="color:#0369a1; font-weight:700;">Nama Obat / Produk Baru</label>
                    <input type="text" id="obatProdukCustom" placeholder="Contoh: Super Tetra, Tetrasiklin, Herbal Kunyit...">
                </div>

                <!-- Acuan Medis dari Master -->
                <div class="master-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin: 12px 0;">
                    <div class="mastertop" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <b style="font-size:12px; color:#0f172a;">Acuan Medis dari Master</b>
                            <span id="medCategoryBadge" style="font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:16px; background:#e0f2fe; color:#0369a1;">-</span>
                        </div>
                        <span style="font-size:11px; font-weight:700; color:#059669; display:flex; align-items:center; gap:3px;">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Terdaftar
                        </span>
                    </div>

                    <div id="medIndicationBox" style="font-size:11px; color:#991b1b; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:7px 10px; margin-bottom:8px; line-height:1.4;">
                        <b>🎯 Indikasi / Gejala:</b> <span id="medIndicationText">-</span>
                    </div>

                    <div style="font-size:11.5px; line-height:1.6; color:#334155;">
                        <div>• Dosis Standar: <b id="medDosisText" style="color:#0f172a;">-</b></div>
                        <div>• Rekomendasi Aplikasi: <b id="medAplikasiText" style="color:#92002f;">-</b></div>
                        <div>• Jadwal / Waktu: <b id="medJadwalText" style="color:#0f172a;">-</b></div>
                        <div style="margin-top:4px; font-size:11px; color:#64748b; font-style:italic;" id="medNotesText">-</div>
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Jumlah Pemakaian Aktual</label>
                        <input type="number" step="0.1" name="dosage" id="obatJumlah" value="1" placeholder="Jumlah" oninput="calcObatSisa()" required>
                    </div>
                    <div class="field">
                        <label>Satuan</label>
                        <select name="unit" id="obatSatuan">
                            <option value="Botol">Botol</option>
                            <option value="Box">Box</option>
                            <option value="Kg">Kg</option>
                            <option value="Gram">Gram</option>
                            <option value="Liter">Liter</option>
                            <option value="Dosis">Dosis</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Cara / Aplikasi Aktual</label>
                    <select name="application_method" id="obatAplikasi">
                        <option value="Air minum">Air minum</option>
                        <option value="Campur pakan">Campur pakan</option>
                        <option value="Tetes mata">Tetes mata</option>
                        <option value="Semprot">Semprot</option>
                        <option value="Suntik paha / dada">Suntik paha / dada</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="field">
                    <label>Keterangan Tambahan</label>
                    <input name="notes" placeholder="Opsional (misal: pemberian jam 08:00 pagi pasca vaksinasi)...">
                </div>

                <div class="sync">
                    <b>↘ Otomatis potong Gudang Obat</b>
                    <p>Saat disimpan, jumlah pemakaian menjadi transaksi keluar/pemakaian. Stok produk di Gudang Obat otomatis berkurang dan riwayat kegiatan tersimpan.</p>
                </div>

                <button type="submit" class="btn-submit">Simpan Vaksin / Obat</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
                <div class="info-note">Produk, dosis, aplikasi, dan jadwal berasal dari Master. Petugas memasukkan pemakaian aktual.</div>
            </form>
        </section>

        <!-- Stok Obat Terkait Real-time -->
        <section class="form-card">
            <div class="cardhead">
                <h2>Stok Obat Terkait</h2>
                <span class="tag">Real-time</span>
            </div>

            <!-- Kartu Highlight Produk Terpilih -->
            <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:12px; margin-bottom:12px;">
                <div style="font-size:10.5px; text-transform:uppercase; font-weight:700; color:#c2410c; margin-bottom:2px;">Produk Aktif yang Dipilih</div>
                <div style="font-size:13px; font-weight:800; color:#431407;" id="obatNamaTerpilih">-</div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:8px; border-top:1px dashed #fdba74; font-size:11.5px;">
                    <span>Stok di Gudang: <b class="orange" id="obatStokTerpilih">0</b></span>
                    <span>Sisa Pasca Transaksi: <b class="green" id="obatSetelahTx">0</b></span>
                </div>
            </div>

            <!-- Filter & Daftar Stok Cepat -->
            <div style="border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Katalog Stok (<span id="stockCountLbl">{{ count($medicines) }}</span> Produk)</span>
                    <input type="text" id="filterStockInput" placeholder="Cari obat..." oninput="searchStockRows()" style="font-size:11px; padding:4px 8px; border:1px solid #cbd5e1; border-radius:6px; width:130px;">
                </div>
                <div id="stockListContainer" style="max-height: 240px; overflow-y: auto; padding-right: 4px; display:flex; flex-direction:column; gap:4px;">
                    @foreach($medicines as $m)
                        <div class="stock-row stock-row-item" data-category="{{ $m['category_key'] }}" data-name="{{ strtolower($m['name']) }}" style="cursor:pointer; padding:6px 10px; border-radius:8px; border:1px solid #f1f5f9; background:#f8fafc; display:flex; justify-content:space-between; align-items:center;" onclick="selectProductFromStock('{{ $m['name'] }}', '{{ $m['category_key'] }}')">
                            <div style="display:flex; flex-direction:column;">
                                <span style="font-weight:700; font-size:11.5px; color:#1e293b;">{{ $m['name'] }}</span>
                                <span style="font-size:9.5px; color:#64748b;">{{ $m['category'] }}</span>
                            </div>
                            <b style="font-size:11.5px; color:#92002f;">{{ $m['stock'] }} {{ $m['unit'] }}</b>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <!-- FORM 5: TIMBANG AYAM + TELUR MINGGUAN -->
    <div id="formSectionBobot" style="{{ $type === 'bobot' ? '' : 'display:none;' }}">
        <section class="form-card">
            <div class="cardhead">
                <h2>⚖️ Timbang Ayam + Telur Mingguan</h2>
                <span class="tag" style="background:#e0f2fe; color:#0369a1; border-color:#bae6fd;">↗ Bobot 6 Blok</span>
            </div>

            <form method="POST" action="{{ route('weight.store') }}" id="formBobot">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="row-fields">
                    <div class="field">
                        <label>Kloter</label>
                        <select id="bobotKloter" onchange="filterCoops('bobot')">
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Blok</label>
                        <select name="coop_id" id="bobotCoop" onchange="updateCoopPop('bobot')" required>
                            @foreach($coops as $coop)
                                @php
                                    $cStd = $coopStandards[$coop->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$coop->chicken_age_weeks);
                                    $eggTgt = (float)($cStd['berat_telur_val'] ?? 0);
                                    $eggMinT = $eggTgt > 0 ? round($eggTgt - 2.5, 1) : 0;
                                    $eggMaxT = $eggTgt > 0 ? round($eggTgt + 2.5, 1) : 0;
                                    $eggTolStr = $eggTgt > 0 ? number_format($eggMinT, 1, ',', '.') . '–' . number_format($eggMaxT, 1, ',', '.') . ' g' : '-';
                                @endphp
                                <option value="{{ $coop->id }}" 
                                        data-flock="{{ $coop->flock_id }}" 
                                        data-pop="{{ $coop->active_chickens }}"
                                        data-age="{{ $coop->chicken_age_weeks }}"
                                        data-fase="{{ $cStd['fase'] ?? 'Laying Phase' }}"
                                        data-pill="{{ $cStd['pill'] ?? 'LAYER' }}"
                                        data-bbmin="{{ number_format($cStd['bb_min'] ?? 0, 2, '.', '') }}"
                                        data-bbtarget="{{ number_format($cStd['bb_target'] ?? 0, 2, '.', '') }}"
                                        data-bbmax="{{ number_format($cStd['bb_max'] ?? 0, 2, '.', '') }}"
                                        data-eggtarget="{{ $eggTgt }}"
                                        data-egglabel="{{ $cStd['berat_telur'] ?? '-' }}"
                                        data-eggtol="{{ $eggTolStr }}">
                                    {{ $coop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Populasi Aktif</label>
                        <input class="readonly" id="bobotPopulasi" value="762 ekor" readonly>
                    </div>
                    <div class="field">
                        <label>Usia Ayam</label>
                        <input class="readonly" id="bobotUsia" value="24 minggu" readonly>
                        <input type="hidden" name="age_weeks" id="bobotUsiaVal" value="24">
                    </div>
                </div>

                <div class="field">
                    <label>Fase Pertumbuhan (Otomatis)</label>
                    <input class="readonly" id="bobotFase" value="Puncak Produksi (Egg Peak)" readonly style="font-weight:700; color:#047857; background:#f0fdf4;">
                </div>

                <div class="field">
                    <label>Nomor Baterai Ayam Sampel <span style="color:#e11d48">*</span></label>
                    <input type="text" name="battery_number" id="bobotBaterai" placeholder="Contoh: Baris 2 / B-14" required>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Berat Ayam (Kg) <span style="color:#e11d48">*</span></label>
                        <input type="number" step="0.1" name="average_weight_kg" id="bobotBeratAyam" placeholder="Contoh: 1.1" oninput="calcBobotFeedback()" required>
                    </div>
                    <div class="field">
                        <label>Berat Telur (Butir / Gram)</label>
                        <input type="number" step="0.1" name="egg_weight_gram" id="bobotBeratTelur" placeholder="Contoh: 58.5" oninput="calcBobotFeedback()">
                    </div>
                </div>

                <!-- Kotak Acuan Master Standar Produksi (Disembunyikan / Hidden Sesuai Permintaan User) -->
                <div class="standard" style="display:none; background:#f0f9ff; border-color:#bae6fd; margin-top:8px;">
                    <div class="stdtop">
                        <b style="color:#0369a1;" id="bobotStdTitle">Acuan Master Umur 24 Minggu</b>
                        <span style="color:#0284c7; font-weight:800;" id="bobotStdFaseBadge">PUNCAK PRODUKSI</span>
                    </div>
                    <div class="calc" style="grid-template-columns: repeat(4, 1fr); margin-top:8px;">
                        <div>
                            <small style="color:#64748b; font-size:7.5px; display:block;">BB Minimum</small>
                            <b style="font-size:10px; color:#334155; display:block; margin-top:2px;" id="bobotStdMin">1,58 kg</b>
                        </div>
                        <div style="background:#ecfdf5; border-color:#a7f3d0;">
                            <small style="color:#047857; font-size:7.5px; display:block;">BB Target (Ideal)</small>
                            <b style="font-size:10.5px; color:#065f46; display:block; margin-top:2px;" id="bobotStdTarget">1,65 kg</b>
                        </div>
                        <div>
                            <small style="color:#64748b; font-size:7.5px; display:block;">BB Maksimum</small>
                            <b style="font-size:10px; color:#334155; display:block; margin-top:2px;" id="bobotStdMax">1,72 kg</b>
                        </div>
                        <div>
                            <small style="color:#64748b; font-size:7.5px; display:block;">Toleransi Telur</small>
                            <b style="font-size:10px; color:#92400e; display:block; margin-top:2px;" id="bobotStdTelur">53,8–58,8 g</b>
                        </div>
                    </div>
                    <!-- Live Feedback Indicator -->
                    <div id="bobotLiveFeedback" style="margin-top:8px; font-size:10px; font-weight:700; padding:7px 9px; border-radius:8px; background:#fff; border:1px solid #bae6fd; color:#0369a1; display:flex; align-items:center; gap:6px;">
                        <span id="bobotFeedbackText">ℹ Masukkan bobot ayam untuk membandingkan langsung dengan acuan master umur ayam ini.</span>
                    </div>
                </div>

                <div class="field" style="margin-top:11px">
                    <label>Catatan Kondisi Ayam</label>
                    <textarea name="notes" placeholder="Contoh: Ayam aktif, nafsu makan baik, jengger merah segar..."></textarea>
                </div>

                <div class="sync" style="background:#f0f9ff; border-color:#bae6fd;">
                    <b style="color:#0369a1;">↗ Otomatis Masuk ke Ringkasan Bobot & Analisa 6 Blok</b>
                    <p style="color:#0284c7;">Data berat badan ayam dan berat butir telur per sampel mingguan ini otomatis memperbarui kartu bobot 6 blok di Dashboard dan Rekapitulasi.</p>
                </div>

                <button type="submit" class="btn-submit" style="background:#0284c7;">Simpan Timbang Ayam & Telur</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
                <div class="info-note">Timbang sampel ayam rutin per minggu untuk memantau kurva pertumbuhan bobot dan kesesuaian target produksi telur.</div>
            </form>
        </section>
    </div>

</div>

<!-- Interactive Engine Logic for Mobile Input Forms -->
<script>
let currentType = '{{ $type }}';
const completedEggCoops = @json($completedEggCoopIds ?? []);
const completedFeedMap = @json($completedFeedRecords ?? []);

// Master copies of original option elements for iOS Safari WebKit compatibility
const masterCoopOptions = {};
const masterWaktuOptions = [];

// 1. Switch Transaction Type Tabs
function switchType(type) {
    currentType = type;

    // Reset button active classes
    document.querySelectorAll('.type-card').forEach(el => el.classList.remove('active'));
    document.getElementById('btnType' + type.charAt(0).toUpperCase() + type.slice(1)).classList.add('active');

    // Hide all form sections
    document.getElementById('formSectionProduksi').style.display = 'none';
    document.getElementById('formSectionPakan').style.display = 'none';
    document.getElementById('formSectionMortalitas').style.display = 'none';
    document.getElementById('formSectionObat').style.display = 'none';
    if (document.getElementById('formSectionBobot')) {
        document.getElementById('formSectionBobot').style.display = 'none';
    }

    // Show selected form section
    const targetSection = document.getElementById('formSection' + type.charAt(0).toUpperCase() + type.slice(1));
    if (targetSection) targetSection.style.display = 'block';

    // Update hero subtitle
    const sub = document.getElementById('heroSubtitle');
    if (type === 'produksi') {
        sub.textContent = 'Satu kali input. Sistem otomatis meneruskan transaksi ke gudang, populasi, rekap dan dashboard.';
    } else if (type === 'pakan') {
        sub.textContent = 'Satu kali input. Pemakaian pakan otomatis tercatat dan mengurangi stok Gudang Pakan.';
    } else if (type === 'mortalitas') {
        sub.textContent = 'Catat ayam mati atau afkir. Populasi aktif blok otomatis diperbarui setelah transaksi disimpan.';
    } else if (type === 'obat') {
        sub.textContent = 'Catat vaksin, vitamin, obat, dan perlakuan. Pemakaian otomatis tercatat ke Gudang Obat.';
    } else if (type === 'bobot') {
        sub.textContent = 'Catat sampel bobot ayam & berat telur per minggu. Metrik 6 blok otomatis terbarui di Dashboard.';
    }
}

// 2. Filter Coops based on Selected Flock & Double Input Protection (iOS & Cross-Platform Safe)
function filterCoops(prefix) {
    const flockSelect = document.getElementById(prefix + 'Kloter');
    const coopSelect = document.getElementById(prefix + 'Coop');
    if (!flockSelect || !coopSelect) return;
    const selectedFlockId = flockSelect.value;
    const masterList = masterCoopOptions[prefix] || Array.from(coopSelect.options);

    const validOptions = [];

    masterList.forEach(origOpt => {
        const flockId = origOpt.getAttribute('data-flock');
        const coopId = parseInt(origOpt.value);
        const matchesFlock = (flockId === selectedFlockId || !flockId);

        if (matchesFlock) {
            let isCompleted = false;

            if (prefix === 'prod') {
                // Produksi Telur: Cek apakah coopId sudah pernah diinput produksi untuk tanggal ini
                isCompleted = completedEggCoops.map(Number).includes(coopId);
            } else if (prefix === 'pakan') {
                // Pemakaian Pakan: Cek apakah Pagi & Sore keduanya sudah selesai diinput untuk coopId
                const doneTimes = (completedFeedMap[coopId] || []).map(t => String(t).toLowerCase());
                const hasPagi = doneTimes.includes('pagi');
                const hasSore = doneTimes.includes('sore') || doneTimes.includes('siang');
                isCompleted = (hasPagi && hasSore);
            }

            if (!isCompleted) {
                const clone = origOpt.cloneNode(true);
                clone.style.display = '';
                clone.disabled = false;
                validOptions.push(clone);
            }
        }
    });

    // Rebuild select options (removes items completely from DOM so iOS Safari WebKit wheel won't show grayed-out items)
    coopSelect.innerHTML = '';
    validOptions.forEach(opt => coopSelect.appendChild(opt));

    const alertBanner = document.getElementById(prefix + 'CompletedAlert');
    const submitBtn = document.getElementById(prefix + 'SubmitBtn');

    if (validOptions.length > 0) {
        coopSelect.value = validOptions[0].value;
        if (alertBanner) alertBanner.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    } else {
        coopSelect.value = '';
        if (alertBanner) alertBanner.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    updateCoopPop(prefix);
}

// 3. Update Population & Time Display for selected Coop (iOS Safe)
function updateCoopPop(prefix) {
    const coopSelect = document.getElementById(prefix + 'Coop');
    if (!coopSelect) return;
    const selectedOpt = coopSelect.options[coopSelect.selectedIndex];
    const pop = selectedOpt ? parseInt(selectedOpt.getAttribute('data-pop') || 762) : 762;

    if (prefix === 'prod') {
        document.getElementById('prodPopulasi').value = pop + ' ekor';
        calcProduksi();
    } else if (prefix === 'pakan') {
        document.getElementById('pakanPopulasi').value = pop + ' ekor';

        // Rebuild Waktu Pemberian Pakan (Pagi / Sore) for iOS Safari
        const coopId = parseInt(coopSelect.value);
        const pakanWaktuSelect = document.getElementById('pakanWaktu');
        if (pakanWaktuSelect && coopId && masterWaktuOptions.length > 0) {
            const doneTimes = (completedFeedMap[coopId] || []).map(t => String(t).toLowerCase());
            const hasPagi = doneTimes.includes('pagi');
            const hasSore = doneTimes.includes('sore') || doneTimes.includes('siang');

            const validWaktuOptions = [];
            masterWaktuOptions.forEach(origOpt => {
                const valLower = origOpt.value.toLowerCase();
                let isTimeDone = false;
                if (valLower === 'pagi' && hasPagi) isTimeDone = true;
                if ((valLower === 'sore' || valLower === 'siang') && hasSore) isTimeDone = true;

                if (!isTimeDone) {
                    const clone = origOpt.cloneNode(true);
                    clone.style.display = '';
                    clone.disabled = false;
                    validWaktuOptions.push(clone);
                }
            });

            pakanWaktuSelect.innerHTML = '';
            validWaktuOptions.forEach(opt => pakanWaktuSelect.appendChild(opt));

            if (validWaktuOptions.length > 0) {
                pakanWaktuSelect.value = validWaktuOptions[0].value;
            }
        }

        calcPakan();
    } else if (prefix === 'mort') {
        document.getElementById('mortPopSebelum').textContent = pop + ' ekor';
        calcMort();
    } else if (prefix === 'bobot') {
        const bobotPop = document.getElementById('bobotPopulasi');
        if (bobotPop) bobotPop.value = pop + ' ekor';
        const age = selectedOpt ? selectedOpt.getAttribute('data-age') : 24;
        const fase = selectedOpt ? selectedOpt.getAttribute('data-fase') : 'Puncak Produksi';
        const pill = selectedOpt ? selectedOpt.getAttribute('data-pill') : 'PUNCAK';
        const bbMin = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-bbmin') || 1.58) : 1.58;
        const bbTarget = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-bbtarget') || 1.65) : 1.65;
        const bbMax = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-bbmax') || 1.72) : 1.72;
        const eggTol = selectedOpt ? selectedOpt.getAttribute('data-eggtol') : '53,8–58,8 g';

        const usiaEl = document.getElementById('bobotUsia');
        const usiaVal = document.getElementById('bobotUsiaVal');
        const faseEl = document.getElementById('bobotFase');
        if (usiaEl) usiaEl.value = (age || 24) + ' minggu';
        if (usiaVal) usiaVal.value = age || 24;
        if (faseEl) faseEl.value = fase;

        const titleEl = document.getElementById('bobotStdTitle');
        const badgeEl = document.getElementById('bobotStdFaseBadge');
        const minEl = document.getElementById('bobotStdMin');
        const tgtEl = document.getElementById('bobotStdTarget');
        const maxEl = document.getElementById('bobotStdMax');
        const tolEl = document.getElementById('bobotStdTelur');

        if (titleEl) titleEl.textContent = `Acuan Master Umur ${age} Minggu`;
        if (badgeEl) badgeEl.textContent = pill;
        if (minEl) minEl.textContent = bbMin.toString().replace('.', ',') + ' kg';
        if (tgtEl) tgtEl.textContent = bbTarget.toString().replace('.', ',') + ' kg';
        if (maxEl) maxEl.textContent = bbMax.toString().replace('.', ',') + ' kg';
        if (tolEl) tolEl.textContent = eggTol;

        calcBobotFeedback();
    }
}

// 3b. Real-time Live Evaluator BB Ayam & Telur vs Master
function calcBobotFeedback() {
    const coopSelect = document.getElementById('bobotCoop');
    if (!coopSelect) return;
    const selectedOpt = coopSelect.options[coopSelect.selectedIndex];
    if (!selectedOpt) return;

    const bbMin = parseFloat(selectedOpt.getAttribute('data-bbmin') || 1.58);
    const bbTarget = parseFloat(selectedOpt.getAttribute('data-bbtarget') || 1.65);
    const bbMax = parseFloat(selectedOpt.getAttribute('data-bbmax') || 1.72);
    const eggTarget = parseFloat(selectedOpt.getAttribute('data-eggtarget') || 0);

    const bbVal = parseFloat(document.getElementById('bobotBeratAyam').value);
    const eggVal = parseFloat(document.getElementById('bobotBeratTelur').value);
    const box = document.getElementById('bobotLiveFeedback');
    const txt = document.getElementById('bobotFeedbackText');
    if (!box || !txt) return;

    if (isNaN(bbVal) || bbVal <= 0) {
        box.style.background = '#fff';
        box.style.borderColor = '#bae6fd';
        box.style.color = '#0369a1';
        txt.innerHTML = 'ℹ Masukkan bobot ayam untuk membandingkan langsung dengan acuan master umur ayam ini.';
        return;
    }

    let bbStatusHtml = '';
    if (bbVal >= bbMin && bbVal <= bbMax) {
        box.style.background = '#ecfdf5';
        box.style.borderColor = '#a7f3d0';
        box.style.color = '#065f46';
        bbStatusHtml = `✓ <b>BB Ayam Sesuai Target (Ideal):</b> ${bbVal.toString().replace('.', ',')} kg berada dalam rentang ideal master (${bbMin.toString().replace('.', ',')} – ${bbMax.toString().replace('.', ',')} kg). Target ideal: ${bbTarget.toString().replace('.', ',')} kg.`;
    } else if (bbVal < bbMin) {
        box.style.background = '#fffbeb';
        box.style.borderColor = '#fde68a';
        box.style.color = '#92400e';
        const diff = (bbMin - bbVal).toFixed(1).replace('.', ',');
        bbStatusHtml = `⚠️ <b>Kurang Bobot:</b> ${bbVal.toString().replace('.', ',')} kg berada di bawah BB Minimum (${bbMin.toString().replace('.', ',')} kg). Selisih ${diff} kg di bawah standar.`;
    } else {
        box.style.background = '#fff1f2';
        box.style.borderColor = '#fecdd3';
        box.style.color = '#9f1239';
        const diff = (bbVal - bbMax).toFixed(1).replace('.', ',');
        bbStatusHtml = `⚠️ <b>Kelebihan Bobot:</b> ${bbVal.toString().replace('.', ',')} kg berada di atas BB Maksimum (${bbMax.toString().replace('.', ',')} kg). Selisih ${diff} kg di atas standar.`;
    }

    let eggStatusHtml = '';
    if (!isNaN(eggVal) && eggVal > 0 && eggTarget > 0) {
        const minTol = eggTarget - 2.5;
        const maxTol = eggTarget + 2.5;
        if (eggVal >= minTol && eggVal <= maxTol) {
            eggStatusHtml = `<br><span style="color:#047857;">✓ <b>BB Telur Sesuai Toleransi:</b> ${eggVal.toString().replace('.', ',')} g (Batas: ${minTol.toFixed(1).replace('.', ',')} – ${maxTol.toFixed(1).replace('.', ',')} g).</span>`;
        } else {
            eggStatusHtml = `<br><span style="color:#b45309;">⚠️ <b>BB Telur di Luar Toleransi:</b> ${eggVal.toString().replace('.', ',')} g (Target acuan: ${eggTarget.toFixed(1).replace('.', ',')} g).</span>`;
        }
    }

    txt.innerHTML = bbStatusHtml + eggStatusHtml;
}

// 4. Calculations for Produksi Telur
function calcProduksi() {
    const baik = parseInt(document.getElementById('prodTelurBaik').value) || 0;
    const retakPecah = parseInt(document.getElementById('prodRetakPecah').value) || 0;
    const total = baik + retakPecah;

    document.getElementById('prodTotalTelur').value = total + ' butir';
}

// Konversi otomatis: setiap 10 kg menjadi 1 Peti (Peti selalu bulat tanpa koma)
function autoConvertEggKg(kgInputId, petiInputId) {
    const kgEl = document.getElementById(kgInputId);
    const petiEl = document.getElementById(petiInputId);
    if (!kgEl || !petiEl) return;
    
    let kgVal = parseFloat(kgEl.value) || 0;
    if (kgVal >= 10) {
        const extraPeti = Math.floor(kgVal / 10);
        const currentPeti = parseInt(petiEl.value || 0, 10);
        petiEl.value = currentPeti + extraPeti;
        const remainderKg = Math.round((kgVal - (extraPeti * 10)) * 10) / 10;
        kgEl.value = remainderKg > 0 ? remainderKg : '';
    }
}

// 5. Calculations for Pemakaian Pakan
function calcPakan() {
    const coopSelect = document.getElementById('pakanCoop');
    if (!coopSelect || coopSelect.selectedIndex < 0) return;
    const selectedOpt = coopSelect.options[coopSelect.selectedIndex];
    if (!selectedOpt) return;

    const popText = document.getElementById('pakanPopulasi').value;
    const pop = parseInt(popText) || 762;

    const feedGram = parseFloat(selectedOpt.getAttribute('data-feed') || 105);
    const age = selectedOpt.getAttribute('data-age') || 21;
    const feedType = selectedOpt.getAttribute('data-feedtype') || 'Layer';

    document.getElementById('pakanStdTitle').textContent = `Umur ${age} minggu · ${feedType}`;
    document.getElementById('pakanStdGram').textContent = feedGram.toString().replace('.', ',');

    // Auto-select Jenis Pakan dropdown based on feedType
    const pakanJenisSelect = document.getElementById('pakanJenis');
    if (pakanJenisSelect) {
        if (feedType.toLowerCase().includes('grower') || feedType.toLowerCase().includes('pullet')) {
            pakanJenisSelect.value = 'Pakan Grower';
        } else {
            pakanJenisSelect.value = 'Pakan Layer';
        }
    }

    const stdBlokKg = (pop * feedGram) / 1000;
    const pagiKg = (stdBlokKg * 0.40).toFixed(1);
    const soreKg = (stdBlokKg * 0.60).toFixed(1);

    const karung = Math.floor(stdBlokKg / 50);
    const sisaKg = (stdBlokKg % 50).toFixed(1);
    
    let standarStr = stdBlokKg.toFixed(1).replace('.', ',') + ' Kg';
    standarStr += ` (${karung} Karung + ${sisaKg.replace('.', ',')} Kg)`;

    document.getElementById('pakanStdBlok').textContent = standarStr;
    document.getElementById('pakanSudahPagi').textContent = ('' + pagiKg).replace('.', ',') + ' Kg';
    document.getElementById('pakanSisaHari').textContent = ('' + soreKg).replace('.', ',') + ' Kg';
}

function calcPakanSisa() {
    // Fungsi tidak diperlukan lagi karena Stok Terkait dihapus
}

// 6. Calculations for Mortalitas & Karantina
function toggleMortalityType() {
    const typeSelect = document.getElementById('mortType');
    const selectedType = typeSelect ? typeSelect.value : 'mati';
    const batteryField = document.getElementById('mortBatteryField');
    const batteryLabel = document.getElementById('mortBatteryLabel');
    const batteryHint = document.getElementById('mortBatteryHint');
    const batteryInput = document.getElementById('mortBatteryNumber');
    const alertTitle = document.getElementById('mortAlertTitle');
    const alertDesc = document.getElementById('mortAlertDesc');
    const btnSubmit = document.getElementById('mortBtnSubmit');
    const countLabel = document.getElementById('mortCountLabel');

    if (selectedType === 'sakit') {
        if (batteryField) batteryField.style.display = 'block';
        if (batteryLabel) batteryLabel.textContent = 'Nomor Baterai Asal (Kandang)';
        if (batteryHint) batteryHint.textContent = 'Catat nomor baterai tempat ayam sakit diambil untuk dipindahkan ke karantina.';
        if (batteryInput) batteryInput.setAttribute('placeholder', 'Contoh: A-12 / Baris 3');
        if (countLabel) countLabel.textContent = 'Jumlah Ayam Sakit';
        if (alertTitle) alertTitle.textContent = '⚠ Ayam dipindahkan ke Karantina';
        if (alertDesc) alertDesc.innerHTML = 'Setelah disimpan, <span id="lblMortCount">1</span> ekor akan dikurangi dari populasi aktif blok terpilih dan bertambah di Karantina.';
        if (btnSubmit) btnSubmit.textContent = 'Simpan ke Karantina';
    } else if (selectedType === 'sembuh') {
        if (batteryField) batteryField.style.display = 'block';
        if (batteryLabel) batteryLabel.textContent = 'Nomor Baterai Tujuan (Kandang)';
        if (batteryHint) batteryHint.textContent = 'Catat nomor baterai tempat ayam yang sembuh dikembalikan ke kandang aktif.';
        if (batteryInput) batteryInput.setAttribute('placeholder', 'Contoh: A-12 / Baris 3');
        if (countLabel) countLabel.textContent = 'Jumlah Ayam Sembuh';
        if (alertTitle) alertTitle.textContent = '✅ Ayam Sembuh kembali ke Kandang';
        if (alertDesc) alertDesc.innerHTML = 'Setelah disimpan, <span id="lblMortCount">1</span> ekor akan ditambahkan kembali ke populasi aktif blok terpilih dan berkurang di Karantina.';
        if (btnSubmit) btnSubmit.textContent = 'Simpan Ayam Sembuh';
    } else {
        if (batteryField) batteryField.style.display = 'none';
        if (countLabel) countLabel.textContent = 'Jumlah Mati / Afkir';
        if (alertTitle) alertTitle.textContent = '⚠ Populasi otomatis diperbarui';
        if (alertDesc) alertDesc.innerHTML = 'Setelah disimpan, <span id="lblMortCount">1</span> ekor akan dikurangi dari populasi aktif blok terpilih.';
        if (btnSubmit) btnSubmit.textContent = 'Simpan Mortalitas';
    }

    calcMort();
}

function calcMort() {
    const popText = document.getElementById('mortPopSebelum').textContent;
    const pop = parseInt(popText) || 762;
    const count = parseInt(document.getElementById('mortCount').value) || 0;
    const typeSelect = document.getElementById('mortType');
    const selectedType = typeSelect ? typeSelect.value : 'mati';

    let setelah = pop;
    if (selectedType === 'sembuh') {
        setelah = pop + count;
    } else {
        setelah = Math.max(0, pop - count);
    }

    document.getElementById('mortPopSetelah').textContent = setelah + ' ekor';
    const lblCount = document.getElementById('lblMortCount');
    if (lblCount) lblCount.textContent = count;
}

// 7. Details & Dynamic Filtering for Vaksin & Obat
let masterMedicineOptions = [];

function filterMedicines() {
    const katSel = document.getElementById('obatKategori');
    const medSel = document.getElementById('obatProduk');
    if (!katSel || !medSel || !masterMedicineOptions.length) return;

    const selectedCategory = katSel.value;
    const currentVal = medSel.value;

    medSel.innerHTML = '';

    let matchingCount = 0;
    masterMedicineOptions.forEach(opt => {
        const optCat = opt.getAttribute('data-category');
        if (selectedCategory === 'all' || optCat === selectedCategory || opt.value === 'custom') {
            medSel.appendChild(opt.cloneNode(true));
            if (opt.value !== 'custom') matchingCount++;
        }
    });

    const countLbl = document.getElementById('medFilterCount');
    if (countLbl) {
        countLbl.textContent = `${matchingCount} produk tersedia`;
    }

    let found = false;
    for (let i = 0; i < medSel.options.length; i++) {
        if (medSel.options[i].value === currentVal) {
            medSel.selectedIndex = i;
            found = true;
            break;
        }
    }
    if (!found && medSel.options.length > 0) {
        medSel.selectedIndex = 0;
    }

    updateMedDetails();
    filterStockRowsByCategory(selectedCategory);
}

function updateMedDetails() {
    const sel = document.getElementById('obatProduk');
    if (!sel || sel.selectedIndex < 0) return;
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    const isCustom = (opt.value === 'custom');
    const customDiv = document.getElementById('fieldCustomMed');
    if (customDiv) {
        customDiv.style.display = isCustom ? 'block' : 'none';
        if (isCustom) {
            const customInput = document.getElementById('obatProdukCustom');
            if (customInput) customInput.focus();
        }
    }

    const dosage = opt.getAttribute('data-dosage') || '-';
    const app = opt.getAttribute('data-app') || '-';
    const sch = opt.getAttribute('data-sch') || '-';
    const unit = opt.getAttribute('data-unit') || 'Botol';
    const catLabel = opt.getAttribute('data-category-label') || '-';
    const indication = opt.getAttribute('data-indication') || '';
    const notes = opt.getAttribute('data-notes') || '';
    const stock = opt.getAttribute('data-stock') || '0';

    // Update Master Details UI
    const elBadge = document.getElementById('medCategoryBadge');
    if (elBadge) elBadge.textContent = catLabel;

    const elIndicationBox = document.getElementById('medIndicationBox');
    const elIndicationText = document.getElementById('medIndicationText');
    if (elIndicationBox && elIndicationText) {
        if (indication) {
            elIndicationBox.style.display = 'block';
            elIndicationText.textContent = indication;
        } else {
            elIndicationBox.style.display = 'none';
        }
    }

    const elDosis = document.getElementById('medDosisText');
    if (elDosis) elDosis.textContent = dosage;

    const elApp = document.getElementById('medAplikasiText');
    if (elApp) elApp.textContent = app;

    const elSch = document.getElementById('medJadwalText');
    if (elSch) elSch.textContent = sch;

    const elNotes = document.getElementById('medNotesText');
    if (elNotes) elNotes.textContent = notes ? `ℹ️ Catatan: ${notes}` : '';

    // Auto sync application method dropdown
    const appSelect = document.getElementById('obatAplikasi');
    if (appSelect) {
        const appLower = app.toLowerCase();
        for (let i = 0; i < appSelect.options.length; i++) {
            const optVal = appSelect.options[i].value.toLowerCase();
            if (appLower.includes(optVal) || (optVal === 'air minum' && appLower.includes('air'))) {
                appSelect.selectedIndex = i;
                break;
            }
        }
    }

    // Auto sync unit dropdown
    const unitSelect = document.getElementById('obatSatuan');
    if (unitSelect) {
        for (let i = 0; i < unitSelect.options.length; i++) {
            if (unitSelect.options[i].value.toLowerCase() === unit.toLowerCase()) {
                unitSelect.selectedIndex = i;
                break;
            }
        }
    }

    // Update Selected Product in Stock Box
    const curStockVal = document.getElementById('obatStokTerpilih');
    if (curStockVal) {
        curStockVal.textContent = `${stock} ${unit}`;
    }
    const curProdName = document.getElementById('obatNamaTerpilih');
    if (curProdName) {
        curProdName.textContent = isCustom ? 'Input Manual (Obat Baru)' : opt.value;
    }

    calcObatSisa();
}

function calcObatSisa() {
    const sel = document.getElementById('obatProduk');
    if (!sel || sel.selectedIndex < 0) return;
    const opt = sel.options[sel.selectedIndex];
    const stok = opt ? parseFloat(opt.getAttribute('data-stock') || 0) : 0;
    const unit = opt ? (opt.getAttribute('data-unit') || 'Botol') : 'Botol';
    const jml = parseFloat(document.getElementById('obatJumlah').value) || 0;
    const sisa = Math.max(0, stok - jml);

    const elSisa = document.getElementById('obatSetelahTx');
    if (elSisa) {
        elSisa.textContent = `${sisa} ${unit}`;
    }
}

function selectProductFromStock(prodName, categoryKey) {
    const katSel = document.getElementById('obatKategori');
    const medSel = document.getElementById('obatProduk');
    if (!katSel || !medSel) return;

    katSel.value = 'all';
    filterMedicines();

    for (let i = 0; i < medSel.options.length; i++) {
        if (medSel.options[i].value === prodName) {
            medSel.selectedIndex = i;
            break;
        }
    }
    updateMedDetails();

    const formSection = document.getElementById('formSectionObat');
    if (formSection) {
        formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function filterStockRowsByCategory(categoryKey) {
    const rows = document.querySelectorAll('.stock-row-item');
    let visibleCount = 0;
    rows.forEach(row => {
        const rowCat = row.getAttribute('data-category');
        if (categoryKey === 'all' || rowCat === categoryKey) {
            row.style.display = 'flex';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    const countLbl = document.getElementById('stockCountLbl');
    if (countLbl) countLbl.textContent = visibleCount;
}

function searchStockRows() {
    const input = document.getElementById('filterStockInput');
    const query = input ? input.value.toLowerCase().trim() : '';
    const katSel = document.getElementById('obatKategori');
    const selectedCategory = katSel ? katSel.value : 'all';

    const rows = document.querySelectorAll('.stock-row-item');
    let visibleCount = 0;
    rows.forEach(row => {
        const rowCat = row.getAttribute('data-category');
        const rowName = row.getAttribute('data-name') || '';
        const matchCategory = (selectedCategory === 'all' || rowCat === selectedCategory);
        const matchQuery = (!query || rowName.includes(query));

        if (matchCategory && matchQuery) {
            row.style.display = 'flex';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    const countLbl = document.getElementById('stockCountLbl');
    if (countLbl) countLbl.textContent = visibleCount;
}

document.addEventListener('DOMContentLoaded', function () {
    // Save master copies of option elements for iOS Safari dropdown rebuilding
    ['prod', 'pakan', 'mort', 'obat', 'bobot'].forEach(prefix => {
        const select = document.getElementById(prefix + 'Coop');
        if (select) {
            masterCoopOptions[prefix] = Array.from(select.options).map(opt => opt.cloneNode(true));
        }
    });

    const pakanWaktuSelect = document.getElementById('pakanWaktu');
    if (pakanWaktuSelect) {
        masterWaktuOptions.push(...Array.from(pakanWaktuSelect.options).map(opt => opt.cloneNode(true)));
    }

    const medSelect = document.getElementById('obatProduk');
    if (medSelect) {
        masterMedicineOptions = Array.from(medSelect.options).map(opt => opt.cloneNode(true));
    }

    const formObat = document.getElementById('formObat');
    if (formObat) {
        formObat.addEventListener('submit', function (e) {
            const sel = document.getElementById('obatProduk');
            if (sel && sel.value === 'custom') {
                const customInput = document.getElementById('obatProdukCustom');
                const customVal = customInput ? customInput.value.trim() : '';
                if (!customVal) {
                    e.preventDefault();
                    alert('Silakan tuliskan nama produk obat/vaksin manual terlebih dahulu.');
                    if (customInput) customInput.focus();
                    return false;
                }
                sel.options[sel.selectedIndex].value = customVal;
            }
        });
    }

    filterCoops('prod');
    filterCoops('pakan');
    filterCoops('mort');
    filterCoops('obat');
    filterCoops('bobot');
    toggleMortalityType();
    updateMedDetails();
    if (currentType) {
        switchType(currentType);
    }
});
</script>
@endsection

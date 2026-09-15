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
            <input type="date" id="inputTxDate" value="{{ $date }}" onchange="window.location.href='?date=' + this.value + '&type=' + currentType">
        </div>
    </section>

    <!-- Section: Pilih Transaksi -->
    <div class="section-title">PILIH TRANSAKSI</div>
    <section class="types">
        <!-- 1. Produksi Telur -->
        <div class="type-card {{ $type === 'produksi' ? 'active' : '' }}" onclick="switchType('produksi')" id="btnTypeProduksi">
            <div class="icon-box">🥚</div>
            <b>Produksi Telur</b>
            <small>Masuk otomatis ke Gudang Telur</small>
        </div>

        <!-- 2. Pemakaian Pakan -->
        <div class="type-card {{ $type === 'pakan' ? 'active' : '' }}" onclick="switchType('pakan')" id="btnTypePakan">
            <div class="icon-box" style="background:#ecfff8;">🌾</div>
            <b>Pemakaian Pakan</b>
            <small>Potong otomatis Gudang Pakan</small>
        </div>

        <!-- 3. Mortalitas -->
        <div class="type-card {{ $type === 'mortalitas' ? 'active' : '' }}" onclick="switchType('mortalitas')" id="btnTypeMortalitas">
            <div class="icon-box">🐔</div>
            <b>Mortalitas</b>
            <small>Update populasi blok</small>
        </div>

        <!-- 4. Vaksin & Obat -->
        <div class="type-card {{ $type === 'obat' ? 'active' : '' }}" onclick="switchType('obat')" id="btnTypeObat">
            <div class="icon-box">💊</div>
            <b>Vaksin & Obat</b>
            <small>Potong otomatis stok obat</small>
        </div>
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
                        <input type="number" name="good_eggs" id="prodTelurBaik" value="" min="0" oninput="calcProduksi()" required placeholder="0">
                    </div>
                    <div class="field">
                        <label>Retak/Pecah (Butir)</label>
                        <input type="number" name="broken_eggs" id="prodRetakPecah" value="0" min="0" oninput="calcProduksi()">
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Telur Rusak (Butir)</label>
                        <input type="number" name="abnormal_eggs" id="prodRusak" value="0" min="0" oninput="calcProduksi()">
                    </div>
                    <div class="field">
                        <label>Jumlah Peti (Opsional)</label>
                        <input type="number" step="0.01" name="crates_count" id="prodPeti" value="0" min="0">
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

                <button type="submit" class="btn-submit">Simpan Produksi</button>
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
                                    $cStd = $coopStandards[$coop->id] ?? ['feed_gram' => 105, 'feed_type' => 'Layer'];
                                @endphp
                                <option value="{{ $coop->id }}" 
                                        data-flock="{{ $coop->flock_id }}" 
                                        data-pop="{{ $coop->active_chickens }}"
                                        data-age="{{ $coop->chicken_age_weeks }}"
                                        data-feed="{{ $cStd['feed_gram'] }}"
                                        data-feedtype="{{ $cStd['feed_type'] }}">
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

                <div class="field">
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

                <div class="sync green">
                    <b>↘ Otomatis potong Gudang Pakan</b>
                    <p>Saat disimpan, pemakaian ini menjadi transaksi keluar/pemakaian. Stok Gudang Pakan langsung berkurang dan masuk ke Rekap & Dashboard.</p>
                </div>

                <button type="submit" class="btn-submit">Simpan Pemakaian</button>
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
                    <label>Jumlah Mati / Afkir</label>
                    <input type="number" name="count" id="mortCount" value="2" min="1" placeholder="Masukkan jumlah ekor" oninput="calcMort()" required>
                </div>

                <div class="field">
                    <label>Status / Kategori</label>
                    <select name="type">
                        <option value="mati">Mati</option>
                        <option value="afkir">Afkir</option>
                    </select>
                </div>

                <div class="field">
                    <label>Penyebab</label>
                    <select name="cause">
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
                        <small>Mortalitas Hari Ini</small>
                        <b class="red">{{ $mortalitasHariIni + 2 }} ekor</b>
                    </div>
                    <div>
                        <small>Total Mati/Afkir</small>
                        <b>{{ $mortalitasHariIni + 2 }} ekor</b>
                    </div>
                    <div>
                        <small>Populasi Farm</small>
                        <b>{{ number_format($totalChickens, 0, ',', '.') }}</b>
                    </div>
                </div>

                <div class="alert-box">
                    <b>⚠ Populasi otomatis diperbarui</b><br>
                    Setelah disimpan, <span id="lblMortCount">2</span> ekor akan dikurangi dari populasi aktif blok terpilih. Populasi baru dipakai oleh Input Produksi dan Input Pakan berikutnya.
                </div>

                <button type="submit" class="btn-submit">Simpan Mortalitas</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Batal</a>
                <div class="info-note">Mortalitas tidak mengubah stok gudang. Transaksi ini memperbarui populasi aktif dan masuk ke Rekap serta Dashboard.</div>
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
                    <label>Kategori</label>
                    <select name="type" id="obatKategori" onchange="filterMedicines()">
                        <option value="vitamin">Vitamin</option>
                        <option value="vaksin">Vaksin</option>
                        <option value="obat">Obat</option>
                        <option value="disinfektan">Disinfektan</option>
                        <option value="mineral">Mineral / Premix</option>
                    </select>
                </div>

                <div class="field">
                    <label>Produk</label>
                    <select name="medicine_name" id="obatProduk" onchange="updateMedDetails()">
                        @foreach($medicines as $med)
                            <option value="{{ $med['name'] }}" data-dosage="{{ $med['dosage'] }}" data-app="{{ $med['application'] }}" data-sch="{{ $med['schedule'] }}" data-unit="{{ $med['unit'] }}" data-stock="{{ $med['stock'] }}">
                                {{ $med['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Acuan dari Master -->
                <div class="master-box">
                    <div class="mastertop">
                        <b>Acuan dari Master</b>
                        <span>✓ Terdaftar</span>
                    </div>
                    <p id="medMasterRef">
                        Dosis: <b>1 g / 2 L air</b> · Aplikasi: <b>Air minum pagi</b> · Jadwal: <b>2×/minggu / cuaca panas</b>
                    </p>
                </div>

                <div class="row-fields">
                    <div class="field">
                        <label>Jumlah Pemakaian</label>
                        <input type="number" step="0.5" name="dosage" id="obatJumlah" value="1" placeholder="Jumlah" oninput="calcObatSisa()" required>
                    </div>
                    <div class="field">
                        <label>Satuan</label>
                        <select id="obatSatuan">
                            <option value="Botol">Botol</option>
                            <option value="Box">Box</option>
                            <option value="Kg">Kg</option>
                            <option value="Gram">Gram</option>
                            <option value="Liter">Liter</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Cara / Aplikasi</label>
                    <select name="application_method">
                        <option value="Air minum">Air minum</option>
                        <option value="Campur pakan">Campur pakan</option>
                        <option value="Tetes mata">Tetes mata</option>
                        <option value="Semprot">Semprot</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="field">
                    <label>Keterangan</label>
                    <input name="notes" placeholder="Opsional...">
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

        <!-- Stok Obat Terkait -->
        <section class="form-card">
            <div class="cardhead">
                <h2>Stok Obat Terkait</h2>
                <span class="tag">Real-time</span>
            </div>
            @foreach($medicines as $m)
                <div class="stock-row">
                    <span>{{ $m['name'] }}</span>
                    <b class="{{ $loop->first ? 'orange' : '' }}">{{ $m['stock'] }} {{ $m['unit'] }}</b>
                </div>
            @endforeach
            <div class="stock-row">
                <span>Setelah Transaksi</span>
                <b class="green" id="obatSetelahTx">9 Botol</b>
            </div>
        </section>
    </div>

</div>

<!-- Interactive Engine Logic for Mobile Input Forms -->
<script>
let currentType = '{{ $type }}';

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

    // Show selected form section
    document.getElementById('formSection' + type.charAt(0).toUpperCase() + type.slice(1)).style.display = 'block';

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
    }
}

// 2. Filter Coops based on Selected Flock
function filterCoops(prefix) {
    const flockSelect = document.getElementById(prefix + 'Kloter');
    const coopSelect = document.getElementById(prefix + 'Coop');
    const selectedFlockId = flockSelect.value;

    let firstMatch = null;
    Array.from(coopSelect.options).forEach(opt => {
        const flockId = opt.getAttribute('data-flock');
        if (flockId === selectedFlockId || !flockId) {
            opt.style.display = '';
            if (!firstMatch) firstMatch = opt;
        } else {
            opt.style.display = 'none';
        }
    });

    if (firstMatch) {
        coopSelect.value = firstMatch.value;
    }
    updateCoopPop(prefix);
}

// 3. Update Population Display for selected Coop
function updateCoopPop(prefix) {
    const coopSelect = document.getElementById(prefix + 'Coop');
    const selectedOpt = coopSelect.options[coopSelect.selectedIndex];
    const pop = selectedOpt ? parseInt(selectedOpt.getAttribute('data-pop') || 762) : 762;

    if (prefix === 'prod') {
        document.getElementById('prodPopulasi').value = pop + ' ekor';
        calcProduksi();
    } else if (prefix === 'pakan') {
        document.getElementById('pakanPopulasi').value = pop + ' ekor';
        calcPakan();
    } else if (prefix === 'mort') {
        document.getElementById('mortPopSebelum').textContent = pop + ' ekor';
        calcMort();
    }
}

// 4. Calculations for Produksi Telur
function calcProduksi() {
    const baik = parseInt(document.getElementById('prodTelurBaik').value) || 0;
    const retakPecah = parseInt(document.getElementById('prodRetakPecah').value) || 0;
    const rusak = parseInt(document.getElementById('prodRusak').value) || 0;
    const total = baik + retakPecah + rusak;

    document.getElementById('prodTotalTelur').value = total + ' butir';
}

// 5. Calculations for Pemakaian Pakan
function calcPakan() {
    const coopSelect = document.getElementById('pakanCoop');
    const selectedOpt = coopSelect.options[coopSelect.selectedIndex];

    const popText = document.getElementById('pakanPopulasi').value;
    const pop = parseInt(popText) || 762;

    const feedGram = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-feed') || 105) : 105;
    const age = selectedOpt ? selectedOpt.getAttribute('data-age') : 21;
    const feedType = selectedOpt ? selectedOpt.getAttribute('data-feedtype') : 'Layer';

    document.getElementById('pakanStdTitle').textContent = `Umur ${age} minggu · ${feedType}`;
    document.getElementById('pakanStdGram').textContent = feedGram.toString().replace('.', ',');

    const stdBlokKg = (pop * feedGram) / 1000;
    const pagiKg = (stdBlokKg * 0.40).toFixed(1);
    const soreKg = (stdBlokKg * 0.60).toFixed(1);

    const karung = Math.floor(stdBlokKg / 50);
    const sisaKg = (stdBlokKg % 50).toFixed(1);
    
    let standarStr = stdBlokKg.toFixed(1).replace('.', ',') + ' Kg';
    if (karung > 0) {
        standarStr += ` (${karung} Karung${sisaKg > 0 ? ' + ' + sisaKg.replace('.', ',') + ' Kg' : ''})`;
    }

    document.getElementById('pakanStdBlok').textContent = standarStr;
    document.getElementById('pakanSudahPagi').textContent = ('' + pagiKg).replace('.', ',') + ' Kg';
    document.getElementById('pakanSisaHari').textContent = ('' + soreKg).replace('.', ',') + ' Kg';
}

function calcPakanSisa() {
    // Fungsi tidak diperlukan lagi karena Stok Terkait dihapus
}

// 6. Calculations for Mortalitas
function calcMort() {
    const popText = document.getElementById('mortPopSebelum').textContent;
    const pop = parseInt(popText) || 762;
    const count = parseInt(document.getElementById('mortCount').value) || 0;
    const setelah = Math.max(0, pop - count);

    document.getElementById('mortPopSetelah').textContent = setelah + ' ekor';
    document.getElementById('lblMortCount').textContent = count;
}

// 7. Details for Vaksin & Obat
function updateMedDetails() {
    const sel = document.getElementById('obatProduk');
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    const dosage = opt.getAttribute('data-dosage') || '-';
    const app = opt.getAttribute('data-app') || '-';
    const sch = opt.getAttribute('data-sch') || '-';
    const unit = opt.getAttribute('data-unit') || 'Botol';

    document.getElementById('medMasterRef').innerHTML = 
        `Dosis: <b>${dosage}</b> · Aplikasi: <b>${app}</b> · Jadwal: <b>${sch}</b>`;
    document.getElementById('obatSatuan').value = unit;

    calcObatSisa();
}

function calcObatSisa() {
    const sel = document.getElementById('obatProduk');
    const opt = sel.options[sel.selectedIndex];
    const stok = opt ? parseFloat(opt.getAttribute('data-stock') || 10) : 10;
    const unit = opt ? (opt.getAttribute('data-unit') || 'Botol') : 'Botol';
    const jml = parseFloat(document.getElementById('obatJumlah').value) || 0;
    const sisa = Math.max(0, stok - jml);

    document.getElementById('obatSetelahTx').textContent = `${sisa} ${unit}`;
}

document.addEventListener('DOMContentLoaded', function () {
    filterCoops('prod');
    filterCoops('pakan');
    filterCoops('mort');
    filterCoops('obat');
    updateMedDetails();
});
</script>
@endsection

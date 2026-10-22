```php
<?php
session_start();

/* =========================================
   1. PROTEKSI HALAMAN LOGIN
   ========================================= */

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'barang.php';

/* =========================================
   2. DATA AWAL INVENTARIS
   ========================================= */

if (!isset($_SESSION['barang'])) {
    $_SESSION['barang'] = [
        [
            'kode' => 'BRG-001',
            'nama' => 'Laptop',
            'kategori' => 'Elektronik',
            'jumlah' => 10,
            'harga' => 5000000,
            'kondisi' => 'Baik'
        ],
        [
            'kode' => 'BRG-002',
            'nama' => 'Proyektor',
            'kategori' => 'Elektronik',
            'jumlah' => 4,
            'harga' => 2500000,
            'kondisi' => 'Baik'
        ],
        [
            'kode' => 'BRG-003',
            'nama' => 'Meja',
            'kategori' => 'Furniture',
            'jumlah' => 15,
            'harga' => 350000,
            'kondisi' => 'Perlu Perbaikan'
        ],
        [
            'kode' => 'BRG-004',
            'nama' => 'Kursi',
            'kategori' => 'Furniture',
            'jumlah' => 20,
            'harga' => 150000,
            'kondisi' => 'Baik'
        ]
    ];
}

/* =========================================
   3. FUNGSI MENGHITUNG NILAI ASET
   ========================================= */

function hitungNilaiAset($jumlah, $harga)
{
    return $jumlah * $harga;
}

/* =========================================
   4. PROSES FORM TAMBAH BARANG
   ========================================= */

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = strtoupper(trim($_POST['kode'] ?? ''));
    $nama = trim($_POST['nama'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $jumlahInput = $_POST['jumlah'] ?? '';
    $hargaInput = $_POST['harga'] ?? '';
    $kondisi = $_POST['kondisi'] ?? '';

    $kategoriValid = [
        'Elektronik',
        'Furniture',
        'ATK',
        'Lainnya'
    ];

    $kondisiValid = [
        'Baik',
        'Perlu Perbaikan',
        'Rusak'
    ];

    if (!preg_match('/^BRG-[0-9]+$/', $kode)) {
        $pesan = 'Kode harus berformat BRG-001 atau BRG-002.';
    } elseif ($nama === '') {
        $pesan = 'Nama barang wajib diisi.';
    } elseif (!in_array($kategori, $kategoriValid, true)) {
        $pesan = 'Silakan pilih kategori yang valid.';
    } elseif (
        filter_var($jumlahInput, FILTER_VALIDATE_INT) === false
        || (int) $jumlahInput < 1
    ) {
        $pesan = 'Jumlah barang harus berupa bilangan bulat minimal 1.';
    } elseif (!ctype_digit((string) $hargaInput)) {
        $pesan = 'Harga satuan harus berupa angka nol atau lebih.';
    } elseif (!in_array($kondisi, $kondisiValid, true)) {
        $pesan = 'Silakan pilih kondisi barang yang valid.';
    } else {
        $jumlah = (int) $jumlahInput;
        $harga = (int) $hargaInput;
        $kodeSudahAda = false;

        foreach ($_SESSION['barang'] as $barang) {
            if ($barang['kode'] === $kode) {
                $kodeSudahAda = true;
                break;
            }
        }

        if ($kodeSudahAda) {
            $pesan = 'Kode barang sudah terdaftar.';
        } else {
            $_SESSION['barang'][] = [
                'kode' => $kode,
                'nama' => $nama,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'kondisi' => $kondisi
            ];

            $totalHarga = hitungNilaiAset($jumlah, $harga);

            $pesan = 'Barang berhasil ditambahkan. '
                   . 'Estimasi nilai aset: Rp '
                   . number_format($totalHarga, 0, ',', '.');
        }
    }
}

/* =========================================
   5. HITUNG TOTAL NILAI SELURUH ASET
   ========================================= */

$totalSemuaAset = 0;

foreach ($_SESSION['barang'] as $barang) {
    $totalSemuaAset += hitungNilaiAset(
        $barang['jumlah'],
        $barang['harga']
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIM-Barang - Inventaris Barang</title>

    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>

<body>

<header>
    <h1>SIM-Barang</h1>
    <p>Sistem Informasi Manajemen Inventaris Barang</p>

    <p>
        Login sebagai:
        <strong>
            <?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna') ?>
        </strong>
    </p>
</header>

<nav>
    <ul>
        <li><a href="#inventaris">Inventaris Barang</a></li>
        <li><a href="#form-barang">Tambah Barang</a></li>
        <li><a href="#ringkasan">Ringkasan Aset</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main>

    <!-- =====================================
         6. TABEL INVENTARIS BARANG
         ===================================== -->

    <section id="inventaris">
        <h2>Data Inventaris Barang</h2>

        <label for="cariBarang">Cari Barang</label>

        <input
            type="search"
            id="cariBarang"
            placeholder="Cari kode atau nama barang..."
        >

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Kondisi</th>
                    </tr>
                </thead>

                <tbody id="tabelBarang">
                    <?php foreach ($_SESSION['barang'] as $barang): ?>

                        <?php
                        // Membuat objek dari class Barang
                        $objekBarang = new Barang(
                            $barang['kode'],
                            $barang['nama'],
                            $barang['jumlah']
                        );
                        ?>

                        <tr>
                            <td>
                                <?= htmlspecialchars($barang['kode']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($barang['nama']) ?>

                                <br>

                                <small>
                                    <?= htmlspecialchars(
                                        $objekBarang->tampilkanInfo()
                                    ) ?>
                                </small>
                            </td>

                            <td>
                                <?= htmlspecialchars($barang['kategori']) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    $barang['jumlah'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    $barang['harga'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    hitungNilaiAset(
                                        $barang['jumlah'],
                                        $barang['harga']
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($barang['kondisi']) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p id="pesanKosong" class="pesan-kosong" hidden>
            Barang tidak ditemukan.
        </p>
    </section>

    <!-- =====================================
         7. FORMULIR TAMBAH BARANG
         ===================================== -->

    <section id="form-barang">
        <h2>Formulir Tambah Barang</h2>

        <?php if ($pesan !== ''): ?>
            <p id="notifikasiBarang" role="status" aria-live="polite">
                <?= htmlspecialchars($pesan) ?>
            </p>
        <?php endif; ?>

        <form
            id="formBarang"
            action="index.php#form-barang"
            method="POST"
        >

            <div class="form-group">
                <label for="kode">Kode Barang</label>

                <input
                    type="text"
                    id="kode"
                    name="kode"
                    placeholder="Contoh: BRG-005"
                    pattern="BRG-[0-9]+"
                    required
                >
            </div>

            <div class="form-group">
                <label for="nama">Nama Barang</label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama barang"
                    required
                >
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>

                <select id="kategori" name="kategori" required>
                    <option value="">Pilih kategori</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Furniture">Furniture</option>
                    <option value="ATK">ATK</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Barang</label>

                <input
                    type="number"
                    id="jumlah"
                    name="jumlah"
                    min="1"
                    step="1"
                    placeholder="Masukkan jumlah"
                    required
                >
            </div>

            <div class="form-group">
                <label for="harga">Harga Satuan (Rp)</label>

                <input
                    type="number"
                    id="harga"
                    name="harga"
                    min="0"
                    step="1"
                    placeholder="Contoh: 500000"
                    required
                >
            </div>

            <div class="form-group">
                <label for="kondisi">Kondisi Barang</label>

                <select id="kondisi" name="kondisi" required>
                    <option value="">Pilih kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Perlu Perbaikan">
                        Perlu Perbaikan
                    </option>
                    <option value="Rusak">Rusak</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit">Simpan Barang</button>
                <button type="reset" class="btn-reset">Reset</button>
            </div>

        </form>
    </section>

    <!-- =====================================
         8. RINGKASAN NILAI ASET
         ===================================== -->

    <section id="ringkasan">
        <h2>Ringkasan Estimasi Nilai Aset</h2>

        <p>
            <strong>Total Estimasi Nilai Seluruh Aset:</strong>
            Rp <?= number_format($totalSemuaAset, 0, ',', '.') ?>
        </p>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Estimasi Nilai Aset</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($_SESSION['barang'] as $barang): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($barang['kode']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($barang['nama']) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    $barang['jumlah'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    $barang['harga'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    hitungNilaiAset(
                                        $barang['jumlah'],
                                        $barang['harga']
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<footer>
    <p>
        &copy; 2026 SIM-Barang - Sistem Informasi Inventaris Barang
    </p>
</footer>

</body>
</html>
```
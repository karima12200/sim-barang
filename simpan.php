
<?php
$hasil = null;
$error = "";

// Memproses formulir ketika dikirim menggunakan POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $jumlah = $_POST["jumlah"] ?? "";
    $harga = $_POST["harga"] ?? "";

    // Validasi data yang dimasukkan
    if (
        $nama === "" ||
        $jumlah === "" ||
        $harga === "" ||
        !is_numeric($jumlah) ||
        !is_numeric($harga) ||
        (float) $jumlah <= 0 ||
        (float) $harga < 0
    ) {
        $error = "Mohon isi semua data dengan benar.";
    } else {
        $jumlah = (int) $jumlah;
        $harga = (float) $harga;

        // Menghitung total estimasi nilai aset
        $total = hitungTotal($jumlah, $harga);

        $hasil = [
            "nama" => $nama,
            "jumlah" => $jumlah,
            "harga" => $harga,
            "total" => $total
        ];
    }
}

// Fungsi perhitungan total nilai aset
function hitungTotal($jumlah, $harga) {
    return $jumlah * $harga;
}

// Fungsi format Rupiah
function rupiah($nilai) {
    return "Rp " . number_format($nilai, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pertemuan 5 - SIM-Barang</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .php-container {
            width: min(900px, calc(100% - 32px));
            box-sizing: border-box;
            margin: 36px auto;
            padding: 28px;
            background: #ffffff;
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }

        .php-container h2 {
            margin-top: 0;
            color: #2459d3;
        }

        .tombol-kembali {
            display: inline-block;
            margin-top: 14px;
            padding: 10px 16px;
            color: #ffffff;
            background: #1e3a8a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .tombol-kembali:hover {
            background: #172554;
        }

        .form-barang {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-grup label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        .form-grup input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-grup input:focus {
            outline: 2px solid #93b4ff;
            border-color: #2563eb;
        }

        .tombol-hitung {
            width: 100%;
            padding: 13px 18px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .tombol-hitung:hover {
            background: #1d4ed8;
        }

        .pesan-error {
            margin-top: 18px;
            padding: 12px;
            color: #991b1b;
            background: #fee2e2;
            border-radius: 8px;
        }

        .tabel-ringkasan {
            margin-top: 30px;
            overflow-x: auto;
        }

        .tabel-ringkasan table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-ringkasan th,
        .tabel-ringkasan td {
            padding: 12px;
            border: 1px solid #dbe3ee;
            text-align: left;
        }

        .tabel-ringkasan th {
            color: #ffffff;
            background: #2459d3;
        }

        .total-nilai {
            color: #166534;
            font-weight: bold;
        }

        @media (min-width: 600px) {
            .form-barang {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-grup:last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 480px) {
            .php-container {
                padding: 18px;
            }

            .tabel-ringkasan table {
                min-width: 500px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>SIM-Barang</h1>
    <p>Pertemuan 5 - Pengolahan Formulir PHP</p>

    <a href="index.html" class="tombol-kembali">
        &larr; Kembali ke Halaman Inventaris
    </a>
</header>

<main>
    <section class="php-container">
        <h2>Form Input Barang</h2>

        <p>
            Masukkan nama barang, jumlah barang, dan harga satuan
            untuk menghitung estimasi total nilai aset.
        </p>

        <form
            action="simpan.php"
            method="POST"
            class="form-barang"
        >
            <div class="form-grup">
                <label for="nama">Nama Barang</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Contoh: Laptop"
                    value="<?= htmlspecialchars($_POST["nama"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                    required
                >
            </div>

            <div class="form-grup">
                <label for="jumlah">Jumlah Barang</label>
                <input
                    type="number"
                    id="jumlah"
                    name="jumlah"
                    min="1"
                    step="1"
                    placeholder="Contoh: 5"
                    value="<?= htmlspecialchars($_POST["jumlah"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                    required
                >
            </div>

            <div class="form-grup">
                <label for="harga">Harga Satuan (Rp)</label>
                <input
                    type="number"
                    id="harga"
                    name="harga"
                    min="0"
                    step="1"
                    placeholder="Contoh: 7000000"
                    value="<?= htmlspecialchars($_POST["harga"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                    required
                >
            </div>

            <div class="form-grup">
                <button type="submit" class="tombol-hitung">
                    Hitung Total
                </button>
            </div>
        </form>

        <?php if ($error !== ""): ?>
            <p class="pesan-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <?php if ($hasil !== null): ?>
            <div class="tabel-ringkasan">
                <h2>Ringkasan Data Barang</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total Nilai Aset</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <?= htmlspecialchars($hasil["nama"], ENT_QUOTES, "UTF-8") ?>
                            </td>
                            <td>
                                <?= number_format($hasil["jumlah"], 0, ",", ".") ?>
                            </td>
                            <td>
                                <?= rupiah($hasil["harga"]) ?>
                            </td>
                            <td class="total-nilai">
                                <?= rupiah($hasil["total"]) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>SIM-Barang - Praktikum Pemrograman Web</p>
</footer>

</body>
</html>

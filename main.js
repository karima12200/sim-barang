
const pencarian = document.getElementById("cariBarang");
const tabel = document.getElementById("tabelBarang");
const form = document.getElementById("formBarang");
const notifikasi = document.getElementById("notifikasiBarang");
const pesanKosong = document.getElementById("pesanKosong");

// ==================================
// 1. PENCARIAN BARANG SECARA LANGSUNG
// ==================================

pencarian.addEventListener("input", tampilkanHasilPencarian);

function tampilkanHasilPencarian() {
    const kataKunci = pencarian.value.toLowerCase().trim();
    let jumlahTampil = 0;

    Array.from(tabel.rows).forEach(function (baris) {
        const isiBaris = baris.textContent.toLowerCase();
        const cocok = isiBaris.includes(kataKunci);

        baris.style.display = cocok ? "" : "none";

        if (cocok) {
            jumlahTampil++;
        }
    });

    pesanKosong.hidden = jumlahTampil > 0;
}

// ==================================
// 2. VALIDASI DAN TAMBAH BARANG
// ==================================

form.addEventListener("submit", function (event) {
    // Mencegah halaman dimuat ulang
    event.preventDefault();

    const kode = document.getElementById("kode").value.trim();
    const nama = document.getElementById("nama").value.trim();
    const kategori = document.getElementById("kategori").value;
    const jumlah = document.getElementById("jumlah").value;
    const kondisi = document.getElementById("kondisi").value;

    // Validasi format kode: BRG-001, BRG-002, dst.
    if (!/^BRG-\d+$/.test(kode)) {
        alert("Format kode salah! Contoh yang benar: BRG-005.");
        document.getElementById("kode").focus();
        return;
    }

    // Validasi jumlah harus bilangan bulat positif
    if (
        jumlah === "" ||
        !Number.isInteger(Number(jumlah)) ||
        Number(jumlah) <= 0
    ) {
        alert("Jumlah harus berupa bilangan bulat lebih dari 0.");
        document.getElementById("jumlah").focus();
        return;
    }

    // Cegah kode barang yang sama
    const kodeSudahAda = Array.from(tabel.rows).some(function (baris) {
        return baris.cells[0].textContent.trim().toLowerCase()
            === kode.toLowerCase();
    });

    if (kodeSudahAda) {
        alert("Kode barang sudah digunakan. Silakan gunakan kode lain.");
        document.getElementById("kode").focus();
        return;
    }

    // Buat baris baru pada tabel
    const barisBaru = tabel.insertRow();

    [kode, nama, kategori, jumlah, kondisi].forEach(function (isi) {
        const sel = barisBaru.insertCell();
        sel.textContent = isi;
    });

    // Kosongkan pencarian supaya baris baru terlihat
    pencarian.value = "";
    tampilkanHasilPencarian();

    // Tampilkan notifikasi keberhasilan
    notifikasi.textContent =
        "Barang " + nama + " berhasil ditambahkan!";

    // Kosongkan form setelah berhasil
    form.reset();
});

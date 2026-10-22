
const pencarian = document.getElementById("cariBarang");
const tabel = document.getElementById("tabelBarang");
const form = document.getElementById("formBarang");
const notifikasi = document.getElementById("notifikasiBarang");
const pesanKosong = document.getElementById("pesanKosong");

// FITUR PENCARIAN BARANG
pencarian.addEventListener("input", function () {
    tampilkanHasilPencarian();
});

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

// FITUR TAMBAH BARANG
form.addEventListener("submit", function (event) {
    event.preventDefault();

    const kode = document.getElementById("kode").value.trim();
    const nama = document.getElementById("nama").value.trim();
    const kategori = document.getElementById("kategori").value;
    const jumlah = document.getElementById("jumlah").value;
    const kondisi = document.getElementById("kondisi").value;

    // Validasi kode barang
    if (!/^BRG-\d+$/.test(kode)) {
        alert("Kode barang harus berformat BRG- diikuti angka, contoh BRG-005.");
        return;
    }

    // Validasi jumlah barang
    if (Number(jumlah) <= 0 || !Number.isInteger(Number(jumlah))) {
        alert("Jumlah barang harus berupa bilangan bulat lebih dari 0.");
        return;
    }

    // Cek apakah kode sudah digunakan
    const kodeSudahAda = Array.from(tabel.rows).some(function (baris) {
        return baris.cells[0].textContent.trim().toLowerCase()
            === kode.toLowerCase();
    });

    if (kodeSudahAda) {
        alert("Kode barang sudah ada. Gunakan kode yang berbeda.");
        return;
    }

    // Membuat baris baru
    const barisBaru = tabel.insertRow();

    [kode, nama, kategori, jumlah, kondisi].forEach(function (isi) {
        const sel = barisBaru.insertCell();
        sel.textContent = isi;
    });

    // Reset pencarian agar barang baru terlihat
    pencarian.value = "";
    tampilkanHasilPencarian();

    notifikasi.textContent = "Barang " + nama + " berhasil ditambahkan!";
    form.reset();
});

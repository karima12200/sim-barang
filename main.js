
document.addEventListener("DOMContentLoaded", function () {
    const pencarian = document.getElementById("cariBarang");
    const tabel = document.getElementById("tabelBarang");
    const form = document.getElementById("formBarang");
    const notifikasi = document.getElementById("notifikasiBarang");
    const pesanKosong = document.getElementById("pesanKosong");

    // =========================================
    // FITUR 1: PENCARIAN TABEL REAL-TIME
    // =========================================
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

    pencarian.addEventListener("input", tampilkanHasilPencarian);

    // =========================================
    // FITUR 2: NOTIFIKASI TOAST
    // =========================================
    function tampilkanToast(pesan) {
        let toast = document.getElementById("toastNotifikasi");

        if (!toast) {
            toast = document.createElement("div");
            toast.id = "toastNotifikasi";
            toast.setAttribute("role", "status");
            toast.setAttribute("aria-live", "polite");

            Object.assign(toast.style, {
                position: "fixed",
                bottom: "24px",
                right: "24px",
                maxWidth: "calc(100% - 32px)",
                padding: "14px 20px",
                borderRadius: "8px",
                backgroundColor: "#166534",
                color: "#ffffff",
                boxShadow: "0 4px 12px rgba(0,0,0,0.2)",
                zIndex: "9999",
                fontSize: "14px",
                opacity: "0",
                transition: "opacity 0.25s ease"
            });

            document.body.appendChild(toast);
        }

        toast.textContent = pesan;
        toast.style.opacity = "1";

        // Batalkan animasi sembunyi sebelumnya
        if (toast.timer) {
            clearTimeout(toast.timer);
        }

        toast.timer = setTimeout(function () {
            toast.style.opacity = "0";
        }, 3000);
    }

    // =========================================
    // FITUR 3: VALIDASI DAN TAMBAH BARANG
    // =========================================
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const kodeInput = document.getElementById("kode");
        const namaInput = document.getElementById("nama");
        const kategoriInput = document.getElementById("kategori");
        const jumlahInput = document.getElementById("jumlah");
        const kondisiInput = document.getElementById("kondisi");

        const kode = kodeInput.value.trim();
        const nama = namaInput.value.trim();
        const kategori = kategoriInput.value;
        const jumlah = jumlahInput.value;
        const kondisi = kondisiInput.value;

        // Validasi format kode BRG- diikuti angka
        if (!/^BRG-\d+$/.test(kode)) {
            alert(
                "Kode barang tidak valid!\n" +
                "Gunakan format BRG- diikuti angka, contoh BRG-001."
            );
            kodeInput.focus();
            return;
        }

        // Validasi jumlah numerik positif
        if (jumlah === "" || !Number.isFinite(Number(jumlah)) ||
            Number(jumlah) <= 0) {
            alert("Jumlah barang harus berupa angka lebih dari 0.");
            jumlahInput.focus();
            return;
        }

        // Cegah kode barang duplikat
        const kodeSudahAda = Array.from(tabel.rows).some(function (baris) {
            return baris.cells[0].textContent.trim().toLowerCase() ===
                kode.toLowerCase();
        });

        if (kodeSudahAda) {
            alert("Kode barang sudah terdaftar. Gunakan kode lain.");
            kodeInput.focus();
            return;
        }

        // Buat baris baru menggunakan DOM
        const barisBaru = tabel.insertRow();
        const dataBarang = [kode, nama, kategori, jumlah, kondisi];

        dataBarang.forEach(function (data) {
            const sel = barisBaru.insertCell();
            sel.textContent = data;
        });

        // Reset pencarian agar barang baru terlihat
        pencarian.value = "";
        tampilkanHasilPencarian();

        // Bersihkan formulir
        form.reset();

        // Tampilkan toast sukses
        tampilkanToast("Barang " + nama + " berhasil ditambahkan!");
    });
});

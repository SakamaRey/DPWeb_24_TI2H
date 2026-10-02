// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
// Memakai event delegation di document karena baris tabel sekarang
// dirender dinamis via fetch (lihat buku.js/anggota.js) sehingga
// tombol .btn-hapus belum tentu ada saat DOMContentLoaded.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        console.log(e.target);

        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
        }
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teksJudul = row.querySelector("td").textContent.toLowerCase();
            row.style.display = teksJudul.includes(keyword) ? "" : "none";
        });
    });

    function updateCounter() {
    const barisTerlihat = Array.from(document.querySelectorAll("tbody tr")).filter(row => row.style.display !== "none").length;
    const totalBaris = document.querySelectorAll("tbody tr").length;
    document.getElementById("counter-buku").textContent = `Menampilkan ${barisTerlihat} dari ${totalBaris} baris`;
}
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const judulWajib = ['judul', 'nama', 'pengarang', 'alamat'];
        judulWajib.forEach(judulName => {
        const judul = form.querySelector("[name='judul'], [name='nama']");
        if (judul && judul.value.trim() === "") {
            tampilkanError(judul, "Field ini wajib diisi.");
            valid = false;
        } else if (judul) {
            hapusError(judul);
        }
    });

        const isbn = form.querySelector("[name='isbn']");
        if (isbn) {
            const regexIsbn = /^[0-9-]+$/;
            if (isbn.value.trim() !== "" && !regexIsbn.test(isbn.value)) {
                tampilkanError(isbn, "ISBN hanya boleh berisi angka dan tanda hubung (-).");
                valid = false;
            } else {
                hapusError(isbn);
            }
        }
        // ...(pengecekan pengarang, tahun, stok dengan pola serupa)...

        if (!valid) {
            e.preventDefault();
        }
    });
}

async function muatDataTabel(urlJson, renderRowCallback) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;
    loading.style.display = "block";
    tbody.innerHTML = "";
    try {
        await new Promise((resolve) => setTimeout(resolve, 600));
        const res = await fetch(urlJson);
        if (!res.ok) throw new Error("Gagal mengambil data");
        const data = await res.json();
        data.forEach(item => {
            const tr = document.createElement("tr");
            tr.innerHTML = renderRowCallback(item);
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="5">Gagal: ${err.message}</td></tr>`;
    } finally {
        loading.style.display = "none";
    }
}

// ...fungsi init lain...

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});
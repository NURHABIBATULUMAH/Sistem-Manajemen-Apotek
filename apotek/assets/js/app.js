// assets/js/app.js

// ---- Konfirmasi hapus ----
document.addEventListener('DOMContentLoaded', function () {

  // Tombol hapus dengan konfirmasi
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const nama = this.dataset.nama || 'item ini';
      if (!confirm(`Yakin ingin menghapus "${nama}"?`)) {
        e.preventDefault();
      }
    });
  });

  // Auto-dismiss alert setelah 4 detik
  document.querySelectorAll('.alert-auto').forEach(el => {
    setTimeout(() => {
      el.classList.add('fade');
      setTimeout(() => el.remove(), 300);
    }, 4000);
  });

  // Format input angka ke Rupiah saat blur
  document.querySelectorAll('.input-rupiah').forEach(el => {
    el.addEventListener('blur', function () {
      const val = parseFloat(this.value.replace(/[^0-9]/g, ''));
      if (!isNaN(val)) this.value = val.toLocaleString('id-ID');
    });
    el.addEventListener('focus', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
  });

  // Hitung subtotal otomatis di tabel detail transaksi
  document.querySelectorAll('.qty-input, .harga-input').forEach(el => {
    el.addEventListener('input', hitungSubtotal);
  });

  function hitungSubtotal() {
    let grandTotal = 0;
    document.querySelectorAll('tr.row-detail').forEach(row => {
      const qty    = parseFloat(row.querySelector('.qty-input')?.value   || 0);
      const harga  = parseFloat(row.querySelector('.harga-input')?.value || 0);
      const sub    = qty * harga;
      const elSub  = row.querySelector('.subtotal-text');
      if (elSub) elSub.textContent = 'Rp ' + sub.toLocaleString('id-ID');
      grandTotal += sub;
    });
    const elGrand = document.getElementById('grand-total');
    if (elGrand) elGrand.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
  }

});

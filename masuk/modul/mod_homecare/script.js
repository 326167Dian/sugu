// Fungsi Pindah Form
function showForm(type) {
    document.querySelectorAll('.form-page').forEach(f => f.style.display = 'none');
    document.querySelectorAll('.main-nav button').forEach(b => b.classList.remove('active'));
    
    document.getElementById('form-' + type).style.display = 'block';
    document.getElementById('btn-' + type).classList.add('active');
}

// Fungsi Tambah Baris Tabel
function addRow(targetId) {
    const tbody = document.getElementById(targetId);
    const rowCount = tbody.children.length + 1;
    const tr = document.createElement('tr');
    
    if (targetId === 'tbody-catatan') {
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" class="input-inline" style="width:100%"></td>
            <td><input type="text" class="input-inline" style="width:100%"></td>
            <td><textarea class="auto-line" rows="2"></textarea></td>
            <td><textarea class="auto-line" rows="2"></textarea></td>
        `;
    } else {
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" class="input-inline" style="width:100%"></td>
            <td><textarea class="auto-line" rows="3"></textarea></td>
        `;
    }
    tbody.appendChild(tr);
}

// Fitur Auto-Save ke LocalStorage
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('save-local')) {
        const key = e.target.getAttribute('data-key');
        localStorage.setItem(key, e.target.type === 'checkbox' || e.target.type === 'radio' ? e.target.checked : e.target.value);
    }
});

// Load data saat halaman dibuka
window.onload = function() {
    document.querySelectorAll('.save-local').forEach(el => {
        const saved = localStorage.getItem(el.getAttribute('data-key'));
        if (saved !== null) {
            if (el.type === 'checkbox' || el.type === 'radio') el.checked = saved === 'true';
            else el.value = saved;
        }
    });
    
    // Default baris tabel
    addRow('tbody-catatan');
    addRow('tbody-homecare');
    showForm('pio');
};

// Export PDF
function exportPDF() {
    const activeForm = document.querySelector('.form-page[style*="block"]');
    const opt = {
        margin: 10,
        filename: 'Dokumen_Apotek.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(activeForm).save();
}

function clearAllData() {
    if (confirm("Hapus semua data yang sudah diisi?")) {
        localStorage.clear();
        location.reload();
    }
}
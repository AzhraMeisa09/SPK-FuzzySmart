import './bootstrap';

// ═══════════════════════════════════════════════════════
//  SPK FUZZY SMART — Main JavaScript
// ═══════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initRoleSwitcher();
    initModals();
    initToasts();
    initSearch();
    initPagination();
    initPenilaian();
    initProgressBars();
    initCharts();
    initDropdowns();
    highlightActiveNav();
});

// ─── MOCK DATA ──────────────────────────────────────────
window.MOCK = {
    currentRole: localStorage.getItem('spk_role') || 'admin',

    users: [
        { id: 1, name: 'Dr. Siti Rahmawati', email: 'admin@spk.id',   role: 'admin',    status: 'Aktif' },
        { id: 2, name: 'Budi Santoso, S.Pd', email: 'guru1@spk.id',   role: 'guru',     status: 'Aktif' },
        { id: 3, name: 'Dewi Kurniasih, S.Pd',email: 'guru2@spk.id',  role: 'guru',     status: 'Aktif' },
        { id: 4, name: 'H. Supriyadi, M.Pd',  email: 'kepsek@spk.id', role: 'kepsek',   status: 'Aktif' },
        { id: 5, name: 'Ahmad Fatoni',         email: 'wali1@spk.id',  role: 'wali',     status: 'Aktif' },
        { id: 6, name: 'Rina Puspita',         email: 'wali2@spk.id',  role: 'wali',     status: 'Aktif' },
    ],

    siswa: [
        { id: 1,  nisn: '0012345671', nama: 'Ahmad Rizky Pratama',  kelas: 'A', wali: 'Ahmad Fatoni',    status: 'final'  },
        { id: 2,  nisn: '0012345672', nama: 'Siti Nurhaliza',       kelas: 'A', wali: 'Siti Khadijah',  status: 'final'  },
        { id: 3,  nisn: '0012345673', nama: 'Muhammad Farhan D.',   kelas: 'A', wali: 'Dedi Firmansyah', status: 'draft'  },
        { id: 4,  nisn: '0012345674', nama: 'Anisa Putri Rahayu',   kelas: 'A', wali: 'Rina Puspita',   status: 'belum'  },
        { id: 5,  nisn: '0012345675', nama: 'Budi Prasetyo',        kelas: 'A', wali: 'Prasetyo Ali',   status: 'final'  },
        { id: 6,  nisn: '0012345676', nama: 'Indira Cahyani',       kelas: 'B', wali: 'Cahyani Dewi',   status: 'final'  },
        { id: 7,  nisn: '0012345677', nama: 'Reza Firmansyah',      kelas: 'B', wali: 'Firmansyah J.',  status: 'draft'  },
        { id: 8,  nisn: '0012345678', nama: 'Putri Maulida Sari',   kelas: 'B', wali: 'Maulida H.',     status: 'belum'  },
        { id: 9,  nisn: '0012345679', nama: 'Dimas Pratama W.',     kelas: 'B', wali: 'Pratama W.',     status: 'final'  },
        { id: 10, nisn: '0012345680', nama: 'Nadia Safitri',        kelas: 'B', wali: 'Safitri L.',     status: 'final'  },
        { id: 11, nisn: '0012345681', nama: 'Yoga Aditya Nugraha',  kelas: 'C', wali: 'Nugraha S.',     status: 'belum'  },
        { id: 12, nisn: '0012345682', nama: 'Lestari Dewi Ningsih', kelas: 'C', wali: 'Ningsih R.',     status: 'draft'  },
        { id: 13, nisn: '0012345683', nama: 'Fajar Nugroho P.',     kelas: 'C', wali: 'Nugroho P.',     status: 'final'  },
        { id: 14, nisn: '0012345684', nama: 'Maya Sari Utami',      kelas: 'C', wali: 'Utami S.',       status: 'final'  },
        { id: 15, nisn: '0012345685', nama: 'Rizal Mahendra',       kelas: 'C', wali: 'Mahendra B.',    status: 'belum'  },
    ],

    kriteria: [
        { id: 1, nama: 'Nilai Akademik',       bobot: 30, subkriteria: 4 },
        { id: 2, nama: 'Perkembangan Motorik', bobot: 25, subkriteria: 3 },
        { id: 3, nama: 'Sosial-Emosional',     bobot: 25, subkriteria: 3 },
        { id: 4, nama: 'Kemampuan Bahasa',     bobot: 20, subkriteria: 3 },
    ],

    subkriteria: [
        { id: 1, nama: 'Mengenal Angka 1–20',       kriteria: 'Nilai Akademik',       bobot: 8, rubrik_mb: 'Belum mengenal', rubrik_bsh: 'Mengenal sebagian', rubrik_bsb: 'Mengenal semua' },
        { id: 2, nama: 'Mengenal Huruf Abjad',      kriteria: 'Nilai Akademik',       bobot: 8, rubrik_mb: 'Belum kenal',    rubrik_bsh: 'Kenal sebagian',    rubrik_bsb: 'Kenal semua' },
        { id: 3, nama: 'Pemecahan Masalah Sederhana',kriteria: 'Nilai Akademik',      bobot: 7, rubrik_mb: 'Butuh bimb.',    rubrik_bsh: 'Kadang mandiri',    rubrik_bsb: 'Mandiri' },
        { id: 4, nama: 'Kemampuan Berhitung',       kriteria: 'Nilai Akademik',       bobot: 7, rubrik_mb: 'Belum bisa',    rubrik_bsh: 'Sebagian bisa',     rubrik_bsb: 'Sangat baik' },
        { id: 5, nama: 'Motorik Halus',             kriteria: 'Perkembangan Motorik', bobot: 9, rubrik_mb: 'Belum terampil',rubrik_bsh: 'Cukup terampil',   rubrik_bsb: 'Sangat terampil' },
        { id: 6, nama: 'Motorik Kasar',             kriteria: 'Perkembangan Motorik', bobot: 8, rubrik_mb: 'Perlu latihan', rubrik_bsh: 'Cukup baik',       rubrik_bsb: 'Sangat baik' },
        { id: 7, nama: 'Koordinasi Mata-Tangan',    kriteria: 'Perkembangan Motorik', bobot: 8, rubrik_mb: 'Lemah',         rubrik_bsh: 'Sedang',            rubrik_bsb: 'Kuat' },
        { id: 8, nama: 'Interaksi Sosial',          kriteria: 'Sosial-Emosional',     bobot: 9, rubrik_mb: 'Menyendiri',    rubrik_bsh: 'Mulai berinteraksi',rubrik_bsb: 'Aktif berinteraksi' },
        { id: 9, nama: 'Pengelolaan Emosi',         kriteria: 'Sosial-Emosional',     bobot: 8, rubrik_mb: 'Sering tantrum',rubrik_bsh: 'Kadang terkontrol', rubrik_bsb: 'Terkontrol' },
        { id: 10,nama: 'Kerjasama',                 kriteria: 'Sosial-Emosional',     bobot: 8, rubrik_mb: 'Tidak mau',     rubrik_bsh: 'Mau dengan bimb.',  rubrik_bsb: 'Inisiatif' },
        { id: 11,nama: 'Kosakata',                  kriteria: 'Kemampuan Bahasa',     bobot: 7, rubrik_mb: 'Terbatas',       rubrik_bsh: 'Cukup banyak',     rubrik_bsb: 'Sangat banyak' },
        { id: 12,nama: 'Kemampuan Bercerita',       kriteria: 'Kemampuan Bahasa',     bobot: 7, rubrik_mb: 'Belum bisa',    rubrik_bsh: 'Sederhana',         rubrik_bsb: 'Lancar & runtut' },
        { id: 13,nama: 'Pemahaman Instruksi',       kriteria: 'Kemampuan Bahasa',     bobot: 6, rubrik_mb: 'Tidak paham',   rubrik_bsh: 'Paham sederhana',  rubrik_bsb: 'Paham kompleks' },
    ],

    tahunAjaran: [
        { id: 1, nama: '2024/2025', mulai: '2024-07-15', selesai: '2025-06-30', aktif: true },
        { id: 2, nama: '2023/2024', mulai: '2023-07-17', selesai: '2024-06-28', aktif: false },
    ],

    kelas: [
        { id: 1, nama: 'Kelas A', wali: 'Budi Santoso, S.Pd',   siswa: 5, ta: '2024/2025' },
        { id: 2, nama: 'Kelas B', wali: 'Dewi Kurniasih, S.Pd', siswa: 5, ta: '2024/2025' },
        { id: 3, nama: 'Kelas C', wali: 'Budi Santoso, S.Pd',   siswa: 5, ta: '2024/2025' },
    ],

    minggu: [
        { id: 1, nama: 'Minggu 1', tanggal: '13–17 Jan 2025', subkriteria: 3, aktif: true },
        { id: 2, nama: 'Minggu 2', tanggal: '20–24 Jan 2025', subkriteria: 3, aktif: false },
        { id: 3, nama: 'Minggu 3', tanggal: '27–31 Jan 2025', subkriteria: 3, aktif: false },
        { id: 4, nama: 'Minggu 4', tanggal: '03–07 Feb 2025', subkriteria: 4, aktif: false },
    ],

    evaluasi: [
        { siswa_id: 1, nama: 'Ahmad Rizky Pratama',  nilai: 82.5, kategori: 'BSB', kognitif: 85, motorik: 80, sosial: 78, bahasa: 88 },
        { siswa_id: 2, nama: 'Siti Nurhaliza',        nilai: 74.0, kategori: 'BSH', kognitif: 72, motorik: 76, sosial: 80, bahasa: 68 },
        { siswa_id: 3, nama: 'Muhammad Farhan D.',    nilai: 58.5, kategori: 'MB',  kognitif: 55, motorik: 60, sosial: 58, bahasa: 62 },
        { siswa_id: 4, nama: 'Anisa Putri Rahayu',    nilai: 79.0, kategori: 'BSH', kognitif: 80, motorik: 75, sosial: 82, bahasa: 79 },
        { siswa_id: 5, nama: 'Budi Prasetyo',         nilai: 88.0, kategori: 'BSB', kognitif: 90, motorik: 85, sosial: 88, bahasa: 89 },
    ],
};

// ─── SIDEBAR ────────────────────────────────────────────
function initSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');
    if (!sidebar) return;

    const toggle = () => {
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        sidebar.classList.toggle('-translate-x-full', isOpen);
        overlay?.classList.toggle('opacity-0', isOpen);
        overlay?.classList.toggle('pointer-events-none', isOpen);
    };

    toggleBtn?.addEventListener('click', toggle);
    overlay?.addEventListener('click', toggle);

    // Desktop: always show
    const mq = window.matchMedia('(min-width: 1024px)');
    const handleMQ = (e) => {
        if (e.matches) {
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    };
    mq.addEventListener('change', handleMQ);
    handleMQ(mq);
}

function highlightActiveNav() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-item[data-path]').forEach(el => {
        const itemPath = el.getAttribute('data-path');
        const isActive = path === itemPath || (itemPath !== '/' && path.startsWith(itemPath));
        el.classList.toggle('active', isActive);
    });
}

// ─── ROLE SWITCHER ──────────────────────────────────────
function initRoleSwitcher() {
    const switcher = document.getElementById('role-switcher-btn');
    const menu     = document.getElementById('role-menu');
    if (!switcher) return;

    switcher.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    document.querySelectorAll('[data-role]').forEach(el => {
        el.addEventListener('click', () => {
            const role = el.getAttribute('data-role');
            localStorage.setItem('spk_role', role);
            window.MOCK.currentRole = role;
            window.location.href = '/dashboard';
        });
    });

    document.addEventListener('click', () => menu?.classList.add('hidden'));

    // Display role badge color
    const roleEl = document.getElementById('current-role-badge');
    if (roleEl) {
        const roleMap = {
            admin:  { label: 'Admin',           cls: 'badge-admin' },
            guru:   { label: 'Guru',            cls: 'badge-guru'  },
            kepsek: { label: 'Kepala Sekolah',  cls: 'badge-kepsek'},
            wali:   { label: 'Wali Murid',      cls: 'badge-wali'  },
        };
        const r = roleMap[window.MOCK.currentRole] || roleMap.admin;
        roleEl.textContent = r.label;
        roleEl.className   = `badge ${r.cls}`;
    }

    // Update sidebar menu visibility
    document.querySelectorAll('[data-role-menu]').forEach(el => {
        const allowed = el.getAttribute('data-role-menu').split(',');
        el.style.display = allowed.includes(window.MOCK.currentRole) ? '' : 'none';
    });
}

// ─── MODALS ─────────────────────────────────────────────
function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-modal-open');
            openModal(id);
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-modal-close');
            closeModal(id);
        });
    });
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeModal(backdrop.id);
        });
    });
}

window.openModal  = (id) => document.getElementById(id)?.classList.add('open');
window.closeModal = (id) => document.getElementById(id)?.classList.remove('open');

// ─── TOASTS ─────────────────────────────────────────────
function initToasts() {}

window.showToast = (msg, type = 'info', duration = 3000) => {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const icons = {
        success: '✓',
        error:   '✕',
        warning: '⚠',
        info:    'ℹ',
    };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span style="font-size:1rem;font-weight:700">${icons[type]||icons.info}</span><span style="font-size:0.875rem;font-weight:500">${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

// ─── SEARCH ─────────────────────────────────────────────
function initSearch() {
    document.querySelectorAll('[data-search-table]').forEach(input => {
        const targetId = input.getAttribute('data-search-table');
        const tbody    = document.querySelector(`#${targetId} tbody`);
        if (!tbody) return;
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            tbody.querySelectorAll('tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });
}

// ─── PAGINATION (mock) ───────────────────────────────────
function initPagination() {
    document.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.pagination')?.querySelectorAll('.pagination-btn').forEach(b => b.classList.remove('active-page'));
            btn.classList.add('active-page');
        });
    });
}

// ─── PENILAIAN ──────────────────────────────────────────
function initPenilaian() {
    // Track penilaian progress
    const radios = document.querySelectorAll('.penilaian-radio');
    const progressEl = document.getElementById('penilaian-progress');
    const progressBar = document.getElementById('penilaian-progress-bar');
    const totalEl = document.getElementById('penilaian-total');

    if (!radios.length) return;

    const total = document.querySelectorAll('.subkriteria-group').length;
    if (totalEl) totalEl.textContent = total;

    const updateProgress = () => {
        const answered = new Set();
        radios.forEach(r => { if (r.checked) answered.add(r.name); });
        const count = answered.size;
        const pct   = total > 0 ? Math.round((count / total) * 100) : 0;
        if (progressEl)  progressEl.textContent = count;
        if (progressBar) progressBar.style.width = pct + '%';

        // Mark card as completed
        document.querySelectorAll('.subkriteria-group').forEach(group => {
            const name = group.querySelector('input[type="radio"]')?.name;
            const done = name && group.querySelector(`input[name="${name}"]:checked`);
            group.classList.toggle('completed', !!done);
        });
    };

    radios.forEach(r => r.addEventListener('change', updateProgress));
    updateProgress();

    // Save draft / finalize
    document.getElementById('btn-save-draft')?.addEventListener('click', () => {
        window.showToast('Penilaian disimpan sebagai Draft!', 'success');
    });
    document.getElementById('btn-finalize')?.addEventListener('click', () => {
        const answered = new Set();
        radios.forEach(r => { if (r.checked) answered.add(r.name); });
        const total = document.querySelectorAll('.subkriteria-group').length;
        if (answered.size < total) {
            window.showToast(`Harap lengkapi semua penilaian terlebih dahulu (${answered.size}/${total})`, 'warning');
            return;
        }
        window.showToast('Penilaian berhasil difinalisasi!', 'success');
        setTimeout(() => window.location.href = '/guru/penilaian', 2000);
    });
}

// ─── PROGRESS BARS ──────────────────────────────────────
function initProgressBars() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const target = bar.getAttribute('data-target') || '0';
                bar.style.width = target + '%';
                observer.unobserve(bar);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.progress-bar-fill[data-target]').forEach(bar => {
        bar.style.width = '0%';
        observer.observe(bar);
    });
}

// ─── CHARTS ─────────────────────────────────────────────
function initCharts() {
    initDistribusiChart();
    initRadarChart();
    initTrendChart();
}

function initDistribusiChart() {
    const ctx = document.getElementById('distribusiChart');
    if (!ctx || !window.Chart) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['BSB (Berkembang Sangat Baik)', 'BSH (Berkembang Sesuai Harapan)', 'MB (Mulai Berkembang)'],
            datasets: [{
                data: [6, 6, 3],
                backgroundColor: ['#3b82f6','#10b981','#f59e0b'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { size: 12, family: 'Plus Jakarta Sans' } } },
            }
        }
    });
}

function initRadarChart() {
    const ctx = document.getElementById('radarChart');
    if (!ctx || !window.Chart) return;
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Akademik', 'Motorik', 'Sosial', 'Bahasa'],
            datasets: [{
                label: 'Nilai Siswa',
                data: [82, 78, 85, 75],
                backgroundColor: 'rgba(59,130,246,0.15)',
                borderColor: '#3b82f6',
                pointBackgroundColor: '#3b82f6',
                pointRadius: 5,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    min: 0, max: 100,
                    ticks: { stepSize: 20, font: { size: 10 } },
                    pointLabels: { font: { size: 12, family: 'Plus Jakarta Sans', weight: '600' } }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

function initTrendChart() {
    const ctx = document.getElementById('trendChart');
    if (!ctx || !window.Chart) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            datasets: [
                {
                    label: 'BSB',
                    data: [3, 4, 5, 6],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    borderWidth: 2,
                },
                {
                    label: 'BSH',
                    data: [7, 7, 6, 6],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    borderWidth: 2,
                },
                {
                    label: 'MB',
                    data: [5, 4, 4, 3],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 12, family: 'Plus Jakarta Sans' }, padding: 16 } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// ─── DROPDOWNS ──────────────────────────────────────────
function initDropdowns() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = btn.getAttribute('data-dropdown-toggle');
            const menu = document.getElementById(id);
            menu?.classList.toggle('hidden');
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
    });
}

// ─── DELETE CONFIRM ──────────────────────────────────────
window.confirmDelete = (name) => {
    if (confirm(`Yakin ingin menghapus "${name}"? Tindakan ini tidak bisa dibatalkan.`)) {
        window.showToast(`Data "${name}" berhasil dihapus.`, 'success');
    }
};

// ─── FILTER PENILAIAN ────────────────────────────────────
window.filterPenilaian = () => {
    const ta     = document.getElementById('filter-ta')?.value;
    const kelas  = document.getElementById('filter-kelas')?.value;
    const minggu = document.getElementById('filter-minggu')?.value;
    if (!ta || !kelas || !minggu) {
        window.showToast('Pilih semua filter terlebih dahulu.', 'warning');
        return;
    }
    document.getElementById('siswa-list-section')?.classList.remove('hidden');
    document.getElementById('siswa-list-section')?.scrollIntoView({ behavior: 'smooth' });
};

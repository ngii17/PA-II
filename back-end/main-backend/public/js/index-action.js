/**
 * PURNAMA DASHBOARD - EXTERNAL SCRIPT
 * Lokasi: public/js/dashboard/index-action.js
 */

document.addEventListener('DOMContentLoaded', function () {

    // 1. ANIMASI KARTU STATISTIK
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, i) => {
        setTimeout(() => {
            card.style.transition = 'all .5s cubic-bezier(.34,1.56,.64,1)';
            card.style.opacity    = '1';
            card.style.transform  = 'translateY(0)';
        }, 80 + i * 70);
    });

    // 2. ANIMASI ANGKA (COUNTER)
    document.querySelectorAll('.stat-number[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count || '0');
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 25));
        const iv = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(iv);
        }, 40);
    });

    // 3. LOGIKA CHART (Cek apakah data dikirim dari Blade)
    if (window.dashData) {
        const chartFont = { family: 'Plus Jakarta Sans, sans-serif', size: 11, weight: '600' };

        // --- CHART UTAMA (BAR) ---
        const ctxUtama = document.getElementById('chartUtama');
        if (ctxUtama) {
            new Chart(ctxUtama, {
                type: 'bar',
                data: {
                    labels: window.dashData.labels,
                    datasets: [
                        {
                            label: 'Hotel',
                            data: window.dashData.hotel,
                            backgroundColor: '#00197D',
                            borderRadius: 8,
                        },
                        {
                            label: 'Restoran',
                            data: window.dashData.resto,
                            backgroundColor: '#D4AF37',
                            borderRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, labels: { font: chartFont, usePointStyle: true } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: chartFont } },
                        y: { grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: chartFont } }
                    }
                }
            });
        }

        // --- CHART STATUS (DOUGHNUT) ---
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Terbayar', 'Pending', 'Selesai', 'Batal'],
                    datasets: [{
                        data: window.dashData.status,
                        backgroundColor: ['#10b981', '#F5E6BE', '#00197D', '#EF4444'],
                        borderWidth: 6,
                        borderColor: '#ffffff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: { legend: { position: 'bottom', labels: { font: chartFont, padding: 20, usePointStyle: true } } }
                }
            });
        }
    }

    // 4. ANIMASI BARIS TABEL
    document.querySelectorAll('.p-table tbody tr').forEach((row, i) => {
        setTimeout(() => {
            row.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.34,1.56,.64,1)';
            row.style.opacity    = '1';
            row.style.transform  = 'translateY(0)';
        }, 350 + i * 50);
    });
});
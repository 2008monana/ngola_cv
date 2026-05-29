// public/assets/js/charts.js
// Gráficos do dashboard Ngola CV usando Chart.js.

(function() {
    if (!window.Chart || !window.ngolaDashboardCharts) {
        return;
    }

    const charts = window.ngolaDashboardCharts;
    const palette = {
        blue: '#2c3e66',
        orange: '#e67e22',
        green: '#27ae60',
        red: '#e74c3c',
        purple: '#8e44ad',
        gray: '#95a5a6'
    };

    function getContext(id) {
        const canvas = document.getElementById(id);
        return canvas ? canvas.getContext('2d') : null;
    }

    const monthlyCtx = getContext('resumesByMonthChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: charts.resumesByMonth.labels,
                datasets: [{
                    label: 'Currículos criados',
                    data: charts.resumesByMonth.values,
                    backgroundColor: palette.orange,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    const downloadsCtx = getContext('downloadsByTemplateChart');
    if (downloadsCtx) {
        new Chart(downloadsCtx, {
            type: 'pie',
            data: {
                labels: charts.downloadsByTemplate.labels,
                datasets: [{
                    data: charts.downloadsByTemplate.values,
                    backgroundColor: [palette.blue, palette.orange, palette.green, palette.purple, palette.red, palette.gray]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const activityCtx = getContext('activityLast30DaysChart');
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: charts.activityLast30Days.labels,
                datasets: [{
                    label: 'Atividades',
                    data: charts.activityLast30Days.values,
                    borderColor: palette.blue,
                    backgroundColor: 'rgba(44, 62, 102, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
})();

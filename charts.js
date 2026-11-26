// Renders the admin dashboard charts from window.CHART_DATA (set in admin/dashboard.php).
document.addEventListener('DOMContentLoaded', () => {
  const d = window.CHART_DATA;
  if (!d || typeof Chart === 'undefined') return;
  const palette = ['#1f6f8b', '#f4a261', '#2a9d8f', '#e76f51', '#8d99ae', '#6a4c93'];

  const make = (id, type, labels, data, label) => {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type,
      data: { labels, datasets: [{ label, data, backgroundColor: palette, borderColor: '#1f6f8b', tension: .3 }] },
      options: {
        plugins: { legend: { display: type === 'doughnut' } },
        scales: type === 'doughnut' ? {} : { y: { beginAtZero: true, ticks: { precision: 0 } } },
      },
    });
  };

  make('statusChart', 'doughnut', d.status.labels, d.status.data, 'Status');
  make('categoryChart', 'bar', d.category.labels, d.category.data, 'Feedback');
  make('priorityChart', 'bar', d.priority.labels, d.priority.data, 'Feedback');
  make('trendChart', 'line', d.trend.labels, d.trend.data, 'Submissions');
});

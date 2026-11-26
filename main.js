// Client-side helpers: confirm deletes and live description counter.
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  const desc = document.getElementById('description');
  const counter = document.getElementById('desc-count');
  if (desc && counter) {
    const update = () => counter.textContent = desc.value.length + ' characters';
    desc.addEventListener('input', update);
    update();
  }

  document.querySelectorAll('form.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    });
  });
});

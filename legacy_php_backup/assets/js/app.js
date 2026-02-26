/* ── Bellavella Admin – Global JS ─────────────────────────────────────────── */

/* ── Sidebar Toggles (Handled in sidebar.php) ─────────────────────────────── */

function toggleSubmenu(submenuId, chevronId) {
  const submenu = document.getElementById(submenuId);
  const chevron = document.getElementById(chevronId);
  if (!submenu) return;
  submenu.classList.toggle('open');
  if (chevron) chevron.classList.toggle('chevron-rotate');
}

function toggleProfessionals() { toggleSubmenu('professionals-submenu', 'professionals-chevron'); }
function toggleMedia() { toggleSubmenu('media-submenu', 'media-chevron'); }
function toggleCRM() { toggleSubmenu('crm-submenu', 'crm-chevron'); }

/* ── Image Preview ───────────────────────────────────────────────────────── */
function previewImage(input, previewId = 'img-preview') {
  const preview = document.getElementById(previewId);
  if (!preview || !input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    preview.src = e.target.result;
    preview.classList.remove('hidden');
    preview.classList.add('block');
  };
  reader.readAsDataURL(input.files[0]);
}

/* ── Confirm Delete ──────────────────────────────────────────────────────── */
function confirmDelete(url, label = 'this item') {
  if (typeof Swal === 'undefined') {
    if (confirm(`Delete ${label}? This cannot be undone.`)) window.location.href = url;
    return;
  }
  Swal.fire({
    title: 'Delete?',
    text: `"${label}" will be permanently removed.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e11d48',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Yes, delete',
  }).then(r => { if (r.isConfirmed) window.location.href = url; });
}

/* ── Flash message auto-dismiss ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  const flash = document.getElementById('flash-message');
  if (flash) setTimeout(() => flash.remove(), 4000);
});

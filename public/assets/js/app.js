/* ── Bellavella Admin – Global JS ─────────────────────────────────────────── */

/* ── Sidebar ─────────────────────────────────────────────────────────────── */
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  if (!sidebar) return;
  if (sidebar.classList.contains('-translate-x-full')) {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
  } else {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 300);
  }
}

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

  // ── Global Drawer Manager ───────────────────────────────────────────────
  const drawer = document.getElementById('global-drawer');
  const backdrop = document.getElementById('global-drawer-backdrop');
  if (drawer && backdrop) {
    // Event Delegation: Listen for clicks on .view-drawer-btn anywhere in the document
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.view-drawer-btn');
      if (btn) {
        e.preventDefault();
        const data = btn.closest('tr') ? btn.closest('tr').dataset : btn.dataset;
        openDrawer(data);
      }
    });

    // Close on backdrop click
    backdrop.addEventListener('click', closeDrawer);

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !drawer.classList.contains('closed')) closeDrawer();
    });
  }
});

// ── View Sidebar Logic (AJAX) ──────────────────────────────────────────────
const sidebar = document.getElementById('view-sidebar');
const backdrop = document.getElementById('view-sidebar-backdrop');
const closeBtn = document.getElementById('close-sidebar');
const sidebarData = document.getElementById('sidebar-data');
const loader = document.getElementById('sidebar-loader');
const editBtn = document.getElementById('sidebar-edit-btn');

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.view-btn');
  if (btn) {
    e.preventDefault();
    const id = btn.dataset.id;
    const type = btn.dataset.type || window.location.pathname.split('/').filter(p => p).pop();
    openSidebar(type, id);
  }
});

function openSidebar(type, id) {
  if (!sidebar) return;
  sidebar.classList.add('active');
  backdrop?.classList.add('active');
  sidebarData.innerHTML = '';
  loader.classList.remove('hidden');
  const baseUrl = window.Laravel ? window.Laravel.baseUrl : '';
  fetch(`${baseUrl}/${type}/${id}`, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(res => res.json())
    .then(data => {
      loader.classList.add('hidden');
      renderSidebarData(type, data);
      if (editBtn) editBtn.onclick = () => window.location.href = `/${type}/${data.id}/edit`;
    })
    .catch(err => {
      loader.classList.add('hidden');
      sidebarData.innerHTML = `<p class="text-red-500 text-center py-10 font-medium">Failed to load details.</p>`;
    });
}

function renderSidebarData(type, data) {
  let html = '';

  if (type === 'users') {
    html = `
      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-3xl mb-6">
          <img src="${data.avatar}" class="w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-lg mb-4">
          <h4 class="text-lg font-bold text-gray-900">${data.name}</h4>
          <span class="px-3 py-1 bg-black text-white text-[10px] font-bold rounded-full uppercase tracking-widest mt-2">${data.status}</span>
      </div>
      <div class="grid grid-cols-1 gap-4">
          ${renderField('Email', data.email, 'mail')}
          ${renderField('Phone', data.phone, 'phone')}
          ${renderField('City', data.city, 'map-pin')}
          ${renderField('Joined', data.joined, 'calendar')}
          ${renderField('Total Bookings', data.bookings_count, 'calendar-check')}
      </div>
    `;
  } else if (type === 'services') {
    html = `
      <div class="rounded-3xl overflow-hidden h-48 mb-6 bg-gray-100 border border-gray-100">
          <img src="${data.image}" class="w-full h-full object-cover">
      </div>
      <h4 class="text-xl font-bold text-gray-900 mb-2">${data.name}</h4>
      <div class="grid grid-cols-1 gap-4 mt-6">
          ${renderField('Category', data.category, 'layers')}
          ${renderField('Price', '₹' + data.price, 'indian-rupee')}
          ${renderField('Duration', data.duration + ' mins', 'clock')}
          ${renderField('Status', data.status, 'check-circle')}
          <div class="pt-4 mt-4 border-t border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description</p>
              <p class="text-sm text-gray-600 leading-relaxed">${data.description}</p>
          </div>
      </div>
    `;
  } else if (type === 'packages') {
    html = `
      <div class="rounded-3xl overflow-hidden h-48 mb-6 bg-gray-100 border border-gray-100">
          <img src="${data.image}" class="w-full h-full object-cover">
      </div>
      <h4 class="text-xl font-bold text-gray-900 mb-2">${data.name}</h4>
      <div class="grid grid-cols-1 gap-4 mt-6">
          ${renderField('Price', '₹' + data.price, 'indian-rupee')}
          ${renderField('Status', data.status, 'check-circle')}
          <div class="pt-4 mt-4 border-t border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Included Services</p>
              <div class="flex flex-wrap gap-2">
                  ${data.services.map(s => `<span class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl border border-gray-100">${s}</span>`).join('')}
              </div>
          </div>
      </div>
    `;
  } else if (type === 'offers') {
    html = `
      <div class="rounded-3xl overflow-hidden h-48 mb-6 bg-gray-100 border border-gray-100">
          <img src="${data.image}" class="w-full h-full object-cover">
      </div>
      <h4 class="text-xl font-bold text-gray-900 mb-2">${data.title}</h4>
      <div class="grid grid-cols-1 gap-4 mt-6">
          ${renderField('Code', data.code, 'tag')}
          ${renderField('Discount', data.discount, 'percent')}
          ${renderField('Status', data.status, 'check-circle')}
          <div class="pt-4 mt-4 border-t border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description</p>
              <p class="text-sm text-gray-600 leading-relaxed">${data.description}</p>
          </div>
      </div>
    `;
  } else if (type === 'reviews') {
    html = `
      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-3xl mb-6">
          <div class="flex items-center gap-1 mb-4">
              ${Array(5).fill(0).map((_, i) => `<i data-lucide="star" class="w-5 h-5 ${i < data.rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200'}"></i>`).join('')}
          </div>
          <h4 class="text-lg font-bold text-gray-900">${data.user}</h4>
          <span class="px-3 py-1 bg-black text-white text-[10px] font-bold rounded-full uppercase tracking-widest mt-2">${data.status}</span>
      </div>
      <div class="grid grid-cols-1 gap-4">
          ${renderField('Service', data.service, 'scissors')}
          ${renderField('Type', data.type, 'type')}
          ${renderField('Points', data.points, 'zap')}
          ${renderField('Date', data.created, 'calendar')}
          <div class="pt-4 mt-4 border-t border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Review Message</p>
              <p class="text-sm text-gray-600 leading-relaxed italic">"${data.review_text}"</p>
          </div>
      </div>
    `;
  } else if (type === 'professionals') {
    html = `
      <div class="flex flex-col items-center text-center p-6 bg-gray-50 rounded-3xl mb-6">
          <img src="${data.avatar}" class="w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-lg mb-4">
          <h4 class="text-lg font-bold text-gray-900">${data.name}</h4>
          <span class="px-3 py-1 bg-black text-white text-[10px] font-bold rounded-full uppercase tracking-widest mt-2">${data.status}</span>
      </div>
      <div class="grid grid-cols-1 gap-4">
          ${renderField('Specialty', data.specialty, 'award')}
          ${renderField('Experience', data.experience, 'briefcase')}
          ${renderField('Phone', data.phone, 'phone')}
      </div>
    `;
  } else if (type === 'media') {
    html = `
      <div class="rounded-3xl overflow-hidden h-48 mb-6 bg-gray-100 border border-gray-100">
          <img src="${data.thumbnail}" class="w-full h-full object-cover">
      </div>
      <h4 class="text-xl font-bold text-gray-900 mb-2">${data.title}</h4>
      <div class="grid grid-cols-1 gap-4 mt-6">
          ${renderField('Type', data.type, 'image')}
          ${renderField('Section', data.linked_section, 'layers')}
          ${renderField('Status', data.status, 'check-circle')}
          ${renderField('Created', data.created, 'calendar')}
      </div>
    `;
  } else if (type === 'categories') {
    html = `
      <div class="rounded-3xl overflow-hidden h-48 mb-6 bg-gray-100 border border-gray-100">
          <img src="${data.image}" class="w-full h-full object-cover">
      </div>
      <h4 class="text-xl font-bold text-gray-900 mb-2">${data.name}</h4>
      <div class="grid grid-cols-1 gap-4 mt-6">
          ${renderField('Slug', '/' + data.slug, 'link')}
          ${renderField('Total Services', data.services_count, 'layers')}
          ${renderField('Status', data.status, 'check-circle')}
      </div>
    `;
  }

  sidebarData.innerHTML = html;
  if (window.lucide) lucide.createIcons();
}

function renderField(label, value, icon) {
  return `
    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-gray-50 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 flex-shrink-0">
            <i data-lucide="${icon}" class="w-5 h-5"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">${label}</p>
            <p class="text-sm font-bold text-gray-900">${value || '—'}</p>
        </div>
    </div>
  `;
}

function closeSidebar() {
  sidebar.classList.remove('active');
  backdrop?.classList.remove('active');
}

closeBtn?.addEventListener('click', closeSidebar);
backdrop?.addEventListener('click', closeSidebar);
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });


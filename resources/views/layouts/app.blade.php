<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { baseUrl: "{{ url('/') }}" };</script>
  <title>{{ $pageTitle ?? 'Bellavella Admin' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-black-text,
    .sidebar-black-text span,
    .sidebar-black-text i,
    .sidebar-black-text a span,
    .sidebar-black-text button span { color: #000000 !important; }
    .sidebar-black-text [data-lucide] { color: #000000 !important; opacity: 0.8; transition: opacity 0.2s; }
    .sidebar-black-text a:hover [data-lucide],
    .sidebar-black-text button:hover [data-lucide] { opacity: 1; }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); }

    /* ═══════════════════════════════════════════════════════════════════
       DARK MODE — Comprehensive overrides for entire admin panel
       Uses zinc-based palette: #09090b → #18181b → #1c1c22 → #27272a → #3f3f46
       ═══════════════════════════════════════════════════════════════════ */
    html.dark body { background: #09090b !important; color: #e4e4e7 !important; }
    html.dark aside, html.dark aside#sidebar { background: #111113 !important; border-color: #27272a !important; }
    html.dark .sidebar-black-text, html.dark .sidebar-black-text span, html.dark .sidebar-black-text i,
    html.dark .sidebar-black-text a span, html.dark .sidebar-black-text button span { color: #d4d4d8 !important; }
    html.dark .sidebar-black-text [data-lucide] { color: #a1a1aa !important; }
    html.dark .sidebar-black-text a:hover [data-lucide],
    html.dark .sidebar-black-text button:hover [data-lucide] { color: #e4e4e7 !important; opacity: 1; }
    html.dark .sidebar-item-hover:hover { background-color: #1c1c22 !important; color: #f4f4f5 !important; box-shadow: none !important; }
    html.dark nav a, html.dark nav button, html.dark nav span, html.dark nav i, html.dark nav [data-lucide] { color: #d4d4d8 !important; }
    html.dark nav a:hover, html.dark nav button:hover { color: #f4f4f5 !important; }
    html.dark nav a.bg-white, html.dark nav button.bg-white, html.dark .sidebar-item-hover.bg-white { background-color: #1c1c22 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.4) !important; }
    html.dark aside .bg-black { background: #e4e4e7 !important; }
    html.dark aside .text-white { color: #18181b !important; }
    html.dark aside .bg-black svg { stroke: #18181b !important; }
    html.dark aside span.text-black.font-semibold, html.dark aside .text-lg.text-black { color: #f4f4f5 !important; }
    html.dark aside .bg-red-500 { background: #ef4444 !important; }
    html.dark aside .bg-red-500 span, html.dark aside span.bg-red-500 { color: #fff !important; }
    html.dark #sidebar-overlay { background: rgba(0,0,0,0.7) !important; }
    html.dark header { color: #e4e4e7 !important; }
    html.dark header h1 { color: #f4f4f5 !important; }
    html.dark header input { background: #18181b !important; color: #e4e4e7 !important; border-color: #27272a !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }
    html.dark header input::placeholder { color: #52525b !important; }
    html.dark header button.bg-white, html.dark header .bg-white { background-color: #18181b !important; color: #a1a1aa !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; }
    html.dark header button.bg-white:hover, html.dark header .bg-white:hover { background-color: #27272a !important; color: #f4f4f5 !important; }
    html.dark #theme-toggle { background: #18181b !important; color: #facc15 !important; }
    html.dark #theme-toggle:hover { background: #27272a !important; }
    html.dark .bg-white { background-color: #111113 !important; }
    html.dark .bg-\[\#F6F6F6\] { background-color: #09090b !important; }
    html.dark .bg-\[\#FCFCFC\] { background-color: #111113 !important; }
    html.dark .bg-gray-50, html.dark .bg-gray-50\/50, html.dark .bg-gray-50\/60, html.dark .bg-gray-50\/80 { background-color: #0c0c0f !important; }
    html.dark .bg-gray-100 { background-color: #18181b !important; }
    html.dark .bg-gray-200 { background-color: #27272a !important; }
    html.dark .bg-gray-900 { background-color: #e4e4e7 !important; color: #09090b !important; }
    html.dark .text-gray-900 { color: #f4f4f5 !important; }
    html.dark .text-gray-800 { color: #e4e4e7 !important; }
    html.dark .text-gray-700 { color: #d4d4d8 !important; }
    html.dark .text-gray-600 { color: #a1a1aa !important; }
    html.dark .text-gray-500 { color: #71717a !important; }
    html.dark .text-gray-400 { color: #52525b !important; }
    html.dark .text-gray-300 { color: #3f3f46 !important; }
    html.dark .text-black { color: #d4d4d8 !important; }
    html.dark .text-white { color: #f4f4f5 !important; }
    html.dark .border-gray-50 { border-color: #18181b !important; }
    html.dark .border-gray-100 { border-color: #1c1c22 !important; }
    html.dark .border-gray-200 { border-color: #27272a !important; }
    html.dark .border-gray-300 { border-color: #3f3f46 !important; }
    html.dark .border-white { border-color: #27272a !important; }
    html.dark .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.5) !important; }
    html.dark .shadow-lg { box-shadow: 0 4px 12px rgba(0,0,0,0.4) !important; }
    html.dark .shadow-2xl { box-shadow: 0 8px 30px rgba(0,0,0,0.5) !important; }
    html.dark [class*="shadow-\[0_2px_16px"], html.dark [class*="shadow-\[0_2px_20px"] { box-shadow: 0 2px 16px rgba(0,0,0,0.3) !important; }
    html.dark .shadow-lg.shadow-black\/10 { box-shadow: 0 4px 14px rgba(0,0,0,0.5) !important; }
    html.dark .ring-1.ring-gray-200, html.dark .ring-1.ring-gray-100 { --tw-ring-color: #27272a !important; }
    html.dark .ring-white { --tw-ring-color: #27272a !important; }
    html.dark .table-row { background-color: transparent !important; }
    html.dark .table-row:hover { background: #18181b !important; }
    html.dark .table-row.selected { background: #1a2332 !important; }
    html.dark thead tr { background-color: #0c0c0f !important; }
    html.dark th { color: #52525b !important; }
    html.dark td { color: #d4d4d8; }
    html.dark .stat-card { background-color: #111113 !important; border-color: #1c1c22 !important; }
    html.dark .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3) !important; }
    html.dark .filter-tab { color: #71717a !important; }
    html.dark .filter-tab:not(.active):hover { background: #18181b !important; color: #e4e4e7 !important; }
    html.dark .filter-tab.active { background: #f4f4f5 !important; color: #09090b !important; }
    html.dark .page-btn { border-color: #27272a !important; color: #71717a !important; background: transparent !important; }
    html.dark .page-btn:not(.active):hover { background: #18181b !important; color: #e4e4e7 !important; }
    html.dark .page-btn.active { background: #f4f4f5 !important; color: #09090b !important; border-color: #f4f4f5 !important; }
    html.dark main .bg-black, html.dark .btn-primary { background-color: #f4f4f5 !important; color: #09090b !important; }
    html.dark main .bg-black:hover { background-color: #e4e4e7 !important; }
    html.dark .hover\:bg-gray-800:hover { background-color: #e4e4e7 !important; }
    html.dark .hover\:bg-gray-100:hover { background-color: #27272a !important; }
    html.dark .hover\:bg-gray-50:hover { background-color: #18181b !important; }
    html.dark .hover\:bg-gray-200:hover { background-color: #3f3f46 !important; }
    html.dark .hover\:bg-white:hover { background-color: #18181b !important; }
    html.dark a[class*="border-gray-200"][class*="text-gray-500"],
    html.dark button[class*="border-gray-200"][class*="text-gray-500"] { color: #71717a !important; }
    html.dark .hover\:bg-black:hover { background-color: #f4f4f5 !important; color: #09090b !important; }
    html.dark .hover\:border-black:hover { border-color: #f4f4f5 !important; }
    html.dark .hover\:bg-red-500:hover { background-color: #ef4444 !important; }
    html.dark .hover\:bg-red-50:hover { background-color: rgba(239,68,68,0.12) !important; }
    html.dark .hover\:bg-red-600:hover { background-color: #dc2626 !important; }
    html.dark .form-input { background: #18181b !important; color: #e4e4e7 !important; border-color: #27272a !important; }
    html.dark .form-input:focus { border-color: #3f3f46 !important; box-shadow: 0 0 0 3px rgba(255,255,255,0.05) !important; }
    html.dark .form-input::placeholder { color: #3f3f46 !important; }
    html.dark input, html.dark textarea, html.dark select { background-color: #18181b !important; color: #e4e4e7 !important; border-color: #27272a !important; }
    html.dark input::placeholder, html.dark textarea::placeholder { color: #3f3f46 !important; }
    html.dark select option { background: #18181b; color: #e4e4e7; }
    html.dark .form-label { color: #52525b !important; }
    html.dark .placeholder\:text-gray-400::placeholder { color: #3f3f46 !important; }
    html.dark input[type="checkbox"] { accent-color: #f4f4f5 !important; }
    html.dark .toggle-slider { background: #27272a !important; }
    html.dark input:checked + .toggle-slider { background: #f4f4f5 !important; }
    html.dark .toggle-slider::before, html.dark .toggle-slider:before { background: #52525b !important; }
    html.dark input:checked + .toggle-slider::before, html.dark input:checked + .toggle-slider:before { background: #09090b !important; }
    html.dark .drawer-panel { background-color: #111113 !important; }
    html.dark .modal-box { background-color: #111113 !important; }
    html.dark .modal-backdrop { background: rgba(0,0,0,0.6) !important; }
    html.dark .pro-card { background: #111113 !important; border-color: #1c1c22 !important; }
    html.dark .pro-card:hover { border-color: #3f3f46 !important; background: #18181b !important; }
    html.dark .pro-card.selected { border-color: #f4f4f5 !important; background: #18181b !important; }
    html.dark .pro-checkbox { border-color: #3f3f46 !important; }
    html.dark .pro-card.selected .pro-checkbox { background: #f4f4f5 !important; border-color: #f4f4f5 !important; }
    html.dark .pro-card.selected .pro-checkbox svg { stroke: #09090b !important; }
    html.dark .img-upload-area, html.dark .drop-zone { border-color: #27272a !important; background: transparent !important; }
    html.dark .img-upload-area:hover, html.dark .drop-zone:hover { border-color: #3f3f46 !important; background: #18181b !important; }
    html.dark .drop-zone.dragover { border-color: #f4f4f5 !important; background: #18181b !important; }
    html.dark [class*="border-dashed"] { border-color: #27272a !important; }
    html.dark .section-card { background-color: #111113 !important; border-color: #1c1c22 !important; }
    html.dark .section-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.2) !important; }
    html.dark .sticky-bar { background: rgba(17,17,19,0.95) !important; border-color: #1c1c22 !important; backdrop-filter: blur(12px) !important; }
    html.dark .bg-emerald-50 { background-color: rgba(16,185,129,0.1) !important; }
    html.dark .bg-amber-50 { background-color: rgba(245,158,11,0.1) !important; }
    html.dark .bg-red-50 { background-color: rgba(239,68,68,0.1) !important; }
    html.dark .bg-blue-50 { background-color: rgba(59,130,246,0.1) !important; }
    html.dark .bg-violet-50 { background-color: rgba(139,92,246,0.1) !important; }
    html.dark .bg-rose-50 { background-color: rgba(244,63,94,0.1) !important; }
    html.dark .bg-orange-50 { background-color: rgba(249,115,22,0.1) !important; }
    html.dark .bg-indigo-50 { background-color: rgba(99,102,241,0.1) !important; }
    html.dark .bg-pink-50, html.dark .bg-fuchsia-50 { background-color: rgba(236,72,153,0.1) !important; }
    html.dark .bg-emerald-100 { background-color: rgba(16,185,129,0.15) !important; }
    html.dark .bg-amber-100 { background-color: rgba(245,158,11,0.15) !important; }
    html.dark .badge-luxe { background: rgba(124,58,237,0.12) !important; color: #a78bfa !important; }
    html.dark .badge-prime { background: rgba(194,65,12,0.12) !important; color: #fb923c !important; }
    html.dark .badge-bridal { background: rgba(190,24,93,0.12) !important; color: #f472b6 !important; }
    html.dark .badge-grooming { background: rgba(21,128,61,0.12) !important; color: #4ade80 !important; }
    html.dark .badge-spa { background: rgba(29,78,216,0.12) !important; color: #60a5fa !important; }
    html.dark .badge-skin { background: rgba(161,98,7,0.12) !important; color: #fbbf24 !important; }
    html.dark .badge-green { background: rgba(5,150,105,0.12) !important; }
    html.dark .badge-red { background: rgba(225,29,72,0.12) !important; }
    html.dark .badge-yellow { background: rgba(202,138,4,0.12) !important; }
    html.dark .badge-blue { background: rgba(37,99,235,0.12) !important; }
    html.dark .badge-gray { background: #18181b !important; color: #71717a !important; }
    html.dark .badge-violet { background: rgba(124,58,237,0.12) !important; }
    html.dark .ring-1.ring-gray-100 { --tw-ring-color: #1c1c22 !important; }
    html.dark .ring-emerald-100 { --tw-ring-color: rgba(16,185,129,0.15) !important; }
    html.dark .ring-amber-100 { --tw-ring-color: rgba(245,158,11,0.15) !important; }
    html.dark .ring-red-100 { --tw-ring-color: rgba(239,68,68,0.15) !important; }
    html.dark .border-red-100 { border-color: rgba(239,68,68,0.15) !important; }
    html.dark .border-emerald-200 { border-color: rgba(16,185,129,0.2) !important; }
    html.dark .border-red-200 { border-color: rgba(239,68,68,0.2) !important; }
    html.dark .border-amber-300 { border-color: rgba(245,158,11,0.2) !important; }
    html.dark .btn-danger { background: rgba(225,29,72,0.1) !important; border-color: rgba(225,29,72,0.2) !important; color: #f87171 !important; }
    html.dark .btn-danger:hover { background: #e11d48 !important; color: #fff !important; }
    html.dark .btn-secondary { background: #18181b !important; color: #a1a1aa !important; border-color: #27272a !important; }
    html.dark .btn-secondary:hover { background: #27272a !important; color: #e4e4e7 !important; }
    html.dark footer { background-color: #09090b !important; border-color: #1c1c22 !important; }
    html.dark footer span { color: #3f3f46 !important; }
    html.dark footer strong { color: #71717a !important; }
    html.dark #descEditor { background: #18181b !important; color: #e4e4e7 !important; }
    html.dark #descEditor:empty:before { color: #3f3f46 !important; }
    html.dark [contenteditable="true"] { color: #e4e4e7 !important; caret-color: #e4e4e7; }
    html.dark .swal2-popup { background: #111113 !important; color: #e4e4e7 !important; }
    html.dark .swal2-title { color: #f4f4f5 !important; }
    html.dark .swal2-html-container { color: #a1a1aa !important; }
    html.dark .swal2-input, html.dark .swal2-select { background: #18181b !important; color: #e4e4e7 !important; border-color: #27272a !important; }
    html.dark .swal2-input:focus { border-color: #3f3f46 !important; box-shadow: 0 0 0 3px rgba(255,255,255,0.05) !important; }
    html.dark .swal2-validation-message { background: #18181b !important; color: #f87171 !important; }
    html.dark .swal2-confirm { background: #f4f4f5 !important; color: #09090b !important; }
    html.dark .swal2-cancel { background: #27272a !important; color: #a1a1aa !important; }
    html.dark .swal2-deny { background: #dc2626 !important; }
    html.dark img.border-gray-100, html.dark img.border { border-color: #1c1c22 !important; }
    html.dark .backdrop-blur-sm { background: rgba(0,0,0,0.5) !important; }
    html.dark ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
    html.dark ::-webkit-scrollbar-track { background: #09090b; }
    html.dark .doc-img { border-color: #27272a !important; }
    html.dark .doc-img:hover { border-color: #f4f4f5 !important; }
    html.dark .bg-gradient-to-t { --tw-gradient-from: rgba(0,0,0,0.7); }
    html.dark .backdrop-blur-md { background: rgba(17,17,19,0.85) !important; }
    html.dark .bg-white\/90 { background-color: rgba(17,17,19,0.9) !important; }
    html.dark .bg-gray-200\/90 { background-color: rgba(27,27,30,0.9) !important; }
    html.dark #bulk-bar { background: #f4f4f5 !important; color: #09090b !important; }
    html.dark #bulk-bar .text-gray-400 { color: #71717a !important; }
    html.dark #bulk-bar .bg-red-500 { background: #ef4444 !important; color: #fff !important; }
    html.dark .text-gray-600 [data-lucide], html.dark [data-lucide].text-gray-400 { color: #52525b !important; }
    html.dark .bg-\[\#FCFCFC\].border.border-gray-50 { background: #111113 !important; border-color: #1c1c22 !important; }
    html.dark .hover\:border-gray-100:hover { border-color: #27272a !important; }
    html.dark .bg-amber-50.border.border-amber-100 { background: rgba(245,158,11,0.08) !important; border-color: rgba(245,158,11,0.15) !important; }
    html.dark .bg-sky-50 { background-color: rgba(14,165,233,0.1) !important; }
    html.dark .bg-yellow-50 { background-color: rgba(234,179,8,0.1) !important; }
    .lucide-icon {
      display: inline-block;
      vertical-align: middle;
    }
    .drawer-panel { transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
    .drawer-panel.closed { transform: translateX(100%); }
    .image-header { position: relative; height: 11rem; flex-shrink: 0; overflow: hidden; }
  </style>
  @stack('styles')
</head>
<body class="antialiased selection:bg-gray-200">
<script>if(localStorage.getItem('theme')==='dark')document.documentElement.classList.add('dark');</script>

  <div class="flex min-h-screen">
    @include('layouts.sidebar')

    <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto overflow-x-hidden min-w-0">
      @include('layouts.header')

      @yield('content')
    </main>
  </div>

  @include('layouts.footer')

  @include('components.view-sidebar')

  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script>
    // Apply saved theme BEFORE render to prevent flash
    (function() {
      if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
      }
    })();

    function toggleTheme() {
      const isDark = document.documentElement.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      updateThemeIcons(isDark);
    }

    function updateThemeIcons(isDark) {
      document.querySelectorAll('.dark-icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
      document.querySelectorAll('.dark-icon-sun').forEach(el  => el.classList.toggle('hidden', !isDark));
    }

    // Global Lucide initialization
    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons({
        attrs: {
          'stroke-width': 1.5,
          'class': 'lucide-icon'
        }
      });
    });
    // Sync icons after DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      updateThemeIcons(document.documentElement.classList.contains('dark'));

      @if(session('success'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 3000 });
      @endif
      @if(session('error'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ session('error') }}", showConfirmButton: false, timer: 3000 });
      @endif
      @if(session('info'))
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: "{{ session('info') }}", showConfirmButton: false, timer: 3000 });
      @endif
    });
  </script>
  @stack('scripts')
</body>
</html>

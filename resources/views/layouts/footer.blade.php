  <!-- ── Footer ──────────────────────────────────────────────────────────── -->
  <footer class="lg:ml-72 px-8 py-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400 bg-[#F6F6F6]">
    <span>&copy; {{ date('Y') }} <strong class="text-gray-600">Bellavella</strong>. All rights reserved.</span>
    <span class="flex items-center gap-1.5">
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
      Admin Panel v{{ config('app.version', '1.0.0') }}
    </span>
  </footer>

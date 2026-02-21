      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-full transition-colors">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-semibold text-gray-900 tracking-tight"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
        </div>
        <div class="flex flex-col-reverse md:flex-row items-center gap-3 md:gap-6 w-full md:w-auto">
          <div class="relative group w-full md:w-auto">
            <i data-lucide="search"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-gray-600"></i>
            <input type="text" placeholder="Search anything..."
              class="pl-10 pr-4 py-3 bg-white rounded-full text-base focus:outline-none focus:ring-2 focus:ring-gray-100 w-full md:w-80 shadow-sm placeholder:text-gray-400 transition-all">
          </div>
         
          <div class="flex items-center gap-2 self-end md:self-auto">
            <!-- Dark / Light Mode Toggle -->
            <button id="theme-toggle" onclick="toggleTheme()"
              class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 shadow-sm transition-colors"
              title="Toggle dark mode">
              <i data-lucide="moon" class="w-5 h-5 block dark-icon-moon"></i>
              <i data-lucide="sun"  class="w-5 h-5 hidden dark-icon-sun"></i>
            </button>

            <button
              class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 shadow-sm transition-colors"><i
                data-lucide="bell" class="w-5 h-5"></i></button>
            <button
              class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 shadow-sm transition-colors"><i
                data-lucide="message-square" class="w-5 h-5"></i></button>
            <div class="w-10 h-10 rounded-full overflow-hidden border border-white shadow-sm ml-2">
              <img
                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt="Profile" class="w-full h-full object-cover">
            </div>
          </div>
        </div>
      </header>

      <script>
        // Apply saved theme on load
        (function() {
          if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
            updateThemeIcons(true);
          }
        })();

        function toggleTheme() {
          const isDark = document.documentElement.classList.toggle('dark');
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
          updateThemeIcons(isDark);
        }

        function updateThemeIcons(isDark) {
          const moonIcons = document.querySelectorAll('.dark-icon-moon');
          const sunIcons  = document.querySelectorAll('.dark-icon-sun');
          moonIcons.forEach(el => el.classList.toggle('hidden', isDark));
          sunIcons.forEach(el  => el.classList.toggle('hidden', !isDark));
        }
      </script>

      <style>
        /* ── Dark mode overrides ── */
        html.dark body                     { background: #111113; color: #e5e7eb; }
        html.dark aside                    { background: #18181b !important; border-color: #27272a !important; }
        html.dark nav a, html.dark nav button, html.dark nav span, html.dark nav i { color: #e5e7eb !important; }
        html.dark .sidebar-item-hover:hover { background-color: #27272a !important; }
        html.dark header input             { background: #27272a; color: #e5e7eb; }
        html.dark header input::placeholder { color: #71717a; }
        html.dark .bg-white               { background-color: #1c1c1e !important; }
        html.dark .bg-gray-50, html.dark .bg-gray-50\/50 { background-color: #18181b !important; }
        html.dark .text-gray-900          { color: #f4f4f5 !important; }
        html.dark .text-gray-500          { color: #a1a1aa !important; }
        html.dark .text-gray-400          { color: #71717a !important; }
        html.dark .border-gray-100, html.dark .border-gray-200 { border-color: #27272a !important; }
        html.dark .shadow-sm              { box-shadow: 0 1px 2px rgba(0,0,0,0.5) !important; }
        html.dark #theme-toggle           { background: #27272a; color: #facc15; }
      </style>


      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-full transition-colors">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">{{ $pageTitle ?? 'Dashboard' }}</h1>
        </div>
        <div class="flex flex-col-reverse md:flex-row items-center gap-3 md:gap-6 w-full md:w-auto">
          <div class="relative group w-full md:w-80" id="global-search-container">
            <i data-lucide="search"
              class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-black transition-colors"></i>
            <input type="text" id="global-search-input" placeholder="Search pages, services, banners..."
              autocomplete="off"
              class="pl-11 pr-4 py-3 bg-white border border-transparent rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-black/5 focus:border-black/10 w-full shadow-sm placeholder:text-gray-400 transition-all">

            <!-- Search Results Dropdown -->
            <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-3 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 overflow-hidden z-[100] max-h-[400px] overflow-y-auto">
              <div class="p-2 space-y-1" id="results-list">
                <!-- Results dynamic injected here -->
              </div>
            </div>
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
        // ── Global Search Logic ──────────────────────────────────────────
        const searchItems = [
            { name: 'Dashboard', url: '{{ route("dashboard") }}', icon: 'layout-grid', category: 'Pages' },
            { name: 'Packages', url: '{{ route("packages.index") }}', icon: 'shopping-bag', category: 'Inventory' },
            { name: 'Add Package', url: '{{ route("packages.create") }}', icon: 'plus-circle', category: 'Actions' },
            { name: 'Categories', url: '{{ route("categories.index") }}', icon: 'grid-3x3', category: 'Inventory' },
            { name: 'Services', url: '{{ route("services.index") }}', icon: 'store', category: 'Inventory' },
            { name: 'Add Service', url: '{{ route("services.create") }}', icon: 'plus-circle', category: 'Actions' },
            { name: 'Professionals Overview', url: '{{ route("professionals.index") }}', icon: 'users', category: 'CRM' },
            { name: 'Verification Requests', url: '{{ route("professionals.verification") }}', icon: 'badge-check', category: 'CRM' },
            { name: 'Orders', url: '{{ route("professionals.orders") }}', icon: 'shopping-cart', category: 'CRM' },
            { name: 'History', url: '{{ route("professionals.history") }}', icon: 'history', category: 'CRM' },
            { name: 'Users / Customers', url: '{{ route("users.index") }}', icon: 'user-circle', category: 'CRM' },
            { name: 'Offers & Coupons', url: '{{ route("offers.index") }}', icon: 'tag', category: 'Marketing' },
            { name: 'Assign Tasks', url: '{{ route("assign.index") }}', icon: 'clipboard-list', category: 'Workflow' },
            { name: 'Reviews', url: '{{ route("reviews.index") }}', icon: 'star', category: 'Feedback' },
            { name: 'Homepage Manager', url: '{{ route("homepage.index") }}', icon: 'layout-template', category: 'Content' },
            { name: 'Banners', url: '{{ route("media.banners.index") }}', icon: 'gallery-horizontal', category: 'Content' },
            { name: 'Videos', url: '{{ route("media.videos.index") }}', icon: 'video', category: 'Content' },
            { name: 'Media Library', url: '{{ route("media.index") }}', icon: 'library', category: 'Content' },
            { name: 'Upload Media', url: '{{ route("media.create") }}', icon: 'upload', category: 'Actions' }
        ];

        const searchInput = document.getElementById('global-search-input');
        const searchResults = document.getElementById('search-results');
        const resultsList = document.getElementById('results-list');
        let activeIndex = -1;

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            if (query.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }

            const activeMatches = searchItems.filter(item =>
                item.name.toLowerCase().includes(query) ||
                item.category.toLowerCase().includes(query)
            );

            renderResults(activeMatches);
        });

        function renderResults(matches) {
            resultsList.innerHTML = '';
            if (matches.length === 0) {
                resultsList.innerHTML = `<div class="p-4 text-center text-gray-400 text-xs font-medium uppercase tracking-widest">No matching pages found</div>`;
                searchResults.classList.remove('hidden');
                return;
            }

            let currentCategory = '';
            matches.forEach((item, index) => {
                if (item.category !== currentCategory) {
                    currentCategory = item.category;
                    const catHeader = document.createElement('div');
                    catHeader.className = 'px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-2 first:mt-0';
                    catHeader.textContent = currentCategory;
                    resultsList.appendChild(catHeader);
                }

                const row = document.createElement('a');
                row.href = item.url;
                row.className = `flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors group result-row`;
                row.innerHTML = `
                    <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center group-hover:bg-white border border-gray-100 transition-colors">
                        <i data-lucide="${item.icon}" class="w-4 h-4 text-gray-400 group-hover:text-black transition-colors"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-black transition-colors">${item.name}</span>
                `;
                resultsList.appendChild(row);
            });

            searchResults.classList.remove('hidden');
            lucide.createIcons();
            activeIndex = -1;
        }

        // Handle focus and blur
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim().length > 0) searchResults.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('global-search-container').contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            const rows = resultsList.querySelectorAll('.result-row');
            if (rows.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % rows.length;
                updateActive(rows);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + rows.length) % rows.length;
                updateActive(rows);
            } else if (e.key === 'Enter') {
                if (activeIndex > -1) {
                    e.preventDefault();
                    rows[activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });

        function updateActive(rows) {
            rows.forEach((row, i) => {
                if (i === activeIndex) {
                    row.classList.add('bg-gray-50', 'ring-1', 'ring-black/5');
                    row.scrollIntoView({ block: 'nearest' });
                } else {
                    row.classList.remove('bg-gray-50', 'ring-1', 'ring-black/5');
                }
            });
        }
      </script>

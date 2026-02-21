<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard UI · refined sidebar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #F6F6F6;
    }

    ::-webkit-scrollbar {
      width: 0px;
      background: transparent;
    }

    .submenu {
      display: none;
    }

    .submenu.open {
      display: block;
    }

    .chevron-rotate {
      transform: rotate(180deg);
    }

    /* ensure pure black text on sidebar, no compromise */
    .sidebar-black-text,
    .sidebar-black-text span,
    .sidebar-black-text i,
    .sidebar-black-text a span,
    .sidebar-black-text button span {
      color: #000000 !important;
    }

    /* but keep chevron and icons slightly subtle — they remain pure black as well */
    .sidebar-black-text [data-lucide] {
      color: #000000 !important;
      opacity: 0.8;
      transition: opacity 0.2s;
    }

    .sidebar-black-text a:hover [data-lucide],
    .sidebar-black-text button:hover [data-lucide] {
      opacity: 1;
    }

    /* hover states: crisp black background effect */
    .sidebar-item-hover:hover {
      background-color: #ffffff;
      color: #000000;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
  </style>
</head>

<body class="antialiased selection:bg-gray-200">

  <div class="flex min-h-screen">

    <!-- SIDEBAR – pure black text, crisp -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- MAIN CONTENT (unchanged, retains original elegance) -->
    <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">

      <!-- header (same) -->
      <?php $pageTitle = 'Dashboard'; include '../includes/header.php'; ?>

      <!-- Dashboard Grid (same as original, untouched) -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Left/Middle Column -->
        <div class="xl:col-span-8 flex flex-col gap-6">
          <!-- Overview Section -->
          <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Overview</h2>
              <button
                class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors">Last
                month <i data-lucide="chevron-down" class="w-4 h-4"></i></button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
              <!-- Bookings -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="calendar" class="w-5 h-5"></i><span class="text-base font-medium">Bookings</span></div>
                </div>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-medium text-gray-900 tracking-tight">1,293</span>
                    <div class="flex items-center gap-1 bg-emerald-50 text-emerald-500 px-2 py-1 rounded-lg text-sm font-medium mb-2">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i><span>12.4%</span>
                    </div>
                </div>
                <div class="text-sm text-gray-400 mt-1 pl-1">vs last month</div>
              </div>
              
              <!-- Revenue -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="banknote" class="w-5 h-5"></i><span class="text-base font-medium">Revenue</span></div>
                </div>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-medium text-gray-900 tracking-tight">₹2,56,000</span>
                    <div class="flex items-center gap-1 bg-emerald-50 text-emerald-500 px-2 py-1 rounded-lg text-sm font-medium mb-2">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i><span>18.2%</span>
                    </div>
                </div>
                <div class="text-sm text-gray-400 mt-1 pl-1">vs last month</div>
              </div>

              <!-- Average Rating -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="star" class="w-5 h-5"></i><span class="text-base font-medium">Average Rating</span></div>
                </div>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-medium text-gray-900 tracking-tight">4.8 ⭐</span>
                </div>
                 <div class="text-sm text-gray-400 mt-1 pl-1">from 156 reviews</div>
              </div>
            </div>
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">857 new customers today!</h3>
                <p class="text-base text-gray-400">Send a welcome message to all new customers.</p>
                <div class="flex items-center gap-8 mt-6">
                  <div class="text-center group cursor-pointer">
                    <div
                      class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img
                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="User" class="w-full h-full object-cover"></div><span
                      class="text-sm text-gray-500">Gladyce</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div
                      class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img
                        src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="User" class="w-full h-full object-cover"></div><span
                      class="text-sm text-gray-500">Elbert</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div
                      class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img
                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="User" class="w-full h-full object-cover"></div><span
                      class="text-sm text-gray-500">Dash</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div
                      class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img
                        src="https://images.unsplash.com/photo-1527980965255-d3b416303d12?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="User" class="w-full h-full object-cover"></div><span
                      class="text-sm text-gray-500">Joyce</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div
                      class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img
                        src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="User" class="w-full h-full object-cover"></div><span
                      class="text-sm text-gray-500">Marina</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-col items-center gap-2 self-center md:self-end mb-1">
                <a href="/bellavella/users/index.php"><button
                  class="w-14 h-14 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-900 hover:border-gray-400 transition-all bg-white"><i
                    data-lucide="arrow-right" class="w-6 h-6"></i></button></a><span
                  class="text-sm font-medium text-gray-400">View all</span></div>
            </div>
            
            <div class="h-px bg-gray-100 my-8"></div>

            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">5 new professionals today!</h3>
                <p class="text-base text-gray-400">Review their verification requests and profiles.</p>
                <div class="flex items-center gap-8 mt-6">
                  <div class="text-center group cursor-pointer">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Pro" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm text-gray-500">Anjali</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Pro" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm text-gray-500">Meera</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Pro" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm text-gray-500">Priya</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Pro" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm text-gray-500">Sunita</span>
                  </div>
                  <div class="text-center group cursor-pointer">
                    <div class="w-14 h-14 rounded-full overflow-hidden mb-2 ring-2 ring-white shadow-sm group-hover:scale-110 transition-transform">
                      <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Pro" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm text-gray-500">Kavita</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-col items-center gap-2 self-center md:self-end mb-1">
                <a href="/bellavella/professionals/index.php" class="w-14 h-14 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-900 hover:border-gray-400 transition-all bg-white">
                    <i data-lucide="arrow-right" class="w-6 h-6"></i>
                </a>
                <span class="text-sm font-medium text-gray-400">View all</span>
              </div>
            </div>
          </div>
          
         
          <!-- Product View -->
          <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)] flex-1">
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Product view</h2><button
                class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors">Last
                7 days <i data-lucide="chevron-down" class="w-4 h-4"></i></button>
            </div>
            <div class="relative h-64 flex items-end justify-between gap-2 sm:gap-4 md:gap-8 px-2 md:px-8">
              <div class="absolute bottom-4 left-4 z-10"><span class="text-4xl font-medium text-gray-300">$10.2m</span>
              </div>
              <div class="w-full bg-gray-100 rounded-t-xl h-12 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
              <div class="w-full bg-gray-100 rounded-t-xl h-24 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
              <div class="w-full bg-gray-200 rounded-t-xl h-40 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
              <div class="w-full bg-gray-100 rounded-t-xl h-20 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
              <div class="w-full relative group">
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 flex flex-col items-center animate-bounce">
                  <div class="bg-[#1A1A1A] text-white text-xs py-1 px-3 rounded-lg mb-1 whitespace-nowrap">2.2m</div>
                  <div class="w-2 h-2 rounded-full border-2 border-emerald-400 bg-white"></div>
                </div>
                <div class="w-full bg-emerald-300 rounded-t-xl h-56 transition-colors cursor-pointer"></div>
              </div>
              <div class="w-full bg-gray-100 rounded-t-xl h-36 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
              <div class="w-full bg-gray-200 rounded-t-xl h-48 hover:bg-emerald-300 transition-colors cursor-pointer">
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column (popular products, comments) – unchanged -->
        <div class="xl:col-span-4 flex flex-col gap-6">
          <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-8">Popular packages</h2>
            <!-- Popular Packages -->
            <div class="space-y-8">
              <!-- Item 1 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdd403348?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900 leading-tight">Bridal Glow <br>Package</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-base font-semibold text-gray-900">₹15,000</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 2 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1560750588-73207b1ef5b8?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900 leading-tight">Advanced Hair <br>Treatment</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-base font-semibold text-gray-900">₹4,500</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 3 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900 leading-tight">Luxury Spa <br>Manicure</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-base font-semibold text-gray-900">₹1,200</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-red-50 text-red-500 text-xs font-medium mt-1">Offline</span>
                </div>
              </div>
              <!-- Item 4 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-yellow-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900 leading-tight">Full Body <br>Polishing</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-base font-semibold text-gray-900">₹8,000</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 5 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-pink-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-base font-medium text-gray-900 leading-tight">Party Makeup <br>Glam</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-base font-semibold text-gray-900">₹3,500</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs font-medium mt-1">Active</span>
                </div>
              </div>
            </div>
            <a href="/bellavella/packages/index.php" class="block w-full mt-10 py-3 rounded-xl border border-gray-200 text-base font-medium text-gray-600 hover:bg-gray-50 transition-colors text-center">All packages</a>
          </div>

          <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-8">Reviews</h2>
            <div class="space-y-6">
              <div class="flex gap-4">
                <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50"><img
                    src="https://images.unsplash.com/photo-1527980965255-d3b416303d12?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    class="w-full h-full object-cover"></div>
                <div>
                  <div class="flex flex-wrap items-baseline gap-1"><span
                      class="text-base font-semibold text-gray-900">Joyce</span><span
                      class="text-base text-gray-400">on</span><span class="text-base font-medium text-gray-900">Advanced Hair
                      Treatment</span></div>
                  <div class="text-xs text-gray-400 mb-2">09:00 AM</div>
                  <p class="text-base text-gray-500 leading-relaxed">Amazing service! My hair feels so soft and shiny now.
                    <span class="text-yellow-500">✨</span></p>
                </div>
              </div>
              <div class="flex gap-4 opacity-60">
                <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50"><img
                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    class="w-full h-full object-cover"></div>
                <div>
                  <div class="flex flex-wrap items-baseline gap-1"><span
                      class="text-base font-semibold text-gray-900">Gladyce</span><span
                      class="text-base text-gray-400">on</span><span class="text-base font-medium text-gray-900">Bridal Glow
                      Package</span></div>
                  <div class="text-xs text-gray-400 mb-2">2h ago</div>
                  <div class="h-2 w-24 bg-gray-100 rounded"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="/bellavella/assets/js/app.js"></script>
  <script>
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

    function toggleProfessionals() {
      const submenu = document.getElementById('professionals-submenu');
      const chevron = document.getElementById('professionals-chevron');
      submenu.classList.toggle('open');
      chevron.classList.toggle('chevron-rotate');
    }

    function toggleOrders() { /* kept for compatibility */ }
  </script>

</body>

</html>
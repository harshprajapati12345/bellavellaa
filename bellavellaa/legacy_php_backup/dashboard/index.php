<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard UI · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto overflow-x-hidden min-w-0">

      <!-- header -->
      <?php $pageTitle = 'Dashboard'; include '../includes/header.php'; ?>

      <!-- 1️⃣ Top Summary Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3"><i data-lucide="calendar" class="w-5 h-5 text-violet-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">8</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Bookings Today</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3"><i data-lucide="banknote" class="w-5 h-5 text-emerald-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">₹18,500</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Today's Revenue</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center mb-3"><i data-lucide="user-check" class="w-5 h-5 text-sky-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">5</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Active Professionals</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center mb-3"><i data-lucide="users" class="w-5 h-5 text-pink-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">124</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Total Customers</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3"><i data-lucide="star" class="w-5 h-5 text-amber-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">3</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">New Reviews</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3"><i data-lucide="package" class="w-5 h-5 text-rose-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">42</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Total Services</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all border border-amber-50">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3"><i data-lucide="clock" class="w-5 h-5 text-amber-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">2</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Pending Leaves</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all border border-blue-50">
          <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3"><i data-lucide="calendar-off" class="w-5 h-5 text-blue-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">2</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">On Leave Today</p>
        </div>
      </div>

      <!-- Row 2: Schedule + Revenue -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
        <!-- 2️⃣ Today's Schedule Preview -->
        <div class="xl:col-span-7 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Today's Appointments</h2>
            <span class="text-xs font-medium bg-gray-900 text-white px-3 py-1 rounded-full">Feb 23</span>
          </div>
          <div class="space-y-4">
            <!-- 9:00 AM -->
            <div class="flex items-start gap-6 p-4 rounded-xl bg-gray-50/50 hover:bg-gray-100/50 transition-all cursor-pointer group border border-transparent hover:border-gray-200/50">
              <div class="w-14 text-right shrink-0">
                <span class="text-sm font-bold text-gray-900">9:00</span>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">AM</p>
              </div>
              <div class="flex-1 border-l-2 border-emerald-500 pl-6 py-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-bold text-gray-900">Bridal Makeup</h4>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Confirmed</span>
                </div>
                <div class="flex items-center gap-2">
                  <p class="text-xs text-gray-500 font-medium">Anjali Kapoor</p>
                  <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                  <p class="text-xs text-gray-400">2h duration</p>
                </div>
              </div>
            </div>

            <!-- 10:00 AM -->
            <div class="flex items-start gap-6 p-4 rounded-xl bg-gray-50/50 hover:bg-gray-100/50 transition-all cursor-pointer group border border-transparent hover:border-gray-200/50">
              <div class="w-14 text-right shrink-0">
                <span class="text-sm font-bold text-gray-900">10:00</span>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">AM</p>
              </div>
              <div class="flex-1 border-l-2 border-emerald-500 pl-6 py-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-bold text-gray-900">Facial</h4>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Confirmed</span>
                </div>
                <div class="flex items-center gap-2">
                  <p class="text-xs text-gray-500 font-medium">Meera Joshi</p>
                  <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                  <p class="text-xs text-gray-400">45m duration</p>
                </div>
              </div>
            </div>

            <!-- 11:00 AM -->
            <div class="flex items-start gap-6 p-4 rounded-xl bg-gray-50/50 hover:bg-gray-100/50 transition-all cursor-pointer group border border-transparent hover:border-gray-200/50">
              <div class="w-14 text-right shrink-0">
                <span class="text-sm font-bold text-gray-900">11:00</span>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">AM</p>
              </div>
              <div class="flex-1 border-l-2 border-amber-500 pl-6 py-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-bold text-gray-900">Nail Art</h4>
                  <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wider">Pending</span>
                </div>
                <div class="flex items-center gap-2">
                  <p class="text-xs text-gray-500 font-medium">Sunita Verma</p>
                  <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                  <p class="text-xs text-gray-400">1h duration</p>
                </div>
              </div>
            </div>

            <!-- 12:00 PM -->
            <div class="flex items-start gap-6 p-4 rounded-xl bg-gray-50/50 hover:bg-gray-100/50 transition-all cursor-pointer group border border-transparent hover:border-gray-200/50">
              <div class="w-14 text-right shrink-0">
                <span class="text-sm font-bold text-gray-900">12:00</span>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">PM</p>
              </div>
              <div class="flex-1 border-l-2 border-emerald-500 pl-6 py-1">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-bold text-gray-900">Party Makeup</h4>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Confirmed</span>
                </div>
                <div class="flex items-center gap-2">
                  <p class="text-xs text-gray-500 font-medium">Priya Sharma</p>
                  <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                  <p class="text-xs text-gray-400">1.5h duration</p>
                </div>
              </div>
            </div>
          </div>
          <a href="/bella/assign/index.php" class="flex items-center justify-center gap-2 w-full mt-6 py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"><i data-lucide="calendar" class="w-4 h-4"></i> View Full Schedule</a>
        </div>

        <!-- 3️⃣ Revenue Chart -->
        <div class="xl:col-span-5 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Revenue</h2>
            <div class="flex items-center bg-gray-50 rounded-full p-0.5">
              <button data-view="daily" class="px-3 py-1 rounded-full text-xs font-medium bg-gray-900 text-white">Daily</button>
              <button data-view="weekly" class="px-3 py-1 rounded-full text-xs font-medium text-gray-400 hover:text-gray-600">Weekly</button>
              <button data-view="monthly" class="px-3 py-1 rounded-full text-xs font-medium text-gray-400 hover:text-gray-600">Monthly</button>
            </div>
          </div>
          <div class="flex items-end gap-1 mt-2 mb-4"><span id="revenue-amount" class="text-3xl font-bold text-gray-900">₹1,24,500</span><span id="revenue-growth" class="text-sm text-emerald-500 font-medium ml-2">+18.2%</span></div>
          <p id="revenue-period" class="text-xs text-gray-400 mb-6">Last 7 days</p>
          <div class="h-44 w-full relative">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Row 3: Bookings Table + Quick Actions -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
        <!-- 4️⃣ Recent Bookings -->
        <div class="xl:col-span-8 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Recent Bookings</h2>
            <a href="/bella/assign/index.php" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors">View all →</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead><tr class="border-b border-gray-100">
                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Service</th>
                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Professional</th>
                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
              </tr></thead>
              <tbody class="text-sm">
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td class="py-3.5"><div class="flex items-center gap-3"><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-8 h-8 rounded-full object-cover"><span class="font-medium text-gray-900">Priya Sharma</span></div></td>
                  <td class="py-3.5 text-gray-600">Hair Color</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">Anjali</td>
                  <td class="py-3.5 text-gray-600">9:00 AM</td>
                  <td class="py-3.5"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Confirmed</span></td>
                </tr>
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td class="py-3.5"><div class="flex items-center gap-3"><img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-8 h-8 rounded-full object-cover"><span class="font-medium text-gray-900">Meera Joshi</span></div></td>
                  <td class="py-3.5 text-gray-600">Facial</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">Sunita</td>
                  <td class="py-3.5 text-gray-600">10:00 AM</td>
                  <td class="py-3.5"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Confirmed</span></td>
                </tr>
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td class="py-3.5"><div class="flex items-center gap-3"><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-8 h-8 rounded-full object-cover"><span class="font-medium text-gray-900">Kavita Patel</span></div></td>
                  <td class="py-3.5 text-gray-600">Spa Package</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">Priya</td>
                  <td class="py-3.5 text-gray-600">10:30 AM</td>
                  <td class="py-3.5"><span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-semibold">Pending</span></td>
                </tr>
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td class="py-3.5"><div class="flex items-center gap-3"><img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-8 h-8 rounded-full object-cover"><span class="font-medium text-gray-900">Ananya Kapoor</span></div></td>
                  <td class="py-3.5 text-gray-600">Bridal Makeup</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">Anjali</td>
                  <td class="py-3.5 text-gray-600">11:00 AM</td>
                  <td class="py-3.5"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Confirmed</span></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="py-3.5"><div class="flex items-center gap-3"><img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-8 h-8 rounded-full object-cover"><span class="font-medium text-gray-900">Rahul Verma</span></div></td>
                  <td class="py-3.5 text-gray-600">Hair Treatment</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">Meera</td>
                  <td class="py-3.5 text-gray-600">2:00 PM</td>
                  <td class="py-3.5"><span class="px-2.5 py-1 rounded-full bg-red-50 text-red-500 text-xs font-semibold">Cancelled</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 6️⃣ Quick Actions -->
        <div class="xl:col-span-4 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6">Quick Actions</h2>
          <div class="grid grid-cols-2 gap-3">
            <a href="/bella/services/create.php" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-violet-50 hover:bg-violet-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition-colors"><i data-lucide="plus-circle" class="w-5 h-5 text-violet-600"></i></div>
              <span class="text-xs font-semibold text-violet-700">Add Service</span>
            </a>
            <a href="/bella/professionals/create.php" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-sky-50 hover:bg-sky-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center transition-colors"><i data-lucide="user-plus" class="w-5 h-5 text-sky-600"></i></div>
              <span class="text-xs font-semibold text-sky-700">Add Professional</span>
            </a>
            <a href="/bella/packages/create.php" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors"><i data-lucide="package" class="w-5 h-5 text-amber-600"></i></div>
              <span class="text-xs font-semibold text-amber-700">Create Package</span>
            </a>
            <a href="/bella/media/banners.php" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-pink-50 hover:bg-pink-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center transition-colors"><i data-lucide="image" class="w-5 h-5 text-pink-600"></i></div>
              <span class="text-xs font-semibold text-pink-700">Upload Media</span>
            </a>
            <a href="/bella/assign/index.php" class="col-span-2 flex items-center justify-center gap-2 p-4 rounded-2xl bg-gray-900 hover:bg-gray-800 transition-colors cursor-pointer group">
              <i data-lucide="calendar-plus" class="w-5 h-5 text-white"></i>
              <span class="text-sm font-semibold text-white">Assign Professional</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Row 4: Reviews + Performance Insights -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
        <!-- 5️⃣ Recent Reviews -->
        <div class="xl:col-span-7 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Recent Reviews</h2>
            <a href="/bella/reviews/index.php" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors">Manage Reviews →</a>
          </div>
          <div class="space-y-5">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50">
              <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between"><h4 class="text-sm font-semibold text-gray-900">Ananya Kapoor</h4><span class="text-[10px] text-gray-400">3d ago</span></div>
                <span class="text-amber-400 text-xs">�...�...�...�...�...</span>
                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">"Absolutely loved the bridal package! My skin was glowing on my big day."</p>
              </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50">
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between"><h4 class="text-sm font-semibold text-gray-900">Priya Sharma</h4><span class="text-[10px] text-gray-400">4d ago</span></div>
                <span class="text-amber-400 text-xs">�...�...�...�...<span class="text-gray-200">�...</span></span>
                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">"Great service, my hair feels so much healthier now. Will definitely come back."</p>
              </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50">
              <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between"><h4 class="text-sm font-semibold text-gray-900">Meera Joshi</h4><span class="text-[10px] text-gray-400">1w ago</span></div>
                <span class="text-amber-400 text-xs">�...�...�...�...�...</span>
                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">"Best hair treatment I have ever had. My hair is silky smooth and manageable now."</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 7️⃣ Performance Insights -->
        <div class="xl:col-span-5 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6">Performance Insights</h2>
          <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
              <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center shrink-0"><span class="text-lg">🔥</span></div>
              <div class="flex-1 min-w-0"><p class="text-xs text-gray-400">Most Booked Service</p><p class="text-sm font-semibold text-gray-900">Bridal Makeup</p></div>
              <span class="text-sm font-bold text-gray-900">342</span>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
              <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center shrink-0"><span class="text-lg">👑</span></div>
              <div class="flex-1 min-w-0"><p class="text-xs text-gray-400">Top Professional</p><p class="text-sm font-semibold text-gray-900">Anjali Kapoor</p></div>
              <span class="text-xs font-semibold text-emerald-500">4.9 �...</span>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
              <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0"><span class="text-lg">💎</span></div>
              <div class="flex-1 min-w-0"><p class="text-xs text-gray-400">Most Popular Package</p><p class="text-sm font-semibold text-gray-900">Bridal Glow Package</p></div>
              <span class="text-sm font-bold text-gray-900">₹15K</span>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
              <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0"><span class="text-lg">📊</span></div>
              <div class="flex-1 min-w-0"><p class="text-xs text-gray-400">Booking Growth</p><p class="text-sm font-semibold text-gray-900">vs Last Month</p></div>
              <span class="flex items-center gap-1 text-sm font-bold text-emerald-500"><i data-lucide="trending-up" class="w-4 h-4"></i>+12.4%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 5: Audience Insights -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6 pb-12">
        <div class="xl:col-span-12 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-8">
            <div>
              <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Audience Insights</h2>
              <p class="text-xs text-gray-400 mt-1">Real-time engagement of customers and professionals</p>
            </div>
            <div class="flex items-center gap-2">
              <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wider">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Status
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Professionals Card -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100/50 relative hover:shadow-md transition-all duration-500">
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-violet-50 flex items-center justify-center">
                    <i data-lucide="users-2" class="w-5 h-5 text-violet-600"></i>
                  </div>
                  Active Professionals
                </h3>
                <span class="text-[10px] font-bold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-lg uppercase tracking-wider font-mono">32 Online</span>
              </div>
              
              <div class="space-y-1">
                <!-- Pro Header -->
                <div class="grid grid-cols-12 px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50/50">
                  <div class="col-span-5">Professional</div>
                  <div class="col-span-4 text-center">Expertise</div>
                  <div class="col-span-3 text-right">Status</div>
                </div>

                <!-- Pro 1 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-violet-600 transition-colors">Anjali Kapoor</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Bridal Expert</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-full uppercase tracking-widest ring-1 ring-emerald-200/50 shadow-sm">In Service</span>
                  </div>
                </div>

                <!-- Pro 2 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-violet-600 transition-colors">Meera Joshi</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Skin Specialist</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <span class="text-[9px] font-bold text-sky-700 bg-sky-100/80 px-2.5 py-1 rounded-full uppercase tracking-widest ring-1 ring-sky-200/50 shadow-sm">Available</span>
                  </div>
                </div>

                <!-- Pro 3 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-violet-600 transition-colors">Sunita Verma</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Nail Artist</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-full uppercase tracking-widest ring-1 ring-emerald-200/50 shadow-sm">In Service</span>
                  </div>
                </div>
              </div>
              
              <a href="/bella/professionals/index.php" class="mt-8 flex items-center justify-center gap-2 text-[10px] font-bold text-gray-400 hover:text-violet-600 uppercase tracking-widest transition-all p-4 bg-gray-50/50 rounded-2xl hover:bg-violet-50">
                View all professionals <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </a>
            </div>

            <!-- Active Customers Card -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100/50 relative hover:shadow-md transition-all duration-500">
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-pink-50 flex items-center justify-center">
                    <i data-lucide="user-heart" class="w-5 h-5 text-pink-600"></i>
                  </div>
                  Active Customers
                </h3>
                <span class="text-[10px] font-bold text-pink-600 bg-pink-50 px-2.5 py-1 rounded-lg uppercase tracking-wider font-mono">12 Ongoing</span>
              </div>
              
              <div class="space-y-1">
                <!-- Customer Header -->
                <div class="grid grid-cols-12 px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50/50">
                  <div class="col-span-5">Customer</div>
                  <div class="col-span-4 text-center">Service</div>
                  <div class="col-span-3 text-right">Assigned To</div>
                </div>

                <!-- Customer 1 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Priya Sharma</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Hair Color</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <div class="flex flex-col items-end">
                      <p class="text-[11px] font-extrabold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md border border-violet-100/50">Anjali</p>
                      <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Verified Pro</span>
                    </div>
                  </div>
                </div>

                <!-- Customer 2 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Meera Joshi</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Facial</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <div class="flex flex-col items-end">
                      <p class="text-[11px] font-extrabold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md border border-violet-100/50">Sunita</p>
                      <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Verified Pro</span>
                    </div>
                  </div>
                </div>

                <!-- Customer 3 -->
                <div class="grid grid-cols-12 items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                  <div class="col-span-5 flex items-center gap-3">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=64&h=64&fit=crop" class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Kavita Patel</p>
                  </div>
                  <div class="col-span-4 text-center">
                    <p class="text-[11px] text-gray-500 font-medium bg-gray-100/50 px-2 py-0.5 rounded-full inline-block">Spa Package</p>
                  </div>
                  <div class="col-span-3 text-right">
                    <div class="flex flex-col items-end">
                      <p class="text-[11px] font-extrabold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-md border border-violet-100/50">Priya</p>
                      <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Verified Pro</span>
                    </div>
                  </div>
                </div>
              </div>

              <a href="/bella/assign/index.php" class="mt-8 flex items-center justify-center gap-2 text-[10px] font-bold text-gray-400 hover:text-pink-600 uppercase tracking-widest transition-all p-4 bg-gray-50/50 rounded-2xl hover:bg-pink-50">
                Manage all bookings <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>
  <script src="/bella/assets/js/app.js"></script>
  <script>
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

    // Sidebar toggles are handled by sidebar.php

    function toggleOrders() { /* kept for compatibility */ }

    // --- REVENUE CHART LOGIC ---
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Gradient definitions
    const purpleGradient = ctx.createLinearGradient(0, 0, 0, 160);
    purpleGradient.addColorStop(0, 'rgba(124, 58, 237, 0.85)');
    purpleGradient.addColorStop(1, 'rgba(124, 58, 237, 0.1)');

    const emeraldGradient = ctx.createLinearGradient(0, 0, 0, 160);
    emeraldGradient.addColorStop(0, 'rgba(16, 185, 129, 0.85)');
    emeraldGradient.addColorStop(1, 'rgba(16, 185, 129, 0.1)');

    const chartData = {
      daily: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [12000, 18500, 24000, 28000, 15000, 22000, 10000],
        amount: '₹1,24,500',
        growth: '+18.2%',
        period: 'Last 7 days'
      },
      weekly: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        data: [350000, 420000, 310000, 480000],
        amount: '₹15,60,000',
        growth: '+12.5%',
        period: 'Last 4 weeks'
      },
      monthly: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        data: [1200000, 1500000, 1100000, 1800000, 2100000, 1950000],
        amount: '₹96,50,000',
        growth: '+24.8%',
        period: 'Last 6 months'
      }
    };

    let revenueChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: chartData.daily.labels,
        datasets: [{
          data: chartData.daily.data,
          backgroundColor: emeraldGradient,
          borderRadius: 8,
          borderSkipped: false,
          hoverBackgroundColor: '#059669',
          barThickness: 24
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a1a1a',
            padding: 12,
            titleFont: { size: 13, weight: '600' },
            bodyFont: { size: 12 },
            cornerRadius: 12,
            displayColors: false,
            callbacks: {
              label: function(context) {
                return 'Revenue: ₹' + context.raw.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: { display: false, beginAtZero: true },
          x: { 
            grid: { display: false, drawBorder: false },
            ticks: { color: '#9ca3af', font: { size: 10, weight: '500' } } 
          }
        },
        animation: {
          duration: 1000,
          easing: 'easeInOutQuart'
        }
      }
    });

    // Toggle Buttons Logic
    const buttons = document.querySelectorAll('[data-view]');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const view = btn.getAttribute('data-view');
        
        // Update button styles
        buttons.forEach(b => b.classList.remove('bg-gray-900', 'text-white'));
        buttons.forEach(b => b.classList.add('text-gray-400', 'hover:text-gray-600'));
        btn.classList.remove('text-gray-400', 'hover:text-gray-600');
        btn.classList.add('bg-gray-900', 'text-white');

        // Update Text
        document.getElementById('revenue-amount').innerText = chartData[view].amount;
        document.getElementById('revenue-growth').innerText = chartData[view].growth;
        document.getElementById('revenue-period').innerText = chartData[view].period;

        // Update Chart
        revenueChart.data.labels = chartData[view].labels;
        revenueChart.data.datasets[0].data = chartData[view].data;
        revenueChart.update();
      });
    });
  </script>

</body>

</html>

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
    <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto overflow-x-hidden min-w-0">

      <!-- header (same) -->
      <?php $pageTitle = 'Dashboard'; include '../includes/header.php'; ?>

      <!-- Dashboard Grid (same as original, untouched) -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Left/Middle Column -->
        <div class="xl:col-span-8 flex flex-col gap-6">
          <!-- Overview Section -->
          <div class="bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Overview</h2>
              <button
                class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors">Last
                month <i data-lucide="chevron-down" class="w-4 h-4"></i></button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-10">
              <!-- Bookings -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-3 sm:mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="calendar" class="w-5 h-5"></i><span class="text-sm sm:text-base font-medium">Bookings</span></div>
                </div>
                <div class="flex flex-wrap items-end gap-2 sm:gap-3">
                    <span class="text-2xl sm:text-4xl lg:text-5xl font-medium text-gray-900 tracking-tight">1,293</span>
                    <div class="flex items-center gap-1 bg-emerald-50 text-emerald-500 px-2 py-1 rounded-lg text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                        <i data-lucide="arrow-up-right" class="w-3 h-3 sm:w-4 sm:h-4"></i><span>12.4%</span>
                    </div>
                </div>
                <div class="text-xs sm:text-sm text-gray-400 mt-1 pl-1">vs last month</div>
              </div>
              
              <!-- Revenue -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-3 sm:mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="banknote" class="w-5 h-5"></i><span class="text-sm sm:text-base font-medium">Revenue</span></div>
                </div>
                <div class="flex flex-wrap items-end gap-2 sm:gap-3">
                    <span class="text-2xl sm:text-4xl lg:text-5xl font-medium text-gray-900 tracking-tight">₹2,56,000</span>
                    <div class="flex items-center gap-1 bg-emerald-50 text-emerald-500 px-2 py-1 rounded-lg text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                        <i data-lucide="arrow-up-right" class="w-3 h-3 sm:w-4 sm:h-4"></i><span>18.2%</span>
                    </div>
                </div>
                <div class="text-xs sm:text-sm text-gray-400 mt-1 pl-1">vs last month</div>
              </div>

              <!-- Average Rating -->
              <div class="bg-[#FCFCFC] border border-gray-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 relative group hover:border-gray-100 transition-colors">
                <div class="flex items-start justify-between mb-3 sm:mb-4">
                  <div class="flex items-center gap-2 text-gray-500"><i data-lucide="star" class="w-5 h-5"></i><span class="text-sm sm:text-base font-medium">Average Rating</span></div>
                </div>
                <div class="flex flex-wrap items-end gap-2 sm:gap-3">
                    <span class="text-2xl sm:text-4xl lg:text-5xl font-medium text-gray-900 tracking-tight">4.8 ⭐</span>
                </div>
                 <div class="text-xs sm:text-sm text-gray-400 mt-1 pl-1">from 156 reviews</div>
              </div>
            </div>
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">857 new customers today!</h3>
                <p class="text-base text-gray-400">Send a welcome message to all new customers.</p>
                <div class="flex items-center flex-wrap gap-4 sm:gap-8 mt-6">
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
                <div class="flex items-center flex-wrap gap-4 sm:gap-8 mt-6">
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
          
         
          <!-- Today's Schedule -->
          <style>
            .schedule-card { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
            .schedule-card:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
            .date-pill { transition: all 0.2s ease; }
            .date-pill:hover:not(.date-active) { background: #f5f0eb; }
            .date-active { background: #1a1a1a; color: #fff; }
            .date-active .date-day { color: rgba(255,255,255,0.5); }
            .date-active .date-num { color: #fff; }
            .filter-pill { transition: all 0.2s ease; cursor: pointer; }
            .filter-pill:hover { background: #1a1a1a; color: #fff; }
            .filter-pill.active-filter { background: #1a1a1a; color: #fff; }
            .time-row { position: relative; }
            .time-row::before { content: ''; position: absolute; top: 0; left: 52px; right: 0; height: 1px; background: linear-gradient(90deg, #f0ece8, transparent); }
          </style>
          <div class="bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)] flex-1">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 sm:mb-8">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Today's Schedule</h2>
                <p class="text-sm text-gray-400 mt-0.5">February 23, 2026 · Monday</p>
              </div>
              <div class="flex items-center gap-2">
                <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition-colors">
                  <i data-lucide="chevron-left" class="w-4 h-4 text-gray-500"></i>
                </button>
                <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition-colors">
                  <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500"></i>
                </button>
              </div>
            </div>

            <!-- Date Selector Strip -->
            <div class="flex items-center gap-2 sm:gap-3 mb-6 sm:mb-8 overflow-x-auto pb-1">
              <div class="date-pill flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px]">
                <span class="date-day text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wider">Mon</span>
                <span class="date-num text-base sm:text-lg font-bold text-gray-700 mt-0.5">22</span>
              </div>
              <div class="date-pill date-active flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px] shadow-lg shadow-gray-900/10">
                <span class="date-day text-[10px] sm:text-xs font-medium uppercase tracking-wider">Tue</span>
                <span class="date-num text-base sm:text-lg font-bold mt-0.5">23</span>
              </div>
              <div class="date-pill flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px]">
                <span class="date-day text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wider">Wed</span>
                <span class="date-num text-base sm:text-lg font-bold text-gray-700 mt-0.5">24</span>
              </div>
              <div class="date-pill flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px]">
                <span class="date-day text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wider">Thu</span>
                <span class="date-num text-base sm:text-lg font-bold text-gray-700 mt-0.5">25</span>
              </div>
              <div class="date-pill flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px]">
                <span class="date-day text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wider">Fri</span>
                <span class="date-num text-base sm:text-lg font-bold text-gray-700 mt-0.5">26</span>
              </div>
              <div class="date-pill flex flex-col items-center px-3 sm:px-4 py-2 sm:py-3 rounded-2xl cursor-pointer min-w-[52px]">
                <span class="date-day text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-wider">Sat</span>
                <span class="date-num text-base sm:text-lg font-bold text-gray-700 mt-0.5">27</span>
              </div>
            </div>

            <!-- Vertical Time Slots -->
            <div class="space-y-0">

              <!-- 9:00 AM -->
              <div class="time-row flex gap-3 sm:gap-5 py-3 sm:py-4">
                <div class="w-12 sm:w-14 shrink-0 pt-1">
                  <span class="text-xs sm:text-sm font-semibold text-gray-300">9:00</span>
                  <span class="block text-[10px] text-gray-300 -mt-0.5">AM</span>
                </div>
                <div class="flex-1 flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <!-- Bridal Makeup -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-[#1a1a1a] to-[#2d2d2d] rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-white leading-tight">Bridal Makeup</p>
                        <p class="text-xs text-gray-400 mt-1">Anjali Kapoor</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-white/10 text-white/70 px-2 py-0.5 rounded-full">2h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-[#1a1a1a] object-cover">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-[#1a1a1a] object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Confirmed</span>
                    </div>
                  </div>
                  <!-- Hair Color -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-violet-800 leading-tight">Hair Color</p>
                        <p class="text-xs text-violet-400 mt-1">Priya Sharma</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-violet-100 text-violet-500 px-2 py-0.5 rounded-full">1h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-violet-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-500"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Confirmed</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 10:00 AM -->
              <div class="time-row flex gap-3 sm:gap-5 py-3 sm:py-4">
                <div class="w-12 sm:w-14 shrink-0 pt-1">
                  <span class="text-xs sm:text-sm font-semibold text-gray-300">10:00</span>
                  <span class="block text-[10px] text-gray-300 -mt-0.5">AM</span>
                </div>
                <div class="flex-1 flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <!-- Facial -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-emerald-800 leading-tight">Facial</p>
                        <p class="text-xs text-emerald-400 mt-1">Meera Joshi</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-emerald-100 text-emerald-500 px-2 py-0.5 rounded-full">45m</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-emerald-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-500"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Confirmed</span>
                    </div>
                  </div>
                  <!-- Spa Package -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-amber-800 leading-tight">Spa Package</p>
                        <p class="text-xs text-amber-400 mt-1">Kavita Patel</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-amber-100 text-amber-500 px-2 py-0.5 rounded-full">2h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-amber-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-500"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Pending</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 11:00 AM -->
              <div class="time-row flex gap-3 sm:gap-5 py-3 sm:py-4">
                <div class="w-12 sm:w-14 shrink-0 pt-1">
                  <span class="text-xs sm:text-sm font-semibold text-gray-300">11:00</span>
                  <span class="block text-[10px] text-gray-300 -mt-0.5">AM</span>
                </div>
                <div class="flex-1 flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <!-- Nail Art -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-pink-50 to-rose-50 border border-pink-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-pink-800 leading-tight">Nail Art</p>
                        <p class="text-xs text-pink-400 mt-1">Sunita Verma</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-pink-100 text-pink-500 px-2 py-0.5 rounded-full">1h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-pink-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-500"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Confirmed</span>
                    </div>
                  </div>
                  <!-- Hair Treatment -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-sky-800 leading-tight">Hair Treatment</p>
                        <p class="text-xs text-sky-400 mt-1">Anjali Kapoor</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-sky-100 text-sky-500 px-2 py-0.5 rounded-full">1h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-sky-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-500"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Confirmed</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 12:00 PM -->
              <div class="time-row flex gap-3 sm:gap-5 py-3 sm:py-4">
                <div class="w-12 sm:w-14 shrink-0 pt-1">
                  <span class="text-xs sm:text-sm font-semibold text-gray-300">12:00</span>
                  <span class="block text-[10px] text-gray-300 -mt-0.5">PM</span>
                </div>
                <div class="flex-1 flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <!-- Party Makeup -->
                  <div class="schedule-card flex-1 bg-gradient-to-br from-rose-50 to-fuchsia-50 border border-rose-100/60 rounded-2xl p-3 sm:p-4 cursor-pointer">
                    <div class="flex items-start justify-between">
                      <div>
                        <p class="text-sm sm:text-base font-semibold text-rose-800 leading-tight">Party Makeup</p>
                        <p class="text-xs text-rose-400 mt-1">Priya Sharma</p>
                      </div>
                      <span class="text-[10px] sm:text-xs font-medium bg-rose-100 text-rose-500 px-2 py-0.5 rounded-full">1.5h</span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                      <div class="flex -space-x-2">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-7 h-7 rounded-full ring-2 ring-rose-50 object-cover">
                      </div>
                      <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-500"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Pending</span>
                    </div>
                  </div>
                  <div class="flex-1"></div>
                </div>
              </div>

            </div>

            <!-- Category Filter Tabs + Summary -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-gray-100/80">
              <button class="filter-pill active-filter px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-semibold tracking-wide">Bridal</button>
              <button class="filter-pill px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-semibold tracking-wide bg-gray-50 text-gray-500">Facial</button>
              <button class="filter-pill px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-semibold tracking-wide bg-gray-50 text-gray-500">Spa</button>
              <button class="filter-pill px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-semibold tracking-wide bg-gray-50 text-gray-500">Nails</button>
              <button class="filter-pill px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-semibold tracking-wide bg-gray-50 text-gray-500">Hair</button>
              <div class="ml-auto flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-gray-900 text-white text-xs font-bold">8</span>
                <span class="text-xs sm:text-sm font-medium text-gray-500">bookings today</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column (popular products, comments) – unchanged -->
        <div class="xl:col-span-4 flex flex-col gap-6">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6 sm:mb-8">Popular packages</h2>
            <!-- Popular Packages -->
            <div class="space-y-6 sm:space-y-8">
              <!-- Item 1 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdd403348?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Bridal Glow <br>Package</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">₹15,000</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] sm:text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 2 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-orange-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1560750588-73207b1ef5b8?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Advanced Hair <br>Treatment</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">₹4,500</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] sm:text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 3 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Luxury Spa <br>Manicure</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">₹1,200</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-red-50 text-red-500 text-[10px] sm:text-xs font-medium mt-1">Offline</span>
                </div>
              </div>
              <!-- Item 4 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-yellow-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Full Body <br>Polishing</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">₹8,000</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] sm:text-xs font-medium mt-1">Active</span>
                </div>
              </div>
              <!-- Item 5 -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-pink-100 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Party Makeup <br>Glam</h4>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">₹3,500</div>
                  <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[10px] sm:text-xs font-medium mt-1">Active</span>
                </div>
              </div>
            </div>
            <a href="/bellavella/packages/index.php" class="block w-full mt-8 sm:mt-10 py-3 rounded-xl border border-gray-200 text-sm sm:text-base font-medium text-gray-600 hover:bg-gray-50 transition-colors text-center">All packages</a>
          </div>

          <!-- Popular Categories -->
          <div class="bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6 sm:mb-8">Popular categories</h2>
            <div class="space-y-6 sm:space-y-8">
              <!-- Bridal -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1596704017254-9b1b1b9e07f9?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Bridal</h4>
                    <p class="text-xs text-gray-400 mt-0.5">14 services</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">342</div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold mt-1" style="background:rgba(124,58,237,0.1);color:#7c3aed">
                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" style="background:#7c3aed"></span>Luxe
                  </span>
                </div>
              </div>
              <!-- Makeup -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Makeup</h4>
                    <p class="text-xs text-gray-400 mt-0.5">9 services</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">267</div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold mt-1" style="background:rgba(124,58,237,0.1);color:#7c3aed">
                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" style="background:#7c3aed"></span>Luxe
                  </span>
                </div>
              </div>
              <!-- Grooming -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1599351431202-1e0f013dcec5?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Grooming</h4>
                    <p class="text-xs text-gray-400 mt-0.5">6 services</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">520</div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold mt-1" style="background:rgba(8,145,178,0.1);color:#0891b2">
                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" style="background:#0891b2"></span>Prime
                  </span>
                </div>
              </div>
              <!-- Skin Care -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Skin Care</h4>
                    <p class="text-xs text-gray-400 mt-0.5">11 services</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">411</div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold mt-1" style="background:rgba(8,145,178,0.1);color:#0891b2">
                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" style="background:#0891b2"></span>Prime
                  </span>
                </div>
              </div>
              <!-- Spa & Wellness -->
              <div class="flex items-center justify-between group cursor-pointer">
                <div class="flex items-center gap-3 sm:gap-4">
                  <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl overflow-hidden shrink-0">
                    <img src="https://images.unsplash.com/photo-1600334089648-b0d9d3028eb8?auto=format&fit=crop&w=256&q=80" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 leading-tight">Spa & Wellness</h4>
                    <p class="text-xs text-gray-400 mt-0.5">8 services</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm sm:text-base font-semibold text-gray-900">189</div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-xs font-semibold mt-1" style="background:rgba(124,58,237,0.1);color:#7c3aed">
                    <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full" style="background:#7c3aed"></span>Luxe
                  </span>
                </div>
              </div>
            </div>
            <a href="/bellavella/categories/index.php" class="block w-full mt-8 sm:mt-10 py-3 rounded-xl border border-gray-200 text-sm sm:text-base font-medium text-gray-600 hover:bg-gray-50 transition-colors text-center">All categories</a>
          </div>

          <div class="bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6 sm:mb-8">Recent reviews</h2>
            <div class="space-y-6 sm:space-y-8">
              <!-- Review 1 -->
              <div class="flex items-start gap-3 sm:gap-4 group cursor-pointer">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50">
                  <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">Ananya Kapoor</h4>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">3d ago</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Bridal Glow Package</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-amber-400 text-xs">★★★★★</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold ml-1">5.0</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">Absolutely loved the bridal package! My skin was glowing on my big day.</p>
                </div>
              </div>
              <!-- Review 2 -->
              <div class="flex items-start gap-3 sm:gap-4 group cursor-pointer">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50">
                  <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">Priya Sharma</h4>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">4d ago</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Advanced Hair Treatment</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-amber-400 text-xs">★★★★<span class="text-gray-200">★</span></span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold ml-1">4.0</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">Great service, my hair feels so much healthier now. Will definitely come back.</p>
                </div>
              </div>
              <!-- Review 3 -->
              <div class="flex items-start gap-3 sm:gap-4 group cursor-pointer">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50">
                  <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">Sneha Gupta</h4>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">6d ago</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Party Makeup Glam</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-amber-400 text-xs">★★★★★</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold ml-1">5.0</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">The makeup artist was incredible! Everyone complimented my look.</p>
                </div>
              </div>
              <!-- Review 4 -->
              <div class="flex items-start gap-3 sm:gap-4 group cursor-pointer">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50">
                  <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">Kavita Patel</h4>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">1w ago</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Bridal Glow Package</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-amber-400 text-xs">★★★★<span class="text-gray-200">★</span></span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold ml-1">4.0</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">Wonderful experience! The team was professional and the results were amazing.</p>
                </div>
              </div>
              <!-- Review 5 -->
              <div class="flex items-start gap-3 sm:gap-4 group cursor-pointer">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-gray-50">
                  <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">Meera Joshi</h4>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">9d ago</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Advanced Hair Treatment</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="text-amber-400 text-xs">★★★★★</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-semibold ml-1">5.0</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-400 mt-1.5 line-clamp-2 leading-relaxed">Best hair treatment I have ever had. My hair is silky smooth now.</p>
                </div>
              </div>
            </div>
            <a href="/bellavella/reviews/index.php" class="block w-full mt-8 sm:mt-10 py-3 rounded-xl border border-gray-200 text-sm sm:text-base font-medium text-gray-600 hover:bg-gray-50 transition-colors text-center">All reviews</a>
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
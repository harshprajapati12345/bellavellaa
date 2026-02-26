@extends('layouts.app')
@php $pageTitle = 'Dashboard'; @endphp

@section('content')
      <!-- 1️⃣ Top Summary Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3"><i data-lucide="calendar" class="w-5 h-5 text-violet-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $bookingsToday ?? 8 }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Bookings Today</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3"><i data-lucide="banknote" class="w-5 h-5 text-emerald-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">₹{{ $todayRevenue ?? '18,500' }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Today's Revenue</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center mb-3"><i data-lucide="user-check" class="w-5 h-5 text-sky-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $activeProfessionals ?? 5 }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Active Professionals</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center mb-3"><i data-lucide="users" class="w-5 h-5 text-pink-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $totalCustomers ?? 124 }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Total Customers</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3"><i data-lucide="star" class="w-5 h-5 text-amber-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $newReviews ?? 3 }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">New Reviews</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0_2px_20px_rgb(0,0,0,0.02)] group hover:shadow-md transition-all">
          <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3"><i data-lucide="package" class="w-5 h-5 text-rose-500"></i></div>
          <p class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $totalServices ?? 42 }}</p>
          <p class="text-xs sm:text-sm text-gray-400 mt-1">Total Services</p>
        </div>
      </div>

      <!-- Row 2: Schedule + Revenue -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
        <!-- 2️⃣ Today's Schedule Preview -->
        <div class="xl:col-span-7 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 tracking-tight">Today's Appointments</h2>
            <span class="text-xs font-medium bg-gray-900 text-white px-3 py-1 rounded-full">{{ now()->format('M d') }}</span>
          </div>
          <div class="space-y-4">
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer group">
              <div class="w-10 text-center shrink-0"><span class="text-sm font-bold text-gray-900">9:00</span><br><span class="text-[10px] text-gray-400">AM</span></div>
              <div class="w-px h-10 bg-gray-200"></div>
              <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-9 h-9 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">Bridal Makeup</p><p class="text-xs text-gray-400">Anjali Kapoor · 2h</p></div>
              <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-semibold shrink-0">Confirmed</span>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
              <div class="w-10 text-center shrink-0"><span class="text-sm font-bold text-gray-900">10:00</span><br><span class="text-[10px] text-gray-400">AM</span></div>
              <div class="w-px h-10 bg-gray-200"></div>
              <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-9 h-9 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">Facial</p><p class="text-xs text-gray-400">Meera Joshi · 45m</p></div>
              <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-semibold shrink-0">Confirmed</span>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
              <div class="w-10 text-center shrink-0"><span class="text-sm font-bold text-gray-900">11:00</span><br><span class="text-[10px] text-gray-400">AM</span></div>
              <div class="w-px h-10 bg-gray-200"></div>
              <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-9 h-9 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">Nail Art</p><p class="text-xs text-gray-400">Sunita Verma · 1h</p></div>
              <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-semibold shrink-0">Pending</span>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
              <div class="w-10 text-center shrink-0"><span class="text-sm font-bold text-gray-900">12:00</span><br><span class="text-[10px] text-gray-400">PM</span></div>
              <div class="w-px h-10 bg-gray-200"></div>
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80" class="w-9 h-9 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">Party Makeup</p><p class="text-xs text-gray-400">Priya Sharma · 1.5h</p></div>
              <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-semibold shrink-0">Confirmed</span>
            </div>
          </div>
          <a href="{{ route('assign.index') }}" class="flex items-center justify-center gap-2 w-full mt-6 py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"><i data-lucide="calendar" class="w-4 h-4"></i> View Full Schedule</a>
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
            <a href="{{ route('assign.index') }}" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors">View all →</a>
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
                @foreach($recentBookings as $booking)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                  <td class="py-3.5">
                    <div class="flex items-center gap-3">
                      <img src="{{ $booking->user->avatar ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=64&q=80' }}" class="w-8 h-8 rounded-full object-cover">
                      <span class="font-medium text-gray-900">{{ $booking->user->name ?? 'Guest' }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 text-gray-600">{{ $booking->service->name ?? 'Service' }}</td>
                  <td class="py-3.5 text-gray-600 hidden sm:table-cell">{{ $booking->professional->name ?? 'N/A' }}</td>
                  <td class="py-3.5 text-gray-600">{{ \Carbon\Carbon::parse($booking->slot)->format('h:i A') }}</td>
                  <td class="py-3.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                      {{ $booking->status === 'Confirmed' ? 'bg-emerald-50 text-emerald-600' : ($booking->status === 'Pending' ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-500') }}">
                      {{ $booking->status }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- 6️⃣ Quick Actions -->
        <div class="xl:col-span-4 bg-white rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-[0_2px_20px_rgb(0,0,0,0.02)]">
          <h2 class="text-xl font-semibold text-gray-900 tracking-tight mb-6">Quick Actions</h2>
          <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('services.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-violet-50 hover:bg-violet-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition-colors"><i data-lucide="plus-circle" class="w-5 h-5 text-violet-600"></i></div>
              <span class="text-xs font-semibold text-violet-700">Add Service</span>
            </a>
            <a href="{{ route('professionals.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-sky-50 hover:bg-sky-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center transition-colors"><i data-lucide="user-plus" class="w-5 h-5 text-sky-600"></i></div>
              <span class="text-xs font-semibold text-sky-700">Add Professional</span>
            </a>
            <a href="{{ route('packages.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors"><i data-lucide="package" class="w-5 h-5 text-amber-600"></i></div>
              <span class="text-xs font-semibold text-amber-700">Create Package</span>
            </a>
            <a href="{{ route('media.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-pink-50 hover:bg-pink-100 transition-colors cursor-pointer group">
              <div class="w-11 h-11 rounded-xl bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center transition-colors"><i data-lucide="image" class="w-5 h-5 text-pink-600"></i></div>
              <span class="text-xs font-semibold text-pink-700">Upload Media</span>
            </a>
            <a href="{{ route('assign.index') }}" class="col-span-2 flex items-center justify-center gap-2 p-4 rounded-2xl bg-gray-900 hover:bg-gray-800 transition-colors cursor-pointer group">
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
            <a href="{{ route('reviews.index') }}" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition-colors">Manage Reviews →</a>
          </div>
          <div class="space-y-5">
            @foreach($recentReviews as $review)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 border border-transparent hover:border-gray-100 transition-all">
              <img src="{{ $review->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name ?? 'User') }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shrink-0">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-semibold text-gray-900">{{ $review->user->name ?? 'Customer' }}</h4>
                  <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-1 mt-0.5">
                  @for($i=1; $i<=5; $i++)
                  <span class="{{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }} text-xs">★</span>
                  @endfor
                </div>
                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed italic">"{{ $review->comment }}"</p>
              </div>
            </div>
            @endforeach
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
              <span class="text-xs font-semibold text-emerald-500">4.9 ★</span>
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
@endsection

@push('scripts')
  <script>
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
@endpush

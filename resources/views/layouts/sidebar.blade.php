@php
// Detect current section from route name
$routeName = Route::currentRouteName() ?? '';
$section = '';
if (str_starts_with($routeName, 'dashboard')) $section = 'dashboard';
elseif (str_starts_with($routeName, 'categories')) $section = 'categories';
elseif (str_starts_with($routeName, 'services')) $section = 'services';
elseif (str_starts_with($routeName, 'packages')) $section = 'packages';
elseif (str_starts_with($routeName, 'professionals')) $section = 'professionals';
elseif (str_starts_with($routeName, 'customers')) $section = 'customers';
elseif (str_starts_with($routeName, 'offers')) $section = 'offers';
elseif (str_starts_with($routeName, 'assign')) $section = 'assign';
elseif (str_starts_with($routeName, 'reviews')) $section = 'reviews';
elseif (str_starts_with($routeName, 'homepage')) $section = 'homepage';
elseif (str_starts_with($routeName, 'media')) $section = 'media';
elseif (str_starts_with($routeName, 'banners')) $section = 'banners';
elseif (str_starts_with($routeName, 'videos')) $section = 'videos';
elseif (str_starts_with($routeName, 'kit-products')) $section = 'kit-products';
elseif (str_starts_with($routeName, 'kit-orders')) $section = 'kit-orders';
elseif (str_starts_with($routeName, 'leaves')) $section = 'leaves';
elseif (str_starts_with($routeName, 'settings')) $section = 'settings';

$isProActive = ($section === 'professionals');
$isAssignActive = ($section === 'assign');
$isReviewsActive = ($section === 'reviews');
$isServicesActive = ($section === 'services');
$isHomepageActive = ($section === 'homepage');
$isMediaActive = ($section === 'media' || $section === 'banners' || $section === 'videos');
$isKitActive = ($section === 'kit-products' || $section === 'kit-orders');
$isCRMActive = ($isHomepageActive || $isMediaActive);

// Shared variables from AppServiceProvider:
// $pendingVerificationCount
// $pendingLeaveCount
// $totalProNotificationCount

// Count pending (unassigned) assign requests for badge (still local for now or can move to provider later)
$pendingAssignCount = \App\Models\Booking::where('status', 'Unassigned')->count();
// Count pending reviews for badge
try { $pendingReviewsCount = \App\Models\Review::where('status', 'Pending')->count(); } catch (\Exception $e) { $pendingReviewsCount = 0; }
@endphp
<aside id="sidebar"
      class="w-72 fixed h-screen top-0 left-0 flex flex-col justify-between p-6 z-50 bg-[#F6F6F6] transition-transform duration-300 -translate-x-full lg:translate-x-0 border-r border-gray-200 lg:border-none shadow-2xl lg:shadow-none">
      <div>
        <!-- Logo area -->
        <div class="flex items-center justify-between mb-10 px-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center text-white shadow-sm">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" />
                    <path d="M2 12H22" stroke="currentColor" />
                    <path d="M12 2V22" stroke="currentColor" />
                    </svg>
                </div>
                <span class="text-black font-semibold text-lg tracking-tight">Bellavella</span>
            </div>
            <!-- Mobile Close Button -->
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-black">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="space-y-1">
          <!-- Dashboard -->
          <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'dashboard' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all sidebar-item-hover">
            <i data-lucide="layout-grid" class="w-5 h-5 text-black"></i>
            <span class="font-medium text-base text-black">Dashboard</span>
          </a>

          <!-- Packages -->
          <a href="{{ route('packages.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'packages' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Packages</span>
          </a>

          <!-- Categories -->
          <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'categories' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="grid-3x3" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Categories</span>
          </a>

          <!-- Services -->
          <a href="{{ route('services.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $isServicesActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="store" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Services</span>
          </a>

          <!-- Professionals dropdown -->
          <div class="relative">
            <button onclick="toggleProfessionals()" id="professionals-btn"
              class="w-full flex items-center justify-between px-4 py-3 {{ $isProActive ? 'bg-white shadow-sm ring-1 ring-gray-200' : 'hover:bg-white' }} rounded-xl text-black transition-all group sidebar-item-hover">
              <div class="flex items-center gap-3">
                <i data-lucide="check-square" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
                <span class="font-normal text-base text-black">Professionals</span>
              </div>
              <div class="flex items-center gap-2">
                @if($totalProNotificationCount > 0)
                <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none shadow-sm shadow-red-500/20">
                  {{ $totalProNotificationCount }}
                </span>
                @endif
                <i data-lucide="chevron-down" class="w-4 h-4 text-black opacity-60 transition-transform duration-200 {{ $isProActive ? 'chevron-rotate' : '' }}"
                  id="professionals-chevron"></i>
              </div>
            </button>
            <!-- Submenu -->
            <div id="professionals-submenu" class="submenu{{ $isProActive ? ' open' : '' }} pl-4 mt-1 space-y-1">
              <a href="{{ route('professionals.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ ($section === 'professionals' && !request()->is('*/verification*') && !request()->is('*/orders*') && !request()->is('*/history*')) ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="users" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Overview</span>
              </a>
              <a href="{{ route('professionals.verification') }}"
                class="flex items-center justify-between px-4 py-2.5 {{ request()->is('*/verification*') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <div class="flex items-center gap-3">
                  <i data-lucide="badge-check" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                  <span class="font-normal text-sm text-black">Verification</span>
                </div>
                @if($pendingVerificationCount > 0)
                <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none shadow-sm">
                  {{ $pendingVerificationCount }}
                </span>
                @endif
              </a>
              <a href="{{ route('professionals.orders') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ request()->is('*/orders*') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Orders</span>
              </a>
              <a href="{{ route('professionals.history') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ request()->is('*/history*') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="history" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">History</span>
              </a>

              <!-- Kit Management Submenu -->
              <div class="relative pt-1 border-t border-gray-100/50 mt-1">
                <div class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kits & Inventory</div>
                <a href="{{ route('kit-products.index') }}"
                  class="flex items-center gap-3 px-4 py-2.5 {{ $section === 'kit-products' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                  <i data-lucide="package" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                  <span class="font-normal text-sm text-black">Kit Products</span>
                </a>
                <a href="{{ route('kit-orders.index') }}"
                  class="flex items-center gap-3 px-4 py-2.5 {{ $section === 'kit-orders' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                  <i data-lucide="truck" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                  <span class="font-normal text-sm text-black">Kit Assignments</span>
                </a>
              </div>

              <!-- Leaves Submenu -->
              <div class="relative pt-1 border-t border-gray-100/50 mt-1">
                <a href="{{ route('leaves.index') }}"
                  class="flex items-center justify-between px-4 py-2.5 {{ $section === 'leaves' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                  <div class="flex items-center gap-3">
                    <i data-lucide="calendar-off" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                    <span class="font-normal text-sm text-black">Leave Requests</span>
                  </div>
                  @if($pendingLeaveCount > 0)
                  <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none shadow-sm">
                    {{ $pendingLeaveCount }}
                  </span>
                  @endif
                </a>
              </div>
            </div>
          </div>


          <!-- Customers -->
          <a href="{{ route('customers.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'customers' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="circle-user-round" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Customers</span>
          </a>

          <!-- Offers -->
          <a href="{{ route('offers.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'offers' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="tag" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Offers</span>
          </a>

          <!-- Assign -->
          <a href="{{ route('assign.index') }}"
            class="flex items-center justify-between px-4 py-3 {{ $isAssignActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <div class="flex items-center gap-3">
              <i data-lucide="clipboard-list" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
              <span class="font-normal text-base text-black">Assign</span>
            </div>
            @if($pendingAssignCount > 0)
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none shadow-sm">
              {{ $pendingAssignCount }}
            </span>
            @endif
          </a>

          <!-- Reviews -->
          <a href="{{ route('reviews.index') }}"
            class="flex items-center justify-between px-4 py-3 {{ $isReviewsActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <div class="flex items-center gap-3">
              <i data-lucide="star" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
              <span class="font-normal text-base text-black">Reviews</span>
            </div>
            @if($pendingReviewsCount > 0)
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none shadow-sm">
              {{ $pendingReviewsCount }}
            </span>
            @endif
          </a>

          <!-- CRM dropdown -->
          <div class="relative">
            <button onclick="toggleCRM()" id="crm-btn"
              class="w-full flex items-center justify-between px-4 py-3 {{ $isCRMActive ? 'bg-white shadow-sm ring-1 ring-gray-200' : 'hover:bg-white' }} rounded-xl text-black transition-all group sidebar-item-hover">
              <div class="flex items-center gap-3">
                <i data-lucide="briefcase" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
                <span class="font-normal text-base text-black">CRM</span>
              </div>
              <i data-lucide="chevron-down" class="w-4 h-4 text-black opacity-60 transition-transform duration-200 {{ $isCRMActive ? 'chevron-rotate' : '' }}"
                id="crm-chevron"></i>
            </button>
            <!-- Submenu -->
            <div id="crm-submenu" class="submenu{{ $isCRMActive ? ' open' : '' }} pl-4 mt-1 space-y-1">
              <!-- 1. Homepage Sections -->
              <a href="{{ route('homepage.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ $isHomepageActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="layout-template" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Homepage </span>
              </a>

              <!-- 3. Banners -->
              <a href="{{ route('media.banners.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ $section === 'banners' || request()->is('*/banners*') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="gallery-horizontal" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Banners</span>
              </a>
              <!-- 4. Videos -->
              <a href="{{ route('media.videos.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ $section === 'videos' || request()->is('*/videos*') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="video" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Videos</span>
              </a>

              <!-- 2. Media Library -->
              <a href="{{ route('media.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 {{ ($section === 'media' && !request()->is('*/banners*') && !request()->is('*/videos*')) ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="library" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Media </span>
              </a>
            </div>
          </div>

          <!-- Settings -->
          <a href="{{ route('settings.index') }}"
            class="flex items-center gap-3 px-4 py-3 {{ $section === 'settings' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black' }} rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="settings" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Settings</span>
          </a>

          <!-- Logout -->
          <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit"
              class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white rounded-xl text-black transition-all group sidebar-item-hover">
              <i data-lucide="log-out" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
              <span class="font-normal text-base text-black">Logout</span>
            </button>
          </form>
        </nav>
      </div>
</aside>

    <!-- Overlay for mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

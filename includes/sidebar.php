<script>if(localStorage.getItem('theme')==='dark')document.documentElement.classList.add('dark');</script>
<?php
// Detect current section from URL path
$urlPath = $_SERVER['REQUEST_URI'];
$section = '';
if (preg_match('#/bellavella/([^/?]+)#', $urlPath, $m)) {
    $section = $m[1];
} elseif (basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($urlPath, '/bellavella/') !== false && substr_count(trim(str_replace('/bellavella/', '', $urlPath), '/'), '/') === 0) {
    $section = 'dashboard';
}
// Sub-section for professionals and media
$subSection = '';
if (preg_match('#/bellavella/[^/]+/([^/?]+)#', $urlPath, $m2)) {
    $subSection = rtrim($m2[1], '/');
    if ($subSection === 'index.php') $subSection = '';
}
// Fallback: root index.php = dashboard
if ($section === 'index.php') $section = 'dashboard';

$proSections = ['professionals'];
$isProActive = in_array($section, $proSections);
$isAssignActive = ($section === 'assign');
$isReviewsActive = ($section === 'reviews');
$isServicesActive = ($section === 'services');
$isHomepageActive = ($section === 'homepage');
$isMediaMgrActive = ($section === 'media-manager');
$isMediaActive = ($section === 'media');
$isCRMActive = ($isHomepageActive || $isMediaMgrActive || $isMediaActive);

// Count pending (unassigned) assign requests for badge
$_sidebarBookings = [
  ['status'=>'Unassigned'],['status'=>'Assigned'],['status'=>'In Progress'],
  ['status'=>'Completed'],['status'=>'Unassigned'],['status'=>'Assigned'],
  ['status'=>'Unassigned'],
];
$pendingAssignCount = count(array_filter($_sidebarBookings, fn($b) => $b['status'] === 'Unassigned'));

// Count pending verification requests for badge
$pendingVerificationCount = 2; // 2 pending verification requests (from mock data)

// Count pending reviews for badge
$pendingReviewsCount = 0; // All video reviews are approved by default
?>
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
          <a href="/bellavella/dashboard/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $section === 'dashboard' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all sidebar-item-hover">
            <i data-lucide="layout-grid" class="w-5 h-5 text-black"></i>
            <span class="font-medium text-base text-black">Dashboard</span>
          </a>

          <!-- Packages -->
          <a href="/bellavella/packages/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $section === 'packages' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Packages</span>
          </a>

          <!-- Categories -->
          <a href="/bellavella/categories/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $section === 'categories' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="grid-3x3" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Categories</span>
          </a>

         

          <!-- Services -->
          <a href="/bellavella/services/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $isServicesActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="store" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Services</span>
          </a>

          <!-- Professionals dropdown -->
          <div class="relative">
            <button onclick="toggleProfessionals()" id="professionals-btn"
              class="w-full flex items-center justify-between px-4 py-3 <?php echo $isProActive ? 'bg-white shadow-sm ring-1 ring-gray-200' : 'hover:bg-white'; ?> rounded-xl text-black transition-all group sidebar-item-hover">
              <div class="flex items-center gap-3">
                <i data-lucide="check-square" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
                <span class="font-normal text-base text-black">Professionals</span>
              </div>
              <i data-lucide="chevron-down" class="w-4 h-4 text-black opacity-60 transition-transform duration-200 <?php echo $isProActive ? 'chevron-rotate' : ''; ?>"
                id="professionals-chevron"></i>
            </button>
            <!-- Submenu -->
            <div id="professionals-submenu" class="submenu<?php echo $isProActive ? ' open' : ''; ?> pl-4 mt-1 space-y-1">
              <a href="/bellavella/professionals/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'professionals' && ($subSection === '' || $subSection === 'index.php')) ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="users" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Overview</span>
              </a>
              <a href="/bellavella/professionals/verification/"
                class="flex items-center justify-between px-4 py-2.5 <?php echo ($section === 'professionals' && $subSection === 'verification') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <div class="flex items-center gap-3">
                  <i data-lucide="badge-check" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                  <span class="font-normal text-sm text-black">Verification</span>
                </div>
                <?php if ($pendingVerificationCount > 0): ?>
                <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none shadow-sm">
                  <?php echo $pendingVerificationCount; ?>
                </span>
                <?php endif; ?>
              </a>
              <a href="/bellavella/professionals/orders/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'professionals' && $subSection === 'orders') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Orders</span>
              </a>
              <a href="/bellavella/professionals/history/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'professionals' && $subSection === 'history') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="history" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">History</span>
              </a>
            </div>
          </div>

          <!-- Users -->
          <a href="/bellavella/users/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $section === 'users' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="user-circle" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Users</span>
          </a>

           <!-- Offers -->
          <a href="/bellavella/offers/"
            class="flex items-center gap-3 px-4 py-3 <?php echo $section === 'offers' ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <i data-lucide="tag" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Offers</span>
          </a>

          <!-- Assign -->
          <a href="/bellavella/assign/"
            class="flex items-center justify-between px-4 py-3 <?php echo $isAssignActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <div class="flex items-center gap-3">
              <i data-lucide="clipboard-list" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
              <span class="font-normal text-base text-black">Assign</span>
            </div>
            <?php if ($pendingAssignCount > 0): ?>
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none shadow-sm">
              <?php echo $pendingAssignCount; ?>
            </span>
            <?php endif; ?>
          </a>

          <!-- Reviews -->
          <a href="/bellavella/reviews/"
            class="flex items-center justify-between px-4 py-3 <?php echo $isReviewsActive ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
            <div class="flex items-center gap-3">
              <i data-lucide="star" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
              <span class="font-normal text-base text-black">Reviews</span>
            </div>
            <?php if ($pendingReviewsCount > 0): ?>
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full bg-red-500 text-white text-[11px] font-bold leading-none shadow-sm">
              <?php echo $pendingReviewsCount; ?>
            </span>
            <?php endif; ?>
          </a>

          <!-- CRM dropdown -->
          <div class="relative">
            <button onclick="toggleCRM()" id="crm-btn"
              class="w-full flex items-center justify-between px-4 py-3 <?php echo $isCRMActive ? 'bg-white shadow-sm ring-1 ring-gray-200' : 'hover:bg-white'; ?> rounded-xl text-black transition-all group sidebar-item-hover">
              <div class="flex items-center gap-3">
                <i data-lucide="briefcase" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
                <span class="font-normal text-base text-black">CRM</span>
              </div>
              <i data-lucide="chevron-down" class="w-4 h-4 text-black opacity-60 transition-transform duration-200 <?php echo $isCRMActive ? 'chevron-rotate' : ''; ?>"
                id="crm-chevron"></i>
            </button>
            <!-- Submenu -->
            <div id="crm-submenu" class="submenu<?php echo $isCRMActive ? ' open' : ''; ?> pl-4 mt-1 space-y-1">
              <!-- 1. Homepage Sections -->
              <a href="/bellavella/homepage/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'homepage' && ($subSection === '' || $subSection === 'edit.php')) ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="layout-template" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Homepage </span>
              </a>
          
              <!-- 3. Banners -->
              <a href="/bellavella/media/banners/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'media' && $subSection === 'banners') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="gallery-horizontal" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Banners</span>
              </a>
              <!-- 4. Videos -->
              <a href="/bellavella/media/videos/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'media' && $subSection === 'videos') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="video" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Videos</span>
              </a>

                  <!-- 2. Media Library -->
              <a href="/bellavella/media/"
                class="flex items-center gap-3 px-4 py-2.5 <?php echo ($section === 'media' && $subSection === '') ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' : 'hover:bg-white text-black'; ?> rounded-xl transition-all group sidebar-item-hover">
                <i data-lucide="library" class="w-4 h-4 text-black opacity-70 group-hover:opacity-100"></i>
                <span class="font-normal text-sm text-black">Media </span>
              </a>
            </div>
          </div>

          <!-- Logout -->
          <a href="#"
            class="flex items-center gap-3 px-4 py-3 hover:bg-white rounded-xl text-black transition-all group sidebar-item-hover">
            <i data-lucide="log-out" class="w-5 h-5 text-black opacity-80 group-hover:opacity-100"></i>
            <span class="font-normal text-base text-black">Logout</span>
          </a>
        </nav>
      </div>

    <script>
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
    </script>
</aside>

    <!-- Overlay for mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity opacity-0"></div>


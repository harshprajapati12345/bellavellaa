<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reviews · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-black-text, .sidebar-black-text span, .sidebar-black-text i,
    .sidebar-black-text a span, .sidebar-black-text button span { color: #000000 !important; }
    .sidebar-black-text [data-lucide] { color: #000000 !important; opacity: 0.8; transition: opacity 0.2s; }
    .sidebar-black-text a:hover [data-lucide], .sidebar-black-text button:hover [data-lucide] { opacity: 1; }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .table-row { transition: background 0.15s; }
    .table-row:hover { background: #fafafa; }
    .filter-tab { transition: all 0.2s; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active):hover { background: #f3f4f6; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Reviews'; include '../includes/header.php'; ?>

    <?php
    // ── Sample data ──────────────────────────────────────────────────────
    $reviews = [
      ['id'=>1,'user'=>'Ananya Kapoor','avatar'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Bridal Glow Package','rating'=>5,'review_type'=>'video','review_text'=>'Absolutely loved the bridal package! My skin was glowing on my big day. Highly recommend this to all brides.','video_path'=>'https://www.w3schools.com/html/mov_bbb.mp4','points_given'=>75,'is_featured'=>1,'status'=>'approved','created_at'=>'2026-02-20 10:30:00'],
      ['id'=>2,'user'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Advanced Hair Treatment','rating'=>4,'review_type'=>'text','review_text'=>'Great service, my hair feels so much healthier now. Will definitely come back for more sessions.','video_path'=>'','points_given'=>0,'is_featured'=>0,'status'=>'approved','created_at'=>'2026-02-19 14:15:00'],
      ['id'=>3,'user'=>'Rahul Verma','avatar'=>'https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Luxury Spa Manicure','rating'=>3,'review_type'=>'video','review_text'=>'Decent experience. The spa was nice but the manicure could have been better.','video_path'=>'https://www.w3schools.com/html/mov_bbb.mp4','points_given'=>50,'is_featured'=>0,'status'=>'approved','created_at'=>'2026-02-18 09:00:00'],
      ['id'=>4,'user'=>'Sneha Gupta','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Party Makeup Glam','rating'=>5,'review_type'=>'video','review_text'=>'The makeup artist was incredible! Everyone at the party complimented my look. Absolutely worth it.','video_path'=>'https://www.w3schools.com/html/mov_bbb.mp4','points_given'=>75,'is_featured'=>0,'status'=>'approved','created_at'=>'2026-02-17 16:45:00'],
      ['id'=>5,'user'=>'Vikram Singh','avatar'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Full Body Polishing','rating'=>2,'review_type'=>'text','review_text'=>'Not satisfied with the service. Expected much better results for the price paid.','video_path'=>'','points_given'=>0,'is_featured'=>0,'status'=>'rejected','created_at'=>'2026-02-16 11:20:00'],
      ['id'=>6,'user'=>'Kavita Patel','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Bridal Glow Package','rating'=>4,'review_type'=>'video','review_text'=>'Wonderful experience! The team was professional and the results were amazing.','video_path'=>'https://www.w3schools.com/html/mov_bbb.mp4','points_given'=>0,'is_featured'=>0,'status'=>'approved','created_at'=>'2026-02-15 13:30:00'],
      ['id'=>7,'user'=>'Meera Joshi','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80','service'=>'Advanced Hair Treatment','rating'=>5,'review_type'=>'text','review_text'=>'Best hair treatment I have ever had. My hair is silky smooth and manageable now.','video_path'=>'','points_given'=>0,'is_featured'=>0,'status'=>'approved','created_at'=>'2026-02-14 08:10:00'],
    ];

    // ── Filter logic ─────────────────────────────────────────────────────
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $filtered = array_filter($reviews, function($r) use ($filter) {
      switch ($filter) {
        case 'text':     return $r['review_type'] === 'text';
        case 'video':    return $r['review_type'] === 'video';
        case 'pending':  return $r['status'] === 'pending';
        case 'approved': return $r['status'] === 'approved';
        default:         return true;
      }
    });

    // ── Stats ────────────────────────────────────────────────────────────
    $totalReviews   = count($reviews);
    $videoReviews   = count(array_filter($reviews, fn($r) => $r['review_type'] === 'video'));
    $pendingReviews = count(array_filter($reviews, fn($r) => $r['status'] === 'pending'));
    $totalPoints    = array_sum(array_column($reviews, 'points_given'));

    // ── Reward calculator (for display) ──────────────────────────────────
    function calculatePoints($review) {
      if ($review['review_type'] === 'text') return 0;
      if ($review['is_featured']) return 100;
      if ($review['rating'] >= 4) return 75;
      return 50;
    }
    ?>

    <div class="flex flex-col gap-6">

      <!-- ── Stats Cards ────────────────────────────────────────────────── -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <!-- Total Reviews -->
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-2 text-gray-500"><i data-lucide="message-square" class="w-5 h-5"></i><span class="text-base font-medium">Total Reviews</span></div>
          </div>
          <div class="flex items-end gap-3">
            <span class="text-5xl font-medium text-gray-900 tracking-tight"><?php echo $totalReviews; ?></span>
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">all time</div>
        </div>

        <!-- Video Reviews -->
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-2 text-gray-500"><i data-lucide="video" class="w-5 h-5"></i><span class="text-base font-medium">Video Reviews</span></div>
          </div>
          <div class="flex items-end gap-3">
            <span class="text-5xl font-medium text-gray-900 tracking-tight"><?php echo $videoReviews; ?></span>
            <div class="flex items-center gap-1 bg-purple-50 text-purple-500 px-2 py-1 rounded-lg text-sm font-medium mb-2">
              <i data-lucide="film" class="w-4 h-4"></i><span><?php echo $totalReviews > 0 ? round(($videoReviews / $totalReviews) * 100) : 0; ?>%</span>
            </div>
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">of total reviews</div>
        </div>

        <!-- Pending Reviews -->
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-2 text-gray-500"><i data-lucide="clock" class="w-5 h-5"></i><span class="text-base font-medium">Pending Reviews</span></div>
          </div>
          <div class="flex items-end gap-3">
            <span class="text-5xl font-medium text-gray-900 tracking-tight"><?php echo $pendingReviews; ?></span>
            <?php if ($pendingReviews > 0): ?>
            <div class="flex items-center gap-1 bg-amber-50 text-amber-500 px-2 py-1 rounded-lg text-sm font-medium mb-2">
              <i data-lucide="alert-circle" class="w-4 h-4"></i><span>Action needed</span>
            </div>
            <?php endif; ?>
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">awaiting approval</div>
        </div>

        <!-- Total Points Given -->
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-2 text-gray-500"><i data-lucide="gift" class="w-5 h-5"></i><span class="text-base font-medium">Total Points Given</span></div>
          </div>
          <div class="flex items-end gap-3">
            <span class="text-5xl font-medium text-gray-900 tracking-tight"><?php echo number_format($totalPoints); ?></span>
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">reward points distributed</div>
        </div>
      </div>

      <!-- ── Filter Tabs + Search ───────────────────────────────────────── -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2 flex-wrap">
          <?php
          $tabs = ['all'=>'All Reviews','text'=>'Text Reviews','video'=>'Video Reviews','pending'=>'Pending','approved'=>'Approved'];
          foreach ($tabs as $key => $label):
            $isActive = ($filter === $key);
          ?>
          <a href="?filter=<?php echo $key; ?>"
             class="filter-tab px-4 py-2 rounded-full text-sm font-medium <?php echo $isActive ? 'active' : 'text-gray-600 bg-white border border-gray-200'; ?>">
            <?php echo $label; ?>
          </a>
          <?php endforeach; ?>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input type="text" id="searchReviews" placeholder="Search reviews..." onkeyup="filterTable()"
                 class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-black/5 transition-all">
        </div>
      </div>

      <!-- ── Reviews Table ──────────────────────────────────────────────── -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1100px]" id="reviewsTable">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">User</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Rating</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Type</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Message</th>
                <th class="px-5 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-widest">Video</th>
                <th class="px-5 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-widest">Points</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($filtered as $r): ?>
              <tr class="table-row border-b border-gray-50" id="review-row-<?php echo $r['id']; ?>" data-id="<?php echo $r['id']; ?>" data-type="<?php echo $r['review_type']; ?>" data-rating="<?php echo $r['rating']; ?>" data-featured="<?php echo $r['is_featured']; ?>" data-status="<?php echo $r['status']; ?>">
                <!-- User -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $r['avatar']; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-100" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900"><?php echo $r['user']; ?></p>
                      <p class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($r['created_at'])); ?></p>
                    </div>
                  </div>
                </td>

                <!-- Service -->
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $r['service']; ?></td>

                <!-- Rating -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-0.5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <?php if ($i <= $r['rating']): ?>
                        <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                      <?php else: ?>
                        <svg class="w-4 h-4 text-gray-200 fill-gray-200" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                      <?php endif; ?>
                    <?php endfor; ?>
                    <span class="text-xs text-gray-400 ml-1"><?php echo $r['rating']; ?>.0</span>
                  </div>
                </td>

                <!-- Type Badge -->
                <td class="px-5 py-4">
                  <?php if ($r['review_type'] === 'video'): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 ring-1 ring-purple-100">
                      <i data-lucide="video" class="w-3 h-3"></i> Video
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                      <i data-lucide="type" class="w-3 h-3"></i> Text
                    </span>
                  <?php endif; ?>
                  <?php if ($r['is_featured']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100 ml-1">
                      <i data-lucide="award" class="w-3 h-3"></i> Featured
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Message -->
                <td class="px-5 py-4">
                  <p class="text-sm text-gray-600 max-w-[200px] truncate" title="<?php echo htmlspecialchars($r['review_text']); ?>">
                    <?php echo htmlspecialchars(mb_strimwidth($r['review_text'], 0, 60, '...')); ?>
                  </p>
                </td>

                <!-- Video Preview -->
                <td class="px-5 py-4 text-center">
                  <?php if ($r['review_type'] === 'video' && !empty($r['video_path'])): ?>
                    <div class="relative w-16 h-24 rounded-lg overflow-hidden border border-gray-100 bg-gray-50 mx-auto group cursor-pointer"
                         onclick="openVideoModal('<?php echo $r['video_path']; ?>')">
                      <video class="w-full h-full object-cover" muted playsinline onmouseover="this.play()" onmouseout="this.pause(); this.currentTime = 0;">
                        <source src="<?php echo $r['video_path']; ?>" type="video/mp4">
                      </video>
                      <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:hidden transition-all">
                        <i data-lucide="play" class="w-4 h-4 text-white fill-white"></i>
                      </div>
                    </div>
                  <?php else: ?>
                    <span class="text-gray-300">—</span>
                  <?php endif; ?>
                </td>

                <!-- Points -->
                <td class="px-5 py-4 text-center">
                  <?php if ($r['points_given'] > 0): ?>
                    <button onclick="givePoints(<?php echo $r['id']; ?>, '<?php echo addslashes($r['user']); ?>', '<?php echo addslashes($r['service']); ?>', <?php echo $r['points_given']; ?>)"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer group">
                      <i data-lucide="zap" class="w-3 h-3"></i>
                      <span class="points-value"><?php echo $r['points_given']; ?></span>
                      <i data-lucide="pencil" class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </button>
                  <?php else: ?>
                    <button onclick="givePoints(<?php echo $r['id']; ?>, '<?php echo addslashes($r['user']); ?>', '<?php echo addslashes($r['service']); ?>', 0)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 hover:bg-black hover:text-white transition-all cursor-pointer">
                      <i data-lucide="gift" class="w-3 h-3"></i> Give Points
                    </button>
                  <?php endif; ?>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <?php
                    $statusClasses = [
                      'pending'  => 'bg-amber-50 text-amber-700 ring-1 ring-amber-100',
                      'approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100',
                      'rejected' => 'bg-red-50 text-red-600 ring-1 ring-red-100',
                    ];
                    $cls = $statusClasses[$r['status']] ?? 'bg-gray-100 text-gray-600';
                  ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $cls; ?>">
                    <?php echo ucfirst($r['status']); ?>
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <?php if ($r['review_type'] === 'video'): ?>
                      <!-- Video reviews approved by default, only delete button shown -->
                      <button onclick="handleAction('delete', <?php echo $r['id']; ?>)" title="Delete"
                              class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-50 hover:border-red-200 transition-all flex items-center justify-center">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                      </button>
                    <?php else: ?>
                      <?php if ($r['status'] === 'pending'): ?>
                        <button onclick="handleAction('approve', <?php echo $r['id']; ?>)" title="Approve"
                                class="w-8 h-8 rounded-lg border border-emerald-200 text-emerald-500 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all flex items-center justify-center">
                          <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="handleAction('reject', <?php echo $r['id']; ?>)" title="Reject"
                                class="w-8 h-8 rounded-lg border border-red-200 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                          <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                      <?php endif; ?>
                      <button onclick="handleAction('delete', <?php echo $r['id']; ?>)" title="Delete"
                              class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-50 hover:border-red-200 transition-all flex items-center justify-center">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>

              <?php if (empty($filtered)): ?>
              <tr>
                <td colspan="9" class="px-5 py-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i></div>
                    <p class="text-sm text-gray-400 font-medium">No reviews found</p>
                    <p class="text-xs text-gray-300">Try a different filter</p>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ── Reward Points Info Card ──────────────────────────────────────── -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-1 flex items-center gap-2">
          <i data-lucide="info" class="w-4 h-4 text-gray-400"></i> Reward Points — Admin Controlled
        </h3>
        <p class="text-sm text-gray-400 mb-4">You decide how many points each review earns. Suggested ranges below are just guidelines.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
            <div class="w-9 h-9 rounded-lg bg-gray-200 flex items-center justify-center"><i data-lucide="type" class="w-4 h-4 text-gray-500"></i></div>
            <div><p class="text-xs text-gray-400">Text Review</p><p class="text-sm font-semibold text-gray-900">0 – 25 pts</p></div>
          </div>
          <div class="flex items-center gap-3 p-3 rounded-xl bg-purple-50">
            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center"><i data-lucide="video" class="w-4 h-4 text-purple-500"></i></div>
            <div><p class="text-xs text-gray-400">Video Review</p><p class="text-sm font-semibold text-gray-900">25 – 75 pts</p></div>
          </div>
          <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center"><i data-lucide="star" class="w-4 h-4 text-amber-500"></i></div>
            <div><p class="text-xs text-gray-400">High Rating (4★/5★)</p><p class="text-sm font-semibold text-gray-900">50 – 100 pts</p></div>
          </div>
          <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><i data-lucide="award" class="w-4 h-4 text-emerald-500"></i></div>
            <div><p class="text-xs text-gray-400">Featured Review</p><p class="text-sm font-semibold text-gray-900">75 – 150 pts</p></div>
          </div>
        </div>
      </div>

    </div><!-- /flex-col gap-6 -->
  </main>
</div>

<!-- ── Video Preview Modal ──────────────────────────────────────────────── -->
<div id="videoModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0">
  <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden w-[90vw] max-w-2xl">
    <button onclick="closeVideoModal()" class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-black/60 text-white hover:bg-black flex items-center justify-center transition-colors">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <video id="modalVideo" class="w-full max-h-[70vh] bg-black" controls></video>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  /* ── Video Modal ─────────────────────────────────────────────────────── */
  function openVideoModal(src) {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');
    video.src = src;
    modal.classList.remove('hidden');
    requestAnimationFrame(() => modal.classList.remove('opacity-0'));
    video.play();
  }

  function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');
    video.pause();
    video.src = '';
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
  }

  // Close on backdrop click
  document.getElementById('videoModal').addEventListener('click', function(e) {
    if (e.target === this) closeVideoModal();
  });

  // Close on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeVideoModal();
  });

  /* ── Action Handlers (live DOM updates) ──────────────────────────────── */
  function handleAction(action, id) {
    const titles = { approve: 'Approve Review?', reject: 'Reject Review?', delete: 'Delete Review?', feature: 'Toggle Featured?' };
    const texts  = {
      approve: 'This will approve the review and award reward points.',
      reject:  'This review will be marked as rejected.',
      delete:  'This review will be permanently deleted.',
      feature: 'Toggle the featured status of this review.'
    };
    const colors = { approve: '#10b981', reject: '#ef4444', delete: '#ef4444', feature: '#f59e0b' };
    const icons  = { approve: 'success', reject: 'warning', delete: 'warning', feature: 'question' };

    Swal.fire({
      title: titles[action],
      text: texts[action],
      icon: icons[action],
      showCancelButton: true,
      confirmButtonColor: colors[action],
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, proceed',
    }).then(result => {
      if (!result.isConfirmed) return;

      const row = document.getElementById('review-row-' + id);
      if (!row) return;

      const statusCell   = row.querySelector('td:nth-child(8)');
      const pointsCell   = row.querySelector('td:nth-child(7)');
      const actionsCell  = row.querySelector('td:nth-child(9)');
      const reviewType   = row.dataset.type;
      const rating       = parseInt(row.dataset.rating);
      let   isFeatured   = parseInt(row.dataset.featured);

      if (action === 'approve') {
        // Update status badge
        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">Approved</span>';
        row.dataset.status = 'approved';

        // Remove approve/reject buttons, keep feature & delete
        removeApproveRejectBtns(actionsCell);

        // Now prompt admin for points
        let suggestedPts = 0;
        if (reviewType === 'video') {
          if (isFeatured) { suggestedPts = 100; }
          else if (rating >= 4) { suggestedPts = 75; }
          else { suggestedPts = 50; }
        }

        Swal.fire({
          title: 'Review Approved! ✅',
          html: '<p class="text-gray-500 text-sm mb-3">How many points would you like to give this user?</p>' +
                '<p class="text-xs text-gray-400">Suggested: <strong>' + suggestedPts + ' pts</strong></p>',
          input: 'number',
          inputValue: suggestedPts,
          inputAttributes: { min: 0, max: 1000, step: 5 },
          showCancelButton: true,
          cancelButtonText: 'Skip (0 pts)',
          confirmButtonText: 'Award Points',
          confirmButtonColor: '#10b981',
          cancelButtonColor: '#9ca3af',
          inputValidator: (value) => {
            if (value < 0) return 'Points cannot be negative';
            if (value > 1000) return 'Maximum 1000 points allowed';
          }
        }).then(pointsResult => {
          let pts = pointsResult.isConfirmed ? parseInt(pointsResult.value) || 0 : 0;
          updatePointsInRow(row, pts);
          updateStatCards();
          if (pts > 0) {
            Swal.fire({ title: pts + ' Points Awarded!', text: 'Reward points given to the user.', icon: 'success', timer: 1800, showConfirmButton: false });
          }
        });

        // Already handled below via the .then()

      } else if (action === 'reject') {
        // Update status badge
        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 ring-1 ring-red-100">Rejected</span>';
        row.dataset.status = 'rejected';

        // Zero points
        pointsCell.innerHTML = '<span class="text-xs text-gray-400">0</span>';

        // Remove approve/reject buttons
        removeApproveRejectBtns(actionsCell);

        Swal.fire({ title: 'Rejected!', text: 'Review has been rejected.', icon: 'info', timer: 1500, showConfirmButton: false });

      } else if (action === 'delete') {
        row.style.transition = 'opacity 0.4s, transform 0.4s';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 400);

        Swal.fire({ title: 'Deleted!', text: 'Review has been removed.', icon: 'success', timer: 1500, showConfirmButton: false });

      } else if (action === 'feature') {
        isFeatured = isFeatured ? 0 : 1;
        row.dataset.featured = isFeatured;

        // Update type cell featured badge
        const typeCell = row.querySelector('td:nth-child(4)');
        const existingFeatured = typeCell.querySelector('.featured-badge');
        if (isFeatured) {
          if (!existingFeatured) {
            const badge = document.createElement('span');
            badge.className = 'featured-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100 ml-1';
            badge.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg> Featured';
            typeCell.appendChild(badge);
          }
        } else {
          if (existingFeatured) existingFeatured.remove();
        }

        // Update the feature button style
        const featureBtn = actionsCell.querySelector('[title="Toggle Featured"]');
        if (featureBtn) {
          if (isFeatured) {
            featureBtn.className = featureBtn.className.replace('border-gray-200 text-gray-400', 'border-amber-300 bg-amber-50 text-amber-500');
          } else {
            featureBtn.className = featureBtn.className.replace('border-amber-300 bg-amber-50 text-amber-500', 'border-gray-200 text-gray-400');
          }
        }

        // Points are now admin-controlled — no auto-recalculation on feature toggle

        Swal.fire({ title: isFeatured ? 'Featured!' : 'Unfeatured', text: isFeatured ? 'Review is now featured.' : 'Featured status removed.', icon: 'success', timer: 1500, showConfirmButton: false });
      }

      updateStatCards();
    });
  }

  /* ── Remove approve/reject buttons from actions cell ─────────────────── */
  function removeApproveRejectBtns(actionsCell) {
    const approveBtn = actionsCell.querySelector('[title="Approve"]');
    const rejectBtn  = actionsCell.querySelector('[title="Reject"]');
    if (approveBtn) approveBtn.remove();
    if (rejectBtn)  rejectBtn.remove();
  }

  /* ── Live stat card updater ──────────────────────────────────────────── */
  function updateStatCards() {
    const rows = document.querySelectorAll('#reviewsTable tbody tr[data-id]');
    let total = 0, videos = 0, pending = 0, points = 0;
    rows.forEach(r => {
      total++;
      if (r.dataset.type === 'video') videos++;
      if (r.dataset.status === 'pending') pending++;
      const ptsBadge = r.querySelector('td:nth-child(7) .text-emerald-600');
      if (ptsBadge) {
        const num = parseInt(ptsBadge.textContent.trim());
        if (!isNaN(num)) points += num;
      }
    });
    // Update card values (stat cards are the first 4 in the grid)
    const cards = document.querySelectorAll('.rounded-3xl .text-5xl');
    if (cards[0]) cards[0].textContent = total;
    if (cards[1]) cards[1].textContent = videos;
    if (cards[2]) cards[2].textContent = pending;
    if (cards[3]) cards[3].textContent = points.toLocaleString();
  }

  /* ── Client-side search filter ───────────────────────────────────────── */
  function filterTable() {
    const term = document.getElementById('searchReviews').value.toLowerCase();
    const rows = document.querySelectorAll('#reviewsTable tbody tr');
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(term) ? '' : 'none';
    });
  }

  /* ── Give Points modal (admin-controlled) ────────────────────────────── */
  function givePoints(id, userName, serviceName, currentPts) {
    Swal.fire({
      title: '<span style="font-size:1.1rem">Award Points</span>',
      html:
        '<div style="text-align:left;margin-bottom:12px">' +
          '<p style="font-size:13px;color:#6b7280;margin-bottom:4px"><strong>' + userName + '</strong> — ' + serviceName + '</p>' +
          (currentPts > 0 ? '<p style="font-size:12px;color:#10b981">Current: ' + currentPts + ' pts</p>' : '') +
        '</div>',
      input: 'number',
      inputValue: currentPts,
      inputAttributes: { min: 0, max: 1000, step: 5 },
      inputLabel: 'Enter points to award',
      showCancelButton: true,
      confirmButtonText: 'Save Points',
      confirmButtonColor: '#000',
      cancelButtonColor: '#9ca3af',
      inputValidator: (value) => {
        if (value === '' || value === null) return 'Please enter a number';
        if (parseInt(value) < 0) return 'Points cannot be negative';
        if (parseInt(value) > 1000) return 'Maximum 1000 points allowed';
      }
    }).then(result => {
      if (!result.isConfirmed) return;
      const pts = parseInt(result.value) || 0;
      const row = document.getElementById('review-row-' + id);
      if (!row) return;

      updatePointsInRow(row, pts);
      updateStatCards();

      Swal.fire({
        title: pts > 0 ? pts + ' Points Saved!' : 'Points Reset',
        text: pts > 0 ? 'Reward points updated for ' + userName + '.' : 'Points set to 0.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    });
  }

  /* ── Update points display in a table row ─────────────────────────────── */
  function updatePointsInRow(row, pts) {
    const pointsCell = row.querySelector('td:nth-child(7)');
    const id = row.dataset.id;
    const userName = row.querySelector('td:nth-child(1) .text-sm.font-semibold')?.textContent || '';
    const serviceName = row.querySelector('td:nth-child(2)')?.textContent?.trim() || '';
    if (pts > 0) {
      pointsCell.innerHTML =
        '<button onclick="givePoints(' + id + ', \'' + userName.replace(/'/g, "\\'") + '\', \'' + serviceName.replace(/'/g, "\\'") + '\', ' + pts + ')"' +
        ' class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer group">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>' +
        '<span class="points-value">' + pts + '</span>' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-hover:opacity-100 transition-opacity"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>' +
        '</button>';
    } else {
      pointsCell.innerHTML =
        '<button onclick="givePoints(' + id + ', \'' + userName.replace(/'/g, "\\'") + '\', \'' + serviceName.replace(/'/g, "\\'") + '\', 0)"' +
        ' class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 hover:bg-black hover:text-white transition-all cursor-pointer">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>' +
        ' Give Points</button>';
    }
  }
</script>
</body>
</html>

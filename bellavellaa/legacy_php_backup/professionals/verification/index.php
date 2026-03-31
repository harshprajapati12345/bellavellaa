<?php
/**
 * professionals/verification/index.php — Verification List
 */
$pageTitle = 'Verification';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/bella/assets/css/style.css">
  <style>
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .avatar-ring { box-shadow: 0 0 0 2px #fff, 0 0 0 4px #e5e7eb; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto pb-28">
    <?php include '../../includes/header.php'; ?>

    <?php
    $requests = [
      ['id'=>1,'name'=>'Anjali Mehta','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 4321','pan'=>'ABCDE1234F','submitted'=>'2024-02-10','status'=>'Pending'],
      ['id'=>2,'name'=>'Meera Pillai','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 8765','pan'=>'FGHIJ5678K','submitted'=>'2024-02-08','status'=>'Rejected'],
      ['id'=>3,'name'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 1234','pan'=>'LMNOP9012Q','submitted'=>'2024-01-20','status'=>'Approved'],
      ['id'=>4,'name'=>'Sunita Rao','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 5678','pan'=>'RSTUV3456W','submitted'=>'2024-01-15','status'=>'Approved'],
      ['id'=>5,'name'=>'Kavita Joshi','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 9012','pan'=>'XYZAB7890C','submitted'=>'2024-02-14','status'=>'Pending'],
    ];
    $pendingCount  = count(array_filter($requests, fn($r) => $r['status'] === 'Pending'));
    $approvedCount = count(array_filter($requests, fn($r) => $r['status'] === 'Approved'));
    $rejectedCount = count(array_filter($requests, fn($r) => $r['status'] === 'Rejected'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- -- Page Header ------------------------------------------------ -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Document Verification</h2>
          <p class="text-sm text-gray-400 mt-0.5">Review Aadhaar & PAN submissions from professionals</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="ver-search" type="text" placeholder="Search by name..." oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>

      <!-- -- Stat Cards ------------------------------------------------- -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $pendingCount; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Awaiting review</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="clock" class="w-6 h-6 text-amber-500"></i>
          </div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Approved</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $approvedCount; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Verified</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="badge-check" class="w-6 h-6 text-emerald-500"></i>
          </div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Rejected</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo $rejectedCount; ?></p>
            <p class="text-xs text-gray-400 mt-0.5">Re-upload needed</p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="x-circle" class="w-6 h-6 text-red-400"></i>
          </div>
        </div>
      </div>

      <!-- -- Verification Flow ------------------------------------------ -->
      <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-black"></div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Verification Ecosystem</p>
        <div class="flex flex-wrap items-center gap-3">
          <?php
          $steps = [
            ['Registers','user-plus','gray'],
            ['Uploads Docs','upload','gray'],
            ['Pending Review','clock','amber'],
            ['Admin Reviews','eye','gray'],
            ['Approved','badge-check','emerald'],
            ['Active Professional','check-circle','emerald']
          ];
          foreach($steps as $i => $step): ?>
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-<?php echo $step[2]; ?>-50/50 border border-<?php echo $step[2]; ?>-100/50">
              <i data-lucide="<?php echo $step[1]; ?>" class="w-3.5 h-3.5 text-<?php echo $step[2]; ?>-500"></i>
              <span class="text-xs font-semibold text-<?php echo $step[2]; ?>-700 whitespace-nowrap"><?php echo $step[0]; ?></span>
            </div>
            <?php if($i < count($steps)-1): ?>
              <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-gray-200"></i>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- -- Table ----------------------------------------------------- -->
      <div class="bg-white rounded-[2rem] shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        
        <!-- Filter Tabs -->
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-1.5 overflow-x-auto">
          <?php foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
          <button onclick="setTab('<?php echo $k; ?>')" id="tab-<?php echo $k; ?>"
            class="filter-tab text-xs font-semibold px-5 py-2 <?php echo $k==='all'?'active':''; ?> whitespace-nowrap tracking-wide">
            <?php echo strtoupper($v); ?>
          </button>
          <?php endforeach; ?>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px]">
            <thead>
              <tr class="border-b border-gray-50 bg-gray-50/30">
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Professional</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Aadhaar No.</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">PAN No.</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Submitted</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Status</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em]">Action</th>
              </tr>
            </thead>
            <tbody id="ver-tbody">
              <?php foreach($requests as $req): ?>
              <tr class="table-row border-b border-gray-50/50"
                  data-id="<?php echo $req['id']; ?>"
                  data-name="<?php echo strtolower($req['name']); ?>"
                  data-status="<?php echo strtolower($req['status']); ?>">
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $req['avatar']; ?>" class="w-10 h-10 rounded-full object-cover avatar-ring flex-shrink-0" alt="">
                    <p class="text-sm font-semibold text-gray-900"><?php echo $req['name']; ?></p>
                  </div>
                </td>
                <td class="px-6 py-5 text-sm text-gray-600 font-mono tracking-tight"><?php echo $req['aadhaar']; ?></td>
                <td class="px-6 py-5 text-sm text-gray-600 font-mono tracking-tight"><?php echo $req['pan']; ?></td>
                <td class="px-6 py-5">
                  <p class="text-sm text-gray-500"><?php echo date('d M Y', strtotime($req['submitted'])); ?></p>
                </td>
                <td class="px-6 py-5">
                  <?php
                  $sc = match($req['status']) { 'Approved'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                  $si = match($req['status']) { 'Approved'=>'badge-check', 'Pending'=>'clock', default=>'x-circle' };
                  ?>
                  <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full <?php echo $sc; ?>">
                    <i data-lucide="<?php echo $si; ?>" class="w-3 h-3"></i><?php echo $req['status']; ?>
                  </span>
                </td>
                <td class="px-6 py-5 text-right">
                  <a href="review.php?id=<?php echo $req['id']; ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all
                    <?php echo $req['status']==='Pending' ? 'bg-black text-white hover:bg-gray-800' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
                    <i data-lucide="<?php echo $req['status']==='Pending'?'shield-check':'eye'; ?>" class="w-3.5 h-3.5"></i>
                    <?php echo $req['status']==='Pending' ? 'Review' : 'View'; ?>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-20 text-center">
          <div class="w-16 h-16 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-4 mx-auto border border-gray-100">
            <i data-lucide="search-x" class="w-8 h-8 text-gray-200"></i>
          </div>
          <p class="text-gray-500 font-medium">No verification requests found</p>
          <p class="text-gray-400 text-xs mt-1">Try a different search or filter</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-8 py-5 border-t border-gray-50 bg-gray-50/20">
          <p id="pagination-info" class="text-xs font-medium text-gray-400 uppercase tracking-widest"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>

      </div>

    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  const ROWS_PER_PAGE = 5;
  let currentPage = 1, visibleRows = [], currentTab = 'all';

  function setTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    applyFilters();
  }

  function applyFilters() {
    const search = document.getElementById('ver-search').value.toLowerCase();
    const allRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row'));
    visibleRows = allRows.filter(row => {
      const nameMatch = row.dataset.name.includes(search);
      const tabMatch = currentTab === 'all' || row.dataset.status === currentTab;
      return nameMatch && tabMatch;
    });
    allRows.forEach(r => r.style.display = 'none');
    currentPage = 1; renderPage();
  }

  function renderPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE, end = start + ROWS_PER_PAGE;
    visibleRows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
    const empty = document.getElementById('empty-state'), pw = document.getElementById('pagination-wrap');
    if (visibleRows.length === 0) { empty.classList.remove('hidden'); empty.classList.add('flex'); pw.classList.add('hidden'); }
    else { empty.classList.add('hidden'); empty.classList.remove('flex'); pw.classList.remove('hidden'); }
    renderPagination();
  }

  function renderPagination() {
    const total = visibleRows.length, totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const start = Math.min((currentPage - 1) * ROWS_PER_PAGE + 1, total), end = Math.min(currentPage * ROWS_PER_PAGE, total);
    document.getElementById('pagination-info').textContent = `Showing ${start}–${end} of ${total} request${total !== 1 ? 's' : ''}`;
    const btns = document.getElementById('pagination-btns'); btns.innerHTML = '';
    
    // Prev
    const prev = document.createElement('button');
    prev.className = 'page-btn w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-white transition-all';
    prev.innerHTML = '<i data-lucide="chevron-left" class="w-4 h-4"></i>';
    prev.disabled = currentPage === 1; 
    prev.onclick = () => { currentPage--; renderPage(); }; btns.appendChild(prev);
    
    for (let i = 1; i <= totalPages; i++) {
        const b = document.createElement('button');
        b.className = `page-btn w-9 h-9 rounded-xl text-xs font-bold transition-all border ${i===currentPage?'active border-black':'border-gray-200 text-gray-400 bg-white hover:border-gray-400'}`;
        b.textContent = i;
        b.onclick = () => { currentPage = i; renderPage(); };
        btns.appendChild(b);
    }
    
    // Next
    const next = document.createElement('button');
    next.className = 'page-btn w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-white transition-all';
    next.innerHTML = '<i data-lucide="chevron-right" class="w-4 h-4"></i>';
    next.disabled = currentPage === totalPages || totalPages === 0;
    next.onclick = () => { currentPage++; renderPage(); }; btns.appendChild(next);
    
    lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>

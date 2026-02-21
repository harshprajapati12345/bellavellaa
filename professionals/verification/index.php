<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; } .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
    .stat-card { transition: box-shadow 0.2s; } .stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .filter-tab { transition: all 0.2s; border-radius: 0.75rem; }
    .filter-tab.active { background: #000; color: #fff; }
    .filter-tab:not(.active) { color: #6b7280; }
    .filter-tab:not(.active):hover { background: #f3f4f6; color: #111; }
    .doc-img { border: 2px solid #f3f4f6; transition: border-color 0.2s; cursor: zoom-in; }
    .doc-img:hover { border-color: #000; }
    .page-btn { transition: all 0.15s; } .page-btn.active { background: #000; color: #fff; }
    .page-btn:not(.active):hover { background: #f3f4f6; }
    .modal-box { transition: transform 0.25s cubic-bezier(.34,1.56,.64,1); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Verification'; include '../../includes/header.php'; ?>

    <?php
    $requests = [
      ['id'=>1,'name'=>'Anjali Mehta','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 4321','pan'=>'ABCDE1234F','submitted'=>'2024-02-10','status'=>'Pending','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=400&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=400&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=400&q=80'],
      ['id'=>2,'name'=>'Meera Pillai','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 8765','pan'=>'FGHIJ5678K','submitted'=>'2024-02-08','status'=>'Rejected','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=400&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=400&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=400&q=80'],
      ['id'=>3,'name'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 1234','pan'=>'LMNOP9012Q','submitted'=>'2024-01-20','status'=>'Approved','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=400&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=400&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=400&q=80'],
      ['id'=>4,'name'=>'Sunita Rao','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 5678','pan'=>'RSTUV3456W','submitted'=>'2024-01-15','status'=>'Approved','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=400&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=400&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=400&q=80'],
      ['id'=>5,'name'=>'Kavita Joshi','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=96&h=96&q=80','aadhaar'=>'XXXX XXXX 9012','pan'=>'XYZAB7890C','submitted'=>'2024-02-14','status'=>'Pending','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=400&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=400&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=400&q=80'],
    ];
    $pendingCount  = count(array_filter($requests, fn($r) => $r['status'] === 'Pending'));
    $approvedCount = count(array_filter($requests, fn($r) => $r['status'] === 'Approved'));
    $rejectedCount = count(array_filter($requests, fn($r) => $r['status'] === 'Rejected'));
    ?>

    <div class="flex flex-col gap-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Document Verification</h2>
          <p class="text-sm text-gray-400 mt-0.5">Review Aadhaar & PAN submissions</p>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input id="ver-search" type="text" placeholder="Search by name…" oninput="applyFilters()"
            class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-56 transition-all">
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p><p class="text-3xl font-bold text-gray-900"><?php echo $pendingCount; ?></p><p class="text-xs text-gray-400 mt-0.5">Awaiting review</p></div>
          <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0"><i data-lucide="clock" class="w-5 h-5 text-amber-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Approved</p><p class="text-3xl font-bold text-gray-900"><?php echo $approvedCount; ?></p><p class="text-xs text-gray-400 mt-0.5">Verified</p></div>
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0"><i data-lucide="badge-check" class="w-5 h-5 text-emerald-500"></i></div>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center justify-between gap-4">
          <div><p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-1">Rejected</p><p class="text-3xl font-bold text-gray-900"><?php echo $rejectedCount; ?></p><p class="text-xs text-gray-400 mt-0.5">Re-upload needed</p></div>
          <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0"><i data-lucide="x-circle" class="w-5 h-5 text-red-400"></i></div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-2xl p-3 shadow-[0_2px_16px_rgba(0,0,0,0.04)] flex items-center gap-1.5">
        <?php foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
        <button onclick="setTab('<?php echo $k; ?>')" id="tab-<?php echo $k; ?>"
          class="filter-tab text-sm font-medium px-4 py-2 <?php echo $k==='all'?'active':''; ?>">
          <?php echo $v; ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- Verification Flow Banner -->
      <div class="bg-white rounded-2xl p-5 shadow-[0_2px_16px_rgba(0,0,0,0.04)]">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Verification Flow</p>
        <div class="flex flex-wrap items-center gap-2">
          <?php
          $steps = [['Registers','user-plus','gray'],['Uploads Docs','upload','gray'],['Pending Review','clock','amber'],['Admin Reviews','eye','gray'],['Approved','badge-check','emerald'],['Can Accept Orders','check-circle','emerald']];
          foreach($steps as $i => $step): ?>
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-<?php echo $step[2]; ?>-50 border border-<?php echo $step[2]; ?>-100">
              <i data-lucide="<?php echo $step[1]; ?>" class="w-3.5 h-3.5 text-<?php echo $step[2]; ?>-500"></i>
              <span class="text-xs font-semibold text-<?php echo $step[2]; ?>-700"><?php echo $step[0]; ?></span>
            </div>
            <?php if($i < count($steps)-1): ?><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300 flex-shrink-0"></i><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[800px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Professional</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Aadhaar No.</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">PAN No.</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Submitted</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Action</th>
              </tr>
            </thead>
            <tbody id="ver-tbody">
              <?php foreach($requests as $req): ?>
              <tr class="table-row border-b border-gray-50"
                  data-id="<?php echo $req['id']; ?>"
                  data-name="<?php echo strtolower($req['name']); ?>"
                  data-status="<?php echo strtolower($req['status']); ?>">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $req['avatar']; ?>" class="w-10 h-10 rounded-full object-cover ring-2 ring-white ring-offset-1 ring-offset-gray-100 flex-shrink-0" alt="">
                    <p class="text-sm font-semibold text-gray-900"><?php echo $req['name']; ?></p>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600 font-mono"><?php echo $req['aadhaar']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-600 font-mono"><?php echo $req['pan']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-500"><?php echo date('d M Y', strtotime($req['submitted'])); ?></td>
                <td class="px-5 py-4">
                  <?php
                  $sc = match($req['status']) { 'Approved'=>'bg-emerald-50 text-emerald-600', 'Pending'=>'bg-amber-50 text-amber-600', default=>'bg-red-50 text-red-500' };
                  $si = match($req['status']) { 'Approved'=>'badge-check', 'Pending'=>'clock', default=>'x-circle' };
                  ?>
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $sc; ?>">
                    <i data-lucide="<?php echo $si; ?>" class="w-3 h-3"></i><?php echo $req['status']; ?>
                  </span>
                </td>
                <td class="px-5 py-4 text-right">
                  <a href="review.php?id=<?php echo $req['id']; ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all
                    <?php echo $req['status']==='Pending' ? 'bg-black text-white hover:bg-gray-800 shadow-sm' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'; ?>">
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
          <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <i data-lucide="search-x" class="w-8 h-8 text-gray-300"></i>
          </div>
          <p class="text-gray-500 font-medium">No requests found</p>
        </div>

        <!-- Pagination -->
        <div id="pagination-wrap" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-gray-100">
          <p id="pagination-info" class="text-sm text-gray-400"></p>
          <div id="pagination-btns" class="flex items-center gap-1.5"></div>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
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
    const prev = document.createElement('button');
    prev.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed';
    prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>';
    prev.disabled = currentPage === 1; prev.onclick = () => { currentPage--; renderPage(); }; btns.appendChild(prev);
    for (let i = 1; i <= totalPages; i++) { const b = document.createElement('button'); b.className = `page-btn w-8 h-8 rounded-lg text-sm font-medium border transition-all ${i===currentPage?'active border-black':'border-gray-200 text-gray-600'}`; b.textContent = i; b.onclick = () => { currentPage = i; renderPage(); }; btns.appendChild(b); }
    const next = document.createElement('button');
    next.className = 'page-btn w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed';
    next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
    next.disabled = currentPage === totalPages || totalPages === 0; next.onclick = () => { currentPage++; renderPage(); }; btns.appendChild(next);
  }

  (function init() { visibleRows = Array.from(document.querySelectorAll('#ver-tbody tr.table-row')); renderPage(); })();
</script>
</body>
</html>

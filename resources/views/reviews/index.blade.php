@extends('layouts.app')
@php $pageTitle = 'Reviews'; @endphp

@section('content')
@php
$reviews = $reviews ?? collect();
$filter  = request('filter', 'all');
$totalReviews   = $reviews->count();
$videoReviews   = $reviews->where('review_type', 'video')->count();
$pendingReviews = $reviews->where('status', 'pending')->count();
$totalPoints    = $reviews->sum('points_given');
$filtered = $reviews->filter(function($r) use ($filter) {
  return match($filter) { 'text' => $r->review_type === 'text', 'video' => $r->review_type === 'video', 'pending' => $r->status === 'pending', 'approved' => $r->status === 'approved', default => true };
});
@endphp

    <div class="flex flex-col gap-6">

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4"><div class="flex items-center gap-2 text-gray-500"><i data-lucide="message-square" class="w-5 h-5"></i><span class="text-base font-medium">Total Reviews</span></div></div>
          <div class="flex items-end gap-3"><span id="stat-total" class="text-5xl font-medium text-gray-900 tracking-tight">{{ $stats['total'] }}</span></div>
          <div class="text-sm text-gray-400 mt-1 pl-1">all time</div>
        </div>
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4"><div class="flex items-center gap-2 text-gray-500"><i data-lucide="video" class="w-5 h-5"></i><span class="text-base font-medium">Video Reviews</span></div></div>
          <div class="flex items-end gap-3"><span id="stat-video" class="text-5xl font-medium text-gray-900 tracking-tight">{{ $stats['video'] }}</span>
            <div class="flex items-center gap-1 bg-purple-50 text-purple-500 px-2 py-1 rounded-lg text-sm font-medium mb-2"><i data-lucide="film" class="w-4 h-4"></i><span>{{ $stats['total'] > 0 ? round(($stats['video'] / $stats['total']) * 100) : 0 }}%</span></div>
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">of total reviews</div>
        </div>
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4"><div class="flex items-center gap-2 text-gray-500"><i data-lucide="clock" class="w-5 h-5"></i><span class="text-base font-medium">Pending Reviews</span></div></div>
          <div class="flex items-end gap-3"><span id="stat-pending" class="text-5xl font-medium text-gray-900 tracking-tight">{{ $stats['pending'] }}</span>
            @if($stats['pending'] > 0)<div class="flex items-center gap-1 bg-amber-50 text-amber-500 px-2 py-1 rounded-lg text-sm font-medium mb-2"><i data-lucide="alert-circle" class="w-4 h-4"></i><span>Action needed</span></div>@endif
          </div>
          <div class="text-sm text-gray-400 mt-1 pl-1">awaiting approval</div>
        </div>
        <div class="bg-[#FCFCFC] border border-gray-50 rounded-3xl p-6 relative group hover:border-gray-100 transition-colors">
          <div class="flex items-start justify-between mb-4"><div class="flex items-center gap-2 text-gray-500"><i data-lucide="gift" class="w-5 h-5"></i><span class="text-base font-medium">Total Points Given</span></div></div>
          <div class="flex items-end gap-3"><span id="stat-points" class="text-5xl font-medium text-gray-900 tracking-tight">{{ number_format($stats['points']) }}</span></div>
          <div class="text-sm text-gray-400 mt-1 pl-1">reward points distributed</div>
        </div>
      </div>

      <!-- Filter Tabs + Search -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2 flex-wrap">
          @foreach(['all'=>'All Reviews','text'=>'Text Reviews','video'=>'Video Reviews','pending'=>'Pending','approved'=>'Approved'] as $key => $label)
          <a href="?filter={{ $key }}"
             class="filter-tab px-4 py-2 rounded-full text-sm font-medium {{ $filter === $key ? 'active' : 'text-gray-600 bg-white border border-gray-200' }}">
            {{ $label }}
          </a>
          @endforeach
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
          <input type="text" id="searchReviews" placeholder="Search reviews..." onkeyup="filterTable()"
                 class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-black/5 transition-all">
        </div>
      </div>

      <!-- Reviews Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1100px]" id="reviewsTable">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Customer</th>
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
              @forelse($filtered as $r)
              @php
                $statusClasses = ['pending'=>'bg-amber-50 text-amber-700 ring-1 ring-amber-100','approved'=>'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100','rejected'=>'bg-red-50 text-red-600 ring-1 ring-red-100'];
                $cls = $statusClasses[$r->status ?? ''] ?? 'bg-gray-100 text-gray-600';
              @endphp
              <tr class="table-row border-b border-gray-50" id="review-row-{{ $r->id }}" 
                  data-id="{{ $r->id }}" 
                  data-customer-name="{{ $r->customer->name ?? $r->customer_name ?? 'Customer' }}"
                  data-customer-avatar="{{ $r->customer->avatar ?? $r->customer_avatar ?? 'https://i.pravatar.cc/80?img='.($r->id % 50) }}"
                  data-date="{{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '—' }}"
                  data-service="{{ $r->service_name ?? $r->service ?? '—' }}"
                  data-type="{{ $r->review_type }}" 
                  data-rating="{{ $r->rating }}" 
                  data-featured="{{ $r->is_featured ?? 0 }}" 
                  data-status="{{ $r->status }}"
                  data-message="{{ $r->review_text ?? '' }}"
                  data-video="{{ $r->video_path ?? '' }}"
                  data-points="{{ $r->points_given ?? 0 }}">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="{{ $r->customer->avatar ?? $r->customer_avatar ?? 'https://i.pravatar.cc/80?img='.($r->id % 50) }}" class="w-10 h-10 rounded-full object-cover border border-gray-100" alt="">
                    <div>
                      <p class="text-sm font-semibold text-gray-900">{{ $r->customer->name ?? $r->customer_name ?? 'Customer' }}</p>
                      <p class="text-xs text-gray-400">{{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '—' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ $r->service_name ?? $r->service ?? '—' }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= ($r->rating ?? 0))
                      <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                      @else
                      <svg class="w-4 h-4 text-gray-200 fill-gray-200" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                      @endif
                    @endfor
                    <span class="text-xs text-gray-400 ml-1">{{ $r->rating ?? 0 }}.0</span>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <span class="type-container">
                    @if(($r->review_type ?? 'text') === 'video')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 ring-1 ring-purple-100"><i data-lucide="video" class="w-3 h-3"></i> Video</span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><i data-lucide="type" class="w-3 h-3"></i> Text</span>
                    @endif
                    @if($r->is_featured ?? false)
                    <span class="featured-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-600 ring-1 ring-amber-100 ml-1"><i data-lucide="award" class="w-3 h-3"></i> Featured</span>
                    @endif
                  </span>
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm text-gray-600 max-w-[200px] truncate" title="{{ $r->review_text ?? '' }}">{{ Str::limit($r->review_text ?? '', 60) }}</p>
                </td>
                <td class="px-5 py-4 text-center">
                  @if(($r->review_type ?? 'text') === 'video' && !empty($r->video_path))
                  <div class="relative w-16 h-24 rounded-lg overflow-hidden border border-gray-100 bg-gray-50 mx-auto group cursor-pointer" onclick="openVideoModal('{{ $r->video_path }}')">
                    <video class="w-full h-full object-cover" muted playsinline onmouseover="this.play()" onmouseout="this.pause(); this.currentTime = 0;">
                      <source src="{{ $r->video_path }}" type="video/mp4">
                    </video>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:hidden transition-all"><i data-lucide="play" class="w-4 h-4 text-white fill-white"></i></div>
                  </div>
                  @else
                  <span class="text-gray-300">—</span>
                  @endif
                </td>
                <td class="px-5 py-4 text-center">
                  @if(($r->points_given ?? 0) > 0)
                  <button onclick="givePoints({{ $r->id }}, '{{ addslashes($r->customer->name ?? $r->customer_name ?? '') }}', '{{ addslashes($r->service_name ?? $r->service ?? '') }}', {{ $r->points_given }})"
                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer group">
                    <i data-lucide="zap" class="w-3 h-3"></i><span class="points-value">{{ $r->points_given }}</span><i data-lucide="pencil" class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  </button>
                  @else
                  <button onclick="givePoints({{ $r->id }}, '{{ addslashes($r->customer->name ?? $r->customer_name ?? '') }}', '{{ addslashes($r->service_name ?? $r->service ?? '') }}', 0)"
                          class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 hover:bg-black hover:text-white transition-all cursor-pointer">
                    <i data-lucide="gift" class="w-3 h-3"></i> Give Points
                  </button>
                  @endif
                </td>
                <td class="px-5 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cls }}">{{ ucfirst($r->status ?? 'pending') }}</span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                      <button type="button" class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center" data-id="{{ $r->id }}" data-type="reviews" title="View">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      </button>
                    <button onclick="handleAction('feature', {{ $r->id }})" title="Toggle Featured"
                            class="w-8 h-8 rounded-lg border {{ ($r->is_featured ?? false) ? 'border-amber-300 bg-amber-50 text-amber-500' : 'border-gray-200 text-gray-400' }} hover:bg-amber-50 hover:text-amber-500 hover:border-amber-300 transition-all flex items-center justify-center">
                      <i data-lucide="award" class="w-3.5 h-3.5"></i>
                    </button>

                    @if(($r->review_type ?? 'text') === 'video')
                    <button onclick="handleAction('delete', {{ $r->id }})" title="Delete" class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-50 hover:border-red-200 transition-all flex items-center justify-center"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    @else
                      @if(($r->status ?? '') === 'pending')
                      <button onclick="handleAction('approve', {{ $r->id }})" title="Approve" class="w-8 h-8 rounded-lg border border-emerald-200 text-emerald-500 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all flex items-center justify-center"><i data-lucide="check" class="w-3.5 h-3.5"></i></button>
                      <button onclick="handleAction('reject', {{ $r->id }})" title="Reject" class="w-8 h-8 rounded-lg border border-red-200 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                      @endif
                    <button onclick="handleAction('delete', {{ $r->id }})" title="Delete" class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-50 hover:border-red-200 transition-all flex items-center justify-center"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="9" class="px-5 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center"><i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i></div>
                  <p class="text-sm text-gray-400 font-medium">No reviews found</p>
                  <p class="text-xs text-gray-300">Try a different filter</p>
                </div>
              </td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reward Points Info Card -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-1 flex items-center gap-2"><i data-lucide="info" class="w-4 h-4 text-gray-400"></i> Reward Points — Admin Controlled</h3>
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

    </div>

<!-- Video Preview Modal -->
  </div>
</div>

@endsection

@push('styles')
<style>
  .table-row { transition: background 0.15s; } .table-row:hover { background: #fafafa; }
  .filter-tab { transition: all 0.2s; } .filter-tab.active { background: #000; color: #fff; } .filter-tab:not(.active):hover { background: #f3f4f6; }
</style>
@endpush

@push('scripts')
<script>
  /* Video Modal */
  function openVideoModal(src) { const m = document.getElementById('videoModal'), v = document.getElementById('modalVideo'); v.src = src; m.classList.remove('hidden'); requestAnimationFrame(() => m.classList.remove('opacity-0')); v.play(); }
  function closeVideoModal() { const m = document.getElementById('videoModal'), v = document.getElementById('modalVideo'); v.pause(); v.src = ''; m.classList.add('opacity-0'); setTimeout(() => m.classList.add('hidden'), 300); }
  document.getElementById('videoModal').addEventListener('click', function(e) { if (e.target === this) closeVideoModal(); });
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeVideoModal(); });

  /* Removed local drawer logic — handled by global AJAX sidebar */

  /* Action Handlers */
  function handleAction(action, id) {
    const titles = { approve: 'Approve Review?', reject: 'Reject Review?', delete: 'Delete Review?', feature: 'Toggle Featured?' };
    const texts = { approve: 'This will approve the review and award reward points.', reject: 'This review will be marked as rejected.', delete: 'This review will be permanently deleted.', feature: 'Toggle the featured status of this review.' };
    const colors = { approve: '#10b981', reject: '#ef4444', delete: '#ef4444', feature: '#f59e0b' };
    const icons = { approve: 'success', reject: 'warning', delete: 'warning', feature: 'question' };
    const endpoints = {
      approve: `/reviews/${id}/approve`,
      reject: `/reviews/${id}/reject`,
      delete: `/reviews/${id}`,
      feature: `/reviews/${id}/toggle-featured`
    };
    const method = action === 'delete' ? 'DELETE' : 'POST';

    Swal.fire({ title: titles[action], text: texts[action], icon: icons[action], showCancelButton: true, confirmButtonColor: colors[action], cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, proceed' })
    .then(result => {
      if (!result.isConfirmed) return;

      fetch(endpoints[action], {
        method: method,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
      })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .catch(error => {
        // Even if JSON fails, standard redirect back might work, but we want to update UI
        window.location.reload();
      })
      .finally(() => {
        if (action === 'approve') {
          // After approval, prompt for points if it was a manual action
          // But since we reload, we might need a different approach or just let the reload handle it
          window.location.reload();
        } else {
          window.location.reload();
        }
      });
    });
  }


  /* Live stat card updater */
  function updateStatCards() {
    const rows = document.querySelectorAll('#reviewsTable tbody tr.table-row');
    let total = 0, videos = 0, pending = 0, points = 0;
    rows.forEach(r => {
      total++;
      if (r.dataset.type === 'video') videos++;
      if (r.dataset.status === 'pending') pending++;
      const pVal = r.querySelector('.points-value');
      if (pVal) points += parseInt(pVal.textContent) || 0;
    });
    const sTotal = document.getElementById('stat-total'), sVideo = document.getElementById('stat-video'), sPending = document.getElementById('stat-pending'), sPoints = document.getElementById('stat-points');
    if (sTotal) sTotal.textContent = total;
    if (sVideo) sVideo.textContent = videos;
    if (sPending) sPending.textContent = pending;
    if (sPoints) sPoints.textContent = points.toLocaleString();
  }

  /* Client-side search */
  function filterTable() { const term = document.getElementById('searchReviews').value.toLowerCase(); document.querySelectorAll('#reviewsTable tbody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(term) ? '' : 'none'); }

  /* Give Points modal */
  function givePoints(id, customerName, serviceName, currentPts) {
    Swal.fire({ title: '<span style="font-size:1.1rem">Award Points</span>', html: '<div style="text-align:left;margin-bottom:12px"><p style="font-size:13px;color:#6b7280;margin-bottom:4px"><strong>' + customerName + '</strong> — ' + serviceName + '</p>' + (currentPts > 0 ? '<p style="font-size:12px;color:#10b981">Current: ' + currentPts + ' pts</p>' : '') + '</div>', input: 'number', inputValue: currentPts, inputAttributes: { min: 0, max: 1000, step: 5 }, inputLabel: 'Enter points to award', showCancelButton: true, confirmButtonText: 'Save Points', confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', inputValidator: (v) => { if (v === '' || v === null) return 'Please enter a number'; if (parseInt(v) < 0) return 'Points cannot be negative'; if (parseInt(v) > 1000) return 'Maximum 1000 points'; } })
    .then(r => {
      if (!r.isConfirmed) return;
      const pts = parseInt(r.value) || 0;
      
      fetch(`/reviews/${id}/award-points`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ points: pts })
      })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        window.location.reload();
      })
      .catch(error => {
        window.location.reload();
      });
    });
  }


  /* Update points display */
  function updatePointsInRow(row, pts) {
    const pointsCell = row.querySelector('td:nth-child(7)'), id = row.dataset.id;
    const customerName = row.querySelector('td:nth-child(1) .font-semibold')?.textContent || '';
    const serviceName = row.querySelector('td:nth-child(2)')?.textContent?.trim() || '';
    if (pts > 0) {
      pointsCell.innerHTML = `<button onclick="givePoints(${id}, '${customerName.replace(/'/g,"\\'")}', '${serviceName.replace(/'/g,"\\'")}', ${pts})" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer group"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg><span class="points-value">${pts}</span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-hover:opacity-100 transition-opacity"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></button>`;
    } else {
      pointsCell.innerHTML = `<button onclick="givePoints(${id}, '${customerName.replace(/'/g,"\\'")}', '${serviceName.replace(/'/g,"\\'")}', 0)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 hover:bg-black hover:text-white transition-all cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg> Give Points</button>`;
    }
  }
</script>
@endpush

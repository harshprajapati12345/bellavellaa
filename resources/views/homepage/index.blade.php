@extends('layouts.app')
@php $pageTitle = 'Home Page Manager'; @endphp

@section('content')
@php
$sections = $sections ?? collect();
@endphp

  <!-- Top Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
    <div>
      <p class="text-sm text-gray-400 mt-1">Drag sections to reorder. Changes save automatically.</p>
    </div>
    <a href="{{ route('homepage.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
      <i data-lucide="plus" class="w-4 h-4"></i>
      Add Section
    </a>
  </div>

  <!-- Sections List (Draggable) -->
  <div id="sections-list" class="space-y-3">
    @foreach($sections as $s)
    @php
        $iconMap = [
            'hero' => 'image',
            'about' => 'info',
            'services' => 'scissors',
            'packages' => 'shopping-bag',
            'professionals' => 'users',
            'testimonials' => 'message-circle',
            'gallery' => 'grid-3x3',
            'contact' => 'phone'
        ];
        $icon = $iconMap[$s->section] ?? 'layout';
        $ctype = $s->content['content_type'] ?? 'static';
        $ctypeLabel = ucfirst($ctype);
    @endphp
    <div class="section-card bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-5 flex items-center gap-4"
         draggable="true" data-id="{{ $s->id }}">

      <!-- Drag Handle -->
      <div class="flex-shrink-0 text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing">
        <i data-lucide="grip-vertical" class="w-5 h-5"></i>
      </div>

      <!-- Order Badge -->
      <div class="w-8 h-8 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500 order-badge">
        {{ $s->sort_order ?? $loop->iteration }}
      </div>

      <!-- Section Icon -->
      <div class="w-10 h-10 flex-shrink-0 bg-gray-50 rounded-xl flex items-center justify-center">
        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
      </div>

      <!-- Section Info -->
      <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-gray-900">{{ $s->content['name'] ?? $s->title ?? $s->section }}</h3>
        <div class="flex items-center gap-3 mt-1">
          <span class="text-xs text-gray-400 font-mono">{{ $s->section }}</span>
          <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ strtolower($ctype) === 'dynamic' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">{{ $ctypeLabel }}</span>
        </div>
      </div>

      <!-- Status Toggle -->
      <div class="flex-shrink-0">
        <label class="toggle-switch">
          <input type="checkbox" {{ $s->status === 'Active' ? 'checked' : '' }} onchange="toggleSection({{ $s->id }}, this)">
          <span class="toggle-slider"></span>
        </label>
      </div>

      <!-- Edit Button -->
      <a href="{{ route('homepage.edit', $s->id) }}"
        class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" title="Edit">
        <i data-lucide="pencil" class="w-4 h-4"></i>
      </a>

      <!-- Delete Button -->
      <button onclick="confirmDelete({{ $s->id }}, '{{ addslashes($s->content['name'] ?? $s->title ?? $s->section) }}')"
        class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
      </button>
    </div>
    @endforeach
  </div>

  @if($sections->isEmpty())
  <div class="mt-6 bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-12 text-center">
    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <i data-lucide="layout" class="w-8 h-8 text-gray-300"></i>
    </div>
    <h3 class="text-base font-semibold text-gray-900">No sections yet</h3>
    <p class="text-gray-400 text-sm mt-1">Start building your homepage by adding your first section.</p>
    <a href="{{ route('homepage.create') }}" class="inline-flex items-center gap-2 mt-5 px-6 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
      <i data-lucide="plus" class="w-4 h-4"></i> Add Section
    </a>
  </div>
  @endif

  <!-- Info Card -->
  <div class="mt-8 bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 flex items-start gap-4">
    <div class="w-10 h-10 flex-shrink-0 bg-blue-50 rounded-xl flex items-center justify-center">
      <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
    </div>
    <div>
      <h4 class="text-sm font-semibold text-gray-900 mb-1">How it works</h4>
      <ul class="text-sm text-gray-500 space-y-1">
        <li>- Drag sections to reorder how they appear on the homepage</li>
        <li>- Toggle visibility to show/hide sections without deleting</li>
        <li>- <strong>Dynamic</strong> sections auto-fetch data from Services, Packages, etc.</li>
        <li>- <strong>Static</strong> sections use the content you provide</li>
      </ul>
    </div>
  </div>

@endsection

@push('styles')
<style>
  .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .toggle-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #e5e7eb; border-radius: 999px; transition: 0.25s;
  }
  .toggle-slider:before {
    content: ''; position: absolute;
    width: 16px; height: 16px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: 0.25s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  }
  input:checked + .toggle-slider { background: #000; }
  input:checked + .toggle-slider:before { transform: translateX(16px); }

  .section-card {
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    cursor: grab;
  }
  .section-card:active { cursor: grabbing; }
  .section-card.dragging {
    opacity: 0.5;
    transform: scale(0.98);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }
  .section-card.drag-over {
    border-top: 3px solid #000;
  }
</style>
@endpush

@push('scripts')
<script>
  // Drag & Drop
  const list = document.getElementById('sections-list');
  let draggedEl = null;

  if (list) {
    list.querySelectorAll('.section-card').forEach(card => {
      card.addEventListener('dragstart', (e) => {
        draggedEl = card;
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
      });
      card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        list.querySelectorAll('.section-card').forEach(c => c.classList.remove('drag-over'));
        updateOrder();
      });
      card.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (card !== draggedEl) {
          card.classList.add('drag-over');
        }
      });
      card.addEventListener('dragleave', () => {
        card.classList.remove('drag-over');
      });
      card.addEventListener('drop', (e) => {
        e.preventDefault();
        card.classList.remove('drag-over');
        if (card !== draggedEl) {
          const cards = [...list.children];
          const fromIdx = cards.indexOf(draggedEl);
          const toIdx = cards.indexOf(card);
          if (fromIdx < toIdx) {
            card.after(draggedEl);
          } else {
            card.before(draggedEl);
          }
        }
      });
    });
  }

  function updateOrder() {
    const cards = [...list.querySelectorAll('.section-card')];
    const order = cards.map((card, i) => ({
      id: card.dataset.id,
      position: i + 1
    }));

    cards.forEach((card, i) => {
      const badge = card.querySelector('.order-badge');
      if (badge) badge.textContent = i + 1;
    });

    axios.post('{{ route("homepage.reorder") }}', { order: order })
      .then(() => {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Order updated',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
        }
      });
  }

  /* Toggle section visibility */
  function toggleSection(id, el) {
    const status = el.checked ? 'Active' : 'Inactive';
    axios.patch(`{{ url('homepage') }}/${id}/toggle-status`, { status: status })
      .then(() => {
        const label = status === 'Active' ? 'visible' : 'hidden';
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: `Section ${label}`, showConfirmButton: false, timer: 1500 });
      });
  }

  /* Confirm delete */
  function confirmDelete(id, name) {
    Swal.fire({ title: 'Delete Section?', text: `"${name}" will be permanently removed from the homepage.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, delete it' })
    .then(r => {
      if (r.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('homepage.destroy', '') }}/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endpush

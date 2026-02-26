@extends('layouts.app')
@php $pageTitle = 'Users'; @endphp

@section('content')
    <div class="flex flex-col gap-6">

      <!-- ── Page Header ──────────────────────────────────────────────────── -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Users</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage all customer accounts in one place</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ route('users.create') }}"
            class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10 whitespace-nowrap">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Add User
          </a>
        </div>
      </div>

      <!-- ── Table Layout ─────────────────────────────────────────────── -->
      <div class="flex flex-col gap-4">

        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="relative">
              <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
              <input id="user-search" type="text" placeholder="Search users…" onkeyup="filterUsers()"
                class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black/40 w-52 transition-all">
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]" id="users-table">
              <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">User</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">City</th>
                  <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                  <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr class="table-row border-b border-gray-50">
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <img src="{{ $user->avatar ?? 'https://i.pravatar.cc/80?u='.$user->id }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100" alt="">
                      <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $user->email }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600">{{ $user->phone ?? '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <span class="text-sm text-gray-600">{{ $user->city ?? '—' }}</span>
                  </td>
                  <td class="px-5 py-4">
                    <label class="toggle-switch">
                      <input type="checkbox" {{ $user->status === 'Active' ? 'checked' : '' }} onchange="toggleUserStatus({{ $user->id }}, this)">
                      <span class="toggle-slider"></span>
                    </label>
                  </td>
                  <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                      <button class="view-btn w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-all flex items-center justify-center" data-id="{{ $user->id }}" data-type="users" title="View">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                      </button>
                      <a href="{{ route('users.edit', $user->id) }}" title="Edit"
                        class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                      </a>
                      <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Delete"
                        class="w-8 h-8 rounded-lg border border-red-100 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all flex items-center justify-center">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
@endsection

@push('scripts')
<script>
  function filterUsers() {
    const term = document.getElementById('user-search').value.toLowerCase();
    const rows = document.querySelectorAll('#users-table tbody tr');
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(term) ? '' : 'none';
    });
  }

  function toggleUserStatus(id, checkbox) {
    fetch(`/users/${id}/toggle-status`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const status = data.status;
        Swal.fire({
          title: 'Success!',
          text: `User is now ${status}.`,
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
      }
    });
  }

  function deleteUser(id, name) {
    Swal.fire({
      title: `Delete ${name}?`,
      text: "This action cannot be undone.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Yes, delete it'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/users/${id}`;
        form.innerHTML = `
          <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
          <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }
</script>
@endpush

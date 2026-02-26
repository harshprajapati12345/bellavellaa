@extends('layouts.app')
@php $pageTitle = 'Edit User'; @endphp

@section('content')
    <div class="flex flex-col gap-6">
      <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit User</h2>
          <p class="text-sm text-gray-400 mt-0.5">Modify account details for {{ $user->name }}</p>
        </div>
      </div>

      <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-8 border-b border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left: Info -->
            <div class="md:col-span-2 space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Personal Details</h3>
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div class="sm:col-span-2">
                     <label class="form-label">Full Name *</label>
                     <input type="text" name="name" class="form-input" placeholder="e.g. Ananya Kapoor" required value="{{ old('name', $user->name) }}">
                     @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                     <label class="form-label">Email Address *</label>
                     <input type="email" name="email" class="form-input" placeholder="e.g. ananya@example.com" required value="{{ old('email', $user->email) }}">
                     @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                     <label class="form-label">Phone Number</label>
                     <input type="tel" name="phone" class="form-input" placeholder="+91 00000 00000" value="{{ old('phone', $user->phone) }}">
                     @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                     <label class="form-label">City</label>
                     <input type="text" name="city" class="form-input" placeholder="e.g. Delhi" value="{{ old('city', $user->city) }}">
                     @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                     <label class="form-label">Postal Code</label>
                     <input type="text" name="zip" class="form-input" placeholder="e.g. 110001" value="{{ old('zip', $user->zip) }}">
                     @error('zip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div class="sm:col-span-2">
                     <label class="form-label">Address</label>
                     <textarea name="address" rows="3" class="form-input resize-none" placeholder="Full address...">{{ old('address', $user->address) }}</textarea>
                     @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                  </div>
               </div>
            </div>

            <!-- Right: Media & Settings -->
            <div class="space-y-6">
               <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Profile & Status</h3>
               
               <div>
                  <label class="form-label">Profile Photo</label>
                  <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all overflow-hidden relative">
                    @if($user->avatar)
                    <img id="preview" src="{{ $user->avatar }}" class="w-full h-full object-cover rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                      <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                    </div>
                    @else
                    <i data-lucide="camera" class="w-6 h-6 text-gray-300 mb-2"></i>
                    <p class="text-xs text-gray-400">Upload photo</p>
                    <img id="preview" class="hidden w-full h-full object-cover rounded-2xl">
                    @endif
                    <input type="file" name="avatar" class="hidden" onchange="previewImage(this)">
                  </label>
               </div>

               <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                 <div><p class="text-sm font-medium text-gray-900">Active Account</p><p class="text-xs text-gray-400">User can log in</p></div>
                 <label class="toggle-switch"><input type="checkbox" name="status" {{ $user->status === 'Active' ? 'checked' : '' }}><span class="toggle-slider"></span></label>
               </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
          <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
          <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl hover:bg-gray-800 transition-all font-semibold shadow-lg shadow-black/10">Update User</button>
        </div>
      </form>
    </div>
@endsection

@push('scripts')
<script>
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('preview');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        const icon = input.parentElement.querySelector('i');
        const p = input.parentElement.querySelector('p');
        if (icon) icon.classList.add('hidden');
        if (p) p.classList.add('hidden');
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endpush

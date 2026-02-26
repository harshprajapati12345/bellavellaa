@extends('layouts.app')
@php $pageTitle = 'Add Professional'; @endphp

@section('content')
<div class="flex flex-col gap-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('professionals.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
      <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
    </a>
    <div>
      <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Professional</h2>
      <p class="text-sm text-gray-400 mt-0.5">Register a new beauty professional</p>
    </div>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
      <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('professionals.store') }}" enctype="multipart/form-data" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    @csrf
    <div class="p-8 border-b border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Main Form Info -->
        <div class="md:col-span-2 space-y-6">
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Personal Information</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
              <label class="form-label">Full Name *</label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Priya Sharma" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="priya@example.com" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Phone *</label>
              <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" class="form-input" required>
            </div>
            <div>
              <label class="form-label">Category *</label>
              <select name="category" class="form-input cursor-pointer" required>
                <option value="">Select category</option>
                @foreach(['Makeup Artist','Hair Stylist','Nail Technician','Skincare Specialist','Wellness Expert'] as $cat)
                  <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="form-label">Experience</label>
              <input type="text" name="experience" value="{{ old('experience') }}" placeholder="e.g. 5 years" class="form-input">
            </div>
            <div>
              <label class="form-label">City</label>
              <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Mumbai" class="form-input">
            </div>
            <div>
                <label class="form-label">Initial Rating</label>
                <input type="number" name="rating" value="{{ old('rating', 0) }}" min="0" max="5" step="0.1" class="form-input">
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Bio</label>
              <textarea name="bio" rows="4" placeholder="Brief professional bio…" class="form-input resize-none">{{ old('bio') }}</textarea>
            </div>
          </div>
        </div>

        <!-- Sidebar / Settings -->
        <div class="space-y-6">
          <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-5">Profile & Settings</h3>
          <div>
            <label class="form-label">Profile Photo</label>
            <div class="flex flex-col items-center gap-4">
              <div class="relative">
                <img id="img-preview" src="https://ui-avatars.com/api/?name=New+Pro&background=f3f4f6&color=6b7280&size=128" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm" alt="Preview">
              </div>
              <label class="w-full flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-all">
                <i data-lucide="upload" class="w-5 h-5 text-gray-300 mb-1"></i>
                <p class="text-sm text-gray-400">Upload photo</p>
                <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewImage(this)">
              </label>
            </div>
          </div>

          <div class="bg-gray-50 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-gray-900">Active Account</p>
              <label class="toggle-switch">
                <input type="checkbox" name="status" checked>
                <span class="toggle-slider"></span>
              </label>
            </div>
            <p class="text-xs text-gray-400">Allow this professional to receive bookings</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 px-8 py-5 bg-gray-50/50">
      <a href="{{ route('professionals.index') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Add Professional
      </button>
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
      document.getElementById('img-preview').src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endpush

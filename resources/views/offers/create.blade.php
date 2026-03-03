@extends('layouts.app')
@section('content')
  <div class="flex flex-col gap-6">
    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>@endif
    <div class="flex items-center gap-4">
      <a href="{{ route('offers.index') }}"
        class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm"><i
          data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i></a>
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Offer</h2>
      </div>
    </div>
    <form method="POST" action="{{ route('offers.store') }}" enctype="multipart/form-data">@csrf
      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-8 pt-7 pb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="space-y-5">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name <span
                  class="text-red-400">*</span></label><input type="text" name="name" value="{{ old('name') }}"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all"
                required></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label id="discount-value-label" class="block text-sm font-medium text-gray-700 mb-1">Discount
                  Percentage (%)</label><input type="number" name="discount_value" value="{{ old('discount_value', 0) }}"
                  min="0"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all"
                  placeholder="e.g. 10"></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="discount_type" id="discount_type"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all bg-white">
                  <option value="percentage">Percentage (%)</option>
                  <option value="fixed">Fixed (₹)</option>
                </select>
              </div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Promo Code</label><input type="text"
                name="code" value="{{ old('code') }}"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all font-mono"
                placeholder="e.g. BEAUTY20"></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Valid From</label><input type="date"
                  name="valid_from" value="{{ old('valid_from') }}"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all">
              </div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label><input type="date"
                  name="valid_until" value="{{ old('valid_until') }}"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all">
              </div>
            </div>
          </div>
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
              <input type="file" name="image" id="offer-image" accept="image/*"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all"
                onchange="previewImage(this, 'offer-img-preview')">
              <img id="offer-img-preview" src="" alt="Preview" class="hidden mt-3 h-32 rounded-xl object-cover border border-gray-100">
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea
                name="description" rows="4"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black outline-none transition-all resize-none">{{ old('description') }}</textarea>
            </div>
            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="status" checked
                class="w-4 h-4 accent-black"><span class="text-sm text-gray-700">Active</span></label>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <a href="{{ route('offers.index') }}"
          class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-medium">Cancel</a>
        <button type="submit"
          class="px-6 py-3 rounded-xl bg-black text-white font-medium hover:bg-gray-800 transition-all shadow-lg shadow-black/10">Create Offer</button>
      </div>
    </form>
  </div>
  @push('scripts')
    <script>
      document.getElementById('discount_type').addEventListener('change', function () {
        const label = document.getElementById('discount-value-label');
        const input = document.querySelector('input[name="discount_value"]');
        if (this.value === 'percentage') {
          label.innerText = 'Discount Percentage (%)';
          input.placeholder = 'e.g. 10';
        } else {
          label.innerText = 'Discount Amount (₹)';
          input.placeholder = 'e.g. 500';
        }
      });
    </script>
  @endpush
@endsection
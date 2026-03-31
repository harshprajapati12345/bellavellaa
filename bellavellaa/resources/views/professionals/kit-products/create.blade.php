@extends('layouts.app')
@php $pageTitle = 'Add Kit Product'; @endphp

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('kit-products.index') }}"
        class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
      </a>
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Kit Product</h2>
        <p class="text-sm text-gray-400 mt-0.5">Create a new product for salon kits</p>
      </div>
    </div>

    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm shadow-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li><i data-lucide="alert-circle"
        class="w-4 h-4 inline mr-1"></i> {{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('kit-products.store') }}" enctype="multipart/form-data"
      class="bg-white rounded-[2.5rem] shadow-[0_4px_24px_rgba(0,0,0,0.03)] overflow-hidden">
      @csrf
      <div class="p-8 lg:p-10 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <!-- Left Column -->
          <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> General Information
            </h3>
            <div>
              <label class="form-label font-semibold">SKU / Code *</label>
              <input type="text" name="sku" value="{{ old('sku') }}" placeholder="e.g. KIT-SMPO-01" class="form-input" required>
            </div>
            <div>
              <label class="form-label font-semibold">Product Name *</label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Professional Shampoo" class="form-input" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label font-semibold">Brand</label>
                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="e.g. L'Oreal" class="form-input">
              </div>
            <div>
              <label class="form-label font-semibold">Category *</label>
              <select name="category_id" class="form-input" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            </div>
            <div>
              <label class="form-label font-semibold">Description</label>
              <textarea name="description" placeholder="Describe the kit contents and benefits..." class="form-input h-32 py-3">{{ old('description') }}</textarea>
            </div>
            <div>
              <label class="form-label font-semibold">Kit Image</label>
              <input type="file" name="image" class="form-input py-2">
              <p class="text-[10px] text-gray-400 mt-1">Recommended: 800x800px, Max 2MB (JPG, PNG, WebP)</p>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Inventory & Pricing
            </h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label font-semibold">Price (₹)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" class="form-input">
              </div>
              <div>
                <label class="form-label font-semibold">Unit</label>
                <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" placeholder="e.g. 500ml, pcs" class="form-input">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label font-semibold">Total Stock *</label>
                <input type="number" name="total_stock" value="{{ old('total_stock', 0) }}" class="form-input" required>
              </div>
              <div>
                <label class="form-label font-semibold">Min Stock Level</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" class="form-input">
              </div>
            </div>
            <div>
              <label class="form-label font-semibold">Expiry Date</label>
              <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input">
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
              <div>
                <p class="text-sm font-semibold text-gray-900">Active Status</p>
                <p class="text-xs text-gray-400 mt-0.5">Available for kit assignments</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="status" value="Active" {{ old('status', 'Active') === 'Active' ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 px-10 py-6 bg-[#F9F9F9]/50">
        <a href="{{ route('kit-products.index') }}" class="btn btn-secondary">Discard Changes</a>
        <button type="submit" class="btn btn-primary lg:px-10 shadow-lg shadow-black/10">
          <i data-lucide="check" class="w-4 h-4"></i> Create Product
        </button>
      </div>
    </form>
  </div>
@endsection

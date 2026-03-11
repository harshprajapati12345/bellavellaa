@extends('layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('services.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white shadow-sm hover:bg-gray-50">
        <i data-lucide="arrow-left" class="h-4 w-4 text-gray-600"></i>
      </a>
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Add Service</h2>
        <p class="text-sm text-gray-400 mt-0.5">Create a level 4 service under a level 3 service type.</p>
      </div>
    </div>

    @if($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-sm text-red-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1.3fr_.7fr]">
      @csrf

      <div class="space-y-6">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Hierarchy</h3>
          <p class="mt-1 text-sm text-gray-400">Select the service type first. Category and group are inferred automatically.</p>

          <div class="mt-6 grid gap-5">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Service Type <span class="text-red-400">*</span></label>
              <select name="service_type_id" id="serviceTypeSelect" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none" required>
                <option value="">Select service type</option>
                @foreach($serviceTypes as $type)
                  <option value="{{ $type->id }}" data-group="{{ $type->serviceGroup?->name }}" data-category="{{ $type->serviceGroup?->category?->name }}" @selected((string) old('service_type_id') === (string) $type->id)>
                    {{ $type->name }} @if($type->serviceGroup?->name) - {{ $type->serviceGroup->name }} @endif
                  </option>
                @endforeach
              </select>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
              <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4">
                <div class="text-xs font-semibold uppercase tracking-widest text-gray-400">Category</div>
                <div id="selectedCategory" class="mt-2 text-sm font-semibold text-gray-900">Select a service type</div>
              </div>
              <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-4">
                <div class="text-xs font-semibold uppercase tracking-widest text-gray-400">Service Group</div>
                <div id="selectedGroup" class="mt-2 text-sm font-semibold text-gray-900">Select a service type</div>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Service Details</h3>
          <div class="mt-6 grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700">Service Name <span class="text-red-400">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none" required>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Duration Minutes</label>
              <input type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Sort Order</label>
              <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div id="priceFieldWrap">
              <label class="mb-2 block text-sm font-medium text-gray-700">Base Price <span class="text-red-400">*</span></label>
              <input type="number" name="base_price" min="0" step="0.01" value="{{ old('base_price') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div id="salePriceFieldWrap">
              <label class="mb-2 block text-sm font-medium text-gray-700">Sale Price</label>
              <input type="number" name="sale_price" min="0" step="0.01" value="{{ old('sale_price') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div class="md:col-span-2 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-5">
              <label class="flex items-start gap-3">
                <input type="checkbox" name="has_variants" id="hasVariantsToggle" value="1" @checked(old('has_variants')) class="mt-1 h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                <span>
                  <span class="block text-sm font-medium text-gray-900">This service has level 5 variants</span>
                  <span class="block text-xs text-gray-500">If enabled, this level 4 service normally stops being directly bookable and price moves to variants.</span>
                </span>
              </label>
              <label class="flex items-start gap-3">
                <input type="checkbox" name="allow_direct_booking_with_variants" value="1" @checked(old('allow_direct_booking_with_variants')) class="mt-1 h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                <span>
                  <span class="block text-sm font-medium text-gray-900">Allow direct booking even when variants exist</span>
                  <span class="block text-xs text-gray-500">Use this only if the level 4 service itself should also remain sellable.</span>
                </span>
              </label>
              <div id="variantNotice" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                Save this service first, then manage its level 5 variants from the edit screen.
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
              <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">{{ old('description') }}</textarea>
            </div>
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700">Description Title</label>
              <input type="text" name="desc_title" value="{{ old('desc_title') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Media</h3>
          <div class="mt-6">
            <label class="mb-2 block text-sm font-medium text-gray-700">Service Image</label>
            <input type="file" name="service_image" accept="image/*" class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-black file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-800">
          </div>
        </div>

        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Publish</h3>
          <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('services.index') }}" class="rounded-xl border border-gray-200 px-4 py-3 text-center text-sm text-gray-600">Cancel</a>
            <button type="submit" name="form_action" value="draft" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700">Save as Draft</button>
            <button type="submit" name="form_action" value="publish" class="rounded-xl bg-black px-4 py-3 text-sm font-medium text-white">Publish Service</button>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  function updateServiceTypeContext() {
    const select = document.getElementById('serviceTypeSelect');
    const option = select.options[select.selectedIndex];
    document.getElementById('selectedCategory').textContent = option?.dataset.category || 'Select a service type';
    document.getElementById('selectedGroup').textContent = option?.dataset.group || 'Select a service type';
  }

  function toggleVariantPricingState() {
    const hasVariants = document.getElementById('hasVariantsToggle').checked;
    document.getElementById('variantNotice').classList.toggle('hidden', !hasVariants);
    document.getElementById('salePriceFieldWrap').classList.toggle('opacity-60', hasVariants);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('serviceTypeSelect').addEventListener('change', updateServiceTypeContext);
    document.getElementById('hasVariantsToggle').addEventListener('change', toggleVariantPricingState);
    updateServiceTypeContext();
    toggleVariantPricingState();
  });
</script>
@endpush

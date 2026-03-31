@php
  $selectedCategoryId = old('category_id', $serviceType->serviceGroup->category_id ?? request('category_id'));
  $selectedGroupId = old('service_group_id', $serviceType->service_group_id ?? request('service_group_id'));
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="space-y-5">
    <div>
      <label class="form-label">Category</label>
      <select id="category-filter" class="form-input">
        <option value="">Select category</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="form-label">Service Group</label>
      <select name="service_group_id" id="service-group-select" class="form-input" required>
        <option value="">Select group</option>
        @foreach($groups as $group)
          <option value="{{ $group->id }}" data-category-id="{{ $group->category_id }}" {{ (string) $selectedGroupId === (string) $group->id ? 'selected' : '' }}>
            {{ $group->name }} ({{ $group->category?->name }})
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-input" value="{{ old('name', $serviceType->name ?? '') }}" required>
    </div>

    <div>
      <label class="form-label">Slug</label>
      <input type="text" name="slug" class="form-input" value="{{ old('slug', $serviceType->slug ?? '') }}" placeholder="Auto-generated if left empty">
    </div>

    <div>
      <label class="form-label">Description</label>
      <textarea name="description" rows="4" class="form-input">{{ old('description', $serviceType->description ?? '') }}</textarea>
    </div>
  </div>

  <div class="space-y-5">
    <div>
      <label class="form-label">Image</label>
      <input type="file" name="image" class="form-input" accept="image/*">
      @if(!empty($serviceType?->image))
        <img src="{{ Storage::disk('public')->url($serviceType->image) }}" alt="{{ $serviceType->name }}" class="mt-3 h-32 w-full rounded-2xl object-cover">
      @endif
    </div>

    <div>
      <label class="form-label">Sort Order</label>
      <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', $serviceType->sort_order ?? 0) }}" min="0">
    </div>

    <div>
      <label class="form-label">Status</label>
      <select name="status" class="form-input">
        <option value="Active" {{ old('status', $serviceType->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
        <option value="Inactive" {{ old('status', $serviceType->status ?? 'Active') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-800">
      Service types are navigation-only. Booking starts only at level 4 services or level 5 variants.
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const categorySelect = document.getElementById('category-filter');
    const groupSelect = document.getElementById('service-group-select');

    function filterGroups() {
      const selectedCategoryId = categorySelect.value;
      const options = Array.from(groupSelect.options);

      options.forEach((option, index) => {
        if (index === 0) {
          option.hidden = false;
          return;
        }

        option.hidden = selectedCategoryId !== '' && option.dataset.categoryId !== selectedCategoryId;
      });

      if (groupSelect.selectedOptions[0]?.hidden) {
        groupSelect.value = '';
      }
    }

    categorySelect?.addEventListener('change', filterGroups);
    filterGroups();
  })();
</script>
@endpush

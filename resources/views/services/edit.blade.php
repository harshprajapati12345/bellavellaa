@extends('layouts.app')

@section('content')
  @php
    $selectedType = old('service_type_id', $service->service_type_id);
  @endphp

  <div class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('services.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white shadow-sm hover:bg-gray-50">
        <i data-lucide="arrow-left" class="h-4 w-4 text-gray-600"></i>
      </a>
      <div class="flex-1">
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Service</h2>
        <p class="text-sm text-gray-400 mt-0.5">{{ $service->name }}</p>
      </div>
      <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $service->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $service->status }}</span>
    </div>

    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3.5 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3.5 text-sm text-red-700">
        <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1.3fr_.7fr]">
      @csrf
      @method('PUT')

      <div class="space-y-6">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Hierarchy</h3>
          <p class="mt-1 text-sm text-gray-400">This service now belongs to a level 3 service type. Category and group stay inferred.</p>

          <div class="mt-6 grid gap-5">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Service Type <span class="text-red-400">*</span></label>
              <select name="service_type_id" id="serviceTypeSelect" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none" required>
                <option value="">Select service type</option>
                @foreach($serviceTypes as $type)
                  <option value="{{ $type->id }}" data-group="{{ $type->serviceGroup?->name }}" data-category="{{ $type->serviceGroup?->category?->name }}" @selected((string) $selectedType === (string) $type->id)>
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
              <input type="text" name="name" value="{{ old('name', $service->name) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none" required>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Duration Minutes</label>
              <input type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes', $service->duration_minutes ?? $service->duration) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Sort Order</label>
              <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $service->sort_order ?? 0) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Base Price @if(!$service->has_variants)<span class="text-red-400">*</span>@endif</label>
              <input type="number" name="base_price" min="0" step="0.01" value="{{ old('base_price', $service->base_price ?? $service->price) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Sale Price</label>
              <input type="number" name="sale_price" min="0" step="0.01" value="{{ old('sale_price', $service->sale_price) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
            <div class="md:col-span-2 space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-5">
              <label class="flex items-start gap-3">
                <input type="checkbox" name="has_variants" id="hasVariantsToggle" value="1" @checked(old('has_variants', $service->has_variants)) class="mt-1 h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                <span>
                  <span class="block text-sm font-medium text-gray-900">This service has level 5 variants</span>
                  <span class="block text-xs text-gray-500">If enabled, booking should usually happen on variants instead of this service.</span>
                </span>
              </label>
              <label class="flex items-start gap-3">
                <input type="checkbox" name="allow_direct_booking_with_variants" value="1" @checked(old('allow_direct_booking_with_variants', $service->allow_direct_booking_with_variants)) class="mt-1 h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                <span>
                  <span class="block text-sm font-medium text-gray-900">Allow direct booking when variants exist</span>
                  <span class="block text-xs text-gray-500">Only enable if both the service and variants should be sellable.</span>
                </span>
              </label>
              <div id="variantNotice" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700 hidden">
                Use the variant list below to confirm the actual level 5 sellable items.
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
              <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">{{ old('description', $service->long_description ?? $service->description) }}</textarea>
            </div>
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700">Description Title</label>
              <input type="text" name="desc_title" value="{{ old('desc_title', $service->desc_title) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
            </div>
          </div>
        </div>

        <div id="variantsSection" class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Level 5 Variants</h3>
              <p class="mt-1 text-sm text-gray-400">These remain the sellable items when variant mode is enabled.</p>
            </div>
            <div class="flex items-center gap-3">
              <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $service->variants->count() }} variants</span>
              <button type="button" id="addVariantToggle" class="rounded-xl bg-black px-4 py-2 text-sm font-medium text-white">Add Variant</button>
            </div>
          </div>

          <div id="variantFeedback" class="mt-4 hidden rounded-xl border px-4 py-3 text-sm"></div>

          <div id="createVariantForm" class="mt-6 hidden rounded-2xl border border-gray-200 bg-gray-50 p-5">
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Variant Name <span class="text-red-400">*</span></label>
                <input type="text" data-variant-field="name" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Price <span class="text-red-400">*</span></label>
                <input type="number" data-variant-field="price" min="0" step="0.01" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Duration Minutes</label>
                <input type="number" data-variant-field="duration_minutes" min="0" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Image</label>
                <input type="file" data-variant-field="image" accept="image/*" class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-black file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-800">
              </div>
            </div>
            <div class="mt-4 flex items-center justify-end gap-3">
              <button type="button" id="cancelAddVariant" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700">Cancel</button>
              <button type="button" id="saveVariantButton" class="rounded-xl bg-black px-4 py-3 text-sm font-medium text-white">Save Variant</button>
            </div>
          </div>

          <div class="mt-6 overflow-hidden rounded-2xl border border-gray-100">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gray-50/70 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                  <th class="px-4 py-3">Name</th>
                  <th class="px-4 py-3">Price</th>
                  <th class="px-4 py-3">Duration</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                @forelse($service->variants as $variant)
                  <tr class="variant-row" data-variant-id="{{ $variant->id }}">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $variant->name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ number_format($variant->display_price ?? $variant->price, 2) }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $variant->duration_minutes ?? $service->duration_minutes ?? $service->duration }} min</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $variant->status === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">{{ $variant->status }}</span></td>
                    <td class="px-4 py-3">
                      <div class="flex justify-end gap-2">
                        <button type="button" class="edit-variant rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-700">Edit</button>
                        <button type="button" class="delete-variant rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-600">Delete</button>
                      </div>
                    </td>
                  </tr>
                  <tr class="variant-edit-row hidden bg-gray-50/60" data-variant-id="{{ $variant->id }}">
                    <td colspan="5" class="px-4 py-4">
                      <div class="editVariantForm grid gap-4 md:grid-cols-4" data-variant-id="{{ $variant->id }}">
                        <div>
                          <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Name</label>
                          <input type="text" data-variant-field="name" value="{{ $variant->name }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
                        </div>
                        <div>
                          <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Price</label>
                          <input type="number" data-variant-field="price" min="0" step="0.01" value="{{ $variant->price }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
                        </div>
                        <div>
                          <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Duration</label>
                          <input type="number" data-variant-field="duration_minutes" min="0" value="{{ $variant->duration_minutes }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
                        </div>
                        <div>
                          <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Status</label>
                          <select data-variant-field="status" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:border-black/40 focus:outline-none">
                            <option value="Active" @selected($variant->status === 'Active')>Active</option>
                            <option value="Inactive" @selected($variant->status === 'Inactive')>Inactive</option>
                          </select>
                        </div>
                        <div class="md:col-span-3">
                          <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Replace Image</label>
                          <input type="file" data-variant-field="image" accept="image/*" class="block w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-black file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-800">
                        </div>
                        <div class="flex items-end justify-end gap-3">
                          <button type="button" class="cancel-edit rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700">Cancel</button>
                          <button type="button" class="save-edit rounded-xl bg-black px-4 py-3 text-sm font-medium text-white">Update</button>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr id="noVariantsRow">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No variants yet. Add the first variant here.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">
          <h3 class="text-sm font-semibold uppercase tracking-widest text-gray-900">Media</h3>
          @if($service->image)
            <img src="{{ str_starts_with($service->image, 'http') ? $service->image : asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="mt-6 h-48 w-full rounded-2xl object-cover border border-gray-100">
          @endif
          <div class="mt-6">
            <label class="mb-2 block text-sm font-medium text-gray-700">Replace Image</label>
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
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  function updateServiceTypeContext() {
    const select = document.getElementById('serviceTypeSelect');
    const option = select.options[select.selectedIndex];
    document.getElementById('selectedCategory').textContent = option?.dataset.category || 'Select a service type';
    document.getElementById('selectedGroup').textContent = option?.dataset.group || 'Select a service type';
  }

  function toggleVariantPricingState() {
    const hasVariants = document.getElementById('hasVariantsToggle').checked;
    document.getElementById('variantNotice').classList.toggle('hidden', !hasVariants);
  }

  function showVariantFeedback(message, type = 'success') {
    const feedback = document.getElementById('variantFeedback');
    feedback.textContent = message;
    feedback.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'border-red-200', 'bg-red-50', 'text-red-700');
    if (type === 'error') {
      feedback.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
    } else {
      feedback.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
    }
  }

  function toggleCreateVariantForm(force) {
    document.getElementById('createVariantForm').classList.toggle('hidden', force === undefined ? !document.getElementById('createVariantForm').classList.contains('hidden') : !force);
  }

  async function submitVariantForm(url, formData, method = 'POST') {
    const response = await fetch(url, {
      method,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: formData,
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
      const message = payload.message || Object.values(payload.errors || {}).flat().join(' ') || 'Variant request failed.';
      throw new Error(message);
    }

    return payload;
  }

  function validateVariantFields(fields) {
    if (!fields.name || !fields.name.trim()) {
      throw new Error('Variant name is required.');
    }

    if (fields.price === '' || fields.price === null || fields.price === undefined) {
      throw new Error('Variant price is required.');
    }

    if (Number(fields.price) < 0) {
      throw new Error('Variant price must be 0 or more.');
    }

    if (fields.duration_minutes !== '' && Number(fields.duration_minutes) < 0) {
      throw new Error('Duration must be 0 or more.');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('serviceTypeSelect').addEventListener('change', updateServiceTypeContext);
    document.getElementById('hasVariantsToggle').addEventListener('change', toggleVariantPricingState);

    document.getElementById('addVariantToggle')?.addEventListener('click', () => toggleCreateVariantForm(true));
    document.getElementById('cancelAddVariant')?.addEventListener('click', () => {
      document.querySelectorAll('#createVariantForm [data-variant-field]').forEach((input) => {
        if (input.type === 'file' || input.type === 'text' || input.type === 'number') {
          input.value = '';
        }
      });
      toggleCreateVariantForm(false);
    });

    document.getElementById('saveVariantButton')?.addEventListener('click', async () => {
      try {
        const panel = document.getElementById('createVariantForm');
        const formData = new FormData();
        const fields = {};
        panel.querySelectorAll('[data-variant-field]').forEach((input) => {
          if (input.type === 'file') {
            if (input.files.length > 0) {
              formData.append(input.dataset.variantField, input.files[0]);
            }
            return;
          }
          const key = input.dataset.variantField;
          fields[key] = input.value;
          formData.append(key, input.value);
        });
        validateVariantFields(fields);
        await submitVariantForm(@json(route('services.variants.store', $service)), formData);
        showVariantFeedback('Variant created successfully. Refreshing list...');
        window.location.reload();
      } catch (error) {
        showVariantFeedback(error.message, 'error');
      }
    });

    document.querySelectorAll('.edit-variant').forEach((button) => {
      button.addEventListener('click', () => {
        const row = button.closest('[data-variant-id]');
        document.querySelector(`.variant-edit-row[data-variant-id="${row.dataset.variantId}"]`)?.classList.remove('hidden');
      });
    });

    document.querySelectorAll('.cancel-edit').forEach((button) => {
      button.addEventListener('click', () => {
        button.closest('.variant-edit-row').classList.add('hidden');
      });
    });

    document.querySelectorAll('.save-edit').forEach((button) => {
      button.addEventListener('click', async () => {
        try {
          const currentForm = button.closest('.editVariantForm');
          const formData = new FormData();
          const fields = {};
          currentForm.querySelectorAll('[data-variant-field]').forEach((field) => {
            if (field.type === 'file') {
              if (field.files.length > 0) {
                formData.append(field.dataset.variantField, field.files[0]);
              }
              return;
            }
            const key = field.dataset.variantField;
            fields[key] = field.value;
            formData.append(key, field.value);
          });
          validateVariantFields(fields);
          formData.append('_method', 'PATCH');
          await submitVariantForm(@json(url('service-variants')) + '/' + currentForm.dataset.variantId, formData, 'POST');
          showVariantFeedback('Variant updated successfully. Refreshing list...');
          window.location.reload();
        } catch (error) {
          showVariantFeedback(error.message, 'error');
        }
      });
    });

    document.querySelectorAll('.delete-variant').forEach((button) => {
      button.addEventListener('click', async () => {
        const row = button.closest('[data-variant-id]');
        if (!confirm('Delete this variant?')) {
          return;
        }

        try {
          const formData = new FormData();
          formData.append('_method', 'DELETE');
          await submitVariantForm(@json(url('service-variants')) + '/' + row.dataset.variantId, formData, 'POST');
          showVariantFeedback('Variant deleted successfully. Refreshing list...');
          window.location.reload();
        } catch (error) {
          showVariantFeedback(error.message, 'error');
        }
      });
    });

    updateServiceTypeContext();
    toggleVariantPricingState();
  });
</script>
@endpush

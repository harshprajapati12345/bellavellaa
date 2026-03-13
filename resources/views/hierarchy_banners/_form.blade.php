@php
    $banner = $hierarchyBanner ?? null;
    $selectedTargetType = old('target_type', $banner->target_type ?? 'service_group');
    $selectedTargetId = (string) old('target_id', $banner->target_id ?? '');
    $selectedMediaType = old('media_type', $banner->media_type ?? 'image');
@endphp

<div class="p-8 lg:p-10 border-b border-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Placement Mapping
            </h3>

            <div>
                <label class="form-label font-semibold">Placement *</label>
                <select name="placement_type" class="form-input" required>
                    <option value="page_header" {{ old('placement_type', $banner->placement_type ?? '') === 'page_header' ? 'selected' : '' }}>Page Header Banner</option>
                    <option value="promo_banner" {{ old('placement_type', $banner->placement_type ?? '') === 'promo_banner' ? 'selected' : '' }}>In-page Promo Banner</option>
                    <option value="popup_banner" {{ old('placement_type', $banner->placement_type ?? '') === 'popup_banner' ? 'selected' : '' }}>Popup Banner</option>
                </select>
            </div>

            <div>
                <label class="form-label font-semibold">Target Type *</label>
                <select name="target_type" id="target-type" class="form-input" required>
                    <option value="category" {{ $selectedTargetType === 'category' ? 'selected' : '' }}>Category</option>
                    <option value="service_group" {{ $selectedTargetType === 'service_group' ? 'selected' : '' }}>Service Group</option>
                    <option value="service_type" {{ $selectedTargetType === 'service_type' ? 'selected' : '' }}>Service Type</option>
                    <option value="service" {{ $selectedTargetType === 'service' ? 'selected' : '' }}>Service</option>
                    <option value="variant" {{ $selectedTargetType === 'variant' ? 'selected' : '' }}>Variant</option>
                </select>
            </div>

            <div>
                <label class="form-label font-semibold">Target Entity *</label>
                <select name="target_id" id="target-id" class="form-input" required>
                    <option value="">Select Target</option>
                    @foreach($categories as $item)
                        <option value="{{ $item->id }}" data-target-type="category" {{ $selectedTargetType === 'category' && $selectedTargetId === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                    @foreach($serviceGroups as $item)
                        <option value="{{ $item->id }}" data-target-type="service_group" {{ $selectedTargetType === 'service_group' && $selectedTargetId === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}{{ $item->category ? ' (' . $item->category->name . ')' : '' }}
                        </option>
                    @endforeach
                    @foreach($serviceTypes as $item)
                        <option value="{{ $item->id }}" data-target-type="service_type" {{ $selectedTargetType === 'service_type' && $selectedTargetId === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}{{ $item->serviceGroup ? ' (' . $item->serviceGroup->name . ')' : '' }}
                        </option>
                    @endforeach
                    @foreach($services as $item)
                        <option value="{{ $item->id }}" data-target-type="service" {{ $selectedTargetType === 'service' && $selectedTargetId === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}{{ $item->serviceType ? ' (' . $item->serviceType->name . ')' : '' }}
                        </option>
                    @endforeach
                    @foreach($variants as $item)
                        <option value="{{ $item->id }}" data-target-type="variant" {{ $selectedTargetType === 'variant' && $selectedTargetId === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }}{{ $item->service ? ' (' . $item->service->name . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label font-semibold">Title</label>
                <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="form-input" placeholder="Optional title">
            </div>

            <div>
                <label class="form-label font-semibold">Subtitle</label>
                <textarea name="subtitle" rows="3" class="form-input resize-none" placeholder="Optional subtitle">{{ old('subtitle', $banner->subtitle ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label font-semibold">Action Link</label>
                    <input type="text" name="action_link" value="{{ old('action_link', $banner->action_link ?? '') }}" class="form-input" placeholder="Optional URL or deeplink">
                </div>
                <div>
                    <label class="form-label font-semibold">Button Text</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}" class="form-input" placeholder="Optional CTA">
                </div>
            </div>

            <div>
                <label class="form-label font-semibold">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0" class="form-input">
            </div>
        </div>

        <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Status
            </h3>

            <div>
                <label class="form-label font-semibold">Media Type *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="media_type" value="image" {{ $selectedMediaType === 'image' ? 'checked' : '' }} class="w-4 h-4 accent-black" required>
                        <span class="text-sm font-medium text-gray-700">Image</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="media_type" value="video" {{ $selectedMediaType === 'video' ? 'checked' : '' }} class="w-4 h-4 accent-black" required>
                        <span class="text-sm font-medium text-gray-700">Video</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="form-label font-semibold">Media File {{ $banner ? '' : '*' }}</label>
                <input type="file" name="media_file" class="form-input" accept="image/*,video/*" {{ $banner ? '' : 'required' }}>
                @if($banner && $banner->media_path)
                    <p class="text-xs text-gray-400 mt-2">Current file: {{ $banner->media_path }}</p>
                @endif
            </div>

            <div id="thumbnail-field" class="{{ $selectedMediaType === 'video' ? '' : 'hidden' }}">
                <label class="form-label font-semibold">Video Thumbnail</label>
                <input type="file" name="thumbnail_file" class="form-input" accept="image/*">
                @if($banner && $banner->thumbnail_path)
                    <p class="text-xs text-gray-400 mt-2">Current thumbnail: {{ $banner->thumbnail_path }}</p>
                @endif
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Active Status</p>
                    <p class="text-xs text-gray-400 mt-0.5">Show this banner in the client flow</p>
                </div>
                <label class="toggle-switch">
                    <input type="hidden" name="status" value="Inactive">
                    <input type="checkbox" name="status" value="Active" {{ old('status', $banner->status ?? 'Active') === 'Active' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function syncTargetOptions() {
        const targetType = document.getElementById('target-type');
        const targetId = document.getElementById('target-id');
        const currentType = targetType.value;

        Array.from(targetId.options).forEach((option) => {
            if (!option.dataset.targetType) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.targetType !== currentType;
        });

        const selectedOption = targetId.options[targetId.selectedIndex];
        if (selectedOption && selectedOption.dataset.targetType && selectedOption.dataset.targetType !== currentType) {
            targetId.value = '';
        }
    }

    function syncMediaFields() {
        const mediaType = document.querySelector('input[name="media_type"]:checked')?.value || 'image';
        document.getElementById('thumbnail-field').classList.toggle('hidden', mediaType !== 'video');
    }
    document.getElementById('target-type').addEventListener('change', syncTargetOptions);
    document.querySelectorAll('input[name="media_type"]').forEach((input) => {
        input.addEventListener('change', syncMediaFields);
    });
    syncTargetOptions();
    syncMediaFields();
</script>
@endpush

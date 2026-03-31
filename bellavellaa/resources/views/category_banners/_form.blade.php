<div class="p-8 lg:p-10 border-b border-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Banner Details
            </h3>
            
            <div>
                <label class="form-label font-semibold">Category *</label>
                <select name="category_id" class="form-input" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $categoryBanner->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label font-semibold">Banner Type *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="radio" name="banner_type" value="slider" {{ old('banner_type', $categoryBanner->banner_type ?? 'slider') === 'slider' ? 'checked' : '' }}
                            class="w-4 h-4 accent-black cursor-pointer" required>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-black">Slider</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="radio" name="banner_type" value="promo" {{ old('banner_type', $categoryBanner->banner_type ?? '') === 'promo' ? 'checked' : '' }}
                            class="w-4 h-4 accent-black cursor-pointer">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-black">Promo</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="form-label font-semibold">Title</label>
                <input type="text" name="title" value="{{ old('title', $categoryBanner->title ?? '') }}" placeholder="Banner Title" class="form-input">
            </div>

            <div>
                <label class="form-label font-semibold">Subtitle</label>
                <textarea name="subtitle" rows="3" placeholder="Banner Subtitle" class="form-input resize-none">{{ old('subtitle', $categoryBanner->subtitle ?? '') }}</textarea>
            </div>

            <div>
                <label class="form-label font-semibold">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $categoryBanner->sort_order ?? 0) }}" min="0" class="form-input" placeholder="0">
                <p class="text-xs text-gray-400 mt-1">Lower number = shown first (for Sliders)</p>
            </div>
        </div>

        <div class="space-y-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span> Media & Status
            </h3>

            <div>
                <label class="form-label font-semibold">Banner Image {{ !isset($categoryBanner) ? '*' : '' }}</label>
                <div class="flex gap-4 items-start">
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:border-black/20 hover:bg-gray-50 transition-all flex-1 pb-2">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                            <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600">Click to upload</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                        <input type="file" name="image" class="hidden" onchange="previewImage(this)" accept="image/*" {{ !isset($categoryBanner) ? 'required' : '' }}>
                    </label>
                    <div id="preview-container" class="{{ isset($categoryBanner->image) ? '' : 'hidden' }} w-40 h-40 relative group">
                        <img id="img-preview" class="w-full h-full object-cover rounded-[2rem] border border-gray-100" 
                             src="{{ isset($categoryBanner->image) ? asset('storage/' . $categoryBanner->image) : '' }}" alt="">
                        <div class="absolute inset-0 bg-black/40 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-[#F9F9F9] rounded-[1.5rem] border border-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-900">Active Status</p>
                    <p class="text-xs text-gray-400 mt-0.5">Control visibility in the app</p>
                </div>
                <label class="toggle-switch">
                    <input type="hidden" name="status" value="Inactive">
                    <input type="checkbox" name="status" value="Active" {{ old('status', $categoryBanner->status ?? 'Active') === 'Active' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

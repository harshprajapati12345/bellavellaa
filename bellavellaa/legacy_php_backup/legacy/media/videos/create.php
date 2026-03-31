<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Video · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .upload-zone {
      border: 2px dashed #e5e7eb;
      transition: border-color 0.2s, background 0.2s;
    }
    .upload-zone:hover, .upload-zone.drag-active {
      border-color: #000;
      background: #fafafa;
    }
    .upload-zone.has-file {
      border-color: #8b5cf6;
      background: #faf5ff;
    }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; cursor: pointer; inset: 0;
      background: #e5e7eb; border-radius: 999px; transition: 0.25s;
    }
    .toggle-slider:before {
      content: ''; position: absolute;
      width: 16px; height: 16px; left: 3px; bottom: 3px;
      background: white; border-radius: 50%; transition: 0.25s;
      box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .reel-hint {
      background: linear-gradient(135deg, #faf5ff 0%, #f0e7ff 100%);
      border: 1px solid #e9d5ff;
    }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Add Video'; include '../../includes/header.php'; ?>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
      <a href="/bella/media/videos/" class="hover:text-gray-600 transition-colors">Videos</a>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <span class="text-gray-900 font-medium">Add Video</span>
    </nav>

    <!-- Portrait/Reel Hint Banner -->
    <div class="reel-hint rounded-2xl p-4 mb-6 flex items-center gap-4">
      <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <i data-lucide="smartphone" class="w-5 h-5 text-purple-600"></i>
      </div>
      <div>
        <p class="text-sm font-semibold text-purple-900">Portrait / Reel Format Recommended</p>
        <p class="text-xs text-purple-600 mt-0.5">Upload videos in <strong>9:16 portrait</strong> format for the best mobile experience — just like Instagram Reels or TikTok.</p>
      </div>
    </div>

    <form onsubmit="event.preventDefault(); handleSubmit();">

      <!-- Card 1: Video Details -->
      <div class="bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.03)] p-6 sm:p-8 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-6 flex items-center gap-2">
          <i data-lucide="info" class="w-4 h-4 text-gray-400"></i>
          Video Details
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <!-- Title -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Video Title <span class="text-red-400">*</span></label>
            <input type="text" required
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
              placeholder="e.g. Bridal Makeup Tutorial">
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-400">*</span></label>
            <select required
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
              <option value="">Select category...</option>
              <option>Makeup</option>
              <option>Hair</option>
              <option>Nails</option>
              <option>Skincare</option>
              <option>Wellness</option>
              <option>Promo</option>
            </select>
          </div>

          <!-- Target Page -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Target Page <span class="text-red-400">*</span></label>
            <select required
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
              <option value="">Select page...</option>
              <option>Home</option>
              <option>Services</option>
              <option>About Us</option>
              <option>Contact</option>
              <option>Careers</option>
            </select>
          </div>

          <!-- Display Order -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Display Order</label>
            <input type="number" min="0" value="1"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
              placeholder="1">
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <div class="flex items-center gap-3 mt-1">
              <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
              </label>
              <span class="text-sm text-gray-500">Published</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Video File & Thumbnail -->
      <div class="bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.03)] p-6 sm:p-8 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-2 flex items-center gap-2">
          <i data-lucide="upload-cloud" class="w-4 h-4 text-purple-500"></i>
          Upload Files
        </h2>
        <p class="text-xs text-gray-400 mb-6">Upload a portrait/reel video (9:16) and an optional thumbnail cover image.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Video File -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i data-lucide="video" class="w-4 h-4 text-purple-500"></i>
              Video File <span class="text-red-400">*</span>
            </label>
            <div class="upload-zone rounded-2xl p-8 text-center cursor-pointer h-[calc(100%-40px)]" id="video-dropzone" onclick="document.getElementById('video-input').click()">
              <div id="video-preview" class="hidden mb-4">
                <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto">
                  <i data-lucide="film" class="w-8 h-8 text-purple-500"></i>
                </div>
                <p id="video-name" class="text-sm font-semibold text-gray-700 mt-3"></p>
                <p id="video-size" class="text-xs text-gray-400 mt-1"></p>
              </div>
              <div id="video-placeholder">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                  <i data-lucide="video" class="w-6 h-6 text-purple-400"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700">Click to upload video</p>
                <p class="text-xs text-gray-400 mt-1.5">MP4, WebM, MOV — Max 50MB</p>
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                  <span class="text-[10px] text-purple-500 bg-purple-50 px-2.5 py-1 rounded-full">?? Portrait 9:16</span>
                  <span class="text-[10px] text-purple-500 bg-purple-50 px-2.5 py-1 rounded-full">?? Reel Format</span>
                  <span class="text-[10px] text-purple-500 bg-purple-50 px-2.5 py-1 rounded-full">??? Video Only</span>
                </div>
              </div>
              <input type="file" id="video-input" class="hidden" accept="video/mp4,video/webm,video/quicktime" onchange="previewVideo(this)">
            </div>
          </div>

          <!-- Thumbnail -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
              <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
              Thumbnail <span class="text-xs text-gray-400 font-normal">(Optional)</span>
            </label>
            <div class="upload-zone rounded-2xl p-8 text-center cursor-pointer h-[calc(100%-40px)]" onclick="document.getElementById('thumb-input').click()">
              <div id="thumb-preview" class="hidden mb-3">
                <img id="thumb-img" class="max-h-36 mx-auto rounded-xl shadow-sm" src="" alt="">
              </div>
              <div id="thumb-placeholder">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                  <i data-lucide="image-plus" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm font-semibold text-gray-600">Click to upload thumbnail</p>
                <p class="text-xs text-gray-400 mt-1.5">JPG, PNG — Max 5MB</p>
                <p class="text-[10px] text-gray-400 mt-3">If not provided, the first frame will be used as cover.</p>
              </div>
              <input type="file" id="thumb-input" class="hidden" accept="image/jpeg,image/png,image/webp" onchange="previewThumb(this)">
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between">
        <a href="/bella/media/videos/" class="px-6 py-3 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-white transition-colors">Cancel</a>
        <button type="submit" class="px-8 py-3 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm flex items-center gap-2">
          <i data-lucide="upload" class="w-4 h-4"></i>
          Save Video
        </button>
      </div>
    </form>
  </main>
</div>

<script src="/bella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  function previewVideo(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (!file.type.startsWith('video/')) {
      Swal.fire({ title: 'Invalid File', text: 'Only video files are allowed.', icon: 'error', confirmButtonColor: '#000' });
      input.value = '';
      return;
    }
    document.getElementById('video-name').textContent = file.name;
    document.getElementById('video-size').textContent = (file.size / (1024*1024)).toFixed(1) + ' MB';
    document.getElementById('video-preview').classList.remove('hidden');
    document.getElementById('video-placeholder').classList.add('hidden');
    document.getElementById('video-dropzone').classList.add('has-file');
    lucide.createIcons();
  }

  function previewThumb(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('thumb-img').src = e.target.result;
      document.getElementById('thumb-preview').classList.remove('hidden');
      document.getElementById('thumb-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function handleSubmit() {
    Swal.fire({
      title: 'Video Saved!',
      text: 'Your video has been uploaded successfully.',
      icon: 'success',
      confirmButtonColor: '#000',
      confirmButtonText: 'Go to Videos'
    }).then(() => {
      window.location.href = '/bella/media/videos/';
    });
  }

  // Drag & drop for video
  const zone = document.getElementById('video-dropzone');
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-active'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-active'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-active');
    const input = document.getElementById('video-input');
    input.files = e.dataTransfer.files;
    previewVideo(input);
  });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Section · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; }
    .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-black-text, .sidebar-black-text span, .sidebar-black-text i,
    .sidebar-black-text a span, .sidebar-black-text button span { color: #000000 !important; }
    .sidebar-black-text [data-lucide] { color: #000000 !important; opacity: 0.8; transition: opacity 0.2s; }
    .sidebar-black-text a:hover [data-lucide], .sidebar-black-text button:hover [data-lucide] { opacity: 1; }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
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
  </style>
</head>
<body>
<?php
$pageTitle = 'Edit Section';
$section_data = [
  'id'           => 1,
  'name'         => 'Hero Banner',
  'key'          => 'hero',
  'content_type' => 'dynamic',
  'source'       => 'Gallery / Media',
  'title'        => 'Welcome to Bellavella',
  'subtitle'     => 'Premium Salon & Beauty Experience',
  'description'  => '<p>Discover our world-class beauty services crafted by expert professionals. From bridal makeup to relaxing spa treatments, we bring luxury to every visit.</p>',
  'btn_text'     => 'Explore Services',
  'btn_link'     => '/services',
  'status'       => true,
];
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="lg:ml-72 min-h-screen p-6 sm:p-10">
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
    <a href="/bella/homepage/" class="hover:text-gray-600 transition-colors">Home Page Manager</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-gray-900 font-medium">Edit — <?php echo $section_data['name']; ?></span>
  </nav>

  <form onsubmit="event.preventDefault(); window.location.href='/bella/homepage/';" class="max-w-4xl mx-auto">

    <!-- Card 1: Basic Info -->
    <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 sm:p-8 mb-6">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
          <i data-lucide="settings-2" class="w-4 h-4 text-gray-400"></i>
          Section Details
        </h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg text-xs font-medium text-gray-500">
          <i data-lucide="hash" class="w-3.5 h-3.5"></i>
          Section #<?php echo $section_data['id']; ?>
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Section Name <span class="text-red-400">*</span></label>
          <input type="text" required value="<?php echo $section_data['name']; ?>"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Section Key <span class="text-red-400">*</span></label>
          <input type="text" value="<?php echo $section_data['key']; ?>" readonly
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 focus:outline-none">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Content Type <span class="text-red-400">*</span></label>
          <select required id="content-type"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
            <option value="static" <?php echo $section_data['content_type'] === 'static' ? 'selected' : ''; ?>>Static Content</option>
            <option value="dynamic" <?php echo $section_data['content_type'] === 'dynamic' ? 'selected' : ''; ?>>Dynamic (Linked to Data)</option>
          </select>
        </div>
        <div id="dynamic-source-wrap" class="<?php echo $section_data['content_type'] !== 'dynamic' ? 'hidden' : ''; ?>">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Data Source</label>
          <select class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all bg-white">
            <option value="">Select source...</option>
            <option <?php echo $section_data['source'] === 'Services' ? 'selected' : ''; ?>>Services</option>
            <option <?php echo $section_data['source'] === 'Packages' ? 'selected' : ''; ?>>Packages</option>
            <option <?php echo $section_data['source'] === 'Reviews / Testimonials' ? 'selected' : ''; ?>>Reviews / Testimonials</option>
            <option <?php echo $section_data['source'] === 'Professionals' ? 'selected' : ''; ?>>Professionals</option>
            <option <?php echo $section_data['source'] === 'Gallery / Media' ? 'selected' : ''; ?>>Gallery / Media</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <div class="flex items-center gap-3 mt-1">
            <label class="toggle-switch">
              <input type="checkbox" <?php echo $section_data['status'] ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
            <span class="text-sm text-gray-500"><?php echo $section_data['status'] ? 'Active' : 'Inactive'; ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Content -->
    <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 sm:p-8 mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
        <i data-lucide="type" class="w-4 h-4 text-gray-400"></i>
        Section Content
      </h2>

      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Section Title</label>
        <input type="text" value="<?php echo $section_data['title']; ?>"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
      </div>
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Subtitle</label>
        <input type="text" value="<?php echo $section_data['subtitle']; ?>"
          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
      </div>
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden">
          <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="bold" class="w-4 h-4"></i></button>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="italic" class="w-4 h-4"></i></button>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="underline" class="w-4 h-4"></i></button>
            <div class="w-px h-5 bg-gray-200 mx-1"></div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="list" class="w-4 h-4"></i></button>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-black hover:bg-gray-200 transition-colors"><i data-lucide="link" class="w-4 h-4"></i></button>
          </div>
          <div contenteditable="true" class="min-h-[160px] px-4 py-4 text-sm text-gray-700 focus:outline-none leading-relaxed"><?php echo $section_data['description']; ?></div>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Button Text <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
          <input type="text" value="<?php echo $section_data['btn_text']; ?>"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Button Link <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
          <input type="text" value="<?php echo $section_data['btn_link']; ?>"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-3">
      <a href="/bella/homepage/" class="px-6 py-3 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-white transition-colors">Cancel</a>
      <button type="submit" class="px-8 py-3 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
        Update Section
      </button>
    </div>
  </form>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });
  document.getElementById('content-type').addEventListener('change', function() {
    document.getElementById('dynamic-source-wrap').classList.toggle('hidden', this.value !== 'dynamic');
  });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page Sections · Bellavella Admin</title>
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

    /* Drag & Drop Styles */
    .section-card {
      transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
      cursor: grab;
    }
    .section-card:active { cursor: grabbing; }
    .section-card.dragging {
      opacity: 0.5;
      transform: scale(0.98);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .section-card.drag-over {
      border-top: 3px solid #000;
    }
  </style>
</head>
<body>
<?php $pageTitle = 'Home Page Manager'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="lg:ml-72 min-h-screen p-6 sm:p-10">
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <!-- Top Bar -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
    <div>
      <p class="text-sm text-gray-400 mt-1">Drag sections to reorder. Changes save automatically.</p>
    </div>
    <a href="/bella/homepage/create.php" class="flex items-center gap-2 px-5 py-2.5 bg-black text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
      <i data-lucide="plus" class="w-4 h-4"></i>
      Add Section
    </a>
  </div>

  <!-- Sections List (Draggable) -->
  <div id="sections-list" class="space-y-3">
    <?php
    $sections = [
      ['id'=>1, 'name'=>'Hero Banner',           'key'=>'hero',           'type'=>'Dynamic', 'status'=>true,  'order'=>1, 'icon'=>'image'],
      ['id'=>2, 'name'=>'About Section',          'key'=>'about',          'type'=>'Static',  'status'=>true,  'order'=>2, 'icon'=>'info'],
      ['id'=>3, 'name'=>'Services Section',       'key'=>'services',       'type'=>'Dynamic', 'status'=>true,  'order'=>3, 'icon'=>'scissors'],
      ['id'=>4, 'name'=>'Packages Section',       'key'=>'packages',       'type'=>'Dynamic', 'status'=>true,  'order'=>4, 'icon'=>'shopping-bag'],
      ['id'=>5, 'name'=>'Professionals Section',  'key'=>'professionals',  'type'=>'Dynamic', 'status'=>true,  'order'=>5, 'icon'=>'users'],
      ['id'=>6, 'name'=>'Testimonials',           'key'=>'testimonials',   'type'=>'Dynamic', 'status'=>true,  'order'=>6, 'icon'=>'message-circle'],
      ['id'=>7, 'name'=>'Gallery',                'key'=>'gallery',        'type'=>'Dynamic', 'status'=>false, 'order'=>7, 'icon'=>'grid-3x3'],
      ['id'=>8, 'name'=>'Contact Section',        'key'=>'contact',        'type'=>'Static',  'status'=>true,  'order'=>8, 'icon'=>'phone'],
    ];
    foreach ($sections as $s):
    ?>
    <div class="section-card bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-5 flex items-center gap-4"
         draggable="true" data-id="<?php echo $s['id']; ?>">

      <!-- Drag Handle -->
      <div class="flex-shrink-0 text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing">
        <i data-lucide="grip-vertical" class="w-5 h-5"></i>
      </div>

      <!-- Order Badge -->
      <div class="w-8 h-8 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500">
        <?php echo $s['order']; ?>
      </div>

      <!-- Section Icon -->
      <div class="w-10 h-10 flex-shrink-0 bg-gray-50 rounded-xl flex items-center justify-center">
        <i data-lucide="<?php echo $s['icon']; ?>" class="w-5 h-5 text-gray-600"></i>
      </div>

      <!-- Section Info -->
      <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-gray-900"><?php echo $s['name']; ?></h3>
        <div class="flex items-center gap-3 mt-1">
          <span class="text-xs text-gray-400 font-mono"><?php echo $s['key']; ?></span>
          <span class="text-xs font-medium px-2 py-0.5 rounded-md <?php echo $s['type'] === 'Dynamic' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500'; ?>"><?php echo $s['type']; ?></span>
        </div>
      </div>

      <!-- Status Toggle -->
      <div class="flex-shrink-0">
        <label class="toggle-switch">
          <input type="checkbox" <?php echo $s['status'] ? 'checked' : ''; ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <!-- Edit Button -->
      <a href="/bella/homepage/edit.php?id=<?php echo $s['id']; ?>"
        class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-black hover:bg-gray-100 transition-colors" title="Edit">
        <i data-lucide="pencil" class="w-4 h-4"></i>
      </a>

      <!-- Delete Button -->
      <button onclick="confirmDelete('#', '<?php echo $s['name']; ?>')"
        class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
      </button>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Info Card -->
  <div class="mt-8 bg-white rounded-2xl shadow-[0_2px_20px_rgb(0,0,0,0.02)] p-6 flex items-start gap-4">
    <div class="w-10 h-10 flex-shrink-0 bg-blue-50 rounded-xl flex items-center justify-center">
      <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
    </div>
    <div>
      <h4 class="text-sm font-semibold text-gray-900 mb-1">How it works</h4>
      <ul class="text-sm text-gray-500 space-y-1">
        <li>- Drag sections to reorder how they appear on the homepage</li>
        <li>- Toggle visibility to show/hide sections without deleting</li>
        <li>- <strong>Dynamic</strong> sections auto-fetch data from Services, Packages, etc.</li>
        <li>- <strong>Static</strong> sections use the content you provide</li>
      </ul>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  // Drag & Drop
  const list = document.getElementById('sections-list');
  let draggedEl = null;

  list.querySelectorAll('.section-card').forEach(card => {
    card.addEventListener('dragstart', (e) => {
      draggedEl = card;
      card.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    });
    card.addEventListener('dragend', () => {
      card.classList.remove('dragging');
      list.querySelectorAll('.section-card').forEach(c => c.classList.remove('drag-over'));
      updateOrder();
    });
    card.addEventListener('dragover', (e) => {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      if (card !== draggedEl) {
        card.classList.add('drag-over');
      }
    });
    card.addEventListener('dragleave', () => {
      card.classList.remove('drag-over');
    });
    card.addEventListener('drop', (e) => {
      e.preventDefault();
      card.classList.remove('drag-over');
      if (card !== draggedEl) {
        const cards = [...list.children];
        const fromIdx = cards.indexOf(draggedEl);
        const toIdx = cards.indexOf(card);
        if (fromIdx < toIdx) {
          card.after(draggedEl);
        } else {
          card.before(draggedEl);
        }
      }
    });
  });

  function updateOrder() {
    const cards = list.querySelectorAll('.section-card');
    cards.forEach((card, i) => {
      const badge = card.querySelector('.w-8.h-8');
      if (badge) badge.textContent = i + 1;
    });
    // Toast notification
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Order updated',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      });
    }
  }
</script>
</body>
</html>

<?php
$section    = 'professionals';
$subSection = 'kit-products';
$pageTitle  = 'Add Kit Product';

$statuses    = ['Active' => 'Active', 'Inactive' => 'Inactive'];

$saved = ($_SERVER['REQUEST_METHOD'] === 'POST');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Add Product · Bellavella Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
  ::-webkit-scrollbar { width: 0; }
  .section-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f0f0f0; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
  .field-label { display: block; font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 8px; }
  .field-input { width: 100%; padding: 11px 15px; border-radius: 10px; border: 1px solid #e5e7eb; font-size: 14px; color: #111; outline: none; transition: border .15s, box-shadow .15s; background: #fff; }
  .field-input:focus { border-color: #a3a3a3; box-shadow: 0 0 0 3px rgba(0,0,0,.04); }
  .req { color: #f87171; }
  .section-num { width: 28px; height: 28px; background: #111; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0; }
  .cat-pill input { position: absolute; opacity: 0; }
  .cat-pill:has(input:checked) .pill-inner { background: #111; color: #fff; border-color: #111; }
  .pill-inner { border: 1.5px solid #e5e7eb; border-radius: 999px; padding: 6px 14px; font-size: 12px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all .15s; }
  .pill-inner:hover { border-color: #9ca3af; color: #111; }
</style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72">
    <?php include '../../includes/header.php'; ?>

    <!-- Page Header -->
    <div class="px-6 lg:px-10 pt-6 pb-4 border-b border-gray-100 bg-white sticky top-0 z-40">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <a href="index.php" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
          </a>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Add Kit Product</h1>
            <p class="text-xs text-gray-400 mt-0.5">Create a new inventory product for professional use</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <a href="index.php" class="px-5 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Cancel</a>
          <button onclick="submitForm()" class="px-6 py-2 text-sm font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i> Save Product
          </button>
        </div>
      </div>
    </div>

    <?php if($saved): ?>
    <div class="mx-6 lg:mx-10 mt-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3">
      <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
      <p class="text-sm font-semibold text-emerald-700">Product created! <a href="index.php" class="underline">View inventory →</a></p>
    </div>
    <?php endif; ?>

    <form id="product-form" method="POST" action="create.php" class="px-6 lg:px-10 py-8 space-y-6">

      <!-- Section 1: Product Details -->
      <div class="section-card p-7">
        <div class="flex items-center gap-3 mb-6">
          <div class="section-num">1</div>
          <div>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Product Details</h2>
            <p class="text-xs text-gray-400 mt-0.5">Basic product identity and classification</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

          <div class="lg:col-span-2">
            <label class="field-label">Product Name <span class="req">*</span></label>
            <input type="text" name="name" id="f-name" required placeholder="e.g. HD Pro Foundation" class="field-input" oninput="updatePreview()">
          </div>

          <div>
            <label class="field-label">Brand <span class="req">*</span></label>
            <input type="text" name="brand" id="f-brand" required placeholder="e.g. MAC Cosmetics" class="field-input" oninput="updatePreview()">
          </div>

          <div>
             <label class="field-label">SKU <span class="req">*</span></label>
             <input type="text" name="sku" id="f-sku" required placeholder="e.g. KP-1024" class="field-input">
             <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Must be unique and non-repeatable</p>
           </div>

        </div>
      </div>

      <!-- Section 2: Pricing & Stock -->
      <div class="section-card p-7">
        <div class="flex items-center gap-3 mb-6">
          <div class="section-num">2</div>
          <div>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Pricing & Stock</h2>
            <p class="text-xs text-gray-400 mt-0.5">Set price, initial inventory, and alert thresholds</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">

          <div>
            <label class="field-label">Price per Unit <span class="req">*</span></label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none">₹</span>
              <input type="number" name="price" id="f-price" required min="0" placeholder="0.00" class="field-input pl-8" oninput="updatePreview()">
            </div>
          </div>

          <div>
            <label class="field-label">Initial Stock <span class="req">*</span></label>
            <input type="number" name="initial_stock" id="f-stock" required min="0" placeholder="0" class="field-input" oninput="updatePreview()">
          </div>

          <div>
            <label class="field-label">Min Stock Alert</label>
            <input type="number" name="min_stock" id="f-min" min="0" placeholder="e.g. 20" class="field-input" oninput="updatePreview()">
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Trigger low-stock warning</p>
          </div>

          <div>
            <label class="field-label">Inventory Value</label>
            <div class="field-input bg-gray-50 flex items-center gap-1.5">
              <span class="text-gray-400 text-sm">₹</span>
              <span id="inv-value" class="text-sm font-bold text-gray-900">0</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Price × Initial Stock</p>
          </div>

        </div>

        <!-- Stock Preview Bar -->
        <div class="mt-6 bg-gray-50 rounded-2xl p-5">
          <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Status Preview</p>
            <span id="stock-badge" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gray-100 text-gray-400">No Stock Set</span>
          </div>
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-xl p-3 text-center border border-gray-100">
              <p class="text-[10px] text-gray-400 font-semibold uppercase mb-1">Total</p>
              <p id="prev-total" class="text-xl font-bold text-gray-900">—</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-100">
              <p class="text-[10px] text-amber-400 font-semibold uppercase mb-1">Assigned</p>
              <p class="text-xl font-bold text-amber-400">0</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-100">
              <p class="text-[10px] text-emerald-500 font-semibold uppercase mb-1">Available</p>
              <p id="prev-avail" class="text-xl font-bold text-emerald-600">—</p>
            </div>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
            <div id="stock-bar" class="h-full rounded-full bg-emerald-400 transition-all duration-500" style="width:0%"></div>
          </div>
          <p id="stock-hint" class="text-[10px] text-gray-400 mt-1.5">Set initial stock and min alert to see status</p>
        </div>
      </div>

      <!-- Section 3: Status & Settings -->
      <div class="section-card p-7">
        <div class="flex items-center gap-3 mb-6">
          <div class="section-num">3</div>
          <div>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Status & Settings</h2>
            <p class="text-xs text-gray-400 mt-0.5">Visibility and availability configuration</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="field-label">Product Status</label>
            <div class="grid grid-cols-2 gap-3 mt-1">
              <?php
              $statusConfig = [
                'Active'   => ['icon'=>'check-circle','col'=>'emerald','desc'=>'Available for assignment'],
                'Inactive' => ['icon'=>'pause-circle','col'=>'gray',   'desc'=>'Temporarily disabled'],
              ];
              foreach($statusConfig as $st => $cfg): ?>
              <label class="flex items-center gap-3 border border-gray-200 rounded-2xl p-4 cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-black has-[:checked]:bg-gray-900 group">
                <input type="radio" name="status" value="<?php echo $st; ?>" class="sr-only" <?php echo $st==='Active'?'checked':''; ?>>
                <i data-lucide="<?php echo $cfg['icon']; ?>" class="w-5 h-5 text-<?php echo $cfg['col']; ?>-500 group-has-[:checked]:text-white flex-shrink-0"></i>
                <div>
                  <p class="text-sm font-semibold text-gray-900 group-has-[:checked]:text-white"><?php echo $st; ?></p>
                  <p class="text-[11px] text-gray-400 group-has-[:checked]:text-gray-400"><?php echo $cfg['desc']; ?></p>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="bg-violet-50 rounded-2xl p-5 border border-violet-100">
            <div class="flex items-center gap-2 mb-3">
              <i data-lucide="zap" class="w-4 h-4 text-violet-600"></i>
              <h4 class="text-sm font-bold text-violet-900">What happens after save?</h4>
            </div>
            <ul class="space-y-1.5 text-xs text-violet-700">
               <li class="flex items-start gap-1.5"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i>Product becomes assignable to professionals</li>
               <li class="flex items-start gap-1.5"><i data-lucide="check" class="w-3 h-3 mt-0.5 flex-shrink-0"></i>Low-stock alerts activate at min threshold</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Bottom Actions -->
      <div class="flex items-center justify-between pt-2 pb-8">
        <a href="index.php" class="text-sm font-medium text-gray-400 hover:text-gray-700 transition-colors flex items-center gap-1.5">
          <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Products
        </a>
        <div class="flex items-center gap-3">
          <button type="reset" onclick="resetForm()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Clear Form</button>
          <button type="button" onclick="submitForm()" class="px-8 py-2.5 text-sm font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i> Save Product
          </button>
        </div>
      </div>

    </form>
  </main>
</div>

<script>
lucide.createIcons({ attrs: { 'stroke-width': 2 } });

function updatePreview() {
  const stock = parseInt(document.getElementById('f-stock').value) || 0;
  const min   = parseInt(document.getElementById('f-min').value)   || 0;
  const price = parseFloat(document.getElementById('f-price').value) || 0;

  document.getElementById('inv-value').textContent    = (stock * price).toLocaleString('en-IN', { maximumFractionDigits: 0 });
  document.getElementById('prev-total').textContent   = stock > 0 ? stock : '—';
  document.getElementById('prev-avail').textContent   = stock > 0 ? stock : '—';

  const bar   = document.getElementById('stock-bar');
  const badge = document.getElementById('stock-badge');
  const hint  = document.getElementById('stock-hint');

  if (stock <= 0) {
    bar.style.width = '0%';
    badge.textContent = 'No Stock'; badge.className = 'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gray-100 text-gray-400';
    hint.textContent = 'Set initial stock and min alert to see status';
    return;
  }
  bar.style.width = '100%';
  if (stock === 0) {
    bar.style.background = '#f87171'; badge.textContent = 'Out of Stock'; badge.className = 'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-red-50 text-red-600';
  } else if (min > 0 && stock <= min) {
    bar.style.background = '#fbbf24'; badge.textContent = 'Low Stock'; badge.className = 'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-50 text-amber-600';
  } else {
    bar.style.background = '#34d399'; badge.textContent = 'In Stock'; badge.className = 'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700';
  }
  hint.textContent = `Total: ${stock} · Min Alert: ${min || 'not set'} · Value: ₹${(stock * price).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
}

function submitForm() {
  const name  = document.getElementById('f-name').value.trim();
  const brand = document.getElementById('f-brand').value.trim();
  const sku   = document.getElementById('f-sku').value.trim();
  const price = document.getElementById('f-price').value;
  const stock = document.getElementById('f-stock').value;
  if (!name || !brand || !sku || !price || !stock) {
    Swal.fire({ title: 'Missing Fields', text: 'Please fill all required fields marked with *', icon: 'warning', confirmButtonColor: '#000' });
    return;
  }
  document.getElementById('product-form').submit();
}

function resetForm() {
  updatePreview();
  lucide.createIcons({ attrs: { 'stroke-width': 2 } });
}
</script>
</body>
</html>

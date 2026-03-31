<?php
$section    = 'professionals';
$subSection = 'kit-orders';
$pageTitle  = 'New Kit Order';

$today     = date('Y-m-d');
$suppliers = ['MAC Cosmetics India', "L'Oreal Professional", 'Forest Essentials', 'Kama Ayurveda', 'Maybelline India', 'Nykaa Professional'];
$saved     = ($_SERVER['REQUEST_METHOD'] === 'POST');
$orderId   = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>New Order · Bellavella Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body { font-family:'Inter',sans-serif; background:#F6F6F6; }
  ::-webkit-scrollbar{width:0}
  .sc{background:#fff;border-radius:1.25rem;border:1px solid #f0f0f0;box-shadow:0 1px 3px rgba(0,0,0,.04)}
  .fl{display:block;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px}
  .fi{width:100%;padding:11px 15px;border-radius:10px;border:1px solid #e5e7eb;font-size:14px;color:#111;outline:none;transition:border .15s;background:#fff}
  .fi:focus{border-color:#a3a3a3;box-shadow:0 0 0 3px rgba(0,0,0,.04)}
  .req{color:#f87171}
  .sn{width:28px;height:28px;background:#111;color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0}
</style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
<?php include '../../includes/sidebar.php'; ?>
<main class="flex-1 lg:ml-72">
<?php include '../../includes/header.php'; ?>

<div class="px-6 lg:px-10 pt-6 pb-4 border-b border-gray-100 bg-white sticky top-0 z-40">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="index.php" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4 text-gray-600"></i>
      </a>
      <div>
        <h1 class="text-lg font-bold text-gray-900">New Supplier Order</h1>
        <p class="text-xs text-gray-400 mt-0.5">Auto ID: <span class="font-mono font-semibold"><?php echo $orderId; ?></span></p>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <a href="index.php" class="px-5 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Cancel</a>
      <button onclick="submitOrder()" class="px-6 py-2 text-sm font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2">
        <i data-lucide="send" class="w-4 h-4"></i> Place Order
      </button>
    </div>
  </div>
</div>

<?php if($saved): ?>
<div class="mx-6 lg:mx-10 mt-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3">
  <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
  <p class="text-sm font-semibold text-emerald-700">Order placed! <a href="index.php" class="underline">View orders →</a></p>
</div>
<?php endif; ?>

<form id="order-form" method="POST" action="create.php" class="px-6 lg:px-10 py-8 space-y-6">

  <!-- 1 Supplier -->
  <div class="sc p-7">
    <div class="flex items-center gap-3 mb-6">
      <div class="sn">1</div>
      <div><h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Supplier & Dates</h2><p class="text-xs text-gray-400 mt-0.5">Who is supplying and delivery timeline</p></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
      <div class="md:col-span-2">
        <label class="fl">Supplier <span class="req">*</span></label>
        <div class="relative">
          <select name="supplier" id="f-supplier" required class="fi pr-10 appearance-none cursor-pointer">
            <option value="">Select Supplier</option>
            <?php foreach($suppliers as $s): ?><option><?php echo $s; ?></option><?php endforeach; ?>
          </select>
          <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>
      </div>
      <div>
        <label class="fl">Order Date <span class="req">*</span></label>
        <input type="date" name="order_date" id="f-order-date" value="<?php echo $today; ?>" required class="fi">
      </div>
      <div>
        <label class="fl">Expected Delivery</label>
        <input type="date" name="delivery_date" class="fi">
      </div>
      <div>
        <label class="fl">Payment Status</label>
        <div class="relative">
          <select name="payment_status" class="fi pr-10 appearance-none cursor-pointer">
            <option value="Unpaid">Unpaid</option>
            <option value="Partial">Partial</option>
            <option value="Paid">Paid</option>
          </select>
          <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>
      </div>
      <div class="md:col-span-3">
        <label class="fl">Order Notes</label>
        <input type="text" name="notes" placeholder="Optional notes…" class="fi">
      </div>
    </div>
  </div>

  <!-- 2 Products -->
  <div class="sc p-7">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="sn">2</div>
        <div><h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Products <span class="req">*</span></h2><p class="text-xs text-gray-400 mt-0.5">Add products and quantities for this order</p></div>
      </div>
      <button type="button" onclick="addRow()" class="flex items-center gap-1.5 text-sm font-semibold text-black bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i> Add Product
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left min-w-[600px]">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="pb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest pr-4 w-[38%]">Product Name</th>
            <th class="pb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest pr-4 w-[12%]">Qty</th>
            <th class="pb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest pr-4 w-[12%]">Unit</th>
            <th class="pb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest pr-4 w-[18%]">Price / Unit</th>
            <th class="pb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest pr-4 w-[14%]">Amount</th>
            <th class="pb-3 w-[6%]"></th>
          </tr>
        </thead>
        <tbody id="products-tbody"></tbody>
      </table>
      <div id="no-products" class="py-10 text-center text-sm text-gray-400 border-2 border-dashed border-gray-100 rounded-2xl mt-2">
        <i data-lucide="package" class="w-8 h-8 text-gray-200 mx-auto mb-2"></i>
        No products added. Click "+ Add Product" to start.
      </div>
    </div>
    <div class="mt-6 pt-5 border-t border-gray-100">
      <div class="max-w-sm ml-auto space-y-2">
        <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span id="t-subtotal" class="font-semibold text-gray-900">₹0</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">GST (18%)</span><span id="t-gst" class="font-semibold text-gray-900">₹0</span></div>
        <div class="flex justify-between font-bold text-base pt-3 border-t border-gray-200">
          <span>Total Amount</span><span id="t-total">₹0</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 3 Delivery Logic Info -->
  <div class="sc p-7">
    <div class="flex items-center gap-3 mb-5">
      <div class="sn">3</div>
      <div><h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">On Delivery</h2><p class="text-xs text-gray-400 mt-0.5">Automatic actions when order is marked Delivered</p></div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="flex items-start gap-3 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
        <div><p class="text-xs font-bold text-emerald-900">Stock Increased</p><p class="text-[11px] text-emerald-700 mt-0.5">Ordered qty added to total stock</p></div>
      </div>
      <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-2xl border border-blue-100">
        <i data-lucide="box" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
        <div><p class="text-xs font-bold text-blue-900">Availability Updated</p><p class="text-[11px] text-blue-700 mt-0.5">Available stock reflects new inventory</p></div>
      </div>
      <div class="flex items-start gap-3 p-4 bg-violet-50 rounded-2xl border border-violet-100">
        <i data-lucide="file-text" class="w-5 h-5 text-violet-600 flex-shrink-0 mt-0.5"></i>
        <div><p class="text-xs font-bold text-violet-900">Stock Log Created</p><p class="text-[11px] text-violet-700 mt-0.5">Auto entry with order ID & quantities</p></div>
      </div>
      <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-2xl border border-amber-100">
        <i data-lucide="package-check" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
        <div><p class="text-xs font-bold text-amber-900">Order Completed</p><p class="text-[11px] text-amber-700 mt-0.5">Status moves to Delivered automatically</p></div>
      </div>
    </div>
  </div>

  <!-- Bottom Actions -->
  <div class="flex items-center justify-between pt-2 pb-8">
    <a href="index.php" class="text-sm font-medium text-gray-400 hover:text-gray-700 transition-colors flex items-center gap-1.5">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Orders
    </a>
    <div class="flex items-center gap-3">
      <button type="reset" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Clear</button>
      <button type="button" onclick="submitOrder()" class="px-8 py-2.5 text-sm font-semibold text-white bg-black rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2">
        <i data-lucide="send" class="w-4 h-4"></i> Place Order
      </button>
    </div>
  </div>
</form>
</main>
</div>

<script>
lucide.createIcons({attrs:{'stroke-width':2}});
let rowCount = 0;

function addRow() {
  rowCount++;
  document.getElementById('no-products').classList.add('hidden');
  const tr = document.createElement('tr');
  tr.id = `row-${rowCount}`;
  tr.className = 'border-b border-gray-50';
  tr.innerHTML = `
    <td class="py-2 pr-3"><input type="text" name="p[${rowCount}][name]" placeholder="Product name" oninput="calcTotals()" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-gray-400 text-sm"></td>
    <td class="py-2 pr-3"><input type="number" name="p[${rowCount}][qty]" min="1" value="1" oninput="calcTotals()" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none text-sm text-center"></td>
    <td class="py-2 pr-3"><select name="p[${rowCount}][unit]" class="w-full px-2 py-2.5 rounded-xl border border-gray-200 text-sm cursor-pointer"><option>ml</option><option>pcs</option><option>gm</option><option>ltr</option><option>kg</option></select></td>
    <td class="py-2 pr-3"><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">₹</span><input type="number" name="p[${rowCount}][price]" min="0" value="0" oninput="calcTotals()" class="w-full pl-7 pr-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none text-sm"></div></td>
    <td class="py-2 pr-3"><span id="amt-${rowCount}" class="text-sm font-semibold text-gray-900">₹0</span></td>
    <td class="py-2"><button type="button" onclick="removeRow(${rowCount})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 flex items-center justify-center transition-all"><i data-lucide="x" class="w-3.5 h-3.5 text-red-500"></i></button></td>`;
  document.getElementById('products-tbody').appendChild(tr);
  lucide.createIcons({attrs:{'stroke-width':2}});
  calcTotals();
}

function removeRow(id) {
  const el = document.getElementById(`row-${id}`);
  if (el) el.remove();
  calcTotals();
  if (!document.querySelector('#products-tbody tr')) document.getElementById('no-products').classList.remove('hidden');
}

function calcTotals() {
  const rows = document.querySelectorAll('#products-tbody tr');
  let sub = 0;
  rows.forEach(row => {
    const qty   = parseFloat(row.querySelector('[name*="[qty]"]')?.value || 0);
    const price = parseFloat(row.querySelector('[name*="[price]"]')?.value || 0);
    const amt   = qty * price; sub += amt;
    const amtEl = row.querySelector('[id^="amt-"]');
    if (amtEl) amtEl.textContent = '₹' + Math.round(amt).toLocaleString('en-IN');
  });
  const gst = sub * 0.18;
  document.getElementById('t-subtotal').textContent = '₹' + Math.round(sub).toLocaleString('en-IN');
  document.getElementById('t-gst').textContent      = '₹' + Math.round(gst).toLocaleString('en-IN');
  document.getElementById('t-total').textContent    = '₹' + Math.round(sub + gst).toLocaleString('en-IN');
}

function submitOrder() {
  const supplier = document.getElementById('f-supplier').value;
  const rows     = document.querySelectorAll('#products-tbody tr');
  const hasItems = Array.from(rows).some(r => r.querySelector('[name*="[name]"]')?.value.trim());
  if (!supplier) { Swal.fire({title:'Missing Supplier',text:'Please select a supplier.',icon:'warning',confirmButtonColor:'#000'}); return; }
  if (!hasItems)  { Swal.fire({title:'No Products', text:'Add at least one product.',icon:'warning',confirmButtonColor:'#000'}); return; }
  document.getElementById('order-form').submit();
}

addRow();
</script>
</body>
</html>

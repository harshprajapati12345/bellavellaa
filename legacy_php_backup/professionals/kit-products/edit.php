<?php
$section    = 'professionals';
$subSection = 'kit-products';
$pageTitle  = 'Edit Kit Product';

/* Mock: load product by ID */
$id = intval($_GET['id'] ?? 1);
$products_db = [
    1 => ['id'=>1,'name'=>'HD Pro Foundation','brand'=>'MAC','sku'=>'KP-0001','category'=>'Bridal','unit'=>'ml','price'=>1800,'total_stock'=>500,'assigned_stock'=>350,'min_stock'=>50,'expiry_date'=>'2025-06-30','status'=>'Active'],
    2 => ['id'=>2,'name'=>'Argan Oil Hair Mask','brand'=>"L'Oreal",'sku'=>'KP-0002','category'=>'Hair','unit'=>'ml','price'=>1200,'total_stock'=>1000,'assigned_stock'=>870,'min_stock'=>100,'expiry_date'=>'2024-12-31','status'=>'Active'],
    3 => ['id'=>3,'name'=>'Lavender Essential Oil','brand'=>'Forest Essentials','sku'=>'KP-0003','category'=>'Spa','unit'=>'ml','price'=>950,'total_stock'=>250,'assigned_stock'=>250,'min_stock'=>30,'expiry_date'=>'2024-08-15','status'=>'Inactive'],
];
$p = $products_db[$id] ?? $products_db[1];

$brands      = ['MAC',"L'Oreal",'Forest Essentials','Kama Ayurveda','Maybelline','Lakme','Nykaa Professional','Other'];

$available = $p['total_stock'] - $p['assigned_stock'];
$pct       = $p['total_stock'] > 0 ? round(($available / $p['total_stock']) * 100) : 0;
$saved     = ($_SERVER['REQUEST_METHOD'] === 'POST');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit Product · Bellavella Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{font-family:'Inter',sans-serif;background:#F6F6F6;}
::-webkit-scrollbar{width:0px;}
.card{background:#fff;border-radius:1.5rem;border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.04);}
.form-label{display:block;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;margin-left:2px;}
.form-input{width:100%;padding:12px 16px;border-radius:12px;border:1px solid #e5e7eb;font-size:14px;color:#111;outline:none;transition:all .15s;}
.form-input:focus{border-color:#9ca3af;box-shadow:0 0 0 3px rgba(0,0,0,.04);}
.form-input[readonly]{background:#f9fafb;color:#9ca3af;cursor:not-allowed;}
.required{color:#f87171;}
</style>
</head>
<body class="antialiased">
<div class="flex min-h-screen">
<?php include '../../includes/sidebar.php'; ?>
<main class="flex-1 lg:ml-72 p-4 lg:p-8">
<?php include '../../includes/header.php'; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="index.php" class="hover:text-black transition-colors">Kit Products</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-gray-900 font-medium">Edit: <?php echo htmlspecialchars($p['name']); ?></span>
        </div>
        <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Edit Product</h2>
        <div class="flex items-center gap-2 mt-1">
            <span class="text-sm text-gray-400">SKU:</span>
            <span class="text-sm font-mono text-gray-600 font-semibold"><?php echo $p['sku']; ?></span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="confirmDelete()" class="flex items-center gap-2 border border-red-200 text-red-500 px-5 py-2.5 rounded-full hover:bg-red-50 transition-all font-medium text-sm">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
        </button>
        <a href="index.php" class="flex items-center gap-2 border border-gray-200 bg-white text-gray-600 px-5 py-2.5 rounded-full hover:bg-gray-50 transition-all font-medium text-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
        </a>
    </div>
</div>

<?php if($saved): ?>
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 mb-6">
    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
    <p class="text-sm font-semibold text-emerald-700">Product updated successfully! <a href="index.php" class="underline">Back to list →</a></p>
</div>
<?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $id; ?>">
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT -->
    <div class="xl:col-span-2 flex flex-col gap-6">

        <!-- Identity -->
        <div class="card p-7">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 bg-black rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="package" class="text-white" style="width:18px;height:18px"></i>
                </div>
                <div><h3 class="text-base font-bold text-gray-900">Product Identity</h3><p class="text-xs text-gray-400">Update basic product information</p></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">Product Name <span class="required">*</span></label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Brand <span class="required">*</span></label>
                    <div class="relative">
                        <select name="brand" class="form-input pr-10 cursor-pointer appearance-none" required>
                            <?php foreach($brands as $b): ?>
                            <option <?php echo $b===$p['brand']?'selected':''; ?>><?php echo $b; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="form-label">SKU <span class="required">*</span></label>
                    <input type="text" name="sku" value="<?php echo $p['sku']; ?>" class="form-input" required>
                </div>
            </div>
        </div>

        <!-- Pricing & Stock (live edit view) -->
        <div class="card p-7">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bar-chart-3" style="width:18px;height:18px" class="text-gray-600"></i>
                </div>
                <div><h3 class="text-base font-bold text-gray-900">Pricing & Stock</h3><p class="text-xs text-gray-400">Current live stock breakdown</p></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                <div>
                    <label class="form-label">Price per Unit ₹ <span class="required">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">₹</span>
                        <input type="number" name="price" value="<?php echo $p['price']; ?>" class="form-input pl-8" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Min Stock Alert</label>
                    <input type="number" name="min_stock" value="<?php echo $p['min_stock']; ?>" class="form-input">
                </div>
                <div>
                    <label class="form-label">Stock Adjust <span class="text-gray-300 font-normal normal-case">(+/−)</span></label>
                    <input type="number" name="stock_adjust" placeholder="0" class="form-input" title="Positive to add, negative to reduce stock">
                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Positive adds, negative reduces</p>
                </div>
            </div>

            <!-- Live Stock Breakdown -->
            <div class="bg-gray-50 rounded-2xl p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Current Stock Breakdown</p>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-white rounded-xl p-3 border border-gray-100 text-center shadow-sm">
                        <p class="text-[10px] text-gray-400 font-semibold uppercase mb-1">Total</p>
                        <p class="text-xl font-bold text-gray-900"><?php echo $p['total_stock']; ?></p>
                        <p class="text-[10px] text-gray-300">Stock</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3 border border-amber-100 text-center shadow-sm">
                        <p class="text-[10px] text-amber-400 font-semibold uppercase mb-1">Assigned</p>
                        <p class="text-xl font-bold text-amber-600"><?php echo $p['assigned_stock']; ?></p>
                        <p class="text-[10px] text-amber-300">Stock</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-center shadow-sm">
                        <p class="text-[10px] text-emerald-500 font-semibold uppercase mb-1">Available</p>
                        <p class="text-xl font-bold text-emerald-600"><?php echo $available; ?></p>
                        <p class="text-[10px] text-emerald-300">Stock</p>
                    </div>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full <?php echo $available <= $p['min_stock'] ? 'bg-amber-400' : 'bg-emerald-400'; ?>" style="width:<?php echo $pct; ?>%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1.5"><?php echo $pct; ?>% available · Inventory Value: ₹<?php echo number_format($p['total_stock'] * $p['price']); ?></p>
                <?php if($available <= $p['min_stock']): ?>
                <div class="mt-3 flex items-center gap-2 text-amber-600 text-xs font-semibold bg-amber-50 rounded-xl px-3 py-2">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 flex-shrink-0"></i>
                    Stock is below minimum alert level (<?php echo $p['min_stock']; ?>)
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="flex flex-col gap-6">
        <!-- Status -->
        <div class="card p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Product Status</h3>
            <div class="flex flex-col gap-2.5">
                <?php foreach(['Active','Inactive','Discontinued'] as $st):
                    $desc  = ['Active'=>'Available for assignment','Inactive'=>'Temporarily disabled','Discontinued'=>'Permanently removed'];
                    $icCol = ['Active'=>'text-emerald-500','Inactive'=>'text-gray-400','Discontinued'=>'text-rose-500'];
                    $ic    = ['Active'=>'check-circle','Inactive'=>'pause-circle','Discontinued'=>'x-circle'];
                ?>
                <label class="flex items-center gap-3 border border-gray-100 rounded-2xl p-4 cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-black has-[:checked]:bg-gray-50">
                    <input type="radio" name="status" value="<?php echo $st; ?>" class="sr-only" <?php echo $st===$p['status']?'checked':''; ?>>
                    <i data-lucide="<?php echo $ic[$st]; ?>" class="w-5 h-5 flex-shrink-0 <?php echo $icCol[$st]; ?>"></i>
                    <div><p class="text-sm font-semibold text-gray-900"><?php echo $st; ?></p><p class="text-[11px] text-gray-400"><?php echo $desc[$st]; ?></p></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Info Card -->
        <div class="card p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Info</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">SKU</span><span class="font-mono font-semibold text-gray-700 text-xs"><?php echo $p['sku']; ?></span></div>
                <div class="flex justify-between"><span class="text-gray-400">Brand</span><span class="font-semibold text-gray-900"><?php echo $p['brand']; ?></span></div>
                    <span class="font-semibold text-gray-900"><?php echo $p['status']; ?></span>
            </div>
        </div>

        <!-- Save Buttons -->
        <div class="card p-5 flex flex-col gap-3">
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-black text-white py-3 rounded-2xl font-semibold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
                <i data-lucide="save" class="w-4 h-4"></i> Save Changes
            </button>
            <a href="index.php" class="w-full flex items-center justify-center gap-2 text-gray-500 hover:text-gray-800 py-2.5 rounded-2xl font-medium text-sm transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Cancel
            </a>
            <hr class="border-gray-100">
            <button type="button" onclick="confirmDelete()" class="w-full flex items-center justify-center gap-2 text-red-400 hover:text-red-600 py-2.5 rounded-2xl font-medium text-sm transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i> Delete Product
            </button>
        </div>
    </div>

</div>
</form>
</main>
</div>
 <script>
 lucide.createIcons({attrs:{'stroke-width':2}});
 function confirmDelete() {
     const assigned = <?php echo $p['assigned_stock']; ?>;
     if (assigned > 0) {
         Swal.fire({title:'Cannot Delete',html:`<p class="text-gray-500">This product has <strong>${assigned}</strong> currently assigned to professionals. Recall all assignments before deleting.</p>`,icon:'warning',confirmButtonColor:'#000',confirmButtonText:'Understood'});
         return;
     }
     Swal.fire({
         title:'Delete Product?',
         html:`<p class="text-gray-500">Are you sure you want to soft-delete <strong><?php echo htmlspecialchars($p['name']); ?></strong>? The audit history will be preserved.</p>`,
         icon:'warning',showCancelButton:true,confirmButtonColor:'#e11d48',cancelButtonColor:'#9ca3af',confirmButtonText:'Yes, Delete'
     }).then(r => {
         if(r.isConfirmed) {
             Swal.fire({title:'Deleted!',text:'Product has been soft-deleted.',icon:'success',confirmButtonColor:'#000',timer:2000,showConfirmButton:false})
                 .then(() => window.location.href='index.php');
         }
     });
 }
 </script>
</body>
</html>

<?php
$section    = 'professionals';
$subSection = 'kit-orders';
$pageTitle  = 'Kit Orders';

$today = date('Y-m-d');

/* -- Mock Data for Kit Orders ---------------------------------------------- */
/* ── Flat kit inventory rows (Purchased / Used / Remaining) ──────────── */
$kits = [
    ['id' => 'K-101', 'name' => 'Foundation Shades Set',        'category' => 'Bridal', 'professional' => 'Priya Sharma',    'purchased' => 50, 'used' => 38],
    ['id' => 'K-102', 'name' => 'Full Coverage Concealer',       'category' => 'Bridal', 'professional' => 'Ananya Iyer',    'purchased' => 40, 'used' => 32],
    ['id' => 'K-103', 'name' => 'Lip Colour Palette',            'category' => 'Bridal', 'professional' => 'Sarah Khan',     'purchased' => 30, 'used' => 28],
    ['id' => 'K-104', 'name' => 'Professional Brushes Set',      'category' => 'Bridal', 'professional' => 'Priya Sharma',    'purchased' => 20, 'used' => 18],
    ['id' => 'K-105', 'name' => 'Setting Spray',                 'category' => 'Bridal', 'professional' => 'Ananya Iyer',    'purchased' => 60, 'used' => 52],
    ['id' => 'K-106', 'name' => 'Gentle Facial Cleanser 500ml',  'category' => 'Facial', 'professional' => 'Meera Reddy',    'purchased' => 40, 'used' => 28],
    ['id' => 'K-107', 'name' => 'Rose Water Toner 200ml',        'category' => 'Facial', 'professional' => 'Zoya Qureshi',   'purchased' => 50, 'used' => 42],
    ['id' => 'K-108', 'name' => 'Vitamin C Face Serum 30ml',     'category' => 'Facial', 'professional' => 'Meera Reddy',    'purchased' => 30, 'used' => 29],
    ['id' => 'K-109', 'name' => 'Gold Face Pack',                'category' => 'Facial', 'professional' => 'Sarah Khan',     'purchased' => 35, 'used' => 5],
    ['id' => 'K-110', 'name' => 'Lavender Essential Oil 100ml',  'category' => 'Spa',    'professional' => 'Rahul Verma',    'purchased' => 30, 'used' => 22],
    ['id' => 'K-111', 'name' => 'Eucalyptus Essential Oil 100ml','category' => 'Spa',    'professional' => 'Rahul Verma',    'purchased' => 25, 'used' => 18],
    ['id' => 'K-112', 'name' => 'Swedish Massage Oil 500ml',     'category' => 'Spa',    'professional' => 'Zoya Qureshi',   'purchased' => 20, 'used' => 15],
    ['id' => 'K-113', 'name' => 'Hot Stone Massage Oil 250ml',   'category' => 'Spa',    'professional' => 'Sarah Khan',     'purchased' => 18, 'used' => 18],
    ['id' => 'K-114', 'name' => 'Hard Wax Beans 1kg',            'category' => 'Waxing', 'professional' => 'Priya Sharma',    'purchased' => 10, 'used' => 8],
    ['id' => 'K-115', 'name' => 'Strip Wax Rolls',               'category' => 'Waxing', 'professional' => 'Ananya Iyer',    'purchased' => 20, 'used' => 17],
    ['id' => 'K-116', 'name' => 'Pre-Wax Lotion 500ml',          'category' => 'Waxing', 'professional' => 'Meera Reddy',    'purchased' => 15, 'used' => 14],
    ['id' => 'K-117', 'name' => 'Post-Wax Soothing Lotion 500ml','category' => 'Waxing', 'professional' => 'Zoya Qureshi',   'purchased' => 15, 'used' => 10],
    ['id' => 'K-118', 'name' => 'Hair Color Tubes (Assorted)',   'category' => 'Hair Color', 'professional' => 'Neha Gupta',  'purchased' => 50, 'used' => 35],
    ['id' => 'K-119', 'name' => 'Color Developer 20Vol',         'category' => 'Hair Color', 'professional' => 'Neha Gupta',  'purchased' => 10, 'used' => 7],
    ['id' => 'K-120', 'name' => 'Keratin Treatment Cream',       'category' => 'Keratin', 'professional' => 'Vikram Roy',    'purchased' => 12, 'used' => 10],
    ['id' => 'K-121', 'name' => 'Hair Spa Cream 500ml',          'category' => 'Hair Spa', 'professional' => 'Vikram Roy',    'purchased' => 24, 'used' => 16],
    ['id' => 'K-122', 'name' => 'Intensive Hair Mask 500ml',     'category' => 'Hair Spa', 'professional' => 'Neha Gupta',  'purchased' => 20, 'used' => 12],
    ['id' => 'K-123', 'name' => 'Hair Shine Serum 100ml',        'category' => 'Hair Spa', 'professional' => 'Vikram Roy',    'purchased' => 15, 'used' => 14],
];

/* ── Compute remaining & stats ───────────────────────────────────────── */
foreach ($kits as &$k) {
    $k['remaining'] = $k['purchased'] - $k['used'];
}
unset($k);

$totalKits    = count($kits);
$outStock     = count(array_filter($kits, fn($k) => $k['remaining'] === 0));
$lowStock     = count(array_filter($kits, fn($k) => $k['remaining'] > 0 && $k['remaining'] <= 5));
$inStock      = $totalKits - $outStock - $lowStock;
$totalValue   = array_reduce($kits, fn($c, $k) => $c + ($k['purchased'] * 850), 0); // Mock value calculation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kit Orders · Bellavella Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        .table-row:hover { background: #fafafa; }
        .kit-row.hidden-row { display: none; }
        .kit-row.out-of-stock { background: #fff5f5; }
        .kit-row.out-of-stock:hover { background: #fee2e2; }
        @keyframes rowPulse { 0%,100% { background:transparent; } 50% { background:#f0fdf4; } }
        .row-updated { animation: rowPulse .6s ease; }
        @media print { .no-print { display:none !important; } }
    </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-72 p-4 lg:p-8">
        <?php include '../../includes/header.php'; ?>

        <div class="flex flex-col gap-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Kit Orders</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Manage professional kit purchases and stock tracking</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400 font-medium">Auto-refresh: 5m</span>
                </div>
            </div>



            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Assigned</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $totalKits; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="package" class="w-6 h-6 text-gray-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Low Stock</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $lowStock; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Out of Stock</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $outStock; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Allocation Value</p>
                        <p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalValue); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Toolbar -->
                <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative flex-1 max-w-xs">
                        <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        <input id="search-input" type="text" placeholder="Search by name or professional…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
                    </div>
                    <span id="filter-count" class="text-xs text-gray-400 ml-auto whitespace-nowrap"></span>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kit Details</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professional</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Purchased</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Used</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Remaining</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="orders-tbody">
                            <?php foreach($kits as $k):
                                $rem = $k['remaining'];
                                $isOut = $rem === 0;
                                $isLow = $rem > 0 && $rem <= 5;

                                if($isOut) {
                                    $statusLabel = "Out of Stock";
                                    $statusClass = "bg-red-50 text-red-600";
                                    $rowClass    = "out-of-stock";
                                } elseif($isLow) {
                                    $statusLabel = "Low Stock";
                                    $statusClass = "bg-amber-50 text-amber-600";
                                    $rowClass    = "";
                                } else {
                                    $statusLabel = "Available";
                                    $statusClass = "bg-emerald-50 text-emerald-600";
                                    $rowClass    = "";
                                }
                            ?>
                            <tr class="kit-row table-row transition-all <?php echo $rowClass; ?>"
                                data-id="<?php echo strtolower($k['id']); ?>"
                                data-name="<?php echo strtolower($k['name']); ?>"
                                data-professional="<?php echo strtolower($k['professional']); ?>">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-gray-400 tracking-tighter"><?php echo $k['id']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo $k['name']; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-700"><?php echo $k['professional']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm text-gray-600"><?php echo $k['purchased']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-medium text-gray-800">
                                    <?php echo $k['used']; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold <?php echo $isLow?'text-amber-600':'text-gray-900'; ?>">
                                        <?php echo $rem; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="no-results" class="hidden py-16 text-center">
                        <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No results match your filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ===================== SCRIPT ===================== -->
<script>
lucide.createIcons({ attrs: { 'stroke-width': 2 } });

const kits = <?php echo json_encode($kits); ?>;

function applyFilters() {
    const search   = document.getElementById('search-input').value.toLowerCase();
    const rows     = document.querySelectorAll('.kit-row');
    let visible    = 0;

     rows.forEach(row => {
         const show = !search || 
                      row.dataset.name.includes(search) || 
                      row.dataset.professional.includes(search) || 
                      row.dataset.id.includes(search);
         row.classList.toggle('hidden-row', !show);
         if (show) visible++;
     });

    const noRes = document.getElementById('no-results');
    if (noRes) noRes.classList.toggle('hidden', visible > 0);
    
    const countEl = document.getElementById('filter-count');
    if (countEl) {
        countEl.textContent = visible === rows.length ? '' : `${visible} of ${rows.length} shown`;
    }
}

lucide.createIcons({ attrs: { 'stroke-width': 2 } });
</script>
</body>
</html>

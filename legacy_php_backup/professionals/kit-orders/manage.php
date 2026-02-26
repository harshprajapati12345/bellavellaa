<?php
$section    = 'professionals';
$subSection = 'kit-orders';
$pageTitle  = 'Order Management';

$today = date('Y-m-d');

/* -- Mock Data for Supplier Orders ----------------------------------------- */
$orders = [
    [
        'id' => 'ORD-9842',
        'supplier' => 'Luxe Beauty Supplies',
        'products' => [
            ['name' => 'Foundation Shades Set', 'qty' => 10, 'price' => 1200],
            ['name' => 'Setting Spray', 'qty' => 20, 'price' => 450],
        ],
        'order_date' => '2024-02-15',
        'est_delivery' => '2024-02-22',
        'status' => 'Delivered',
        'payment' => 'Paid',
        'amount' => 21000
    ],
    [
        'id' => 'ORD-9855',
        'supplier' => 'Glow Essentials Co.',
        'products' => [
            ['name' => 'Gold Face Pack', 'qty' => 15, 'price' => 850],
            ['name' => 'Vitamin C Serum', 'qty' => 10, 'price' => 950],
        ],
        'order_date' => '2024-02-20',
        'est_delivery' => '2024-02-27',
        'status' => 'Shipped',
        'payment' => 'Paid',
        'amount' => 22250
    ],
    [
        'id' => 'ORD-9861',
        'supplier' => 'Spa Direct India',
        'products' => [
            ['name' => 'Swedish Massage Oil', 'qty' => 5, 'price' => 1500],
        ],
        'order_date' => '2024-02-24',
        'est_delivery' => '2024-03-01',
        'status' => 'Approved',
        'payment' => 'Unpaid',
        'amount' => 7500
    ],
    [
        'id' => 'ORD-9868',
        'supplier' => 'Professional Waxing Ltd',
        'products' => [
            ['name' => 'Hard Wax Beans 1kg', 'qty' => 30, 'price' => 600],
        ],
        'order_date' => '2024-02-25',
        'est_delivery' => '2024-03-02',
        'status' => 'Pending',
        'payment' => 'Unpaid',
        'amount' => 18000
    ]
];

/* -- Stats Calculation ----------------------------------------------------- */
$totalOrders = count($orders);
$pending     = count(array_filter($orders, fn($o) => $o['status'] === 'Pending'));
$shipped     = count(array_filter($orders, fn($o) => $o['status'] === 'Shipped'));
$totalSpend  = array_reduce($orders, fn($c, $o) => $c + $o['amount'], 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management · Bellavella Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        .table-row:hover { background: #fafafa; }
        .order-row.hidden-row { display: none; }
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
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Order Management</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Track supplier purchases and procurement status</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="newOrder()" class="bg-gray-900 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-2 shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> New Order
                    </button>
                    <button onclick="exportOrders()" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                        <i data-lucide="download" class="w-4 h-4"></i> Export All
                    </button>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shopping-cart" class="w-6 h-6 text-gray-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Pending</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $pending; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">In Transit</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $shipped; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="truck" class="w-6 h-6 text-blue-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Total Spend</p>
                        <p class="text-3xl font-bold text-gray-900">₹<?php echo number_format($totalSpend); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="credit-card" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Toolbar -->
                <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative flex-1 max-w-xs">
                        <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        <input id="search-input" type="text" placeholder="Search by ID or supplier…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
                    </div>
                    <select id="filter-status" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Order ID</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Supplier & Products</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dates</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Amount</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="orders-tbody">
                            <?php foreach($orders as $o):
                                $statusClass = match($o['status']) {
                                    'Pending'   => 'bg-amber-50 text-amber-600',
                                    'Approved'  => 'bg-blue-50 text-blue-600',
                                    'Shipped'   => 'bg-purple-50 text-purple-600',
                                    'Delivered' => 'bg-emerald-50 text-emerald-600',
                                    default     => 'bg-gray-50 text-gray-600'
                                };
                            ?>
                            <tr class="order-row table-row transition-all"
                                data-id="<?php echo strtolower($o['id']); ?>"
                                data-supplier="<?php echo strtolower($o['supplier']); ?>"
                                data-status="<?php echo $o['status']; ?>">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900"><?php echo $o['id']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo $o['supplier']; ?></span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-tight"><?php echo count($o['products']); ?> Products</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-600">Ordered: <?php echo date('d M', strtotime($o['order_date'])); ?></span>
                                        <span class="text-[10px] text-gray-400 italic">Est: <?php echo date('d M', strtotime($o['est_delivery'])); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $statusClass; ?>">
                                        <?php echo $o['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-gray-900">₹<?php echo number_format($o['amount']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="viewOrder('<?php echo $o['id']; ?>')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-900">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div id="no-results" class="hidden py-16 text-center">
                        <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No orders found.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ===================== SCRIPT ===================== -->
<script>
lucide.createIcons({ attrs: { 'stroke-width': 2 } });

const ordersData = <?php echo json_encode($orders); ?>;

function applyFilters() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    const rows   = document.querySelectorAll('.order-row');
    let visible  = 0;

    rows.forEach(row => {
        const matchSearch = !search || row.dataset.id.includes(search) || row.dataset.supplier.includes(search);
        const matchStatus = !status || row.dataset.status === status;
        const show = matchSearch && matchStatus;
        row.classList.toggle('hidden-row', !show);
        if (show) visible++;
    });

    document.getElementById('no-results').classList.toggle('hidden', visible > 0);
}

function newOrder() {
    Swal.fire({
        title: 'New Supplier Order',
        html: `
            <div class="text-left">
                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Supplier</label>
                <select id="swal-supplier" class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm mb-4">
                    <option>Luxe Beauty Supplies</option>
                    <option>Glow Essentials Co.</option>
                    <option>Spa Direct India</option>
                </select>
                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Product List</label>
                <div class="space-y-2 mb-4">
                    <div class="flex gap-2">
                        <input type="text" placeholder="Product Name" class="flex-1 p-3 bg-gray-50 border-none rounded-xl text-sm">
                        <input type="number" placeholder="Qty" class="w-20 p-3 bg-gray-50 border-none rounded-xl text-sm">
                    </div>
                </div>
                <label class="text-xs font-bold text-gray-400 uppercase mb-1 block">Expected Delivery</label>
                <input type="date" id="swal-date" class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create Order',
        confirmButtonColor: '#000',
        cancelButtonText: 'Cancel',
        padding: '2rem',
        customClass: {
            popup: 'rounded-[2rem]',
            confirmButton: 'rounded-xl px-6 py-3',
            cancelButton: 'rounded-xl px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Order Created',
                text: 'The supplier order has been drafted successfully.',
                confirmButtonColor: '#000',
                customClass: { popup: 'rounded-[1.5rem]' }
            });
        }
    });
}

function viewOrder(id) {
    const order = ordersData.find(o => o.id === id);
    if (!order) return;

    let itemsHtml = order.products.map(p => `
        <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-sm text-gray-900">${p.name} (x${p.qty})</span>
            <span class="text-sm font-medium text-gray-700">₹${number_format(p.price * p.qty)}</span>
        </div>
    `).join('');

    Swal.fire({
        title: `Order Details - ${id}`,
        html: `
            <div class="text-left mt-4">
                <div class="bg-gray-50 p-4 rounded-2xl mb-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Supplier</p>
                    <p class="text-sm font-semibold text-gray-900">${order.supplier}</p>
                </div>
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Order Items</p>
                    ${itemsHtml}
                </div>
                <div class="flex justify-between py-3 border-t-2 border-gray-100 mt-2">
                    <span class="font-bold text-gray-900">Total Amount</span>
                    <span class="font-bold text-xl text-gray-900">₹${number_format(order.amount)}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-[10px] text-gray-400 uppercase mb-1">Status</p>
                        <p class="text-xs font-bold text-gray-900">${order.status}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-[10px] text-gray-400 uppercase mb-1">Payment</p>
                        <p class="text-xs font-bold text-emerald-600">${order.payment}</p>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Print Invoice',
        confirmButtonColor: '#000',
        padding: '2rem',
        customClass: {
            popup: 'rounded-[2rem]',
            confirmButton: 'rounded-xl px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.print();
        }
    });
}

function exportOrders() {
    Swal.fire({
        icon: 'success',
        title: 'Exporting Data',
        text: 'CSV file is being generated...',
        timer: 1500,
        showConfirmButton: false,
        customClass: { popup: 'rounded-[1.5rem]' }
    });
}

function number_format(num) {
    return new Intl.NumberFormat('en-IN').format(num);
}
</script>
</body>
</html>

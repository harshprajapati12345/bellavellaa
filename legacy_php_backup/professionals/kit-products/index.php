<?php
$section    = 'professionals';
$subSection = 'kit-products';
$pageTitle  = 'Kit Products';

/* ═══════════════════════════════════════════════════════════════════════
   WOMEN'S SALON KITS
   ═══════════════════════════════════════════════════════════════════════ */

/* ─── Bridal Makeup Kit ─────────────────────────────────────────────── */
$bridal = [
    ['id'=>1,  'sku'=>'BR-0001','name'=>'Foundation Shades Set',    'brand'=>'MAC',             'category'=>'Bridal','unit'=>'pcs','price'=>3800,'total_stock'=>40, 'assigned_stock'=>30,'available_stock'=>10,'min_stock'=>8, 'expiry_date'=>'2026-12-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>2,  'sku'=>'BR-0002','name'=>'Full Coverage Concealer',  'brand'=>'NARS',             'category'=>'Bridal','unit'=>'pcs','price'=>2100,'total_stock'=>60, 'assigned_stock'=>45,'available_stock'=>15,'min_stock'=>10,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>3,  'sku'=>'BR-0003','name'=>'Compact Powder',           'brand'=>'Lakme',            'category'=>'Bridal','unit'=>'pcs','price'=>950, 'total_stock'=>80, 'assigned_stock'=>55,'available_stock'=>25,'min_stock'=>15,'expiry_date'=>'2026-06-30','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>4,  'sku'=>'BR-0004','name'=>'Blush & Highlighter Duo',  'brand'=>'Charlotte Tilbury','category'=>'Bridal','unit'=>'pcs','price'=>4200,'total_stock'=>35, 'assigned_stock'=>28,'available_stock'=>7, 'min_stock'=>8, 'expiry_date'=>'2026-11-30','status'=>'Active','last_restocked'=>'2025-01-20'],
    ['id'=>5,  'sku'=>'BR-0005','name'=>'Lip Colour Palette',       'brand'=>'MAC',             'category'=>'Bridal','unit'=>'pcs','price'=>5500,'total_stock'=>25, 'assigned_stock'=>20,'available_stock'=>5, 'min_stock'=>5, 'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-22'],
    ['id'=>6,  'sku'=>'BR-0006','name'=>'Professional Brushes Set', 'brand'=>'Sigma Beauty',    'category'=>'Bridal','unit'=>'pcs','price'=>3200,'total_stock'=>20, 'assigned_stock'=>15,'available_stock'=>5, 'min_stock'=>5, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-12-01'],
    ['id'=>7,  'sku'=>'BR-0007','name'=>'Setting Spray',            'brand'=>'Urban Decay',     'category'=>'Bridal','unit'=>'ml', 'price'=>2800,'total_stock'=>50, 'assigned_stock'=>38,'available_stock'=>12,'min_stock'=>10,'expiry_date'=>'2026-05-31','status'=>'Active','last_restocked'=>'2025-01-05'],
];

/* ─── Facial Treatment Kit ──────────────────────────────────────────── */
$facial = [
    ['id'=>8,  'sku'=>'FC-0001','name'=>'Gentle Facial Cleanser',   'brand'=>'Kama Ayurveda',   'category'=>'Skin',  'unit'=>'ml', 'price'=>1200,'total_stock'=>100,'assigned_stock'=>70,'available_stock'=>30,'min_stock'=>20,'expiry_date'=>'2026-10-31','status'=>'Active','last_restocked'=>'2025-01-08'],
    ['id'=>9,  'sku'=>'FC-0002','name'=>'Exfoliating Face Scrub',   'brand'=>'Forest Essentials','category'=>'Skin', 'unit'=>'ml', 'price'=>1800,'total_stock'=>80, 'assigned_stock'=>60,'available_stock'=>20,'min_stock'=>15,'expiry_date'=>'2026-07-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>10, 'sku'=>'FC-0003','name'=>'Massage Cream',            'brand'=>'Biotique',        'category'=>'Skin',  'unit'=>'ml', 'price'=>650, 'total_stock'=>120,'assigned_stock'=>90,'available_stock'=>30,'min_stock'=>25,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>11, 'sku'=>'FC-0004','name'=>'Gold Face Pack',           'brand'=>'VLCC',            'category'=>'Skin',  'unit'=>'gm', 'price'=>850, 'total_stock'=>90, 'assigned_stock'=>3, 'available_stock'=>87,'min_stock'=>20,'expiry_date'=>'2026-04-30','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>12, 'sku'=>'FC-0005','name'=>'Rose Water Toner',         'brand'=>'Kama Ayurveda',   'category'=>'Skin',  'unit'=>'ml', 'price'=>950, 'total_stock'=>150,'assigned_stock'=>100,'available_stock'=>50,'min_stock'=>30,'expiry_date'=>'2026-11-30','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>13, 'sku'=>'FC-0006','name'=>'Vitamin C Face Serum',     'brand'=>'Minimalist',      'category'=>'Skin',  'unit'=>'ml', 'price'=>2100,'total_stock'=>60, 'assigned_stock'=>45,'available_stock'=>15,'min_stock'=>12,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-20'],
];

/* ─── Manicure–Pedicure Kit ─────────────────────────────────────────── */
$maniPedi = [
    ['id'=>14, 'sku'=>'MP-0001','name'=>'Nail Cutter Set',          'brand'=>'Vega',            'category'=>'Nail',  'unit'=>'pcs','price'=>480, 'total_stock'=>30, 'assigned_stock'=>22,'available_stock'=>8, 'min_stock'=>5, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-11-01'],
    ['id'=>15, 'sku'=>'MP-0002','name'=>'Cuticle Pusher & Nipper',  'brand'=>'Vega',            'category'=>'Nail',  'unit'=>'pcs','price'=>320, 'total_stock'=>25, 'assigned_stock'=>18,'available_stock'=>7, 'min_stock'=>5, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-11-05'],
    ['id'=>16, 'sku'=>'MP-0003','name'=>'Nail Buffer Block',        'brand'=>'OPI',             'category'=>'Nail',  'unit'=>'pcs','price'=>180, 'total_stock'=>50, 'assigned_stock'=>35,'available_stock'=>15,'min_stock'=>10,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-12-10'],
    ['id'=>17, 'sku'=>'MP-0004','name'=>'Foot & Hand Scrub',        'brand'=>'Lotus Herbals',   'category'=>'Nail',  'unit'=>'ml', 'price'=>750, 'total_stock'=>80, 'assigned_stock'=>0, 'available_stock'=>80,'min_stock'=>20,'expiry_date'=>'2026-05-31','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>18, 'sku'=>'MP-0005','name'=>'Nourishing Hand Cream',    'brand'=>'The Body Shop',   'category'=>'Nail',  'unit'=>'ml', 'price'=>1100,'total_stock'=>60, 'assigned_stock'=>40,'available_stock'=>20,'min_stock'=>15,'expiry_date'=>'2026-06-30','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>19, 'sku'=>'MP-0006','name'=>'Acetone Nail Remover',     'brand'=>'Nail Care',       'category'=>'Nail',  'unit'=>'ml', 'price'=>280, 'total_stock'=>200,'assigned_stock'=>150,'available_stock'=>50,'min_stock'=>40,'expiry_date'=>'2027-12-31','status'=>'Active','last_restocked'=>'2025-01-05'],
];

/* ─── Waxing Kit ────────────────────────────────────────────────────── */
$waxing = [
    ['id'=>20, 'sku'=>'WX-0001','name'=>'Wax Heater Machine',       'brand'=>'Rica',            'category'=>'Luxe',  'unit'=>'pcs','price'=>3500,'total_stock'=>10, 'assigned_stock'=>8, 'available_stock'=>2, 'min_stock'=>2, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-08-01'],
    ['id'=>21, 'sku'=>'WX-0002','name'=>'Hard Wax Beans',           'brand'=>'Rica',            'category'=>'Prime', 'unit'=>'gm', 'price'=>1200,'total_stock'=>5000,'assigned_stock'=>3500,'available_stock'=>1500,'min_stock'=>500,'expiry_date'=>'2027-06-30','status'=>'Active','last_restocked'=>'2025-01-20'],
    ['id'=>22, 'sku'=>'WX-0003','name'=>'Strip Wax Roll',           'brand'=>'Nads',            'category'=>'Prime', 'unit'=>'pcs','price'=>850, 'total_stock'=>60, 'assigned_stock'=>42,'available_stock'=>18,'min_stock'=>10,'expiry_date'=>'2026-12-31','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>23, 'sku'=>'WX-0004','name'=>'Wax Strips (Non-Woven)',   'brand'=>'Rica',            'category'=>'Prime', 'unit'=>'pcs','price'=>120, 'total_stock'=>500,'assigned_stock'=>380,'available_stock'=>120,'min_stock'=>100,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>24, 'sku'=>'WX-0005','name'=>'Pre-Wax Lotion',           'brand'=>'Depileve',        'category'=>'Prime', 'unit'=>'ml', 'price'=>680, 'total_stock'=>120,'assigned_stock'=>90,'available_stock'=>30,'min_stock'=>20,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>25, 'sku'=>'WX-0006','name'=>'Post-Wax Soothing Lotion', 'brand'=>'Rica',            'category'=>'Prime', 'unit'=>'ml', 'price'=>750, 'total_stock'=>100,'assigned_stock'=>72,'available_stock'=>28,'min_stock'=>20,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-12'],
];

/* ═══════════════════════════════════════════════════════════════════════
   SPA KITS
   ═══════════════════════════════════════════════════════════════════════ */

/* ─── Massage Therapy Kit ───────────────────────────────────────────── */
$massage = [
    ['id'=>26, 'sku'=>'SP-0001','name'=>'Swedish Massage Oil',      'brand'=>'Forest Essentials','category'=>'Spa',  'unit'=>'ml', 'price'=>1800,'total_stock'=>200, 'assigned_stock'=>150,'available_stock'=>50,'min_stock'=>40,'expiry_date'=>'2026-10-31','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>27, 'sku'=>'SP-0002','name'=>'Deep Tissue Massage Cream','brand'=>'Biotique',         'category'=>'Spa',  'unit'=>'ml', 'price'=>1400,'total_stock'=>150, 'assigned_stock'=>110,'available_stock'=>40,'min_stock'=>30,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>28, 'sku'=>'SP-0003','name'=>'Disposable Massage Sheets','brand'=>'Hygiene Plus',     'category'=>'Spa',  'unit'=>'pcs','price'=>25,  'total_stock'=>1000,'assigned_stock'=>650,'available_stock'=>350,'min_stock'=>200,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2025-01-20'],
    ['id'=>29, 'sku'=>'SP-0004','name'=>'Premium Spa Towels',       'brand'=>'Trident',          'category'=>'Spa',  'unit'=>'pcs','price'=>380, 'total_stock'=>60,  'assigned_stock'=>42,'available_stock'=>18,'min_stock'=>10,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-12-01'],
];

/* ─── Body Polishing Kit ────────────────────────────────────────────── */
$bodyPolish = [
    ['id'=>30, 'sku'=>'SP-0005','name'=>'Coffee Body Scrub',        'brand'=>'Mcaffeine',        'category'=>'Spa',  'unit'=>'gm', 'price'=>950, 'total_stock'=>80,  'assigned_stock'=>55,'available_stock'=>25,'min_stock'=>15,'expiry_date'=>'2026-06-30','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>31, 'sku'=>'SP-0006','name'=>'Brightening Body Pack',    'brand'=>'Kama Ayurveda',    'category'=>'Spa',  'unit'=>'gm', 'price'=>1450,'total_stock'=>70,  'assigned_stock'=>50,'available_stock'=>20,'min_stock'=>15,'expiry_date'=>'2026-07-31','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>32, 'sku'=>'SP-0007','name'=>'Shea Butter Moisturizer',  'brand'=>'The Body Shop',    'category'=>'Spa',  'unit'=>'ml', 'price'=>1850,'total_stock'=>90,  'assigned_stock'=>65,'available_stock'=>25,'min_stock'=>20,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-15'],
];

/* ─── Aromatherapy Kit ──────────────────────────────────────────────── */
$aroma = [
    ['id'=>33, 'sku'=>'AR-0001','name'=>'Lavender Essential Oil',   'brand'=>'Forest Essentials','category'=>'Spa',  'unit'=>'ml', 'price'=>1200,'total_stock'=>50,  'assigned_stock'=>38,'available_stock'=>12,'min_stock'=>10,'expiry_date'=>'2026-04-30','status'=>'Active','last_restocked'=>'2025-01-08'],
    ['id'=>34, 'sku'=>'AR-0002','name'=>'Eucalyptus Essential Oil', 'brand'=>'Kama Ayurveda',    'category'=>'Spa',  'unit'=>'ml', 'price'=>980, 'total_stock'=>60,  'assigned_stock'=>45,'available_stock'=>15,'min_stock'=>12,'expiry_date'=>'2026-05-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>35, 'sku'=>'AR-0003','name'=>'Aroma Diffuser',           'brand'=>'Iris',             'category'=>'Spa',  'unit'=>'pcs','price'=>2200,'total_stock'=>8,   'assigned_stock'=>6, 'available_stock'=>2, 'min_stock'=>2, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-10-01'],
    ['id'=>36, 'sku'=>'AR-0004','name'=>'Herbal Compress Bags',     'brand'=>'Thai Therapies',   'category'=>'Spa',  'unit'=>'pcs','price'=>850, 'total_stock'=>40,  'assigned_stock'=>28,'available_stock'=>12,'min_stock'=>8, 'expiry_date'=>'2026-12-31','status'=>'Active','last_restocked'=>'2025-01-05'],
];

/* ─── Hot Stone Therapy Kit ─────────────────────────────────────────── */
$hotStone = [
    ['id'=>37, 'sku'=>'HS-0001','name'=>'Basalt Hot Stones Set',    'brand'=>'Spa Pro',          'category'=>'Spa',  'unit'=>'pcs','price'=>4800,'total_stock'=>6,   'assigned_stock'=>5, 'available_stock'=>1, 'min_stock'=>2, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-09-01'],
    ['id'=>38, 'sku'=>'HS-0002','name'=>'Stone Heater Machine',     'brand'=>'Spa Master',       'category'=>'Spa',  'unit'=>'pcs','price'=>8500,'total_stock'=>4,   'assigned_stock'=>3, 'available_stock'=>1, 'min_stock'=>1, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-06-01'],
    ['id'=>39, 'sku'=>'HS-0003','name'=>'Hot Stone Massage Oil',    'brand'=>'Forest Essentials','category'=>'Spa',  'unit'=>'ml', 'price'=>1600,'total_stock'=>80,  'assigned_stock'=>60,'available_stock'=>20,'min_stock'=>15,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-15'],
];

/* ═══════════════════════════════════════════════════════════════════════
   HAIR STUDIO KITS
   ═══════════════════════════════════════════════════════════════════════ */

/* ─── Hair Coloring Kit ─────────────────────────────────────────────── */
$hairColor = [
    ['id'=>40, 'sku'=>'HC-0001','name'=>'Professional Hair Color Tubes','brand'=>"L'Oreal",     'category'=>'Hair',  'unit'=>'gm', 'price'=>950, 'total_stock'=>500, 'assigned_stock'=>350,'available_stock'=>150,'min_stock'=>80,'expiry_date'=>'2026-06-30','status'=>'Active','last_restocked'=>'2025-01-20'],
    ['id'=>41, 'sku'=>'HC-0002','name'=>'Color Developer 20Vol',    'brand'=>"L'Oreal",          'category'=>'Hair',  'unit'=>'ml', 'price'=>450, 'total_stock'=>1000,'assigned_stock'=>700,'available_stock'=>300,'min_stock'=>150,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>42, 'sku'=>'HC-0003','name'=>'Tint Brush Set',           'brand'=>'Wella',            'category'=>'Hair',  'unit'=>'pcs','price'=>380, 'total_stock'=>30,  'assigned_stock'=>22,'available_stock'=>8, 'min_stock'=>5, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-12-15'],
    ['id'=>43, 'sku'=>'HC-0004','name'=>'Color Gloves (Box)',       'brand'=>'Safe Hands',        'category'=>'Hair',  'unit'=>'pcs','price'=>120, 'total_stock'=>100, 'assigned_stock'=>75,'available_stock'=>25,'min_stock'=>20,'expiry_date'=>'2028-12-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>44, 'sku'=>'HC-0005','name'=>'Color Mixing Bowl Set',    'brand'=>'Wella',            'category'=>'Hair',  'unit'=>'pcs','price'=>280, 'total_stock'=>20,  'assigned_stock'=>15,'available_stock'=>5, 'min_stock'=>5, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-11-01'],
];

/* ─── Keratin / Smoothening Kit ─────────────────────────────────────── */
$keratin = [
    ['id'=>45, 'sku'=>'KR-0001','name'=>'Keratin Treatment Cream',  'brand'=>'Brazilian Blowout','category'=>'Hair', 'unit'=>'ml', 'price'=>4500,'total_stock'=>60,  'assigned_stock'=>42,'available_stock'=>18,'min_stock'=>10,'expiry_date'=>'2026-05-31','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>46, 'sku'=>'KR-0002','name'=>'Clarifying Shampoo',       'brand'=>"L'Oreal",          'category'=>'Hair', 'unit'=>'ml', 'price'=>850, 'total_stock'=>100, 'assigned_stock'=>70,'available_stock'=>30,'min_stock'=>20,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>47, 'sku'=>'KR-0003','name'=>'Neutralizer Solution',     'brand'=>'Wella',            'category'=>'Hair',  'unit'=>'ml', 'price'=>1200,'total_stock'=>80,  'assigned_stock'=>58,'available_stock'=>22,'min_stock'=>15,'expiry_date'=>'2026-07-31','status'=>'Active','last_restocked'=>'2025-01-10'],
    ['id'=>48, 'sku'=>'KR-0004','name'=>'Heat Protection Serum',    'brand'=>"Tresemme",         'category'=>'Hair',  'unit'=>'ml', 'price'=>980, 'total_stock'=>90,  'assigned_stock'=>65,'available_stock'=>25,'min_stock'=>18,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-08'],
];

/* ─── Hair Spa Kit ──────────────────────────────────────────────────── */
$hairSpa = [
    ['id'=>49, 'sku'=>'HS-0101','name'=>'Hair Spa Cream',           'brand'=>"L'Oreal",          'category'=>'Hair', 'unit'=>'ml', 'price'=>1300,'total_stock'=>120, 'assigned_stock'=>88,'available_stock'=>32,'min_stock'=>25,'expiry_date'=>'2026-06-30','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>50, 'sku'=>'HS-0102','name'=>'Intensive Hair Mask',      'brand'=>'Schwarzkopf',      'category'=>'Hair',  'unit'=>'ml', 'price'=>2200,'total_stock'=>80,  'assigned_stock'=>58,'available_stock'=>22,'min_stock'=>15,'expiry_date'=>'2026-08-31','status'=>'Active','last_restocked'=>'2025-01-15'],
    ['id'=>51, 'sku'=>'HS-0103','name'=>'Hair Shine Serum',         'brand'=>'Moroccan Oil',     'category'=>'Hair',  'unit'=>'ml', 'price'=>2800,'total_stock'=>60,  'assigned_stock'=>45,'available_stock'=>15,'min_stock'=>12,'expiry_date'=>'2026-10-31','status'=>'Active','last_restocked'=>'2025-01-12'],
    ['id'=>52, 'sku'=>'HS-0104','name'=>'Steam Cap (Disposable)',   'brand'=>'Hair Pro',         'category'=>'Hair',  'unit'=>'pcs','price'=>45,  'total_stock'=>200, 'assigned_stock'=>150,'available_stock'=>50,'min_stock'=>50,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2025-01-10'],
];

/* ─── Hair Styling Kit ──────────────────────────────────────────────── */
$hairStyle = [
    ['id'=>53, 'sku'=>'ST-0001','name'=>'Strong Hold Hair Spray',   'brand'=>"L'Oreal",          'category'=>'Hair', 'unit'=>'ml', 'price'=>1100,'total_stock'=>70,  'assigned_stock'=>50,'available_stock'=>20,'min_stock'=>15,'expiry_date'=>'2026-07-31','status'=>'Active','last_restocked'=>'2025-01-20'],
    ['id'=>54, 'sku'=>'ST-0002','name'=>'Heat Protectant Spray',    'brand'=>"Tresemme",         'category'=>'Hair',  'unit'=>'ml', 'price'=>850, 'total_stock'=>90,  'assigned_stock'=>65,'available_stock'=>25,'min_stock'=>20,'expiry_date'=>'2026-09-30','status'=>'Active','last_restocked'=>'2025-01-18'],
    ['id'=>55, 'sku'=>'ST-0003','name'=>'Professional Curling Iron','brand'=>'Wahl',             'category'=>'Hair',  'unit'=>'pcs','price'=>5200,'total_stock'=>8,   'assigned_stock'=>6, 'available_stock'=>2, 'min_stock'=>2, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-09-01'],
    ['id'=>56, 'sku'=>'ST-0004','name'=>'Ceramic Hair Straightener','brand'=>'Philips',          'category'=>'Hair',  'unit'=>'pcs','price'=>4800,'total_stock'=>10,  'assigned_stock'=>7, 'available_stock'=>3, 'min_stock'=>2, 'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2024-10-01'],
    ['id'=>57, 'sku'=>'ST-0005','name'=>'Section Clips Set',        'brand'=>'Wahl',             'category'=>'Hair',  'unit'=>'pcs','price'=>280, 'total_stock'=>60,  'assigned_stock'=>42,'available_stock'=>18,'min_stock'=>12,'expiry_date'=>'2030-12-31','status'=>'Active','last_restocked'=>'2025-01-05'],
];

/* ─── Merge All Product Groups ──────────────────────────────────────── */
$products = array_merge($bridal, $facial, $maniPedi, $waxing, $massage, $bodyPolish, $aroma, $hotStone, $hairColor, $keratin, $hairSpa, $hairStyle);

/* ─── Summary Stats ─────────────────────────────────────────────────── */
$totalProducts   = count($products);
$lowStockCount   = count(array_filter($products, fn($p) => $p['available_stock'] > 0 && $p['available_stock'] <= $p['min_stock']));
$outOfStockCount = count(array_filter($products, fn($p) => $p['available_stock'] == 0));
$totalValue      = array_reduce($products, fn($c, $p) => $c + ($p['price'] * $p['available_stock']), 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kit Products · Bellavella Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        .table-row:hover { background: #fafafa; }
        .product-row.hidden-row { display: none; }
        .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="flex-1 lg:ml-72 p-4 lg:p-8">
        <?php include '../../includes/header.php'; ?>

        <div class="flex flex-col gap-6">

            <!-- ── Page Header ──────────────────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Kit Products</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Manage salon kit inventory across all service types</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="exportProducts()" class="flex items-center gap-2 border border-gray-200 bg-white text-gray-700 px-5 py-2.5 rounded-full hover:bg-gray-50 transition-all font-medium text-sm">
                        <i data-lucide="download" class="w-4 h-4"></i> Export
                    </button>
                    <a href="create.php" class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10">
                        <i data-lucide="plus" class="w-4 h-4"></i> Add Product
                    </a>
                </div>
            </div>

            <?php if($lowStockCount > 0 || $outOfStockCount > 0): ?>
            <!-- ── Alert Banner ──────────────────────────────────────── -->
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4">
                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-700">Stock Alert</p>
                    <p class="text-xs text-amber-500">
                        <?php echo $lowStockCount; ?> product<?php echo $lowStockCount!=1?'s':''; ?> running low
                        <?php if($outOfStockCount > 0): ?> · <?php echo $outOfStockCount; ?> out of stock<?php endif; ?>.
                        Consider restocking soon.
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Stat Cards ────────────────────────────────────────── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Total SKUs</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $totalProducts; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="package" class="w-6 h-6 text-gray-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Low Stock</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $lowStockCount; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-500 uppercase tracking-widest mb-1">Out of Stock</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $outOfStockCount; ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-500"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-500 uppercase tracking-widest mb-1">Inventory Value</p>
                        <p class="text-3xl font-bold text-gray-900">&#x20B9;<?php echo number_format($totalValue); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="indian-rupee" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                </div>
            </div>

            <!-- ── Products Table ────────────────────────────────────── -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Toolbar -->
                <div class="p-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative flex-1 max-w-xs">
                        <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        <input id="search-input" type="text" placeholder="Search by name, SKU, brand…" oninput="applyFilters()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-black/5 outline-none">
                    </div>
                    <select id="filter-stock" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                        <option value="">All Stock Levels</option>
                        <option value="in">In Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                    <select id="filter-status" onchange="applyFilters()" class="bg-gray-50 border-none rounded-xl text-sm px-4 py-2 focus:ring-2 focus:ring-black/5 outline-none cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <span id="filter-count" class="text-xs text-gray-400 ml-auto whitespace-nowrap"></span>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKU</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Price</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="products-tbody">
                            <?php foreach($products as $p):
                                $avail  = $p['available_stock'];
                                $min    = $p['min_stock'];
                                $total  = $p['total_stock'];

                                if ($avail == 0)             { $stockClass = 'bg-red-50 text-red-600';    $stockLabel = 'Out of Stock'; }
                                elseif ($avail <= $min)       { $stockClass = 'bg-amber-50 text-amber-600'; $stockLabel = 'Low Stock'; }
                                else                          { $stockClass = 'bg-emerald-50 text-emerald-700'; $stockLabel = 'In Stock'; }

                                $barPct  = $total > 0 ? min(100, round($avail / $total * 100)) : 0;
                                $barColor = $avail == 0 ? 'bg-red-400' : ($avail <= $min ? 'bg-amber-400' : 'bg-emerald-400');

                                $statusClass = $p['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500';

                                // Filter data attrs
                                $stockAttr = $avail == 0 ? 'out' : ($avail <= $min ? 'low' : 'in');
                            ?>
                            <tr class="product-row table-row transition-all"
                                data-name="<?php echo strtolower($p['name']); ?>"
                                data-sku="<?php echo strtolower($p['sku']); ?>"
                                data-brand="<?php echo strtolower($p['brand']); ?>"
                                data-category="<?php echo $p['category']; ?>"
                                data-stock="<?php echo $stockAttr; ?>"
                                data-status="<?php echo $p['status']; ?>">

                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono font-bold text-gray-500"><?php echo $p['sku']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($p['name']); ?></span>
                                        <span class="text-xs text-gray-400"><?php echo htmlspecialchars($p['brand']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1 min-w-[120px]">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-800"><?php echo number_format($avail); ?> <span class="font-normal text-gray-400">/ <?php echo number_format($total); ?></span></span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full <?php echo $barColor; ?> rounded-full" style="width:<?php echo $barPct; ?>%"></div>
                                        </div>
                                        <span class="badge <?php echo $stockClass; ?> mt-0.5" style="font-size:9px"><?php echo $stockLabel; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900">&#x20B9;<?php echo number_format($p['price']); ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $p['status']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="edit.php?id=<?php echo $p['id']; ?>" class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-black" title="Edit Product">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <button onclick="deleteProduct(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['assigned_stock']; ?>)" class="p-2 hover:bg-red-50 rounded-lg transition-colors text-gray-400 hover:text-red-500" title="Delete Product">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div id="no-results" class="hidden py-16 text-center">
                        <i data-lucide="search-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No products match your filters.</p>
                    </div>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-400">Showing <span id="visible-count"><?php echo $totalProducts; ?></span> of <?php echo $totalProducts; ?> products</p>
                    <p class="text-xs text-gray-400">Total available value: <span class="font-semibold text-gray-700">&#x20B9;<?php echo number_format($totalValue); ?></span></p>
                </div>
            </div>

        </div><!-- /flex-col -->
    </main>
</div>

<script>
    lucide.createIcons();

    /* ── Filters ─────────────────────────────────────────────────────── */
    function applyFilters() {
        const q        = document.getElementById('search-input').value.toLowerCase().trim();
        const stock    = document.getElementById('filter-stock').value;
        const status   = document.getElementById('filter-status').value;
        const rows     = document.querySelectorAll('.product-row');
        let visible    = 0;

        rows.forEach(row => {
            const nameMatch  = !q || row.dataset.name.includes(q) || row.dataset.sku.includes(q) || row.dataset.brand.includes(q);
            const stockMatch = !stock  || row.dataset.stock === stock;
            const statMatch  = !status || row.dataset.status === status;

            if (nameMatch && stockMatch && statMatch) {
                row.classList.remove('hidden-row');
                visible++;
            } else {
                row.classList.add('hidden-row');
            }
        });

        document.getElementById('visible-count').textContent = visible;
        document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        const fc = document.getElementById('filter-count');
        fc.textContent = (q || cat || stock || status) ? `${visible} result${visible!==1?'s':''}` : '';
    }

    /* ── Delete Guard ────────────────────────────────────────────────── */
    function deleteProduct(id, name, assignedStock) {
        if (assignedStock > 0) {
            Swal.fire({
                title: 'Cannot Delete',
                html: `<b>${name}</b> is currently assigned to <b>${assignedStock}</b> professional${assignedStock>1?'s':''}.<br>Return all kits before deleting.`,
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111'
            });
            return;
        }
        Swal.fire({
            title: `Delete "${name}"?`,
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280'
        }).then(r => {
            if (r.isConfirmed) {
                Swal.fire({ title: 'Deleted!', text: `${name} has been removed.`, icon: 'success', confirmButtonColor: '#111', timer: 1800, showConfirmButton: false });
            }
        });
    }

    /* ── Export ──────────────────────────────────────────────────────── */
    function exportProducts() {
        Swal.fire({ title: 'Exporting…', text: 'Generating CSV export.', icon: 'info', timer: 1500, showConfirmButton: false, confirmButtonColor: '#111' });
    }
</script>
</body>
</html>

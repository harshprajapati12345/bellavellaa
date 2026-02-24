<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Offer · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .submenu { display: none; } .submenu.open { display: block; }
    .chevron-rotate { transform: rotate(180deg); }
    .sidebar-item-hover:hover { background-color: #ffffff; color: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .toggle-switch { position: relative; display: inline-block; width: 38px; height: 22px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e5e7eb; border-radius: 999px; transition: 0.25s; }
    .toggle-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.25s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    input:checked + .toggle-slider { background: #000; }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Add Offer'; include '../includes/header.php'; ?>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
      <a href="/bellavella/offers/" class="hover:text-gray-600 transition-colors">Offers</a>
      <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
      <span class="text-gray-900 font-medium">Add Offer</span>
    </nav>

    <form onsubmit="event.preventDefault(); handleSubmit();" class="w-full">
      <div class="bg-white rounded-[2rem] shadow-[0_2px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-10">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Left: Offer Details (2 Columns) -->
            <div class="lg:col-span-2 space-y-8">
              <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-6">Offer Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <!-- Name -->
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Offer Name <span class="text-red-400">*</span></label>
                    <input type="text" required
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                      placeholder="e.g. Seasonal Bridal Glow">
                  </div>

                  <!-- Code -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Promo Code <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="text" required uppercase id="offer-code"
                        class="w-full pl-4 pr-12 py-3 border border-gray-200 rounded-xl text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                        placeholder="BRIDE500">
                        <button type="button" onclick="generateCode()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </button>
                    </div>
                  </div>

                  <!-- Type -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type <span class="text-red-400">*</span></label>
                    <div class="flex p-1 bg-gray-100 rounded-2xl w-full">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="discount_type" value="flat" checked class="hidden peer">
                            <div class="flex items-center justify-center py-2 text-sm font-semibold rounded-xl transition-all peer-checked:bg-white peer-checked:text-black peer-checked:shadow-sm text-gray-400 hover:text-gray-600">
                                Flat (₹)
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="discount_type" value="percentage" class="hidden peer">
                            <div class="flex items-center justify-center py-2 text-sm font-semibold rounded-xl transition-all peer-checked:bg-white peer-checked:text-black peer-checked:shadow-sm text-gray-400 hover:text-gray-600">
                                Percentage (%)
                            </div>
                        </label>
                    </div>
                  </div>

                  <!-- Value -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Value <span class="text-red-400">*</span></label>
                    <input type="number" required
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                      placeholder="e.g. 500">
                  </div>

                  <!-- Min order -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Min Order Value</label>
                    <input type="number" value="0"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all"
                      placeholder="e.g. 1000">
                  </div>

                  <!-- Start Date -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="date"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
                  </div>

                  <!-- End Date -->
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="date"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-6">Terms & Usage</h3>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Description / Terms</label>
                  <textarea rows="4" 
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all resize-none"
                    placeholder="Brief details about or limitations of the offer..."></textarea>
                </div>
              </div>
            </div>

            <!-- Right: Status & Limits -->
            <div class="space-y-8 lg:border-l lg:border-gray-50 lg:pl-10">
              <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-6">Availability</h3>
                
                <!-- Status -->
                <div class="flex items-center justify-between py-4 px-4 bg-gray-50 rounded-2xl mb-4">
                  <div>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Active Status</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Offer is live and claimable</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>

                <!-- Featured -->
                <div class="flex items-center justify-between py-4 px-4 bg-gray-50 rounded-2xl mb-8">
                  <div>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Highlight Offer</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Show on homepage banner</p>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox">
                    <span class="toggle-slider"></span>
                  </label>
                </div>

                <!-- Limits -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Max Usage</label>
                        <input type="number" value="100"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
                        <p class="text-[10px] text-gray-400 mt-1">Total times code can be used</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Usage Per User</label>
                        <input type="number" value="1"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-all">
                        <p class="text-[10px] text-gray-400 mt-1">Limit per customer</p>
                    </div>
                </div>
              </div>

              <!-- Tip Card -->
              <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100">
                <div class="flex items-center gap-2 text-blue-600 mb-2">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    <p class="text-xs font-bold uppercase tracking-wider">Builder Tip</p>
                </div>
                <p class="text-[11px] text-blue-600/70 leading-relaxed">
                    Percentage offers are great for higher ticket items, while Flat discounts work better for low-cost services.
                </p>
              </div>
            </div>

          </div>
        </div>

        <!-- Action Bar -->
        <div class="px-6 py-5 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-3">
          <a href="/bellavella/offers/" class="px-6 py-2.5 text-gray-500 text-sm font-medium hover:text-black transition-colors">Discard</a>
          <button type="submit" class="px-10 py-2.5 bg-black text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all shadow-lg shadow-black/10 flex items-center gap-2">
            Create Offer
            <i data-lucide="check" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
    </form>
  </main>
</div>

<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for(let i=0; i<8; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('offer-code').value = code;
  }

  function handleSubmit() {
    Swal.fire({
      title: 'Offer Created!',
      text: 'Promotion has been added and is now active.',
      icon: 'success',
      confirmButtonColor: '#000',
      confirmButtonText: 'Back to Offers'
    }).then(() => {
      window.location.href = '/bellavella/offers/';
    });
  }
</script>
</body>
</html>

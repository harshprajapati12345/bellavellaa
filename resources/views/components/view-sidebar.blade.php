<div id="view-sidebar-backdrop" class="fixed inset-0 z-[70] hidden bg-black/30 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

<div id="view-sidebar" class="fixed top-0 right-0 h-full w-full max-w-[450px] bg-white z-[80] shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0">
        <div>
            <h3 id="sidebar-title" class="text-xl font-bold text-gray-900 leading-tight">Details</h3>
            <p id="sidebar-subtitle" class="text-xs text-gray-400 mt-0.5 tracking-wide uppercase font-semibold">Viewing Record</p>
        </div>
        <button id="close-sidebar" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Content -->
    <div id="sidebar-content" class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-8">
        <!-- Content will be injected here via AJAX -->
        <div id="sidebar-loader" class="hidden flex flex-col items-center justify-center h-full py-20 text-center">
            <div class="w-12 h-12 border-4 border-gray-100 border-t-black rounded-full animate-spin mb-4"></div>
            <p class="text-sm text-gray-500 font-medium">Fetching details...</p>
        </div>
        
        <div id="sidebar-data" class="space-y-8">
            <!-- Dynamic Sections -->
        </div>
    </div>

    <!-- Footer -->
    <div id="sidebar-footer" class="px-6 py-5 border-t border-gray-100 bg-gray-50/50 flex items-center gap-3">
        <button id="sidebar-edit-btn" class="flex-1 bg-black text-white px-5 py-3 rounded-xl hover:bg-gray-800 transition-all font-semibold text-sm shadow-lg shadow-black/10 flex items-center justify-center gap-2">
            <i data-lucide="pencil" class="w-4 h-4"></i> Edit Record
        </button>
    </div>
</div>

<style>
    #view-sidebar-backdrop.active { display: block; opacity: 1; }
    #view-sidebar.active { transform: translateX(0); }
    
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>

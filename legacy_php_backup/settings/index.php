<?php
$configPath = '../config/app_theme.json';

// Ensure directory exists
if (!is_dir('../config')) {
    mkdir('../config', 0777, true);
}

// Default colors
$defaultColors = [
    'primary_color' => '#000000',
    'secondary_color' => '#6B7280',
    'background_color' => '#FFFFFF'
];

// Load existing config
if (file_exists($configPath)) {
    $currentColors = json_decode(file_get_contents($configPath), true);
} else {
    $currentColors = $defaultColors;
    file_put_contents($configPath, json_encode($currentColors, JSON_PRETTY_PRINT));
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newColors = [
        'primary_color' => $_POST['primary_color'] ?? $defaultColors['primary_color'],
        'secondary_color' => $_POST['secondary_color'] ?? $defaultColors['secondary_color'],
        'background_color' => $_POST['background_color'] ?? $defaultColors['background_color']
    ];
    
    if (file_put_contents($configPath, json_encode($newColors, JSON_PRETTY_PRINT))) {
        $currentColors = $newColors;
        $message = 'Settings saved successfully!';
    } else {
        $message = 'Error saving settings.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>App Theme Settings · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@0.344.0/dist/umd/lucide.min.js"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    
    .color-input-wrapper {
        position: relative;
        width: 100%;
        height: 48px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        background: #fff;
        transition: all 0.2s;
    }
    .color-input-wrapper:hover { border-color: #000; }
    .color-input-wrapper input[type="color"] {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 150%;
        height: 150%;
        cursor: pointer;
        padding: 0;
        border: none;
    }
    .color-info {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        background: rgba(255,255,255,0.9);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid #f3f4f6;
    }

    /* Phone Mockup Styling */
    .phone-mockup {
        width: 280px;
        height: 560px;
        background: #000;
        border-radius: 36px;
        padding: 10px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        position: relative;
        transform-origin: top center;
        transition: transform 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .phone-mockup {
            transform: scale(0.85);
            margin-bottom: -60px; /* Offset the scale shrink */
        }
    }

    @media (max-width: 480px) {
        .phone-mockup {
            transform: scale(0.75);
            margin-bottom: -120px;
        }
    }

    .phone-screen {
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 28px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .phone-header {
        height: 60px;
        padding: 24px 16px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .phone-content {
        padding: 20px 16px;
        flex: 1;
        overflow-y: auto;
    }
    .phone-btn {
        width: 100%;
        padding: 14px;
        border-radius: 14px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        margin-top: 20px;
    }
    .phone-card {
        border-radius: 18px;
        padding: 14px;
        margin-bottom: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
    }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">

<div class="flex min-h-screen relative">
  <?php include '../includes/sidebar.php'; ?>

  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php $pageTitle = 'Settings'; include '../includes/header.php'; ?>

    <div class="max-w-5xl mx-auto">
      <div class="flex flex-col md:flex-row gap-8 items-start mt-4">
        
        <!-- Left: Form -->
        <div class="flex-1 bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">App Theme Configuration</h2>
            <p class="text-sm text-gray-400 mt-1">Customize the look and feel of your client-side application.</p>
          </div>

          <?php if ($message): ?>
          <div class="mb-6 p-4 rounded-xl <?php echo strpos($message, 'Error') !== false ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'; ?> text-sm font-medium flex items-center gap-2">
            <i data-lucide="<?php echo strpos($message, 'Error') !== false ? 'alert-circle' : 'check-circle'; ?>" class="w-4 h-4"></i>
            <?php echo $message; ?>
          </div>
          <?php endif; ?>

          <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              
              <!-- Primary Color -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                  Primary Color
                </label>
                <div class="color-input-wrapper">
                  <input type="color" name="primary_color" id="primary_color" value="<?php echo htmlspecialchars($currentColors['primary_color']); ?>" oninput="updatePreview()">
                  <div class="color-info" id="primary_hex"><?php echo $currentColors['primary_color']; ?></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for buttons, icons, and main UI highlights.</p>
              </div>

              <!-- Secondary Color -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                  Secondary Color
                </label>
                <div class="color-input-wrapper">
                  <input type="color" name="secondary_color" id="secondary_color" value="<?php echo htmlspecialchars($currentColors['secondary_color']); ?>" oninput="updatePreview()">
                  <div class="color-info" id="secondary_hex"><?php echo $currentColors['secondary_color']; ?></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for accents, secondary buttons, and text highlights.</p>
              </div>

              <!-- Background Color -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                  App Background
                </label>
                <div class="color-input-wrapper">
                  <input type="color" name="background_color" id="background_color" value="<?php echo htmlspecialchars($currentColors['background_color']); ?>" oninput="updatePreview()">
                  <div class="color-info" id="background_hex"><?php echo $currentColors['background_color']; ?></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 ml-1">The primary background color for pages and containers.</p>
              </div>

            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center gap-3">
              <button type="submit" class="flex-1 bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/5 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Save Configuration
              </button>
              <button type="button" onclick="window.location.reload()" class="px-6 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition-all">
                Reset
              </button>
            </div>
          </form>
        </div>

        <!-- Right: Preview -->
        <div class="w-full md:w-auto flex flex-col items-center flex-shrink-0 md:sticky md:top-8">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">APK Live Preview</p>
            <div class="phone-mockup">
                <div class="phone-screen" id="preview-screen">
                    <div class="phone-header">
                        <i data-lucide="menu" class="w-5 h-5" id="preview-icon-menu"></i>
                        <span class="text-sm font-bold" id="preview-app-name">Bellavella</span>
                        <i data-lucide="shopping-bag" class="w-5 h-5 font-bold" id="preview-icon-cart"></i>
                    </div>
                    <div class="phone-content space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold" id="preview-title">Exclusive Offers</h3>
                            <span class="text-xs font-semibold" id="preview-see-all">See All</span>
                        </div>
                        
                        <div class="phone-card bg-white" id="preview-card-1">
                            <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i data-lucide="sparkles" class="w-6 h-6" id="preview-icon-svc"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="h-3 w-2/3 bg-gray-100 rounded-full mb-2"></div>
                                    <div class="h-2 w-1/3 bg-gray-50 rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        <div class="phone-card bg-white" id="preview-card-2">
                             <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i data-lucide="heart" class="w-6 h-6" id="preview-icon-heart"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="h-3 w-1/2 bg-gray-100 rounded-full mb-2"></div>
                                    <div class="h-2 w-1/4 bg-gray-50 rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        <div class="phone-btn text-white shadow-md shadow-black/5" id="preview-main-btn">
                            Book Appointment
                        </div>

                        <div class="grid grid-cols-4 gap-2 mt-8">
                             <div class="flex flex-col items-center gap-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100" id="preview-nav-1">
                                    <i data-lucide="home" class="w-4 h-4 text-white" id="preview-nav-icon-1"></i>
                                </div>
                             </div>
                             <div class="flex flex-col items-center gap-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50" id="preview-nav-2">
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                </div>
                             </div>
                             <div class="flex flex-col items-center gap-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50" id="preview-nav-3">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                </div>
                             </div>
                             <div class="flex flex-col items-center gap-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50" id="preview-nav-4">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
                <!-- Notch -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-6 bg-black rounded-b-xl flex items-end justify-center pb-1">
                    <div class="w-8 h-1 bg-white/20 rounded-full"></div>
                </div>
            </div>
            <p class="mt-4 text-[10px] text-gray-400 font-medium">Visual representation only</p>
        </div>

      </div>
    </div>
  </main>
</div>

<script>
  function updatePreview() {
    const primary = document.getElementById('primary_color').value;
    const secondary = document.getElementById('secondary_color').value;
    const background = document.getElementById('background_color').value;
    
    // Update labels
    document.getElementById('primary_hex').textContent = primary;
    document.getElementById('secondary_hex').textContent = secondary;
    document.getElementById('background_hex').textContent = background;
    
    // Update Preview
    const screen = document.getElementById('preview-screen');
    const mainBtn = document.getElementById('preview-main-btn');
    const seeAll = document.getElementById('preview-see-all');
    const nav1 = document.getElementById('preview-nav-1');
    const title = document.getElementById('preview-title');
    const menuIcon = document.getElementById('preview-icon-menu');
    const cartIcon = document.getElementById('preview-icon-cart');
    const svcIcon = document.getElementById('preview-icon-svc');
    const heartIcon = document.getElementById('preview-icon-heart');
    
    // Background
    screen.style.backgroundColor = background;
    
    // Primary
    mainBtn.style.backgroundColor = primary;
    nav1.style.backgroundColor = primary;
    title.style.color = '#111827'; // Keep title dark
    menuIcon.style.color = primary;
    cartIcon.style.color = primary;
    svcIcon.style.color = primary;
    
    // Secondary
    seeAll.style.color = secondary;
    heartIcon.style.color = secondary;
    
    // Auto-adjust text color base on background brightness (simplified)
    const isDarkBg = isColorDark(background);
    document.getElementById('preview-app-name').style.color = isDarkBg ? '#fff' : '#000';
    document.getElementById('preview-title').style.color = isDarkBg ? '#fff' : '#000';
  }

  function isColorDark(hex) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness < 128;
  }

  lucide.createIcons({ attrs: { 'stroke-width': 2 } });
  updatePreview();
</script>
<script src="/bella/assets/js/app.js"></script>

</body>
</html>

<?php
/**
 * professionals/verification/review.php — Verification Review Page
 */
$pageTitle = 'Review Verification';
$id = $_GET['id'] ?? 0;

// Mock Data (In production, fetch from DB based on $id)
$req = null;
$requests = [
    1 => ['id'=>1,'name'=>'Anjali Mehta','avatar'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80','email'=>'anjali.m@example.com','phone'=>'+91 98765 43210','aadhaar'=>'XXXX XXXX 4321','pan'=>'ABCDE1234F','submitted'=>'2024-02-10','status'=>'Pending','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80','selfie'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80'],
    2 => ['id'=>2,'name'=>'Meera Pillai','avatar'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80','email'=>'meera.p@example.com','phone'=>'+91 98765 67890','aadhaar'=>'XXXX XXXX 8765','pan'=>'FGHIJ5678K','submitted'=>'2024-02-08','status'=>'Rejected','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80','selfie'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=600&q=80'],
    3 => ['id'=>3,'name'=>'Priya Sharma','avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80','email'=>'priya.s@example.com','phone'=>'+91 98765 11223','aadhaar'=>'XXXX XXXX 1234','pan'=>'LMNOP9012Q','submitted'=>'2024-01-20','status'=>'Approved','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80','selfie'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80'],
    4 => ['id'=>4,'name'=>'Sunita Rao','avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80','email'=>'sunita.r@example.com','phone'=>'+91 98765 33445','aadhaar'=>'XXXX XXXX 5678','pan'=>'RSTUV3456W','submitted'=>'2024-01-15','status'=>'Approved','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80','selfie'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=600&q=80'],
    5 => ['id'=>5,'name'=>'Kavita Joshi','avatar'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80','email'=>'kavita.j@example.com','phone'=>'+91 98765 55667','aadhaar'=>'XXXX XXXX 9012','pan'=>'XYZAB7890C','submitted'=>'2024-02-14','status'=>'Pending','aadhaar_front'=>'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80','aadhaar_back'=>'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80','pan_img'=>'https://images.unsplash.com/photo-1554224155-1a8a10e29e4c?auto=format&fit=crop&w=600&q=80','selfie'=>'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80'],
];

if (isset($requests[$id])) {
    $req = $requests[$id];
} else {
    // Fallback or Redirect
    header('Location: index.php');
    exit;
}

$statusColor = match($req['status']) { 'Approved'=>'emerald', 'Pending'=>'amber', default=>'red' };
$status      = $req['status'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review: <?php echo $req['name']; ?> · Verification</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .doc-card { transition: all 0.2s; border: 2px solid transparent; }
    .doc-card:hover { border-color: #000; transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    .doc-img { cursor: zoom-in; }
  </style>
</head>
<body class="antialiased selection:bg-gray-200">
<div class="flex min-h-screen relative">
  <?php include '../../includes/sidebar.php'; ?>
  <main class="flex-1 lg:ml-72 p-4 lg:p-8 overflow-y-auto">
    <?php include '../../includes/header.php'; ?>
    
    <div class="max-w-5xl mx-auto flex flex-col gap-8">
      
      <!-- Top Navigation -->
      <div class="flex items-center gap-4">
        <a href="index.php" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm">
          <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
        </a>
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Review Submission</h2>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-sm text-gray-400">Application #<?php echo str_pad($req['id'], 6, '0', STR_PAD_LEFT); ?></span>
            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
            <span class="text-sm font-medium text-<?php echo $statusColor; ?>-600"><?php echo $status; ?></span>
          </div>
        </div>
        <div class="ml-auto flex items-center gap-3">
            <?php if ($status === 'Pending'): ?>
            <button onclick="requestReupload()" class="px-5 py-2.5 rounded-xl bg-amber-500 text-white hover:bg-amber-600 font-medium text-sm transition-all shadow-lg shadow-amber-500/20">Request Changes</button>
            <button onclick="rejectDoc()" class="px-5 py-2.5 rounded-xl bg-red-500 text-white hover:bg-red-600 font-medium text-sm transition-all shadow-lg shadow-red-500/20">Reject</button>
            <button onclick="approveDoc()" class="px-5 py-2.5 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 font-medium text-sm transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Approve Verification</button>
            <?php else: ?>
            <span class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-500 font-medium text-sm cursor-not-allowed">Review Closed</span>
            <?php endif; ?>
        </div>
      </div>

      <!-- Applicant Profile Card -->
      <div class="bg-white rounded-[2rem] p-6 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col md:flex-row items-center md:items-start gap-6">
        <img src="<?php echo $req['avatar']; ?>" class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-50 bg-gray-100" alt="">
        <div class="flex-1 text-center md:text-left space-y-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900"><?php echo $req['name']; ?></h3>
                <p class="text-sm text-gray-400">Submitted on <?php echo date('F d, Y \a\t h:i A', strtotime($req['submitted'])); ?></p>
            </div>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                <div class="px-4 py-2 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm"><i data-lucide="mail" class="w-4 h-4 text-gray-400"></i></div>
                    <div class="text-left"><p class="text-xs text-gray-400">Email Address</p><p class="text-sm font-medium text-gray-900"><?php echo $req['email']; ?></p></div>
                </div>
                <div class="px-4 py-2 rounded-xl bg-gray-50 border border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm"><i data-lucide="phone" class="w-4 h-4 text-gray-400"></i></div>
                    <div class="text-left"><p class="text-xs text-gray-400">Phone Number</p><p class="text-sm font-medium text-gray-900"><?php echo $req['phone']; ?></p></div>
                </div>
            </div>
        </div>
      </div>

      <!-- Verification Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column: Identity Documents -->
        <div class="space-y-8">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="file-check" class="w-4 h-4 text-gray-400"></i> Identity Documents
            </h3>
            
            <!-- Aadhaar -->
            <div class="bg-white rounded-[2rem] p-6 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center"><i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i></div>
                        <div><p class="font-semibold text-gray-900">Aadhaar Card</p><p class="text-xs text-gray-400">Govt. ID Proof</p></div>
                    </div>
                    <span class="font-mono text-sm font-medium bg-gray-100 px-3 py-1 rounded-lg text-gray-600 tracking-wide"><?php echo $req['aadhaar']; ?></span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-400 pl-1">Front Side</p>
                        <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-40 relative group">
                            <img src="<?php echo $req['aadhaar_front']; ?>" class="doc-img w-full h-full object-cover" onclick="previewImage(this.src)" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white"></i></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-400 pl-1">Back Side</p>
                        <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-40 relative group">
                            <img src="<?php echo $req['aadhaar_back']; ?>" class="doc-img w-full h-full object-cover" onclick="previewImage(this.src)" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAN -->
            <div class="bg-white rounded-[2rem] p-6 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center"><i data-lucide="file-text" class="w-5 h-5 text-amber-600"></i></div>
                        <div><p class="font-semibold text-gray-900">PAN Card</p><p class="text-xs text-gray-400">Tax ID Proof</p></div>
                    </div>
                    <span class="font-mono text-sm font-medium bg-gray-100 px-3 py-1 rounded-lg text-gray-600 tracking-wide"><?php echo $req['pan']; ?></span>
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-medium text-gray-400 pl-1">Front Side</p>
                    <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 h-56 relative group">
                        <img src="<?php echo $req['pan_img']; ?>" class="doc-img w-full h-full object-cover" onclick="previewImage(this.src)" alt="">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Selfie Review -->
        <div class="space-y-8">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-gray-400"></i> Face Match Verification
            </h3>

            <div class="bg-white rounded-[2rem] p-6 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-100 h-full">
                <div class="flex items-center gap-4 mb-6 bg-violet-50 p-4 rounded-xl border border-violet-100">
                    <i data-lucide="scan-face" class="w-6 h-6 text-violet-600"></i>
                    <div>
                        <p class="text-sm font-semibold text-violet-900">Selfie Verification</p>
                        <p class="text-xs text-violet-600/80">Compare the live selfie with the ID photo to verify identity.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Live Selfie -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-bold text-gray-900 uppercase tracking-widest">Live Selfie</span>
                            <span class="text-[10px] font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Uploaded Today</span>
                        </div>
                        <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 aspect-[3/4] relative group shadow-inner">
                            <img src="<?php echo $req['selfie']; ?>" class="doc-img w-full h-full object-cover" onclick="previewImage(this.src)" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white"></i></div>
                        </div>
                    </div>

                    <!-- ID Photo Crop (Simulated) -->
                    <div class="space-y-3">
                         <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-bold text-gray-900 uppercase tracking-widest">ID Photo</span>
                            <span class="text-[10px] font-medium bg-blue-50 text-blue-600 px-2 py-0.5 rounded">From Aadhaar</span>
                        </div>
                        <div class="doc-card rounded-2xl overflow-hidden bg-gray-50 aspect-[3/4] relative group shadow-inner">
                            <!-- Using Aadhaar front as source for comparison -->
                            <img src="<?php echo $req['aadhaar_front']; ?>" class="doc-img w-full h-full object-cover" onclick="previewImage(this.src)" alt="">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none"><i data-lucide="zoom-in" class="text-white"></i></div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs font-medium text-gray-400 mb-3 text-center uppercase tracking-widest">Verification Checklist</p>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black">
                            <span class="text-sm text-gray-700">Photos match (Person in selfie is person in ID)</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black">
                            <span class="text-sm text-gray-700">ID text is clear and readable</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-100">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black">
                            <span class="text-sm text-gray-700">Documents are valid and not expired</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>
  lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });

  function previewImage(src) {
    Swal.fire({
      imageUrl: src,
      imageAlt: 'Document Preview',
      width: 'auto',
      showConfirmButton: false,
      showCloseButton: true,
      background: 'transparent',
      backdrop: 'rgba(0,0,0,0.8)'
    });
  }

  function approveDoc() {
    Swal.fire({ title: 'Approve Professional?', text: "This will enable them to accept orders.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#9ca3af', confirmButtonText: 'Yes, Approve' })
      .then((result) => {
        if (result.isConfirmed) {
          Swal.fire({ title: 'Verified!', text: 'Professional has been approved.', icon: 'success', confirmButtonColor: '#000' }).then(() => { window.location.href = 'index.php'; });
        }
      });
  }

  function rejectDoc() {
    Swal.fire({ title: 'Reject Verification', input: 'textarea', inputPlaceholder: 'Reason for rejection...', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Reject Application' })
      .then((result) => {
        if (result.isConfirmed) {
          Swal.fire({ title: 'Rejected', text: 'Application has been rejected.', icon: 'error', confirmButtonColor: '#000' }).then(() => { window.location.href = 'index.php'; });
        }
      });
  }

  function requestReupload() {
    Swal.fire({ title: 'Request Re-upload', input: 'checkbox', inputPlaceholder: 'Notify via Email & SMS', inputValue: 1, text: 'Professional will be asked to re-submit documents.', icon: 'info', showCancelButton: true, confirmButtonColor: '#000', confirmButtonText: 'Send Request' })
      .then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Sent', text: 'Re-upload request sent successfully.', icon: 'success', confirmButtonColor: '#000' });
        }
      });
  }
</script>
</body>
</html>

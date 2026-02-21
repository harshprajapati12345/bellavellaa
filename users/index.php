<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="/bellavella/assets/css/style.css">
  <style>
    body { font-family: 'Inter', sans-serif; background: #F6F6F6; }
    ::-webkit-scrollbar { width: 0px; background: transparent; }
    .table-row { transition: background 0.15s; }
    .table-row:hover { background: #fafafa; }
    /* Status toggle */
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
    <?php $pageTitle = 'Users'; include '../includes/header.php'; ?>
    <?php
    $users = [
      ['id'=>1,'name'=>'Ananya Kapoor','email'=>'ananya@example.com','phone'=>'+91 99887 76655','city'=>'Delhi','status'=>'Active','joined'=>'2023-08-20','orders'=>12,'avatar'=>'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'],
      ['id'=>2,'name'=>'Priya Sharma','email'=>'priya.s@example.com','phone'=>'+91 98765 43210','city'=>'Mumbai','status'=>'Active','joined'=>'2023-09-15','orders'=>5,'avatar'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'],
      ['id'=>3,'name'=>'Rahul Verma','email'=>'rahul.v@example.com','phone'=>'+91 91234 56789','city'=>'Bangalore','status'=>'Inactive','joined'=>'2023-11-05','orders'=>2,'avatar'=>'https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'],
      ['id'=>4,'name'=>'Sneha Gupta','email'=>'sneha.g@example.com','phone'=>'+91 88997 76655','city'=>'Delhi','status'=>'Active','joined'=>'2024-01-10','orders'=>8,'avatar'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'],
      ['id'=>5,'name'=>'Vikram Singh','email'=>'vikram.s@example.com','phone'=>'+91 77665 54433','city'=>'Jaipur','status'=>'Active','joined'=>'2024-02-01','orders'=>15,'avatar'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'],
    ];
    ?>

    <div class="flex flex-col gap-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-2xl font-semibold text-gray-900 tracking-tight">Users</h2><p class="text-sm text-gray-400 mt-0.5">Manage customer accounts</p></div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-black/5 transition-all">
          </div>
          <a href="create.php" class="flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-full hover:bg-gray-800 transition-all font-medium text-sm shadow-lg shadow-black/10"><i data-lucide="plus" class="w-4 h-4"></i> Add User</a>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80">
                <th class="px-5 py-4 text-left w-10"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer"></th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">User</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Phone</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Location</th>
                <th class="px-5 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-widest">Orders</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-widest">Joined</th>
                <th class="px-5 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($users as $u): ?>
              <tr class="table-row border-b border-gray-50">
                <td class="px-5 py-4"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer"></td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <img src="<?php echo $u['avatar']; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-100" alt="">
                    <div><p class="text-sm font-semibold text-gray-900"><?php echo $u['name']; ?></p><p class="text-xs text-gray-500"><?php echo $u['email']; ?></p></div>
                  </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $u['phone']; ?></td>
                <td class="px-5 py-4 text-sm text-gray-600"><?php echo $u['city']; ?></td>
                <td class="px-5 py-4 text-center"><span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><?php echo $u['orders']; ?></span></td>
                <td class="px-5 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $u['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600'; ?>"><?php echo $u['status']; ?></span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-500"><?php echo date('d M Y', strtotime($u['joined'])); ?></td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="view.php?id=<?php echo $u['id']; ?>" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 flex items-center justify-center"><i data-lucide="eye" class="w-3.5 h-3.5"></i></a>
                    <a href="edit.php?id=<?php echo $u['id']; ?>" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-black hover:text-white hover:border-black transition-all flex items-center justify-center"><i data-lucide="pencil" class="w-3.5 h-3.5"></i></a>
                    <a href="delete.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Delete user?')" class="w-8 h-8 rounded-lg border border-red-100 text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="/bellavella/assets/js/app.js"></script>
<script>lucide.createIcons({ attrs: { 'stroke-width': 1.5 } });</script>
</body>
</html>

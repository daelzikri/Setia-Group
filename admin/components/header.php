<?php
//
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Mendapatkan nama halaman saat ini untuk penanda aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Setia Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #051094; border-radius: 4px; }
        
        .nav-link { transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { 
            background-color: rgba(255,255,255,0.1); 
            border-right: 4px solid white; 
        }
    </style>
</head>
<body class="bg-gray-50 flex">

    <aside class="w-64 bg-[#051094] text-white h-screen fixed overflow-y-auto z-50 flex flex-col shadow-xl">
        <div class="p-6 text-center border-b border-blue-900 bg-[#040d7a]">
            <h2 class="text-xl font-bold tracking-widest uppercase">SETIA ADMIN</h2>
            <p class="text-xs text-blue-300 mt-1">
                <i class="fa-solid fa-circle-user mr-1"></i> 
                <?= $_SESSION['username'] ?? 'Administrator' ?>
            </p>
        </div>
        
        <nav class="flex-1 py-4">
            <ul class="space-y-1">
                <li>
                    <a href="index.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'index.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-gauge w-5 text-center"></i> Dashboard
                    </a>
                </li>
                
                <li class="px-6 pt-5 pb-2 text-[10px] uppercase text-blue-300 font-bold tracking-wider">Master Data</li>
                <li>
                    <a href="admin.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'admin.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-shield w-5 text-center"></i> Data Admin
                    </a>
                </li>
                <li>
                    <a href="profil.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'profil.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-building w-5 text-center"></i> Profil Perusahaan
                    </a>
                </li>
                <li>
                    <a href="tim.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'tim.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-id-card-clip w-5 text-center"></i> Tim Kami
                    </a>
                </li>
                <li>
                    <a href="klien.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'klien.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-handshake w-5 text-center"></i> Data Klien
                    </a>
                </li>

                <li class="px-6 pt-5 pb-2 text-[10px] uppercase text-blue-300 font-bold tracking-wider">Event Organizer</li>
                <li>
                    <a href="event.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'event.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i> Data Event
                    </a>
                </li>
                <li>
                    <a href="galeri.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'galeri.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-images w-5 text-center"></i> Galeri Event
                    </a>
                </li>

                <li class="px-6 pt-5 pb-2 text-[10px] uppercase text-blue-300 font-bold tracking-wider">Inventory</li>
                <li>
                    <a href="kategori_item.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'kategori_item.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-tags w-5 text-center"></i> Kategori Item
                    </a>
                </li>
                <li>
                    <a href="item.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'item.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-box-open w-5 text-center"></i> Item Sewa
                    </a>
                </li>

                <li class="px-6 pt-5 pb-2 text-[10px] uppercase text-blue-300 font-bold tracking-wider">Feedback</li>
                <li>
                    <a href="pesan.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'pesan.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-envelope w-5 text-center"></i> Pesan Masuk
                    </a>
                </li>
                <li>
                    <a href="testimoni.php" class="nav-link flex items-center px-6 py-3 gap-3 text-sm font-medium <?= $current_page == 'testimoni.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-comment-dots w-5 text-center"></i> Testimoni
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-blue-900 bg-[#040d7a]">
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg text-sm transition font-bold shadow-lg">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <div class="ml-64 w-full min-h-screen flex flex-col transition-all duration-300">
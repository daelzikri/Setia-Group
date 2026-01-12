<?php
//
// Pastikan koneksi database tersedia
if (!isset($pdo)) {
    $config_path = __DIR__ . "/config/database.php";
    if (file_exists($config_path)) {
        require_once $config_path;
    } else {
        require_once "../config/database.php";
    }
}

// Ambil Data Profil Perusahaan
$stmt_prof = $pdo->prepare("SELECT * FROM profil_perusahaan WHERE id_profil = 1");
$stmt_prof->execute();
$profil = $stmt_prof->fetch();

// Default Data jika kosong
$brand          = $profil['nama_brand'] ?? 'SETIA GROUP';
$resmi          = $profil['nama_resmi'] ?? 'PT Setia Group Indonesia';
$desc           = $profil['deskripsi_intro'] ?? 'Partner terbaik untuk setiap momen istimewa Anda.';
$alamat         = $profil['alamat'] ?? 'Indonesia';
// Tambahkan variabel alamat cabang
$alamat_cabang  = $profil['alamat_cabang'] ?? ''; 
$email          = $profil['email'] ?? 'info@setiagroup.com';
$telepon        = $profil['telepon'] ?? '08123456789';
$whatsapp       = $profil['whatsapp'] ?? '081917192999';
$sosmed         = $profil['sosial_media'] ?? '#';
?>

<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">

<style>
    .footer-font { font-family: 'Lora', serif; }
    .footer-heading { font-family: 'Merriweather', serif; }
</style>

<footer class="bg-white pt-20 pb-10 border-t-2 border-[#051094] footer-font">
    <div class="container mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
            
            <div class="md:col-span-5">
                <h3 class="text-2xl font-bold text-[#051094] mb-6 footer-heading uppercase tracking-tight">
                    <?= htmlspecialchars($brand) ?>
                </h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-8 max-w-sm">
                    <?= htmlspecialchars($desc) ?>
                </p>
                <div class="flex gap-4">
                    <h4 class="text-xs font-bold text-[#051094] uppercase tracking-widest mt-2 mr-2">Follow Us</h4>
                    <?php if($sosmed && $sosmed != '#'): ?>
                    <a href="<?= htmlspecialchars($sosmed) ?>" target="_blank" class="w-9 h-9 rounded-full bg-[#051094] text-white flex items-center justify-center hover:bg-black transition-all duration-300 shadow-lg">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    <a href="#" class="w-9 h-9 rounded-full bg-[#051094] text-white flex items-center justify-center hover:bg-black transition-all duration-300 shadow-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            
            <div class="md:col-span-3 md:col-start-7">
                <h4 class="text-sm font-bold uppercase tracking-[0.15em] text-[#051094] mb-6 footer-heading">Quick Links</h4>
                <ul class="space-y-4 text-sm text-gray-500">
                    <li>
                        <a href="index.php" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="event.php" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> Event
                        </a>
                    </li>
                    <li>
                        <a href="services.php" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> Services
                        </a>
                    </li>
                    <li>
                        <a href="index.php#testimoni" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> Testimoni
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> About Us
                        </a>
                    </li>
                    <li>
                        <a href="kontak.php" class="hover:text-[#051094] hover:pl-2 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i> Contact
                        </a>
                    </li>
                </ul>
            </div>

            <div class="md:col-span-3">
                <h4 class="text-sm font-bold uppercase tracking-[0.15em] text-[#051094] mb-6 footer-heading">Contact Info</h4>
                <ul class="space-y-6 text-sm text-gray-500">
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#051094] flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Head Office</span>
                            <span class="leading-relaxed"><?= nl2br(htmlspecialchars($alamat)) ?></span>
                        </div>
                    </li>

                    <?php if(!empty($alamat_cabang)): ?>
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#051094] flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fa-solid fa-map-location-dot text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Branch Office</span>
                            <span class="leading-relaxed"><?= nl2br(htmlspecialchars($alamat_cabang)) ?></span>
                        </div>
                    </li>
                    <?php endif; ?>

                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#051094] flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Phone</span>
                            <span><?= htmlspecialchars($telepon) ?></span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-[#051094] flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Email</span>
                            <span><?= htmlspecialchars($email) ?></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[11px] text-gray-400 font-medium">
                &copy; <?= date('Y') ?> <span class="text-[#051094] font-bold"><?= htmlspecialchars($resmi) ?></span>. All rights reserved.
            </p>
        </div>
    </div>
</footer>
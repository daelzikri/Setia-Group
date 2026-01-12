<?php
require_once "config/database.php";

// 1. Menangkap ID Event dari URL
$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Ambil Informasi Event Lengkap (termasuk tanggal, lokasi, klien)
// Join dengan tabel klien untuk mendapatkan nama klien
$sql = "SELECT e.*, k.nama_klien, k.perusahaan 
        FROM event e 
        LEFT JOIN klien k ON e.id_klien = k.id_klien 
        WHERE e.id_event = ?";
$stmt_event = $pdo->prepare($sql);
$stmt_event->execute([$id_event]);
$event_data = $stmt_event->fetch(PDO::FETCH_ASSOC);

// Jika ID tidak valid atau event tidak ditemukan
if (!$event_data) {
    echo "<script>alert('Event tidak ditemukan'); window.location='event.php';</script>";
    exit;
}

// Data Setup
$title_display = $event_data['nama_event'];
// nl2br mengubah baris baru di database menjadi <br> HTML
$desc_display  = nl2br(htmlspecialchars($event_data['deskripsi'])); 
$bg_header     = !empty($event_data['poster_event']) ? $event_data['poster_event'] : 'assets/header.jpg';
$date_display  = date('d F Y', strtotime($event_data['tanggal_waktu']));
$location      = $event_data['lokasi'];
$client_name   = $event_data['nama_klien'] . ($event_data['perusahaan'] ? ' (' . $event_data['perusahaan'] . ')' : '');

// 3. Ambil Foto Dokumentasi
$stmt_galeri = $pdo->prepare("SELECT foto_dokumentasi, keterangan FROM galeri_event WHERE id_event = ?");
$stmt_galeri->execute([$id_event]);
$photos = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri <?= htmlspecialchars($title_display) ?> - Setia Group Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        .hero-font { font-family: 'Playfair Display', serif; }
        
        .fade-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(40px); }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Styling untuk deskripsi yang memiliki list/bullet points dari DB */
        .prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .prose p { margin-bottom: 1rem; line-height: 1.8; }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>

<header class="relative h-[60vh] min-h-[500px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= htmlspecialchars($bg_header) ?>" class="w-full h-full object-cover" alt="Background Header">
        <div class="absolute inset-0 bg-gradient-to-b from-[#051094]/90 via-[#051094]/60 to-[#051094]/90 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto pt-32">
        <div class="fade-up">
            <span class="inline-block py-1 px-4 rounded-full border border-white/20 bg-white/10 backdrop-blur-md text-blue-100 text-[10px] font-bold uppercase tracking-[0.3em] mb-6">
                <?= htmlspecialchars($event_data['kategori_event']) ?>
            </span>
        </div>
        <h1 class="hero-font text-4xl md:text-6xl text-white font-bold mb-4 leading-tight fade-up delay-100 drop-shadow-lg">
            <?= htmlspecialchars($title_display) ?>
        </h1>
        
        <div class="flex flex-wrap justify-center gap-6 text-blue-100/90 text-sm font-medium fade-up delay-200 mt-4">
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-calendar"></i> <?= $date_display ?>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($location) ?>
            </div>
        </div>
    </div>

    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce text-white/50">
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</header>

<section class="py-16 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100">
                    <h3 class="text-[#051094] font-bold text-lg mb-6 border-b pb-2">Detail Event</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Klien</p>
                            <p class="text-gray-800 font-medium"><?= $client_name ?: '-' ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Tanggal</p>
                            <p class="text-gray-800 font-medium"><?= $date_display ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Lokasi</p>
                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($location) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Status</p>
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold uppercase">
                                <?= htmlspecialchars($event_data['status_event']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <h2 class="hero-font text-3xl text-gray-900 font-bold mb-6">Tentang Acara</h2>
                <div class="prose text-gray-600 text-lg leading-relaxed text-justify">
                    <?= $desc_display ?>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="py-20 px-4 bg-gray-50">
    <div class="container mx-auto max-w-7xl">
        <div class="text-center mb-12">
            <h2 class="hero-font text-3xl text-[#051094] font-bold">Dokumentasi Momen</h2>
            <div class="h-1 w-20 bg-[#051094] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 grid-flow-dense auto-rows-[150px] md:auto-rows-[250px]">
            <?php if(!empty($photos)): ?>
                <?php 
                $layouts = [
                    'col-span-2 row-span-2', 
                    'col-span-1',             
                    'col-span-1 row-span-2',  
                    'col-span-1',             
                    'col-span-1 row-span-2',  
                    'col-span-2 md:col-span-2 row-span-1', 
                    'col-span-1',
                    'col-span-1 row-span-2',
                ];
                foreach($photos as $idx => $photo): 
                    $layoutClass = $layouts[$idx % count($layouts)];
                ?>
                <div class="fade-up overflow-hidden rounded-xl md:rounded-[1.5rem] group relative shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer <?php echo $layoutClass; ?>">
                    <img src="<?= htmlspecialchars($photo['foto_dokumentasi']) ?>" 
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                         alt="<?= htmlspecialchars($photo['keterangan']) ?>"
                         loading="lazy">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-[#051094]/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-6">
                        <p class="text-white text-sm font-medium transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <?= htmlspecialchars($photo['keterangan']) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-[2rem] border border-gray-200 shadow-sm">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-regular fa-image text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada dokumentasi untuk event ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-16">
            <a href="index.php#event" class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 px-8 py-3 rounded-full text-sm font-bold uppercase tracking-wider hover:bg-[#051094] hover:text-white hover:border-[#051094] transition-all duration-300">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Event
            </a>
        </div>

    </div>
</section>

<?php include "includes/footer.php"; ?>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target); 
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>

</body>
</html>
<?php
require_once "config/database.php";

/**
 * Logika Pengambilan Data:
 * 1. Ongoing (Current): Mengambil 4 data terbaru (Agar simetris di layout 2 kolom).
 * 2. Archive (Past): Mengambil data setelah 4 data pertama.
 */

// 1. Ambil 4 Event Terbaru (Ongoing/Current)
$stmt_ongoing = $pdo->query("SELECT * FROM event ORDER BY tanggal_waktu DESC LIMIT 4");
$ongoing_events = $stmt_ongoing->fetchAll(PDO::FETCH_ASSOC);

// 2. Ambil Sisa Event (Archive/Past) - Melewati 4 data pertama
$stmt_past = $pdo->query("SELECT * FROM event ORDER BY tanggal_waktu DESC LIMIT 100 OFFSET 4");
$past_events = $stmt_past->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jejak Karya - PT Setia Group Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
            color: #1a1a1a;
        }
        .hero-font { 
            font-family: 'Playfair Display', serif; 
            letter-spacing: -0.02em;
        }
        .fade-in { 
            animation: fadeIn 1s ease-out forwards; 
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .ongoing-border { 
            border-left: 2px solid #051094; 
        }
        .letter-spacing-luxury { 
            letter-spacing: 0.5em; 
        }
    </style>
</head>
<body class="bg-white">
    <?php include "includes/navbar.php"; ?>

    <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#051094] text-white text-center overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="assets/header.jpg" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80'">
        </div>
        
        <div class="container mx-auto px-4 relative z-10 fade-in">
            <span class="text-blue-300 letter-spacing-luxury uppercase text-[8px] md:text-[9px] font-bold mb-4 md:mb-6 block">Our Journey & Timeline</span>
            <h1 class="text-3xl md:text-5xl lg:text-7xl font-bold hero-font mb-4 md:mb-8 leading-tight">
                Dedikasi dalam <br><span class="italic font-light text-blue-200">Setiap Detail.</span>
            </h1>
            <p class="text-blue-100/60 max-w-2xl mx-auto font-light leading-relaxed text-sm md:text-lg px-2">
                Menelusuri jejak kreasi kami—dari visi yang baru saja terwujud hingga perayaan megah yang telah menjadi sejarah.
            </p>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-16 md:h-32 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    <section class="py-12 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-4 md:gap-6 mb-8 md:mb-16 fade-in">
                <div class="h-[1px] w-10 md:w-16 bg-[#051094]"></div>
                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold hero-font text-gray-900 tracking-tight">
                    Latest Engagements <span class="text-gray-400 font-light italic ml-2 md:ml-3 text-sm md:text-base">— Proyek Terbaru</span>
                </h2>
            </div>

            <?php if(count($ongoing_events) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">
                <?php foreach($ongoing_events as $event): ?>
                <div class="ongoing-border group bg-gray-50 p-6 md:p-8 rounded-tr-[2rem] md:rounded-tr-[3rem] rounded-bl-[1.5rem] md:rounded-bl-[2rem] rounded-br-[1rem] flex flex-col transition-all duration-500 hover:shadow-2xl hover:bg-white fade-in">
                    <div class="w-full h-48 md:h-64 rounded-2xl md:rounded-3xl overflow-hidden shadow-inner mb-6 md:mb-8 relative">
                        <img src="<?= htmlspecialchars($event['poster_event']) ?>" 
                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000"
                             alt="<?= htmlspecialchars($event['nama_event']) ?>">
                    </div>
                    <div>
                        <div class="flex justify-end items-center mb-4 md:mb-6">
                            <span class="text-[9px] md:text-[10px] text-gray-400 font-medium uppercase tracking-widest">
                                <?= date('M Y', strtotime($event['tanggal_waktu'])) ?>
                            </span>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4 hero-font leading-snug"><?= htmlspecialchars($event['nama_event']) ?></h3>
                        <div class="flex items-center gap-3 text-gray-500 text-[10px] md:text-xs font-light mb-6 md:mb-8">
                            <i class="fa-solid fa-location-dot text-[#051094]/50"></i>
                            <span class="italic tracking-wide"><?= htmlspecialchars($event['lokasi']) ?></span>
                        </div>
                        
                        <div class="mt-auto pt-4 md:pt-6 border-t border-gray-100">
                            <a href="galeri.php?id=<?= $event['id_event'] ?>" class="bg-[#051094] text-white px-5 py-3 md:px-6 md:py-3 rounded-full text-[8px] md:text-[9px] font-bold uppercase tracking-widest hover:bg-black transition-all inline-block">
                                <i class="fa-solid fa-camera-retro mr-2"></i> Lihat Galeri Foto
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="py-8 md:py-12 px-6 md:px-10 border border-dashed border-gray-200 rounded-[2rem] text-center">
                    <p class="text-gray-400 italic font-light text-sm md:text-base">Belum ada data event terbaru untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-16 md:py-32 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center text-center mb-12 md:mb-24 fade-in">
                <span class="text-[#051094] font-bold text-[9px] md:text-[10px] uppercase letter-spacing-luxury mb-4 md:mb-6 block">Portfolio Archive</span>
                <h2 class="text-3xl md:text-4xl lg:text-6xl font-bold hero-font text-gray-900 mb-6 md:mb-8 leading-tight">Jejak Mahakarya</h2>
                <div class="w-16 md:w-20 h-[2px] bg-[#051094] mb-6 md:mb-10"></div>
                <p class="text-gray-500 max-w-2xl font-light text-sm md:text-lg">Kumpulan perayaan yang telah kami kurasi sebelumnya, membentuk fondasi reputasi kami saat ini.</p>
            </div>

            <?php if(count($past_events) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10">
                <?php foreach($past_events as $event): ?>
                <div class="group relative aspect-[3/4] md:aspect-[16/9] rounded-[2rem] md:rounded-[3rem] overflow-hidden bg-black shadow-2xl fade-in">
                    <img src="<?= htmlspecialchars($event['poster_event']) ?>" 
                         class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-30 transition-all duration-1000 group-hover:scale-110"
                         alt="<?= htmlspecialchars($event['nama_event']) ?>">
                    
                    <div class="absolute inset-0 p-6 md:p-10 flex flex-col justify-end text-white transition-all duration-500">
                        <span class="text-[8px] md:text-[9px] font-bold uppercase tracking-[0.4em] text-blue-300 mb-2 md:mb-4 block translate-y-4 group-hover:translate-y-0 transition-transform">Success Story</span>
                        <h3 class="text-xl md:text-3xl font-bold hero-font leading-tight mb-4 md:mb-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-500 group-hover:text-blue-100">
                            <?= htmlspecialchars($event['nama_event']) ?>
                        </h3>
                        <div class="overflow-hidden h-0 group-hover:h-12 transition-all duration-700 opacity-0 group-hover:opacity-100">
                            <p class="text-[10px] md:text-sm text-blue-100/60 font-light italic">
                                Lokasi: <?= htmlspecialchars($event['lokasi']) ?>
                            </p>
                        </div>
                        <div class="mt-4 md:mt-6 flex items-center justify-between pt-3 md:pt-4 border-t border-white/20">
                            <span class="text-[9px] md:text-[10px] text-white/40 uppercase tracking-widest font-bold">
                                <?= date('Y', strtotime($event['tanggal_waktu'])) ?>
                            </span>
                            <i class="fa-solid fa-arrow-right-long text-white translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500"></i>
                        </div>
                    </div>
                    <a href="galeri.php?id=<?= $event['id_event'] ?>" class="absolute inset-0 z-30"></a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="text-center text-gray-400 italic text-sm md:text-base">Arsip karya lainnya sedang dalam tahap kurasi digital.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-16 md:py-32 bg-white text-center">
        <div class="container mx-auto px-4 fade-in">
            <h2 class="text-2xl md:text-3xl lg:text-5xl font-bold hero-font text-gray-900 mb-8 md:mb-10 leading-tight">Siap Menciptakan <br>Momen Bersejarah Berikutnya?</h2>
            <a href="kontak.php" class="inline-block bg-[#051094] text-white px-10 py-4 md:px-14 md:py-5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-[0.3em] hover:bg-black transition-all shadow-xl hover:-translate-y-1">
                Hubungi Kurator Kami
            </a>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    </script>
</body>
</html>
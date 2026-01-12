<?php
require_once "config/database.php";

// 1. QUERY DATA (Hanya mengambil Kategori Barang/Sewa)
$stmt_cat_item = $pdo->query("SELECT * FROM kategori_item_sewa ORDER BY nama_kategori ASC");
$item_categories = $stmt_cat_item->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan & Produksi - Setia Group Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #faf9f6; color: #1a1a1a; }
        .hero-font { font-family: 'Playfair Display', serif; }
        .fade-up { animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(40px); }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Smooth Scroll Behavior */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>

    <header class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden bg-[#051094]">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1505236858219-8359eb29e329?q=80&w=2070&auto=format&fit=crop" 
                 class="w-full h-full object-cover opacity-30 mix-blend-overlay" alt="Stage Production">
            <div class="absolute inset-0 bg-gradient-to-b from-[#051094]/80 via-[#051094]/50 to-[#faf9f6]"></div>
        </div>

        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto mt-20">
            <span class="inline-block border border-blue-200/30 bg-blue-900/20 backdrop-blur-md text-blue-100 px-6 py-2 rounded-full text-[10px] font-bold uppercase tracking-[0.3em] mb-6 fade-up">
                Production & Rental
            </span>
            <h1 class="text-5xl md:text-7xl font-bold text-white hero-font mb-8 leading-tight fade-up delay-100">
                Quality <span class="italic font-light text-blue-200">Equipment.</span>
            </h1>
            <p class="text-blue-100/80 text-lg md:text-xl font-light leading-relaxed max-w-2xl mx-auto fade-up delay-200">
                Menyediakan solusi teknis dan inventaris peralatan event terlengkap dengan standar kualitas terbaik untuk menunjang kesuksesan acara Anda.
            </p>
        </div>
    </header>

    <section class="relative z-20 -mt-20 px-6">
        <div class="container mx-auto max-w-5xl">
            <div class="bg-[#051094] text-white text-center p-12 md:p-16 rounded-[3rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                
                <div class="relative z-10">
                    <i class="fa-solid fa-quote-left text-4xl text-blue-400/50 mb-6"></i>
                    <h3 class="text-2xl md:text-3xl hero-font leading-relaxed mb-8 font-light">
                        "Kami memiliki inventaris peralatan produksi sendiri <span class="font-bold italic">(In-House Production)</span> untuk menjamin kualitas, ketersediaan alat, dan harga yang kompetitif bagi klien kami."
                    </h3>
                    <div class="flex items-center justify-center gap-4">
                        <div class="h-[1px] w-12 bg-white/50"></div>
                        <span class="text-xs font-bold tracking-[0.3em] uppercase">Setia Group Production</span>
                        <div class="h-[1px] w-12 bg-white/50"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-6 bg-[#faf9f6]">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center mb-20">
                <span class="text-[#051094] uppercase tracking-[0.3em] text-[10px] font-bold mb-4 block">Our Inventory</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 hero-font">Katalog Perlengkapan</h2>
                <div class="w-20 h-[2px] bg-[#051094] mx-auto mt-6"></div>
                <p class="text-gray-500 max-w-2xl mx-auto mt-6 font-light">
                    Jelajahi berbagai kategori peralatan pendukung event yang kami sediakan.
                </p>
            </div>

            <?php if(empty($item_categories)): ?>
                <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-300">
                    <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada kategori item yang ditambahkan.</p>
                </div>
            <?php else: ?>

                <?php foreach($item_categories as $cat): ?>
                    <?php 
                        // Ambil item berdasarkan kategori ini (Limit 4 untuk preview)
                        $stmt_item = $pdo->prepare("SELECT * FROM item_sewa WHERE id_kategori_item = ? LIMIT 4");
                        $stmt_item->execute([$cat['id_kategori_item']]);
                        $items = $stmt_item->fetchAll(PDO::FETCH_ASSOC);

                        // Jika tidak ada item di kategori ini, skip tampilan kategori
                        if(empty($items)) continue; 
                    ?>
                    
                    <div class="mb-24 last:mb-0">
                        <div class="flex flex-col md:flex-row md:items-center gap-6 mb-10">
                            <div class="flex items-center gap-4">
                                <h3 class="text-2xl font-bold text-gray-800 hero-font"><?= htmlspecialchars($cat['nama_kategori']) ?></h3>
                            </div>
                            <div class="hidden md:block flex-1 h-[1px] bg-gray-200"></div>
                            <p class="text-sm text-gray-500 font-light md:text-right max-w-md">
                                <?= htmlspecialchars($cat['deskripsi'] ?? 'Peralatan profesional berkualitas tinggi.') ?>
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php foreach($items as $item): 
                                // Fallback image jika null
                                $imgItem = !empty($item['gambar_item']) ? $item['gambar_item'] : 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=500&auto=format&fit=crop';
                            ?>
                            <div class="group bg-white rounded-[2rem] p-4 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-50 hover:-translate-y-2 flex flex-col h-full">
                                <div class="relative h-48 rounded-[1.5rem] overflow-hidden mb-5 bg-gray-100">
                                    <img src="<?= htmlspecialchars($imgItem) ?>" class="w-full h-full object-cover mix-blend-multiply group-hover:mix-blend-normal transition-all duration-500" alt="<?= htmlspecialchars($item['nama_item']) ?>">
                                    
                                    </div>
                                
                                <div class="px-2 pb-2 flex-1 flex flex-col">
                                    <h4 class="font-bold text-lg text-gray-900 mb-2 group-hover:text-[#051094] transition-colors">
                                        <?= htmlspecialchars($item['nama_item']) ?>
                                    </h4>
                                    
                                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed">
                                        Professional grade equipment for your event needs.
                                    </p>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 uppercase">Ketersediaan</span>
                                            <span class="text-xs font-bold <?= $item['stok_tersedia'] > 0 ? 'text-green-600' : 'text-red-500' ?>">
                                                <?= $item['stok_tersedia'] > 0 ? 'Ready: '.$item['stok_tersedia'] : 'Full Booked' ?>
                                            </span>
                                        </div>
                                        
                                        <a href="https://wa.me/6281234567890?text=Halo%20Setia%20Group,%20saya%20tertarik%20sewa%20<?= urlencode($item['nama_item']) ?>" target="_blank" class="w-10 h-10 rounded-full bg-[#051094] text-white flex items-center justify-center hover:bg-black transition-all transform hover:scale-110 shadow-md">
                                            <i class="fa-brands fa-whatsapp text-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </section>

    <section class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10 text-center">
            <h2 class="text-4xl md:text-6xl font-bold hero-font text-gray-900 mb-8">Butuh Spesifikasi Khusus?</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mb-12 text-lg font-light">
                Tim teknis kami siap memberikan konsultasi gratis untuk menyesuaikan kebutuhan produksi dengan venue dan anggaran Anda.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="kontak.php" class="bg-[#051094] text-white px-12 py-4 rounded-full text-xs font-bold uppercase tracking-[0.2em] hover:shadow-2xl hover:-translate-y-1 transition-all">
                    Hubungi Kami
                </a>
                <a href="#" class="border border-[#051094] text-[#051094] px-12 py-4 rounded-full text-xs font-bold uppercase tracking-[0.2em] hover:bg-[#051094] hover:text-white transition-all">
                    Download List Harga
                </a>
            </div>
        </div>
        <div class="absolute -bottom-1/2 -right-20 w-[600px] h-[600px] bg-blue-50 rounded-full blur-3xl -z-10"></div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script>
        // Simple Reveal Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-up');
                    entry.target.style.opacity = 1;
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('section, h1, p, span').forEach(el => observer.observe(el));
    </script>
</body>
</html>
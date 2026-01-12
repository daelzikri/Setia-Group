<?php
require_once "config/database.php";

// 1. Ambil Data Profil Perusahaan
$stmt_profil = $pdo->query("SELECT * FROM profil_perusahaan LIMIT 1");
$profil = $stmt_profil->fetch(PDO::FETCH_ASSOC);

// 2. Ambil Data Keunggulan
$keunggulan = [
    [
        'icon' => 'fa-calendar-check',
        'judul' => 'Experience Since 2005',
        'deskripsi' => 'Kami memiliki rekam jejak panjang dalam menangani berbagai event berskala nasional dan internasional.'
    ],
    [
        'icon' => 'fa-users',
        'judul' => 'Professional Team',
        'deskripsi' => 'Didukung oleh tim manajemen yang solid, kreatif, dan tenaga teknis yang tersertifikasi.'
    ],
    [
        'icon' => 'fa-shield-alt',
        'judul' => 'CHSE Certified',
        'deskripsi' => 'Menjalankan standar protokol kebersihan, kesehatan, keselamatan, dan kelestarian lingkungan.'
    ]
];

// 3. Ambil Data Tim
$stmt_tim = $pdo->query("SELECT * FROM tim_kami ORDER BY urutan ASC");
$tim = $stmt_tim->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?= htmlspecialchars($profil['nama_brand']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; color: #1a1a1a; }
        .hero-font { font-family: 'Playfair Display', serif; }
        .fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .accent-line {
            width: 40px;
            height: 2px;
            background-color: #051094;
            margin-bottom: 1rem;
        }

        /* Card Hover Effect Elegant */
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(5, 16, 148, 0.1);
        }

        /* Image Masking for Team */
        .team-img-container {
            overflow: hidden;
            border-radius: 1rem; /* Mobile default */
        }
        @media (min-width: 768px) {
            .team-img-container { border-radius: 20px; }
        }
        
        .team-img-container img {
            transition: transform 0.5s ease;
        }
        .team-img-container:hover img {
            transform: scale(1.05);
        }

        /* Styling Khusus untuk List Misi */
        .misi-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .misi-content li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            font-size: 0.875rem; /* text-sm default mobile */
        }
        @media (min-width: 768px) {
            .misi-content li { margin-bottom: 0.75rem; line-height: 1.6; font-size: 1rem; }
        }

        .misi-content li::before {
            content: "\f00c"; 
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: #60a5fa; 
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-white">
    <?php include "includes/navbar.php"; ?>

    <section class="relative bg-cover bg-center text-white h-[40vh] md:h-[60vh]" style="background-image: url('assets/header.jpg');">
        <div class="absolute inset-0 bg-[#051094]/70"></div> 
        <div class="container mx-auto px-4 h-full flex items-center justify-center relative z-10">
            <div class="text-center fade-in">
                <span class="inline-block tracking-[0.3em] uppercase text-[10px] md:text-sm mb-2 md:mb-4 text-blue-200 font-semibold">Who We Are</span>
                <h1 class="text-3xl md:text-7xl font-bold hero-font">About Us</h1>
            </div>
        </div>
    </section>

    <section class="pt-12 pb-6 md:pt-24 md:pb-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-12 gap-6 md:gap-12 items-start fade-in">
                <div class="md:col-span-5">
                    <div class="accent-line"></div>
                    <span class="text-[#051094] font-bold text-[10px] md:text-xs uppercase tracking-[0.2em] mb-2 md:mb-3 block">Our Story</span>
                    <h2 class="text-2xl md:text-5xl font-bold text-gray-900 hero-font leading-tight">
                        Introduction To <br>
                        <span class="text-[#051094]"><?= htmlspecialchars($profil['nama_brand']) ?></span>
                    </h2>
                </div>
                <div class="md:col-span-7">
                    <p class="text-sm md:text-lg text-gray-600 leading-relaxed font-light">
                        <?= nl2br(htmlspecialchars($profil['deskripsi_lengkap'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8 md:py-12 mb-8 md:mb-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 fade-in">
                
                <div class="p-6 md:p-12 rounded-[1.5rem] md:rounded-[2rem] bg-slate-50 border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                    <div class="absolute top-0 right-0 p-4 md:p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-eye text-6xl md:text-9xl text-[#051094]"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="text-[#051094] font-bold text-[10px] md:text-xs uppercase tracking-widest mb-1 md:mb-2 block">Our Goal</span>
                        <h3 class="text-xl md:text-3xl font-bold hero-font text-gray-900 mb-3 md:mb-6">Visi Kami</h3>
                        <p class="text-sm md:text-lg text-gray-600 leading-relaxed font-light italic">
                            "<?= htmlspecialchars($profil['visi']) ?>"
                        </p>
                    </div>
                </div>

                <div class="p-6 md:p-12 rounded-[1.5rem] md:rounded-[2rem] bg-[#051094] text-white relative overflow-hidden group shadow-xl">
                     <div class="absolute top-0 right-0 p-4 md:p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-bullseye text-6xl md:text-9xl text-white"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="text-blue-200 font-bold text-[10px] md:text-xs uppercase tracking-widest mb-1 md:mb-2 block">Our Way</span>
                        <h3 class="text-xl md:text-3xl font-bold hero-font text-white mb-3 md:mb-6">Misi Kami</h3>
                        <div class="misi-content font-light text-blue-50 text-sm md:text-base">
                            <?= $profil['misi'] ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-8 md:py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8 mb-12 md:mb-24 fade-in">
                <?php foreach($keunggulan as $fitur): ?>
                <div class="feature-card bg-white border border-gray-100 p-6 md:p-8 rounded-[1.5rem] md:rounded-[2rem] shadow-sm flex items-start gap-4 md:gap-6 group hover:border-blue-100 h-full">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-full bg-blue-50 flex items-center justify-center text-[#051094] flex-shrink-0 group-hover:bg-[#051094] group-hover:text-white transition-colors">
                        <i class="fa-solid <?= htmlspecialchars($fitur['icon']) ?> text-sm md:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base md:text-xl font-bold text-gray-900 mb-1 md:mb-2 hero-font"><?= htmlspecialchars($fitur['judul']) ?></h3>
                        <p class="text-xs md:text-sm text-gray-500 leading-relaxed"><?= htmlspecialchars($fitur['deskripsi']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="relative rounded-[1.5rem] md:rounded-[3rem] overflow-hidden h-[250px] md:h-[500px] shadow-2xl fade-in group">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                     alt="Team Working">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-12 h-12 md:w-20 md:h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center cursor-pointer hover:bg-[#051094] hover:text-white transition-all duration-300 animate-pulse">
                        <i class="fa-solid fa-play text-sm md:text-2xl text-white"></i>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="py-12 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            
            <div class="text-center max-w-2xl mx-auto mb-8 md:mb-16 fade-in">
                <div class="flex justify-center mb-2 md:mb-4">
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-[#051094] text-[8px] md:text-[10px] font-bold uppercase tracking-widest">Our Professionals</span>
                </div>
                <h2 class="text-2xl md:text-5xl font-bold text-gray-900 hero-font mb-2 md:mb-4">Team Members</h2>
                <p class="text-xs md:text-base text-gray-500 px-4">Bertemu dengan para ahli yang berdedikasi mewujudkan visi event Anda.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-8">
                <?php foreach($tim as $member): ?>
                <div class="group fade-in">
                    <div class="team-img-container relative h-40 md:h-[350px] mb-3 md:mb-6 bg-gray-200">
                        <?php 
                            $foto = !empty($member['foto']) ? $member['foto'] : 'https://ui-avatars.com/api/?name='.urlencode($member['nama']).'&background=051094&color=fff&size=512';
                        ?>
                        <img src="<?= htmlspecialchars($foto) ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="<?= htmlspecialchars($member['nama']) ?>">
                        
                        <div class="absolute bottom-2 md:bottom-4 left-0 right-0 flex justify-center gap-2 md:gap-3 opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500">
                            <?php if(isset($member['link_instagram']) && !empty($member['link_instagram'])): ?>
                            <a href="<?= $member['link_instagram'] ?>" class="w-6 h-6 md:w-10 md:h-10 bg-white text-[#051094] rounded-full flex items-center justify-center hover:bg-[#051094] hover:text-white transition-colors shadow-lg text-[10px] md:text-base">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <?php endif; ?>

                            <?php if(isset($member['link_linkedin']) && !empty($member['link_linkedin'])): ?>
                            <a href="<?= $member['link_linkedin'] ?>" class="w-6 h-6 md:w-10 md:h-10 bg-white text-[#051094] rounded-full flex items-center justify-center hover:bg-[#051094] hover:text-white transition-colors shadow-lg text-[10px] md:text-base">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-center px-1">
                        <h3 class="text-sm md:text-xl font-bold text-gray-900 hero-font mb-0 md:mb-1 truncate"><?= htmlspecialchars($member['nama']) ?></h3>
                        <p class="text-[8px] md:text-[10px] text-[#051094] uppercase tracking-widest font-semibold"><?= htmlspecialchars($member['jabatan']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <section class="py-12 md:py-20 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-xl md:text-4xl font-bold text-gray-900 hero-font mb-6 md:mb-8">Siap Membuat Event Luar Biasa?</h2>
            <a href="kontak.php" class="btn-elegant bg-[#051094] text-white px-8 py-3 md:px-10 md:py-4 text-xs md:text-base font-bold hover:bg-black inline-block transition shadow-xl tracking-widest rounded-full">
                Hubungi Kami Sekarang
            </a>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script>
        // Simple Intersection Observer for Fade In Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target); 
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0'; // Initial state
            observer.observe(el);
        });

        // Fix initial render opacity
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.animationFillMode = 'forwards';
            });
        });
    </script>
</body>
</html>
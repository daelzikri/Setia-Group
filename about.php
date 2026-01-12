<?php
require_once "config/database.php";

// 1. Ambil Data Profil Perusahaan
$stmt_profil = $pdo->query("SELECT * FROM profil_perusahaan LIMIT 1");
$profil = $stmt_profil->fetch(PDO::FETCH_ASSOC);

// 2. Ambil Data Keunggulan
// Data diambil manual untuk layout, sesuai kode sebelumnya
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
            border-radius: 20px;
        }
        .team-img-container img {
            transition: transform 0.5s ease;
        }
        .team-img-container:hover img {
            transform: scale(1.05);
        }

        /* Styling Khusus untuk List Misi (karena dari database ada tag <ul><li>) */
        .misi-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .misi-content li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }
        .misi-content li::before {
            content: "\f00c"; /* FontAwesome check icon */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: #60a5fa; /* Blue-400 */
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-white">
    <?php include "includes/navbar.php"; ?>

    <section class="relative bg-cover bg-center text-white h-[60vh]" style="background-image: url('assets/header.jpg');">
        <div class="absolute inset-0 bg-[#051094]/70"></div> 
        <div class="container mx-auto px-4 h-full flex items-center justify-center relative z-10">
            <div class="text-center fade-in">
                <span class="inline-block tracking-[0.3em] uppercase text-sm mb-4 text-blue-200 font-semibold">Who We Are</span>
                <h1 class="text-5xl md:text-7xl font-bold hero-font">About Us</h1>
            </div>
        </div>
    </section>

    <section class="pt-24 pb-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-12 gap-12 items-start fade-in">
                <div class="md:col-span-5">
                    <div class="accent-line"></div>
                    <span class="text-[#051094] font-bold text-xs uppercase tracking-[0.2em] mb-3 block">Our Story</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 hero-font leading-tight">
                        Introduction To <br>
                        <span class="text-[#051094]"><?= htmlspecialchars($profil['nama_brand']) ?></span>
                    </h2>
                </div>
                <div class="md:col-span-7">
                    <p class="text-lg text-gray-600 leading-relaxed font-light">
                        <?= nl2br(htmlspecialchars($profil['deskripsi_lengkap'])) ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 mb-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 fade-in">
                
                <div class="p-8 md:p-12 rounded-[2rem] bg-slate-50 border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-eye text-8xl md:text-9xl text-[#051094]"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="text-[#051094] font-bold text-xs uppercase tracking-widest mb-2 block">Our Goal</span>
                        <h3 class="text-3xl font-bold hero-font text-gray-900 mb-6">Visi Kami</h3>
                        <p class="text-lg text-gray-600 leading-relaxed font-light italic">
                            "<?= htmlspecialchars($profil['visi']) ?>"
                        </p>
                    </div>
                </div>

                <div class="p-8 md:p-12 rounded-[2rem] bg-[#051094] text-white relative overflow-hidden group shadow-xl">
                     <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-bullseye text-8xl md:text-9xl text-white"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="text-blue-200 font-bold text-xs uppercase tracking-widest mb-2 block">Our Way</span>
                        <h3 class="text-3xl font-bold hero-font text-white mb-6">Misi Kami</h3>
                        <div class="misi-content font-light text-blue-50">
                            <?= $profil['misi'] ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24 fade-in">
                <?php foreach($keunggulan as $fitur): ?>
                <div class="feature-card bg-white border border-gray-100 p-8 rounded-[2rem] shadow-sm flex items-start gap-6 group hover:border-blue-100 h-full">
                    <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-[#051094] flex-shrink-0 group-hover:bg-[#051094] group-hover:text-white transition-colors">
                        <i class="fa-solid <?= htmlspecialchars($fitur['icon']) ?> text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 hero-font"><?= htmlspecialchars($fitur['judul']) ?></h3>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= htmlspecialchars($fitur['deskripsi']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="relative rounded-[3rem] overflow-hidden h-[400px] md:h-[500px] shadow-2xl fade-in group">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                     alt="Team Working">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center cursor-pointer hover:bg-[#051094] hover:text-white transition-all duration-300 animate-pulse">
                        <i class="fa-solid fa-play text-2xl text-white"></i>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            
            <div class="text-center max-w-2xl mx-auto mb-16 fade-in">
                <div class="flex justify-center mb-4">
                    <span class="px-4 py-1 rounded-full bg-blue-100 text-[#051094] text-[10px] font-bold uppercase tracking-widest">Our Professionals</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 hero-font mb-4">Team Members</h2>
                <p class="text-gray-500">Bertemu dengan para ahli yang berdedikasi mewujudkan visi event Anda menjadi kenyataan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach($tim as $member): ?>
                <div class="group fade-in">
                    <div class="team-img-container relative h-[350px] mb-6 bg-gray-200">
                        <?php 
                            $foto = !empty($member['foto']) ? $member['foto'] : 'https://ui-avatars.com/api/?name='.urlencode($member['nama']).'&background=051094&color=fff&size=512';
                        ?>
                        <img src="<?= htmlspecialchars($foto) ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="<?= htmlspecialchars($member['nama']) ?>">
                        
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3 opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500">
                            <?php if(isset($member['link_instagram']) && !empty($member['link_instagram'])): ?>
                            <a href="<?= $member['link_instagram'] ?>" class="w-10 h-10 bg-white text-[#051094] rounded-full flex items-center justify-center hover:bg-[#051094] hover:text-white transition-colors shadow-lg">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <?php endif; ?>

                            <?php if(isset($member['link_linkedin']) && !empty($member['link_linkedin'])): ?>
                            <a href="<?= $member['link_linkedin'] ?>" class="w-10 h-10 bg-white text-[#051094] rounded-full flex items-center justify-center hover:bg-[#051094] hover:text-white transition-colors shadow-lg">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 hero-font mb-1"><?= htmlspecialchars($member['nama']) ?></h3>
                        <p class="text-sm text-[#051094] uppercase tracking-widest font-semibold text-[10px]"><?= htmlspecialchars($member['jabatan']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <section class="py-20 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 hero-font mb-8">Siap Membuat Event Luar Biasa?</h2>
            <a href="kontak.php" class="btn-elegant bg-[#051094] text-white px-10 py-4 font-bold hover:bg-black inline-block transition shadow-xl tracking-widest rounded-full">
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
<?php
require_once "config/database.php";

// --- 1. Ambil Data Profil Perusahaan ---
$stmt_profil = $pdo->query("SELECT * FROM profil_perusahaan WHERE id_profil = 1");
$profil = $stmt_profil->fetch(PDO::FETCH_ASSOC);

// Format nomor HP untuk WhatsApp (Ubah 08... jadi 628...)
$no_wa = $profil['telepon'];
if (substr($no_wa, 0, 1) == '0') {
    $no_wa = '62' . substr($no_wa, 1);
}

// --- 2. Ambil Event Terbaru (Scheduled) ---
$stmt_event = $pdo->query("SELECT * FROM event 
                     WHERE status_event = 'scheduled' 
                     ORDER BY tanggal_waktu DESC LIMIT 4");
$events = $stmt_event->fetchAll(PDO::FETCH_ASSOC);

// --- 3. Ambil Testimoni ---
$stmt_testi = $pdo->query("SELECT t.*, k.nama_klien, k.perusahaan FROM testimoni t 
                     JOIN klien k ON t.id_klien = k.id_klien 
                     WHERE t.status_tampil = 1 
                     ORDER BY t.created_at DESC LIMIT 3");
$testimonials = $stmt_testi->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profil['nama_brand']) ?> - Event Profesional</title>
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
        
        /* Tombol Bulat (Pill-shaped) */
        .btn-elegant {
            border-radius: 9999px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.875rem;
        }
        .btn-elegant:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(5, 16, 148, 0.2);
        }

        /* Layout Overlap Foto */
        .img-stack {
            position: relative;
            padding-bottom: 10%;
        }
        .img-stack-main {
            width: 85%;
            height: 400px;
            object-fit: cover;
            display: block;
        }
        .img-stack-sub {
            position: absolute;
            width: 55%;
            height: 280px;
            bottom: 0;
            right: 0;
            object-fit: cover;
            border: 12px solid white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .accent-line {
            width: 40px;
            height: 2px;
            background-color: #051094;
            margin-bottom: 1rem;
        }

        /* --- BAGIAN CAROUSEL CLIENT --- */
        @keyframes slide {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .logo-carousel-container {
            overflow: hidden;
            position: relative;
            padding: 20px 0;
        }

        .logo-carousel-container:before, .logo-carousel-container:after {
            content: "";
            position: absolute;
            top: 0;
            width: 200px;
            height: 100%;
            z-index: 2;
        }

        .logo-carousel-container:before {
            left: 0;
            background: linear-gradient(to right, white, transparent);
        }

        .logo-carousel-container:after {
            right: 0;
            background: linear-gradient(to left, white, transparent);
        }

        .logo-track {
            display: flex;
            width: max-content;
            animation: slide 50s linear infinite;
        }

        .logo-track img {
            height: 45px;
            width: auto;
            margin: 0 40px;
            object-fit: contain;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .logo-track img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-white">
    <?php include "includes/navbar.php"; ?>
    
    <section class="relative bg-cover bg-center text-white" style="background-image: url('assets/header.jpg');">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="container mx-auto px-4 py-32 md:py-48 lg:py-64 relative z-10">
            <div class="max-w-4xl mx-auto text-center fade-in">
                <span class="inline-block tracking-[0.3em] uppercase text-xs md:text-sm mb-4 text-blue-300 font-semibold">EVENT PROFESIONAL</span>
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold mb-6 md:mb-8 hero-font leading-tight"><?= htmlspecialchars($profil['nama_brand']) ?></h1>
                <p class="text-lg md:text-xl lg:text-2xl mb-8 md:mb-12 text-gray-200 font-light"><?= htmlspecialchars($profil['tagline']) ?></p>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-24 bg-white overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
                <div class="img-stack fade-in hidden md:block"> <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80" class="img-stack-main" alt="Event setup">
                    <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80" class="img-stack-sub" alt="Detail decor">
                </div>
                <div class="md:hidden fade-in mb-8">
                     <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80" class="w-full h-64 object-cover rounded-2xl shadow-lg" alt="Event setup">
                </div>

                <div class="fade-in">
                    <div class="accent-line"></div>
                    <span class="text-[#051094] font-bold text-xs uppercase tracking-[0.2em] mb-3 block">Raising Comfort to the Highest Level</span>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-6 md:mb-8 hero-font">Welcome to <?= htmlspecialchars($profil['nama_brand']) ?></h2>
                    <div class="text-base md:text-lg text-gray-600 mb-6 leading-relaxed">
                        <?= $profil['deskripsi_intro'] ?>
                    </div>
                    <a href="about.php" class="btn-elegant bg-[#051094] text-white px-8 py-3 md:px-12 md:py-4 font-bold inline-block shadow-lg hover:bg-black transition-colors text-xs md:text-sm">Read More</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-10 md:mb-14 gap-4 md:gap-6">
                <div class="max-w-2xl fade-in">
                    <div class="accent-line"></div>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 hero-font">Event Terbaru Kami</h2>
                    <p class="text-base md:text-lg text-gray-500 leading-relaxed">
                        Eksplorasi dokumentasi event profesional yang telah kami rancang dengan dedikasi tinggi dan kreativitas tanpa batas.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                <?php foreach($events as $event): ?>
                <div class="group relative h-[400px] md:h-[480px] rounded-[2rem] md:rounded-[2.5rem] overflow-hidden shadow-xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent z-10"></div>
                    
                    <img src="<?= htmlspecialchars($event['poster_event']) ?>" 
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         alt="<?= htmlspecialchars($event['nama_event']) ?>">
                    
                    <div class="absolute inset-0 z-20 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 bg-black/40">
                        <a href="galeri.php?id=<?= $event['id_event'] ?>" class="bg-white text-[#051094] px-6 py-3 rounded-full text-[10px] font-bold uppercase tracking-widest hover:bg-[#051094] hover:text-white transition-all transform -translate-y-4 group-hover:translate-y-0 duration-500 shadow-xl">
                            <i class="fa-solid fa-camera-retro mr-2"></i> Lihat Galeri
                        </a>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-20">
                        <h3 class="text-xl md:text-2xl font-bold text-white mb-2 leading-tight">
                            <?= htmlspecialchars($event['nama_event']) ?>
                        </h3>
                        <div class="flex items-center text-white/90 text-xs md:text-sm">
                            <i class="fa-solid fa-location-dot mr-2 text-blue-400"></i>
                            <span class="font-light tracking-wide"><?= htmlspecialchars($event['lokasi']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-12 md:mt-16">
                <a href="event.php" class="btn-elegant bg-[#051094] text-white px-10 py-3 md:px-12 md:py-4 font-bold hover:bg-black inline-block transition shadow-xl tracking-widest text-xs md:text-sm">
                    Lihat Semua Event
                </a>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-24 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 lg:gap-8 divide-x-0 lg:divide-x divide-gray-200">
                <div class="text-center lg:px-8">
                    <p class="text-[9px] md:text-[10px] font-bold text-[#051094] uppercase tracking-[0.3em] mb-2 md:mb-4">Established</p>
                    <h4 class="text-3xl md:text-5xl font-bold text-gray-900 hero-font">2005</h4>
                </div>
                <div class="text-center lg:px-8">
                    <p class="text-[9px] md:text-[10px] font-bold text-[#051094] uppercase tracking-[0.3em] mb-2 md:mb-4">Projects Done</p>
                    <h4 class="text-3xl md:text-5xl font-bold text-gray-900 hero-font">500+</h4>
                </div>
                <div class="text-center lg:px-8">
                    <p class="text-[9px] md:text-[10px] font-bold text-[#051094] uppercase tracking-[0.3em] mb-2 md:mb-4">Global Partners</p>
                    <h4 class="text-3xl md:text-5xl font-bold text-gray-900 hero-font">120+</h4>
                </div>
                <div class="text-center lg:px-8">
                    <p class="text-[9px] md:text-[10px] font-bold text-[#051094] uppercase tracking-[0.3em] mb-2 md:mb-4">Awards Won</p>
                    <h4 class="text-3xl md:text-5xl font-bold text-gray-900 hero-font">15</h4>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-24 bg-white border-y border-gray-50 overflow-hidden">
        <div class="container mx-auto px-4 mb-8 md:mb-12">
            <div class="text-center">
                <span class="text-[#051094] font-bold text-[9px] md:text-[10px] uppercase tracking-[0.5em] mb-3 md:mb-4 block">Trusted by Prestigious Organizations</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 hero-font">Klien & Mitra Strategis</h2>
            </div>
        </div>

        <div class="logo-carousel-container">
            <div class="logo-track" id="logoTrack">
                <img src="assets/client/suzuki.png" alt="suzuki">
                <img src="assets/client/mandiri.png" alt="Mandiri">
                <img src="assets/client/mandalikarc.png" alt="mandalikarc">
                <img src="assets/client/motogp.png" alt="motogp">
                <img src="assets/client/sbk.png" alt="sbk">
                <img src="assets/client/mgpa.png" alt="MGPA">
                <img src="assets/client/dorna.png" alt="dorna">
                <img src="assets/client/ITDC.png" alt="ITDC">
                <img src="assets/client/ducati.png" alt="ducati">
                <img src="assets/client/asean.png" alt="asean">
                <img src="assets/client/bmw.png" alt="bmw">
                <img src="assets/client/bank indonesia.png" alt="Bank indonesia">
                <img src="assets/client/pertamina.png" alt="Pertamina">
                <img src="assets/client/shell.png" alt="shell">
                <img src="assets/client/kominfo.jpg" alt="Kominfo">
                <img src="assets/client/bumn.png" alt="BUMN">
                <img src="assets/client/bankntb.png" alt="bankntb">
                <img src="assets/client/asiaroad.png" alt="asiaroad">
                <img src="assets/client/shelleco.png" alt="shelleco">
                <img src="assets/client/light.png" alt="light">
                <img src="assets/client/mandiri taspen.png" alt="Mandiri Taspen">
                <img src="assets/client/wika.png" alt="Wika">
                <img src="assets/client/hutama karya.png" alt="Hutama Karya">
                <img src="assets/client/angkasapura.jpg" alt="Angkasa Pura">
                <img src="assets/client/kemenparekraf.jpg" alt="Kemenparekraf">
                <img src="assets/client/kemenkumham.png" alt="Kemenkumham">
                <img src="assets/client/kemenkeu.png" alt="Kemenkeu">
                <img src="assets/client/kemensos.png" alt="Kemensos">
                <img src="assets/client/TWMR.png" alt="TWMR">
                <img src="assets/client/fimasia.png" alt="fimasia">
                <img src="assets/client/radical.png" alt="radical">
                <img src="assets/client/dyandra.png" alt="dyandra">
                <img src="assets/client/bkkbn.png" alt="bkkbn">
                <img src="assets/client/pos.png" alt="pos">
                <img src="assets/client/patra.png" alt="patra">
                <img src="assets/client/patrajasa.png" alt="patrajasa">
                <img src="assets/client/elnusa.png" alt="elnusa">
                <img src="assets/client/kesehatan.png" alt="kesehatan">
            </div>
        </div>

        <div class="container mx-auto px-4 mt-8 md:mt-12">
            <p class="text-[9px] md:text-[10px] text-gray-400 font-medium uppercase tracking-[0.2em] md:tracking-[0.3em] text-center italic">
                Serta berbagai instansi pemerintahan, BUMN, dan tokoh publik nasional lainnya.
            </p>
        </div>
    </section>

    <section class="py-20 md:py-32 bg-white" id="testimoni">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto relative">
                <div class="absolute -top-10 md:-top-16 left-1/2 -translate-x-1/2 text-[100px] md:text-[150px] text-gray-50 hero-font select-none z-0">“</div>
                <div class="relative z-10">
                    <div class="flex flex-col items-center text-center">
                        <?php foreach($testimonials as $testi): ?>
                        <div class="mb-16 md:mb-24 last:mb-0">
                            <p class="text-xl md:text-2xl lg:text-3xl text-gray-700 hero-font italic leading-relaxed mb-6 md:mb-10 max-w-4xl mx-auto">
                                "<?= htmlspecialchars($testi['isi_testimoni']) ?>"
                            </p>
                            <div class="inline-flex items-center gap-4">
                                <div class="h-[1px] w-8 bg-[#051094]"></div>
                                <span class="text-xs md:text-sm font-bold text-gray-900 tracking-widest uppercase"><?= htmlspecialchars($testi['nama_klien']) ?></span>
                                <span class="text-[10px] md:text-xs text-gray-400 font-light">— <?= htmlspecialchars($testi['perusahaan']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="relative min-h-[auto] md:min-h-[550px] flex items-center rounded-[2rem] md:rounded-[4rem] overflow-hidden bg-[#051094] py-10 md:py-0">
                
                <div class="absolute inset-0 z-0 opacity-20 grayscale">
                    <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-r from-[#051094] via-[#051094]/95 to-transparent z-10"></div>
                
                <div class="relative z-20 w-full px-6 md:px-12 lg:px-24 grid lg:grid-cols-2 gap-10 md:gap-16 items-center">
                    
                    <div class="text-left">
                        <span class="text-blue-300 font-bold text-[8px] md:text-[10px] uppercase tracking-[0.3em] md:tracking-[0.5em] mb-4 md:mb-6 block">Connect with us</span>
                        
                        <h2 class="text-3xl md:text-6xl font-bold text-white hero-font mb-4 md:mb-8 leading-tight">
                            Menciptakan <br><span class="italic font-light text-blue-200">Legacy</span> Lewat Event.
                        </h2>
                        
                        <p class="text-blue-100/70 text-sm md:text-lg font-light leading-relaxed mb-8 md:mb-12 max-w-md">
                            Kami percaya setiap acara adalah mahakarya. Mari diskusikan bagaimana kami menghidupkan visi Anda menjadi realitas yang megah.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 items-start sm:items-center">
                            <a href="kontak.php" class="bg-white text-[#051094] w-full sm:w-auto px-6 py-3 md:px-10 md:py-5 rounded-full text-[10px] font-bold uppercase tracking-widest hover:shadow-[0_20px_40px_rgba(0,0,0,0.3)] transition-all text-center whitespace-nowrap">
                                Mulai Konsultasi
                            </a>
                            <a href="https://wa.me/<?= $no_wa ?>" class="border border-white/30 text-white w-full sm:w-auto px-6 py-3 md:px-10 md:py-5 rounded-full text-[10px] font-bold uppercase tracking-widest hover:bg-white/10 transition-all text-center flex items-center justify-center gap-2 whitespace-nowrap">
                                <i class="fa-brands fa-whatsapp text-lg"></i> Direct Inquiry
                            </a>
                        </div>
                    </div>
                    
                    <div class="block lg:block mt-6 lg:mt-0 flex justify-center lg:justify-end">
                        <div class="relative scale-90 md:scale-100">
                            <div class="hidden sm:block w-40 h-56 md:w-64 md:h-80 rounded-[2rem] md:rounded-[3rem] border border-white/10 absolute -top-4 -right-4 md:-top-8 md:-right-8 animate-pulse"></div>
                            
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80" 
                                 class="w-48 h-64 md:w-72 md:h-96 object-cover rounded-[2rem] md:rounded-[3rem] shadow-2xl rotate-3 hover:rotate-0 transition-all duration-700 mx-auto">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="py-20 md:py-32 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-12 gap-12 md:gap-20 items-center">
                <div class="lg:col-span-5">
                    <div class="h-[1px] w-12 bg-[#051094] mb-8 md:mb-10"></div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 md:mb-6 hero-font tracking-tight leading-tight">Kunjungi Studio <br><span class="text-gray-400 italic font-light">Kreatif Kami</span></h2>
                    <p class="text-base md:text-lg text-gray-500 font-light leading-relaxed mb-8 md:mb-12">Pintu kami selalu terbuka untuk diskusi inspiratif. Temukan kami di pusat bisnis Jakarta untuk konsultasi privat mengenai rencana acara Anda.</p>
                    
                    <div class="space-y-4 md:space-y-6">
                        <div onclick="updateMap('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.97318578627!2d116.109244474403!3d-8.598572591446587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf10ae56033b%3A0x6100e8701860960e!2sSETIA%20GROUP%20INDONESIA!5e0!3m2!1sid!2sid!4v1768129852325!5m2!1sid!2sid')" 
                             class="flex items-start gap-4 md:gap-6 p-3 md:p-4 rounded-2xl hover:bg-blue-50 cursor-pointer transition-all duration-300 group-addr">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gray-50 group-addr-hover:bg-white flex items-center justify-center text-[#051094] flex-shrink-0 shadow-sm transition-colors text-sm md:text-base">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-[10px] md:text-xs uppercase tracking-widest mb-1 md:mb-2 group-hover:text-[#051094] transition-colors">Head Office (Click to View)</h4>
                                <p class="text-gray-500 text-xs md:text-sm font-light"><?= htmlspecialchars($profil['alamat']) ?></p>
                            </div>
                        </div>

                        <?php if(!empty($profil['alamat_cabang'])): ?>
                        <div onclick="updateMap('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.503543679605!2d106.81430227437585!3d-6.197098693790585!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f69fbffffedd%3A0x98ca409f3f88b2fd!2sThamrin%20Residences!5e0!3m2!1sid!2sid!4v1768130027883!5m2!1sid!2sid')" 
                             class="flex items-start gap-4 md:gap-6 p-3 md:p-4 rounded-2xl hover:bg-blue-50 cursor-pointer transition-all duration-300 group-addr">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gray-50 group-addr-hover:bg-white flex items-center justify-center text-[#051094] flex-shrink-0 shadow-sm transition-colors text-sm md:text-base">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-[10px] md:text-xs uppercase tracking-widest mb-1 md:mb-2 group-hover:text-[#051094] transition-colors">Branch Office (Click to View)</h4>
                                <p class="text-gray-500 text-xs md:text-sm font-light"><?= htmlspecialchars($profil['alamat_cabang']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-start gap-4 md:gap-6 p-3 md:p-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-[#051094] flex-shrink-0 shadow-sm text-sm md:text-base"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-[10px] md:text-xs uppercase tracking-widest mb-1 md:mb-2">Email Inquiry</h4>
                                <p class="text-gray-500 text-xs md:text-sm font-light italic"><?= htmlspecialchars($profil['email']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-7 h-[350px] md:h-[550px] rounded-[2.5rem] md:rounded-[3.5rem] overflow-hidden shadow-2xl border border-gray-100 group">
                    <iframe 
                        id="mapFrame"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.97318578627!2d116.109244474403!3d-8.598572591446587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf10ae56033b%3A0x6100e8701860960e!2sSETIA%20GROUP%20INDONESIA!5e0!3m2!1sid!2sid!4v1768129852325!5m2!1sid!2sid" 
                        class="w-full h-full transition-all duration-1000 border-0" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script>
        // 1. FUNGSI GANTI PETA
        function updateMap(url) {
            const mapFrame = document.getElementById('mapFrame');
            // Efek transisi halus (fade out -> ganti src -> fade in)
            mapFrame.style.opacity = 0;
            setTimeout(() => {
                mapFrame.src = url;
                mapFrame.onload = () => {
                    mapFrame.style.opacity = 1;
                };
            }, 300);
        }

        // 2. OTOMATISASI DUPLIKASI LOGO
        const logoTrack = document.getElementById('logoTrack');
        const originalContent = logoTrack.innerHTML;
        logoTrack.innerHTML = originalContent + originalContent;

        // 3. SMOOTH SCROLL
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if(target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // 4. OPTIMASI INTERSECTION OBSERVER
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target); 
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('section, .fade-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link
    href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Merriweather:wght@300;400;700&display=swap"
    rel="stylesheet">

<style>
    /* Menggunakan font Lora secara umum dan Merriweather untuk logo/judul */
    body,
    #main-navbar,
    #mobile-menu {
        font-family: 'Lora', serif;
    }

    .logo-font {
        font-family: 'Merriweather', serif;
    }

    /* Style saat navbar di-scroll */
    .scrolled {
        background-color: #051094 !important;
        padding-top: 1rem !important; /* setara py-4 */
        padding-bottom: 1rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }

    /* Animasi garis bawah untuk menu desktop */
    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -4px;
        left: 0;
        background-color: white;
        transition: width 0.3s ease;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }

    .nav-link.active {
        opacity: 1 !important;
        font-weight: 700;
    }
</style>

<nav id="main-navbar"
    class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex items-center justify-between text-white transition-all duration-300 bg-transparent">
  
    <div class="flex-shrink-0">
        <a href="index.php" class="flex items-center hover:scale-105 transition-transform duration-300">
            <img src="assets/setiagroup.png" alt="Logo Setia Group Indonesia"
                 class="h-16 md:h-20 w-auto object-contain -my-4">
        </a>
    </div>

    <div class="hidden lg:flex gap-8 text-[14px] font-normal tracking-widest uppercase items-center">
        <a href="index.php"
            class="nav-link opacity-80 hover:opacity-100 <?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a>
        <a href="event.php"
            class="nav-link opacity-80 hover:opacity-100 <?= ($current_page == 'event.php') ? 'active' : '' ?>">Event</a>
        <a href="services.php"
            class="nav-link opacity-80 hover:opacity-100 <?= ($current_page == 'services.php') ? 'active' : '' ?>">Service</a>
        <a href="about.php"
            class="nav-link opacity-80 hover:opacity-100 <?= ($current_page == 'about.php') ? 'active' : '' ?>">About
            Us</a>
        <a href="kontak.php"
            class="nav-link opacity-80 hover:opacity-100 <?= ($current_page == 'kontak.php') ? 'active' : '' ?>">Contact</a>
    </div>

    <div class="flex lg:hidden items-center">
        <button id="mobile-menu-btn" class="text-2xl z-50 relative focus:outline-none">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
    </div>
</nav>

<div id="mobile-overlay"
    class="fixed inset-0 bg-black/60 z-40 hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm"></div>

<div id="mobile-menu"
    class="fixed top-0 right-0 h-full w-80 bg-[#2c3e50] z-50 transform translate-x-full transition-transform duration-500 flex flex-col pt-24 px-8 space-y-6 text-white shadow-2xl">
    <button id="close-menu-btn"
        class="absolute top-6 right-6 text-2xl opacity-70 hover:opacity-100 hover:rotate-90 transition duration-300">
        <i class="fa-solid fa-times"></i>
    </button>

    <div class="text-xs font-sans text-gray-400 uppercase tracking-[0.2em] mb-2 border-b border-white/10 pb-2">Navigasi
        Utama</div>

    <a href="index.php"
        class="text-xl <?= ($current_page == 'index.php') ? 'text-blue-400 font-bold' : 'text-white' ?> hover:translate-x-2 transition-transform">Beranda</a>
    <a href="event.php"
        class="text-xl <?= ($current_page == 'event.php') ? 'text-blue-400 font-bold' : 'text-white' ?> hover:translate-x-2 transition-transform">Event</a>
    <a href="services.php"
        class="text-xl <?= ($current_page == 'services.php') ? 'text-blue-400 font-bold' : 'text-white' ?> hover:translate-x-2 transition-transform">Services</a>
    <a href="about.php"
        class="text-xl <?= ($current_page == 'about.php') ? 'text-blue-400 font-bold' : 'text-white' ?> hover:translate-x-2 transition-transform">About Us</a>
    <a href="kontak.php"
        class="text-xl <?= ($current_page == 'kontak.php') ? 'text-blue-400 font-bold' : 'text-white' ?> hover:translate-x-2 transition-transform">Contact</a>

    <div class="mt-auto pb-10">
        <a href="kontak.php"
            class="flex items-center justify-center gap-3 bg-white text-[#2c3e50] px-6 py-4 rounded-xl font-bold text-sm shadow-lg active:scale-95 transition">
            <i class="fa-solid fa-envelope"></i>
            <span>HUBUNGI KAMI</span>
        </a>
    </div>
</div>

<script>
    // Logika Scroll Navbar
    const navbar = document.getElementById('main-navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
            // PERBAIKAN: Menghapus py-5 saat discroll (bukan py-15)
            navbar.classList.remove('py-5');
        } else {
            navbar.classList.remove('scrolled');
            // PERBAIKAN: Menambah py-5 saat di atas (bukan py-15)
            navbar.classList.add('py-5');
        }
    });

    // Logika Buka/Tutup Menu Mobile
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenuBtn = document.getElementById('close-menu-btn');
    const overlay = document.getElementById('mobile-overlay');

    function openMenu() {
        mobileMenu.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileMenu.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => overlay.classList.add('hidden'), 300);
        document.body.style.overflow = 'auto';
    }

    if (menuBtn) {
        menuBtn.addEventListener('click', openMenu);
        closeMenuBtn.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);
    }
</script>
<?php
//
require_once "../config/database.php";
include "components/header.php";

// 1. Hitung Event (Scheduled vs Total)
$cnt_event_total = $pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();
$cnt_event_scheduled = $pdo->query("SELECT COUNT(*) FROM event WHERE status_event = 'scheduled'")->fetchColumn();

// 2. Hitung Klien
$cnt_klien = $pdo->query("SELECT COUNT(*) FROM klien")->fetchColumn();

// 3. Hitung Item Sewa
$cnt_item = $pdo->query("SELECT COUNT(*) FROM item_sewa")->fetchColumn();

// 4. Hitung Interaksi (Pesan Belum Dibaca & Testimoni Hidden)
$cnt_pesan_unread = $pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE status_dibaca = 0")->fetchColumn();
$cnt_testi_pending = $pdo->query("SELECT COUNT(*) FROM testimoni WHERE status_tampil = 0")->fetchColumn();

// Sapaan Waktu
$jam = date('H');
if ($jam < 12) $sapaan = "Selamat Pagi";
elseif ($jam < 15) $sapaan = "Selamat Siang";
elseif ($jam < 18) $sapaan = "Selamat Sore";
else $sapaan = "Selamat Malam";
?>

<div class="p-8">
    <div class="flex justify-between items-end mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-[#051094] mb-1">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm"><?= $sapaan ?>, <span class="font-bold text-[#051094]"><?= $_SESSION['username'] ?? 'Admin' ?></span>. Berikut ringkasan performa website.</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Hari ini</p>
            <p class="text-lg font-bold text-gray-700"><?= date('l, d F Y') ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Event</p>
                    <h3 class="text-3xl font-bold text-[#051094] mt-2"><?= $cnt_event_total ?></h3>
                    <p class="text-xs text-green-600 mt-1 font-medium bg-green-50 inline-block px-2 py-0.5 rounded-full">
                        <?= $cnt_event_scheduled ?> Scheduled
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#051094] flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Klien Terdaftar</p>
                    <h3 class="text-3xl font-bold text-[#051094] mt-2"><?= $cnt_klien ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Mitra & Customer</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesan Baru</p>
                    <h3 class="text-3xl font-bold <?= $cnt_pesan_unread > 0 ? 'text-red-600' : 'text-gray-700' ?> mt-2"><?= $cnt_pesan_unread ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Belum dibaca</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-envelope<?= $cnt_pesan_unread > 0 ? '-open-text' : '' ?>"></i>
                </div>
            </div>
            <?php if($cnt_pesan_unread > 0): ?>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-red-500"></div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inventory</p>
                    <h3 class="text-3xl font-bold text-[#051094] mt-2"><?= $cnt_item ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Unit Barang</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-gradient-to-r from-[#051094] to-[#0a1abf] rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold mb-2">Kelola Event Organizer</h2>
                <p class="text-blue-200 mb-6 max-w-lg text-sm leading-relaxed">
                    Atur jadwal event, kelola inventaris, dan perbarui galeri dokumentasi agar website utama selalu update untuk klien Anda.
                </p>
                <div class="flex gap-3">
                    <a href="event-tambah.php" class="bg-white text-[#051094] px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-gray-100 transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Buat Event Baru
                    </a>
                    <a href="pesan.php" class="bg-[#2a3bc6] text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-[#3548e0] transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-inbox"></i> Cek Pesan
                    </a>
                </div>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10 text-[10rem] -mr-12 -mb-16 transform rotate-12">
                <i class="fa-solid fa-rocket"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#051094]"></i> Perlu Tindakan
            </h3>
            <div class="space-y-3">
                <?php if($cnt_testi_pending > 0): ?>
                <a href="testimoni.php" class="block bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg hover:bg-yellow-100 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-yellow-800">Review Testimoni</p>
                            <p class="text-xs text-yellow-700 mt-1">Ada <?= $cnt_testi_pending ?> testimoni baru menunggu persetujuan.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-yellow-600 text-xs"></i>
                    </div>
                </a>
                <?php else: ?>
                    <div class="text-center py-4 text-gray-400 text-sm">
                        <p>Tidak ada testimoni pending.</p>
                    </div>
                <?php endif; ?>

                <?php if($cnt_pesan_unread > 0): ?>
                <a href="pesan.php" class="block bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg hover:bg-red-100 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-red-800">Pesan Belum Dibaca</p>
                            <p class="text-xs text-red-700 mt-1">Anda memiliki <?= $cnt_pesan_unread ?> pesan baru dari kontak.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-red-600 text-xs"></i>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
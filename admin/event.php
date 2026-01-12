<?php
//
require_once "../config/database.php";
include "components/header.php";

// --- KONFIGURASI PATH --- //
// Path fisik untuk PHP (Upload & Unlink) - Naik satu folder (../) lalu ke assets/event/
$upload_dir_physical = "../assets/event/"; 
// Path relatif untuk Database & HTML src (tanpa ../)
$upload_dir_db       = "assets/event/";

// --- LOGIC HANDLING --- //
$action = $_GET['action'] ?? 'list';
$id     = $_GET['id'] ?? null;

// 1. DELETE
if ($action == 'delete' && $id) {
    // Ambil info gambar lama untuk dihapus
    $stmt = $pdo->prepare("SELECT poster_event FROM event WHERE id_event = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    // Hapus file fisik jika ada
    if ($row && !empty($row['poster_event'])) {
        $file_to_delete = "../" . $row['poster_event']; // Tambahkan ../ karena path DB mulai dari assets/
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM event WHERE id_event = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Event berhasil dihapus'); window.location='event.php';</script>";
    exit;
}

// 2. SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama       = htmlspecialchars($_POST['nama_event']);
    $kategori   = htmlspecialchars($_POST['kategori_event']); 
    $id_klien   = !empty($_POST['id_klien']) ? $_POST['id_klien'] : NULL;
    $tgl        = $_POST['tanggal_waktu'];
    $lokasi     = htmlspecialchars($_POST['lokasi']);
    $deskripsi  = $_POST['deskripsi'];
    $status     = $_POST['status_event'];
    
    $id_admin   = $_SESSION['id_admin'] ?? 1; 

    // Handle Upload Gambar
    $poster_path_db = $_POST['old_poster'] ?? ''; // Default pakai poster lama
    
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        
        // Buat folder jika belum ada
        if (!is_dir($upload_dir_physical)) {
            mkdir($upload_dir_physical, 0777, true);
        }
        
        // Bersihkan nama file
        $file_ext    = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $file_name   = time() . '_' . rand(100,999) . '.' . $file_ext;
        
        $target_file = $upload_dir_physical . $file_name; // ../assets/event/nama.jpg
        
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
            // Hapus poster lama jika ada dan user mengupload baru
            if (!empty($_POST['old_poster'])) {
                $old_file = "../" . $_POST['old_poster'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            // Set path baru untuk disimpan di DB
            $poster_path_db = $upload_dir_db . $file_name; // assets/event/nama.jpg
        }
    }

    try {
        if ($action == 'edit' && $id) {
            // UPDATE
            $sql = "UPDATE event SET 
                    nama_event=?, kategori_event=?, id_klien=?, 
                    tanggal_waktu=?, lokasi=?, deskripsi=?, 
                    status_event=?, poster_event=? 
                    WHERE id_event=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $kategori, $id_klien, $tgl, $lokasi, $deskripsi, $status, $poster_path_db, $id]);
        } else {
            // INSERT
            $sql = "INSERT INTO event 
                    (nama_event, kategori_event, id_klien, tanggal_waktu, lokasi, deskripsi, status_event, poster_event, id_admin) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $kategori, $id_klien, $tgl, $lokasi, $deskripsi, $status, $poster_path_db, $id_admin]);
        }
        echo "<script>alert('Data Event Berhasil Disimpan!'); window.location='event.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menyimpan: " . $e->getMessage() . "');</script>";
    }
}

// --- DATA FETCHING --- //
$events = $pdo->query("SELECT e.*, k.nama_klien, k.perusahaan 
                       FROM event e 
                       LEFT JOIN klien k ON e.id_klien = k.id_klien 
                       ORDER BY e.tanggal_waktu DESC")->fetchAll();

$kliens = $pdo->query("SELECT * FROM klien ORDER BY nama_klien ASC")->fetchAll();

$editData = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM event WHERE id_event = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Manajemen Event</h1>
            <p class="text-gray-500 text-sm">Atur jadwal, poster, dan detail acara.</p>
        </div>
        
        <?php if($action == 'list'): ?>
            <a href="event.php?action=add" class="bg-[#051094] text-white px-5 py-3 rounded-lg font-bold text-sm hover:bg-black transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Event
            </a>
        <?php else: ?>
            <a href="event.php" class="text-gray-500 hover:text-[#051094] font-bold text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke List
            </a>
        <?php endif; ?>
    </div>

    <?php if($action == 'list'): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 border-b w-24">Poster</th>
                        <th class="p-4 border-b">Detail Event</th>
                        <th class="p-4 border-b">Klien & Kategori</th>
                        <th class="p-4 border-b">Jadwal & Lokasi</th>
                        <th class="p-4 border-b">Status</th>
                        <th class="p-4 border-b text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php foreach($events as $ev): ?>
                    <tr class="hover:bg-blue-50 transition group">
                        <td class="p-4 align-top">
                            <?php if($ev['poster_event']): ?>
                                <img src="../<?= $ev['poster_event'] ?>" class="w-16 h-20 object-cover rounded shadow-sm">
                            <?php else: ?>
                                <div class="w-16 h-20 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs text-center p-1">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 align-top">
                            <h4 class="font-bold text-gray-900 text-base mb-1"><?= htmlspecialchars($ev['nama_event']) ?></h4>
                            <p class="text-gray-500 text-xs line-clamp-2"><?= htmlspecialchars(substr(strip_tags($ev['deskripsi']), 0, 100)) ?>...</p>
                        </td>
                        <td class="p-4 align-top">
                            <span class="inline-block bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded mb-1 uppercase tracking-wide">
                                <?= htmlspecialchars($ev['kategori_event']) ?>
                            </span>
                            <div class="text-xs text-gray-600 font-medium">
                                <i class="fa-solid fa-user-tie mr-1 text-gray-400"></i>
                                <?= $ev['nama_klien'] ? htmlspecialchars($ev['nama_klien']) : '<span class="italic text-gray-400">Internal / Umum</span>' ?>
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <div class="flex items-center gap-2 text-gray-700 font-medium mb-1">
                                <i class="fa-regular fa-calendar text-[#051094]"></i>
                                <?= date('d M Y, H:i', strtotime($ev['tanggal_waktu'])) ?>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 text-xs">
                                <i class="fa-solid fa-location-dot text-red-500"></i>
                                <?= htmlspecialchars($ev['lokasi']) ?>
                            </div>
                        </td>
                        <td class="p-4 align-top">
                            <?php 
                            $statusMap = [
                                'scheduled' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Scheduled'],
                                'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Selesai'],
                                'draft'     => ['bg' => 'bg-gray-200', 'text' => 'text-gray-600', 'label' => 'Draft'],
                                'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Batal']
                            ];
                            $st = $statusMap[$ev['status_event']] ?? $statusMap['draft'];
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide <?= $st['bg'] ?> <?= $st['text'] ?>">
                                <?= $st['label'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-right align-top">
                            <div class="flex justify-end gap-2">
                                <a href="event.php?action=edit&id=<?= $ev['id_event'] ?>" class="text-[#051094] bg-white border border-gray-200 hover:bg-blue-50 w-8 h-8 flex items-center justify-center rounded transition shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="event.php?action=delete&id=<?= $ev['id_event'] ?>" onclick="return confirm('Hapus event ini beserta posternya?')" class="text-red-500 bg-white border border-gray-200 hover:bg-red-50 w-8 h-8 flex items-center justify-center rounded transition shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($events)): ?>
            <div class="p-10 text-center text-gray-400">
                <i class="fa-regular fa-calendar-xmark text-5xl mb-3"></i>
                <p>Belum ada event yang ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 max-w-5xl mx-auto">
        <h2 class="text-xl font-bold mb-6 text-[#051094] border-b pb-4 flex items-center gap-2">
            <i class="fa-solid fa-layer-group"></i>
            <?= $action == 'add' ? 'Formulir Event Baru' : 'Edit Data Event' ?>
        </h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_poster" value="<?= $editData['poster_event'] ?? '' ?>">
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_event" value="<?= $editData['nama_event'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] focus:ring-1 focus:ring-[#051094]" placeholder="Contoh: Wedding Expo 2026" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Event <span class="text-red-500">*</span></label>
                            <input list="kategori_list" name="kategori_event" value="<?= $editData['kategori_event'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Pilih atau ketik manual" required>
                            <datalist id="kategori_list">
                                <option value="Wedding">
                                <option value="Seminar">
                                <option value="Konser">
                                <option value="Workshop">
                                <option value="Corporate">
                                <option value="Pameran">
                            </datalist>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal & Waktu <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tanggal_waktu" value="<?= $editData['tanggal_waktu'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi / Venue <span class="text-red-500">*</span></label>
                            <input type="text" name="lokasi" value="<?= $editData['lokasi'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Nama Gedung / Hotel" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Lengkap</label>
                        <textarea name="deskripsi" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Jelaskan detail acara..."><?= $editData['deskripsi'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="md:col-span-1 space-y-6">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Event</label>
                        <div class="relative">
                            <select name="status_event" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] appearance-none bg-white">
                                <option value="draft" <?= ($editData['status_event']??'') == 'draft' ? 'selected' : '' ?>>Draft (Sembunyikan)</option>
                                <option value="scheduled" <?= ($editData['status_event']??'') == 'scheduled' ? 'selected' : '' ?>>Scheduled (Akan Datang)</option>
                                <option value="completed" <?= ($editData['status_event']??'') == 'completed' ? 'selected' : '' ?>>Completed (Selesai)</option>
                                <option value="cancelled" <?= ($editData['status_event']??'') == 'cancelled' ? 'selected' : '' ?>>Cancelled (Batal)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Klien (Opsional)</label>
                        <p class="text-xs text-gray-500 mb-2">Hubungkan dengan data klien yang terdaftar.</p>
                        <div class="relative">
                            <select name="id_klien" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] appearance-none bg-white">
                                <option value="">-- Tidak / Internal --</option>
                                <?php foreach($kliens as $kl): ?>
                                    <option value="<?= $kl['id_klien'] ?>" <?= ($editData['id_klien']??'') == $kl['id_klien'] ? 'selected' : '' ?>>
                                        <?= $kl['nama_klien'] ?> <?= $kl['perusahaan'] ? '('.$kl['perusahaan'].')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Poster Event</label>
                        
                        <?php if(!empty($editData['poster_event'])): ?>
                            <div class="mb-3 relative group">
                                <img src="../<?= $editData['poster_event'] ?>" class="w-full h-48 object-cover rounded-lg border border-gray-300">
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">Saat ini</div>
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" name="poster" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#051094] hover:file:bg-blue-100 cursor-pointer bg-white border border-gray-300 rounded-lg">
                        <p class="text-[10px] text-gray-400 mt-2">Format: JPG, PNG, WEBP. Max: 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                <a href="event.php" class="px-6 py-3 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Batal</a>
                <button type="submit" class="bg-[#051094] text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Event
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
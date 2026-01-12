<?php
//
require_once "../config/database.php";
include "components/header.php";

// --- KONFIGURASI PATH --- //
// Path fisik untuk PHP (Upload & Hapus) - Naik satu folder (../) lalu ke assets/dokumentasi/
$upload_dir_physical = "../assets/dokumentasi/"; 
// Path relatif untuk Database & HTML src
$upload_dir_db       = "assets/dokumentasi/";

// --- LOGIC HANDLING ---
$action = $_GET['action'] ?? 'list';
$id     = $_GET['id'] ?? null;
$sukses = "";
$error  = "";

// 1. DELETE
if ($action == 'delete' && $id) {
    try {
        // Ambil path file dulu
        $stmt = $pdo->prepare("SELECT foto_dokumentasi FROM galeri_event WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        // Hapus file fisik
        if ($row && !empty($row['foto_dokumentasi'])) {
            $file_to_delete = "../" . $row['foto_dokumentasi']; // Tambahkan ../ agar ketemu path fisiknya
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }

        // Hapus data db
        $stmt = $pdo->prepare("DELETE FROM galeri_event WHERE id_galeri = ?");
        $stmt->execute([$id]);
        
        echo "<script>alert('Foto berhasil dihapus!'); window.location='galeri.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Gagal menghapus: " . $e->getMessage();
    }
}

// 2. SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_event   = $_POST['id_event'];
    $keterangan = htmlspecialchars($_POST['keterangan']);
    
    // Default pakai foto lama jika tidak ada upload baru
    $foto_path_db = $_POST['old_foto'] ?? '';
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        
        // Buat folder jika belum ada
        if (!is_dir($upload_dir_physical)) {
            mkdir($upload_dir_physical, 0777, true);
        }
        
        // Bersihkan nama file
        $file_name   = time() . '_' . rand(100,999) . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['foto']['name']);
        $target_file = $upload_dir_physical . $file_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                
                // Hapus foto lama jika ini adalah proses Edit (Update) dan user upload foto baru
                if (!empty($_POST['old_foto'])) {
                    $old_file_physical = "../" . $_POST['old_foto'];
                    if (file_exists($old_file_physical)) {
                        unlink($old_file_physical);
                    }
                }

                // Set path baru untuk disimpan di DB
                $foto_path_db = $upload_dir_db . $file_name;
            }
        } else {
            $error = "Format file harus JPG, PNG, atau WEBP.";
        }
    }

    if (!$error) {
        try {
            if ($action == 'edit' && $id) {
                // UPDATE
                $sql = "UPDATE galeri_event SET id_event=?, foto_dokumentasi=?, keterangan=? WHERE id_galeri=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_event, $foto_path_db, $keterangan, $id]);
            } else {
                // INSERT
                if ($foto_path_db) {
                    $sql = "INSERT INTO galeri_event (id_event, foto_dokumentasi, keterangan) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$id_event, $foto_path_db, $keterangan]);
                } else {
                    $error = "Foto wajib diupload untuk data baru.";
                }
            }

            if (!$error) {
                echo "<script>window.location='galeri.php';</script>";
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// --- DATA FETCHING ---
$sql_galeri = "SELECT g.*, e.nama_event, e.tanggal_waktu 
               FROM galeri_event g 
               JOIN event e ON g.id_event = e.id_event 
               ORDER BY g.id_galeri DESC";
$galeri_list = $pdo->query($sql_galeri)->fetchAll();

$events_list = $pdo->query("SELECT id_event, nama_event, tanggal_waktu FROM event ORDER BY tanggal_waktu DESC")->fetchAll();

$editData = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM galeri_event WHERE id_galeri = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Galeri Dokumentasi</h1>
            <p class="text-gray-500 text-sm">Upload foto-foto kegiatan event yang telah terlaksana.</p>
        </div>
        <?php if($action == 'list'): ?>
            <a href="galeri.php?action=add" class="bg-[#051094] text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-black transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto
            </a>
        <?php else: ?>
            <a href="galeri.php" class="text-gray-500 hover:text-[#051094] font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded"><?= $error ?></div>
    <?php endif; ?>

    <?php if($action == 'list'): ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach($galeri_list as $g): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition">
            <div class="h-48 overflow-hidden relative">
                <img src="../<?= $g['foto_dokumentasi'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                    <a href="galeri.php?action=edit&id=<?= $g['id_galeri'] ?>" class="bg-white text-blue-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-blue-50" title="Edit">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <a href="galeri.php?action=delete&id=<?= $g['id_galeri'] ?>" onclick="return confirm('Hapus foto ini secara permanen?')" class="bg-white text-red-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50" title="Hapus">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </a>
                </div>
                
                <div class="absolute top-2 left-2">
                    <span class="bg-[#051094] text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm opacity-90">
                        <?= date('d M Y', strtotime($g['tanggal_waktu'])) ?>
                    </span>
                </div>
            </div>
            <div class="p-4">
                <h4 class="text-sm font-bold text-gray-800 truncate mb-1" title="<?= htmlspecialchars($g['nama_event']) ?>">
                    <?= htmlspecialchars($g['nama_event']) ?>
                </h4>
                <p class="text-xs text-gray-500 line-clamp-2">
                    <?= $g['keterangan'] ? htmlspecialchars($g['keterangan']) : '<span class="italic text-gray-300">Tanpa keterangan</span>' ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($galeri_list)): ?>
        <div class="col-span-full text-center py-12 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-300">
            <i class="fa-regular fa-images text-4xl mb-3"></i>
            <p>Belum ada foto dokumentasi.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 max-w-2xl mx-auto">
        <h2 class="text-xl font-bold mb-6 text-[#051094] border-b pb-4">
            <?= $action=='add' ? 'Upload Foto Baru' : 'Edit Foto Dokumentasi' ?>
        </h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_foto" value="<?= $editData['foto_dokumentasi'] ?? '' ?>">
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="id_event" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] appearance-none bg-white" required>
                        <option value="">-- Pilih Event --</option>
                        <?php foreach($events_list as $ev): ?>
                            <option value="<?= $ev['id_event'] ?>" <?= ($editData['id_event']??'') == $ev['id_event'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ev['nama_event']) ?> (<?= date('d/m/Y', strtotime($ev['tanggal_waktu'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">File Foto</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition relative">
                    <?php if(!empty($editData['foto_dokumentasi'])): ?>
                        <div class="mb-3">
                            <img src="../<?= $editData['foto_dokumentasi'] ?>" class="h-32 mx-auto rounded shadow-sm object-cover">
                            <p class="text-xs text-gray-400 mt-2">Foto saat ini</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" name="foto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#051094] file:text-white hover:file:bg-blue-900 cursor-pointer" accept="image/*" <?= $action=='add' ? 'required' : '' ?>>
                    <p class="text-xs text-gray-400 mt-2">Disarankan rasio landscape (16:9). Max 2MB.</p>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan / Caption</label>
                <input type="text" name="keterangan" value="<?= $editData['keterangan'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Contoh: Suasana registrasi peserta...">
            </div>

            <div class="flex gap-4">
                <a href="galeri.php" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold text-center hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="flex-1 bg-[#051094] text-white py-3 rounded-lg font-bold shadow-lg hover:bg-black transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php
//
require_once "../config/database.php";
include "components/header.php";

// --- KONFIGURASI PATH --- //
// Path fisik untuk PHP (Upload & Hapus) - Naik satu folder (../) lalu ke assets/item/
$upload_dir_physical = "../assets/item/"; 
// Path relatif untuk Database & HTML src
$upload_dir_db       = "assets/item/";

$action = $_GET['action'] ?? 'list';
$id     = $_GET['id'] ?? null;
$error  = "";

// --- LOGIC HANDLING --- //

// 1. DELETE
if ($action == 'delete' && $id) {
    try {
        // Ambil info gambar lama
        $stmt = $pdo->prepare("SELECT gambar_item FROM item_sewa WHERE id_item = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        // Hapus file fisik jika ada
        if ($row && !empty($row['gambar_item'])) {
            $file_to_delete = "../" . $row['gambar_item'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM item_sewa WHERE id_item = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Item berhasil dihapus'); window.location='item.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus: " . $e->getMessage() . "'); window.location='item.php';</script>";
    }
}

// 2. SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama       = htmlspecialchars($_POST['nama_item']);
    $kategori   = $_POST['id_kategori_item'];
    $harga      = $_POST['harga_sewa'];
    $stok       = $_POST['stok_tersedia'];
    $id_admin   = $_SESSION['id_admin'] ?? 1; 

    // Handle Upload Gambar
    // Default pakai gambar lama (path DB)
    $gambar_path_db = $_POST['old_gambar'] ?? '';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        
        // Buat folder jika belum ada
        if (!is_dir($upload_dir_physical)) {
            mkdir($upload_dir_physical, 0777, true);
        }
        
        // Bersihkan nama file
        $file_name   = time() . '_' . rand(100,999) . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['gambar']['name']);
        $target_file = $upload_dir_physical . $file_name;
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                
                // Hapus gambar lama jika ada update gambar baru
                if (!empty($_POST['old_gambar'])) {
                    $old_file_physical = "../" . $_POST['old_gambar'];
                    if (file_exists($old_file_physical)) {
                        unlink($old_file_physical);
                    }
                }

                // Set path baru untuk disimpan di DB
                $gambar_path_db = $upload_dir_db . $file_name;
            }
        } else {
            $error = "Format gambar harus JPG, PNG, atau WEBP.";
        }
    }

    if (!$error) {
        try {
            if ($action == 'edit' && $id) {
                // Kolom DB: nama_item, id_kategori_item, harga_sewa, stok_tersedia, gambar_item, id_admin
                $sql = "UPDATE item_sewa SET nama_item=?, id_kategori_item=?, harga_sewa=?, stok_tersedia=?, gambar_item=?, id_admin=? WHERE id_item=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $kategori, $harga, $stok, $gambar_path_db, $id_admin, $id]);
            } else {
                $sql = "INSERT INTO item_sewa (nama_item, id_kategori_item, harga_sewa, stok_tersedia, gambar_item, id_admin) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $kategori, $harga, $stok, $gambar_path_db, $id_admin]);
            }
            echo "<script>alert('Data Item Berhasil Disimpan!'); window.location='item.php';</script>";
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// --- DATA FETCHING --- //

// 1. List Kategori (Untuk Dropdown & Filter)
// Sesuai DB: kategori_item_sewa (id_kategori_item, nama_kategori)
$kategoris = $pdo->query("SELECT * FROM kategori_item_sewa ORDER BY nama_kategori ASC")->fetchAll();

// 2. Logic Filter List Item
$filter_kategori = $_GET['filter_kategori'] ?? '';
$sql_items = "SELECT i.*, k.nama_kategori 
              FROM item_sewa i 
              LEFT JOIN kategori_item_sewa k ON i.id_kategori_item = k.id_kategori_item ";

if ($filter_kategori) {
    $sql_items .= " WHERE i.id_kategori_item = " . intval($filter_kategori);
}

$sql_items .= " ORDER BY i.id_item DESC";
$items = $pdo->query($sql_items)->fetchAll();

// 3. Data Edit
$editData = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM item_sewa WHERE id_item = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Manajemen Item Sewa</h1>
            <p class="text-gray-500 text-sm">Kelola inventaris barang, harga sewa, dan stok.</p>
        </div>
        
        <?php if($action == 'list'): ?>
            <a href="item.php?action=add" class="bg-[#051094] text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-black transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Item
            </a>
        <?php else: ?>
            <a href="item.php" class="text-gray-500 hover:text-[#051094] font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded"><?= $error ?></div>
    <?php endif; ?>

    <?php if($action == 'list'): ?>
    
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <i class="fa-solid fa-filter text-[#051094]"></i>
        <span class="text-sm font-bold text-gray-700">Filter Kategori:</span>
        <form action="" method="GET" class="flex-1 flex gap-2">
            <select name="filter_kategori" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#051094]" onchange="this.form.submit()">
                <option value="">-- Tampilkan Semua --</option>
                <?php foreach($kategoris as $cat): ?>
                    <option value="<?= $cat['id_kategori_item'] ?>" <?= $filter_kategori == $cat['id_kategori_item'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 border-b w-20">Gambar</th>
                        <th class="p-4 border-b">Nama Item</th>
                        <th class="p-4 border-b">Kategori</th>
                        <th class="p-4 border-b text-center">Stok</th>
                        <th class="p-4 border-b">Harga Sewa</th>
                        <th class="p-4 border-b text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php foreach($items as $item): ?>
                    <tr class="hover:bg-blue-50 transition group">
                        <td class="p-4">
                            <?php if($item['gambar_item']): ?>
                                <img src="../<?= $item['gambar_item'] ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-image text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 font-bold text-gray-800">
                            <?= htmlspecialchars($item['nama_item']) ?>
                        </td>
                        <td class="p-4">
                            <?php if($item['nama_kategori']): ?>
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                    <?= htmlspecialchars($item['nama_kategori']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 italic text-xs">Tanpa Kategori</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if($item['stok_tersedia'] > 0): ?>
                                <span class="text-green-600 font-bold"><?= $item['stok_tersedia'] ?></span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-bold">Habis</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 font-medium text-gray-600">
                            Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?> <span class="text-xs text-gray-400">/hari</span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="item.php?action=edit&id=<?= $item['id_item'] ?>" class="text-[#051094] bg-white border border-gray-200 hover:bg-blue-50 w-8 h-8 flex items-center justify-center rounded transition" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="item.php?action=delete&id=<?= $item['id_item'] ?>" onclick="return confirm('Yakin ingin menghapus item ini?')" class="text-red-500 bg-white border border-gray-200 hover:bg-red-50 w-8 h-8 flex items-center justify-center rounded transition" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($items)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            <i class="fa-solid fa-box-open text-4xl mb-2"></i>
                            <p>Belum ada item sewa <?= $filter_kategori ? 'di kategori ini' : '' ?>.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 max-w-2xl mx-auto">
        <h2 class="text-xl font-bold mb-6 text-[#051094] border-b pb-4">
            <?= $action == 'add' ? 'Tambah Item Baru' : 'Edit Item Sewa' ?>
        </h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="old_gambar" value="<?= $editData['gambar_item'] ?? '' ?>">
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kategori <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="id_kategori_item" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] appearance-none bg-white font-bold text-[#051094]" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach($kategoris as $cat): ?>
                            <option value="<?= $cat['id_kategori_item'] ?>" <?= ($editData['id_kategori_item']??'') == $cat['id_kategori_item'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Pilih kategori barang terlebih dahulu.</p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Item / Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_item" value="<?= $editData['nama_item'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Contoh: LED Screen P3.9" required>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Sewa (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold">Rp</span>
                        <input type="number" name="harga_sewa" value="<?= $editData['harga_sewa'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="150000" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Tersedia <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_tersedia" value="<?= $editData['stok_tersedia'] ?? '' ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="0" required min="0">
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Item</label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <?php if(!empty($editData['gambar_item'])): ?>
                        <div class="mb-3 flex items-center gap-4">
                            <img src="../<?= $editData['gambar_item'] ?>" class="w-20 h-20 object-cover rounded border">
                            <span class="text-xs text-gray-500">Gambar saat ini</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="gambar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#051094] file:text-white hover:file:bg-blue-900 cursor-pointer">
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6">
                <a href="item.php" class="px-6 py-3 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Batal</a>
                <button type="submit" class="bg-[#051094] text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-black transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Item
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
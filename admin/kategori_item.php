<?php
require_once "../config/database.php";
include "components/header.php";

// Inisialisasi Variabel
$id_kategori = "";
$nama        = "";
$deskripsi   = "";
$sukses      = "";
$error       = "";
$is_edit     = false;

// --- PROSES: SIMPAN / UPDATE ---
if (isset($_POST['simpan'])) {
    // PERBAIKAN: Hapus htmlspecialchars disini agar tersimpan murni di DB
    $nama      = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'];

    if ($nama) {
        try {
            if (isset($_POST['id_kategori_item']) && $_POST['id_kategori_item'] != "") {
                // UPDATE
                $id_kategori = $_POST['id_kategori_item'];
                $sql = "UPDATE kategori_item_sewa SET nama_kategori=?, deskripsi=? WHERE id_kategori_item=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $deskripsi, $id_kategori]);
                $sukses = "Kategori berhasil diperbarui.";
            } else {
                // INSERT
                $sql = "INSERT INTO kategori_item_sewa (nama_kategori, deskripsi) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $deskripsi]);
                $sukses = "Kategori baru berhasil ditambahkan.";
            }
            // Reset form
            $nama = $deskripsi = "";
        } catch (PDOException $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    } else {
        $error = "Nama kategori wajib diisi.";
    }
}

// --- PROSES: HAPUS ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'];
    try {
        $sql = "DELETE FROM kategori_item_sewa WHERE id_kategori_item = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_hapus]);
        $sukses = "Kategori berhasil dihapus.";
    } catch (PDOException $e) {
        // Menangkap error jika kategori sedang dipakai oleh item
        if ($e->getCode() == '23000') {
            $error = "Gagal menghapus! Kategori ini sedang digunakan oleh item/barang. Hapus atau pindahkan barang terkait terlebih dahulu.";
        } else {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// --- PROSES: EDIT (AMBIL DATA) ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $is_edit = true;
    $id_edit = $_GET['id'];
    $sql = "SELECT * FROM kategori_item_sewa WHERE id_kategori_item = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_edit]);
    $data = $stmt->fetch();

    if ($data) {
        $id_kategori = $data['id_kategori_item'];
        $nama        = $data['nama_kategori'];
        $deskripsi   = $data['deskripsi'];
    }
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Kategori Inventory</h1>
            <p class="text-gray-500 text-sm">Kelompokkan item sewa (Lighting, Sound System, Tenda, dll).</p>
        </div>
    </div>

    <?php if ($sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold"><i class="fa-solid fa-check-circle"></i> Sukses</p>
            <p><?= $sukses ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Gagal</p>
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-bold text-lg text-[#051094] mb-4 border-b pb-2">
                    <?= $is_edit ? 'Edit Kategori' : 'Buat Kategori Baru' ?>
                </h3>
                
                <form action="kategori_item.php" method="POST">
                    <?php if($is_edit): ?>
                        <input type="hidden" name="id_kategori_item" value="<?= $id_kategori ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kategori" value="<?= htmlspecialchars($nama) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="Contoh: Sound System">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" placeholder="Keterangan singkat kategori ini..."><?= htmlspecialchars($deskripsi) ?></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="simpan" class="bg-[#051094] hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg flex-1 transition shadow-lg">
                            <i class="fa-solid fa-save mr-2"></i> Simpan
                        </button>
                        <?php if($is_edit): ?>
                            <a href="kategori_item.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition text-center">
                                Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                                <th class="p-4 border-b font-bold w-10">No</th>
                                <th class="p-4 border-b font-bold">Nama Kategori</th>
                                <th class="p-4 border-b font-bold">Deskripsi</th>
                                <th class="p-4 border-b font-bold text-center">Jumlah Item</th>
                                <th class="p-4 border-b font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            $no = 1;
                            // Query join subquery untuk menghitung jumlah item per kategori
                            $sql = "SELECT k.*, 
                                    (SELECT COUNT(*) FROM item_sewa WHERE id_kategori_item = k.id_kategori_item) as total_item 
                                    FROM kategori_item_sewa k 
                                    ORDER BY k.id_kategori_item ASC";
                            $stmt = $pdo->query($sql);
                            while ($row = $stmt->fetch()) {
                            ?>
                            <tr class="hover:bg-blue-50 transition border-b last:border-0">
                                <td class="p-4 text-gray-500"><?= $no++ ?></td>
                                <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                <td class="p-4 text-gray-600 truncate max-w-xs">
                                    <?= $row['deskripsi'] ? htmlspecialchars($row['deskripsi']) : '<span class="italic text-gray-300">-</span>' ?>
                                </td>
                                <td class="p-4 text-center">
                                    <?php if($row['total_item'] > 0): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">
                                            <?= $row['total_item'] ?> Item
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="kategori_item.php?aksi=edit&id=<?= $row['id_kategori_item'] ?>" class="text-blue-600 hover:bg-blue-100 w-8 h-8 flex items-center justify-center rounded transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="kategori_item.php?aksi=hapus&id=<?= $row['id_kategori_item'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini?')" class="text-red-600 hover:bg-red-100 w-8 h-8 flex items-center justify-center rounded transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if($no == 1): ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    <i class="fa-solid fa-tags text-4xl mb-2"></i>
                                    <p>Belum ada kategori item.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
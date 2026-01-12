<?php
require_once "../config/database.php";
include "components/header.php";

$action = $_GET['action'] ?? 'list';
$id     = $_GET['id'] ?? null;
$error  = "";
$sukses = "";

// --- LOGIC HANDLING --- //

// 1. DELETE
if ($action == 'delete' && $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM testimoni WHERE id_testimoni = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Testimoni berhasil dihapus'); window.location='testimoni.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus: " . $e->getMessage() . "');</script>";
    }
}

// 2. TOGGLE STATUS (Publish/Unpublish Cepat)
if ($action == 'toggle' && $id) {
    try {
        // Ambil status saat ini lalu balik nilainya (0->1, 1->0)
        $sql = "UPDATE testimoni SET status_tampil = NOT status_tampil WHERE id_testimoni = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo "<script>window.location='testimoni.php';</script>";
        exit;
    } catch (PDOException $e) {
        $error = "Gagal mengubah status.";
    }
}

// 3. SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_klien   = $_POST['id_klien'];
    $isi        = htmlspecialchars($_POST['isi_testimoni']);
    $status     = $_POST['status_tampil'];

    if (!$error) {
        try {
            if ($action == 'edit' && $id) {
                $sql = "UPDATE testimoni SET id_klien=?, isi_testimoni=?, status_tampil=? WHERE id_testimoni=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_klien, $isi, $status, $id]);
                $sukses = "Testimoni diperbarui.";
            } else {
                $sql = "INSERT INTO testimoni (id_klien, isi_testimoni, status_tampil) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_klien, $isi, $status]);
                $sukses = "Testimoni baru ditambahkan.";
            }
            // Bersihkan form logic (optional, disini redirect)
            echo "<script>window.location='testimoni.php';</script>";
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// --- DATA FETCHING --- //

// 1. List Testimoni JOIN Klien
$sql_testi = "SELECT t.*, k.nama_klien, k.perusahaan 
              FROM testimoni t 
              JOIN klien k ON t.id_klien = k.id_klien 
              ORDER BY t.status_tampil ASC, t.created_at DESC"; 
              // Ordering: Yang belum tampil (0) di atas, baru berdasarkan tanggal
$testimoni = $pdo->query($sql_testi)->fetchAll();

// 2. List Klien untuk Dropdown
$kliens = $pdo->query("SELECT * FROM klien ORDER BY nama_klien ASC")->fetchAll();

// 3. Data Edit
$editData = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM testimoni WHERE id_testimoni = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Testimoni & Review</h1>
            <p class="text-gray-500 text-sm">Kelola ulasan klien yang akan ditampilkan di halaman depan.</p>
        </div>
        
        <?php if($action == 'list'): ?>
            <a href="testimoni.php?action=add" class="bg-[#051094] text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-black transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Manual
            </a>
        <?php else: ?>
            <a href="testimoni.php" class="text-gray-500 hover:text-[#051094] font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded"><?= $error ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <?php if($action == 'add' || $action == 'edit'): ?>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-bold text-lg text-[#051094] mb-4 border-b pb-2">
                    <?= $action == 'edit' ? 'Edit Testimoni' : 'Tambah Testimoni' ?>
                </h3>
                
                <form method="POST">
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Klien <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="id_klien" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094] appearance-none bg-white" required>
                                <option value="">-- Pilih Klien --</option>
                                <?php foreach($kliens as $kl): ?>
                                    <option value="<?= $kl['id_klien'] ?>" <?= ($editData['id_klien']??'') == $kl['id_klien'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kl['nama_klien']) ?> 
                                        <?= $kl['perusahaan'] ? '('.$kl['perusahaan'].')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Isi Ulasan <span class="text-red-500">*</span></label>
                        <textarea name="isi_testimoni" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]" placeholder="Tuliskan pengalaman klien..." required><?= $editData['isi_testimoni'] ?? '' ?></textarea>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Tampil</label>
                        <select name="status_tampil" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#051094]">
                            <option value="0" <?= ($editData['status_tampil']??0) == 0 ? 'selected' : '' ?>>Sembunyikan (Draft)</option>
                            <option value="1" <?= ($editData['status_tampil']??0) == 1 ? 'selected' : '' ?>>Tampilkan di Website</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-[#051094] text-white px-6 py-2.5 rounded-lg font-bold shadow-lg hover:bg-black transition flex-1">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?= ($action == 'list') ? 'lg:col-span-3' : 'lg:col-span-2' ?>">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-4 border-b w-1/4">Klien</th>
                                <th class="p-4 border-b w-1/3">Isi Testimoni</th>
                                <th class="p-4 border-b text-center">Status</th>
                                <th class="p-4 border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach($testimoni as $row): 
                                // Highlight jika status hidden (0)
                                $bgClass = ($row['status_tampil'] == 0) ? 'bg-yellow-50' : 'hover:bg-gray-50';
                            ?>
                            <tr class="<?= $bgClass ?> transition">
                                <td class="p-4 align-top">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_klien']) ?></div>
                                    <div class="text-xs text-[#051094] mt-1 font-medium">
                                        <?= htmlspecialchars($row['perusahaan'] ?: 'Personal') ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-2">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="p-4 align-top">
                                    <div class="text-gray-600 italic relative pl-4">
                                        <span class="absolute left-0 top-0 text-gray-300 text-lg">"</span>
                                        <?= nl2br(htmlspecialchars($row['isi_testimoni'])) ?>
                                        <span class="text-gray-300 text-lg ml-1">"</span>
                                    </div>
                                </td>
                                <td class="p-4 align-top text-center">
                                    <?php if($row['status_tampil'] == 1): ?>
                                        <a href="testimoni.php?action=toggle&id=<?= $row['id_testimoni'] ?>" class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold hover:bg-green-200 transition" title="Klik untuk sembunyikan">
                                            <i class="fa-solid fa-check-circle"></i> Tampil
                                        </a>
                                    <?php else: ?>
                                        <a href="testimoni.php?action=toggle&id=<?= $row['id_testimoni'] ?>" class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold hover:bg-yellow-200 transition" title="Klik untuk tampilkan">
                                            <i class="fa-solid fa-eye-slash"></i> Sembunyi
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 align-top text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="testimoni.php?action=edit&id=<?= $row['id_testimoni'] ?>" class="text-[#051094] bg-white border border-blue-100 hover:bg-blue-600 hover:text-white w-8 h-8 flex items-center justify-center rounded-lg transition shadow-sm" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="testimoni.php?action=delete&id=<?= $row['id_testimoni'] ?>" onclick="return confirm('Hapus testimoni ini?')" class="text-red-500 bg-white border border-red-100 hover:bg-red-500 hover:text-white w-8 h-8 flex items-center justify-center rounded-lg transition shadow-sm" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if(empty($testimoni)): ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <i class="fa-regular fa-comments text-4xl mb-2"></i>
                                    <p>Belum ada testimoni.</p>
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
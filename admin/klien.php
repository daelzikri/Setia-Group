<?php
//
require_once "../config/database.php";
include "components/header.php";

// Inisialisasi Variabel
$id_klien       = "";
$nama_klien     = "";
$perusahaan     = "";
$email          = "";
$nomor_telepon  = "";
$sukses         = "";
$error          = "";
$is_edit        = false;

// --- PROSES: SIMPAN / UPDATE ---
if (isset($_POST['simpan'])) {
    $nama_klien     = htmlspecialchars($_POST['nama_klien']);
    $perusahaan     = htmlspecialchars($_POST['perusahaan']);
    $email          = htmlspecialchars($_POST['email']);
    $nomor_telepon  = htmlspecialchars($_POST['nomor_telepon']);

    if ($nama_klien && $email && $nomor_telepon) {
        try {
            if (isset($_POST['id_klien']) && $_POST['id_klien'] != "") {
                // UPDATE
                $id_klien = $_POST['id_klien'];
                $sql = "UPDATE klien SET nama_klien=?, perusahaan=?, email=?, nomor_telepon=? WHERE id_klien=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_klien, $perusahaan, $email, $nomor_telepon, $id_klien]);
                $sukses = "Data klien berhasil diperbarui.";
            } else {
                // INSERT
                $sql = "INSERT INTO klien (nama_klien, perusahaan, email, nomor_telepon) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_klien, $perusahaan, $email, $nomor_telepon]);
                $sukses = "Klien baru berhasil ditambahkan.";
            }
            // Reset form setelah sukses
            $nama_klien = $perusahaan = $email = $nomor_telepon = "";
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    } else {
        $error = "Nama, Email, dan Nomor Telepon wajib diisi.";
    }
}

// --- PROSES: HAPUS ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'];
    try {
        $sql = "DELETE FROM klien WHERE id_klien = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_hapus]);
        $sukses = "Data klien berhasil dihapus.";
    } catch (PDOException $e) {
        $error = "Gagal menghapus: " . $e->getMessage();
    }
}

// --- PROSES: EDIT (AMBIL DATA) ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $is_edit = true;
    $id_edit = $_GET['id'];
    $sql = "SELECT * FROM klien WHERE id_klien = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_edit]);
    $data = $stmt->fetch();

    if ($data) {
        $id_klien       = $data['id_klien'];
        $nama_klien     = $data['nama_klien'];
        $perusahaan     = $data['perusahaan'];
        $email          = $data['email'];
        $nomor_telepon  = $data['nomor_telepon'];
    }
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Data Klien & Mitra</h1>
            <p class="text-gray-500 text-sm">Kelola data pelanggan dan perusahaan yang bekerja sama.</p>
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
            <p class="font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Error</p>
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-bold text-lg text-[#051094] mb-4 border-b pb-2">
                    <?= $is_edit ? 'Edit Data Klien' : 'Tambah Klien Baru' ?>
                </h3>
                
                <form action="klien.php" method="POST">
                    <?php if($is_edit): ?>
                        <input type="hidden" name="id_klien" value="<?= $id_klien ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Klien / PIC <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_klien" value="<?= $nama_klien ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="Nama lengkap">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Perusahaan</label>
                        <input type="text" name="perusahaan" value="<?= $perusahaan ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" placeholder="Opsional jika perorangan">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="<?= $email ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="email@contoh.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon / WA <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_telepon" value="<?= $nomor_telepon ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="0812...">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="simpan" class="bg-[#051094] hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg flex-1 transition shadow-lg">
                            <i class="fa-solid fa-save mr-2"></i> Simpan
                        </button>
                        <?php if($is_edit): ?>
                            <a href="klien.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition text-center">
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
                                <th class="p-4 border-b font-bold">No</th>
                                <th class="p-4 border-b font-bold">Nama & Perusahaan</th>
                                <th class="p-4 border-b font-bold">Kontak</th>
                                <th class="p-4 border-b font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            $no = 1;
                            $sql = "SELECT * FROM klien ORDER BY id_klien DESC";
                            $stmt = $pdo->query($sql);
                            while ($row = $stmt->fetch()) {
                            ?>
                            <tr class="hover:bg-blue-50 transition border-b last:border-0">
                                <td class="p-4 text-gray-500"><?= $no++ ?></td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_klien']) ?></div>
                                    <?php if($row['perusahaan']): ?>
                                        <div class="text-xs text-[#051094] font-medium bg-blue-100 inline-block px-2 py-0.5 rounded mt-1">
                                            <i class="fa-solid fa-building mr-1"></i> <?= htmlspecialchars($row['perusahaan']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-xs text-gray-400 mt-1 italic">Personal</div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 text-gray-600 mb-1">
                                        <i class="fa-solid fa-envelope w-4"></i> <?= htmlspecialchars($row['email']) ?>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fa-brands fa-whatsapp w-4"></i> <?= htmlspecialchars($row['nomor_telepon']) ?>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="klien.php?aksi=edit&id=<?= $row['id_klien'] ?>" class="text-blue-600 hover:bg-blue-100 w-8 h-8 flex items-center justify-center rounded transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="klien.php?aksi=hapus&id=<?= $row['id_klien'] ?>" onclick="return confirm('Yakin ingin menghapus klien ini? Data event terkait mungkin akan kehilangan data klien.')" class="text-red-600 hover:bg-red-100 w-8 h-8 flex items-center justify-center rounded transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if($no == 1): ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                                    <p>Belum ada data klien.</p>
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
<?php
require_once "../config/database.php";
include "components/header.php";

// Inisialisasi variabel untuk form
$id_admin   = "";
$username   = "";
$hak_akses  = "";
$sukses     = "";
$error      = "";
$is_edit    = false;

// --- PROSES: TAMBAH / EDIT DATA ---
if (isset($_POST['simpan'])) {
    $username   = htmlspecialchars($_POST['username']);
    $hak_akses  = $_POST['hak_akses'];
    $password   = $_POST['password']; // Ambil password input

    // Validasi input
    if ($username && $hak_akses) {
        if (isset($_POST['id_admin']) && $_POST['id_admin'] != "") {
            // === MODE EDIT ===
            $id_admin = $_POST['id_admin'];
            
            // Cek apakah password diubah?
            if (!empty($password)) {
                // Jika password diisi, update password juga
                // Catatan: Untuk keamanan produksi, gunakan password_hash($password, PASSWORD_DEFAULT)
                $sql = "UPDATE admin SET username = ?, hak_akses = ?, password = ? WHERE id_admin = ?";
                $q   = $pdo->prepare($sql);
                $q->execute([$username, $hak_akses, $password, $id_admin]);
            } else {
                // Jika password kosong, jangan ubah password lama
                $sql = "UPDATE admin SET username = ?, hak_akses = ? WHERE id_admin = ?";
                $q   = $pdo->prepare($sql);
                $q->execute([$username, $hak_akses, $id_admin]);
            }
            $sukses = "Data admin berhasil diperbarui.";
        } else {
            // === MODE TAMBAH ===
            if (!empty($password)) {
                $sql = "INSERT INTO admin (username, password, hak_akses) VALUES (?, ?, ?)";
                $q   = $pdo->prepare($sql);
                $q->execute([$username, $password, $hak_akses]);
                $sukses = "Admin baru berhasil ditambahkan.";
            } else {
                $error = "Password wajib diisi untuk admin baru.";
            }
        }
    } else {
        $error = "Semua data wajib diisi.";
    }
}

// --- PROSES: HAPUS DATA ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'];
    
    // Proteksi: Tidak boleh menghapus diri sendiri
    // Asumsi id_admin disimpan di session saat login
    $current_admin_id = $_SESSION['id_admin'] ?? 0; 

    if ($id_hapus == $current_admin_id) {
        $error = "Anda tidak dapat menghapus akun yang sedang digunakan.";
    } else {
        $sql = "DELETE FROM admin WHERE id_admin = ?";
        $q   = $pdo->prepare($sql);
        $q->execute([$id_hapus]);
        $sukses = "Data admin berhasil dihapus.";
    }
}

// --- PROSES: PERSIAPAN EDIT (AMBIL DATA) ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = $_GET['id'];
    $sql     = "SELECT * FROM admin WHERE id_admin = ?";
    $q       = $pdo->prepare($sql);
    $q->execute([$id_edit]);
    $data    = $q->fetch();

    if ($data) {
        $is_edit    = true;
        $id_admin   = $data['id_admin'];
        $username   = $data['username'];
        $hak_akses  = $data['hak_akses'];
    }
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Manajemen Admin</h1>
            <p class="text-gray-500 text-sm">Kelola pengguna yang memiliki akses ke dashboard ini.</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Error</p>
            <p><?= $error ?></p>
        </div>
    <?php endif; ?>
    <?php if ($sukses): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Sukses</p>
            <p><?= $sukses ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-lg text-[#051094] mb-4 border-b pb-2">
                    <?= $is_edit ? 'Edit Admin' : 'Tambah Admin Baru' ?>
                </h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="id_admin" value="<?= $id_admin ?>">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                        <input type="text" name="username" value="<?= $username ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="Masukkan username">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Password 
                            <?php if($is_edit): ?>
                                <span class="text-xs font-normal text-red-500">(Kosongkan jika tidak ingin mengubah)</span>
                            <?php endif; ?>
                        </label>
                        <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" <?= $is_edit ? '' : 'required' ?> placeholder="Masukkan password">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Hak Akses</label>
                        <select name="hak_akses" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]">
                            <option value="superadmin" <?= $hak_akses == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                            <option value="editor" <?= $hak_akses == 'editor' ? 'selected' : '' ?>>Editor</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Superadmin: Akses penuh. Editor: Terbatas.</p>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="simpan" class="bg-[#051094] hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg flex-1 transition">
                            <i class="fa-solid fa-save mr-2"></i> Simpan
                        </button>
                        <?php if($is_edit): ?>
                            <a href="admin.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition text-center">
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
                                <th class="p-4 border-b font-bold">Username</th>
                                <th class="p-4 border-b font-bold">Hak Akses</th>
                                <th class="p-4 border-b font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            $no = 1;
                            $sql = "SELECT * FROM admin ORDER BY id_admin ASC";
                            $stmt = $pdo->query($sql);
                            while ($row = $stmt->fetch()) {
                            ?>
                            <tr class="hover:bg-blue-50 transition border-b last:border-0">
                                <td class="p-4 text-gray-500"><?= $no++ ?></td>
                                <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="p-4">
                                    <?php if($row['hak_akses'] == 'superadmin'): ?>
                                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Superadmin</span>
                                    <?php else: ?>
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Editor</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="admin.php?aksi=edit&id=<?= $row['id_admin'] ?>" class="text-blue-600 hover:bg-blue-100 w-8 h-8 flex items-center justify-center rounded transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="admin.php?aksi=hapus&id=<?= $row['id_admin'] ?>" onclick="return confirm('Yakin ingin menghapus admin ini?')" class="text-red-600 hover:bg-red-100 w-8 h-8 flex items-center justify-center rounded transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
<?php
require_once "../config/database.php";
include "components/header.php";

// --- KONFIGURASI PATH --- //
// Path fisik untuk PHP (Upload & Hapus) - Naik satu folder (../) lalu ke assets/team/
$upload_dir_physical = "../assets/team/"; 
// Path relatif untuk Database & HTML src
$upload_dir_db       = "assets/team/";

// Inisialisasi Variabel
$id_tim     = "";
$nama       = "";
$jabatan    = "";
$urutan     = "";
$foto_lama  = "";
$sukses     = "";
$error      = "";
$is_edit    = false;

// --- PROSES: SIMPAN / UPDATE ---
if (isset($_POST['simpan'])) {
    $nama       = htmlspecialchars($_POST['nama']);
    $jabatan    = htmlspecialchars($_POST['jabatan']);
    $urutan     = (int) $_POST['urutan'];
    $id_profil  = 1; // Default ke profil utama

    // Handle Upload Foto
    // Default pakai foto lama (path DB)
    $foto_path_db = $_POST['foto_lama'] ?? ''; 
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        
        // Pastikan folder ada
        if (!is_dir($upload_dir_physical)) {
            mkdir($upload_dir_physical, 0777, true);
        }

        $file_name = $_FILES['foto']['name'];
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            // Generate nama file baru (unik)
            $new_name  = time() . '_' . rand(100, 999) . '.' . $file_ext;
            
            $target_file = $upload_dir_physical . $new_name; // ../assets/team/nama.jpg

            if (move_uploaded_file($file_tmp, $target_file)) {
                
                // Hapus foto lama jika ada dan sedang mode edit (ganti foto)
                if (!empty($_POST['foto_lama'])) {
                    $old_file_physical = "../" . $_POST['foto_lama'];
                    if (file_exists($old_file_physical)) {
                        unlink($old_file_physical);
                    }
                }

                // Set path baru untuk disimpan di DB
                $foto_path_db = $upload_dir_db . $new_name; // assets/team/nama.jpg
            } else {
                $error = "Gagal mengupload gambar ke server.";
            }
        } else {
            $error = "Format file tidak diizinkan (Gunakan JPG, PNG, WEBP).";
        }
    }

    if (!$error) {
        try {
            if (isset($_POST['id_tim']) && $_POST['id_tim'] != "") {
                // UPDATE
                $id_tim = $_POST['id_tim'];
                $sql = "UPDATE tim_kami SET nama=?, jabatan=?, urutan=?, foto=? WHERE id_tim=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama, $jabatan, $urutan, $foto_path_db, $id_tim]);
                $sukses = "Data anggota tim berhasil diperbarui.";
            } else {
                // INSERT
                $sql = "INSERT INTO tim_kami (id_profil, nama, jabatan, urutan, foto) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_profil, $nama, $jabatan, $urutan, $foto_path_db]);
                $sukses = "Anggota tim baru berhasil ditambahkan.";
            }
            // Reset form agar bersih setelah simpan
            if (!$is_edit) {
                $nama = $jabatan = $urutan = $foto_lama = "";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// --- PROSES: HAPUS ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'];
    
    // Ambil info gambar dulu untuk dihapus fisiknya
    $sql_cek = "SELECT foto FROM tim_kami WHERE id_tim = ?";
    $stmt = $pdo->prepare($sql_cek);
    $stmt->execute([$id_hapus]);
    $row = $stmt->fetch();

    if ($row && !empty($row['foto'])) {
        $file_to_delete = "../" . $row['foto'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }

    $sql = "DELETE FROM tim_kami WHERE id_tim = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_hapus]);
    $sukses = "Data berhasil dihapus.";
}

// --- PROSES: EDIT (AMBIL DATA) ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $is_edit = true;
    $id_edit = $_GET['id'];
    $sql = "SELECT * FROM tim_kami WHERE id_tim = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_edit]);
    $data = $stmt->fetch();

    if ($data) {
        $id_tim     = $data['id_tim'];
        $nama       = $data['nama'];
        $jabatan    = $data['jabatan'];
        $urutan     = $data['urutan'];
        $foto_lama  = $data['foto'];
    }
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Tim & Keunggulan SDM</h1>
            <p class="text-gray-500 text-sm">Kelola profil anggota tim yang akan ditampilkan di website.</p>
        </div>
        
        <?php if($is_edit): ?>
            <a href="tim.php" class="text-gray-500 hover:text-[#051094] font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Tambah Baru
            </a>
        <?php endif; ?>
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
                    <?= $is_edit ? 'Edit Anggota' : 'Tambah Anggota Tim' ?>
                </h3>
                
                <form action="tim.php" method="POST" enctype="multipart/form-data">
                    <?php if($is_edit): ?>
                        <input type="hidden" name="id_tim" value="<?= $id_tim ?>">
                        <input type="hidden" name="foto_lama" value="<?= $foto_lama ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $nama ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="Cth: Budi Santoso">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jabatan</label>
                        <input type="text" name="jabatan" value="<?= $jabatan ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="Cth: Project Manager">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Urutan Tampil</label>
                        <input type="number" name="urutan" value="<?= $urutan ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" required placeholder="1, 2, 3...">
                        <p class="text-xs text-gray-400 mt-1">Angka kecil tampil di awal.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto Profil</label>
                        <?php if($is_edit && !empty($foto_lama)): ?>
                            <div class="mb-2">
                                <img src="../<?= $foto_lama ?>" class="w-20 h-20 object-cover rounded-full border border-gray-200">
                                <p class="text-xs text-gray-400">Foto saat ini</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="foto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#051094] hover:file:bg-blue-100 cursor-pointer" accept="image/*" <?= $is_edit ? '' : 'required' ?>>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="simpan" class="bg-[#051094] hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg flex-1 transition shadow-lg">
                            <i class="fa-solid fa-save mr-2"></i> Simpan
                        </button>
                        <?php if($is_edit): ?>
                            <a href="tim.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition text-center">
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
                                <th class="p-4 border-b font-bold text-center">Urutan</th>
                                <th class="p-4 border-b font-bold">Profil</th>
                                <th class="p-4 border-b font-bold">Nama & Jabatan</th>
                                <th class="p-4 border-b font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            // Ambil data dan urutkan berdasarkan 'urutan'
                            $sql = "SELECT * FROM tim_kami ORDER BY urutan ASC";
                            $stmt = $pdo->query($sql);
                            $count = 0;
                            while ($row = $stmt->fetch()) {
                                $count++;
                            ?>
                            <tr class="hover:bg-blue-50 transition border-b last:border-0">
                                <td class="p-4 text-center font-bold text-gray-400">#<?= $row['urutan'] ?></td>
                                <td class="p-4">
                                    <?php if($row['foto']): ?>
                                        <img src="../<?= $row['foto'] ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <h4 class="font-bold text-gray-800"><?= htmlspecialchars($row['nama']) ?></h4>
                                    <p class="text-xs text-blue-600 font-medium bg-blue-50 inline-block px-2 rounded-full mt-1">
                                        <?= htmlspecialchars($row['jabatan']) ?>
                                    </p>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="tim.php?aksi=edit&id=<?= $row['id_tim'] ?>" class="text-blue-600 hover:bg-blue-100 w-8 h-8 flex items-center justify-center rounded transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="tim.php?aksi=hapus&id=<?= $row['id_tim'] ?>" onclick="return confirm('Yakin ingin menghapus anggota tim ini?')" class="text-red-600 hover:bg-red-100 w-8 h-8 flex items-center justify-center rounded transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            
                            <?php if($count == 0): ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <i class="fa-solid fa-users-slash text-4xl mb-2"></i>
                                    <p>Belum ada data tim.</p>
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
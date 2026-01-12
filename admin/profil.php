<?php
//
require_once "../config/database.php";
include "components/header.php";

// 1. Ambil Data Profil (ID = 1)
$id_profil = 1;
$stmt = $pdo->prepare("SELECT * FROM profil_perusahaan WHERE id_profil = ?");
$stmt->execute([$id_profil]);
$data = $stmt->fetch();

// Jika data kosong (belum ada di db), buat array dummy agar tidak error
if (!$data) {
    // PERUBAHAN: Menambahkan 'alamat_cabang' ke dalam dummy array
    $data = array_fill_keys(['nama_resmi', 'nama_brand', 'tagline', 'deskripsi_intro', 'deskripsi_lengkap', 'visi', 'misi', 'keunggulan', 'alamat', 'alamat_cabang', 'email', 'telepon', 'whatsapp', 'sosial_media'], '');
}

// 2. Proses Simpan Data (Insert / Update)
if (isset($_POST['simpan'])) {
    try {
        $nama_resmi = $_POST['nama_resmi'];
        $nama_brand = $_POST['nama_brand'];
        $tagline    = $_POST['tagline'];
        $desc_intro = $_POST['deskripsi_intro'];
        $desc_full  = $_POST['deskripsi_lengkap'];
        $visi       = $_POST['visi'];
        $misi       = $_POST['misi'];
        $keunggulan = $_POST['keunggulan'];
        $alamat     = $_POST['alamat'];
        
        // PERUBAHAN: Menangkap input alamat cabang
        $alamat_cabang = $_POST['alamat_cabang']; 
        
        $email      = $_POST['email'];
        $telepon    = $_POST['telepon'];
        $wa         = $_POST['whatsapp'];
        $sosmed     = $_POST['sosial_media'];
        
        $id_admin   = $_SESSION['id_admin'] ?? 1; 

        // CEK APAKAH DATA SUDAH ADA?
        $cekStmt = $pdo->prepare("SELECT COUNT(*) FROM profil_perusahaan WHERE id_profil = ?");
        $cekStmt->execute([$id_profil]);
        $dataExists = $cekStmt->fetchColumn();

        if ($dataExists > 0) {
            // === JIKA DATA ADA, LAKUKAN UPDATE ===
            // PERUBAHAN: Menambahkan update untuk alamat_cabang
            $sql = "UPDATE profil_perusahaan SET 
                    nama_resmi=?, nama_brand=?, tagline=?, 
                    deskripsi_intro=?, deskripsi_lengkap=?, 
                    visi=?, misi=?, keunggulan=?, 
                    alamat=?, alamat_cabang=?, email=?, telepon=?, whatsapp=?, sosial_media=?,
                    updated_at=NOW(), id_admin=? 
                    WHERE id_profil=?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nama_resmi, $nama_brand, $tagline, 
                $desc_intro, $desc_full, 
                $visi, $misi, $keunggulan, 
                $alamat, $alamat_cabang, $email, $telepon, $wa, $sosmed, 
                $id_admin, $id_profil
            ]);

        } else {
            // === JIKA DATA KOSONG, LAKUKAN INSERT ===
            // PERUBAHAN: Menambahkan insert untuk alamat_cabang
            $sql = "INSERT INTO profil_perusahaan (
                        id_profil, nama_resmi, nama_brand, tagline, 
                        deskripsi_intro, deskripsi_lengkap, 
                        visi, misi, keunggulan, 
                        alamat, alamat_cabang, email, telepon, whatsapp, sosial_media,
                        updated_at, id_admin
                    ) VALUES (
                        ?, ?, ?, ?, 
                        ?, ?, 
                        ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?,
                        NOW(), ?
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_profil, $nama_resmi, $nama_brand, $tagline, 
                $desc_intro, $desc_full, 
                $visi, $misi, $keunggulan, 
                $alamat, $alamat_cabang, $email, $telepon, $wa, $sosmed, 
                $id_admin
            ]);
        }
        
        echo "<script>alert('Data Profil Perusahaan Berhasil Disimpan!'); window.location='profil.php';</script>";
        exit;

    } catch (PDOException $e) {
        echo "<script>alert('Gagal menyimpan: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Profil Perusahaan</h1>
            <p class="text-gray-500 text-sm">Kelola informasi identitas, visi misi, dan kontak perusahaan.</p>
        </div>
        <div class="text-sm text-gray-500 italic">
            Terakhir diupdate: <?= date('d F Y H:i', strtotime($data['updated_at'] ?? 'now')) ?>
        </div>
    </div>

    <form method="POST">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-[#051094] mb-4 uppercase text-xs tracking-widest border-b pb-2">Identitas Brand</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Brand (Singkat)</label>
                            <input type="text" name="nama_brand" value="<?= htmlspecialchars($data['nama_brand']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" placeholder="Contoh: SETIA GROUP">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Resmi (PT/CV)</label>
                            <input type="text" name="nama_resmi" value="<?= htmlspecialchars($data['nama_resmi']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tagline</label>
                            <textarea name="tagline" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]"><?= htmlspecialchars($data['tagline']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-[#051094] mb-4 uppercase text-xs tracking-widest border-b pb-2">Kontak Informasi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Telepon</label>
                                <input type="text" name="telepon" value="<?= htmlspecialchars($data['telepon']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">WhatsApp</label>
                                <input type="text" name="whatsapp" value="<?= htmlspecialchars($data['whatsapp']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Head Office</label>
                            <textarea name="alamat" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]"><?= htmlspecialchars($data['alamat']) ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Branch Office</label>
                            <textarea name="alamat_cabang" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" placeholder="Kosongkan jika tidak ada"><?= htmlspecialchars($data['alamat_cabang'] ?? '') ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Link Sosial Media</label>
                            <input type="text" name="sosial_media" value="<?= htmlspecialchars($data['sosial_media']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]" placeholder="Instagram / Facebook Link">
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-[#051094] mb-4 uppercase text-xs tracking-widest border-b pb-2">Detail Perusahaan</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Intro (Singkat)</label>
                            <p class="text-xs text-gray-400 mb-2">Ditampilkan di footer atau sekilas info.</p>
                            <textarea name="deskripsi_intro" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]"><?= htmlspecialchars($data['deskripsi_intro']) ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Lengkap (About Us)</label>
                            <textarea name="deskripsi_lengkap" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]"><?= htmlspecialchars($data['deskripsi_lengkap']) ?></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Visi</label>
                                <textarea name="visi" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094]"><?= htmlspecialchars($data['visi']) ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Misi</label>
                                <p class="text-xs text-gray-400 mb-2">Bisa menggunakan format list HTML (&lt;ul&gt;&lt;li&gt;...)</p>
                                <textarea name="misi" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094] font-mono text-sm"><?= htmlspecialchars($data['misi']) ?></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keunggulan Perusahaan</label>
                            <p class="text-xs text-gray-400 mb-2">Bisa menggunakan format list HTML (&lt;ul&gt;&lt;li&gt;...)</p>
                            <textarea name="keunggulan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#051094] font-mono text-sm"><?= htmlspecialchars($data['keunggulan']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" name="simpan" class="bg-[#051094] text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
</body>
</html>
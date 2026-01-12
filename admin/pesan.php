<?php
require_once "../config/database.php";
include "components/header.php";

// Ambil Action & ID dari URL
$action = $_GET['action'] ?? 'list';
$id     = $_GET['id'] ?? null;

// --- LOGIC HANDLING --- //

// 1. DELETE PESAN
if ($action == 'delete' && $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM pesan_kontak WHERE id_pesan = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Pesan berhasil dihapus dari kotak masuk.'); window.location='pesan.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus: " . $e->getMessage() . "');</script>";
    }
}

// 2. VIEW DETAIL & UPDATE STATUS DIBACA
$detail = null;
if ($action == 'view' && $id) {
    // Ambil data pesan
    $stmt = $pdo->prepare("SELECT * FROM pesan_kontak WHERE id_pesan = ?");
    $stmt->execute([$id]);
    $detail = $stmt->fetch();

    if ($detail) {
        // Jika status masih "Belum Dibaca" (0), update jadi "Dibaca" (1)
        if ($detail['status_dibaca'] == 0) {
            $id_admin = $_SESSION['id_admin'] ?? 1;
            $updateSql = "UPDATE pesan_kontak SET status_dibaca = 1, id_admin = ? WHERE id_pesan = ?";
            $pdo->prepare($updateSql)->execute([$id_admin, $id]);
            
            // Update variabel agar tampilan status di UI langsung berubah
            $detail['status_dibaca'] = 1;
        }
    } else {
        echo "<script>alert('Pesan tidak ditemukan!'); window.location='pesan.php';</script>";
        exit;
    }
}

// 3. FETCH LIST PESAN (Inbox)
// Logic: Tampilkan yang belum dibaca (status=0) di paling atas, kemudian urutkan tanggal terbaru
$sql = "SELECT * FROM pesan_kontak ORDER BY status_dibaca ASC, tanggal DESC, id_pesan DESC";
$inbox = $pdo->query($sql)->fetchAll();

// Hitung Statistik Sederhana
$unread_count = 0;
foreach ($inbox as $msg) {
    if ($msg['status_dibaca'] == 0) $unread_count++;
}
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#051094]">Kotak Masuk</h1>
            <p class="text-gray-500 text-sm">Kelola pesan dan pertanyaan dari pengunjung website.</p>
        </div>
        
        <?php if ($action == 'view'): ?>
            <a href="pesan.php" class="bg-white border border-gray-300 text-gray-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Inbox
            </a>
        <?php else: ?>
            <div class="bg-blue-50 text-[#051094] px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-envelope"></i>
                Total: <?= count($inbox) ?> Pesan (<?= $unread_count ?> Belum Dibaca)
            </div>
        <?php endif; ?>
    </div>

    <?php if ($action == 'view' && $detail): ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-user-circle text-gray-400 text-xl"></i>
                            <?= htmlspecialchars($detail['nama_pengirim']) ?>
                        </h2>
                        <a href="mailto:<?= htmlspecialchars($detail['email']) ?>" class="text-sm text-blue-600 hover:underline">
                            <?= htmlspecialchars($detail['email']) ?>
                        </a>
                    </div>
                    <div class="text-right">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                            <i class="fa-solid fa-check-double mr-1"></i> Dibaca
                        </span>
                        <p class="text-xs text-gray-400 mt-2">Diterima: <?= date('d F Y', strtotime($detail['tanggal'])) ?></p>
                    </div>
                </div>
                
                <div class="p-8 min-h-[300px]">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap font-serif text-lg">
                        <?= htmlspecialchars($detail['isi_pesan']) ?>
                    </p>
                </div>

                <div class="p-6 border-t border-gray-100 flex gap-4 bg-gray-50">
                    <a href="mailto:<?= htmlspecialchars($detail['email']) ?>?subject=Balasan: Pertanyaan Anda&body=Halo <?= htmlspecialchars($detail['nama_pengirim']) ?>,%0D%0A%0D%0AMenanggapi pesan Anda...%0D%0A%0D%0ATerima Kasih." target="_blank" class="bg-[#051094] text-white px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-black transition shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-reply"></i> Balas via Email
                    </a>
                    <a href="pesan.php?action=delete&id=<?= $detail['id_pesan'] ?>" onclick="return confirm('Hapus pesan ini secara permanen?')" class="bg-white border border-red-200 text-red-600 px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-red-50 transition flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Hapus Pesan
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h3 class="font-bold text-[#051094] mb-2">Informasi Status</h3>
                <p class="text-sm text-gray-600 mb-4">Pesan ini telah ditandai sebagai "Dibaca" oleh sistem saat Anda membukanya.</p>
                <?php if($detail['id_admin']): ?>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Diproses oleh Admin ID: <?= $detail['id_admin'] ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-4 border-b w-10 text-center"><i class="fa-solid fa-circle text-[8px] text-gray-300"></i></th>
                        <th class="p-4 border-b w-1/4">Pengirim</th>
                        <th class="p-4 border-b w-1/3">Preview Pesan</th>
                        <th class="p-4 border-b">Tanggal</th>
                        <th class="p-4 border-b text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php foreach($inbox as $msg): 
                        $is_unread = ($msg['status_dibaca'] == 0);
                        $row_class = $is_unread ? "bg-blue-50 hover:bg-blue-100" : "hover:bg-gray-50";
                        $font_class = $is_unread ? "font-bold text-gray-900" : "font-normal text-gray-600";
                    ?>
                    <tr class="<?= $row_class ?> transition group">
                        <td class="p-4 text-center">
                            <?php if($is_unread): ?>
                                <i class="fa-solid fa-circle text-xs text-blue-600" title="Belum Dibaca"></i>
                            <?php else: ?>
                                <i class="fa-regular fa-envelope-open text-gray-300"></i>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <div class="<?= $font_class ?>"><?= htmlspecialchars($msg['nama_pengirim']) ?></div>
                            <div class="text-xs text-gray-500 truncate"><?= htmlspecialchars($msg['email']) ?></div>
                        </td>
                        <td class="p-4">
                            <div class="max-w-md truncate <?= $font_class ?>">
                                <?= htmlspecialchars(strip_tags($msg['isi_pesan'])) ?>
                            </div>
                        </td>
                        <td class="p-4 text-gray-500 text-xs whitespace-nowrap">
                            <?= date('d M Y', strtotime($msg['tanggal'])) ?>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="pesan.php?action=view&id=<?= $msg['id_pesan'] ?>" class="text-[#051094] bg-white border border-blue-100 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg transition shadow-sm text-xs font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                                <a href="pesan.php?action=delete&id=<?= $msg['id_pesan'] ?>" onclick="return confirm('Hapus pesan ini?')" class="text-red-500 bg-white border border-red-100 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg transition shadow-sm text-xs" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($inbox)): ?>
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400">
                            <i class="fa-regular fa-envelope text-5xl mb-3 block"></i>
                            <span class="text-sm">Tidak ada pesan masuk.</span>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
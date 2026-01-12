<?php
require_once "config/database.php";
// 1. Ambil Data Info Perusahaan
// Menggunakan SELECT * agar kolom alamat_cabang otomatis ikut terambil
$stmt_profil = $pdo->query("SELECT * FROM profil_perusahaan LIMIT 1");
$profil = $stmt_profil->fetch(PDO::FETCH_ASSOC);

// 2. Proses Kirim Pesan
$pesan_status = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kirim_pesan'])) {
    
    // Sanitasi Input
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $subjek = htmlspecialchars($_POST['subjek']);
    $pesan_input = htmlspecialchars($_POST['pesan']); 
    
    // Tanggal hari ini
    $tanggal = date('Y-m-d');

    // Membersihkan new line agar rapi di database
    $pesan_bersih = str_replace(array("\r", "\n"), ' ', $pesan_input);
    
    // Gabungkan Subjek dan Pesan
    $isi_pesan_final = "[Subjek: " . $subjek . "] - " . $pesan_bersih;

    try {
        $sql = "INSERT INTO pesan_kontak (nama_pengirim, email, isi_pesan, tanggal, status_dibaca) 
                VALUES (:nama, :email, :isi_pesan, :tanggal, 0)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama'      => $nama,
            ':email'     => $email,
            ':isi_pesan' => $isi_pesan_final,
            ':tanggal'   => $tanggal
        ]);
        
        $pesan_status = "success";
    } catch (PDOException $e) {
        $pesan_status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - <?= htmlspecialchars($profil['nama_brand']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; color: #1a1a1a; }
        .hero-font { font-family: 'Playfair Display', serif; }
        .bg-navy { background-color: #051094; }
        .text-navy { color: #051094; }
        .form-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: #051094;
            box-shadow: 0 0 0 3px rgba(5, 16, 148, 0.1);
        }
    </style>
</head>
<body class="bg-white">
    <?php include "includes/navbar.php"; ?>

    <section class="relative bg-cover bg-center text-white h-[50vh]" style="background-image: url('assets/header.jpg');">
        <div class="absolute inset-0 bg-[#051094]/70"></div>
        <div class="container mx-auto px-4 h-full flex items-center justify-center relative z-10">
            <div class="text-center animate-fade-in-up">
                <span class="inline-block tracking-[0.3em] uppercase text-sm mb-4 text-blue-200 font-semibold">Get In Touch</span>
                <h1 class="text-5xl md:text-6xl font-bold hero-font">Hubungi Kami</h1>
            </div>
        </div>
    </section>

    <section class="py-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                
                <div class="space-y-10">
                    <div>
                        <div class="w-12 h-1 bg-navy mb-6"></div>
                        <h2 class="text-4xl font-bold text-gray-900 hero-font mb-4">Mari Berkolaborasi</h2>
                        <p class="text-gray-500 text-lg leading-relaxed">
                            Kami siap mendengarkan kebutuhan event Anda.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start gap-6 group">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-navy text-xl group-hover:bg-navy group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1 hero-font">Head Office</h3>
                                <p class="text-gray-500 w-3/4"><?= htmlspecialchars($profil['alamat']) ?></p>
                            </div>
                        </div>

                        <?php if(!empty($profil['alamat_cabang'])): ?>
                        <div class="flex items-start gap-6 group">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-navy text-xl group-hover:bg-navy group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1 hero-font">Branch Office</h3>
                                <p class="text-gray-500 w-3/4"><?= htmlspecialchars($profil['alamat_cabang']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-start gap-6 group">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-navy text-xl group-hover:bg-navy group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1 hero-font">Email</h3>
                                <p class="text-gray-500"><?= htmlspecialchars($profil['email']) ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6 group">
                            <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-navy text-xl group-hover:bg-navy group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1 hero-font">Telepon / WhatsApp</h3>
                                <p class="text-gray-500"><?= htmlspecialchars($profil['telepon']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 hero-font mb-6">Kirim Pesan</h3>
                    
                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="nama" required class="form-input" placeholder="Nama Anda">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                                <input type="email" name="email" required class="form-input" placeholder="email@contoh.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Subjek</label>
                            <input type="text" name="subjek" required class="form-input" placeholder="Misal: Tanya Harga Sewa LED">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan Anda</label>
                            <textarea name="pesan" rows="5" required class="form-input" placeholder="Tuliskan detail kebutuhan event Anda..."></textarea>
                        </div>

                        <button type="submit" name="kirim_pesan" class="w-full bg-navy text-white font-bold py-4 rounded-xl hover:bg-black transition-all duration-300 shadow-lg tracking-wide uppercase text-sm">
                            Kirim Pesan Sekarang
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <?php if ($pesan_status == "success"): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Terkirim!',
            text: 'Pesan berhasil dikirim.',
            confirmButtonColor: '#051094'
        });
    </script>
    <?php elseif ($pesan_status == "error"): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan sistem.',
            confirmButtonColor: '#d33'
        });
    </script>
    <?php endif; ?>

</body>
</html>
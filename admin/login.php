<?php
session_start();
require_once "../config/database.php";

if(isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek user
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Validasi sederhana (sesuai data dump: admin/admin123)
    // Catatan: Di produksi, gunakan password_verify() hash
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_role'] = $admin['hak_akses'];
        header("Location: index.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Setia Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-[#051094] p-8 text-center">
            <h1 class="text-2xl font-bold text-white tracking-widest uppercase">Setia Group</h1>
            <p class="text-blue-200 text-sm mt-2">Administrator Panel</p>
        </div>
        <div class="p-8">
            <?php if(isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" class="w-full px-4 py-3 rounded-lg bg-gray-50 border focus:border-[#051094] focus:outline-none" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-lg bg-gray-50 border focus:border-[#051094] focus:outline-none" required>
                </div>
                <button type="submit" class="w-full bg-[#051094] text-white font-bold py-3 rounded-lg hover:bg-black transition-all">LOGIN</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: index.php');
    exit;
}

$pengguna = [
    'admin' => 'password123'
];

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($pengguna[$username]) && $pengguna[$username] === $password) {
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;

        header('Location: index.php');
        exit;
    } else {
        $pesan = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIM-Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>SIM-Barang</h1>
        <p>Silakan login untuk mengakses inventaris.</p>
    </header>

    <main>
        <section>
            <h2>Login Pengguna</h2>

            <?php if ($pesan !== ''): ?>
                <p role="alert">
                    <?= htmlspecialchars($pesan) ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit">Login</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 SIM-Barang</p>
    </footer>
</body>
</html>

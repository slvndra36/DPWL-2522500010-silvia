<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
</head>
<body>
    <h1>Data Profil Mahasiswa</h1>
    <ul>
        <li><strong>NIM:</strong> <?= $2522500010; ?></li>
        <li><strong>NAMA:</strong> <?= $Silvia; ?></li>
        <li><strong>KELAS:</strong> <?= $SI3A; ?></li>
    </ul>

    <!-- Gunakan site_url() untuk navigasi halaman sesuai petunjuk modul 0.2 -->
    <a href="<?= site_url(); ?>">Kembali ke Beranda</a>
</body>
</html>
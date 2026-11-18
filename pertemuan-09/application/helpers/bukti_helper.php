<?php
// P9 BARU - application/helpers/bukti_helper.php
// Mengirim berkas bukti pembayaran dari uploads/bayar/ yang dilindungi .htaccess (Require all denied).
// Pemeriksaan hak akses (penyewa pemilik / admin) WAJIB dilakukan Controller sebelum fungsi ini dipanggil.

function kirim_bukti_pembayaran(?string $namaFile): never
{
    $namaFile = (string) $namaFile;

    if (
        $namaFile === '' ||
        str_contains($namaFile, '..') ||
        !preg_match('/\A[A-Za-z0-9._-]{1,100}\z/', $namaFile)
    ) {
        http_response_code(404);
        exit('Bukti pembayaran tidak ditemukan.');
    }

    $path = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'uploads'
        . DIRECTORY_SEPARATOR . 'bayar' . DIRECTORY_SEPARATOR . $namaFile;

    if (!is_file($path) || !is_readable($path)) {
        http_response_code(404);
        exit('Berkas bukti pembayaran tidak ditemukan.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($path);
    $diizinkan = ['image/jpeg', 'image/png', 'image/webp'];
    if (!is_string($mime) || !in_array($mime, $diizinkan, true)) {
        http_response_code(404);
        exit('Berkas bukti pembayaran tidak valid.');
    }

    $ukuran = filesize($path);

    // Lepas kunci session agar permintaan lain dari pengguna yang sama tidak tertahan.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: ' . $mime);
    if ($ukuran !== false) {
        header('Content-Length: ' . $ukuran);
    }
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store, max-age=0');
    header('Content-Disposition: inline; filename="bukti-pembayaran"');

    readfile($path);
    exit;
}

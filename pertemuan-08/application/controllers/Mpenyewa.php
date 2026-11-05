
<?php
// P8: Manajemen Penyewa (CRUD oleh admin)

class Mpenyewa_controller extends Controller
{
    private Penyewa_model $model;

    public function __construct()
    {
        require_once APPPATH . 'models/Penyewa_model.php';
        require_once APPPATH . 'helpers/auth_helper.php';
        require_once APPPATH . 'helpers/flash_helper.php';

        $this->model = new Penyewa_model();
    }

    // =========================
    // TAMPIL DATA PENYEWA
    // =========================

    public function index(): void
    {
        require_admin_login();

        $this->render(null, null, null, get_flash_message());
    }

    // =========================
    // TAMBAH PENYEWA
    // =========================

    public function tambah(): void
    {
        require_admin_login();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('tambah', []);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode request tidak diizinkan.');
        }

        $in = $this->readInput();
        $password = $_POST['sandi'] ?? '';

        $error = $this->validateInput($in, $password, false);

        if (
            $error === null &&
            $this->model->findByUserPlg($in['user_plg']) !== null
        ) {
            $error = 'Username sudah digunakan.';
        }

        if ($error !== null) {
            $this->render('tambah', $in, $error);
            return;
        }

        $fotoKtp = null;
        $fotoSim = null;

        try {
            // Upload foto KTP dan SIM A
            $fotoKtp = $this->uploadFoto('ft_ktp');
            $fotoSim = $this->uploadFoto('ft_sim_a');

            $ok = $this->model->create([
                'user_plg'   => $in['user_plg'],
                'sandi'      => password_hash($password, PASSWORD_DEFAULT),
                'nik'        => $in['nik'],
                'nm_lngkp'   => $in['nm_lngkp'],
                'no_hp'      => $in['no_hp'],
                'almt_lngkp' => $in['almt_lngkp'],
                'ft_ktp'     => $fotoKtp,
                'ft_sim_a'   => $fotoSim,
            ]);

        } catch (Throwable $e) {
            error_log('Tambah penyewa gagal: ' . $e->getMessage());
            $ok = false;
        }

        if (!$ok) {
            // Hapus foto yang sudah terlanjur diupload
            $this->hapusFileUpload('ft_ktp', $fotoKtp);
            $this->hapusFileUpload('ft_sim_a', $fotoSim);

            $this->render(
                'tambah',
                $in,
                'Data penyewa gagal disimpan. Periksa kembali data dan foto.'
            );
            return;
        }

        set_flash_message('success', 'Data penyewa berhasil ditambahkan.');

        header('Location: ' . site_url('admin/mpenyewa'));
        exit;
    }

    // =========================
    // UBAH PENYEWA
    // =========================

    public function ubah(string $userPlg): void
    {
        require_admin_login();

        $penyewa = $this->model->getProfil($userPlg);

        if ($penyewa === null) {
            set_flash_message('danger', 'Data penyewa tidak ditemukan.');

            header('Location: ' . site_url('admin/mpenyewa'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('ubah', $penyewa);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode request tidak diizinkan.');
        }

        $in = $this->readInput();

        // Username mengikuti route, bukan input form
        $in['user_plg'] = $userPlg;

        $password = $_POST['sandi'] ?? '';

        $error = $this->validateInput($in, $password, true);

        if ($error !== null) {
            $this->render(
                'ubah',
                array_merge($penyewa, $in),
                $error
            );
            return;
        }

        $hash = $password !== ''
            ? password_hash($password, PASSWORD_DEFAULT)
            : null;

        $fotoKtpBaru = null;
        $fotoSimBaru = null;

        try {
            // Upload hanya jika admin memilih foto baru
            $fotoKtpBaru = $this->uploadFoto('ft_ktp');
            $fotoSimBaru = $this->uploadFoto('ft_sim_a');

            // Jika tidak upload foto baru, gunakan foto lama
            $fotoKtp = $fotoKtpBaru ?? ($penyewa['ft_ktp'] ?? null);
            $fotoSim = $fotoSimBaru ?? ($penyewa['ft_sim_a'] ?? null);

            $ok = $this->model->updateByAdmin(
                $userPlg,
                $in['nik'],
                $in['nm_lngkp'],
                $in['no_hp'],
                $in['almt_lngkp'],
                $hash,
                $fotoKtp,
                $fotoSim
            );

        } catch (Throwable $e) {
            error_log('Ubah penyewa gagal: ' . $e->getMessage());
            $ok = false;
        }

        if (!$ok) {
            // Hapus foto baru jika update database gagal
            $this->hapusFileUpload('ft_ktp', $fotoKtpBaru);
            $this->hapusFileUpload('ft_sim_a', $fotoSimBaru);

            $this->render(
                'ubah',
                array_merge($penyewa, $in),
                'Data penyewa gagal diperbarui.'
            );
            return;
        }

        // Hapus foto lama setelah update database berhasil
        if (
            $fotoKtpBaru !== null &&
            !empty($penyewa['ft_ktp'])
        ) {
            $this->hapusFoto([
                ['ft_ktp', $penyewa['ft_ktp']]
            ]);
        }

        if (
            $fotoSimBaru !== null &&
            !empty($penyewa['ft_sim_a'])
        ) {
            $this->hapusFoto([
                ['ft_sim_a', $penyewa['ft_sim_a']]
            ]);
        }

        set_flash_message('success', 'Data penyewa berhasil diperbarui.');

        header('Location: ' . site_url('admin/mpenyewa'));
        exit;
    }

    // =========================
    // HAPUS PENYEWA
    // =========================

    public function hapus(string $userPlg): void
    {
        require_admin_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode request tidak diizinkan.');
        }

        $penyewa = $this->model->getProfil($userPlg);

        if ($penyewa === null) {
            set_flash_message('danger', 'Data penyewa tidak ditemukan.');

            header('Location: ' . site_url('admin/mpenyewa'));
            exit;
        }

        try {
            $ok = $this->model->delete($userPlg);

        } catch (Throwable $e) {
            error_log('Hapus penyewa gagal: ' . $e->getMessage());
            $ok = false;
        }

        if (!$ok) {
            set_flash_message(
                'danger',
                'Penyewa tidak dapat dihapus. Kemungkinan masih memiliki riwayat transaksi sewa.'
            );

            header('Location: ' . site_url('admin/mpenyewa'));
            exit;
        }

        // Hapus foto setelah data database berhasil dihapus
        $this->hapusFoto([
            ['ft_ktp', $penyewa['ft_ktp'] ?? null],
            ['ft_sim_a', $penyewa['ft_sim_a'] ?? null],
        ]);

        set_flash_message('success', 'Data penyewa berhasil dihapus.');

        header('Location: ' . site_url('admin/mpenyewa'));
        exit;
    }

    // =========================
    // HELPER MEMBACA INPUT
    // =========================

    private function readInput(): array
    {
        return [
            'user_plg'   => trim($_POST['user_plg'] ?? ''),
            'nik'        => trim($_POST['nik'] ?? ''),
            'nm_lngkp'   => trim($_POST['nm_lngkp'] ?? ''),
            'no_hp'      => trim($_POST['no_hp'] ?? ''),
            'almt_lngkp' => trim($_POST['almt_lngkp'] ?? ''),
        ];
    }

    // =========================
    // HELPER RENDER VIEW
    // =========================

    private function render(
        ?string $mode,
        ?array $dipilih = null,
        ?string $error = null,
        ?array $flash = null
    ): void {
        $this->view('admin/mpenyewa_view', [
            'username'        => $_SESSION['admin_username'] ?? '',
            'penyewa'         => $this->model->getAll(),
            'flash'           => $flash,
            'mode'            => $mode,
            'penyewa_dipilih'  => $dipilih,
            'error'           => $error,
        ]);
    }

    // =========================
    // VALIDASI INPUT
    // =========================

    private function validateInput(
        array $in,
        string $password,
        bool $isEdit
    ): ?string {

        if (
            !$isEdit &&
            !preg_match('/\A[A-Za-z0-9._-]{1,15}\z/', $in['user_plg'])
        ) {
            return 'Username harus 1–15 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.';
        }

        if (!$isEdit && strlen($password) < 8) {
            return 'Password minimal 8 karakter.';
        }

        if (
            $isEdit &&
            $password !== '' &&
            (strlen($password) < 8 || strlen($password) > 72)
        ) {
            return 'Password baru harus 8–72 karakter.';
        }

        if (!preg_match('/\A[0-9]{16}\z/', $in['nik'])) {
            return 'NIK harus terdiri dari 16 digit angka.';
        }

        if (
            $in['nm_lngkp'] === '' ||
            mb_strlen($in['nm_lngkp']) > 100
        ) {
            return 'Nama lengkap wajib diisi, maksimal 100 karakter.';
        }

        if (
            $in['no_hp'] === '' ||
            strlen($in['no_hp']) > 15 ||
            !preg_match('/\A[0-9+(). -]+\z/', $in['no_hp'])
        ) {
            return 'Nomor HP wajib diisi dan formatnya tidak valid.';
        }

        if (
            $in['almt_lngkp'] === '' ||
            mb_strlen($in['almt_lngkp']) > 300
        ) {
            return 'Alamat wajib diisi, maksimal 300 karakter.';
        }

        return null;
    }

    // =========================
    // HELPER FOLDER UPLOAD
    // =========================

    private function folderUpload(): string
    {
        return dirname(APPPATH)
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'penyewa'
            . DIRECTORY_SEPARATOR;
    }

    // =========================
    // UPLOAD FOTO KTP DAN SIM
    // =========================

    private function uploadFoto(string $field): ?string
    {
        if (!in_array($field, ['ft_ktp', 'ft_sim_a'], true)) {
            throw new RuntimeException('Jenis foto tidak diizinkan.');
        }

        if (
            !isset($_FILES[$field]) ||
            $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload foto gagal.');
        }

        // Maksimal 2 MB
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('Ukuran foto maksimal 2 MB.');
        }

        // Validasi MIME berdasarkan isi file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            throw new RuntimeException(
                'Format foto harus JPG, PNG, atau WEBP.'
            );
        }

        $dir = $this->folderUpload();

        if (
            !is_dir($dir) &&
            !mkdir($dir, 0755, true) &&
            !is_dir($dir)
        ) {
            throw new RuntimeException('Folder upload gagal dibuat.');
        }

        $filename = $field . '_'
            . time() . '_'
            . bin2hex(random_bytes(8))
            . '.' . $allowed[$mime];

        $destination = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Foto gagal dipindahkan.');
        }

        return $filename;
    }

    // =========================
    // HAPUS FOTO BARU JIKA GAGAL
    // =========================

    private function hapusFileUpload(
        string $field,
        ?string $filename
    ): void {
        if (
            !in_array($field, ['ft_ktp', 'ft_sim_a'], true) ||
            !is_string($filename) ||
            $filename === ''
        ) {
            return;
        }

        $pattern = '/^'
            . preg_quote($field, '/')
            . '_\d{10,14}_[a-f0-9]{16}\.(jpg|jpeg|png|webp)$/i';

        if (!preg_match($pattern, $filename)) {
            return;
        }

        $path = $this->folderUpload() . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    }

    // =========================
    // HAPUS FOTO LAMA
    // =========================

    private function hapusFoto(array $items): void
    {
        foreach ($items as [$field, $filename]) {
            $this->hapusFileUpload($field, $filename);
        }
    }
}

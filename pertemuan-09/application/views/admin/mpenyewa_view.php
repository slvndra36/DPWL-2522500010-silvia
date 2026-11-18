
<?php
require APPPATH . '/views/templates/header.php';
require APPPATH . '/views/templates/sidebar.php';

$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$penyewa = $penyewa ?? [];
$flash = $flash ?? null;
$error = $error ?? null;
$mode = $mode ?? null;
$p = $penyewa_dipilih ?? [];

$isForm = in_array($mode, ['tambah', 'ubah'], true);
$isEdit = $mode === 'ubah';

$flashClass = (($flash['type'] ?? '') === 'success')
    ? 'alert-success'
    : 'alert-danger';

$formAction = $isEdit
    ? site_url('admin/mpenyewa/ubah/' . rawurlencode((string) ($p['user_plg'] ?? '')))
    : site_url('admin/mpenyewa/tambah');

$folderFoto = 'uploads/penyewa/';
?>

<div class="content-wrapper">

    <section class="content-header">
        <h1>
            Manajemen Penyewa
            <small>Kelola data penyewa</small>
        </h1>

        <ol class="breadcrumb">
            <li>
                <a href="<?= $e(site_url('admin')) ?>">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </li>
            <li class="active">Manajemen Penyewa</li>
        </ol>
    </section>

    <section class="content">

        <?php if (!empty($flash)): ?>
            <div class="alert <?= $flashClass ?> alert-dismissible">
                <button type="button" class="close"
                    data-dismiss="alert">&times;</button>

                <i class="fa <?= (($flash['type'] ?? '') === 'success')
                    ? 'fa-check' : 'fa-warning' ?>"></i>

                <?= $e($flash['message'] ?? '') ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close"
                    data-dismiss="alert">&times;</button>

                <i class="fa fa-warning"></i>
                <?= $e($error) ?>
            </div>
        <?php endif; ?>

        <!-- TABEL DATA PENYEWA -->
        <div class="box">

            <div class="box-header">
                <h3 class="box-title">Data Penyewa</h3>

                <div class="box-tools">
                    <a href="<?= $e(site_url('admin/mpenyewa/tambah')) ?>"
                        class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Tambah Penyewa
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">

                    <table id="penyewaTable"
                        class="table table-bordered table-striped js-data-table">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Foto KTP</th>
                                <th>Foto SIM A</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($penyewa as $no => $item): ?>

                                <tr>
                                    <td><?= (int) $no + 1 ?></td>
                                    <td><?= $e($item['user_plg'] ?? '') ?></td>
                                    <td><?= $e($item['nik'] ?? '') ?></td>
                                    <td><?= $e($item['nm_lngkp'] ?? '') ?></td>
                                    <td><?= $e($item['no_hp'] ?? '') ?></td>
                                    <td><?= $e($item['almt_lngkp'] ?? '') ?></td>

                                    <!-- FOTO KTP -->
                                    <td>
                                        <?php if (!empty($item['ft_ktp'])): ?>

                                            <a href="<?= $e(base_url(
                                                $folderFoto . rawurlencode($item['ft_ktp'])
                                            )) ?>" target="_blank">

                                                <img src="<?= $e(base_url(
                                                    $folderFoto . rawurlencode($item['ft_ktp'])
                                                )) ?>"
                                                    alt="Foto KTP"
                                                    style="width:80px;height:55px;object-fit:contain;border:1px solid #ddd;">
                                            </a>

                                        <?php else: ?>
                                            <span class="text-muted">
                                                Belum ada foto
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- FOTO SIM A -->
                                    <td>
                                        <?php if (!empty($item['ft_sim_a'])): ?>

                                            <a href="<?= $e(base_url(
                                                $folderFoto . rawurlencode($item['ft_sim_a'])
                                            )) ?>" target="_blank">

                                                <img src="<?= $e(base_url(
                                                    $folderFoto . rawurlencode($item['ft_sim_a'])
                                                )) ?>"
                                                    alt="Foto SIM A"
                                                    style="width:80px;height:55px;object-fit:contain;border:1px solid #ddd;">
                                            </a>

                                        <?php else: ?>
                                            <span class="text-muted">
                                                Belum ada foto
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- AKSI -->
                                    <td>
                                        <a class="btn btn-warning btn-xs"
                                            href="<?= $e(site_url(
                                                'admin/mpenyewa/ubah/' .
                                                rawurlencode($item['user_plg'])
                                            )) ?>">
                                            <i class="fa fa-edit"></i> Ubah
                                        </a>

                                        <form method="post"
                                            style="display:inline"
                                            action="<?= $e(site_url(
                                                'admin/mpenyewa/hapus/' .
                                                rawurlencode($item['user_plg'])
                                            )) ?>"
                                            onsubmit="return confirm('Hapus penyewa ini beserta foto terkait?');">

                                            <button type="submit"
                                                class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

        <!-- FORM TAMBAH DAN UBAH -->
        <?php if ($isForm): ?>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        <?= $isEdit ? 'Ubah Data Penyewa' : 'Tambah Penyewa' ?>
                    </h3>
                </div>

                <form role="form"
                    method="post"
                    enctype="multipart/form-data"
                    action="<?= $e($formAction) ?>">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="user_plg">Username</label>

                            <input type="text"
                                class="form-control"
                                id="user_plg"
                                name="user_plg"
                                maxlength="15"
                                value="<?= $e($p['user_plg'] ?? '') ?>"
                                <?= $isEdit ? 'readonly' : '' ?>
                                required>
                        </div>

                        <div class="form-group">
                            <label for="sandi">
                                Password
                                <?php if ($isEdit): ?>
                                    <small>(Kosongkan jika tidak diganti)</small>
                                <?php endif; ?>
                            </label>

                            <input type="password"
                                class="form-control"
                                id="sandi"
                                name="sandi"
                                minlength="8"
                                maxlength="72"
                                autocomplete="new-password"
                                <?= $isEdit ? '' : 'required' ?>>
                        </div>

                        <div class="form-group">
                            <label for="nik">NIK (16 digit)</label>

                            <input type="text"
                                class="form-control"
                                id="nik"
                                name="nik"
                                maxlength="16"
                                pattern="[0-9]{16}"
                                value="<?= $e($p['nik'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="nm_lngkp">Nama Lengkap</label>

                            <input type="text"
                                class="form-control"
                                id="nm_lngkp"
                                name="nm_lngkp"
                                maxlength="100"
                                value="<?= $e($p['nm_lngkp'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="no_hp">No. HP</label>

                            <input type="text"
                                class="form-control"
                                id="no_hp"
                                name="no_hp"
                                maxlength="15"
                                value="<?= $e($p['no_hp'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="almt_lngkp">Alamat</label>

                            <textarea class="form-control"
                                id="almt_lngkp"
                                name="almt_lngkp"
                                rows="3"
                                maxlength="300"
                                required><?= $e($p['almt_lngkp'] ?? '') ?></textarea>
                        </div>

                        <hr>
                 <!-- KTP SIM PERTEMUAN 8 -->
                        <!-- UPLOAD FOTO KTP -->
                        <div class="form-group">
                            <label for="ft_ktp">Foto KTP</label>

                            <?php if (!empty($p['ft_ktp'])): ?>
                                <div class="mb-2">
                                    <p>Foto KTP saat ini:</p>

                                    <img src="<?= $e(base_url(
                                        $folderFoto . rawurlencode($p['ft_ktp'])
                                    )) ?>"
                                        alt="Foto KTP"
                                        style="width:250px;max-height:180px;object-fit:contain;border:1px solid #ddd;padding:5px;">
                                </div>
                            <?php endif; ?>

                            <input type="file"
                                class="form-control"
                                id="ft_ktp"
                                name="ft_ktp"
                                accept=".jpg,.jpeg,.png,.webp">

                            <small class="text-muted">
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                <?= $isEdit ? 'Kosongkan jika tidak ingin mengganti foto.' : '' ?>
                            </small>
                        </div>

                        <!-- UPLOAD FOTO SIM A -->
                        <div class="form-group">
                            <label for="ft_sim_a">Foto SIM A</label>

                            <?php if (!empty($p['ft_sim_a'])): ?>
                                <div class="mb-2">
                                    <p>Foto SIM A saat ini:</p>

                                    <img src="<?= $e(base_url(
                                        $folderFoto . rawurlencode($p['ft_sim_a'])
                                    )) ?>"
                                        alt="Foto SIM A"
                                        style="width:250px;max-height:180px;object-fit:contain;border:1px solid #ddd;padding:5px;">
                                </div>
                            <?php endif; ?>

                            <input type="file"
                                class="form-control"
                                id="ft_sim_a"
                                name="ft_sim_a"
                                accept=".jpg,.jpeg,.png,.webp">

                            <small class="text-muted">
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                <?= $isEdit ? 'Kosongkan jika tidak ingin mengganti foto.' : '' ?>
                            </small>
                        </div>

                    </div>

                    <div class="box-footer">

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Penyewa
                        </button>

                        <a href="<?= $e(site_url('admin/mpenyewa')) ?>"
                            class="btn btn-default">
                            <i class="fa fa-times"></i> Batal
                        </a>

                    </div>

                </form>
            </div>

        <?php endif; ?>

    </section>
</div>

<?php require APPPATH . '/views/templates/footer.php'; ?>

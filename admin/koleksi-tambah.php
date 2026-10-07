<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");

    if ($nama === "" || $kategori === "") {
        $error = "Nama koleksi dan kategori wajib diisi.";
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES UPLOAD GAMBAR
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        if (
            !isset($_FILES["gambar"]) ||
            $_FILES["gambar"]["error"] !== UPLOAD_ERR_OK
        ) {

            $error = "Gambar wajib dipilih.";

        } else {

            $file = $_FILES["gambar"];

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $mime = $finfo->file(
                $file["tmp_name"]
            );

            if (!isset($allowedTypes[$mime])) {

                $error = "Format gambar harus JPG, PNG, atau WEBP.";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $error = "Ukuran gambar maksimal 5 MB.";

            } else {

                $extension = $allowedTypes[$mime];

                $namaFile =
                    uniqid("koleksi_", true)
                    . "."
                    . $extension;

                $folder = "../uploads/koleksi/";

                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }

                $tujuan = $folder . $namaFile;

                if (!move_uploaded_file(
                    $file["tmp_name"],
                    $tujuan
                )) {

                    $error = "Gambar gagal diupload.";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN KE DATABASE
                    |--------------------------------------------------------------------------
                    */

                    $sql = "
                        INSERT INTO koleksi
                        (
                            nama,
                            kategori,
                            deskripsi,
                            gambar
                        )
                        VALUES (?, ?, ?, ?)
                    ";

                    $stmt = $conn->prepare($sql);

                    if (!$stmt) {

                        $error = "Query database gagal.";

                        if (file_exists($tujuan)) {
                            unlink($tujuan);
                        }

                    } else {

                        $stmt->bind_param(
                            "ssss",
                            $nama,
                            $kategori,
                            $deskripsi,
                            $namaFile
                        );

                        if ($stmt->execute()) {

                            header("Location: koleksi.php");
                            exit;

                        } else {

                            $error = "Data koleksi gagal disimpan.";

                            if (file_exists($tujuan)) {
                                unlink($tujuan);
                            }
                        }

                        $stmt->close();
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Tambah Koleksi - TUKANG EMAS REZKY
    </title>

    <link
        rel="stylesheet"
        href="../style.css"
    >


    <!--
    ============================================================
    CSS KHUSUS HALAMAN TAMBAH KOLEKSI
    ============================================================
    -->

    <style>

        /* Area halaman */

        .admin-form-section {
            min-height: 100vh;
            padding: 80px 20px;
            background: #0b0b0b;
        }


        /* Container form */

        .admin-form-container {
            max-width: 800px;
            margin: 0 auto;
        }


        /* Judul halaman */

        .admin-form-heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-form-heading h1 {
            color: #d4af37;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .admin-form-heading p {
            color: #bdbdbd;
            font-size: 15px;
        }


        /* Card form */

        .admin-form-card {
            background: #151515;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }


        /* Group */

        .admin-form-group {
            margin-bottom: 24px;
        }


        /* Label */

        .admin-form-group label {
            display: block;
            margin-bottom: 9px;

            color: #f5f5f5;

            font-size: 15px;
            font-weight: 600;
        }


        /* Input */

        .admin-form-group input[type="text"],
        .admin-form-group select,
        .admin-form-group textarea {

            width: 100%;

            box-sizing: border-box;

            padding: 13px 15px;

            background: #202020;

            color: #f5f5f5;

            border: 1px solid #3a3a3a;

            border-radius: 6px;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition: 0.3s;
        }


        /* Fokus */

        .admin-form-group input[type="text"]:focus,
        .admin-form-group select:focus,
        .admin-form-group textarea:focus {

            border-color: #d4af37;

            box-shadow:
                0 0 0 2px rgba(212, 175, 55, 0.12);
        }


        /* Textarea */

        .admin-form-group textarea {

            min-height: 140px;

            resize: vertical;
        }


        /* Select */

        .admin-form-group select {

            cursor: pointer;
        }


        /* File input */

        .admin-file-wrapper {

            background: #202020;

            border: 1px solid #3a3a3a;

            border-radius: 6px;

            padding: 8px;

        }

        .admin-file-wrapper input[type="file"] {

            width: 100%;

            color: #f5f5f5;

            font-family: inherit;

            font-size: 14px;

        }


        /* Keterangan file */

        .admin-file-info {

            margin-top: 8px;

            color: #999;

            font-size: 12px;
        }


        /* Pesan error */

        .admin-error {

            background: rgba(180, 40, 40, 0.15);

            border: 1px solid #8f3030;

            color: #ff8a8a;

            padding: 13px 15px;

            border-radius: 6px;

            margin-bottom: 25px;

            font-size: 14px;
        }


        /* Tombol */

        .admin-form-actions {

            display: flex;

            justify-content: flex-end;

            gap: 12px;

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #2d2d2d;
        }


        /* Tombol batal */

        .admin-btn-cancel {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 120px;

            padding: 13px 22px;

            color: #d4af37;

            background: transparent;

            border: 1px solid #d4af37;

            border-radius: 5px;

            text-decoration: none;

            font-weight: 600;

            transition: 0.3s;

            box-sizing: border-box;
        }

        .admin-btn-cancel:hover {

            background: #d4af37;

            color: #111;
        }


        /* Tombol simpan */

        .admin-btn-save {

            min-width: 170px;

            padding: 13px 22px;

            background: #d4af37;

            color: #111;

            border: 1px solid #d4af37;

            border-radius: 5px;

            font-family: inherit;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.3s;
        }

        .admin-btn-save:hover {

            background: #e4c354;

            border-color: #e4c354;

        }


        /* Responsive */

        @media (max-width: 600px) {

            .admin-form-section {
                padding: 50px 15px;
            }

            .admin-form-card {
                padding: 25px 20px;
            }

            .admin-form-heading h1 {
                font-size: 26px;
            }

            .admin-form-actions {

                flex-direction: column;

            }

            .admin-btn-cancel,
            .admin-btn-save {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<!-- ============================================================
     NAVBAR
============================================================ -->

<nav class="navbar">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand"
        >
            TUKANG EMAS REZKY
        </a>


        <div>

            <a
                href="index.php"
                class="nav-link"
            >
                Dashboard
            </a>

        </div>

    </div>

</nav>



<!-- ============================================================
     FORM TAMBAH KOLEKSI
============================================================ -->

<section class="admin-form-section">

    <div class="admin-form-container">


        <!-- Judul -->

        <div class="admin-form-heading">

            <h1>
                Tambah Koleksi
            </h1>

            <p>
                Tambahkan koleksi perhiasan baru
                ke website TUKANG EMAS REZKY.
            </p>

        </div>



        <!-- Card -->

        <div class="admin-form-card">


            <!-- Error -->

            <?php if ($error !== ""): ?>

                <div class="admin-error">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>



            <!-- Form -->

            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- Nama -->

                <div class="admin-form-group">

                    <label for="nama">
                        Nama Koleksi
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Contoh: Cincin Nikah"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["nama"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>



                <!-- Kategori -->

                <div class="admin-form-group">

                    <label for="kategori">
                        Kategori
                    </label>

                    <select
                        id="kategori"
                        name="kategori"
                        required
                    >

                        <option value="">
                            Pilih Kategori
                        </option>

                        <option
                            value="Cincin"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Cincin"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Cincin
                        </option>

                        <option
                            value="Anting"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Anting"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Anting
                        </option>

                        <option
                            value="Kalung"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Kalung"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Kalung
                        </option>

                        <option
                            value="Gelang"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Gelang"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Gelang
                        </option>

                        <option
                            value="Liontin"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Liontin"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Liontin
                        </option>

                        <option
                            value="Custom"
                            <?php
                            if (
                                ($_POST["kategori"] ?? "")
                                === "Custom"
                            ) {
                                echo "selected";
                            }
                            ?>
                        >
                            Custom
                        </option>

                    </select>

                </div>



                <!-- Deskripsi -->

                <div class="admin-form-group">

                    <label for="deskripsi">
                        Deskripsi
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Deskripsi koleksi..."
                    ><?php
                    echo htmlspecialchars(
                        $_POST["deskripsi"] ?? ""
                    );
                    ?></textarea>

                </div>



                <!-- Gambar -->

                <div class="admin-form-group">

                    <label for="gambar">
                        Gambar
                    </label>

                    <div class="admin-file-wrapper">

                        <input
                            type="file"
                            id="gambar"
                            name="gambar"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                        >

                    </div>

                    <div class="admin-file-info">

                        Format:
                        JPG, PNG, atau WEBP.
                        Maksimal 5 MB.

                    </div>

                </div>



                <!-- Tombol -->

                <div class="admin-form-actions">

                    <a
                        href="koleksi.php"
                        class="admin-btn-cancel"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="admin-btn-save"
                    >
                        Simpan Koleksi
                    </button>

                </div>


            </form>

        </div>

    </div>

</section>


</body>

</html>
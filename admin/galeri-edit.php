<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";


$id = (int)($_GET["id"] ?? 0);


if ($id <= 0) {
    die("ID tidak valid.");
}


$stmt = $conn->prepare(
    "SELECT * FROM galeri WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Data galeri tidak ditemukan.");
}


$data = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul = trim($_POST["judul"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");

    $gambarBaru = $data["gambar"];


    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["gambar"];


        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];


        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file(
            $file["tmp_name"]
        );


        if (!isset($allowedTypes[$mime])) {
            die("Format gambar tidak valid.");
        }


        if ($file["size"] > 5 * 1024 * 1024) {
            die("Ukuran gambar maksimal 5 MB.");
        }


        $extension = $allowedTypes[$mime];


        $gambarBaru =
            uniqid("galeri_", true) .
            "." .
            $extension;


        $folder = "../uploads/galeri/";


        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }


        if (!move_uploaded_file(
            $file["tmp_name"],
            $folder . $gambarBaru
        )) {
            die("Gambar gagal diupload.");
        }


        if (
            !empty($data["gambar"]) &&
            file_exists($folder . $data["gambar"])
        ) {
            unlink(
                $folder . $data["gambar"]
            );
        }
    }


    $sql = "
        UPDATE galeri
        SET
            judul = ?,
            deskripsi = ?,
            gambar = ?
        WHERE id = ?
    ";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "sssi",
        $judul,
        $deskripsi,
        $gambarBaru,
        $id
    );


    if (!$stmt->execute()) {
        die("Data gagal diperbarui.");
    }


    header("Location: galeri.php");
    exit;
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

    <title>Edit Galeri - TUKANG EMAS REZKY</title>

    <link rel="stylesheet" href="../style.css">

</head>


<body>

<nav class="navbar">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand"
        >
            TUKANG EMAS REZKY
        </a>

    </div>

</nav>


<section class="section">

    <div class="container">

        <div class="section-heading">

            <h1>Edit Galeri</h1>

            <p>
                Ubah informasi foto galeri.
            </p>

        </div>


        <div class="custom-form-card">

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <label>
                    Judul
                </label>

                <input
                    type="text"
                    name="judul"
                    class="form-control"
                    value="<?php echo htmlspecialchars($data["judul"]); ?>"
                    required
                >


                <label>
                    Deskripsi
                </label>

                <textarea
                    name="deskripsi"
                    class="form-control"
                    rows="5"
                ><?php echo htmlspecialchars($data["deskripsi"]); ?></textarea>


                <label>
                    Ganti Gambar
                </label>

                <input
                    type="file"
                    name="gambar"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <p>
                    Kosongkan jika tidak ingin mengganti gambar.
                </p>


                <div class="form-buttons">

                    <a
                        href="galeri.php"
                        class="btn-outline-gold"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-gold"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</section>

</body>

</html>
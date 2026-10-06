<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: pesanan.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil data pesanan
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM pesanan
    WHERE id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: pesanan.php");
    exit;
}

$pesanan = $result->fetch_assoc();

$error = "";


/*
|--------------------------------------------------------------------------
| Update data
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama =
        trim($_POST["nama"] ?? "");

    $jenisPerhiasan =
        trim($_POST["jenis_perhiasan"] ?? "");

    $ukuran =
        trim($_POST["ukuran"] ?? "");

    $kadar =
        trim($_POST["kadar"] ?? "");

    $berat =
        (float) ($_POST["berat"] ?? 0);

    $hargaEmas =
        (float) ($_POST["harga_emas"] ?? 0);

    $desain =
        trim($_POST["desain"] ?? "");

    $ukiran =
        trim($_POST["ukiran"] ?? "");

    $catatan =
        trim($_POST["catatan"] ?? "");

    $status =
        trim($_POST["status"] ?? "Menunggu");


    if (
        $nama === "" ||
        $jenisPerhiasan === "" ||
        $ukuran === "" ||
        $kadar === "" ||
        $berat <= 0 ||
        $hargaEmas <= 0
    ) {

        $error =
            "Data pesanan belum lengkap.";

    } else {

        $estimasi =
            $berat * $hargaEmas;


        $sql = "
            UPDATE pesanan

            SET
                nama = ?,
                jenis_perhiasan = ?,
                ukuran = ?,
                kadar = ?,
                berat = ?,
                harga_emas = ?,
                desain = ?,
                ukiran = ?,
                catatan = ?,
                estimasi = ?,
                status = ?

            WHERE id = ?
        ";

        $stmt = $conn->prepare($sql);

if (!$stmt) {

    $error = "Query database gagal.";

} else {

    $stmt->bind_param(
        "ssssddsssdsi",
        $nama,
        $jenisPerhiasan,
        $ukuran,
        $kadar,
        $berat,
        $hargaEmas,
        $desain,
        $ukiran,
        $catatan,
        $estimasi,
        $status,
        $id
    );

    if ($stmt->execute()) {

        header(
            "Location: pesanan-detail.php?id=" . $id
        );

        exit;

    } else {

        $error =
            "Pesanan gagal diperbarui: " .
            $stmt->error;
    }

    $stmt->close();
}
            /*
             * Perbaikan format bind_param:
             * s s s s d d s s s d s i
             */

            $stmt->close();

            $stmt =
                $conn->prepare($sql);

            $stmt->bind_param(
                "ssssddsssdsi",
                $nama,
                $jenisPerhiasan,
                $ukuran,
                $kadar,
                $berat,
                $hargaEmas,
                $desain,
                $ukiran,
                $catatan,
                $estimasi,
                $status,
                $id
            );

            if ($stmt->execute()) {

                header(
                    "Location: pesanan-detail.php?id=" . $id
                );

                exit;

            } else {

                $error =
                    "Pesanan gagal diperbarui.";
            }

            $stmt->close();
        }
    }

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Pesanan - TUKANG EMAS REZKY</title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../style.css">

</head>

<body>

<nav class="navbar navbar-expand-lg fixed-top">

    <div class="container">

        <a class="navbar-brand" href="../index.html">
            TUKANG EMAS
            <span class="brand-gold">REZKY</span>
        </a>

        <div class="ms-auto">

            <a
                class="nav-link d-inline-block"
                href="pesanan.php"
            >
                Pesanan
            </a>

            <a
                class="nav-link d-inline-block"
                href="logout.php"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<section class="page-header">

    <div class="container text-center">

        <p class="small-title">
            DATABASE
        </p>

        <h1>
            Edit
            <span class="brand-gold">Pesanan</span>
        </h1>

    </div>

</section>


<section class="section">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="custom-form-card">

                    <?php if ($error): ?>

                        <div class="error-message mb-4">
                            <?= htmlspecialchars($error); ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST">

                        <div class="mb-4">

                            <label>Nama</label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                value="<?= htmlspecialchars($pesanan["nama"]); ?>"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Jenis Perhiasan</label>

                            <select
                                name="jenis_perhiasan"
                                class="form-select"
                                required
                            >

                                <?php

                                $jenisList = [
                                    "Cincin",
                                    "Anting",
                                    "Kalung",
                                    "Gelang",
                                    "Liontin"
                                ];

                                foreach ($jenisList as $jenis):

                                ?>

                                    <option
                                        value="<?= $jenis; ?>"
                                        <?= $pesanan["jenis_perhiasan"] === $jenis ? "selected" : ""; ?>
                                    >
                                        <?= $jenis; ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label>Ukuran</label>

                            <input
                                type="text"
                                name="ukuran"
                                class="form-control"
                                value="<?= htmlspecialchars($pesanan["ukuran"]); ?>"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Kadar</label>

                            <select
                                name="kadar"
                                class="form-select"
                                required
                            >

                                <option
                                    value="88%"
                                    <?= $pesanan["kadar"] === "88%" ? "selected" : ""; ?>
                                >
                                    88%
                                </option>

                                <option
                                    value="90%"
                                    <?= $pesanan["kadar"] === "90%" ? "selected" : ""; ?>
                                >
                                    90%
                                </option>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label>Berat (gram)</label>

                            <input
                                type="number"
                                name="berat"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                value="<?= htmlspecialchars($pesanan["berat"]); ?>"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Harga Emas / Gram</label>

                            <input
                                type="number"
                                name="harga_emas"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="<?= htmlspecialchars($pesanan["harga_emas"]); ?>"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Desain / Referensi</label>

                            <textarea
                                name="desain"
                                class="form-control"
                                rows="4"
                            ><?= htmlspecialchars($pesanan["desain"]); ?></textarea>

                        </div>


                        <div class="mb-4">

                            <label>Ukiran</label>

                            <input
                                type="text"
                                name="ukiran"
                                class="form-control"
                                value="<?= htmlspecialchars($pesanan["ukiran"]); ?>"
                            >

                        </div>


                        <div class="mb-4">

                            <label>Catatan</label>

                            <textarea
                                name="catatan"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars($pesanan["catatan"]); ?></textarea>

                        </div>


                        <div class="mb-4">

                            <label>Status Pesanan</label>

                            <select
                                name="status"
                                class="form-select"
                            >

                                <?php

                                $statusList = [
                                    "Menunggu",
                                    "Diproses",
                                    "Selesai",
                                    "Dibatalkan"
                                ];

                                foreach ($statusList as $status):

                                ?>

                                    <option
                                        value="<?= $status; ?>"
                                        <?= $pesanan["status"] === $status ? "selected" : ""; ?>
                                    >
                                        <?= $status; ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="form-buttons">

                            <button
                                type="submit"
                                class="btn-gold"
                            >
                                Simpan Perubahan
                            </button>

                            <a
                                href="pesanan-detail.php?id=<?= $id; ?>"
                                class="btn-outline-gold"
                            >
                                Batal
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>

</body>
</html>
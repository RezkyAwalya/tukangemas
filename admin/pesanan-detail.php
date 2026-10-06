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

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Detail Pesanan - TUKANG EMAS REZKY</title>

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
            Detail
            <span class="brand-gold">Pesanan</span>
        </h1>

    </div>

</section>


<section class="section">

    <div class="container">

        <div class="custom-form-card">

            <div class="row g-4">

                <div class="col-md-6">

                    <p>
                        <strong>Nama</strong><br>
                        <?= htmlspecialchars($pesanan["nama"]); ?>
                    </p>

                    <p>
                        <strong>Jenis Perhiasan</strong><br>
                        <?= htmlspecialchars($pesanan["jenis_perhiasan"]); ?>
                    </p>

                    <p>
                        <strong>Ukuran</strong><br>
                        <?= htmlspecialchars($pesanan["ukuran"]); ?>
                    </p>

                    <p>
                        <strong>Kadar</strong><br>
                        <?= htmlspecialchars($pesanan["kadar"]); ?>
                    </p>

                    <p>
                        <strong>Berat</strong><br>
                        <?= number_format(
                            $pesanan["berat"],
                            2,
                            ",",
                            "."
                        ); ?>
                        gram
                    </p>

                </div>


                <div class="col-md-6">

                    <p>
                        <strong>Harga Emas / Gram</strong><br>
                        Rp <?= number_format(
                            $pesanan["harga_emas"],
                            0,
                            ",",
                            "."
                        ); ?>
                    </p>

                    <p>
                        <strong>Estimasi</strong><br>
                        Rp <?= number_format(
                            $pesanan["estimasi"],
                            0,
                            ",",
                            "."
                        ); ?>
                    </p>

                    <p>
                        <strong>Status</strong><br>
                        <?= htmlspecialchars($pesanan["status"]); ?>
                    </p>

                    <p>
                        <strong>Tanggal</strong><br>
                        <?= date(
                            "d-m-Y H:i",
                            strtotime($pesanan["tanggal"])
                        ); ?>
                    </p>

                </div>


                <div class="col-12">

                    <hr>

                    <p>
                        <strong>Desain / Referensi</strong>
                    </p>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                $pesanan["desain"]
                            )
                        ); ?>
                    </p>


                    <p>
                        <strong>Ukiran</strong>
                    </p>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                $pesanan["ukiran"]
                            )
                        ); ?>
                    </p>


                    <p>
                        <strong>Catatan</strong>
                    </p>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                $pesanan["catatan"]
                            )
                        ); ?>
                    </p>

                </div>

            </div>


            <div class="form-buttons mt-4">

                <a
                    href="pesanan-edit.php?id=<?= $pesanan["id"]; ?>"
                    class="btn-gold"
                >
                    Edit
                </a>

                <a
                    href="pesanan.php"
                    class="btn-outline-gold"
                >
                    Kembali
                </a>

            </div>

        </div>

    </div>

</section>

</body>
</html>
<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Statistik Pesanan
|--------------------------------------------------------------------------
*/

$query =
    $conn->query(
        "SELECT COUNT(*) AS total FROM pesanan"
    );

$totalPesanan =
    $query->fetch_assoc()["total"];


$query =
    $conn->query(
        "SELECT COUNT(*) AS total
         FROM pesanan
         WHERE status = 'Menunggu'"
    );

$totalMenunggu =
    $query->fetch_assoc()["total"];


/*
|--------------------------------------------------------------------------
| Harga emas terbaru
|--------------------------------------------------------------------------
*/

$queryHarga =
    $conn->query(
        "SELECT *
         FROM harga_emas
         ORDER BY tanggal DESC, waktu_update DESC
         LIMIT 1"
    );

$hargaEmas =
    $queryHarga->fetch_assoc();


/*
|--------------------------------------------------------------------------
| Pesanan terbaru
|--------------------------------------------------------------------------
*/

$pesananTerbaru =
    $conn->query(
        "SELECT *
         FROM pesanan
         ORDER BY tanggal DESC
         LIMIT 5"
    );

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin - TUKANG EMAS REZKY</title>

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


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg fixed-top">

    <div class="container">

        <a class="navbar-brand"
           href="../index.html">

            TUKANG EMAS
            <span class="brand-gold">
                REZKY
            </span>

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#adminNavbar"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="adminNavbar"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="index.php"
                    >
                        Dashboard
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="pesanan.php"
                    >
                        Pesanan
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="logout.php"
                    >
                        Logout
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>



<!-- HEADER -->

<section class="page-header">

    <div class="container text-center">

        <p class="small-title">
            ADMIN PANEL
        </p>

        <h1>
            Dashboard
            <span class="brand-gold">
                Admin
            </span>
        </h1>

        <p>
            Selamat datang,
            <?= htmlspecialchars($_SESSION["admin"]); ?>.
        </p>

    </div>

</section>



<!-- DASHBOARD -->

<section class="section">

    <div class="container">


        <div class="row g-4">


            <!-- TOTAL PESANAN -->

            <div class="col-md-4">

                <div class="home-feature">

                    <i class="bi bi-receipt"></i>

                    <h3>
                        <?= $totalPesanan; ?>
                    </h3>

                    <p>
                        Total Pesanan
                    </p>

                </div>

            </div>


            <!-- MENUNGGU -->

            <div class="col-md-4">

                <div class="home-feature">

                    <i class="bi bi-clock"></i>

                    <h3>
                        <?= $totalMenunggu; ?>
                    </h3>

                    <p>
                        Pesanan Menunggu
                    </p>

                </div>

            </div>


            <!-- HARGA -->

            <div class="col-md-4">

                <div class="home-feature">

                    <i class="bi bi-gem"></i>

                    <h3>
                        Harga Emas
                    </h3>

                    <?php if ($hargaEmas): ?>

                        <p>
                            88%:
                            Rp <?= number_format(
                                $hargaEmas["harga_88"],
                                0,
                                ",",
                                "."
                            ); ?>
                        </p>

                        <p>
                            90%:
                            Rp <?= number_format(
                                $hargaEmas["harga_90"],
                                0,
                                ",",
                                "."
                            ); ?>
                        </p>

                        <small>
                            <?= htmlspecialchars(
                                $hargaEmas["sumber"]
                            ); ?>
                        </small>

                    <?php else: ?>

                        <p>
                            Harga belum tersedia.
                        </p>

                    <?php endif; ?>

                </div>

            </div>


        </div>



        <!-- PESANAN TERBARU -->

        <div class="section-heading text-center mt-5 mb-4">

            <p class="small-title">
                DATABASE
            </p>

            <h2>
                Pesanan Terbaru
            </h2>

        </div>


        <div class="custom-form-card">

            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Nama</th>
                            <th>Perhiasan</th>
                            <th>Kadar</th>
                            <th>Berat</th>
                            <th>Estimasi</th>
                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($pesananTerbaru->num_rows > 0): ?>

                        <?php $no = 1; ?>

                        <?php while (
                            $row =
                            $pesananTerbaru->fetch_assoc()
                        ): ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row["nama"]
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row["jenis_perhiasan"]
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row["kadar"]
                                    ); ?>
                                </td>

                                <td>
                                    <?= number_format(
                                        $row["berat"],
                                        2,
                                        ",",
                                        "."
                                    ); ?>
                                    gram
                                </td>

                                <td>
                                    Rp <?= number_format(
                                        $row["estimasi"],
                                        0,
                                        ",",
                                        "."
                                    ); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row["status"]
                                    ); ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center"
                            >
                                Belum ada pesanan.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <div class="form-buttons mt-4">

                <a
                    href="pesanan.php"
                    class="btn-gold"
                >
                    Kelola Pesanan
                </a>

            </div>

        </div>



        <!-- HARGA EMAS -->

        <div class="custom-form-card mt-5">

            <div class="section-heading mb-4">

                <p class="small-title">
                    HARGA EMAS
                </p>

                <h2>
                    Harga Emas Terkini
                </h2>

                <p>
                    Harga diambil secara otomatis.
                    Admin tidak dapat mengubah harga emas.
                </p>

            </div>


            <?php if ($hargaEmas): ?>

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="estimate-box">

                            <div class="estimate-title">
                                KADAR 88%
                            </div>

                            <div class="estimate-result">

                                Rp <?= number_format(
                                    $hargaEmas["harga_88"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </div>

                            <p>
                                per gram
                            </p>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="estimate-box">

                            <div class="estimate-title">
                                KADAR 90%
                            </div>

                            <div class="estimate-result">

                                Rp <?= number_format(
                                    $hargaEmas["harga_90"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </div>

                            <p>
                                per gram
                            </p>

                        </div>

                    </div>

                </div>


                <p>
                    Sumber:
                    <?= htmlspecialchars(
                        $hargaEmas["sumber"]
                    ); ?>
                </p>


            <?php else: ?>

                <p>
                    Harga emas belum tersedia.
                </p>

            <?php endif; ?>

        </div>


    </div>

</section>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
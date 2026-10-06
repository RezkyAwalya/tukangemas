<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$sql = "
    SELECT *
    FROM pesanan
    ORDER BY tanggal DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pesanan - TUKANG EMAS REZKY</title>

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

        <a
            class="navbar-brand"
            href="../index.html"
        >

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
                        class="nav-link"
                        href="index.php"
                    >
                        Dashboard
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link active"
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



<section class="page-header">

    <div class="container text-center">

        <p class="small-title">
            DATABASE
        </p>

        <h1>
            Kelola
            <span class="brand-gold">
                Pesanan
            </span>
        </h1>

        <p>
            Kelola data pesanan pelanggan.
        </p>

    </div>

</section>



<section class="section">

    <div class="container">


        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2>
                    Daftar Pesanan
                </h2>

            </div>


            <a
                href="pesanan-tambah.php"
                class="btn-gold"
            >
                + Tambah Pesanan
            </a>

        </div>



        <div class="custom-form-card">

            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Nama</th>
                            <th>Perhiasan</th>
                            <th>Ukuran</th>
                            <th>Kadar</th>
                            <th>Berat</th>
                            <th>Estimasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php $no = 1; ?>

                        <?php while (
                            $row =
                            $result->fetch_assoc()
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
                                        $row["ukuran"]
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

                                <td>
                                    <?= date(
                                        "d-m-Y H:i",
                                        strtotime(
                                            $row["tanggal"]
                                        )
                                    ); ?>
                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a
                                            href="pesanan-detail.php?id=<?= $row["id"]; ?>"
                                            class="btn-outline-gold"
                                        >
                                            Detail
                                        </a>

                                        <a
                                            href="pesanan-edit.php?id=<?= $row["id"]; ?>"
                                            class="btn-gold"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="pesanan-hapus.php?id=<?= $row["id"]; ?>"
                                            class="btn-outline-gold"
                                            onclick="return confirm('Yakin ingin menghapus pesanan ini?');"
                                        >
                                            Hapus
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="10"
                                class="text-center"
                            >
                                Belum ada pesanan.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
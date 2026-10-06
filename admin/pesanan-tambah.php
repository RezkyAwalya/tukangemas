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
    $jenisPerhiasan = trim($_POST["jenis_perhiasan"] ?? "");
    $ukuran = trim($_POST["ukuran"] ?? "");
    $kadar = trim($_POST["kadar"] ?? "");
    $berat = (float) ($_POST["berat"] ?? 0);
    $hargaEmas = (float) ($_POST["harga_emas"] ?? 0);
    $desain = trim($_POST["desain"] ?? "");
    $ukiran = trim($_POST["ukiran"] ?? "");
    $catatan = trim($_POST["catatan"] ?? "");
    $estimasi = (float) ($_POST["estimasi"] ?? 0);
    $status = trim($_POST["status"] ?? "Menunggu");

    if (
        $nama === "" ||
        $jenisPerhiasan === "" ||
        $ukuran === "" ||
        $kadar === "" ||
        $berat <= 0 ||
        $hargaEmas <= 0
    ) {

        $error = "Data pesanan belum lengkap.";

    } else {

        $estimasi = $berat * $hargaEmas;

        $sql = "
            INSERT INTO pesanan
            (
                nama,
                jenis_perhiasan,
                ukuran,
                kadar,
                berat,
                harga_emas,
                desain,
                ukiran,
                catatan,
                estimasi,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Query database gagal.";

        } else {

            $stmt->bind_param(
                "ssssddsssds",
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
                $status
            );

            if ($stmt->execute()) {

                header("Location: pesanan.php");
                exit;

            } else {

                $error = "Pesanan gagal ditambahkan.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Pesanan - TUKANG EMAS REZKY</title>

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

            <a class="nav-link d-inline-block"
               href="pesanan.php">
                Pesanan
            </a>

            <a class="nav-link d-inline-block"
               href="logout.php">
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
            Tambah
            <span class="brand-gold">Pesanan</span>
        </h1>

        <p>
            Tambahkan data pesanan ke database.
        </p>

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

                                <option value="">
                                    Pilih jenis
                                </option>

                                <option value="Cincin">
                                    Cincin
                                </option>

                                <option value="Anting">
                                    Anting
                                </option>

                                <option value="Kalung">
                                    Kalung
                                </option>

                                <option value="Gelang">
                                    Gelang
                                </option>

                                <option value="Liontin">
                                    Liontin
                                </option>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label>Ukuran</label>

                            <input
                                type="text"
                                name="ukuran"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Kadar</label>

                            <select
                                name="kadar"
                                id="kadar"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih kadar
                                </option>

                                <option value="88%">
                                    88%
                                </option>

                                <option value="90%">
                                    90%
                                </option>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label>Berat (gram)</label>

                            <input
                                type="number"
                                name="berat"
                                id="berat"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Harga Emas / Gram</label>

                            <input
                                type="number"
                                name="harga_emas"
                                id="hargaEmas"
                                class="form-control"
                                min="0"
                                step="0.01"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label>Desain / Referensi</label>

                            <textarea
                                name="desain"
                                class="form-control"
                                rows="4"
                            ></textarea>

                        </div>


                        <div class="mb-4">

                            <label>Ukiran</label>

                            <input
                                type="text"
                                name="ukiran"
                                class="form-control"
                            >

                        </div>


                        <div class="mb-4">

                            <label>Catatan</label>

                            <textarea
                                name="catatan"
                                class="form-control"
                                rows="3"
                            ></textarea>

                        </div>


                        <div class="mb-4">

                            <label>Status</label>

                            <select
                                name="status"
                                class="form-select"
                            >

                                <option value="Menunggu">
                                    Menunggu
                                </option>

                                <option value="Diproses">
                                    Diproses
                                </option>

                                <option value="Selesai">
                                    Selesai
                                </option>

                                <option value="Dibatalkan">
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        <div class="estimate-box mb-4">

                            <div class="estimate-title">
                                ESTIMASI
                            </div>

                            <div
                                id="hasilEstimasi"
                                class="estimate-result"
                            >
                                Rp 0
                            </div>

                        </div>


                        <div class="form-buttons">

                            <button
                                type="submit"
                                class="btn-gold"
                            >
                                Simpan Pesanan
                            </button>

                            <a
                                href="pesanan.php"
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


<script>

const berat =
    document.getElementById("berat");

const harga =
    document.getElementById("hargaEmas");

const hasil =
    document.getElementById("hasilEstimasi");


function hitung() {

    const nilaiBerat =
        Number(berat.value);

    const nilaiHarga =
        Number(harga.value);

    const total =
        nilaiBerat * nilaiHarga;

    hasil.textContent =
        new Intl.NumberFormat(
            "id-ID",
            {
                style: "currency",
                currency: "IDR",
                maximumFractionDigits: 0
            }
        ).format(total);
}


berat.addEventListener("input", hitung);
harga.addEventListener("input", hitung);

</script>

</body>
</html>
<?php

session_start();

require_once "../config/db.php";

if (isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $sql = "
        SELECT *
        FROM admin
        WHERE username = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "s",
            $username
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin =
                $result->fetch_assoc();

            if ($password === $admin["password"]) {

                $_SESSION["admin"] =
                    $admin["username"];

                header("Location: index.php");
                exit;

            } else {

                $error =
                    "Password salah.";

            }

        } else {

            $error =
                "Username tidak ditemukan.";

        }

        $stmt->close();

    } else {

        $error =
            "Terjadi kesalahan database.";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login Admin - TUKANG EMAS REZKY</title>

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


<section class="page-header">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-6 col-lg-5">

                <div class="custom-form-card">

                    <div class="text-center mb-4">

                        <p class="small-title">
                            ADMIN
                        </p>

                        <h1>
                            TUKANG EMAS
                            <span class="brand-gold">
                                REZKY
                            </span>
                        </h1>

                        <p>
                            Silakan masuk ke halaman admin.
                        </p>

                    </div>


                    <?php if ($error): ?>

                        <div class="error-message mb-3">
                            <?= htmlspecialchars($error); ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST">

                        <div class="mb-4">

                            <label for="username">
                                Username
                            </label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn-gold w-100"
                        >
                            Login
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


</body>
</html>
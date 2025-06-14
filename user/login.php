<?php
// 登入頁面
require_once "./connect.php";

?>

<!doctype html>
<html lang="en" class="layout-wide customizer-hide" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>登入 - Xin Chào心橋</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./logo.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

    <!-- custom.css -->
    <link rel="stylesheet" href="./css/custom.css">

    <style>
        body {
            background-image: url("./img/vie.webp");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .authentication-wrapper {
            backdrop-filter: blur(2px);
            background: rgba(0, 0, 0, 0.1);
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .placeholder-color::placeholder {
            font-weight: bold;
            color: #888;
            opacity: 1;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        .authentication-wrapper.authentication-basic .authentication-inner {
            max-inline-size: 360px;
        }
    </style>

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card px-6 py-0">
                    <div class="card-body">


                        <div class="text-center mb-4">
                            <h3 class="text-primary">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>登入
                            </h3>
                        </div>

                        <hr>

                        <form id="memberForm" class="mb-4" action="./doLogin.php" method="post" enctype="multipart/form-data">

                            <!-- 帳號與密碼 -->

                            <div class="input-group mb-6">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control placeholder-color" id="account" name="account"
                                    placeholder="帳號 * (5-20字元)" minlength="5" maxlength="20" required autofocus />
                            </div>

                            <div class="input-group mb-10">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password" class="form-control placeholder-color"
                                    name="password" placeholder="密碼 * (5-20字元)" minlength="5" maxlength="20" required />
                            </div>


                            <!-- 提交按鈕 -->
                            <div class="d-flex gap-3 justify-content-center mb-12">
                                <button type="submit" class="btn btn-lg btn-primary col-lg-12">
                                    登入
                                </button>
                            </div>

                            <hr>

                            <!-- 新增使用者 -->
                            <div class="text-center mb-3">
                                <label class="fw-bold">
                                    需要新增使用者嗎?
                                    <a href="./add.php" class="btn btn-sm btn-warning ms-2">新增</a>
                                </label>
                            </div>

                            <!-- Logo -->
                            <div class="d-flex justify-content-center">
                                <a href="" class="d-flex align-items-end">
                                    <span>
                                        <span><img class="w-40px h-40px" src="./images/logo.png" alt=""></span>
                                    </span>
                                    <span class="fs-4 fw-bold ms-2 demo text-heading">心橋</span>
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
                <!-- /Register Card -->
            </div>
        </div>
    </div>
    <!-- / Content -->


    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

</body>

</html>
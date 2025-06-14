<?php
// 登入驗證及會員資訊
session_start();
if (!isset($_SESSION["members"])) {
    header("location: ./login.php");
    exit;
}

// 修改會員主要程式
require_once "./connect.php";
require_once "./Utilities.php";

if (!isset($_GET["id"])) {
    alertGoTo("請從正常管道進入", "./index.php");
    exit;
}

$id = intval($_GET["id"]);

// 修改為 members 表並包含關聯資料
$sql = "SELECT m.*, g.name as gender_name, ml.name as level_name
        FROM `members` m
        LEFT JOIN `genders` g ON m.gender_id = g.id
        LEFT JOIN `member_level` ml ON m.status_id = ml.id
        WHERE m.`is_valid` = 1 AND m.`id` = ?";

// 取得性別和等級選項
$genderSql = "SELECT * FROM `genders` ORDER BY id ASC";
$levelSql = "SELECT * FROM `member_level` ORDER BY id ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        alertGoTo("沒有這個使用者", "./index.php");
    }

    // 取得選項資料
    $genderStmt = $pdo->prepare($genderSql);
    $genderStmt->execute();
    $genders = $genderStmt->fetchAll(PDO::FETCH_ASSOC);

    $levelStmt = $pdo->prepare($levelSql);
    $levelStmt->execute();
    $levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "錯誤: " . $e->getMessage();
    goBack();
    exit;
}
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>修改會員資料 - Xin Chào心橋</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="./images/logo.ico" />

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

    <!-- custom.css -->
    <link rel="stylesheet" href="./css/custom.css">

    <!-- update.css -->
    <link rel="stylesheet" href="./css/update.css">

    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar position-relative">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.php" class="app-brand-link">
                        <span>
                            <span><img class="w-40px h-40px" src="./images/logo.png" alt=""></span>
                        </span>
                        <span class="fs-4 fw-bold ms-2 app-brand-text demo menu-text align-items-center">心橋</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link ms-auto">
                        <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
                    </a>
                </div>

                <div class="menu-divider mt-0"></div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">

                    <!-- 會員管理 -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-text fw-bold">後台功能</span>
                    </li>
                    <li class="menu-item active">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class=" fa-solid fa-users me-4"></i>
                            <div class="menu-text fw-bold" data-i18n="Dashboards">會員管理</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="index.php" class="menu-link">
                                    <div class="menu-text fw-bold">會員列表</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="add.php" class="menu-link">
                                    <div class="menu-text fw-bold">新增會員</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="deletedMembers.php" class="menu-link">
                                    <div class="menu-text fw-bold">已刪除會員</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 商品管理 -->
                    <li class="menu-item">
                        <a href="productTrip-Index.php" class="menu-link menu-toggle">
                            <i class="fa-solid fa-map-location-dot me-2 menu-text"></i>
                            <div class="menu-text fw-bold" data-i18n="Layouts">商品管理</div>
                        </a>

                        <ul class="menu-sub">
                            <li class="menu-item ">
                                <a href="productTrip-Index.php" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Without menu">行程列表</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="./addTrip.php" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Without menu">新增行程</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 票券管理 -->
                    <li class="menu-item">
                        <a href="#" class="menu-link menu-toggle">
                            <i class="fa-solid fa-ticket me-2 menu-text"></i>
                            <div class="menu-text fw-bold" data-i18n="Dashboards">票券管理</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="#" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Analytics">票券列表</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Analytics">新增票券</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 優惠券管理 -->
                    <li class="menu-item">
                        <a href="#" class="menu-link menu-toggle">
                            <i class="fa-solid fa-tags me-2 menu-text"></i>
                            <div class="menu-text fw-bold" data-i18n="Dashboards">優惠券管理</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item active">
                                <a href="#" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Analytics">優惠券列表</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link">
                                    <div class="menu-text fw-bold" data-i18n="Analytics">新增優惠券</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- 登出 -->
                    <li class="menu-header small text-uppercase">
                        <span class="menu-text fw-bold">會員資訊</span>
                    </li>
                    <div class="container text-center">

                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <img class="head" src="./img/<?= $_SESSION["members"]["avatar"] ?>" alt="">
                            <div class="menu-text fw-bold align-self-center"><?= $_SESSION["members"]["name"] ?></div>
                        </div>

                        <li class="menu-item row justify-content-center">
                            <a href="./doLogout.php"
                                class="btn rounded-pill btn-gradient-success btn-ban col-10 justify-content-center">
                                <div class="menu-text fw-bold"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>登出</div>
                            </a>
                        </li>
                    </div>

                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <div class="d-flex align-items-center">
                    <span class="layout-menu-toggle align-items-xl-center m-4 d-xl-none">
                        <a class="me-xl-6 text-primary" href="javascript:void(0)">
                            <i class="icon-base bx bx-menu icon-md"></i>
                        </a>
                    </span>
                    <nav aria-label="breadcrumb" class="mt-4 m-xl-6">
                        <!-- 需要調整文字和active的顏色 -->
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#" class="text-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="./index.php" class="text-primary">會員管理</a>
                            </li>
                            <li class="breadcrumb-item active" class="text-primary">修改會員</li>
                        </ol>
                    </nav>
                </div>

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">

                        <!-- Form Section -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-primary">
                                            <a class="btn btn-primary me-3" href="./index.php">
                                                <i class="fas fa-arrow-left"></i>
                                            </a>
                                            <i class="fas fa-user-edit me-2"></i>修改會員資料
                                        </h5>
                                        <small class="text-muted text-primary">
                                            會員編號: <?= $row['id'] ?>
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <form action="./doUpdate.php" method="post" enctype="multipart/form-data">
                                            <!-- 抓ID 去判斷 -->
                                            <input type="hidden" name="id" value="<?= $row["id"] ?>">

                                            <!-- 帳號與密碼同一行 -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-user"></i></span>
                                                        <input readonly name="account" type="text"
                                                            class="form-control placeholder-color"
                                                            value="<?= htmlspecialchars($row["account"]) ?>"
                                                            placeholder="帳號">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-lock"></i></span>
                                                        <input name="password" type="password"
                                                            class="form-control placeholder-color"
                                                            placeholder="新密碼 (不修改請留空)">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 姓名 -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input required name="name" type="text"
                                                    class="form-control placeholder-color"
                                                    value="<?= htmlspecialchars($row["name"]) ?>" placeholder="姓名 *">
                                            </div>

                                            <!-- 信箱 -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input readonly name="email" type="email"
                                                    class="form-control placeholder-color"
                                                    value="<?= htmlspecialchars($row["email"]) ?>" placeholder="電子信箱">
                                            </div>

                                            <!-- 電話 -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input required name="phone" type="tel"
                                                    class="form-control placeholder-color"
                                                    value="<?= htmlspecialchars($row["phone"]) ?>" placeholder="電話號碼 *">
                                            </div>

                                            <!-- 生日 -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i
                                                        class="fas fa-birthday-cake"></i></span>
                                                <input required name="birth_date" type="date" class="form-control"
                                                    value="<?= $row["birth_date"] ?>">
                                            </div>

                                            <!-- 性別 -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">性別 *</label>
                                                <div class="d-flex gap-4">
                                                    <?php foreach ($genders as $gender): ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="gender_id"
                                                                id="gender<?= $gender['id'] ?>" value="<?= $gender['id'] ?>"
                                                                <?= $row['gender_id'] == $gender['id'] ? 'checked' : '' ?>
                                                                required>
                                                            <label class="form-check-label fw-bold"
                                                                for="gender<?= $gender['id'] ?>">
                                                                <i
                                                                    class="fas fa-<?= $gender['name'] === '男性' ? 'mars text-primary' : 'venus text-danger' ?> me-1"></i>
                                                                <?= htmlspecialchars($gender['name']) ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <!-- 會員等級 -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="fas fa-crown"></i></span>
                                                <select name="status_id" class="form-control fw-bold" required>
                                                    <?php foreach ($levels as $level): ?>
                                                        <option value="<?= $level['id'] ?>"
                                                            <?= $row['status_id'] == $level['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($level['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- 大頭貼 -->
                                            <div class="input-group mb-4">
                                                <span class="input-group-text"><i class="fas fa-image"></i></span>
                                                <input name="avatar" type="file" class="form-control" accept="image/*"
                                                    id="avatarInput">
                                            </div>

                                            <div class="d-flex gap-3 justify-content-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>更新資料
                                                </button>
                                                <a class="btn btn-outline-success fw-bold" href="./index.php">
                                                    <i class="fas fa-times me-1"></i>取消
                                                </a>
                                                <a class="btn btn-outline-primary fw-bold"
                                                    href="./view.php?id=<?= $row['id'] ?>">
                                                    <i class="fas fa-eye me-1"></i>查看詳情
                                                </a>

                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- 操作歷史紀錄 -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-history me-2"></i>操作說明
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>修改須知</h6>
                                            <ul class="mb-0">
                                                <li>帳號和電子信箱為唯一值，無法修改</li>
                                                <li>密碼欄位留空表示不修改現有密碼</li>
                                                <li>大頭貼支援 JPG、PNG、GIF 格式，建議尺寸 200x200 像素</li>
                                                <li>標示 * 的欄位為必填項目</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 右側顯示目前頭像 -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user-circle me-2"></i>會員資訊
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if ($row["avatar"]): ?>
                                            <img class="img-fluid rounded preview-avatar" id="avatarPreview"
                                                src="./img/<?= htmlspecialchars($row["avatar"]) ?>" alt="會員頭像"
                                                onerror="this.src='./img/avatar1.jpg'">
                                        <?php else: ?>
                                            <img class="img-fluid rounded preview-avatar" id="avatarPreview"
                                                src="./img/avatar1.jpg" alt="預設頭像">
                                        <?php endif; ?>

                                        <div class="mt-3">
                                            <div class="member-info justify-content-center mb-3">
                                                <div class="text-center">
                                                    <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                                    <small
                                                        class="text-muted">@<?= htmlspecialchars($row['account']) ?></small>
                                                </div>
                                            </div>

                                            <div class="text-start">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-id-badge me-2"></i>會員編號: <?= $row['id'] ?>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar-plus me-2"></i>註冊日期:
                                                    <?= date('Y-m-d', strtotime($row['created_at'])) ?>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-clock me-2"></i>最後更新:
                                                    <?= date('Y-m-d H:i', strtotime($row['updated_at'])) ?>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i
                                                        class="fas fa-envelope me-2"></i><?= htmlspecialchars($row['email']) ?>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i
                                                        class="fas fa-phone me-2"></i><?= htmlspecialchars($row['phone']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 會員等級資訊 -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-crown me-2"></i>會員等級
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php
                                        function getLevelBadgeClass($level)
                                        {
                                            switch ($level) {
                                                case '銅':
                                                    return 'bg-label-info rounded-pill';
                                                case '銀':
                                                    return 'bg-label-secondary rounded-pill';
                                                case '金':
                                                    return 'bg-label-warning rounded-pill';
                                                case '鑽':
                                                    return 'color-vip rounded-pill';
                                                default:
                                                    return 'bg-light text-dark';
                                            }
                                        }
                                        ?>
                                        <span
                                            class="badge <?= getLevelBadgeClass($row['level_name']) ?> fs-6 px-3 py-2">
                                            <i class="fas fa-crown me-1"></i><?= htmlspecialchars($row['level_name']) ?>
                                        </span>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <?php
                                                switch ($row['level_name']) {
                                                    case '銅':
                                                        echo '基礎會員，享有標準服務';
                                                        break;
                                                    case '銀':
                                                        echo '優質會員，享有進階服務';
                                                        break;
                                                    case '金':
                                                        echo '黃金會員，享有專屬優惠';
                                                        break;
                                                    case '鑽':
                                                        echo 'VIP會員，享有頂級服務';
                                                        break;
                                                }
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-fluid">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    Copyright ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    <a href="./index.php" target="_blank" class="footer-link">心橋❤️</a>
                                    by 前端67-第四組
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a href="./index.php" target="_blank" class="footer-link me-4">關於我們</a>
                                    <a href="./index.php" class="footer-link me-4" target="_blank">相關服務</a>
                                    <a href="./index.php" target="_blank" class="footer-link">進階設定</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <script>

        // 圖片預覽功能
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');

            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];

                    if (file) {
                        // 檢查檔案類型
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('請選擇 JPG、PNG 或 GIF 格式的圖片');
                            this.value = '';
                            return;
                        }

                        // 檢查檔案大小 (5MB)
                        const maxSize = 5 * 1024 * 1024;
                        if (file.size > maxSize) {
                            alert('圖片檔案不能超過 5MB');
                            this.value = '';
                            return;
                        }

                        // 創建 FileReader 來讀取檔案
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            // 更新預覽圖片
                            avatarPreview.src = e.target.result;

                            // 添加淡入效果
                            avatarPreview.style.opacity = '0.5';
                            setTimeout(() => {
                                avatarPreview.style.opacity = '1';
                            }, 100);
                        };

                        reader.onerror = function () {
                            alert('圖片讀取失敗，請重新選擇');
                            avatarInput.value = '';
                        };

                        // 讀取檔案為 data URL
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>

</html>
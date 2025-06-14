<?php
// 登入驗證及會員資訊
session_start();
if (!isset($_SESSION["members"])) {
  header("location: ./login.php");
  exit;
}

require_once "./connect.php";
require_once "./Utilities.php";

// 分頁設定
$perPage = 25;
$page = intval($_GET["page"] ?? 1);
$pageStart = ($page - 1) * $perPage;

// 排序設定
$sortBy = $_GET['sort'] ?? 'id';
$sortOrder = $_GET['order'] ?? 'ASC';
$allowedSort = ['id', 'name', 'email', 'gender_name', 'level_name', 'is_banned', 'created_at'];
$allowedOrder = ['ASC', 'DESC'];

// 搜尋功能（新增日期篩選）
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$level = $_GET['level'] ?? '';
$start_date = $_GET['start_date'] ?? '';  // 新增
$end_date = $_GET['end_date'] ?? '';      // 新增
$searchWhere = '';
$searchParams = [];

if (!empty($search)) {
  $searchWhere .= " AND (m.account LIKE :search OR m.name LIKE :search OR m.email LIKE :search)";
  $searchParams[':search'] = "%$search%";
}

if (!empty($status)) {
  if ($status === 'banned') {
    $searchWhere .= " AND mb.id IS NOT NULL AND mb.unbaded_at IS NULL";
  } elseif ($status === 'active') {
    $searchWhere .= " AND (mb.id IS NULL OR mb.unbaded_at IS NOT NULL)";
  }
}

// 新增會員等級篩選
if (!empty($level)) {
  $searchWhere .= " AND m.status_id = :level";
  $searchParams[':level'] = $level;
}

// 新增日期篩選邏輯
if (!empty($start_date) && !empty($end_date)) {
  $searchWhere .= " AND DATE(m.created_at) BETWEEN :start_date AND :end_date";
  $searchParams[':start_date'] = $start_date;
  $searchParams[':end_date'] = $end_date;
} elseif (!empty($start_date)) {
  $searchWhere .= " AND DATE(m.created_at) >= :start_date";
  $searchParams[':start_date'] = $start_date;
} elseif (!empty($end_date)) {
  $searchWhere .= " AND DATE(m.created_at) <= :end_date";
  $searchParams[':end_date'] = $end_date;
}

// 主查詢 - 加入關聯表
$sql = "SELECT m.*, g.name as gender_name, ml.name as level_name,
               CASE WHEN mb.id IS NOT NULL AND mb.unbaded_at IS NULL THEN 1 ELSE 0 END as is_banned,
               br.name as ban_reason
        FROM members m
        LEFT JOIN genders g ON m.gender_id = g.id
        LEFT JOIN member_level ml ON m.status_id = ml.id
        LEFT JOIN member_ban mb ON m.id = mb.member_id AND mb.unbaded_at IS NULL
        LEFT JOIN ban_reasons br ON mb.reason_id = br.id
        WHERE m.is_valid = 1 $searchWhere
        ORDER BY $sortBy $sortOrder
        LIMIT $perPage OFFSET $pageStart";

$sqlCount = "SELECT COUNT(*) FROM members m 
             LEFT JOIN member_ban mb ON m.id = mb.member_id AND mb.unbaded_at IS NULL
             WHERE m.is_valid = 1 $searchWhere";

// 統計查詢
$statsQueries = [
  'total' => "SELECT COUNT(*) FROM members WHERE is_valid = 1",
  'active' => "SELECT COUNT(*) FROM members m 
                 LEFT JOIN member_ban mb ON m.id = mb.member_id AND mb.unbaded_at IS NULL
                 WHERE m.is_valid = 1 AND (mb.id IS NULL OR mb.unbaded_at IS NOT NULL)",
  'banned' => "SELECT COUNT(*) FROM members m 
                 INNER JOIN member_ban mb ON m.id = mb.member_id 
                 WHERE m.is_valid = 1 AND mb.unbaded_at IS NULL",
  'diamond' => "SELECT COUNT(*) FROM members WHERE is_valid = 1 AND status_id = 4"
];

// 取得會員等級選項
$levelQuery = "SELECT `id`, `name` FROM `member_level` ORDER BY `id`";

try {
  // 執行主查詢
  $stmt = $pdo->prepare($sql);
  foreach ($searchParams as $key => $value) {
    $stmt->bindValue($key, $value);
  }
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 計算總數
  $stmtCount = $pdo->prepare($sqlCount);
  foreach ($searchParams as $key => $value) {
    $stmtCount->bindValue($key, $value);
  }
  $stmtCount->execute();
  $totalCount = $stmtCount->fetchColumn();

  // 取得統計資料
  $stats = [];
  foreach ($statsQueries as $key => $query) {
    $statStmt = $pdo->prepare($query);
    $statStmt->execute();
    $stats[$key] = $statStmt->fetchColumn();
  }

  // 取得會員等級資料
  $levelStmt = $pdo->prepare($levelQuery);
  $levelStmt->execute();
  $levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "錯誤: " . $e->getMessage();
  exit;
}

$totalPage = ceil($totalCount / $perPage);

// 等級徽章顏色函數
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

// 生成排序連結函數（更新加入日期參數）
function getSortUrl($column, $currentSort, $currentOrder, $search = '', $status = '', $level = '', $start_date = '', $end_date = '', $page = 1)
{
  $newOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
  $params = [
    'sort' => $column,
    'order' => $newOrder,
    'page' => $page
  ];

  if (!empty($search))
    $params['search'] = $search;
  if (!empty($status))
    $params['status'] = $status;
  if (!empty($level))
    $params['level'] = $level;
  if (!empty($start_date))
    $params['start_date'] = $start_date;
  if (!empty($end_date))
    $params['end_date'] = $end_date;

  return '?' . http_build_query($params);
}

// 生成分頁連結函數（更新加入日期參數）
function getPaginationUrl($page, $search = '', $status = '', $level = '', $start_date = '', $end_date = '', $sort = '', $order = '')
{
  $params = ['page' => $page];

  if (!empty($search))
    $params['search'] = $search;
  if (!empty($status))
    $params['status'] = $status;
  if (!empty($level))
    $params['level'] = $level;
  if (!empty($start_date))
    $params['start_date'] = $start_date;
  if (!empty($end_date))
    $params['end_date'] = $end_date;
  if (!empty($sort))
    $params['sort'] = $sort;
  if (!empty($order))
    $params['order'] = $order;

  return '?' . http_build_query($params);
}

// 性別圖示函數
function getGenderIcon($gender)
{
  return $gender === '男性' ? 'fas fa-mars text-primary' : 'fas fa-venus text-danger';
}
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>會員管理系統 - Xin Chào心橋</title>

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

  <!-- index.css -->
  <link rel="stylesheet" href="./css/index.css">

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
          <li class="menu-item active open">
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
              <li class="breadcrumb-item active" class="text-primary">會員列表</li>
            </ol>
          </nav>
        </div>

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="container-fluid flex-grow-1 container-p-y">

            <!-- Search and Actions Section -->
            <div class="search-section z-4 position-sticky top-0 start-50">
              <form method="GET" class="row align-items-center mt-3">

                <!-- 保持當前排序參數 -->
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($sortOrder) ?>">

                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="input-group rounded-pill search-box">
                    <span class="input-group-text border border-warning"><i
                        class="fas fa-search text-primary"></i></span>
                    <input type="text" class="form-control placeholder-color border border-warning text-primary w-50"
                      placeholder="搜尋帳號、姓名或Email..." name="search" value="<?= htmlspecialchars($search) ?>">
                  </div>
                </div>

                <!-- 會員狀態 -->
                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                  <select class="form-select fw-bold text-primary" name="status">
                    <option value="">全部狀態</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>正常會員</option>
                    <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>已封鎖</option>
                  </select>
                </div>

                <!-- 會員等級 -->
                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                  <select class="form-select fw-bold text-primary" name="level">
                    <option value="">全部等級</option>
                    <?php foreach ($levels as $levelOption): ?>
                      <option value="<?= $levelOption['id'] ?>" <?= $level == $levelOption['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($levelOption['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- 註冊日期篩選 -->

                <div class="row my-4">
                  <label class="col-lg-1 col-form-label text-primary fw-bold"><i
                      class="fa-solid fa-calendar-days me-2"></i>開始日期</label>
                  <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                    <input type="date" class="form-control fw-bold text-primary" name="start_date"
                      value="<?= htmlspecialchars($start_date) ?>" placeholder="開始日期" title="註冊開始日期">
                  </div>


                  <label class="col-sm-1 col-form-label text-primary fw-bold"><i
                      class="fa-solid fa-calendar-days me-2"></i>結束日期</label>
                  <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                    <input type="date" class="form-control fw-bold text-primary" name="end_date"
                      value="<?= htmlspecialchars($end_date) ?>" placeholder="結束日期" title="註冊結束日期">
                  </div>
                </div>


                <div class="col-lg-12 col-md-12 mb-3">
                  <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-outline-primary fw-bold">
                      <i class="fas fa-search me-1"></i>篩選
                    </button>
                    <a href="index.php" class="btn btn-outline-success fw-bold">清除</a>
                    <a href="add.php" class="btn btn-primary">
                      <i class="fas fa-user-plus me-1"></i>新增
                    </a>
                  </div>
                </div>

              </form>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
              <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                          <i class="fas fa-users"></i>
                        </span>
                      </div>
                      <div>
                        <small class="text-muted d-block">總會員數</small>
                        <h4 class="card-title mb-0"><?= number_format($stats['total']) ?></h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                          <i class="fas fa-user-check"></i>
                        </span>
                      </div>
                      <div>
                        <small class="text-muted d-block">活躍會員</small>
                        <h4 class="card-title mb-0"><?= number_format($stats['active']) ?></h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-danger">
                          <i class="fas fa-user-times"></i>
                        </span>
                      </div>
                      <div>
                        <small class="text-muted d-block">封鎖會員</small>
                        <h4 class="card-title mb-0"><?= number_format($stats['banned']) ?></h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded color-vip">
                          <i class="fas fa-crown"></i>
                        </span>
                      </div>
                      <div>
                        <small class="text-muted d-block">鑽石會員</small>
                        <h4 class="card-title mb-0"><?= number_format($stats['diamond']) ?></h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Members Table -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0 text-primary">
                  <i class="fas fa-users me-2"></i>會員列表
                </h5>

                <!-- Pagination -->
                <?php if ($totalPage > 1): ?>
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <div class="pagination-info text-primary">
                        顯示第 <?= ($pageStart + 1) ?> - <?= min($pageStart + $perPage, $totalCount) ?> 筆
                      </div>
                    </div>
                    <div class="col-md-6">
                      <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                          <?php if ($page > 1): ?>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl(1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevrons-left"></i>
                              </a>
                            </li>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($page - 1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevron-left"></i>
                              </a>
                            </li>
                          <?php endif; ?>

                          <?php
                          $startPage = max(1, $page - 2);
                          $endPage = min($totalPage, $page + 2);
                          for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                              <a class="page-link"
                                href="<?= getPaginationUrl($i, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <?= $i ?>
                              </a>
                            </li>
                          <?php endfor; ?>

                          <?php if ($page < $totalPage): ?>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($page + 1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevron-right"></i>
                              </a>
                            </li>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($totalPage, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevrons-right"></i>
                              </a>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </nav>
                    </div>
                  </div>

                <?php endif; ?>

                <small class="text-muted text-primary">
                  <?php if (!empty($search) || !empty($status) || !empty($level) || !empty($start_date) || !empty($end_date)): ?>
                    顯示 <?= number_format($totalCount) ?> 筆篩選結果
                    (共 <?= number_format($stats['total']) ?> 筆)
                  <?php else: ?>
                    目前共 <?= number_format($totalCount) ?> 筆資料
                  <?php endif; ?>
                </small>

              </div>

              <!-- table data -->
              <div class="tab-content">
                <div class="tab-pane fade show active table-responsive full-width-card" id="navs-pills-top-profile"
                  role="tabpanel">
                  <table class="table align-middle text-nowrap w-100 text-center">
                    <thead class="">
                      <tr>
                        <th
                          class="sortable-header text-primary <?= $sortBy === 'id' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('id', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            #
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-start <?= $sortBy === 'name' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('name', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            會員資訊
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-start <?= $sortBy === 'email' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('email', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            聯絡資訊
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-start <?= $sortBy === 'gender_name' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('gender_name', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            性別
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-center <?= $sortBy === 'level_name' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('level_name', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            等級
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-center <?= $sortBy === 'is_banned' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('is_banned', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            狀態
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th
                          class="sortable-header text-primary text-center <?= $sortBy === 'created_at' ? 'active ' . strtolower($sortOrder) : '' ?>">
                          <a href="<?= getSortUrl('created_at', $sortBy, $sortOrder, $search, $status, $level, $start_date, $end_date, $page) ?>"
                            class="fs-5 fw-bold">
                            加入日期
                            <div class="sort-icons">
                              <i class="sort-icon up fas fa-caret-up"></i>
                              <i class="sort-icon down fas fa-caret-down"></i>
                            </div>
                          </a>
                        </th>
                        <th class="text-primary text-center fs-5 fw-bold">操作</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php if (empty($rows)): ?>
                        <tr>
                          <td colspan="8" class="text-center py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <div class="text-muted">
                              <?php if (!empty($search) || !empty($status) || !empty($level) || !empty($start_date) || !empty($end_date)): ?>
                                沒有符合搜尋條件的會員
                              <?php else: ?>
                                目前沒有會員資料
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                          <tr class="" data-member-id="<?= $row['id'] ?>">
                            <td>
                              <div>
                                <div class="fw-bold"><?= htmlspecialchars($row['id']) ?></div>
                              </div>
                            </td>
                            <td>
                              <div class="member-info">
                                <img src="./img/<?= htmlspecialchars($row['avatar']) ?>" alt="Avatar"
                                  class="rounded-circle avatar-sm" onerror="this.src='./img/avatar1.jpg'" />
                                <div class="text-start">
                                  <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                  <small class="text-muted">@<?= htmlspecialchars($row['account']) ?></small>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="text-start">
                                <div class="fw-bold"><?= htmlspecialchars($row['email']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($row['phone']) ?></small>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <i class="<?= getGenderIcon($row['gender_name']) ?> me-2"></i>
                                <span><?= htmlspecialchars($row['gender_name']) ?></span>
                              </div>
                            </td>
                            <td>
                              <span class="badge <?= getLevelBadgeClass($row['level_name']) ?>">
                                <i class="fas fa-crown me-1"></i><?= htmlspecialchars($row['level_name']) ?>
                              </span>
                            </td>
                            <td>
                              <?php if ($row['is_banned']): ?>
                                <span class="badge bg-label-danger rounded-pill member-status" data-bs-toggle="tooltip"
                                  title="<?= htmlspecialchars($row['ban_reason']) ?>">已封鎖</span>
                              <?php else: ?>
                                <span class="badge bg-label-success rounded-pill member-status">正常</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <small><?= date('Y-m-d', strtotime($row['created_at'])) ?></small>
                            </td>
                            <td>
                              <div class="action-buttons">
                                <a href="./view.php?id=<?= $row['id'] ?>" class="btn btn-sm rounded-pill btn-warning"
                                  data-bs-toggle="tooltip" title="查看詳情">
                                  <i class="fas fa-eye"></i>
                                </a>
                                <a href="./update.php?id=<?= $row['id'] ?>" class="btn btn-sm rounded-pill btn-info"
                                  data-bs-toggle="tooltip" title="修改資料">
                                  <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($row['is_banned']): ?>
                                  <button type="button" class="btn btn-sm rounded-pill btn-secondary btn-unban"
                                    data-member-id="<?= $row['id'] ?>"
                                    data-member-name="<?= htmlspecialchars($row['name']) ?>"
                                    data-member-account="<?= htmlspecialchars($row['account']) ?>"
                                    data-member-avatar="./img/<?= htmlspecialchars($row['avatar']) ?>"
                                    data-bs-toggle="tooltip" title="解除封鎖">
                                    <i class="fas fa-unlock"></i>
                                  </button>
                                <?php else: ?>
                                  <button type="button" class="btn btn-sm rounded-pill btn-primary btn-ban"
                                    data-member-id="<?= $row['id'] ?>"
                                    data-member-name="<?= htmlspecialchars($row['name']) ?>"
                                    data-member-account="<?= htmlspecialchars($row['account']) ?>"
                                    data-member-avatar="./img/<?= htmlspecialchars($row['avatar']) ?>"
                                    data-bs-toggle="tooltip" title="封鎖會員">
                                    <i class="fas fa-ban"></i>
                                  </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm rounded-pill btn-success btn-delete"
                                  data-member-id="<?= $row['id'] ?>"
                                  data-member-name="<?= htmlspecialchars($row['name']) ?>" data-bs-toggle="tooltip"
                                  title="刪除會員">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Pagination -->
              <?php if ($totalPage > 1): ?>
                <div class="card-footer">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <div class="pagination-info text-primary">
                        顯示第 <?= ($pageStart + 1) ?> - <?= min($pageStart + $perPage, $totalCount) ?> 筆，
                        共 <?= number_format($totalCount) ?> 筆資料
                      </div>
                    </div>
                    <div class="col-md-6">
                      <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                          <?php if ($page > 1): ?>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl(1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevrons-left"></i>
                              </a>
                            </li>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($page - 1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevron-left"></i>
                              </a>
                            </li>
                          <?php endif; ?>

                          <?php
                          $startPage = max(1, $page - 2);
                          $endPage = min($totalPage, $page + 2);
                          for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                              <a class="page-link"
                                href="<?= getPaginationUrl($i, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <?= $i ?>
                              </a>
                            </li>
                          <?php endfor; ?>

                          <?php if ($page < $totalPage): ?>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($page + 1, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevron-right"></i>
                              </a>
                            </li>
                            <li class="page-item">
                              <a class="page-link"
                                href="<?= getPaginationUrl($totalPage, $search, $status, $level, $start_date, $end_date, $sortBy, $sortOrder) ?>">
                                <i class="tf-icon bx bx-chevrons-right"></i>
                              </a>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </nav>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
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

  <!-- Ban Member Modal -->
  <div class="modal fade" id="banModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-ban me-2"></i>封鎖會員
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="banForm">
          <div class="modal-body">
            <input type="hidden" id="banMemberId">
            <div class="mb-3">
              <label class="form-label">會員資訊：</label>
              <div class="alert alert-info" id="memberInfo">
                <div class="d-flex align-items-center">
                  <img id="memberAvatar" src="" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                  <div>
                    <div class="fw-bold" id="memberName"></div>
                    <small class="text-muted" id="memberAccount"></small>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">封鎖原因：</label>
              <select class="form-select" id="banReason" required>
                <option value="">請選擇封鎖原因</option>
                <option value="1">違規發言</option>
                <option value="2">濫用功能</option>
                <option value="3">惡意洗版</option>
                <option value="4">詐騙行為</option>
                <option value="5">散布不實訊息</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-ban me-2"></i>確認封鎖
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Unban Member Modal -->
  <div class="modal fade" id="unbanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa-solid fa-unlock me-2"></i></i>解封鎖會員
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="unbanForm">
          <div class="modal-body">
            <input type="hidden" id="unbanMemberId">
            <div class="mb-3">
              <label class="form-label">會員資訊：</label>
              <div class="alert alert-info" id="UnbanMemberInfo">
                <div class="d-flex align-items-center">
                  <img id="unbanMemberAvatar" src="" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                  <div>
                    <div class="fw-bold" id="unbanMemberName"></div>
                    <small class="text-muted" id="unbanMemberAccount"></small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">
              <i class="fa-solid fa-unlock me-2"></i>確認解封鎖
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-trash me-2"></i>刪除會員
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
            <h4 class="mt-3">確定要刪除此會員嗎？</h4>
            <p class="text-muted">會員：<span id="deleteMemberName" class="fw-bold"></span></p>
          </div>
          <input type="hidden" id="deleteMemberId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" id="confirmDelete">
            <i class="fas fa-trash me-2"></i>確認刪除
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>

  <!-- 自己的 JS -->
  <script src="./js/modal-ban.js"></script>
  <script src="./js/modal-unban.js"></script>
  <script src="./js/modal-delete.js"></script>
  <script src="./js/modal-success.js"></script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      BanModalModule.init();
      UnbanModalModule.init();
      DeleteModalModule.init();
    });
  </script>
</body>

</html>
<?php
// 登入驗證及會員資訊
session_start();
if (!isset($_SESSION["members"])) {
  header("location: ./login.php");
  exit;
}

// 會員詳情查看頁面
require_once "./connect.php";
require_once "./Utilities.php";

date_default_timezone_set("Asia/Taipei");

if (!isset($_GET["id"])) {
  alertGoTo("請從正常管道進入", "./index.php");
  exit;
}

$id = intval($_GET["id"]);

// 取得會員詳細資料（包含關聯表資料）
$sql = "SELECT m.*, g.name as gender_name, ml.name as level_name
        FROM `members` m
        LEFT JOIN `genders` g ON m.gender_id = g.id
        LEFT JOIN `member_level` ml ON m.status_id = ml.id
        WHERE m.`is_valid` = 1 AND m.`id` = ?";

// 取得封鎖紀錄
$banSql = "SELECT mb.*, br.name as reason_name 
           FROM `member_ban` mb
           LEFT JOIN `ban_reasons` br ON mb.reason_id = br.id
           WHERE mb.member_id = ?
           ORDER BY mb.baded_at DESC";

try {
  // 取得會員基本資料
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id]);
  $member = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$member) {
    alertGoTo("找不到此會員", "./index.php");
    exit;
  }

  // 取得封鎖紀錄
  $banStmt = $pdo->prepare($banSql);
  $banStmt->execute([$id]);
  $banHistory = $banStmt->fetchAll(PDO::FETCH_ASSOC);

  // 檢查目前是否被封鎖
  $currentBan = null;
  foreach ($banHistory as $ban) {
    if (is_null($ban['unbaded_at'])) {
      $currentBan = $ban;
      break;
    }
  }

} catch (PDOException $e) {
  alertGoTo("資料庫錯誤: " . $e->getMessage(), "./index.php");
  exit;
}

// 計算年齡
function calculateAge($birthDate)
{
  $today = new DateTime();
  $birthday = new DateTime($birthDate);
  return $today->diff($birthday)->y;
}

// 等級徽章顏色
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

  <title>會員詳情 - <?= htmlspecialchars($member['name']) ?> - Xin Chào心橋</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="./images/logo.png" />

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

  <style>
    .color-vip {
      background-color: color-mix(in srgb, #fff 84%, #03c3ec) !important;
      color: #03c3ec !important;
    }

    .profile-avatar {
      width: 250px;
      height: 250px;
      object-fit: cover;
      border: 4px solid #f0f0f0;
    }

    .info-card {
      transition: transform 0.2s ease;
    }

    .info-card:hover {
      transform: translateY(-2px);
    }

    .status-banned {
      background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
      color: white;
    }

    .status-active {
      background: linear-gradient(135deg, #51cf66, #40c057);
      color: white;
    }

    .info-label {
      font-size: 0.875rem;
      color: #6c757d;
      margin-bottom: 0.25rem;
    }

    .info-value {
      font-weight: 600;
      color: #495057;
    }

    .head {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 2px;
      object-fit: cover;
    }
  </style>

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
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="#" class="text-primary">Home</a>
              </li>
              <li class="breadcrumb-item">
                <a href="./index.php" class="text-primary">會員管理</a>
              </li>
              <li class="breadcrumb-item active" class="text-primary">會員詳情</li>
            </ol>
          </nav>
        </div>

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="container-fluid flex-grow-1 container-p-y">

            <!-- 頁面標題與操作按鈕 -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h3 class="text-primary">
                <i class="fas fa-user-circle me-2"></i>會員詳情
              </h3>
              <div class="d-flex gap-2">
                <a href="./index.php" class="btn btn-outline-success fw-bold">
                  <i class="fas fa-arrow-left me-1"></i>返回列表
                </a>
                <a href="./update.php?id=<?= $member['id'] ?>" class="btn btn-primary">
                  <i class="fas fa-edit me-1"></i>修改資料
                </a>
              </div>
            </div>

            <div class="row">
              <!-- 左側：基本資料卡片 -->
              <div class="col-md-4">
                <div class="card info-card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                      <i class="fas fa-user-circle me-2"></i>基本資料
                    </h5>
                  </div>
                  <div class="card-body text-center">
                    <!-- 頭像 -->
                    <img src="./img/<?= htmlspecialchars($member['avatar']) ?>" class="rounded profile-avatar mb-3"
                      alt="會員頭像" onerror="this.src='./img/avatar1.jpg'">

                    <!-- 姓名與帳號 -->
                    <div class="justify-content-center mb-3">
                      <div class="text-center">
                        <h4 class="fw-bold"><?= htmlspecialchars($member['name']) ?></h4>
                        <p class="text-muted mb-2">@<?= htmlspecialchars($member['account']) ?></p>
                      </div>
                    </div>

                    <!-- 會員等級 -->
                    <span class="badge <?= getLevelBadgeClass($member['level_name']) ?> fs-6 px-3 py-2 mb-3">
                      <i class="fas fa-crown me-1"></i><?= htmlspecialchars($member['level_name']) ?>
                    </span>

                    <!-- 狀態 -->
                    <div class="mb-3">
                      <?php if ($currentBan): ?>
                        <div class="alert alert-danger status-banned py-2">
                          <i class="fas fa-ban me-1"></i>
                          <strong>已封鎖</strong><br>
                          <small>原因：<?= htmlspecialchars($currentBan['reason_name']) ?></small>
                        </div>
                      <?php else: ?>
                        <div class="alert alert-success status-active py-2">
                          <i class="fas fa-check-circle me-1"></i>
                          <strong>正常使用中</strong>
                        </div>
                      <?php endif; ?>
                    </div>

                    <!-- 快速操作 -->
                    <div class="d-grid gap-2">
                      <?php if ($currentBan): ?>
                        <button class="btn btn-success btn-unban" data-member-id="<?= $member['id'] ?>"
                          data-member-name="<?= htmlspecialchars($member['name']) ?>"
                          data-member-account="<?= htmlspecialchars($member['account']) ?>"
                          data-member-avatar="./img/<?= htmlspecialchars($member['avatar']) ?>" data-bs-toggle="tooltip"
                          title="解除封鎖">
                          <i class="fas fa-unlock me-1"></i>解除封鎖
                        </button>
                      <?php else: ?>
                        <button class="btn btn-outline-warning fw-bold"
                          onclick="showBanModal(<?= $member['id'] ?>, '<?= htmlspecialchars($member['name']) ?>', '<?= htmlspecialchars($member['account']) ?>', './img/<?= htmlspecialchars($member['avatar']) ?>')">
                          <i class="fas fa-ban me-1"></i>封鎖會員
                        </button>
                      <?php endif; ?>
                      <button class="btn btn-outline-danger fw-bold"
                        onclick="showDeleteModal('<?= htmlspecialchars($member['name']) ?>', <?= $member['id'] ?>)">
                        <i class="fas fa-trash me-1"></i>刪除會員
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 右側：詳細資訊 -->
              <div class="col-md-8">
                <!-- 個人資訊 -->
                <div class="card info-card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                      <i class="fas fa-info-circle me-2"></i>個人資訊
                    </h5>
                    <small class="text-muted text-primary">
                      會員編號: #<?= $member['id'] ?>
                    </small>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-envelope me-1"></i>電子信箱
                        </div>
                        <div class="info-value"><?= htmlspecialchars($member['email']) ?></div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-phone me-1"></i>電話號碼
                        </div>
                        <div class="info-value"><?= htmlspecialchars($member['phone']) ?></div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="<?= getGenderIcon($member['gender_name']) ?> me-1"></i>性別
                        </div>
                        <div class="info-value"><?= htmlspecialchars($member['gender_name']) ?></div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-birthday-cake me-1"></i>生日 / 年齡
                        </div>
                        <div class="info-value">
                          <?= date('Y-m-d', strtotime($member['birth_date'])) ?>
                          <small class="text-muted">(<?= calculateAge($member['birth_date']) ?> 歲)</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 帳戶資訊 -->
                <div class="card info-card mb-4">
                  <div class="card-header">
                    <h5 class="mb-0 text-primary">
                      <i class="fas fa-user-cog me-2"></i>帳戶資訊
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-calendar-plus me-1"></i>註冊日期
                        </div>
                        <div class="info-value"><?= date('Y-m-d H:i', strtotime($member['created_at'])) ?></div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-clock me-1"></i>最後更新
                        </div>
                        <div class="info-value"><?= date('Y-m-d H:i', strtotime($member['updated_at'])) ?></div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-calendar me-1"></i>帳號天數
                        </div>
                        <div class="info-value"><?= floor((time() - strtotime($member['created_at'])) / 86400) ?> 天
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="info-label">
                          <i class="fas fa-crown me-1"></i>會員等級
                        </div>
                        <div class="info-value">
                          <span class="badge <?= getLevelBadgeClass($member['level_name']) ?>">
                            <?= htmlspecialchars($member['level_name']) ?>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 封鎖紀錄 -->
                <?php if (!empty($banHistory)): ?>
                  <div class="card info-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0 text-primary">
                        <i class="fas fa-history me-2"></i>封鎖紀錄
                      </h5>
                      <span class="badge bg-label-secondary"><?= count($banHistory) ?> 筆紀錄</span>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-sm align-middle text-center">
                          <thead>
                            <tr>
                              <th class="text-primary fw-bold">封鎖時間</th>
                              <th class="text-primary fw-bold">封鎖原因</th>
                              <th class="text-primary fw-bold">解封時間</th>
                              <th class="text-primary fw-bold">狀態</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($banHistory as $ban): ?>
                              <tr>
                                <td><small><?= date('Y-m-d H:i', strtotime($ban['baded_at'])) ?></small></td>
                                <td><?= htmlspecialchars($ban['reason_name']) ?></td>
                                <td>
                                  <small><?= $ban['unbaded_at'] ? date('Y-m-d H:i', strtotime($ban['unbaded_at'])) : '-' ?></small>
                                </td>
                                <td>
                                  <?php if ($ban['unbaded_at']): ?>
                                    <span class="badge bg-label-success rounded-pill">已解封</span>
                                  <?php else: ?>
                                    <span class="badge bg-label-danger rounded-pill">封鎖中</span>
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
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
                  <img id="memberAvatar" src="./img/<?= htmlspecialchars($member['avatar']) ?>"
                    class="rounded-circle me-3" style="width: 40px; height: 40px;"
                    onerror="this.src='./img/avatar1.jpg'">
                  <div>
                    <div class="fw-bold" id="memberName"><?= htmlspecialchars($member['name']) ?></div>
                    <small class="text-muted" id="memberAccount">@<?= htmlspecialchars($member['account']) ?></small>
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
    <div class="modal-dialog modal-dialog-centered">
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
            <p class="text-muted">會員：<span id="deleteMemberName"
                class="fw-bold"><?= htmlspecialchars($member['name']) ?></span></p>
          </div>
          <input type="hidden" id="deleteMemberId" value="<?= $member['id'] ?>">
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
  <!-- <script src="./js/modal-ban.js"></script> -->
  <script src="./js/modal-unban.js"></script>
  <!-- <script src="./js/modal-delete.js"></script> -->

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // BanModalModule.init();
      UnbanModalModule.init();
      // DeleteModalModule.init();
    });

    // Modal instances
    const banModal = new bootstrap.Modal(document.getElementById('banModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Ban member functionality
    function showBanModal(memberId, memberName, memberAccount, memberAvatar) {
      document.getElementById('banMemberId').value = memberId;
      document.getElementById('memberName').textContent = memberName;
      document.getElementById('memberAccount').textContent = '@' + memberAccount;
      document.getElementById('memberAvatar').src = memberAvatar;
      banModal.show();
    }

    // Delete member functionality
    function showDeleteModal(memberName, memberId) {
      document.getElementById('deleteMemberId').value = memberId;
      document.getElementById('deleteMemberName').textContent = memberName;
      deleteModal.show();
    }

    // Ban form submission
    document.getElementById('banForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const memberId = document.getElementById('banMemberId').value;
      const reason = document.getElementById('banReason').value;

      if (reason) {
        window.location.href = `./doBan.php?id=${memberId}&reason=${reason}`;
      }
    });

    // Delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function () {
      const memberId = document.getElementById('deleteMemberId').value;
      window.location.href = `./doDelete.php?id=${memberId}`;
    });
  </script>
</body>

</html>
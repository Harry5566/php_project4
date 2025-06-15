<?php
// add.php - 新增會員表單
require_once "./connect.php";

// 取得性別選項
$genderSql = "SELECT * FROM `genders` ORDER BY id ASC";
$genderStmt = $pdo->prepare($genderSql);
$genderStmt->execute();
$genders = $genderStmt->fetchAll(PDO::FETCH_ASSOC);

// 取得會員等級選項
$levelSql = "SELECT * FROM `member_level` ORDER BY id ASC";
$levelStmt = $pdo->prepare($levelSql);
$levelStmt->execute();
$levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en" class="layout-wide customizer-hide" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>新增會員 - Xin Chào心橋</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/vnlogo-ic.ico" />

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

    .color-vip {
      background-color: color-mix(in srgb, #fff 84%, #03c3ec) !important;
      color: #03c3ec !important;
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

    .gender-options {
      display: flex;
      gap: 2rem;
      margin-top: 0.5rem;
    }

    .file-preview {
      max-width: 250px;
      max-height: 250px;
      object-fit: cover;
      border: 3px solid #e8491d;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .upload-area {
      border: 2px dashed #d4d4d8;
      border-radius: 0.375rem;
      background-color: rgba(250, 250, 250, 0.8);
      padding: 1.5rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.5s;
    }

    .upload-area:hover {
      border-color: #ae431e;
      background-color: rgba(141, 52, 25, 0.2);
    }

    .logo {
      max-height: 180px;
      width: auto;
      display: block;
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
        <div class="card px-6 py-5">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-4">
              <a href="./login.php" class="app-brand-link gap-2">
                <span>
                  <span><img class="logo" src="../assets/img/favicon/logo-main.png" alt=""></span>
                </span>
                <!-- <span class="fs-4 fw-bold ms-2 demo text-heading">心橋</span> -->
              </a>
            </div>

            <div class="text-center mb-4">
              <h5 class="text-primary">
                <i class="fas fa-user-plus me-2"></i>新增會員
              </h5>
              <p class="text-muted">歡迎加入心橋旅遊大家庭！🌟</p>
            </div>

            <form id="memberForm" class="mb-4" action="./doAdd.php" method="post" enctype="multipart/form-data">

              <!-- 帳號與密碼 -->
              <div class="form-row mb-3">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control placeholder-color" id="account" name="account"
                      placeholder="帳號 *" required autofocus />
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" class="form-control placeholder-color" name="password"
                      placeholder="密碼 * (5-20字元)" minlength="5" maxlength="20" required />
                  </div>
                </div>
              </div>

              <!-- 姓名 -->
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                <input type="text" class="form-control placeholder-color" id="name" name="name" placeholder="姓名 *"
                  required />
              </div>

              <!-- Email 和電話 -->
              <div class="form-row mb-3">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control placeholder-color" id="email" name="email"
                      placeholder="Email *" required />
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control placeholder-color" id="phone" name="phone"
                      placeholder="電話號碼 *" required />
                  </div>
                </div>
              </div>

              <!-- 生日 -->
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                <input type="date" class="form-control" id="birth_date" name="birth_date" required />
              </div>

              <!-- 性別 -->
              <div class="mb-3">
                <label class="form-label fw-bold">性別 *</label>
                <div class="gender-options">
                  <?php foreach ($genders as $gender): ?>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender_id" id="gender<?= $gender['id'] ?>"
                        value="<?= $gender['id'] ?>" required>
                      <label class="form-check-label fw-bold" for="gender<?= $gender['id'] ?>">
                        <?php if ($gender['name'] === '男性'): ?>
                          <i class="fas fa-mars text-primary me-1"></i>
                        <?php else: ?>
                          <i class="fas fa-venus text-danger me-1"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($gender['name']) ?>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- 會員等級 -->
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-crown"></i></span>
                <select class="form-control fw-bold" name="status_id" id="status_id">
                  <?php foreach ($levels as $level): ?>
                    <option value="<?= $level['id'] ?>" <?= $level['id'] == 1 ? 'selected' : '' ?>>
                      <?= htmlspecialchars($level['name']) ?>
                      <?php if ($level['id'] == 1): ?>
                        (預設)
                      <?php endif; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- 大頭貼 -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-camera me-1"></i>大頭貼
                </label>
                <div class="upload-area" onclick="document.getElementById('avatar').click()">
                  <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                  <div class="fw-bold text-muted mb-2">點擊上傳頭像</div>
                  <small class="text-muted">支援 JPG、PNG、GIF 格式，檔案大小不超過 5MB</small>
                </div>
                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">

                <!-- 圖片預覽 -->
                <div class="d-flex justify-content-center mt-3">
                  <img id="imagePreview" class="rounded file-preview" alt="預覽圖片" style="display: none;">
                </div>
              </div>

              <!-- 服務條款 -->
              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required />
                  <label class="form-check-label fw-bold" for="terms-conditions">
                    我同意
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#termsModal"
                      class="text-primary">服務條款與隱私政策</a>
                  </label>
                </div>
              </div>

              <!-- 提交按鈕 -->
              <div class="d-flex gap-3 justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-user-plus me-1"></i>建立會員帳號
                </button>
                <a href="login.php" class="btn btn-outline-success fw-bold">
                  <i class="fas fa-arrow-left me-1"></i>返回
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

  <!-- Terms Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-file-contract me-2"></i>服務條款與隱私政策
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6 class="text-primary">服務條款</h6>
          <p>歡迎使用心橋旅遊會員服務。使用本服務即表示您同意遵守以下條款：</p>
          <ul class="mb-3">
            <li>您必須提供真實、準確的個人資料</li>
            <li>不得使用本服務進行任何違法或不當行為</li>
            <li>我們保留隨時修改服務條款的權利</li>
          </ul>

          <h6 class="text-primary">隱私政策</h6>
          <p>我們重視您的隱私權，承諾保護您的個人資料：</p>
          <ul class="mb-0">
            <li>僅將您的資料用於提供旅遊服務</li>
            <li>不會未經同意將資料提供給第三方</li>
            <li>採用適當的安全措施保護您的資料</li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="acceptTerms()">
            <i class="fas fa-check me-1"></i>我同意
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
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

  <script>
    // 等待 DOM 完全載入後再執行
    document.addEventListener('DOMContentLoaded', function () {

      // 預先初始化 Modal 來提升速度
      const termsModalElement = document.getElementById('termsModal');
      let termsModal = null;
      if (termsModalElement) {
        termsModal = new bootstrap.Modal(termsModalElement, {
          backdrop: true,
          keyboard: true,
          focus: true
        });
      }

      // 圖片上傳預覽功能
      const avatarInput = document.getElementById('avatar');
      const imagePreview = document.getElementById('imagePreview');
      const uploadArea = document.querySelector('.upload-area');

      if (avatarInput && imagePreview && uploadArea) {
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
            if (file.size > 5 * 1024 * 1024) {
              alert('圖片檔案不能超過 5MB');
              this.value = '';
              return;
            }

            // 顯示預覽
            const reader = new FileReader();
            reader.onload = function (e) {
              imagePreview.src = e.target.result;
              imagePreview.style.display = 'block';

              // 更新上傳區域
              uploadArea.innerHTML = `
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <div class="fw-bold text-success mb-2">圖片已選擇</div>
                <small class="text-muted">點擊重新選擇圖片</small>
              `;
            };
            reader.readAsDataURL(file);
          } else {
            imagePreview.style.display = 'none';
            uploadArea.innerHTML = `
              <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
              <div class="fw-bold text-muted mb-2">點擊上傳頭像</div>
              <small class="text-muted">支援 JPG、PNG、GIF 格式，檔案大小不超過 5MB</small>
            `;
          }
        });
      }
    });

    // 服務條款同意功能 - 使用預先初始化的 Modal
    function acceptTerms() {
      document.getElementById('terms-conditions').checked = true;
      const termsModal = bootstrap.Modal.getInstance(document.getElementById('termsModal'));
      if (termsModal) {
        termsModal.hide();
      }
    }
  </script>
</body>

</html>
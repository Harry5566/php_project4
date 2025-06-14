<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>錯誤提示</title>
    <link rel="icon" type="image/x-icon" href="./images/logo.ico" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Iconify 與 Font Awesome -->
    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/custom.css">
    <link rel="stylesheet" href="./css/index.css">

    <!-- JS 設定 -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- ❌ 錯誤提示 Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-xmark-circle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <!-- <h5 class="mb-2">操作失敗！</h5> -->
                    <h5 class="text-muted mb-0" id="errorMessage">發生錯誤，請稍後再試</h5>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">確定</button>
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

    <!-- 其他模組 JS -->
    <script src="./js/modal-ban.js"></script>
    <script src="./js/modal-unban.js"></script>
    <script src="./js/modal-delete.js"></script>
    <script src="./js/modal-success.js"></script>

    <!-- ❌ ErrorModal JS 模組 -->
    <script>
        const ErrorModalModule = (() => {
            const modalElement = document.getElementById('errorModal');
            const messageElement = document.getElementById('errorMessage');
            const confirmButton = modalElement.querySelector('.btn-danger');
            const modal = new bootstrap.Modal(modalElement);

            let onConfirmCallback = null;

            function show(message = "發生錯誤，請稍後再試", onConfirm = null) {
                messageElement.textContent = message;
                onConfirmCallback = onConfirm;
                modal.show();
            }

            confirmButton.addEventListener('click', () => {
                if (typeof onConfirmCallback === 'function') {
                    onConfirmCallback();
                    onConfirmCallback = null;
                }
            });

            return {
                show
            };
        })();
    </script>
</body>

</html>
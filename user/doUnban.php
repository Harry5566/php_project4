<?php
// 解停權會員主要程式
require_once "./connect.php";
require_once "./Utilities.php";

include "./SuccessModal.php";
include "./ErrorModal.php";

date_default_timezone_set("Asia/Taipei");

if (!isset($_GET["id"])) {
    alertGoToFail("請從正常管道進入", "./index.php");
    exit;
}

$memberId = $_GET['id'] ?? 0;

if (!$memberId) {
    alertGoToFail("參數錯誤！", "index.php");
    exit;
}

$sql = "UPDATE member_ban SET unbaded_at = NOW() WHERE member_id = :member_id AND unbaded_at IS NULL";

try {
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([':member_id' => $memberId]);

    if ($result) {
        alertGoTo("會員解封成功！", "index.php");
    } else {
        alertGoToFail("解封失敗，請重試！", "index.php");
    }

} catch (PDOException $e) {
    alertGoToFail("錯誤：" . $e->getMessage(), "index.php");
}
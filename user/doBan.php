<?php
// 停權會員主要程式
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
$reasonId = $_GET['reason'] ?? 0;

if (!$memberId || !$reasonId) {
    alertGoToFail("參數錯誤！", "index.php");
    exit;
}

$checkSql = "SELECT COUNT(*) FROM member_ban WHERE member_id = :member_id AND unbaded_at IS NULL";


try {
    // 檢查是否已被封鎖
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':member_id' => $memberId]);

    if ($checkStmt->fetchColumn() > 0) {
        alertGoToFail("該會員已被封鎖！", "index.php");
        exit;
    }

    // 新增封鎖記錄
    $sql = "INSERT INTO member_ban (member_id, reason_id, baded_at) VALUES (:member_id, :reason_id, NOW())";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':member_id' => $memberId,
        ':reason_id' => $reasonId
    ]);

    if ($result) {
        alertGoTo("會員封鎖成功！", "index.php");
    } else {
        alertGoToFail("封鎖失敗，請重試！", "index.php");
    }

} catch (PDOException $e) {
    alertGoToFail("錯誤：" . $e->getMessage(), "index.php");
}
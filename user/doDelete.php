<?php
// 刪除會員主要程式
require_once "./connect.php";
require_once "./Utilities.php";

include "./SuccessModal.php";
include "./ErrorModal.php";


if (!isset($_GET["id"])) {
    alertGoToFail("請從正常管道進入", "./index.php");
    exit;
}

$id = intval($_GET["id"]);

// 檢查會員是否存在且有效
$checkSql = "SELECT COUNT(*) FROM `members` WHERE `id` = ? AND `is_valid` = 1";
// 執行軟刪除（將 is_valid 設為 0）
$sql = "UPDATE `members` SET `is_valid` = 0 WHERE `id` = ?";


try {
    $checkStmt  = $pdo->prepare($checkSql);
    $checkStmt ->execute([$id]);
    $exists = $checkStmt->fetchColumn();

    if ($exists == 0) {
        alertGoToFail("會員不存在或已被刪除", "./index.php");
        exit;
    }

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);

    if ($result && $stmt->rowCount() > 0) {
        alertGoTo("刪除會員成功", "./index.php");
    } 
    else {
        alertGoToFail("刪除失敗，請重試", "./index.php");
    }

} catch (PDOException $e) {
    echo "錯誤: {{$e->getMessage()}}";
    exit;
}

<?php
session_start();
require_once "./connect.php";
require_once "./Utilities.php";

include "./SuccessModal.php";
include "./ErrorModal.php";

if (!isset($_POST["account"])) {
    alertGoToFail("請從正常管道進入", "./login.php");
    exit;
}

$account = htmlspecialchars($_POST["account"]);
$password = $_POST["password"];


if ($account == "") {
    alertAndBack("請輸入有效的帳號");
    exit;
};

if ($password == "") {
    alertAndBack("請輸入密碼");
    exit;
};

$sql = "SELECT * FROM `members` WHERE `account` = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$account]);
    // 判斷有沒有抓到內容 再進行判斷
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "錯誤: {{$e->getMessage()}}";
    exit;
}

if(!$row) {
    alertGoToFail("登入失敗", "./index.php");
}
else {
    if(password_verify($password, $row["password"])) {
        $_SESSION["members"] = [
            "id" => $row["id"],
            "name" => $row["name"],
            "email"=> $row["email"],
            "avatar" => $row["avatar"]
        ];
        alertGoTo("登入成功", "./index.php");
    }
    else {
        alertGoToFail("登入失敗", "./index.php");
    }
}
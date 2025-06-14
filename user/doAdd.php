<?php
// 新增會員主要程式
require_once "./connect.php";
require_once "./Utilities.php";

include "./SuccessModal.php";
include "./ErrorModal.php";


if (!isset($_POST["account"])) {
    alertGoToFail("請從正常管道進入", "./index.php");
    exit;
}

// 取得表單資料
$account = htmlspecialchars($_POST["account"]);
$password = $_POST["password"];
$name = htmlspecialchars($_POST["name"]);
$email = htmlspecialchars($_POST["email"]);
$phone = htmlspecialchars($_POST["phone"]);
$birth_date = $_POST["birth_date"];
$gender_id = $_POST["gender_id"];
$status_id = $_POST["status_id"] ?? 1; // 預設銅會員

// 表單驗證
if (empty($account)) {
    alertAndBack("請輸入帳號");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    alertAndBack("請輸入有效的信箱");
    exit;
}

if ($password == "") {
    alertAndBack("請輸入密碼");
    exit;
}

$passwrodLength = strlen($password);
if ($passwrodLength < 5 || $passwrodLength > 20) {
    alertAndBack("請輸入密碼");
    exit;
}

if (empty($name)) {
    alertAndBack("請輸入姓名");
    exit;
}

if (empty($phone)) {
    alertAndBack("請輸入電話");
    exit;
}

if (empty($birth_date)) {
    alertAndBack("請選擇生日");
    exit;
}

if (empty($gender_id)) {
    alertAndBack("請選擇性別");
    exit;
}


$hashedPassword = password_hash($password, PASSWORD_ARGON2I);

// 新增會員資料
$sql = "INSERT INTO `members` (`account`, `password`, `name`, `phone`, `gender_id`, `birth_date`, `email`, `status_id`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$values = [$account, $hashedPassword, $name, $phone, $gender_id, $birth_date, $email, $status_id];

// 處理大頭貼上傳
if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] == 0) {
    $avatar = 'avatar1.jpg'; // 預設頭像
    $timestamp = time();
    $ext = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
    $newFileName = "{$timestamp}.{$ext}";
    $file = "./img/{$newFileName}";
    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $file)) {
        $avatar = $newFileName;
    }
    // 新增會員資料
    $sql = "INSERT INTO `members` (`account`, `password`, `name`, `phone`, `gender_id`, `birth_date`, `email`, `status_id`, `avatar`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $values = [$account, $hashedPassword, $name, $phone, $gender_id, $birth_date, $email, $status_id, $avatar];
}

// 檢查帳號和信箱是否已存在
$checkSql = "SELECT COUNT(*) AS `count` FROM `members` WHERE `account` = ? OR `email` = ?";

try {
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$account, $email]);

    $count = $checkStmt->fetchColumn();
    if ($count > 0) {
        alertAndBack("此帳號已經使用過");
        exit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
} catch (PDOException $e) {
    echo "錯誤: {{$e->getMessage()}}";
    exit;
}

alertGoTo("新增資料成功", "./index.php");

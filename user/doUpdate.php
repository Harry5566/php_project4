<?php
// 修改會員主要程式
require_once "./connect.php";
require_once "./Utilities.php";

date_default_timezone_set("Asia/Taipei");

if (!isset($_POST["id"])) {
  alertGoTo("請從正常管道進入", "./index.php");
  exit;
}

$id = intval($_POST["id"]);
$name = htmlspecialchars($_POST["name"]);
$phone = htmlspecialchars($_POST["phone"]);
$birth_date = $_POST["birth_date"];
$gender_id = $_POST["gender_id"];
$status_id = $_POST["status_id"];
$password = $_POST["password"];

// 如果有輸入密碼，檢查長度
if (!empty($password)) {
  $passwordLength = strlen($password);
  if ($passwordLength < 5 || $passwordLength > 20) {
    alertAndBack("密碼長度必須在 5-20 字元之間");
    exit;
  }
}

$set = [];
$values = [":id" => intval($id)];

if ($name !== "") {
  $set[] = "`name` = :name";
  $values[":name"] = $name;
}

if ($phone !== "") {
  $set[] = "`phone` = :phone";
  $values[":phone"] = $phone;
}

if ($gender_id !== "") {
  $set[] = "`gender_id` = :gender_id";
  $values[":gender_id"] = $gender_id;
}

if ($status_id !== "") {
  $set[] = "`status_id` = :status_id";
  $values[":status_id"] = $status_id;
}

if ($birth_date !== "") {
  $set[] = "`birth_date` = :birth_date";
  $values[":birth_date"] = $birth_date;
}


if ($password !== "") {
  $password = password_hash($password, PASSWORD_BCRYPT);
  $set[] = "`password` = :password";
  $values[":password"] = $password;
}


if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === 0) {
  $img = null;
  $timestamp = time();
  $ext = pathinfo($_FILES["avatar"]['name'], PATHINFO_EXTENSION);
  $newFileName = "{$timestamp}.{$ext}";
  if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "./img/{$newFileName}")) {
    $img = $newFileName;
  }
  $set[] = "`avatar` = :avatar";
  $values[":avatar"] = $img;
}

// 檢查是否有要更新的欄位
if (count($set) == 0) {
  alertAndBack("沒有修改任何欄位");
  exit;
}

$set[] = "`updated_at` = :updated_at";
$values[":updated_at"] = date("Y-m-d H:i:s");

$sql = "UPDATE `members` SET " . implode(", ", $set) . " WHERE `id` = :id";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);

} catch (PDOException $e) {
  echo "錯誤: {{$e->getMessage()}}";
  goBack();
  exit;
}

if ($stmt->rowCount() > 0) {
  alertGoTo("更新會員資料成功", "./view.php?id={$id}");
} else {
  alertGoTo("沒有資料被更新", "./update.php?id={$id}");
}
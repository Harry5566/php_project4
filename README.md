會員管理系統使用說明
📋 專案簡介
Xin Chào心橋會員管理系統是一個基於PHP開發的Web應用程式，提供完整的會員管理功能，包括會員註冊、登入、資料管理、封鎖/解封、等級管理等功能。
🔧 系統需求

Web服務器: Apache/Nginx
PHP版本: 7.4 或以上
資料庫: MySQL 5.7 或以上
瀏覽器: 支援現代瀏覽器（Chrome, Firefox, Safari, Edge）

📁 檔案結構
member-management/
├── connect.php           # 資料庫連線設定
├── login.php            # 登入頁面
├── doLogin.php          # 登入處理
├── doLogout.php         # 登出處理
├── index.php            # 會員列表主頁
├── add.php              # 新增會員頁面
├── doAdd.php            # 新增會員處理
├── view.php             # 會員詳情頁面
├── update.php           # 修改會員頁面
├── doUpdate.php         # 修改會員處理
├── doDelete.php         # 刪除會員處理
├── doBan.php            # 封鎖會員處理
├── doUnban.php          # 解除封鎖處理
├── Utilities.php        # 工具函數庫
├── u.sql                # 資料庫結構文件
├── PASSWORD-ARGON2I.php # 密碼加密轉換
├── css/                 # 樣式檔案
├── js/                  # JavaScript檔案
├── img/                 # 圖片資源
└── images/              # 系統圖片
🗄️ 資料庫設定
1. 建立資料庫
sqlCREATE DATABASE my_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_db;
2. 執行資料庫結構
執行 u.sql 檔案中的 SQL 語句來建立所需的資料表：
bashmysql -u root -p my_db < u.sql
3. 資料表結構
系統包含以下主要資料表：

members: 會員主表
genders: 性別選項
member_level: 會員等級（銅、銀、金、鑽）
ban_reasons: 封鎖原因
member_ban: 封鎖記錄表

4. 修改資料庫連線設定
編輯 connect.php 檔案：
php$servername = "localhost";
$username = "root";           // 修改為您的資料庫用戶名
$password = "";               // 修改為您的資料庫密碼
$dbname = "my_db";            // 修改為您的資料庫名稱
$port = 3306;
🚀 安裝步驟
1. 部署檔案
將所有檔案上傳到您的Web服務器根目錄或子目錄。
2. 設定權限
確保以下目錄具有寫入權限：
bashchmod 755 img/
chmod 755 images/
3. 測試連線
訪問 connect.php 確認資料庫連線正常。
4. 初始登入
使用資料庫中的測試帳號登入，或創建新的管理員帳號。
🎯 主要功能
🔐 登入系統

檔案: login.php, doLogin.php
功能: 管理員登入驗證
特色:

支援帳號/密碼登入
Session管理
自動跳轉功能



👥 會員管理

檔案: index.php
功能:

會員列表顯示
搜尋功能（帳號、姓名、Email）
分頁顯示
排序功能
篩選功能（狀態、等級、註冊日期）



➕ 新增會員

檔案: add.php, doAdd.php
功能:

表單驗證
大頭貼上傳
密碼加密（ARGON2I）
重複帳號檢查



👀 查看詳情

檔案: view.php
功能:

完整會員資訊展示
封鎖記錄查看
快速操作按鈕



✏️ 修改資料

檔案: update.php, doUpdate.php
功能:

會員資料修改
圖片預覽功能
選擇性密碼更新



🚫 封鎖/解封

檔案: doBan.php, doUnban.php
功能:

會員封鎖管理
封鎖原因記錄
封鎖歷史追蹤



🗑️ 刪除會員

檔案: doDelete.php
功能: 軟刪除（設定 is_valid = 0）

💻 使用方法
1. 管理員登入

訪問 login.php
輸入管理員帳號密碼
登入成功後跳轉到會員列表頁面

2. 瀏覽會員列表

在 index.php 查看所有會員
使用搜尋功能尋找特定會員
使用篩選器按狀態、等級或日期篩選

3. 新增會員

點擊「新增會員」按鈕
填寫必要資訊（標*為必填）
上傳大頭貼（可選）
同意服務條款
提交表單

4. 管理會員

查看詳情: 點擊眼睛圖示
修改資料: 點擊編輯圖示
封鎖會員: 點擊封鎖圖示並選擇原因
解除封鎖: 點擊解鎖圖示
刪除會員: 點擊刪除圖示並確認

🎨 界面特色
響應式設計

支援桌面、平板、手機顯示
Bootstrap框架確保兼容性

現代化UI

毛玻璃效果
動態背景
漸層按鈕
圖示豐富

用戶體驗

即時圖片預覽
表單驗證
載入動畫
確認對話框

🔒 安全性設計
密碼安全

使用 ARGON2I 加密算法
支援密碼長度限制（5-20字元）

輸入驗證

前端表單驗證
後端數據清理（htmlspecialchars）
SQL注入防護（PDO預處理）

會話管理

Session-based身份驗證
自動登出功能
路由保護

🛠️ 工具函數說明
Utilities.php 包含的函數：
phpalertAndBack($msg)        // 顯示訊息並返回上頁
alertGoTo($msg, $url)     // 顯示訊息並跳轉
goBack()                  // 返回上一頁
🎯 會員等級系統
等級名稱顏色標識說明1銅藍色基礎會員2銀灰色進階會員3金黃色黃金會員4鑽特殊色VIP會員
📝 封鎖原因選項

違規發言
濫用功能
惡意洗版
詐騙行為
散布不實訊息

⚠️ 注意事項
檔案上傳

支援格式：JPG、PNG、GIF
檔案大小限制：5MB
上傳目錄：img/

資料備份

定期備份資料庫
重要檔案版本控制

效能優化

圖片壓縮處理
資料庫索引優化
分頁限制：25筆/頁

瀏覽器兼容

建議使用Chrome 90+
需要JavaScript支援
Cookie必須啟用

🐛 常見問題
Q: 無法登入系統
A: 檢查資料庫連線設定和帳號密碼是否正確
Q: 圖片上傳失敗
A: 檢查img目錄權限和檔案大小限制
Q: 分頁顯示異常
A: 檢查資料庫連線和SQL查詢語句
Q: 封鎖功能無效
A: 確認ban_reasons表有正確的資料
📞 技術支援
如有問題請聯繫開發團隊：

項目：前端67-第四組
系統名稱：Xin Chào心橋會員管理系統

📋 更新日誌
v1.0.0 (2025-06-14)

初始版本發布
完整的會員CRUD功能
封鎖/解封系統
響應式設計界面
安全性優化


© 2025 心橋會員管理系統 - 版權所有

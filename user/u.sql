USE `my_db`;

-- MySQL 資料表建立語句
CREATE TABLE genders (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '性別編號',
    name VARCHAR(20) NOT NULL UNIQUE COMMENT '性別名稱（male / female / other）'
);
DROP TABLE `genders`;

CREATE TABLE member_level (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '會員等級編號',
    name VARCHAR(20) NOT NULL UNIQUE COMMENT '會員等級（銅 / 銀 / 金 / 鑽）'
);
DROP TABLE `member_level`;


CREATE TABLE ban_reasons (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '封鎖原因 ID',
    name VARCHAR(100) NOT NULL UNIQUE COMMENT '封鎖原因說明（如：違規發言 / 濫用功能）'
);
DROP TABLE `ban_reasons`;


CREATE TABLE members (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '會員編號（主鍵，自動編號）',
    account VARCHAR(50) NOT NULL UNIQUE COMMENT '帳號（唯一）',
    password VARCHAR(255) NOT NULL COMMENT '加密後的登入密碼',
    name VARCHAR(50) NOT NULL COMMENT '姓名',
    avatar VARCHAR(255) DEFAULT 'avatar1.jpg' COMMENT '大頭貼（未設定則使用預設路徑）',
    phone VARCHAR(20) NOT NULL COMMENT '電話號碼',
    gender_id INT NOT NULL COMMENT '性別 ID（參照 genders 表）',
    birth_date DATE NOT NULL COMMENT '出生日期（YYYY-MM-DD）',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT '聯絡 Email',
    status_id INT NOT NULL COMMENT '會員等級（銅 / 銀 / 金 / 鑽）',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '資料建立時間',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '資料最後更新時間',
    is_valid TINYINT NOT NULL DEFAULT '1' COMMENT '是否被軟刪除',
    FOREIGN KEY (gender_id) REFERENCES genders(id),
    FOREIGN KEY (status_id) REFERENCES member_level(id)
);

DROP TABLE `members`;


CREATE TABLE member_ban (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '封鎖紀錄編號（主鍵，自動編號）',
    member_id INT NOT NULL COMMENT '被封鎖的會員 ID（參照 members 表）',
    reason_id INT NOT NULL COMMENT '封鎖原因 ID（參照 ban_reasons 表）',
    baded_at DATETIME COMMENT '封鎖開始時間',
    unbaded_at DATETIME COMMENT '解封時間（null 表示尚未解封）',
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (reason_id) REFERENCES ban_reasons(id)
);

DROP TABLE `member_ban`;

-- 性別表資料
INSERT INTO genders (`name`) VALUES
('男性'),
('女性');


-- 會員等級表資料
INSERT INTO member_level (`name`) VALUES
('銅'),
('銀'),
('金'),
('鑽');

-- 封鎖原因表資料
INSERT INTO ban_reasons (`name`) VALUES
('違規發言'),
('濫用功能'),
('惡意洗版'),
('詐騙行為'),
('散布不實訊息');

-- 100筆會員假資料
INSERT INTO members (account, password, name, phone, gender_id, birth_date, email, status_id, created_at, updated_at) VALUES
('xiaoming_chen', 'password123', '陳小明', '0912345678', 1, '1990-03-15', 'xiaoming.chen@example.com', 1, '2024-01-15 10:30:00', '2024-01-15 10:30:00'),
('meihua_lin', 'abc123456', '林美花', '0223456789', 2, '1985-07-22', 'meihua.lin@example.com', 2, '2024-01-16 14:20:00', '2024-01-16 14:20:00'),
('datong_wang', 'mypassword', '王大同', '0934567890', 1, '1992-11-08', 'datong.wang@example.com', 3, '2024-01-17 09:45:00', '2024-01-17 09:45:00'),
('yating_zhang', 'zhang2024', '張雅婷', '0945678901', 2, '1988-05-12', 'yating.zhang@example.com', 1, '2024-01-18 16:10:00', '2024-01-18 16:10:00'),
('zhiqiang_li', 'li888999', '李志強', '0956789012', 1, '1995-02-28', 'zhiqiang.li@example.com', 4, '2024-01-19 11:25:00', '2024-01-19 11:25:00'),
('jiahui_liu', 'hello123', '劉佳慧', '0367890123', 2, '1993-09-17', 'jiahui.liu@example.com', 2, '2024-01-20 13:40:00', '2024-01-20 13:40:00'),
('jianhua_huang', 'huang456', '黃建華', '0978901234', 1, '1987-12-03', 'jianhua.huang@example.com', 1, '2024-01-21 08:15:00', '2024-01-21 08:15:00'),
('shufen_wu', 'wu789012', '吳淑芬', '0989012345', 2, '1991-06-25', 'shufen.wu@example.com', 3, '2024-01-22 15:50:00', '2024-01-22 15:50:00'),
('minghong_cai', 'cai2024', '蔡明宏', '0990123456', 1, '1989-04-14', 'minghong.cai@example.com', 2, '2024-01-23 12:30:00', '2024-01-23 12:30:00'),
('yawen_zheng', 'zheng123', '鄭雅雯', '0422234567', 2, '1994-10-31', 'yawen.zheng@example.com', 1, '2024-01-24 17:20:00', '2024-01-24 17:20:00'),
('wenjie_xu', 'xupass456', '許文傑', '0923345678', 1, '1986-08-07', 'wenjie.xu@example.com', 4, '2024-01-25 10:45:00', '2024-01-25 10:45:00'),
('lihua_yang', 'yang789', '楊麗華', '0934456789', 2, '1990-01-19', 'lihua.yang@example.com', 2, '2024-01-26 14:35:00', '2024-01-26 14:35:00'),
('junhui_he', 'hepassword', '何俊輝', '0945567890', 1, '1992-03-26', 'junhui.he@example.com', 1, '2024-01-27 09:20:00', '2024-01-27 09:20:00'),
('meiling_xie', 'xie2024', '謝美玲', '0756678901', 2, '1988-11-13', 'meiling.xie@example.com', 3, '2024-01-28 16:55:00', '2024-01-28 16:55:00'),
('zhiming_gao', 'gao123456', '高志明', '0967789012', 1, '1995-07-04', 'zhiming.gao@example.com', 2, '2024-01-29 11:40:00', '2024-01-29 11:40:00'),
('yaru_pan', 'panpan123', '潘雅茹', '0978890123', 2, '1993-05-21', 'yaru.pan@example.com', 1, '2024-01-30 13:25:00', '2024-01-30 13:25:00'),
('jianguo_shen', 'shen789', '沈建國', '0989901234', 1, '1987-09-18', 'jianguo.shen@example.com', 4, '2024-01-31 08:50:00', '2024-01-31 08:50:00'),
('shuhui_shi', 'shi2024', '施淑惠', '0890012345', 2, '1991-12-09', 'shuhui.shi@example.com', 2, '2024-02-01 15:15:00', '2024-02-01 15:15:00'),
('zhihao_fan', 'fanfan456', '范志豪', '0912123456', 1, '1989-02-16', 'zhihao.fan@example.com', 1, '2024-02-02 12:05:00', '2024-02-02 12:05:00'),
('meihui_yao', 'yao123789', '姚美惠', '0923234567', 2, '1994-08-23', 'meihui.yao@example.com', 3, '2024-02-03 17:30:00', '2024-02-03 17:30:00'),
('wenhao_zhao', 'zhao2024', '趙文豪', '0934345678', 1, '1986-04-11', 'wenhao.zhao@example.com', 2, '2024-02-04 10:20:00', '2024-02-04 10:20:00'),
('yafang_qian', 'qian456789', '錢雅芳', '0245456789', 2, '1990-10-28', 'yafang.qian@example.com', 1, '2024-02-05 14:45:00', '2024-02-05 14:45:00'),
('jiancheng_sun', 'sun123456', '孫建成', '0956567890', 1, '1992-06-15', 'jiancheng.sun@example.com', 4, '2024-02-06 09:35:00', '2024-02-06 09:35:00'),
('yajun_li', 'liyajun789', '李雅君', '0967678901', 2, '1988-01-05', 'yajun.li@example.com', 2, '2024-02-07 16:10:00', '2024-02-07 16:10:00'),
('junhong_zhou', 'zhou2024', '周俊宏', '0978789012', 1, '1995-03-22', 'junhong.zhou@example.com', 1, '2024-02-08 11:55:00', '2024-02-08 11:55:00'),
('jiaying_wu', 'wujiaying', '吳佳穎', '0389890123', 2, '1993-11-07', 'jiaying.wu@example.com', 3, '2024-02-09 13:40:00', '2024-02-09 13:40:00'),
('zhiwei_jiang', 'jiang123', '蔣志偉', '0990901234', 1, '1987-07-14', 'zhiwei.jiang@example.com', 2, '2024-02-10 08:25:00', '2024-02-10 08:25:00'),
('meiling_han', 'han456789', '韓美玲', '0912012345', 2, '1991-05-01', 'meiling.han@example.com', 1, '2024-02-11 15:20:00', '2024-02-11 15:20:00'),
('jianhua_feng', 'feng2024', '馮建華', '0923123456', 1, '1989-09-12', 'jianhua.feng@example.com', 4, '2024-02-12 12:15:00', '2024-02-12 12:15:00'),
('shufen_wei', 'wei123456', '衛淑芬', '0434234567', 2, '1994-12-29', 'shufen.wei@example.com', 2, '2024-02-13 17:05:00', '2024-02-13 17:05:00'),
('zhiming_you', 'you789012', '尤志明', '0945345678', 1, '1986-02-06', 'zhiming.you@example.com', 1, '2024-02-14 10:50:00', '2024-02-14 10:50:00'),
('yahui_zhu', 'zhu2024', '朱雅慧', '0956456789', 2, '1990-08-17', 'yahui.zhu@example.com', 3, '2024-02-15 14:35:00', '2024-02-15 14:35:00'),
('wenjie_niu', 'niu123456', '牛文傑', '0967567890', 1, '1992-04-24', 'wenjie.niu@example.com', 2, '2024-02-16 09:25:00', '2024-02-16 09:25:00'),
('meihua_mao', 'mao789', '毛美華', '0578678901', 2, '1988-12-11', 'meihua.mao@example.com', 1, '2024-02-17 16:45:00', '2024-02-17 16:45:00'),
('zhihao_shen', 'shenzhihao', '申志豪', '0989789012', 1, '1995-06-03', 'zhihao.shen@example.com', 4, '2024-02-18 11:30:00', '2024-02-18 11:30:00'),
('yamin_tang', 'tang123', '唐雅敏', '0990890123', 2, '1987-01-20', 'yamin.tang@example.com', 2, '2024-02-19 14:20:00', '2024-02-19 14:20:00'),
('jiancheng_feng', 'fengpass', '馮建成', '0912901234', 1, '1993-07-11', 'jiancheng.feng@example.com', 1, '2024-02-20 09:15:00', '2024-02-20 09:15:00'),
('shujuan_ding', 'ding456', '丁淑娟', '0823012345', 2, '1991-03-28', 'shujuan.ding@example.com', 3, '2024-02-21 16:40:00', '2024-02-21 16:40:00'),
('weiming_ye', 'ye789012', '葉偉明', '0934123456', 1, '1989-09-05', 'weiming.ye@example.com', 2, '2024-02-22 12:25:00', '2024-02-22 12:25:00'),
('liyun_su', 'su2024', '蘇麗雲', '0945234567', 2, '1994-11-17', 'liyun.su@example.com', 1, '2024-02-23 17:50:00', '2024-02-23 17:50:00'),
('zhenhua_lu', 'lu123456', '陸振華', '0956345678', 1, '1986-05-14', 'zhenhua.lu@example.com', 4, '2024-02-24 10:35:00', '2024-02-24 10:35:00'),
('meifeng_kong', 'kong789', '孔美鳳', '0567456789', 2, '1990-02-01', 'meifeng.kong@example.com', 2, '2024-02-25 14:55:00', '2024-02-25 14:55:00'),
('guoqiang_bai', 'bai2024', '白國強', '0978567890', 1, '1992-08-18', 'guoqiang.bai@example.com', 1, '2024-02-26 09:40:00', '2024-02-26 09:40:00'),
('xiulan_cui', 'cui123789', '崔秀蘭', '0989678901', 2, '1988-04-25', 'xiulan.cui@example.com', 3, '2024-02-27 16:25:00', '2024-02-27 16:25:00'),
('mingjie_kang', 'kang456', '康明傑', '0990789012', 1, '1995-12-12', 'mingjie.kang@example.com', 2, '2024-02-28 11:45:00', '2024-02-28 11:45:00'),
('yawen_liang', 'liang789', '梁雅雯', '0712890123', 2, '1993-06-29', 'yawen.liang@example.com', 1, '2024-03-01 13:30:00', '2024-03-01 13:30:00'),
('haiming_jin', 'jin2024', '金海明', '0923901234', 1, '1987-10-16', 'haiming.jin@example.com', 4, '2024-03-02 08:20:00', '2024-03-02 08:20:00'),
('suzhen_xia', 'xia123456', '夏素珍', '0934012345', 2, '1991-01-03', 'suzhen.xia@example.com', 2, '2024-03-03 15:10:00', '2024-03-03 15:10:00'),
('chenghao_tan', 'tan789012', '譚承豪', '0945123456', 1, '1989-07-20', 'chenghao.tan@example.com', 1, '2024-03-04 12:50:00', '2024-03-04 12:50:00'),
('huilan_qin', 'qin2024', '秦慧蘭', '0856234567', 2, '1994-03-07', 'huilan.qin@example.com', 3, '2024-03-05 17:35:00', '2024-03-05 17:35:00'),
('weijie_duan', 'duan123', '段偉傑', '0967345678', 1, '1986-11-24', 'weijie.duan@example.com', 2, '2024-03-06 10:25:00', '2024-03-06 10:25:00'),
('fangfang_yue', 'yue456789', '岳芳芳', '0978456789', 2, '1990-07-31', 'fangfang.yue@example.com', 1, '2024-03-07 14:40:00', '2024-03-07 14:40:00'),
('junde_ren', 'ren2024', '任俊德', '0989567890', 1, '1992-04-08', 'junde.ren@example.com', 4, '2024-03-08 09:55:00', '2024-03-08 09:55:00'),
('lijuan_zou', 'zou123456', '鄒麗娟', '0290678901', 2, '1988-12-15', 'lijuan.zou@example.com', 2, '2024-03-09 16:15:00', '2024-03-09 16:15:00'),
('guowei_yu', 'yu789', '余國偉', '0912789012', 1, '1995-08-02', 'guowei.yu@example.com', 1, '2024-03-10 11:05:00', '2024-03-10 11:05:00'),
('xiaoli_pan', 'panxiaoli', '潘小莉', '0923890123', 2, '1993-05-19', 'xiaoli.pan@example.com', 3, '2024-03-11 13:50:00', '2024-03-11 13:50:00'),
('tianming_du', 'du2024', '杜天明', '0934901234', 1, '1987-02-26', 'tianming.du@example.com', 2, '2024-03-12 08:35:00', '2024-03-12 08:35:00'),
('huifang_yin', 'yin123789', '尹慧芳', '0645012345', 2, '1991-09-13', 'huifang.yin@example.com', 1, '2024-03-13 15:25:00', '2024-03-13 15:25:00'),
('zhenguo_peng', 'peng456', '彭振國', '0956123456', 1, '1989-05-30', 'zhenguo.peng@example.com', 4, '2024-03-14 12:40:00', '2024-03-14 12:40:00'),
('meizhu_lu', 'lumeizhu', '盧美珠', '0967234567', 2, '1994-01-07', 'meizhu.lu@example.com', 2, '2024-03-15 17:20:00', '2024-03-15 17:20:00'),
('jiahao_fu', 'fu789012', '傅家豪', '0978345678', 1, '1986-09-24', 'jiahao.fu@example.com', 1, '2024-03-16 10:10:00', '2024-03-16 10:10:00'),
('yanping_bu', 'bu2024', '卜燕萍', '0389456789', 2, '1990-06-11', 'yanping.bu@example.com', 3, '2024-03-17 14:30:00', '2024-03-17 14:30:00'),
('hongwei_mu', 'mu123456', '穆宏偉', '0990567890', 1, '1992-02-28', 'hongwei.mu@example.com', 2, '2024-03-18 09:45:00', '2024-03-18 09:45:00'),
('xiuying_shui', 'shui789', '水秀英', '0912678901', 2, '1988-10-05', 'xiuying.shui@example.com', 1, '2024-03-19 16:55:00', '2024-03-19 16:55:00'),
('deming_chang', 'chang2024', '常德明', '0923789012', 1, '1995-04-22', 'deming.chang@example.com', 4, '2024-03-20 11:20:00', '2024-03-20 11:20:00'),
('ruihua_yan', 'yan456789', '顏瑞華', '0234890123', 2, '1993-12-09', 'ruihua.yan@example.com', 2, '2024-03-21 13:35:00', '2024-03-21 13:35:00'),
('zhigang_meng', 'meng123', '孟志剛', '0945901234', 1, '1987-08-16', 'zhigang.meng@example.com', 1, '2024-03-22 08:50:00', '2024-03-22 08:50:00'),
('wanqing_qi', 'qi789012', '齊婉晴', '0956012345', 2, '1991-04-03', 'wanqing.qi@example.com', 3, '2024-03-23 15:40:00', '2024-03-23 15:40:00'),
('xiangdong_guan', 'guan2024', '關向東', '0967123456', 1, '1989-11-20', 'xiangdong.guan@example.com', 2, '2024-03-24 12:15:00', '2024-03-24 12:15:00'),
('jingmei_jiao', 'jiao123456', '焦靜美', '0778234567', 2, '1994-07-07', 'jingmei.jiao@example.com', 1, '2024-03-25 17:00:00', '2024-03-25 17:00:00'),
('guoliang_luo', 'luo789', '羅國良', '0989345678', 1, '1986-03-14', 'guoliang.luo@example.com', 4, '2024-03-26 10:45:00', '2024-03-26 10:45:00'),
('yumei_nie', 'nie2024', '聶玉梅', '0990456789', 2, '1990-09-01', 'yumei.nie@example.com', 2, '2024-03-27 14:25:00', '2024-03-27 14:25:00'),
('xiaoyang_qiao', 'qiao456', '喬曉陽', '0912567890', 1, '1992-05-18', 'xiaoyang.qiao@example.com', 1, '2024-03-28 09:10:00', '2024-03-28 09:10:00'),
('chunhua_gong', 'gong123789', '龔春花', '0823678901', 2, '1988-01-25', 'chunhua.gong@example.com', 3, '2024-03-29 16:30:00', '2024-03-29 16:30:00'),
('yingcai_cheng', 'cheng2024', '程英才', '0934789012', 1, '1995-10-12', 'yingcai.cheng@example.com', 2, '2024-03-30 11:55:00', '2024-03-30 11:55:00'),
('lina_hou', 'hou789012', '侯麗娜', '0945890123', 2, '1993-06-29', 'lina.hou@example.com', 1, '2024-03-31 13:20:00', '2024-03-31 13:20:00'),
('dawei_kuang', 'kuang123', '鄺大偉', '0956901234', 1, '1987-02-16', 'dawei.kuang@example.com', 4, '2024-04-01 08:40:00', '2024-04-01 08:40:00'),
('fenglan_mo', 'mo456789', '莫鳳蘭', '0567012345', 2, '1991-08-03', 'fenglan.mo@example.com', 2, '2024-04-02 15:15:00', '2024-04-02 15:15:00'),
('junming_lang', 'lang2024', '郎俊明', '0978123456', 1, '1989-04-20', 'junming.lang@example.com', 1, '2024-04-03 12:05:00', '2024-04-03 12:05:00'),
('xiufen_ning', 'ning123456', '甯秀芬', '0989234567', 2, '1994-12-07', 'xiufen.ning@example.com', 3, '2024-04-04 17:45:00', '2024-04-04 17:45:00'),
('zhenliang_chai', 'chai789', '柴振良', '0990345678', 1, '1986-07-24', 'zhenliang.chai@example.com', 2, '2024-04-05 10:30:00', '2024-04-05 10:30:00'),
('meirong_tong', 'tong2024', '童美蓉', '0712456789', 2, '1990-03-11', 'meirong.tong@example.com', 1, '2024-04-06 14:50:00', '2024-04-06 14:50:00'),
('haotian_bian', 'bian456', '卞昊天', '0923567890', 1, '1992-09-28', 'haotian.bian@example.com', 4, '2024-04-07 09:25:00', '2024-04-07 09:25:00'),
('yingjie_xi', 'xi123789', '席英傑', '0934678901', 2, '1988-05-15', 'yingjie.xi@example.com', 2, '2024-04-08 16:35:00', '2024-04-08 16:35:00'),
('wanli_ju', 'ju2024', '鞠萬里', '0945789012', 1, '1995-01-02', 'wanli.ju@example.com', 1, '2024-04-09 11:40:00', '2024-04-09 11:40:00'),
('chunmei_ge', 'ge789012', '葛春梅', '0856890123', 2, '1993-07-19', 'chunmei.ge@example.com', 3, '2024-04-10 13:55:00', '2024-04-10 13:55:00'),
('guangming_wu', 'wuguang123', '巫光明', '0967901234', 1, '1987-11-06', 'guangming.wu@example.com', 2, '2024-04-11 08:10:00', '2024-04-11 08:10:00'),
('xiaoqin_gan', 'gan456789', '甘小琴', '0978012345', 2, '1991-02-23', 'xiaoqin.gan@example.com', 1, '2024-04-12 15:25:00', '2024-04-12 15:25:00'),
('dehua_wen', 'wen2024', '溫德華', '0989123456', 1, '1989-08-10', 'dehua.wen@example.com', 4, '2024-04-13 12:45:00', '2024-04-13 12:45:00'),
('liqin_hua', 'hua123456', '華麗琴', '0290234567', 2, '1994-04-27', 'liqin.hua@example.com', 2, '2024-04-14 17:20:00', '2024-04-14 17:20:00'),
('zhenyu_hong', 'hong789', '洪振宇', '0912345678', 1, '1986-12-14', 'zhenyu.hong@example.com', 1, '2024-04-15 10:55:00', '2024-04-15 10:55:00'),
('jinhua_pu', 'pu2024', '濮金華', '0923456789', 2, '1990-06-01', 'jinhua.pu@example.com', 3, '2024-04-16 14:15:00', '2024-04-16 14:15:00'),
('mingshan_leng', 'leng456', '冷明山', '0934567890', 1, '1992-01-18', 'mingshan.leng@example.com', 2, '2024-04-17 09:40:00', '2024-04-17 09:40:00'),
('yulan_que', 'que123789', '闕玉蘭', '0345678901', 2, '1988-09-05', 'yulan.que@example.com', 1, '2024-04-18 16:00:00', '2024-04-18 16:00:00'),
('changjie_wei', 'wei2024', '魏昌傑', '0956789012', 1, '1995-05-22', 'changjie.wei@example.com', 4, '2024-04-19 11:25:00', '2024-04-19 11:25:00'),
('huijuan_tao', 'tao789012', '陶慧娟', '0967890123', 2, '1993-11-09', 'huijuan.tao@example.com', 2, '2024-04-20 13:50:00', '2024-04-20 13:50:00'),
('zhonghua_fei', 'fei123', '費中華', '0978901234', 1, '1987-03-26', 'zhonghua.fei@example.com', 1, '2024-04-21 08:35:00', '2024-04-21 08:35:00'),
('cuiping_lian', 'lian456789', '連翠萍', '0489012345', 2, '1991-10-13', 'cuiping.lian@example.com', 3, '2024-04-22 15:45:00', '2024-04-22 15:45:00'),
('guojun_zuo', 'zuo2024', '左國軍', '0990123456', 1, '1989-06-30', 'guojun.zuo@example.com', 2, '2024-04-23 12:20:00', '2024-04-23 12:20:00'),
('xiaoyan_sha', 'sha789012', '沙曉燕', '0912234567', 2, '1994-02-17', 'xiaoyan.sha@example.com', 1, '2024-04-24 17:10:00', '2024-04-24 17:10:00'),
('suxing410', 'su12345', '肅幸', '0917122344', 2, '1996-11-21', 'suxing@gmail.com', 4, '2025-06-14 19:18:00', '2025-06-14 19:19:00'),
('emmavn', 'a123456', '䕒心', '0953285082', 2, '1996-05-23', 'emma@gmail.com', 4, '2025-06-14 19:18:00', '2025-06-14 19:19:00'),
('dodocat', '12345', '建亨', '0909182222', 1, '1999-01-22', 'dodocat@gmail.com', 4, '2025-06-14 19:18:00', '2025-06-14 19:19:00');



SELECT * FROM `members`;
SELECT * FROM `member_ban`;

SELECT * FROM `genders`;

SELECT * FROM `member_level`;


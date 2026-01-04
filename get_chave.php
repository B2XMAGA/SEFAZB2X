<?php
$host = 'a8484c4s4cgoskk0g8scsk08';
$port = 3306;
$db   = 'b2x_sefaz';
$user = 'b2x_sefaz';
$pass = '@Pl68267713210';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true, // 🔥 ESSENCIAL
]);

$sql = <<<SQL
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for account
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account`  (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `account_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_id_company` int NULL DEFAULT NULL,
  `account_ide_client` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_code_bank` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_key_pix` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_client_id` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `account_dev_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_client_secret` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `account_trash` int NULL DEFAULT NULL,
  `account_status` int NULL DEFAULT NULL,
  `account_certificate` blob NULL,
  `account_password` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_certificate_key` blob NULL,
  `account_pluggy_ide_item` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_pluggy_ide_account` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_agency` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account_conta` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`account_id`) USING BTREE,
  INDEX `idx_account_ide_id`(`account_ide`, `account_ide_client`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of account
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
SQL;

try {
    $pdo->exec($sql);
    echo "✅ Script SQL executado com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro ao executar SQL: " . $e->getMessage();
}

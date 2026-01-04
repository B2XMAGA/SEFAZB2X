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

-- ----------------------------
-- Table structure for chave
-- ----------------------------
DROP TABLE IF EXISTS `chave`;
CREATE TABLE `chave`  (
  `chave_id` int NOT NULL AUTO_INCREMENT,
  `chave_id_company` int NULL DEFAULT NULL,
  `chave_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `chave_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `chave_data_hora` datetime NULL DEFAULT NULL,
  `chave_status` int NULL DEFAULT NULL,
  `chave_data_hora_final` datetime NULL DEFAULT NULL,
  `chave_xml` blob NULL,
  `chave_ide_client` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`chave_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of chave
-- ----------------------------

-- ----------------------------
-- Table structure for check
-- ----------------------------
DROP TABLE IF EXISTS `check`;
CREATE TABLE `check`  (
  `check_id` int NOT NULL AUTO_INCREMENT,
  `check_id_company` int NULL DEFAULT NULL,
  `check_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_data_hora` datetime NULL DEFAULT NULL,
  `check_status` int NULL DEFAULT NULL,
  PRIMARY KEY (`check_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of check
-- ----------------------------

-- ----------------------------
-- Table structure for check_pix
-- ----------------------------
DROP TABLE IF EXISTS `check_pix`;
CREATE TABLE `check_pix`  (
  `check_pix_id` int NOT NULL AUTO_INCREMENT,
  `check_pix_id_check` int NULL DEFAULT NULL,
  `check_pix_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_chave` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_codigo_banco` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_nome_banco` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_nome_titular` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_doc_titular` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_agencia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_agencia_digito` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_conta` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_conta_digito` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `check_pix_status` int NULL DEFAULT NULL,
  `check_pix_datetime` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`check_pix_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of check_pix
-- ----------------------------

-- ----------------------------
-- Table structure for client
-- ----------------------------
DROP TABLE IF EXISTS `client`;
CREATE TABLE `client`  (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `client_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_id_company` int NULL DEFAULT NULL,
  `client_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_fantasy` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_doc` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_ie` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_im` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_place` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_number` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_district` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_zip` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_code_city` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_city` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_state` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_address_complement` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_fixed` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_whats` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_status` int NULL DEFAULT NULL,
  `client_dfe_certificate` blob NULL,
  `client_dfe_password_certificate` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_dfe_use` int NULL DEFAULT NULL,
  `client_dfe_ult_nsu` int NULL DEFAULT NULL,
  `client_dfe_date_consult` datetime NULL DEFAULT NULL,
  `client_cnae` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_crt` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_csc` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_cscid` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_dfe_ult_event_code` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_dfe_ult_event_desc` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_dfe_ult_event_datetime` datetime NULL DEFAULT NULL,
  `client_type` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_status_certificado` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_validade_certificado` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_forcar_download` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_status_comunicacao` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_status_comunicacao_texto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `client_status_comunicacao_data` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`client_id`) USING BTREE,
  INDEX `idx_client_ide`(`client_ide`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of client
-- ----------------------------

-- ----------------------------
-- Table structure for company
-- ----------------------------
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company`  (
  `company_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_status` int NULL DEFAULT NULL,
  `company_client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_client_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `company_iat_token` bigint NULL DEFAULT NULL,
  `company_exp_token` bigint NULL DEFAULT NULL,
  `company_cartao_credito_status` int NULL DEFAULT NULL,
  `company_cartao_credito_ambiente` int NULL DEFAULT NULL,
  `company_cartao_credito_taxa` double NULL DEFAULT NULL,
  `company_id_wallet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `company_token_asaas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company
-- ----------------------------
INSERT INTO `company` VALUES (1, 'Marques Junior', 'junioralphasistemas@gmail.com', '123456', 0, 'e10adc3949ba59abbe56e057f20f8asd', '8d969edddcad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'a9add53c8ecc9544c4b7484678aa5a1f', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjY2MDI4MTQsImVtYWlsIjoianVuaW9yYWxwaGFzaXN0ZW1hc0BnbWFpbC5jb20iLCJuYmYiOjE3NjY2MDI4MTQsImV4cCI6MTc2NjY4OTIxNCwia2V5IjoiYTlhZGQ1M2M4ZWNjOTU0NGM0Yjc0ODQ2NzhhYTVhMWYifQ.w6I9V15ZEanf1-9zWLBUag0UeobNizfKWcixDbYhg4g', 1766602814, 1766689214, 1, 1, 2, NULL, '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwMDkwMzg6OiRhYWNoXzIzZTVlMjcxLWE4ZjktNDEwMS1hOGE2LWE0NjVhNTBhOTJmMA==');

-- ----------------------------
-- Table structure for consulta_sped
-- ----------------------------
DROP TABLE IF EXISTS `consulta_sped`;
CREATE TABLE `consulta_sped`  (
  `consulta_sped_id` int NOT NULL AUTO_INCREMENT,
  `consulta_sped_id_empresa` int NULL DEFAULT NULL,
  `consulta_sped_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `consulta_sped_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `consulta_sped_data_hora` datetime NULL DEFAULT NULL,
  `consulta_sped_status` int NULL DEFAULT NULL,
  `consulta_sped_data_hora_final` datetime NULL DEFAULT NULL,
  `consulta_sped_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `consulta_sped_certificado` longblob NULL,
  `consulta_sped_senha` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `consulta_sped_data_inicial` date NULL DEFAULT NULL,
  `consulta_sped_data_final` date NULL DEFAULT NULL,
  PRIMARY KEY (`consulta_sped_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of consulta_sped
-- ----------------------------

-- ----------------------------
-- Table structure for contato
-- ----------------------------
DROP TABLE IF EXISTS `contato`;
CREATE TABLE `contato`  (
  `contato_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contato_id_tipo_contato` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_id_empresa` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_tipo_cliente` int NULL DEFAULT 0,
  `contato_tipo_fornecedor` int NULL DEFAULT 0,
  `contato_tipo_transportador` int NULL DEFAULT 0,
  `contato_cliente_final` int NULL DEFAULT 0,
  `contato_nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_doc` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_nome_fantasia` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_nome_representante` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_telefone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_endereco_cep` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_logradouro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_complemento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_bairro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_uf` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_cidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_ibge` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_cep` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_logradouro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_complemento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_bairro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_uf` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_cidade` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_cidade_ibge` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_doc` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_ie` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_endereco_entrega_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_endereco_entrega_telefone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_indicador_ie` int NULL DEFAULT 0,
  `contato_ie` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_ie_sub_trib` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_im` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_suframa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_data_nascimento` date NULL DEFAULT NULL,
  `contato_palavras_chave` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_data_comemorativa` date NULL DEFAULT NULL,
  `contato_descricao_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_base_legal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `contato_obs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `contato_lixeira` int NULL DEFAULT 0,
  `contato_status` int NULL DEFAULT 0,
  PRIMARY KEY (`contato_id`) USING BTREE,
  INDEX `idx_contato_empresa`(`contato_id_empresa` ASC) USING BTREE,
  INDEX `idx_contato_tipo`(`contato_id_tipo_contato` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of contato
-- ----------------------------

-- ----------------------------
-- Table structure for debug
-- ----------------------------
DROP TABLE IF EXISTS `debug`;
CREATE TABLE `debug`  (
  `debug_id` int NOT NULL AUTO_INCREMENT,
  `debug_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `debug_header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `debug_end` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `debug_id_company` int NULL DEFAULT NULL,
  `debug_date_hora` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`debug_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 844847 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of debug
-- ----------------------------

-- ----------------------------
-- Table structure for delivery
-- ----------------------------
DROP TABLE IF EXISTS `delivery`;
CREATE TABLE `delivery`  (
  `delivery_id` int NOT NULL AUTO_INCREMENT,
  `delivery_id_company` int NULL DEFAULT NULL,
  `delivery_ide_hub_delivery` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_name_cliente` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_date_time` datetime NULL DEFAULT NULL,
  `delivery_status` int NULL DEFAULT NULL,
  `delivery_subtotal` double(15, 2) NULL DEFAULT NULL,
  `delivery_forma_pagamento` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_desconto` double(15, 2) NULL DEFAULT NULL,
  `delivery_total` double(15, 2) NULL DEFAULT NULL,
  `delivery_trash` int NULL DEFAULT NULL,
  `delivery_frete` double(15, 2) NULL DEFAULT NULL,
  `delivery_cpf_cliente` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_endereco_rota` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_endereco_complemento` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_endereco_cidade_uf` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_endereco_cep` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_endereco_bairro` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_troco` double(15, 2) NULL DEFAULT NULL,
  `delivery_troco_para` double(15, 2) NULL DEFAULT NULL,
  `delivery_taxa_conveniencia` double(15, 2) NULL DEFAULT NULL,
  `delivery_data_hora_captura` datetime NULL DEFAULT NULL,
  `delivery_codigo_entrega` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_obs` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `delivery_data_itens` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_tipo_pedido` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_telefone` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_tem_itens` int NULL DEFAULT NULL,
  PRIMARY KEY (`delivery_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_data
-- ----------------------------
DROP TABLE IF EXISTS `delivery_data`;
CREATE TABLE `delivery_data`  (
  `delivery_data_id` int NOT NULL AUTO_INCREMENT,
  `delivery_data_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `delivery_data_hora_pedido` datetime NULL DEFAULT NULL,
  `delivery_data_hora_aceite` datetime NULL DEFAULT NULL,
  `delivery_data_hora_captura` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`delivery_data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_data
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_itens
-- ----------------------------
DROP TABLE IF EXISTS `delivery_itens`;
CREATE TABLE `delivery_itens`  (
  `delivery_itens_id` int NOT NULL AUTO_INCREMENT,
  `delivery_itens_id_delivery` int NULL DEFAULT NULL,
  `delivery_itens_id_produto` int NULL DEFAULT NULL,
  `delivery_itens_descricao` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_itens_qtd` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `delivery_itens_valor_unitario` double(15, 2) NULL DEFAULT NULL,
  `delivery_itens_valor_total` double(15, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`delivery_itens_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_itens
-- ----------------------------

-- ----------------------------
-- Table structure for dfe
-- ----------------------------
DROP TABLE IF EXISTS `dfe`;
CREATE TABLE `dfe`  (
  `dfe_id` int NOT NULL AUTO_INCREMENT,
  `dfe_ide_client` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `dfe_nsu` int NULL DEFAULT NULL,
  `dfe_doc` longblob NULL,
  `dfe_status` int NULL DEFAULT NULL,
  `dfe_schema` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `dfe_url_s3` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `dfe_md5` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`dfe_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3326265 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dfe
-- ----------------------------

-- ----------------------------
-- Table structure for dfe_hours
-- ----------------------------
DROP TABLE IF EXISTS `dfe_hours`;
CREATE TABLE `dfe_hours`  (
  `dfe_hours_id` int NOT NULL AUTO_INCREMENT,
  `dfe_hours_ide_client` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `dfe_hours_key` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`dfe_hours_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5005 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dfe_hours
-- ----------------------------

-- ----------------------------
-- Table structure for dfe_migrate
-- ----------------------------
DROP TABLE IF EXISTS `dfe_migrate`;
CREATE TABLE `dfe_migrate`  (
  `dfe_id` int NOT NULL AUTO_INCREMENT,
  `dfe_ide_client` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `dfe_nsu` int NULL DEFAULT NULL,
  `dfe_doc` longblob NULL,
  `dfe_status` int NULL DEFAULT NULL,
  `dfe_schema` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`dfe_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3283267 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dfe_migrate
-- ----------------------------

-- ----------------------------
-- Table structure for doc
-- ----------------------------
DROP TABLE IF EXISTS `doc`;
CREATE TABLE `doc`  (
  `doc_id` int NOT NULL AUTO_INCREMENT,
  `doc_id_client` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_mod` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_code` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_nat_op` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_serie` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_num` int NULL DEFAULT NULL,
  `doc_date_emi` datetime NULL DEFAULT NULL,
  `doc_date_sai` datetime NULL DEFAULT NULL,
  `doc_emit_documento` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_emit_nome` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_emit_fantasia` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_emit_ie` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_dest_documento` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_dest_nome` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_dest_ie` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_valor` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_trib` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_base_icms` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_icms` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_frete` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_seguro` double(15, 2) NULL DEFAULT 0.00,
  `doc_valor_desconto` double(15, 2) NULL DEFAULT 0.00,
  `doc_uf_inicio` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_uf_final` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_status` int NULL DEFAULT NULL,
  `doc_status_download` int NULL DEFAULT NULL,
  `doc_status_manifestacao` int NULL DEFAULT NULL,
  `doc_file` longblob NULL,
  `doc_chave` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_tipo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_status_s3` int NULL DEFAULT NULL,
  `doc_ide` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`doc_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 36498 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of doc
-- ----------------------------

-- ----------------------------
-- Table structure for doc_res
-- ----------------------------
DROP TABLE IF EXISTS `doc_res`;
CREATE TABLE `doc_res`  (
  `doc_res_id` int NOT NULL AUTO_INCREMENT,
  `doc_res_id_client` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_chave` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_doc` varchar(35) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_ie` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_date_emi` datetime NULL DEFAULT NULL,
  `doc_res_amount` double(15, 2) NULL DEFAULT NULL,
  `doc_res_dig` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_date_recbto` datetime NULL DEFAULT NULL,
  `doc_res_num_prot` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_status` int NULL DEFAULT NULL,
  `doc_res_file` blob NULL,
  `doc_res_ide` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `doc_res_status_s3` int NULL DEFAULT NULL,
  PRIMARY KEY (`doc_res_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38258 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of doc_res
-- ----------------------------


SET FOREIGN_KEY_CHECKS = 1;
SQL;

try {
    $pdo->exec($sql);
    echo "✅ Script SQL executado com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro ao executar SQL: " . $e->getMessage();
}

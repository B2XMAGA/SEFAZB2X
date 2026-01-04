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

-- ----------------------------
-- Table structure for eventos
-- ----------------------------
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE `eventos`  (
  `eventos_id` int NOT NULL AUTO_INCREMENT,
  `eventos_id_client` varchar(35) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_chave` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_code_evento` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_desc_evento` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_data` datetime NULL DEFAULT NULL,
  `eventos_prot` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_file` longblob NULL,
  `eventos_ide` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_status_s3` int NULL DEFAULT NULL,
  PRIMARY KEY (`eventos_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 159 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of eventos
-- ----------------------------

-- ----------------------------
-- Table structure for finance
-- ----------------------------
DROP TABLE IF EXISTS `finance`;
CREATE TABLE `finance`  (
  `finance_id` int NOT NULL AUTO_INCREMENT,
  `finance_id_company` int NULL DEFAULT NULL,
  `finance_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_ide_wallet` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_type_pagador` int NULL DEFAULT NULL,
  `finance_doc_pagador` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_name_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_address_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_number_pagador` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_district_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_complement_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_city_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_state_pagador` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_zip_pagador` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_telefone_pagador` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_email_pagador` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_our_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_date_due` date NULL DEFAULT NULL,
  `finance_value` double(15, 2) NULL DEFAULT NULL,
  `finance_date_payment` date NULL DEFAULT NULL,
  `finance_value_payment` double(15, 2) NULL DEFAULT NULL,
  `finance_status` int NULL DEFAULT NULL,
  `finance_trash` int NULL DEFAULT NULL,
  `finance_status_webservice` int NULL DEFAULT NULL,
  `finance_barcode` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `finance_line_dig` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `finance_date_release` date NULL DEFAULT NULL,
  `finance_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_payload_hybrid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_txtId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_antecipado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_antecipado_valor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_antecipado_data` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_ra` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_codigo_solicitacao` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_data_credito` date NULL DEFAULT NULL,
  `finance_valor_multa` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_valor_juros` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`finance_id`) USING BTREE,
  INDEX `idx_finance_company`(`finance_id_company` ASC, `finance_trash` ASC) USING BTREE,
  INDEX `idx_finance_wallet`(`finance_ide_wallet` ASC) USING BTREE,
  INDEX `idx_finance_company_trash_id`(`finance_id_company` ASC, `finance_trash` ASC, `finance_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 72 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of finance
-- ----------------------------

-- ----------------------------
-- Table structure for food_delivery
-- ----------------------------
DROP TABLE IF EXISTS `food_delivery`;
CREATE TABLE `food_delivery`  (
  `food_delivery_id` int NOT NULL AUTO_INCREMENT,
  `food_delivery_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `food_delivery_id_entrega` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `food_delivery_status` int NULL DEFAULT NULL,
  `food_delivery_uuid` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `food_delivery_data` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`food_delivery_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of food_delivery
-- ----------------------------

-- ----------------------------
-- Table structure for hub_delivery
-- ----------------------------
DROP TABLE IF EXISTS `hub_delivery`;
CREATE TABLE `hub_delivery`  (
  `hub_delivery_id` int NOT NULL AUTO_INCREMENT,
  `hub_delivery_id_company` int NULL DEFAULT NULL,
  `hub_delivery_ide_client` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_dev` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_clientid` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_secretid` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_status` int NULL DEFAULT NULL,
  `hub_delivery_trash` int NULL DEFAULT NULL,
  `hub_delivery_auth` int NULL DEFAULT NULL,
  `hub_delivery_senha_mail` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`hub_delivery_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hub_delivery
-- ----------------------------

-- ----------------------------
-- Table structure for impressora
-- ----------------------------
DROP TABLE IF EXISTS `impressora`;
CREATE TABLE `impressora`  (
  `impressora_id` int NOT NULL AUTO_INCREMENT,
  `impressora_id_comprany` int NULL DEFAULT NULL,
  `impressora_ide` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_marca` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_modelo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_dim_cima` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_dim_baixo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_dim_direita` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_dim_esquerda` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_status` int NULL DEFAULT NULL,
  `impressora_trash` int NULL DEFAULT NULL,
  PRIMARY KEY (`impressora_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of impressora
-- ----------------------------

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`  (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `log_request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `log_files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `log_post` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for lote
-- ----------------------------
DROP TABLE IF EXISTS `lote`;
CREATE TABLE `lote`  (
  `lote_id` int NOT NULL AUTO_INCREMENT,
  `lote_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_ide_client` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_status` int NULL DEFAULT NULL,
  `lote_certificate` blob NULL,
  `lote_certificate_password` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_date_init` datetime NULL DEFAULT NULL,
  `lote_date_finish` datetime NULL DEFAULT NULL,
  `lote_file` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`lote_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of lote
-- ----------------------------

-- ----------------------------
-- Table structure for lote_boleto
-- ----------------------------
DROP TABLE IF EXISTS `lote_boleto`;
CREATE TABLE `lote_boleto`  (
  `lote_boleto_id` int NOT NULL AUTO_INCREMENT,
  `lote_boleto_id_company` int NULL DEFAULT NULL,
  `lote_boleto_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lote_boleto_data_hora` datetime NULL DEFAULT NULL,
  `lote_boleto_status` int NULL DEFAULT NULL,
  `lote_boleto_trash` int NULL DEFAULT NULL,
  `lote_boleto_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`lote_boleto_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of lote_boleto
-- ----------------------------

-- ----------------------------
-- Table structure for lote_boleto_ret
-- ----------------------------
DROP TABLE IF EXISTS `lote_boleto_ret`;
CREATE TABLE `lote_boleto_ret`  (
  `lote_boleto_ret_id` int NOT NULL AUTO_INCREMENT,
  `lote_boleto_ret_id_lote_boleto` int NULL DEFAULT NULL,
  `lote_boleto_ret_linha_digitavel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lote_boleto_ret_status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lote_boleto_ret_data_consulta` datetime NULL DEFAULT NULL,
  `lote_boleto_ret_data_retorno` datetime NULL DEFAULT NULL,
  `lote_boleto_ret_valor` double(15, 2) NULL DEFAULT NULL,
  `lote_boleto_ret_data_vencimento` date NULL DEFAULT NULL,
  PRIMARY KEY (`lote_boleto_ret_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of lote_boleto_ret
-- ----------------------------

-- ----------------------------
-- Table structure for lote_item
-- ----------------------------
DROP TABLE IF EXISTS `lote_item`;
CREATE TABLE `lote_item`  (
  `lote_item_id` int NOT NULL AUTO_INCREMENT,
  `lote_item_id_lote` int NULL DEFAULT NULL,
  `lote_item_key` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_item_status` int NULL DEFAULT NULL,
  `lote_item_log` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`lote_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of lote_item
-- ----------------------------

-- ----------------------------
-- Table structure for monitor
-- ----------------------------
DROP TABLE IF EXISTS `monitor`;
CREATE TABLE `monitor`  (
  `monitor_id` int NOT NULL AUTO_INCREMENT,
  `monitor_ide_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_nome_arquivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_status` int NULL DEFAULT NULL,
  PRIMARY KEY (`monitor_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of monitor
-- ----------------------------

-- ----------------------------
-- Table structure for monitor_xml
-- ----------------------------
DROP TABLE IF EXISTS `monitor_xml`;
CREATE TABLE `monitor_xml`  (
  `monitor_xml_id` int NOT NULL AUTO_INCREMENT,
  `monitor_xml_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_id_company` int NULL DEFAULT NULL,
  `monitor_xml_chave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_status` int NULL DEFAULT NULL,
  `monitor_xml_descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_data_criacao` datetime NULL DEFAULT NULL,
  `monitor_xml_data_ultima_conferencia` datetime NULL DEFAULT NULL,
  `monitor_xml_tipo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`monitor_xml_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of monitor_xml
-- ----------------------------

-- ----------------------------
-- Table structure for monitor_xml_webhook
-- ----------------------------
DROP TABLE IF EXISTS `monitor_xml_webhook`;
CREATE TABLE `monitor_xml_webhook`  (
  `monitor_xml_webhook_id` int NOT NULL AUTO_INCREMENT,
  `monitor_xml_webhook_id_company` int NULL DEFAULT NULL,
  `monitor_xml_webhook_ide` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_webhook_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `monitor_xml_webhook_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`monitor_xml_webhook_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of monitor_xml_webhook
-- ----------------------------

-- ----------------------------
-- Table structure for nfe
-- ----------------------------
DROP TABLE IF EXISTS `nfe`;
CREATE TABLE `nfe`  (
  `nfe_id` int NOT NULL AUTO_INCREMENT,
  `nfe_id_company` int NULL DEFAULT NULL,
  `nfe_status` int NULL DEFAULT NULL,
  `nfe_ide_client` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_natureza_operacao` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_serie` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_numero` int NULL DEFAULT NULL,
  `nfe_data_emissao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_data_entrada_saida` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_tipo_operacao` int NULL DEFAULT NULL,
  `nfe_finalidade_emissao` int NULL DEFAULT NULL,
  `nfe_consumidor_final` int NULL DEFAULT NULL,
  `nfe_presenca_comprador` int NULL DEFAULT NULL,
  `nfe_destinatario_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_cpf` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_id_estrangeiro` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_indicador_inscricao_estadual` int NULL DEFAULT NULL,
  `nfe_destinatario_inscricao_estadual` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_inscricao_suframa` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_inscricao_municipal` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_logradouro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_numero` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_complemento` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_bairro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_codigo_municipio` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_nome_municipio` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_cep` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_codigo_pais` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_nome_pais` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_destinatario_endereco_telefone` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_informe` int NULL DEFAULT NULL,
  `nfe_local_retirada_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_cpf` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_inscricao_estadual` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_logradouro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_numero` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_complemento` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_bairro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_codigo_municipio` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_nome_municipio` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_cep` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_codigo_pais` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_nome_pais` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_retirada_endereco_telefone` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_informe` int NULL DEFAULT NULL,
  `nfe_local_entrega_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_cpf` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_email` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_inscricao_estadual` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_logradouro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_numero` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_complemento` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_bairro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_codigo_municipio` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_nome_municipio` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_cep` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_codigo_pais` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_nome_pais` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_local_entrega_endereco_telefone` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_informe` int NULL DEFAULT NULL,
  `nfe_notas_referenciadas_tipo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nfe_chave` varchar(44) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_cte_chave` varchar(44) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_anomes` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_modelo` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_serie` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_nf_numero` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_anomes` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_cpf` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_inscricao_estadual` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_modelo` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_serie` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_produtor_numero` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_cupom_modelo` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_cupom_numero_sequencial` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_notas_referenciadas_cupom_coo` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_intermediario_informe` int NULL DEFAULT NULL,
  `nfe_intermediario_indicador` int NULL DEFAULT NULL,
  `nfe_intermediario_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_intermediario_identificacao` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_modalidade_frete` int NULL DEFAULT NULL,
  `nfe_frete_transportador_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_cpf` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_inscricao_estadual` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_endereco` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_nome_municipio` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_transportador_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_retencao_informe` int NULL DEFAULT NULL,
  `nfe_frete_retencao_valor_servico` double(15, 2) NULL DEFAULT NULL,
  `nfe_frete_retencao_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_frete_retencao_valor_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_frete_retencao_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_frete_retencao_cfop` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_retencao_codigo_municipio` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_veiculo_placa` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_veiculo_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_veiculo_rntc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_identificacao_vagao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_identificacao_balsa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_volumes_quantidade` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_volumes_especie` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_volumes_marca` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_volumes_numero` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_frete_volumes_peso_liquido` double(15, 3) NULL DEFAULT NULL,
  `nfe_frete_volumes_peso_bruto` double(15, 3) NULL DEFAULT NULL,
  `nfe_frete_volumes_numero_lacre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_cobranca_fatura_numero` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_cobranca_fatura_valor_original` double(15, 2) NULL DEFAULT NULL,
  `nfe_cobranca_fatura_valor_desconto` double(15, 2) NULL DEFAULT NULL,
  `nfe_cobranca_fatura_valor_liquido` double(15, 2) NULL DEFAULT NULL,
  `nfe_pagamento_valor_troco` double(15, 2) NULL DEFAULT NULL,
  `nfe_informacoes_adicionais_fisco` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `nfe_informacoes_adicionais_contribuinte` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `nfe_informacoes_adicionais_compra_informe` int NULL DEFAULT NULL,
  `nfe_informacoes_adicionais_compra_nota_empenho` varchar(22) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_informacoes_adicionais_compra_pedido` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_informacoes_adicionais_compra_contrato` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_exportacao_informe` int NULL DEFAULT NULL,
  `nfe_exportacao_uf_local_embarque` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_exportacao_local_embarque` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_exportacao_local_despacho` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_processo_numero_processo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_processo_identificador_origem_processo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_contingencia_informe` int NULL DEFAULT NULL,
  `nfe_contingencia_data` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_contingencia_motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_totais_icms_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_valor_total_desonerado` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_fcp_valor_total_uf_dest` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_valor_total_uf_dest` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_valor_total_uf_rem` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_fcp_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_st_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_st_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_fcp_st_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_fcp_st_valor_total_retido` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_monofasico_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_monofasico_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_monofasico_retencao_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_
icms_monofasico_retencao_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_monofasico_retido_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_icms_monofasico_retido_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_valor_produtos_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_valor_frete_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_valor_seguro_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_valor_desconto_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_ii_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_ipi_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_ipi_valor_devolvido_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_pis_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_cofins_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_outras_despesas_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_tributos_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_informe` int NULL DEFAULT NULL,
  `nfe_totais_issqn_servicos_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_base_calculo_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_iss_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_pis_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_cofins_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_data_prestacao_servico` date NULL DEFAULT NULL,
  `nfe_totais_issqn_deducao_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_outros_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_desconto_incondicionado_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_desconto_condicionado_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_iss_retido_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_issqn_regime_especial_tributacao` int NULL DEFAULT NULL,
  `nfe_totais_retencoes_informe` int NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_pis_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_cofins_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_csll_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_base_calculo_irrf_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_irrf_valor_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_base_calculo_previdencia_valor_tot` double(15, 2) NULL DEFAULT NULL,
  `nfe_totais_retencoes_tributos_previdencia_valor_total` double(15, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_duplicata
-- ----------------------------
DROP TABLE IF EXISTS `nfe_duplicata`;
CREATE TABLE `nfe_duplicata`  (
  `nfe_duplicata_id` int NOT NULL AUTO_INCREMENT,
  `nfe_duplicata_id_nfe` int NULL DEFAULT NULL,
  `nfe_duplicata_data_vencimento` date NULL DEFAULT NULL,
  `nfe_duplicata_valor_original` double(15, 2) NULL DEFAULT NULL,
  `nfe_duplicata_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_duplicata_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_duplicata
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens`;
CREATE TABLE `nfe_itens`  (
  `nfe_itens_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_id_nfe` int NULL DEFAULT NULL,
  `nfe_itens_numero_item` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_produto` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_origem` int NULL DEFAULT NULL,
  `nfe_itens_codigo_barras_comercial` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_barras_interno_comercial` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_descricao` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_ncm` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_cest` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_escala_relevante` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_cnpj_fabricante` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_beneficio_fiscal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_ex_tipi` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_cfop` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_unidade_comercial` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_quantidade_comercial` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_valor_unitario_comercial` double(15, 10) NULL DEFAULT NULL,
  `nfe_itens_valor_bruto` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_codigo_barras_tributavel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_codigo_barras_interno_tributavel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_unidade_tributavel` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_quantidade_tributavel` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_valor_unitario_tributavel` double(15, 10) NULL DEFAULT NULL,
  `nfe_itens_valor_frete` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_valor_seguro` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_valor_desconto` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_valor_outras_despesas` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_inclui_no_total` int NULL DEFAULT NULL,
  `nfe_itens_numero_fci` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_informacoes_adicionais` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `nfe_itens_pedido_compra_informe` int NULL DEFAULT NULL,
  `nfe_itens_pedido_compra_numero` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_pedido_compra_item` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_produto_especifico_informe` int NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_tipo_operacao` int NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_chassi` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_codigo_cor` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_descricao_cor` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_potencia_motor` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_capacidade` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_peso_liquido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_peso_bruto` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_numero_serie` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_tipo_combustivel` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_numero_motor` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_capacidade_maxima_tracao` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_distancia_eixos` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_ano_modelo` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_ano_fabricacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_tipo_pintura` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_tipo` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_especie` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_remercacao` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_condicao` int NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_codigo_marca_modelo` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_codigo_cor_denatran` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_lotacao` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_veiculos_novos_restricao` int NULL DEFAULT NULL,
  `nfe_itens_medicamentos_codigo_anvisa` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_medicamentos_motivo_isencao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_medicamentos_preco_maximo_consumidor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_armamentos_tipo` int NULL DEFAULT NULL,
  `nfe_itens_armamentos_serie` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_armamentos_cano_serie` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_armamentos_descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_codigo_anp` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_descricao_anp` varchar(95) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_percentual_glp` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_percentual_gas_natural_nacional` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_percentual_gas_natural_importado` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_valor_partida` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_registro_codif` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_quantidade_temperatura_ambiente` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_sigla_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_cide_base_calculo` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_cide_aliquota` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_cide_valor` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_percentual_biodiesel` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_encerrante_numero_bico` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_encerrante_numero_bomba` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_encerrante_numero_tanque` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustiveis_encerrante_valor_inicial` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustiveis_encerrante_valor_final` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_papel_imune_recopi` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_valor_aproximado_tributos` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_situacao_tributaria` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_modalidade_base_calculo` int NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_aliquota` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_ad_rem_monofasico` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_modalidade_base_calculo_st` int NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_margem_valor_adicionado_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_reducao_base_calculo_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_motivo_desoneracao_icms_st` int NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_icms_st_desonerado` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_valor_base_calculo_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_aliquota_st` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_fcp_valor_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo_retencao_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_ad_rem_retencao_monofasico` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_retencao_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_motivo_reducao_ad_rem_monofasico` int NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_reducao_ad_rem_monofasico` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_reducao_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_motivo_desoneracao` int NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_desonerado` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_operacao` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_diferimento` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_diferido` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_operacao_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_diferimento_monofasico` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo_retido_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_substituto` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_retido_st` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_base_calculo_retido_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_ad_rem_retido_monofasico` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_retido_monofasico` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_operacao_propria` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_part_uf` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_final` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_aliquota_credito_simples` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_icms_valor_credito_simples` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_situacao_tributaria` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_quantidade_total` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_valor_unidade_tributavel` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_cnpj_produtor` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_codigo_selo_controle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_quantidade_selo_controle` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_codigo_enquadramento_legal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_devolvido_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_devolvido_percentual` int NULL DEFAULT NULL,
  `nfe_itens_imposto_ipi_devolvido_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_situacao_tributaria` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_quantidade_vendida` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_aliquota_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pis_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_aliquota` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_quantidade_vendida` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_aliquota_valor` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_pisst_inclui_no_total` int NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_situacao_tributaria` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_quantidade_vendida` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_aliquota_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofins_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_aliquota` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_quantidade_vendida` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_aliquota_valor` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_cofinsst_inclui_no_total` int NULL DEFAULT NULL,
  `nfe_itens_imposto_importacao_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_importacao_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_importacao_valor_aduaneiro` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_importacao_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_importacao_valor_iof` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_informe` int NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_base_calculo` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_aliquota` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_codigo_muncipio` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_codigo_servico` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_deducao` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_outro` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_desconto_incondicionado` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_desconto_condicionado` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_valor_iss_retido` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_exigibilidade_iss` int NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_codigo_municipal_servico` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_codigo_municipio_incidencia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_codigo_pais` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_numero_processo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_imposto_issqn_incentivo_fiscal` int NULL DEFAULT NULL,
  `nfe_itens_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_combustivel
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_combustivel`;
CREATE TABLE `nfe_itens_combustivel`  (
  `nfe_itens_combustivel_origens_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_combustivel_origens_id_nfe_itens` int NULL DEFAULT NULL,
  `nfe_itens_combustivel_origens_indicador_importacao` int NULL DEFAULT NULL,
  `nfe_itens_combustivel_origens_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_combustivel_origens_percentual` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_combustivel_origens_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_combustivel_origens_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_combustivel
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_doc_importacao
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_doc_importacao`;
CREATE TABLE `nfe_itens_doc_importacao`  (
  `nfe_itens_doc_importacao_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_doc_importacao_id_nfe_itens` int NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_numero` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_data_registro` date NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_local_desembaraco_aduaneiro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_uf_desembaraco_aduaneiro` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_data_desembaraco_aduaneiro` date NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_via_transporte` int NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_valor_afrmm` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_forma_intermedio` int NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_uf_terceiro` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_codigo_exportador` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_doc_importacao_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_doc_importacao
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_doc_importacao_adicao
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_doc_importacao_adicao`;
CREATE TABLE `nfe_itens_doc_importacao_adicao`  (
  `nfe_itens_doc_importacao_adicao_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_doc_importacao_adicao_id_nfe_itens_doc_importacao` int NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_numero` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_numero_sequencial_item` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_codigo_fabricante_estrangeiro` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_valor_desconto` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_numero_drawback` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_doc_importacao_adicao_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_doc_importacao_adicao_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_doc_importacao_adicao
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_exportacao
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_exportacao`;
CREATE TABLE `nfe_itens_exportacao`  (
  `nfe_itens_exportacao_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_exportacao_id_nfe_itens` int NULL DEFAULT NULL,
  `nfe_itens_exportacao_numero_drawback` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_exportacao_exportacao_indireta_numero_re` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_exportacao_exportacao_indireta_chave_nfe` varchar(44) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_exportacao_exportacao_indireta_quantidade` double(15, 2) NULL DEFAULT NULL,
  `nfe_itens_exportacao_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_exportacao_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_exportacao
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_nve
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_nve`;
CREATE TABLE `nfe_itens_nve`  (
  `nfe_itens_nve_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_nve_id_nfe_itens` int NULL DEFAULT NULL,
  `nfe_itens_nve_codigo` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_nve_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_nve_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_nve
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_itens_rastros
-- ----------------------------
DROP TABLE IF EXISTS `nfe_itens_rastros`;
CREATE TABLE `nfe_itens_rastros`  (
  `nfe_itens_rastros_id` int NOT NULL AUTO_INCREMENT,
  `nfe_itens_rastros_id_nfe_itens` int NULL DEFAULT NULL,
  `nfe_itens_rastros_numero_lote` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_rastros_quantidade_lote` double(15, 4) NULL DEFAULT NULL,
  `nfe_itens_rastros_data_fabricacao` date NULL DEFAULT NULL,
  `nfe_itens_rastros_data_validade` date NULL DEFAULT NULL,
  `nfe_itens_rastros_codigo_agregacao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_itens_rastros_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_itens_rastros_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_itens_rastros
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_obs_contribuinte
-- ----------------------------
DROP TABLE IF EXISTS `nfe_obs_contribuinte`;
CREATE TABLE `nfe_obs_contribuinte`  (
  `nfe_obs_contribuinte_id` int NOT NULL AUTO_INCREMENT,
  `nfe_obs_contribuinte_id_nfe` int NULL DEFAULT NULL,
  `nfe_obs_contribuinte_campo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_obs_contribuinte_texto` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_obs_contribuinte_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_obs_contribuinte_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_obs_contribuinte
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_obs_fisco
-- ----------------------------
DROP TABLE IF EXISTS `nfe_obs_fisco`;
CREATE TABLE `nfe_obs_fisco`  (
  `nfe_obs_fisco_id` int NOT NULL AUTO_INCREMENT,
  `nfe_obs_fisco_id_nfe` int NULL DEFAULT NULL,
  `nfe_obs_fisco_campo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_obs_fisco_texto` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_obs_fisco_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_obs_fisco_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_obs_fisco
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_pagamento_forma_pagamento
-- ----------------------------
DROP TABLE IF EXISTS `nfe_pagamento_forma_pagamento`;
CREATE TABLE `nfe_pagamento_forma_pagamento`  (
  `nfe_pagamento_forma_pagamento_id` int NOT NULL AUTO_INCREMENT,
  `nfe_pagamento_forma_pagamento_id_nfe` int NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_indicador_pagamento` int NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_meio_pagamento` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_descricao_pagamento` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_valor` double(15, 2) NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_tipo_integracao` int NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_cartao_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_cartao_bandeira` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_cartao_numero_autorizacao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pagamento_forma_pagamento_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_pagamento_forma_pagamento_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_pagamento_forma_pagamento
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_pessoas_autorizadas
-- ----------------------------
DROP TABLE IF EXISTS `nfe_pessoas_autorizadas`;
CREATE TABLE `nfe_pessoas_autorizadas`  (
  `nfe_pessoas_autorizadas_id` int NOT NULL AUTO_INCREMENT,
  `nfe_pessoas_autorizadas_id_nfe` int NULL DEFAULT NULL,
  `nfe_pessoas_autorizadas_cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pessoas_autorizadas_cpf` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_pessoas_autorizadas_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_pessoas_autorizadas_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_pessoas_autorizadas
-- ----------------------------

-- ----------------------------
-- Table structure for nfe_reboque
-- ----------------------------
DROP TABLE IF EXISTS `nfe_reboque`;
CREATE TABLE `nfe_reboque`  (
  `nfe_reboque_id` int NOT NULL AUTO_INCREMENT,
  `nfe_reboque_id_nfe` int NULL DEFAULT NULL,
  `nfe_reboque_placa` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_reboque_uf` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_reboque_rntc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nfe_reboque_lixeira` int NULL DEFAULT NULL,
  PRIMARY KEY (`nfe_reboque_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of nfe_reboque
-- ----------------------------

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment`  (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `payment_id_company` int NULL DEFAULT NULL,
  `payment_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_codigo_barras` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_data_pagamento` date NULL DEFAULT NULL,
  `payment_data_vencimento` date NULL DEFAULT NULL,
  `payment_data_agendamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_trash` int NULL DEFAULT NULL,
  `payment_valor_pagamento` double(15, 2) NULL DEFAULT NULL,
  `payment_ide_account` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payment
-- ----------------------------

-- ----------------------------
-- Table structure for payment_pix
-- ----------------------------
DROP TABLE IF EXISTS `payment_pix`;
CREATE TABLE `payment_pix`  (
  `payment_pix_id` int NOT NULL AUTO_INCREMENT,
  `payment_pix_id_company` int NULL DEFAULT NULL,
  `payment_pix_ide_account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_uuid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_valor` double(15, 2) NULL DEFAULT NULL,
  `payment_pix_data_pagamento` date NULL DEFAULT NULL,
  `payment_pix_descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_chave` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_e2eid` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_codigo_solicitacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `payment_pix_data_operacao` date NULL DEFAULT NULL,
  `payment_pix_trash` int NULL DEFAULT NULL,
  PRIMARY KEY (`payment_pix_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payment_pix
-- ----------------------------

-- ----------------------------
-- Table structure for pix
-- ----------------------------
DROP TABLE IF EXISTS `pix`;
CREATE TABLE `pix`  (
  `pix_id` int NOT NULL AUTO_INCREMENT,
  `pix_id_company` int NULL DEFAULT NULL,
  `pix_ide_account` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_txtid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_duration` int NULL DEFAULT NULL,
  `pix_doc` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_amount` double(15, 2) NULL DEFAULT NULL,
  `pix_description` varchar(140) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_location` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_e2eid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_reference` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_trash` int NULL DEFAULT NULL,
  `pix_created` datetime NULL DEFAULT NULL,
  `pix_payment` datetime NULL DEFAULT NULL,
  `pix_transacao` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`pix_id`) USING BTREE,
  INDEX `idx_pix_completo`(`pix_ide_account`, `pix_ide`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pix
-- ----------------------------

-- ----------------------------
-- Table structure for pix_add
-- ----------------------------
DROP TABLE IF EXISTS `pix_add`;
CREATE TABLE `pix_add`  (
  `pix_add_id` int NOT NULL AUTO_INCREMENT,
  `pix_add_id_pix` int NULL DEFAULT NULL,
  `pix_add_title` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pix_add_description` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`pix_add_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pix_add
-- ----------------------------

-- ----------------------------
-- Table structure for pixv
-- ----------------------------
DROP TABLE IF EXISTS `pixv`;
CREATE TABLE `pixv`  (
  `pixv_id` int NOT NULL AUTO_INCREMENT,
  `pixv_id_company` int NULL DEFAULT NULL,
  `pixv_ide_account` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_txtid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_vencimento` date NULL DEFAULT NULL,
  `pixv_dia_devolucao` int NULL DEFAULT NULL,
  `pixv_doc` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_endereco` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_cidade` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_uf` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_cep` varchar(14) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_amount` double(15, 2) NULL DEFAULT NULL,
  `pixv_description` varchar(140) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_location` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_ide` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_e2eid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_reference` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_trash` int NULL DEFAULT NULL,
  `pixv_created` datetime NULL DEFAULT NULL,
  `pixv_payment` datetime NULL DEFAULT NULL,
  `pixv_multa_mod` int NULL DEFAULT NULL,
  `pixv_multa_valor` double(15, 2) NULL DEFAULT NULL,
  `pixv_juros_mod` int NULL DEFAULT NULL,
  `pixv_juros_valor` double(15, 2) NULL DEFAULT NULL,
  `pixv_abatimento_mod` int NULL DEFAULT NULL,
  `pixv_abatimento_valor` double(15, 2) NULL DEFAULT NULL,
  `pixv_amount_payment` double(15, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`pixv_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pixv
-- ----------------------------

-- ----------------------------
-- Table structure for pixv_add
-- ----------------------------
DROP TABLE IF EXISTS `pixv_add`;
CREATE TABLE `pixv_add`  (
  `pixv_add_id` int NOT NULL AUTO_INCREMENT,
  `pixv_add_id_pixv` int NULL DEFAULT NULL,
  `pixv_add_title` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pixv_add_description` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`pixv_add_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pixv_add
-- ----------------------------

-- ----------------------------
-- Table structure for pixv_desconto
-- ----------------------------
DROP TABLE IF EXISTS `pixv_desconto`;
CREATE TABLE `pixv_desconto`  (
  `pixv_desconto_id` int NOT NULL AUTO_INCREMENT,
  `pixv_desconto_id_pixv` int NULL DEFAULT NULL,
  `pixv_desconto_data` date NULL DEFAULT NULL,
  `pixv_desconto_valor` double(15, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`pixv_desconto_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pixv_desconto
-- ----------------------------

-- ----------------------------
-- Table structure for produto
-- ----------------------------
DROP TABLE IF EXISTS `produto`;
CREATE TABLE `produto`  (
  `produto_id` int NOT NULL AUTO_INCREMENT,
  `produto_descricao` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `produto_tipo` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `produto_link_imagem` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `produto_codigo_ze` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`produto_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of produto
-- ----------------------------

-- ----------------------------
-- Table structure for regularize
-- ----------------------------
DROP TABLE IF EXISTS `regularize`;
CREATE TABLE `regularize`  (
  `regularize_id` int NOT NULL AUTO_INCREMENT,
  `regularize_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `regularize_id_company` int NULL DEFAULT NULL,
  `regularize_status` int NULL DEFAULT NULL,
  `regularize_certificado` longblob NULL,
  `regularize_certificado_senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `regularize_procurador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`regularize_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of regularize
-- ----------------------------

-- ----------------------------
-- Table structure for regularize_itens
-- ----------------------------
DROP TABLE IF EXISTS `regularize_itens`;
CREATE TABLE `regularize_itens`  (
  `regularize_itens_id` int NOT NULL AUTO_INCREMENT,
  `regularize_itens_id_regularize` int NULL DEFAULT NULL,
  `regularize_itens_json` longblob NULL,
  PRIMARY KEY (`regularize_itens_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of regularize_itens
-- ----------------------------

-- ----------------------------
-- Table structure for saldo
-- ----------------------------
DROP TABLE IF EXISTS `saldo`;
CREATE TABLE `saldo`  (
  `saldo_id` int NOT NULL AUTO_INCREMENT,
  `saldo_valor` int NULL DEFAULT NULL,
  PRIMARY KEY (`saldo_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of saldo
-- ----------------------------

-- ----------------------------
-- Table structure for transacao
-- ----------------------------
DROP TABLE IF EXISTS `transacao`;
CREATE TABLE `transacao`  (
  `transacao_id` int NOT NULL AUTO_INCREMENT,
  `transacao_ide` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_id_company` int NULL DEFAULT NULL,
  `transacao_ide_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_doc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_cep` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_customer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_billing_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_valor` double(15, 2) NULL DEFAULT NULL,
  `transacao_data_vencimento` datetime NULL DEFAULT NULL,
  `transacao_ip` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_telefone` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_numero_cartao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_mes_cartao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_ano_cartao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_cvv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_valor_estorno` double(15, 2) NULL DEFAULT NULL,
  `transacao_desc_estorno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_tipo` int NULL DEFAULT NULL,
  `transacao_status` int NULL DEFAULT NULL,
  `transacao_data_pagamento` datetime NULL DEFAULT NULL,
  `transacao_data_liberacao` datetime NULL DEFAULT NULL,
  `transacao_tipo_ap` int NULL DEFAULT NULL,
  `transacao_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `transacao_senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transacao_retorno` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `transacao_id_pix` int NULL DEFAULT NULL,
  PRIMARY KEY (`transacao_id`) USING BTREE,
  UNIQUE INDEX `unique_id_pix`(`transacao_id_pix` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transacao
-- ----------------------------

-- ----------------------------
-- Table structure for type_event
-- ----------------------------
DROP TABLE IF EXISTS `type_event`;
CREATE TABLE `type_event`  (
  `type_event_id` int NOT NULL AUTO_INCREMENT,
  `type_event_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `type_event_description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`type_event_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 101 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of type_event
-- ----------------------------
INSERT INTO `type_event` VALUES (1, '610610', 'MDF-e autorizado');
INSERT INTO `type_event` VALUES (2, '610614', 'MDF-e Autorizado com CT-e');
INSERT INTO `type_event` VALUES (3, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (4, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (5, '610514', 'Registro de Passagem de NFe propagado pelo MDFe/CTe');
INSERT INTO `type_event` VALUES (6, '610600', 'Registro de AutorizaÃ§Ã£o de CT-e para a NF-e');
INSERT INTO `type_event` VALUES (7, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (8, '610552', 'Registro de Passagem Automatico MDFe');
INSERT INTO `type_event` VALUES (9, '610131', 'Cancelamento Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (10, '610615', 'Cancelamento de MDF-e Autorizado com CT-e');
INSERT INTO `type_event` VALUES (11, '610554', 'Registro de Passagem Automatico MDF-e com CT-e');
INSERT INTO `type_event` VALUES (12, '110111', 'Cancelamento');
INSERT INTO `type_event` VALUES (13, '610510', 'Registro de Passagem de NFe propagado pelo MDFe');
INSERT INTO `type_event` VALUES (14, '110110', 'Carta de Correcao');
INSERT INTO `type_event` VALUES (15, '610500', 'Registro de Passagem Autorização');
INSERT INTO `type_event` VALUES (16, '210240', 'Operacao nao Realizada');
INSERT INTO `type_event` VALUES (17, '990900', 'Vistoria SUFRAMA');
INSERT INTO `type_event` VALUES (18, '990910', 'Confirmacao de Internalizacao da Mercadoria na SUFRAMA');
INSERT INTO `type_event` VALUES (19, '210220', 'Desconhecimento da Operacao');
INSERT INTO `type_event` VALUES (20, '610601', 'Registro de Cancelamento de CT-e para a NF-e');
INSERT INTO `type_event` VALUES (21, '510630', 'Registro de Passagem Automatico Originado MDFe');
INSERT INTO `type_event` VALUES (22, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (23, '610611', 'MDF-e cancelado');
INSERT INTO `type_event` VALUES (24, '110140', 'EPEC');
INSERT INTO `type_event` VALUES (25, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (26, '110110', 'Carta de Correcao');
INSERT INTO `type_event` VALUES (27, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (28, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (29, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (30, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (31, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (32, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (33, '110111', 'Cancelamento');
INSERT INTO `type_event` VALUES (34, '110111', 'Cancelamento');
INSERT INTO `type_event` VALUES (35, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (36, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (37, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (38, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (39, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (40, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (41, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (42, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (43, '110111', 'Cancelamento');
INSERT INTO `type_event` VALUES (44, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (45, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (46, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (47, '110111', 'Cancelamento');
INSERT INTO `type_event` VALUES (48, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (49, '110130', 'Comprovante de Entrega da NF-e');
INSERT INTO `type_event` VALUES (50, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (51, '110110', 'Carta de Correcao');
INSERT INTO `type_event` VALUES (52, '110110', 'Carta de Correcao');
INSERT INTO `type_event` VALUES (53, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (54, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (55, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (56, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (57, '610131', 'Cancelamento Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (58, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (59, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (60, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (61, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (62, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (63, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (64, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (65, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (66, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (67, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (68, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (69, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (70, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (71, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (72, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (73, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (74, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (75, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (76, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (77, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (78, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (79, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (80, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (81, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (82, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (83, '210240', 'Operacao nao Realizada');
INSERT INTO `type_event` VALUES (84, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (85, '210210', 'Ciencia da Operacao');
INSERT INTO `type_event` VALUES (86, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (87, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (88, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (89, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (90, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (91, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (92, '210200', 'Confirmacao da Operacao');
INSERT INTO `type_event` VALUES (93, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (94, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (95, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (96, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (97, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (98, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (99, '610130', 'Comprovante de Entrega do CT-e');
INSERT INTO `type_event` VALUES (100, '', '');

-- ----------------------------
-- Table structure for wallet
-- ----------------------------
DROP TABLE IF EXISTS `wallet`;
CREATE TABLE `wallet`  (
  `wallet_id` int NOT NULL AUTO_INCREMENT,
  `wallet_id_company` int NULL DEFAULT NULL,
  `wallet_ide_client` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_ide` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_bank` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_agency` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_post` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_bill` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_bill_dv` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_beneficiary` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_code` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_day_protest` int NULL DEFAULT NULL,
  `wallet_day_devolution` int NULL DEFAULT NULL,
  `wallet_type_penalty` int NULL DEFAULT NULL,
  `wallet_penalty` double(15, 3) NULL DEFAULT NULL,
  `wallet_type_fees` int NULL DEFAULT NULL,
  `wallet_fees` double(15, 3) NULL DEFAULT NULL,
  `wallet_type_discount` int NULL DEFAULT NULL,
  `wallet_inform_discount1` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_value_discount1` double(15, 3) NULL DEFAULT NULL,
  `wallet_day_discount1` int NULL DEFAULT NULL,
  `wallet_inform_discount2` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_value_discount2` double(15, 3) NULL DEFAULT NULL,
  `wallet_day_discount2` int NULL DEFAULT NULL,
  `wallet_inform_discount3` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_value_discount3` double(15, 3) NULL DEFAULT NULL,
  `wallet_day_discount3` int NULL DEFAULT NULL,
  `wallet_inform_discount_anticipated` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_value_discount_anticipated` double(15, 2) NULL DEFAULT NULL,
  `wallet_informative` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_species_document` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_accept` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_token` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_status` int NULL DEFAULT NULL,
  `wallet_trash` int NULL DEFAULT NULL,
  `wallet_variation` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_bb_client_id` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `wallet_bb_client_secret` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `wallet_bb_dev_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_hybrid` int NULL DEFAULT NULL,
  `wallet_certificate` blob NULL,
  `wallet_pass_certificate` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_certificate_key` blob NULL,
  `wallet_token_secret` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_workspace` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_permitir_pagamento_vencimento` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_key_pix` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_aceitar_vencido` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_aceitar_parcial` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `wallet_versao` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`wallet_id`) USING BTREE,
  INDEX `idx_wallet_client`(`wallet_ide_client` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wallet
-- ----------------------------

-- ----------------------------
-- Table structure for webhook
-- ----------------------------
DROP TABLE IF EXISTS `webhook`;
CREATE TABLE `webhook`  (
  `webhook_id` int NOT NULL AUTO_INCREMENT,
  `webhook_id_company` int NULL DEFAULT NULL,
  `webhook_ide_client` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_pix` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_boleto` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_status` int NULL DEFAULT NULL,
  `webhook_trash` int NULL DEFAULT NULL,
  `webhook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_ide` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`webhook_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of webhook
-- ----------------------------

-- ----------------------------
-- Table structure for webhook_dfe
-- ----------------------------
DROP TABLE IF EXISTS `webhook_dfe`;
CREATE TABLE `webhook_dfe`  (
  `webhook_dfe_id` int NOT NULL AUTO_INCREMENT,
  `webhook_dfe_id_company` int NULL DEFAULT NULL,
  `webhook_dfe_ide_client` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_dfe_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `webhook_dfe_lixeira` int NULL DEFAULT NULL,
  `webhook_dfe_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`webhook_dfe_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of webhook_dfe
-- ----------------------------

-- ----------------------------
-- Table structure for webhook_retorno
-- ----------------------------
DROP TABLE IF EXISTS `webhook_retorno`;
CREATE TABLE `webhook_retorno`  (
  `webhook_retorno_id` int NOT NULL AUTO_INCREMENT,
  `webhook_retorno_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`webhook_retorno_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of webhook_retorno
-- ----------------------------

-- ----------------------------
-- Table structure for whatsapp
-- ----------------------------
DROP TABLE IF EXISTS `whatsapp`;
CREATE TABLE `whatsapp`  (
  `whatsapp_id` int NOT NULL AUTO_INCREMENT,
  `whatsapp_id_company` int NULL DEFAULT NULL,
  `whatsapp_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_trash` int NULL DEFAULT NULL,
  `whatsapp_conexao` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`whatsapp_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of whatsapp
-- ----------------------------

-- ----------------------------
-- Table structure for whatsapp_msg
-- ----------------------------
DROP TABLE IF EXISTS `whatsapp_msg`;
CREATE TABLE `whatsapp_msg`  (
  `whatsapp_msg_id` int NOT NULL AUTO_INCREMENT,
  `whatsapp_msg_id_company` int NULL DEFAULT NULL,
  `whatsapp_msg_ide_whatsapp` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_ide` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_numero` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_texto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `whatsapp_msg_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `whatsapp_msg_trash` int NULL DEFAULT NULL,
  `whatsapp_msg_id_msg` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`whatsapp_msg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of whatsapp_msg
-- ----------------------------

-- ----------------------------
-- Table structure for ze_duplo
-- ----------------------------
DROP TABLE IF EXISTS `ze_duplo`;
CREATE TABLE `ze_duplo`  (
  `duplo_id` int NOT NULL AUTO_INCREMENT,
  `duplo_ide_hub_delivery` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `duplo_codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `duplo_status` int NULL DEFAULT NULL,
  PRIMARY KEY (`duplo_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ze_duplo
-- ----------------------------

-- ----------------------------
-- Table structure for ze_itens_pedido
-- ----------------------------
DROP TABLE IF EXISTS `ze_itens_pedido`;
CREATE TABLE `ze_itens_pedido`  (
  `itens_pedido_id` int NOT NULL AUTO_INCREMENT,
  `itens_pedido_id_pedido` int NULL DEFAULT NULL,
  `itens_pedido_id_produto` int NULL DEFAULT NULL,
  `itens_pedido_descricao_produto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `itens_pedido_qtd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `itens_pedido_valor_unitario` double(15, 2) NULL DEFAULT NULL,
  `itens_pedido_valor_total` double(15, 2) NULL DEFAULT NULL,
  `itens_pedido_st` int NULL DEFAULT NULL,
  PRIMARY KEY (`itens_pedido_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ze_itens_pedido
-- ----------------------------

-- ----------------------------
-- Table structure for ze_pedido
-- ----------------------------
DROP TABLE IF EXISTS `ze_pedido`;
CREATE TABLE `ze_pedido`  (
  `pedido_id` int NOT NULL AUTO_INCREMENT,
  `pedido_ide` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
  `pedido_data` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
  `pedido_hora` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '',
  `pedido_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_email_entregador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_valor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_pagamento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_cupom` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_desconto` double(15, 2) NULL DEFAULT NULL,
  `pedido_st` int NULL DEFAULT NULL,
  `pedido_st_delivery` int NULL DEFAULT NULL,
  `pedido_frete` double(15, 2) NULL DEFAULT NULL,
  `pedido_st_validacao` int NULL DEFAULT NULL,
  `pedido_nome_cliente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_cpf_cliente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_endereco_rota` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_endereco_complemento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_endereco_cidade_uf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_endereco_cep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_endereco_bairro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_taxa_conveniencia` double(15, 2) NULL DEFAULT NULL,
  `pedido_troco_para` double(15, 2) NULL DEFAULT NULL,
  `pedido_troco` double(15, 2) NULL DEFAULT NULL,
  `pedido_data_hora_captura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pedido_aceitar` int NULL DEFAULT NULL,
  PRIMARY KEY (`pedido_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ze_pedido
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
SQL;

try {
    $pdo->exec($sql);
    echo "✅ Script SQL executado com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro ao executar SQL: " . $e->getMessage();
}

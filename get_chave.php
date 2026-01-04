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

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE `eventos`  (
  `eventos_id` int NOT NULL AUTO_INCREMENT,
  `eventos_id_client` varchar(35) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_chave` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_code_evento` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_desc_evento` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_data` datetime NULL DEFAULT NULL,
  `eventos_prot` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `eventos_file` longblob NULL,
  `eventos_ide` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
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
  `finance_name_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_address_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_number_pagador` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_district_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_complement_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_city_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_state_pagador` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_zip_pagador` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_telefone_pagador` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_email_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
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
  `finance_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_payload_hybrid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_txtId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `finance_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
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
  `hub_delivery_clientid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_secretid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `hub_delivery_status` int NULL DEFAULT NULL,
  `hub_delivery_trash` int NULL DEFAULT NULL,
  `hub_delivery_auth` int NULL DEFAULT NULL,
  `hub_delivery_senha_mail` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
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
  `impressora_ide` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_marca` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `impressora_modelo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `lote_certificate_password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_date_init` datetime NULL DEFAULT NULL,
  `lote_date_finish` datetime NULL DEFAULT NULL,
  `lote_file` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
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
  `lote_boleto_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `lote_boleto_ret_linha_digitavel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `lote_item_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lote_item_status` int NULL DEFAULT NULL,
  `lote_item_log` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
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
  `monitor_nome_arquivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `monitor_xml_descricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `monitor_xml_webhook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `nfe_ide_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `nfe_cobranca_fatura_numero` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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
  `nfe_contingencia_motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
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


SET FOREIGN_KEY_CHECKS = 1;
SQL;

try {
    $pdo->exec($sql);
    echo "✅ Script SQL executado com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro ao executar SQL: " . $e->getMessage();
}

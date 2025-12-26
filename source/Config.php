<?php

/**
 * URL DO SISTEMA API
 */


const URL_BASE = "https://fisds.app";
//const URL_BASE = "http://localhost:8080/newproject/apisimples";

const JWT_KEY = "D98@75@89";

const CLIENT_ID_PLUGGY = '0285809bcb46a5f59';
const CLIENT_SECRET_PLUGGY = '934d377b-ba-c294e7dd912a';

const TOKEN_CNPJJA = "b8c33f4c-a1fb-5d7628d7-470d-4855-80c1-2f682ca3d30c";
//const TOKEN_CNPJJA = "99acb21d-e98d-49d5-a22d-2d9c-2b0c6e27-51da-4713-bdc6-e320df2a72c9";
const URL_CNPJJA = "https://api.cnpja.com/office";
const URL_CNPJJA_IE = "https://api.cnpja.com";

const URL_CEP = "https://opencep.com/v1";


//const URL_WHATSAPP = "http://139.59.143333";
//const URL_WHATSAPP = "http://164.175:3333";
const URL_WHATSAPP = "http://apiwhbr";
const ADMIN_KEY_WHATSAPP = "ce5b72aa12cbc9aa49e09f3ba102";

const URL_WEBHOOK_SANTANDER = "https://webhook.sic-4b6b-4ffc-9c41-3bc920c8952d";



const SITE = [
    "name" => "ADMINISTRADOR DO SISTEMA DE ECOMMERCE",
    "desc" => "",
    "domain" => "integraphp.com",
    "locale" => "pt-br",
    "root" => URL_BASE
];

const URL_ZE = 'http://1api';

const TOKEN_SENHA = 'Um9x5';

const TOKEN_ASAAS = '$aact_YTU5YTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAzNjQ2MzQ6OiRhYWNoXzcyMWEzMmFjLTlhMjMtNGQwNy05ODhhLTk3MmE5ZjQxMDcyMA==';

const URL_WEBHOOK_INTER = 'https://api.apisimples.com.br/v1/webhook/pix_inter.php';

//5c09700f-235f-30c7-8f7c-f11630cad8b8
//9a1bec84-4234-4290-91d1-68a76a6c45c8

const VIEW_CIP = '<td colspan="2" >
    <div class="titulo">Uso do banco</div>
    <div class="conteudo"><?php echo $uso_banco ?></div>
</td>';


/**
 * DADOS DE ACESSO DSN
 */
const DSN = 'mysql:host=a8484c4s4cgoskk0g8scsk08;port=3306;dbname=b2x_sefaz';
/**
 * USUÁRIO DO BANCO DE DADOS
 */
const USER = 'b2x_sefaz';
/**
 * SENHA DO BANCO DE DADOS
 */
const PASS = '7j8pMMnbypXjvUPx58c9gBFCrY9bSMewrvJSyARBadSQ3pRNktfJjjJuntMmYoix';


function url(string $uri = null): string {
    if ($uri) {
        return URL_BASE . "/{$uri}";
    }

    return URL_BASE;
}

<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=dfeCiencia.php'>";
require_once '../vendor/autoload.php';

use Source\Conn\DataLayer;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

function createTemp($base64)
{
    $decode = base64_decode($base64);
    $nameCertificate = md5(date('Y-m-dH:i:s') . rand(1000, 9999)) . '.pfx';
    file_put_contents(__DIR__ . '/../shared/certificate/' . $nameCertificate, $decode);

    return $nameCertificate;
}

$db = new DataLayer();

$dateH = date('H');

$resultCron = $db->db()->from('doc_res')
    ->join('client', function ($join) {
        $join->on('client_ide', 'doc_res_id_client');
    })
    ->where('doc_res_status')->is(0)
    ->limit(1)
    ->select()
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
        $arr = [
            "atualizacao" => date('Y-m-d H:i:s'),
            "tpAmb" => intval(1),
            "razaosocial" => $rCron->client_name,
            "cnpj" => $rCron->client_doc,
            "siglaUF" => $rCron->client_address_state,
            "schemes" => "PL_009_V4",
            "versao" => '4.00',
            "tokenIBPT" => "",
            "CSC" => "",
            "CSCid" => "",
            "proxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];


        try {
            $configJson = json_encode($arr);

            $nameCertificate = createTemp($rCron->client_dfe_certificate);
            $pfxContent = file_get_contents(realpath(__DIR__ . '/../shared/certificate/' . $nameCertificate));

            $tools = new Tools($configJson, Certificate::readPfx($pfxContent, $rCron->client_dfe_password_certificate));
            $tools->model(55);

            $chNFe = $rCron->doc_res_chave;
            $tpEvento = '210210';
            $xJust = '';
            $nSeqEvento = 2;

            $response = $tools->sefazManifesta($chNFe, $tpEvento, $xJust = '', $nSeqEvento = 1);
            $st = new Standardize($response);
            $stdRes = $st->toStd();
            $arr = $st->toArray();

            if ($arr['retEvento']['infEvento']['cStat'] == '596') {
                $chNFe = $rCron->doc_res_chave;
                $tpEvento = '210200';
                $xJust = '';
                $nSeqEvento = 2;

                $response = $tools->sefazManifesta($chNFe, $tpEvento, $xJust = '', $nSeqEvento = 1);
                $st = new Standardize($response);
                $stdRes = $st->toStd();
                $arr = $st->toArray();

                if ($arr['retEvento']['infEvento']['cStat'] == '135') {
                    $form_doc['doc_res_status'] = '1';
                    $db->db()->update('doc_res')->where('doc_res_id')->is($rCron->doc_res_id)->set($form_doc);

                    $form_doc_completo['doc_status_manifestacao'] = '2';
                    $db->db()->update('doc')->where('doc_id')->is($rCron->doc_res_chave)->set($form_doc_completo);
                }
            }

            if ($arr['retEvento']['infEvento']['cStat'] == '573') {
                $form_doc['doc_res_status'] = '1';
                $db->db()->update('doc_res')->where('doc_res_id')->is($rCron->doc_res_id)->set($form_doc);
            }
            if ($arr['retEvento']['infEvento']['cStat'] == '650') {
                $form_doc['doc_res_status'] = '1';
                $db->db()->update('doc_res')->where('doc_res_id')->is($rCron->doc_res_id)->set($form_doc);

                $form_doc_completo['doc_status'] = '1';
                $db->db()->update('doc')->where('doc_id')->is($rCron->doc_res_chave)->set($form_doc_completo);
            }

            if ($arr['retEvento']['infEvento']['cStat'] == '655') {
                $form_doc['doc_res_status'] = '1';
                $db->db()->update('doc_res')->where('doc_res_id')->is($rCron->doc_res_id)->set($form_doc);

                $form_doc_completo['doc_status_manifestacao'] = '2';
                $db->db()->update('doc')->where('doc_id')->is($rCron->doc_res_chave)->set($form_doc_completo);
            }

            echo '<pre>';
            print_r($arr);
            $json = $st->toJson();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

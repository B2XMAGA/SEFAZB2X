<?php
$get = addslashes($_GET['n']);
$novo = $get + 1;
echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=dfeSearch.php?n=$novo'>";
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
require_once '../vendor/autoload.php';

use Source\Conn\DataLayer;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

function createTemp($base64)
{
    $decode = base64_decode($base64);
    $nameCertificate = md5(date('Y-m-dH:i:s').rand(1000,9999)).'.pfx';
    file_put_contents(__DIR__.'/../shared/certificate/'.$nameCertificate,$decode);

    return $nameCertificate;
}

$db = new DataLayer();

$dateH = date('H');


//echo $dateH;

$resultCron = $db->db()->from('client')
    ->where('client_dfe_use')->is(1)
    ->andWhere('client_status')->is(0)
	->orderBy('client_id')
    ->limit(1)
	->offset($get)
    ->select()
    ->all();
print_r($resultCron);	

if($resultCron){
    foreach($resultCron as $rCron){
		echo $rCron->client_id.'<br />';

        $start_date = new \DateTime($rCron->client_dfe_date_consult);
        $since_start = $start_date->diff(new \DateTime(date('Y-m-d H:i:s')));
        $minutes = $since_start->days * 24 * 60;
        $minutes += $since_start->h * 60;
        $minutes += $since_start->i;

        if($minutes < '60'){
            $jSON['msg'] = 'Intervalo de busca inoperante!';
            $jSON['type'] = 'ok';
            $jSON['title'] = 'ok';

            $updateErroNovoInt['client_dfe_ult_event_code'] = '001';
            $updateErroNovoInt['client_dfe_ult_event_desc'] = 'Intervalo de busca inoperante!';
            //$db->db()->update('client')->where('client_id')->is($rCron->client_id)->set($updateErroNovoInt);
            echo json_encode($jSON);
        }else{
            $readHours = $db->db()->from('dfe_hours')
                ->where('dfe_hours_ide_client')->is($rCron->client_ide)
                ->andWhere('dfe_hours_key')->is($dateH)
                ->select()
                ->all();

            //if($readHours){

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

                $configJson = json_encode($arr);

				try {
					$nameCertificate = createTemp($rCron->client_dfe_certificate);
					$pfxContent = file_get_contents(realpath(__DIR__.'/../shared/certificate/'.$nameCertificate));

					$tools = new Tools($configJson, Certificate::readPfx($pfxContent, $rCron->client_dfe_password_certificate));


					$tools->model(55);
					$tools->setEnvironment(1);

					$ultNSU     = $rCron->client_dfe_ult_nsu;
					$maxNSU     = $ultNSU;
					$loopLimit  = 10;
					$iCount     = 0;


					while ($ultNSU <= $maxNSU) {
						$iCount++;
						if ($iCount >= $loopLimit) {
							break;
						}
						try {
							$resp = $tools->sefazDistDFe($ultNSU);
							echo '<pre>';
							print_r($resp);
						} catch (\Exception $e) {
                            $updateErro['client_dfe_ult_event_code'] = '000';
                            $updateErro['client_dfe_ult_event_desc'] = $e->getMessage();
                            $updateErro['client_dfe_ult_event_datetime'] = date('Y-m-d H:i:s');
                            $updateErro['client_dfe_date_consult'] = date('Y-m-d H:i:s');
                            $db->db()->update('client')->where('client_id')->is($rCron->client_id)->set($updateErro);
							break;
						}

						$dom = new \DOMDocument();
						$dom->loadXML($resp);
						$node       = $dom->getElementsByTagName('retDistDFeInt')->item(0);
						$tpAmb      = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
						$verAplic   = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
						$cStat      = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
						$xMotivo    = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
						$dhResp     = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
						$ultNSU     = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
						$maxNSU     = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
						$lote       = $node->getElementsByTagName('loteDistDFeInt')->item(0);
						if (in_array($cStat, ['137', '656'])) {
                            $updateErroNovo['client_dfe_ult_event_code'] = $cStat;
                            $updateErroNovo['client_dfe_ult_event_desc'] = $xMotivo;
                            $updateErroNovo['client_dfe_ult_event_datetime'] = date('Y-m-d H:i:s');
                            $updateErroNovo['client_dfe_date_consult'] = date('Y-m-d H:i:s');
                            $db->db()->update('client')->where('client_id')->is($rCron->client_id)->set($updateErroNovo);
							echo 'Consumo indevido';
							break;
						}
						if (empty($lote)) {
							continue;
						}

						$docs = $lote->getElementsByTagName('docZip');
						foreach ($docs as $doc) {
							$numnsu = $doc->getAttribute('NSU');
							$schema = $doc->getAttribute('schema');

							$content    = gzdecode(base64_decode($doc->nodeValue));
							$tipo       = substr($schema, 0, 6);


							$XML = simplexml_load_string($content);

							$postNSU['dfe_ide_client']  = $rCron->client_ide;
							$postNSU['dfe_nsu']         = $numnsu;
							$postNSU['dfe_schema']      = $schema;
							$postNSU['dfe_doc']         = base64_encode($content);
							$postNSU['dfe_status']      = '0';

							echo '<pre>';
							print_r($XML);
							$db->db()->insert($postNSU)->into('dfe');
						}
						if ($ultNSU == $maxNSU) {
							$updateNSU['client_dfe_ult_nsu'] = $maxNSU;
							$updateNSU['client_dfe_date_consult'] = date('Y-m-d H:i:s');
                            $updateNSU['client_dfe_ult_event_datetime'] = date('Y-m-d H:i:s');
                            $updateNSU['client_dfe_ult_event_code'] = '201';
                            $updateNSU['client_dfe_ult_event_desc'] = 'Documento(s) encontrado(s)';
							$db->db()->update('client')->where('client_id')->is($rCron->client_id)->set($updateNSU);
							break;
						}
						sleep(2);
					}
				} catch (\Exception $e) {
                    echo '<pre>';
                    print_r($e);
                    echo '</pre>';
                    $updateErroAnterior['client_dfe_ult_event_code'] = '000';
                    $updateErroAnterior['client_dfe_ult_event_desc'] = $e->getMessage();
                    $updateErroAnterior['client_dfe_ult_event_datetime'] = date('Y-m-d H:i:s');
                    $updateErroAnterior['client_dfe_date_consult'] = date('Y-m-d H:i:s');
                    $db->db()->update('client')->where('client_id')->is($rCron->client_id)->set($updateErroAnterior);
					break;
				}
            //}
            echo 'ERRO SEM HORA CADASTRADA';
        }



    }
}




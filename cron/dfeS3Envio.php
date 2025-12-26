<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
//echo "<meta HTTP-EQUIV='refresh' CONTENT='10;URL=dfeS3Envio.php'>";
require_once '../vendor/autoload.php';


$accessKey = 'DO801C8RKMK43DU98ZHK';
$secretKey = 'p/VmfyseNHkdNizPkBohuO7h6sFyYp1WD4CqmZOzuGw';
$region = 'ams3'; // Exemplo: nyc3, sfo3, etc.
$bucket = 's3xmls';


$inicio = microtime(true);

use Source\Conn\DataLayer;
use Source\Facades\KS3DigitalOcean;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

function createTemp($base64)
{
    $decode = base64_decode($base64);
    $nameCertificate = md5(date('Y-m-dH:i:s') . rand(1000, 9999)) . '.pfx';
    file_put_contents(__DIR__ . '/../shared/certificate/' . $nameCertificate, $decode);

    return $nameCertificate;
}

$db = new DataLayer();

$dateH = date('H');

$resultCron = $db->db()->from('dfe')
    ->where('dfe_status')->is(0)
	->andWhere('dfe_md5')->notNull()
	->andWhere('dfe_url_s3')->isNull()
    ->limit(50)
    ->select(['dfe_id'])
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
		$result = $db->db()->from('dfe')
			->where('dfe_id')->is($rCron->dfe_id)
			->limit(1)
			->select()
			->all();
		if($result){
			foreach($result as $r){
				$ks3 = new KS3DigitalOcean($accessKey, $secretKey, $region, $bucket);
				
				if ($ks3->uploadXMLBase64($r->dfe_doc, $r->dfe_ide_client . '/'.$r->dfe_md5.'.xml')) {
					$form_doc['dfe_url_s3'] = 1;
				
					$db->db()->update('dfe')->where('dfe_id')->is($r->dfe_id)->set($form_doc);
				} else {
					echo 'ERRO';
				}
			}
		}
    }
}

$tempo = microtime(true) - $inicio;
echo "Tempo da requisição: " . number_format($tempo, 4) . " segundos\n";

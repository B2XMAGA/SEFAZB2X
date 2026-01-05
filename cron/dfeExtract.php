<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=dfeExtract.php'>";
require_once '../vendor/autoload.php';
use Source\Conn\DataLayer;
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
$randOffset = rand(0,50);
$resultCron = $db->db()->from('dfe')
    ->where('dfe_status')->is(0)
    ->limit(5)
    ->select()
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
        echo '<pre>';
		echo $randOffset;
		print_r($rCron);

        $XMLBase64 = base64_decode($rCron->dfe_doc);
        $XML = simplexml_load_string($XMLBase64);
        print_r($XML);


        if ($rCron->dfe_schema == 'resNFe_v1.01.xsd') {

            $resultNFeResumes = $db->db()->from('doc_res')
                ->where('doc_res_id_client')->is($rCron->dfe_ide_client)
                ->andWhere('doc_res_chave')->is($XML->chNFe)
                ->limit(1)
                ->select()
                ->all();

            if (!$resultNFeResumes) {
                $form_nfe_resume['doc_res_id_client'] = $rCron->dfe_ide_client;
                $form_nfe_resume['doc_res_chave'] = $XML->chNFe;
                $form_nfe_resume['doc_res_doc'] = $XML->CNPJ;
                if ($XML->CPF) {
                    $form_nfe_resume['doc_res_doc'] = $XML->CPF;
                }
                $form_nfe_resume['doc_res_name'] = $XML->xNome;
                $form_nfe_resume['doc_res_ie'] = $XML->IE;
                $explode_data_emi = explode('T', $XML->dhEmi);
                $form_nfe_resume['doc_res_date_emi'] = $explode_data_emi['0'] . ' ' . substr($explode_data_emi['1'], 0, 8);
                $form_nfe_resume['doc_res_amount'] = $XML->vNF;
                $form_nfe_resume['doc_res_dig'] = $XML->digVal;
                $explode_data_rec = explode('T', $XML->dhRecbto);
                $form_nfe_resume['doc_res_date_recbto'] = $explode_data_rec['0'] . ' ' . substr($explode_data_rec['1'], 0, 8);
                $form_nfe_resume['doc_res_num_prot'] = $XML->nProt;
                $form_nfe_resume['doc_res_status'] = '0';
                $form_nfe_resume['doc_res_file'] = $rCron->dfe_doc;

                $db->db()->insert($form_nfe_resume)->into('doc_res');
                $form_doc['dfe_status'] = '1';
                $db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
                echo 'ok res';
            } else {
                $form_doc['dfe_status'] = '2';
                $db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
            }
        }

        if ($rCron->dfe_schema == 'procNFe_v4.00.xsd') {
            $form_nfe['doc_id_client'] = $rCron->dfe_ide_client;
            $form_nfe['doc_mod'] = '55';
            $form_nfe['doc_code'] = $XML->NFe->infNFe->ide->cNF;
            $form_nfe['doc_nat_op'] = $XML->NFe->infNFe->ide->natOp;
            $form_nfe['doc_serie'] = $XML->NFe->infNFe->ide->serie;
            $form_nfe['doc_num'] = $XML->NFe->infNFe->ide->nNF;
            $explode_data_ini = explode('T', $XML->NFe->infNFe->ide->dhEmi);
            $form_nfe['doc_date_emi'] = $explode_data_ini['0'] . ' ' . substr($explode_data_ini['1'], 0, 8);
            if (isset($XML->NFe->infNFe->ide->dhSaiEnt)) {
                $explode_data_sai = explode('T', $XML->NFe->infNFe->ide->dhSaiEnt);
                $form_nfe['doc_date_sai'] = $explode_data_sai['0'] . ' ' . substr($explode_data_sai['1'], 0, 8);
            } else {
                $form_nfe['doc_date_sai'] = $explode_data_ini['0'] . ' ' . substr($explode_data_ini['1'], 0, 8);
            }
            if ($XML->NFe->infNFe->emit->CNPJ) {
                $form_nfe['doc_emit_documento'] = $XML->NFe->infNFe->emit->CNPJ;
            } else {
                $form_nfe['doc_emit_documento'] = $XML->NFe->infNFe->emit->CPF;
            }
            $form_nfe['doc_emit_nome'] = $XML->NFe->infNFe->emit->xNome;
            $form_nfe['doc_emit_fantasia'] = $XML->NFe->infNFe->emit->xFant;
            $form_nfe['doc_emit_ie'] = $XML->NFe->infNFe->emit->IE;
            if ($XML->NFe->infNFe->dest->CNPJ) {
                $form_nfe['doc_dest_documento'] = $XML->NFe->infNFe->dest->CNPJ;
            } else {
                $form_nfe['doc_dest_documento'] = $XML->NFe->infNFe->dest->CPF;
            }
            $form_nfe['doc_dest_nome'] = $XML->NFe->infNFe->dest->xNome;
            $form_nfe['doc_dest_ie'] = $XML->NFe->infNFe->dest->IE;
            $form_nfe['doc_valor'] = isset($XML->NFe->infNFe->total->ICMSTot->vNF) ? $XML->NFe->infNFe->total->ICMSTot->vNF : '0.00';
            $form_nfe['doc_valor_trib'] = isset($XML->NFe->infNFe->total->ICMSTot->vTotTrib) ? $XML->NFe->infNFe->total->ICMSTot->vTotTrib : '0.00';
            $form_nfe['doc_valor_base_icms'] = isset($XML->NFe->infNFe->total->ICMSTot->vBC) ? $XML->NFe->infNFe->total->ICMSTot->vBC : '0.00';
            $form_nfe['doc_valor_icms'] = isset($XML->NFe->infNFe->total->ICMSTot->vICMS) ? $XML->NFe->infNFe->total->ICMSTot->vICMS : '0.00';
            $form_nfe['doc_valor_frete'] = isset($XML->NFe->infNFe->total->ICMSTot->vFrete) ? $XML->NFe->infNFe->total->ICMSTot->vFrete : '0.00';
            $form_nfe['doc_valor_seguro'] = isset($XML->NFe->infNFe->total->ICMSTot->vSeg) ? $XML->NFe->infNFe->total->ICMSTot->vSeg : '0.00';
            $form_nfe['doc_valor_desconto'] = isset($XML->NFe->infNFe->total->ICMSTot->vDesc) ? $XML->NFe->infNFe->total->ICMSTot->vDesc : '0.00';

            $form_nfe['doc_uf_inicio'] = $XML->NFe->infNFe->emit->enderEmit->UF;
            $form_nfe['doc_uf_final'] = $XML->NFe->infNFe->dest->enderDest->UF;

            $form_nfe['doc_status'] = '0';
            $form_nfe['doc_status_download'] = '1';
            $form_nfe['doc_status_manifestacao'] = '1';
            $form_nfe['doc_chave'] = $XML->protNFe->infProt->chNFe;
            $form_nfe['doc_file'] = $rCron->dfe_doc;
            $form_nfe['doc_tipo'] = 'dest';


            try {
                $resultDoc = $db->db()->from('doc')
                    ->where('doc_id_client')->is($rCron->dfe_ide_client)
                    ->andWhere('doc_chave')->is($form_nfe['doc_chave'])
                    ->limit(1)
                    ->select()
                    ->all();

                if (!$resultDoc) {
                    echo '<pre>';
                    print_r($form_nfe);
                    $db->db()->insert($form_nfe)->into('doc');
                }
                $form_doc['dfe_status'] = '1';
                $db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
                echo 'ok proc';
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        if ($rCron->dfe_schema == 'resEvento_v1.01.xsd') {
            echo 'resumo evento';
            $resultTypeEvent = $db->db()->from('type_event')
                ->where('type_event_code')->is($XML->tpEvento)
                ->limit(1)
                ->select()
                ->all();

            if (!$resultTypeEvent) {
                $form_type_event['type_event_code'] = $XML->tpEvento;
                $form_type_event['type_event_description'] = $XML->xEvento;
                $db->db()->insert($form_type_event)->into('type_event');
            }


            $form_evento['eventos_id_client'] = $rCron->dfe_ide_client;
            $form_evento['eventos_chave'] = $XML->chNFe;
            $form_evento['eventos_code_evento'] = $XML->tpEvento;
            $form_evento['eventos_desc_evento'] = $XML->xEvento;
            $explode_data = explode('T', $XML->dhEvento);
            $form_evento['eventos_data'] = $explode_data['0'] . '-' . substr($explode_data['1'], 0, 8);
            $form_evento['eventos_prot'] = $XML->nProt;
            $form_evento['eventos_file'] = $rCron->dfe_doc;


            $resultEventOne = $db->db()->from('eventos')
                ->where('eventos_id_client')->is($rCron->dfe_ide_client)
                ->andWhere('eventos_prot')->is($XML->nProt)
                ->limit(1)
                ->select()
                ->all();

            try {
                if (!$resultEventOne) {
                    $db->db()->insert($form_evento)->into('eventos');
                }
                $form_doc['dfe_status'] = '1';
                $db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            
            echo 'ok res evento';
        }

        if ($rCron->dfe_schema == 'procEventoNFe_v1.00.xsd') {
            $resultTypeEvent = $db->db()->from('type_event')
                ->where('type_event_code')->is($XML->evento->infEvento->tpEvento)
                ->limit(1)
                ->select()
                ->all();
            if (!$resultTypeEvent) {
                $form_tipo_evento['type_event_code'] = $XML->evento->infEvento->tpEvento;
                $form_tipo_evento['type_event_description'] = $XML->evento->infEvento->detEvento->descEvento;
                $db->db()->insert($form_tipo_evento)->into('type_event');
            }

            $form_evento['eventos_id_client'] = $rCron->dfe_ide_client;
            $form_evento['eventos_chave'] = $XML->evento->infEvento->chNFe;
            $form_evento['eventos_code_evento'] = $XML->evento->infEvento->tpEvento;
            $form_evento['eventos_desc_evento'] = $XML->evento->infEvento->detEvento->descEvento;
            $explode_data = explode('T', $XML->evento->infEvento->dhEvento);
            $form_evento['eventos_data'] = $explode_data['0'] . '-' . substr($explode_data['1'], 0, 8);
            $form_evento['eventos_prot'] = $XML->evento->infEvento->detEvento->nProt;
            $form_evento['eventos_file'] = $rCron->dfe_doc;

            $resultEventTwo = $db->db()->from('eventos')
                ->where('eventos_id_client')->is($rCron->dfe_ide_client)
                ->andWhere('eventos_prot')->is($XML->evento->infEvento->detEvento->nProt)
                ->limit(1)
                ->select()
                ->all();

            if (!$resultEventTwo) {
                $db->db()->insert($form_evento)->into('eventos');
            }

            $form_doc['dfe_status'] = '1';
            $db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
            echo 'ok proc evento';
        }
    }
}else{
    echo 'Nenhum documento encontrado';
}

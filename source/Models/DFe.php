<?php

namespace Source\Models;

use NFePHP\DA\NFe\Danfce;
use NFePHP\DA\NFe\Danfe;
use Source\Conn\DataLayer;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

class DFe extends DataLayer
{

    public function uploadCertificate()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();


        $fieldsRequired = ['certificate', 'passCertificate'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){

            $postCertificate = [
                'client_dfe_certificate' => $postVars['certificate'],
                'client_dfe_password_certificate' => $postVars['passCertificate'],
                'client_dfe_use' => '1',
            ];

            $pfxContent = base64_decode($postVars['certificate']);
            $certInfo = array();
            if (openssl_pkcs12_read($pfxContent, $certInfo, $postVars['passCertificate'])) {
                $cert = openssl_x509_parse($certInfo['cert']);
                $postCertificate['client_status_certificado'] = $cert['subject']['CN'].' - VALIDADE: '.date('Y-m-d', $cert['validTo_time_t']);
                $postCertificate['client_validade_certificado'] = date('Y-m-d', $cert['validTo_time_t']);

                $currentDate = time();
                $validTo = $cert['validTo_time_t'];
                if ($currentDate > $validTo) {
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Certificado está vencido: venceu em ".date('d/m/Y', $validTo)
                    )->back(["count" => 0]);
                    return;
                }
            }else{
                $this->call(
                    '401',
                    'Ops',
                    '',
                    "ops",
                    "Certificado não conseguimos ler"
                )->back(["count" => 0]);
                return;
            }


            $result = $this->db()->update('client')->where('client_ide')->is($getHeaders['ideClient'])->andWhere('client_id_company')->is($idReg)->set($postCertificate);
            if($result == '0' || $result == '1'){
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back([]);
                return;
            }

            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Tivemos um problema, tente novamente mais tarde"
            )->back(["count" => 0]);
            return;

        }
    }

    public function setHoursConsult()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $fieldsRequired = ['hours'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){
            if(count($postVars['hours']) <= 0){
                $this->call(
                    '400',
                    'Ops',
                    '',
                    "ops",
                    "Não existem parametros para serem considerados"
                )->back(["count" => 0]);
                return;
            }

            $this->db()->from('dfe_hours')->where('dfe_hours_ide_client')->is(clearDoc($getHeaders['ideClient']))->delete();
            for($x=0;$x<count($postVars['hours']);$x++){

                $postDFe['dfe_hours_ide_client'] = clearDoc($getHeaders['ideClient']);
                $postDFe['dfe_hours_key']        = $postVars['hours'][$x];
                $this->db()->insert($postDFe)->into('dfe_hours');
            }

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back([]);
            return;

        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function dfeDocsManifesta()
    {

        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $fieldsRequired = ['typeEvent', 'chave', 'justification'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->join('doc', function($join){
                $join->on('doc_id_client', 'client_ide');
            })
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
            ->andWhere('doc_chave')->is($postVars['chave'])
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){

            foreach($result as $rDFe){

                $arr = [
                    "atualizacao" => date('Y-m-d H:i:s'),
                    "tpAmb" => intval(1),
                    "razaosocial" => $rDFe->client_name,
                    "cnpj" => $rDFe->client_doc,
                    "siglaUF" => $rDFe->client_address_state,
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

                    $nameCertificate = $this->createTemp($rDFe->client_dfe_certificate);
                    $pfxContent = file_get_contents(realpath(__DIR__.'/../../shared/certificate/'.$nameCertificate));

                    $tools = new Tools($configJson, Certificate::readPfx($pfxContent, $rDFe->client_dfe_password_certificate));
                    $tools->model(55);

                    $chNFe = $rDFe->doc_chave;
                    $tpEvento = $postVars['typeEvent'];
                    $xJust = $postVars['justification'];
                    if($tpEvento == '210210'){
                        $nSeqEvento = 1;
                    }else{
                        $nSeqEvento = 2;
                    }


                    $response = $tools->sefazManifesta($chNFe,$tpEvento,$xJust = '',$nSeqEvento);
                    $st = new Standardize($response);
                    $stdRes = $st->toStd();
                    $arr = $st->toArray();

                    if($arr['retEvento']['infEvento']['cStat'] == '135'){
                        $explode_data_rec = explode('T', $arr['retEvento']['infEvento']['dhRegEvento']);
                        $dateTime = $explode_data_rec['0'].' '.substr($explode_data_rec['1'],0,8);

                        if($postVars['typeEvent'] == '210200'){
                            $updateNfeDoc['doc_status_manifestacao'] = '2';
                        }elseif($postVars['typeEvent'] == '210210'){
                            $updateNfeDoc['doc_status_manifestacao'] = '1';
                        }elseif($postVars['typeEvent'] == '210220'){
                            $updateNfeDoc['doc_status_manifestacao'] = '4';
                        }elseif($postVars['typeEvent'] == '210240'){
                            $updateNfeDoc['doc_status_manifestacao'] = '3';
                        }
                        $this->db()->update('doc')->where('doc_id')->is($rDFe->doc_id)->set($updateNfeDoc);
                        $this->call(
                            '200',
                            'Sucesso',
                            '',
                            "ok",
                            "Operação realizada com sucesso"
                        )->back(["cstat" => $arr['retEvento']['infEvento']['cStat'], "motivo" => $arr['retEvento']['infEvento']['xMotivo'], "dataEvento" => $dateTime, "protocolo" => $arr['retEvento']['infEvento']['nProt']]);
                        return;
                    }

                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Tivemos um problema: ".$arr['retEvento']['infEvento']['xMotivo']
                    )->back(["count" => 0]);
                    return;
                } catch (\Exception $e) {
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Tivemos um erro: ".$e->getMessage()
                    )->back(["count" => 0]);
                    return;
                }

            }

        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Não encontrei o documento informado"
        )->back(["count" => 0]);
        return;
    }

    public function uploadXML()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $fieldsRequired = ['tipoDoc', 'documento', 'tipo'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){
            foreach($result as $rDFe){

                $arrTipoAceito = ["destinataria", "emitida"];

                if(!in_array($postVars['tipo'], $arrTipoAceito)){
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Tipo de nota não aceita, apenas destinataria e emitida são aceitas"
                    )->back(["count" => 0]);
                    return;
                }

                $arrTipoDocAceito = ["nfe", "nfce"];

                if(!in_array($postVars['tipoDoc'], $arrTipoDocAceito)){
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Por enquanto são aceitos apenas: nfe, nfce"
                    )->back(["count" => 0]);
                    return;
                }

                $decodeXML = base64_decode($postVars['documento']);

                if ($decodeXML === false) {
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Impossível ler o arquivo informado"
                    )->back(["count" => 0]);
                    return;
                }

                try{
                    $XML = new \SimpleXMLElement($decodeXML);

                    $form_nfe['doc_id_client'] = $rDFe->client_ide;
                    if($postVars['tipoDoc'] == 'nfe'){
                        $form_nfe['doc_mod'] = '55';
                    }elseif($postVars['tipoDoc'] == 'nfce'){
                        $form_nfe['doc_mod'] = '65';
                    }

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
                    $form_nfe['doc_file'] = $postVars['documento'];

                    if($postVars['tipo'] == 'destinataria'){
                        $form_nfe['doc_tipo'] = 'dest';
                    }else{
                        $form_nfe['doc_tipo'] = 'emit';
                    }


                    try {
                        $resultDoc = $this->db()->from('doc')
                            ->where('doc_id_client')->is($rDFe->client_ide)
                            ->andWhere('doc_chave')->is($form_nfe['doc_chave'])
                            ->limit(1)
                            ->select()
                            ->all();

                        if (!$resultDoc) {
                            $this->db()->insert($form_nfe)->into('doc');
                            $this->call(
                                '200',
                                'Sucesso',
                                '',
                                "ok",
                                "Operação realizada com sucesso"
                            )->back(["chave" => $form_nfe['doc_chave']]);
                            return;
                        }

                        $this->call(
                            '401',
                            'Ops',
                            '',
                            "ops",
                            "Erro ao processar o documento, ele já existe na sua base de dados"
                        )->back(["count" => 0]);
                        return;
                    } catch (\Exception $e) {
                        $this->call(
                            '401',
                            'Ops',
                            '',
                            "ops",
                            "Erro ao processar o documento, verifique as informações e tente novamente mais tarde"
                        )->back(["count" => 0]);
                        return;
                    }
                }catch (Exception $e) {
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Erro ao carregar o XML"
                    )->back(["count" => 0]);
                    return;
                }

            }
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Não encontrei o client informado"
        )->back(["count" => 0]);
        return;
    }

    public function uploadXMLZIP()
    {

        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();



        try{
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmls'])) {
                $uploadedFile = $_FILES['xmls']['tmp_name'];

                $tipoDoc = addslashes($_POST['tipoDoc']);
                $tipo = addslashes($_POST['tipo']);

                $result = $this->db()->from('client')
                    ->where('client_id_company')->is($idReg)
                    ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
                    ->orderBy('client_id', 'DESC')
                    ->limit('1')
                    ->select()
                    ->all();

                if(!$result){
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Client não localizado na sua base"
                    )->back(["count" => 0]);
                    return;
                }

                if($result) {
                    foreach ($result as $rDFe) {

                        $ideClient = $rDFe->client_ide;
                        $arrTipoAceito = ["destinataria", "emitida"];

                        if (!in_array($tipo, $arrTipoAceito)) {
                            $this->call(
                                '401',
                                'Ops',
                                '',
                                "ops",
                                "Tipo de nota não aceita, apenas destinataria e emitida são aceitas"
                            )->back(["count" => 0]);
                            return;
                        }

                        $arrTipoDocAceito = ["nfe", "nfce"];

                        if (!in_array($tipoDoc, $arrTipoDocAceito)) {
                            $this->call(
                                '401',
                                'Ops',
                                '',
                                "ops",
                                "Por enquanto são aceitos apenas: nfe, nfce"
                            )->back(["count" => 0]);
                            return;
                        }
                    }
                }

                if (mime_content_type($uploadedFile) === 'application/zip') {
                    $zip = new \ZipArchive;
                    if ($zip->open($uploadedFile) === TRUE) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $fileName = $zip->getNameIndex($i);

                            if (pathinfo($fileName, PATHINFO_EXTENSION) === 'xml') {
                                $xmlContent = $zip->getFromIndex($i);

                                $XML = simplexml_load_string($xmlContent);

                                try {

                                    $form_nfe['doc_id_client'] = $ideClient;
                                    if($tipoDoc == 'nfe'){
                                        $form_nfe['doc_mod'] = '55';
                                    }elseif($tipoDoc == 'nfce'){
                                        $form_nfe['doc_mod'] = '65';
                                    }

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
                                    $form_nfe['doc_file'] = base64_encode($XML->asXML());
                                    if($$tipo == 'destinataria'){
                                        $form_nfe['doc_tipo'] = 'dest';
                                    }else{
                                        $form_nfe['doc_tipo'] = 'emit';
                                    }




                                    try {
                                        $resultDoc = $this->db()->from('doc')
                                            ->where('doc_id_client')->is($ideClient)
                                            ->andWhere('doc_chave')->is($form_nfe['doc_chave'])
                                            ->limit(1)
                                            ->select()
                                            ->all();

                                        if (!$resultDoc) {
                                            $chavesInseridas[] = $form_nfe['doc_chave'];
                                            $this->db()->insert($form_nfe)->into('doc');
                                            unset($form_nfe);
                                        }
                                        unset($form_nfe);
                                    } catch (\Exception $e) {
                                        $this->call(
                                            '401',
                                            'Ops',
                                            '',
                                            "ops",
                                            "Erro ao processar o documento, verifique as informações e tente novamente mais tarde"
                                        )->back(["count" => 0]);
                                        return;
                                    }
                                }catch (\Exception $e) {
                                    $this->call(
                                        '401',
                                        'Ops',
                                        '',
                                        "ops",
                                        "Erro ao processar o documento, verifique as informações e tente novamente mais tarde"
                                    )->back(["count" => 0]);
                                    return;
                                }
                            }
                        }
                        $this->call(
                            '200',
                            'Sucesso',
                            '',
                            "ok",
                            "Operação realizada com sucesso"
                        )->back(["chaves_inseridas" => $chavesInseridas]);
                        return;
                    }else{
                        $this->call(
                            '401',
                            'Ops',
                            '',
                            "ops",
                            "Falha ao abrir o arquivo zip"
                        )->back(["count" => 0]);
                        return;
                    }
                }else{
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "É necessário enviar arquivo zip"
                    )->back(["count" => 0]);
                    return;
                }
            }else{
                $this->call(
                    '401',
                    'Ops',
                    '',
                    "ops",
                    "Problemas ao enviar arquivos"
                )->back(["count" => 0]);
                return;
            }
        }catch (\Exception $e){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Erro ao enviar arquivos, tente novamente mais tarde"
            )->back(["count" => 0]);
            return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Não encontrei o client informado"
        )->back(["count" => 0]);
        return;
    }

    public function dfeDocsSelectIde()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $fieldsRequired = ['chave'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->join('doc', function($join){
                $join->on('doc_id_client', 'client_ide');
            })
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']))
            ->andWhere('doc_chave')->is($postVars['chave'])
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){

            foreach($result as $rDFe){

                $resultEvents = $this->db()->from('eventos')
                    ->where('eventos_id_client')->is(clearDoc($getHeaders['ideClient']))
                    ->andWhere('eventos_chave')->is($postVars['chave'])
                    ->orderBy('eventos_id', 'ASC')
                    ->select()
                    ->all();

                if($resultEvents){
                    foreach($resultEvents as $rEvents){
                        $arrEvents[] = [
                            "tipo" => $rEvents->eventos_desc_evento,
                            "codigo" => $rEvents->eventos_code_evento,
                            "data" => $rEvents->eventos_data,
                            "protocolo" => $rEvents->eventos_prot,
                            "xml" => $rEvents->eventos_file
                        ];
                    }
                }

                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(
                    [
                        "chave" => $rDFe->doc_chave,
                        "doc" => $rDFe->doc_dest_documento,
                        "name" => $rDFe->doc_emit_nome,
                        "data" => $rDFe->doc_date_emi,
                        "valor" => number_format($rDFe->doc_valor, 2, ".", ""),
                        "xml" => $rDFe->doc_file,
                        "events" => $arrEvents
                    ]);
                return;

            }

        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Não encontrei o documento informado"
        )->back(["count" => 0]);
        return;
    }

    public function dfeDocs()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        if (!is_numeric($postVars['offset'])) {
            $limit = '500';
        }else if ($postVars['limit'] < 500) {
            $limit = $postVars['limit'];
        } else {
            $limit = '500';
        }
        if (!is_numeric($postVars['offset'])) {
            $offset = '0';
        } else {
            $offset = $postVars['offset'];
        }

        $query = $this->db()->from('client')
            ->join('doc', function($join){
                $join->on('doc_id_client', 'client_ide');
            })
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ideClient']));

        if($postVars['doc'] != ''){
            $query = $query->andWhere('doc_emit_documento')->is($postVars['doc']);
        }
        if($postVars['data_inicial'] != '' && $postVars['data_final'] != ''){
            $query = $query->andWhere('doc_date_emi')->between($postVars['data_inicial'].' 00:00:00', $postVars['data_final'].' 23:59:59');
        }

        $resultTodal = $query->orderBy('doc_date_emi', 'DESC')
            ->select(function($include){
                $include->columns(['doc_id']);
            })
            ->all();

        $result = $query->orderBy('doc_date_emi', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->select(function($include){
                $include->columns(['doc_status_manifestacao', 'doc_chave', 'doc_dest_documento', 'doc_emit_nome', 'doc_date_emi', 'doc_valor']);
            })
            ->all();

        if($result){

            foreach($result as $rDFe){

                if($rDFe->doc_status_manifestacao == '0'){
                    $status_manifestacao = 'SEM MANIFESTACAO';
                }elseif($rDFe->doc_status_manifestacao == '1'){
                    $status_manifestacao = 'CIENCIA';
                }elseif($rDFe->doc_status_manifestacao == '2'){
                    $status_manifestacao = 'CONFIRMACAO';
                }elseif($rDFe->doc_status_manifestacao == '3'){
                    $status_manifestacao = 'DESACORDO';
                }elseif($rDFe->doc_status_manifestacao == '4'){
                    $status_manifestacao = 'DESCONHECIMENTO';
                }else{
                    $status_manifestacao = 'SEM INFORMACAO';
                }

                $resultEvents = $this->db()->from('eventos')
                    ->where('eventos_id_client')->is(clearDoc($getHeaders['ideClient']))
                    ->andWhere('eventos_chave')->is($rDFe->doc_chave)
                    ->orderBy('eventos_id', 'ASC')
                    ->select()
                    ->all();

                if($resultEvents){
                    foreach($resultEvents as $rEvents){
                        $arrEvents[] = [
                            "tipo" => $rEvents->eventos_desc_evento,
                            "codigo" => $rEvents->eventos_code_evento,
                            "data" => $rEvents->eventos_data,
                            "protocolo" => $rEvents->eventos_prot
                        ];
                    }
                }

                if($rDFe->doc_status == '0'){
                    $status_nfe = 'AUTORIZADA';
                }elseif($rDFe->doc_status == '1'){
                    $status_nfe = 'CANCELADA';
                }

                $arrXML[] = [
                    "chave" => $rDFe->doc_chave,
                    "doc" => $rDFe->doc_dest_documento,
                    "name" => $rDFe->doc_emit_nome,
                    "data" => $rDFe->doc_date_emi,
                    "status" => $status_nfe,
                    "valor" => number_format($rDFe->doc_valor, 2, ".", ""),
                    "manifestacao" => $status_manifestacao,
                    "events" => $arrEvents
                ];
                unset($arrEvents);
            }

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(
                [
                    "total" => count($resultTodal),
                    "xmls" => $arrXML
                ]);
            return;

        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Não encontrei o documento informado"
        )->back(["count" => 0]);
        return;
    }


    private function createTemp($base64)
    {
        $decode = base64_decode($base64);
        $nameCertificate = md5(date('Y-m-dH:i:s').rand(1000,9999)).'.pfx';
        file_put_contents(__DIR__.'/../../shared/certificate/'.$nameCertificate,$decode);

        return $nameCertificate;
    }

    public function webhookCreate():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $fieldsRequired = ['url', 'ideClient'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($postVars['ideClient']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if(!$result){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "O ideClient informado não pertence ao seu cadastro"
            )->back(["count" => 0]);
            return;
        }

        $postDFe = [
            'webhook_dfe_ide' => hash('md5', date('YmdHis').rand(1000,9999)),
            'webhook_dfe_id_company' => $idReg,
            'webhook_dfe_ide_client' => $postVars['ideClient'],
            'webhook_dfe_url' => $postVars['url'],
            'webhook_dfe_lixeira' => '0'
        ];


        if($this->db()->insert($postDFe)->into('webhook_dfe')){
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $postDFe['webhook_dfe_ide']]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function webhookDelete():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $getHeaders = $this->getHeaders();

        $fieldsRequired = ['url', 'ideClient'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        $postDFe = [
            'webhook_dfe_lixeira' => '1'
        ];

        $result = $this->db()->update('webhook_dfe')->where('webhook_dfe_ide')->is($getHeaders['ide'])->andWhere('webhook_dfe_id_company')->is($idReg)->set($postDFe);
        if($result == '0' || $result == '1'){
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $getHeaders['ide']]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function dfeDocsZIP()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $query = $this->db()->from('doc')->where('doc_id_client')->is(clearDoc($getHeaders['ideClient']));

        if($postVars['data_inicial'] != '' && $postVars['data_final']){
            $query = $query->andWhere('doc_date_emi')->between($postVars['data_inicial'], $postVars['data_final']);
        }
        if(isset($postVars['chaves'])){
            if(count($postVars['chaves']) > '0'){
                $query = $query->andWhere('doc_chave')->in($postVars['chaves']);
            }
        }
        $result = $query->orderBy('doc_id')
            ->select()
            ->all();

        if($result){
            $nameArq = md5(date('Y-m-dH:i:s').rand(100,999));
            $zip = new \ZipArchive();
            if($zip->open('shared/zip/'.$nameArq.'.zip', \ZipArchive::CREATE) === TRUE){
                foreach($result as $r){
                    $dataBin = base64_decode($r->doc_file);
                    if($r->doc_mod == '55'){
                        $zip->addFromString("{$r->doc_chave}-nfe.xml", $dataBin);
                    }
                    if($r->doc_mod == '65'){
                        $zip->addFromString("{$r->doc_chave}-nfce.xml", $dataBin);
                    }
                }
                $zip->close();
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(["arquivo" => URL_BASE.'/shared/zip/'.$nameArq.'.zip', "expiracao" => date('Y-m-d H:i:s', strtotime("+1 hours"))]);
                return;
            }

            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Tivemos um problema, tente novamente mais tarde"
            )->back(["count" => 0]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function dfeEventClient()
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $query = $this->db()->from('client')->where('client_dfe_use')->is(1)->andWhere('client_id_company')->is($idReg);

        if($postVars['ideClient'] != ''){
            $query = $query->andWhere('client_ide')->is($postVars['ideClient']);
        }
        $result = $query->orderBy('client_id')
            ->select()
            ->all();

        if($result){
            foreach($result as $r){
                unset($fieldsResponse);
                $fieldsResponse = [
                    'ide' => $r->client_ide,
                    'cpfCnpj' => $r->client_doc,
                    'inscricaoMunicipal' => $r->client_im,
                    'inscricaoEstadual' => $r->client_ie,
                    'razaoSocial' => $r->client_name,
                    'nomeFantasia' => $r->client_fantasy,
                    'endereco' => [
                        'bairro' => $r->client_address_district,
                        'cep' => $r->client_address_zip,
                        'codigoCidade' => $r->client_address_code_city,
                        'cidade' => $r->client_address_city,
                        'estado' => $r->client_address_state,
                        'logradouro' => $r->client_address_place,
                        'numero' => $r->client_address_number,
                        'complemento' => $r->client_address_complement
                    ],
                    'telefone' => [
                        'celular' => $r->client_phone,
                        'whats' => $r->client_whats,
                        'fixo' => $r->client_fixed
                    ],
                    'email' => $r->client_email,
                    'status' => $r->client_status,
                    'dfeCode' => $r->client_dfe_ult_event_code,
                    'dfeDesc' => $r->client_dfe_ult_event_desc,
                    'dfeDataHora' => $r->client_dfe_ult_event_datetime
                ];
                $rows['data'][] = $fieldsResponse;
            }
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Registros encontrados"
            )->back(["count" => 1, "response" => $rows]);
            return;
        }
        $this->call(
            '200',
            'Erro',
            '',
            "error",
            "Não encontramos nenhum registro"
        )->back(["count" => 0]);
        return;
    }

    public function dfePDFZIP()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $query = $this->db()->from('doc')->where('doc_id_client')->is(clearDoc($getHeaders['ideClient']));

        if(isset($postVars['chaves'])){
            if(count($postVars['chaves']) > '0'){
                $query = $query->andWhere('doc_chave')->in($postVars['chaves']);
            }
        }
        $result = $query->orderBy('doc_id')
            ->select()
            ->all();

        if($result){
            $nameArq = md5(date('Y-m-dH:i:s').rand(100,999));
            $zip = new \ZipArchive();
            if($zip->open('shared/zip/'.$nameArq.'.zip', \ZipArchive::CREATE) === TRUE){
                foreach($result as $r){
                    $dataBin = base64_decode($r->doc_file);
                    if($r->doc_mod == '55'){
                        try {
                            $danfe = new Danfe($dataBin);

                            $danfe->printParameters($orientacao = 'P', $papel = 'A4', $margSup = 2, $margEsq = 2);
                            $danfe->logoParameters('', $logoAlign = 'C', $mode_bw = false);
                            $danfe->setDefaultFont($font = 'times');
                            $danfe->setDefaultDecimalPlaces(4);
                            $danfe->debugMode(false);

                            $pdf = $danfe->render();
                            $zip->addFromString("{$r->doc_chave}-nfe.pdf", $pdf);

                        } catch (InvalidArgumentException $e) {

                        }

                    }
                    if($r->doc_mod == '65'){
                        try {
                            $danfce = new Danfce($dataBin);

                            $danfce->setPaperWidth(80);
                            $danfce->setOffLineDoublePrint(true);
                            $danfce->setDefaultFont('arial');
                            $danfce->setMargins(2);

                            $pdf = $danfce->render();
                            $zip->addFromString("{$r->doc_chave}-nfce.pdf", $pdf);
                        } catch (InvalidArgumentException $e) {

                        }
                    }
                }
                $zip->close();
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(["arquivo" => URL_BASE.'/shared/zip/'.$nameArq.'.zip', "expiracao" => date('Y-m-d H:i:s', strtotime("+1 hours"))]);
                return;
            }

            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Tivemos um problema, tente novamente mais tarde"
            )->back(["count" => 0]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function dfePDF()
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $postVars = $this->postVars();

        $result = $this->db()->from('doc')->where('doc_id_client')->is(clearDoc($getHeaders['ideClient']))
                ->andWhere('doc_chave')->is($postVars['chave'])
                ->orderBy('doc_id')
                ->select()
                ->all();

        if($result){
            $nameArq = md5(date('Y-m-dH:i:s').rand(100,999));
            foreach($result as $r){
                $dataBin = base64_decode($r->doc_file);
                if($r->doc_mod == '55'){
                    try {
                        $danfe = new Danfe($dataBin);

                        $danfe->printParameters($orientacao = 'P', $papel = 'A4', $margSup = 2, $margEsq = 2);
                        $danfe->logoParameters('', $logoAlign = 'C', $mode_bw = false);
                        $danfe->setDefaultFont($font = 'times');
                        $danfe->setDefaultDecimalPlaces(4);
                        $danfe->debugMode(false);

                        $pdf = $danfe->render();
                        file_put_contents('shared/pdf/'.$nameArq.'.pdf', $pdf);
                        $this->call(
                            '200',
                            'Sucesso',
                            '',
                            "ok",
                            "Operação realizada com sucesso"
                        )->back(["arquivo" => URL_BASE.'/shared/pdf/'.$nameArq.'.pdf', "expiracao" => date('Y-m-d H:i:s', strtotime("+1 hours"))]);
                        return;
                    } catch (InvalidArgumentException $e) {
                        $this->call(
                            '400',
                            'Ops',
                            '',
                            "ops",
                            "Tivemos um problema, tente novamente mais tarde"
                        )->back(["count" => 0]);
                        return;
                    }
                }

                if($r->doc_mod == '65'){
                    try {
                        $danfce = new Danfce($dataBin);

                        $danfce->setPaperWidth(80);
                        $danfce->setOffLineDoublePrint(true);
                        $danfce->setDefaultFont('arial');
                        $danfce->setMargins(2);

                        $pdf = $danfce->render();
                        file_put_contents('shared/pdf/'.$nameArq.'.pdf', $pdf);
                        $this->call(
                            '200',
                            'Sucesso',
                            '',
                            "ok",
                            "Operação realizada com sucesso"
                        )->back(["arquivo" => URL_BASE.'/shared/pdf/'.$nameArq.'.pdf', "expiracao" => date('Y-m-d H:i:s', strtotime("+1 hours"))]);
                        return;
                    } catch (InvalidArgumentException $e) {
                        $this->call(
                            '400',
                            'Ops',
                            '',
                            "ops",
                            "Tivemos um problema, tente novamente mais tarde"
                        )->back(["count" => 0]);
                        return;
                    }
                }
            }


            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Tivemos um problema, tente novamente mais tarde"
            )->back(["count" => 0]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }
}

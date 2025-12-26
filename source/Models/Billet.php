<?php

namespace Source\Models;

use DateTime;
use sleifer\boleto\Boleto;
use Source\Conn\DataLayer;
use Source\Facades\BoletoBB;
use Source\Facades\BoletoCaixa;
use Source\Facades\BoletoInter;
use Source\Facades\BoletoInterHibrido;
use Source\Facades\BoletoSantander;
use Source\Facades\BoletoSicoob;
use Source\Facades\BoletoSicredi;
use Source\Facades\BoletoSicrediHibrido;
use Source\Facades\PixBB;
use Source\Facades\PixInter;
use Source\Facades\BoletoBradesco;
use Source\Facades\BoletoCora;

class Billet extends DataLayer {
    public function create() {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $postVars['ide'] = md5(date('Y-mdHis') . rand(10000, 99999));

        $requiredValidation = $this->getFieldsBillet($postVars);

        if ($requiredValidation['type'] == 'error') {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                $requiredValidation['msg']
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('wallet')
            ->where('wallet_id_company')->is($idReg)
            ->andWhere('wallet_ide')->is(clearDoc($postVars['ideWallet']))
            ->andWhere('wallet_trash')->is(0)
            ->orderBy('wallet_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "O ideWallet informado não pertence ao seu cadastro"
            )->back(["count" => 0]);
            return;
        }


        $requiredValidation['fields']['finance_id_company'] = $idReg;
        $requiredValidation['fields']['finance_trash'] = 0;
        $requiredValidation['fields']['finance_status'] = 0;
        $requiredValidation['fields']['finance_status_webservice'] = 0;
        $requiredValidation['fields']['finance_date_release'] = date('Y-m-d');

        /*if($idReg == '13'){
                $requiredValidation['fields']['finance_ra'] = 'S';
            }*/

        foreach ($result as $r) {
            if ($r->wallet_bank == '104') {
                $requiredValidation['fields']['finance_number'] = '14' . str_pad($requiredValidation['fields']['finance_our_number'], 15, '0', STR_PAD_LEFT);
            }
        }
        //print_r($requiredValidation['fields']);


        if ($this->db()->insert($requiredValidation['fields'])->into('finance')) {
            $resultIdReg = $this->db()->from('finance')
                ->where('finance_id_company')->is($idReg)
                ->andWhere('finance_ide')->is(clearDoc($postVars['ide']))
                ->andWhere('finance_trash')->is(0)
                ->orderBy('finance_id', 'DESC')
                ->limit('1')
                ->select()
                ->all();
            if ($resultIdReg) {
                foreach ($resultIdReg as $rIdReg) {
                    $idRegBillet = $rIdReg->finance_id;
                }
            }
            //$idRegBillet = $this->lastReg('finance', 'finance_id', 'finance_id');

            $res = $this->registerBilletBankSR($idRegBillet, $postVars);

            if ($res['type'] == 'error') {
                $upBillet['finance_trash'] = '1';
                $this->db()->update('finance')->where('finance_id')->is($idRegBillet)->set($upBillet);

                $this->call(
                    '400',
                    'Ops',
                    '',
                    "ops",
                    $res['msg']
                )->back(["count" => 0]);
                return;
            }

            if ($res['type'] == 'ok') {
                if ($res['status'] == 'REGISTRADA') {
                    $upBillet['finance_status'] = '0';
                    $upBillet['finance_status_webservice'] = '1';
                    $upBillet['finance_barcode'] = $res['codigo_barras'];
                    $upBillet['finance_line_dig'] = $res['linha_digitavel'];
                    if ($res['codigoSolicitacao']) {
                        $upBillet['finance_codigo_solicitacao'] = $res['codigoSolicitacao'];
                    }
                    if (isset($res['id'])) {
                        $upBillet['finance_codigo_solicitacao'] = $res['id'];
                    }

                    $upBillet['finance_payload_hybrid'] = $res['location'];
                    $upBillet['finance_txtId'] = $res['txtid'];
                    if ($res['nossoNumero']) {
                        $requiredValidation['fields']['finance_number'] = $res['nossoNumero'];
                        $upBillet['finance_number'] = $res['nossoNumero'];
                    }
                    if ($res['link']) {
                        $upBillet['finance_link'] = $res['link'];
                    }

                    if ($postVars['qrCode'] == 'S') {
                        $resultQr = $this->db()->from('finance')
                            ->join('wallet', function ($join) {
                                $join->on('wallet_ide', 'finance_ide_wallet');
                            })
                            ->join('client', function ($join2) {
                                $join2->on('client_ide', 'wallet_ide_client');
                            })
                            ->where('finance_id')->is($idRegBillet)
                            ->andWhere('finance_trash')->is(0)
                            ->andWhere('finance_id_company')->is($idReg)
                            ->orderBy('finance_id', 'DESC')
                            ->limit('1')
                            ->select()
                            ->all();
                        if ($resultQr) {
                            foreach ($resultQr as $rQr) {
                                $base64 = getQRCode($rQr->client_name, $rQr->client_address_city, $res['location'], 'base64');
                            }
                        } else {
                            $base64 = '';
                        }
                    } else {
                        $base64 = '';
                    }
                    $this->db()->update('finance')->where('finance_id')->is($idRegBillet)->set($upBillet);
                }

                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(["ide" => $requiredValidation['fields']['finance_ide'], "nossoNumero" => $requiredValidation['fields']['finance_number'], "linha_digitavel" => $res['linha_digitavel'], "codigo_barras" => $res['codigo_barras'], "link" => $upBillet['finance_link'], "copiaCola" => $res['copiaCola'], "qrCode" => $base64]);
                return;
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

    public function update() {
        $idReg = $this->checkToken();
        $postVars = $this->postVars();
        $getHeaders = $this->getHeaders();

        if ($postVars['dataVencimento'] == '' || $postVars['valor'] == '') {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                'Existem campos obrigatórios não preenchido'
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_ide')->is($getHeaders['ide'])
            ->andWhere('finance_trash')->is(0)
            ->andWhere('finance_id_company')->is($idReg)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não encontrei o registro enviado"
            )->back(["count" => 0]);
            return;
        }

        $postFinance['finance_value'] = $postVars['valor'];
        $postFinance['finance_date_due'] = $postVars['dataVencimento'];

        $resultUpdate = $this->db()->update('finance')->where('finance_ide')->is($getHeaders['ide'])->andWhere('finance_id_company')->is($idReg)->set($postFinance);
        if ($resultUpdate == '0' || $resultUpdate == '1') {
            foreach ($result as $r) {
                $res = $this->editBilletBank($r->finance_id, $postVars);

                if ($res['type'] == 'error') {
                    $this->call(
                        '400',
                        'Ops',
                        '',
                        "ops",
                        $res['msg']
                    )->back(["count" => 0]);
                    return;
                }

                if ($res['type'] == 'ok') {
                    if ($res['status'] == 'REGISTRADA') {
                        $upBillet['finance_status_webservice'] = '1';
                        $upBillet['finance_barcode'] = $res['codigo_barras'];
                        $upBillet['finance_line_dig'] = $res['linha_digitavel'];
                        $this->db()->update('finance')->where('finance_id')->is($r->finance_id)->set($upBillet);
                    }

                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["ide" => $r->finance_ide]);
                    return;
                }
            }
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

    public function selectIde(): void {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $result = $this->db()->from('finance')
            ->where('finance_id_company')->is($idReg)
            ->andWhere('finance_ide')->is(clearDoc($getHeaders['ide']))
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                unset($fieldsResponse);
                if ($r->finance_status == '0') {
                    $r->finance_status = 'ABERTO';
                } elseif ($r->finance_status == '1') {
                    $r->finance_status = 'BAIXADO';
                } elseif ($r->finance_status == '2') {
                    $r->finance_status = 'CANCELADO/DEVOLVIDO';
                }
                if ($r->finance_status_webservice == '0') {
                    $r->finance_status_webservice = 'NÃO REGISTRADO';
                } elseif ($r->finance_status_webservice == '1') {
                    $r->finance_status_webservice = 'REGISTRADO';
                }
                $copiaCola = '';
                if ($r->finance_payload_hybrid != '') {
                    $copiaCola = getCopyPaste($r->finance_payload_hybrid);
                }
                $fieldsResponse = [
                    'ide' => $r->finance_ide,
                    'ideWallet' => $r->finance_ide_wallet,
                    'referencia' => $r->finance_reference,
                    'tipoPagador' => $r->finance_type_pagador,
                    'cpfCnpj' => $r->finance_doc_pagador,
                    'nome' => $r->finance_name_pagador,
                    'endereco' => [
                        'bairro' => $r->finance_district_pagador,
                        'cep' => $r->finance_zip_pagador,
                        'cidade' => $r->finance_city_pagador,
                        'estado' => $r->finance_state_pagador,
                        'logradouro' => $r->finance_address_pagador,
                        'numero' => $r->finance_number_pagador,
                        'complemento' => $r->finance_complement_pagador,
                    ],
                    'telefone' => $r->finance_telefone_pagador,
                    'email' => $r->finance_email_pagador,
                    'nossoNumero' => $r->finance_number,
                    'numeroDocumento' => $r->finance_our_number,
                    'dataVencimento' => $r->finance_date_due,
                    'valor' => $r->finance_value,
                    'status' => $r->finance_status,
                    'statusWebservice' => $r->finance_status_webservice,
                    'codigoBarras' => $r->finance_barcode,
                    'linhaDigitavel' => $r->finance_line_dig,
                    'copiaCola' => $copiaCola,
                    'link' => $r->finance_link

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

    public function select(): void {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $query_paginator = $this->db()->from('finance')->where('finance_id_company')->is($idReg)->andWhere('finance_trash')->is(0);
        $query = $this->db()->from('finance')->where('finance_id_company')->is($idReg)->andWhere('finance_trash')->is(0);

        if ($postVars['ide'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_ide')->is($postVars['ide']);
            $query = $query->andWhere('finance_ide')->is($postVars['ide']);
        }
        if ($postVars['status'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_status')->is($postVars['status']);
            $query = $query->andWhere('finance_status')->is($postVars['status']);
        }
        if ($postVars['statusWebservice'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_status_webservice')->is($postVars['statusWebservice']);
            $query = $query->andWhere('finance_status_webservice')->is($postVars['statusWebservice']);
        }
        if ($postVars['ideWallet'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_ide_wallet')->is($postVars['ideWallet']);
            $query = $query->andWhere('finance_ide_wallet')->is($postVars['ideWallet']);
        }
        if ($postVars['nossoNumero'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_number')->is($postVars['nossoNumero']);
            $query = $query->andWhere('finance_number')->is($postVars['nossoNumero']);
        }
        if ($postVars['reference'] != '') {
            $query_paginator = $query_paginator->andWhere('finance_reference')->is($postVars['reference']);
            $query = $query->andWhere('finance_reference')->is($postVars['reference']);
        }

        if ($postVars['typeSearch'] == 'dataVencimento') {
            if ($postVars['dateInit'] != '' && $postVars['dateFinish'] != '') {
                $query_paginator = $query_paginator->andWhere('finance_date_due')->between($postVars['dateInit'], $postVars['dateFinish']);
                $query = $query->andWhere('finance_date_due')->between($postVars['dateInit'], $postVars['dateFinish']);
            }
        }

        if ($postVars['limit'] < 50) {
            $limit = $postVars['limit'];
        } else {
            $limit = '50';
        }

        if (!is_numeric($postVars['offset'])) {
            $offset = '0';
        } else {
            $offset = $postVars['offset'];
        }
        $resultPaginator = $query_paginator
            ->select()
            ->all();
        $result = $query->orderBy('finance_id')
            ->limit($limit)
            ->offset($offset)
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                unset($fieldsResponse);
                if ($r->finance_status == '0') {
                    $r->finance_status = 'ABERTO';
                } elseif ($r->finance_status == '1') {
                    $r->finance_status = 'BAIXADO';
                } elseif ($r->finance_status == '2') {
                    $r->finance_status = 'CANCELADO/DEVOLVIDO';
                }
                if ($r->finance_status_webservice == '0') {
                    $r->finance_status_webservice = 'NÃO REGISTRADO';
                } elseif ($r->finance_status_webservice == '1') {
                    $r->finance_status_webservice = 'REGISTRADO';
                }


                $fieldsResponse = [
                    'ide' => $r->finance_ide,
                    'ideWallet' => $r->finance_ide_wallet,
                    'referencia' => $r->finance_reference,
                    'tipoPagador' => $r->finance_type_pagador,
                    'cpfCnpj' => $r->finance_doc_pagador,
                    'nome' => $r->finance_name_pagador,
                    'endereco' => [
                        'bairro' => $r->finance_district_pagador,
                        'cep' => $r->finance_zip_pagador,
                        'cidade' => $r->finance_city_pagador,
                        'estado' => $r->finance_state_pagador,
                        'logradouro' => $r->finance_address_pagador,
                        'numero' => $r->finance_number_pagador,
                        'complemento' => $r->finance_complement_pagador,
                    ],
                    'telefone' => $r->finance_telefone_pagador,
                    'email' => $r->finance_email_pagador,
                    'nossoNumero' => $r->finance_number,
                    'numeroDocumento' => $r->finance_our_number,
                    'dataVencimento' => $r->finance_date_due,
                    'valor' => $r->finance_value,
                    'status' => $r->finance_status,
                    'statusWebservice' => $r->finance_status_webservice,
                    'codigoBarras' => $r->finance_barcode,
                    'linhaDigitavel' => $r->finance_line_dig,
                    'link' => $r->finance_link

                ];
                if ($r->finance_payload_hybrid != '') {
                    $fieldsResponse['qrCode'] = getQRCode($r->finance_name_pagador, $r->finance_city_pagador, $r->finance_payload_hybrid, 'base64');
                    $fieldsResponse['copiaCola'] = getCopyPaste($r->finance_payload_hybrid);
                } else {
                    $fieldsResponse['qrCode'] = '';
                    $fieldsResponse['copiaCola'] = '';
                }
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

    public function payDevolution() {
        $idReg = $this->checkToken();
        $getHeaders = $this->getHeaders();

        if ($getHeaders['ide'] == '') {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                'Existem campos obrigatórios não preenchido'
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join) {
                $join->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_ide')->is($getHeaders['ide'])
            ->andWhere('finance_trash')->is(0)
            ->andWhere('finance_id_company')->is($idReg)
            ->andWhere('finance_status_webservice')->is(1)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não encontrei o registro enviado"
            )->back(["count" => 0]);
            return;
        }

        if ($result) {
            foreach ($result as $ra) {
                if ($ra->finance_status == '2') {
                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["ide" => $ra->finance_ide]);
                    return;
                }
            }
        }

        //$postFinance['finance_status'] = '2';
        $resultUpdate = '0';

        //$resultUpdate = $this->db()->update('finance')->where('finance_ide')->is($getHeaders['ide'])->andWhere('finance_id_company')->is($idReg)->set($postFinance);
        if ($resultUpdate == '0' || $resultUpdate == '1') {
            foreach ($result as $r) {
                $res = $this->downBilletBank($r->finance_id);

                if ($res['type'] == 'error') {
                    $this->call(
                        '400',
                        'Ops',
                        '',
                        "ops",
                        $res['msg']
                    )->back(["count" => 0]);
                    return;
                }

                if ($res['type'] == 'ok') {
                    if ($res['status'] == 'BAIXADO') {
                        $upBillet['finance_status_webservice'] = '1';
                        $upBillet['finance_status'] = '2';
                        $upBillet['finance_barcode'] = $res['codigo_barras'];
                        $upBillet['finance_line_dig'] = $res['linha_digitavel'];
                        $this->db()->update('finance')->where('finance_id')->is($r->finance_id)->set($upBillet);
                    }

                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["ide" => $r->finance_ide]);
                    return;
                }
            }
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


    public function printBillet() {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        if (count($postVars['ide']) > '36') {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Só é possível emitir 36 faturas por requisição"
            )->back(["count" => 0]);
            return;
        }

        if ($postVars['tipo'] != 'carne' && $postVars['tipo'] != 'boleto') {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Por enquanto só podemos emitir o tipo carnê/boleto"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_ide')->in($postVars['ide'])
            ->andWhere('finance_trash')->is(0)
            ->andWhere('finance_id_company')->is($idReg)
            ->andWhere('finance_status_webservice')->is(1)
            ->andWhere('finance_ide_wallet')->is($postVars['ideWallet'])
            ->orderBy('finance_date_due', 'ASC')
            ->limit('36')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Não encontrei nenhum registro válido"
            )->back(["count" => 0]);
            return;
        }

        $html_carne = '';

        $contador_geral = count($result);
        $count_carne = '0';
        $count_carne_geral = '0';
        $count_boleto = '0';

        foreach ($result as $r) {
            $texto_juros_multa = '';
            $count_carne++;
            $count_boleto++;
            $count_carne_geral++;
            if ($postVars['tipo'] == 'carne') {
                $billet = file_get_contents(__DIR__ . '/../../templates/carne/index.php');
            }
            if ($postVars['tipo'] == 'boleto') {
                if ($r->finance_ra == 'S') {
                    $billet = file_get_contents(__DIR__ . '/../../templates/ra/index.php');
                } else {
                    $billet = file_get_contents(__DIR__ . '/../../templates/boleto/index.php');
                }
            }
            if ($r->wallet_type_penalty == '1') {
                $texto_juros_multa .= 'Após o vencimento cobrar multa de ' . $r->wallet_penalty . '%';
            } elseif ($r->wallet_type_penalty == '2') {
                $texto_juros_multa .= 'Após o vencimento cobrar multa de R$ ' . $r->wallet_penalty . '';
            } else {
                $texto_juros_multa .= '';
            }

            if ($r->wallet_type_fees == '1') {
                $texto_juros_multa .= '<br />Após o vencimento cobrar juros de ' . $r->wallet_fees . '%';
            } elseif ($r->wallet_type_fees == '2') {
                $texto_juros_multa .= '<br />Após o vencimento cobrar juros de R$ ' . $r->wallet_fees . '';
            } else {
                $texto_juros_multa .= '';
            }
            $arrData = [
                'dataVencimento' => format_date_br($r->finance_date_due),
                'agencia' => $r->wallet_agency,
                'cedente' => $r->wallet_beneficiary,
                'valor' => format_money($r->finance_value),
                'nossoNumero' => $r->finance_number,
                'numeroDocumento' => $r->finance_our_number,
                'dadosSacado' => $r->finance_doc_pagador . ' - ' . $r->finance_name_pagador,
                'linhaDigitavel' => $r->finance_line_dig,
                'dataDocumento' => format_date_br($r->finance_date_release),
                'especie' => $r->wallet_species_document,
                'aceite' => $r->wallet_accept,
                'informativo' => $r->wallet_informative,
                'dataProcessamento' => date('d/m/Y'),
                'descricao' => $r->finance_description,
                'dadosBeneficiario' => $r->client_doc . ' - ' . $r->client_name,
                'endereco01Sacado' => $r->finance_address_pagador . ' - ' . $r->finance_number_pagador,
                'endereco02Sacado' => $r->finance_city_pagador . ' - ' . $r->finance_state_pagador,
                'docBeneficiario' => $r->client_doc,
                'endereco01Beneficiario' => $r->client_address_place . ',' . $r->client_address_number,
                'endereco02Beneficiario' => $r->client_address_district . ' | ' . $r->client_address_city . ' - ' . $r->client_address_state,
                'nameSacado' => $r->finance_name_pagador,
                'docSacado' => $r->finance_doc_pagador,
                'descricaoBoleto' => '<span>' . $r->finance_description . '</span>',
                'valorJurosMulta' => '<span>' . $texto_juros_multa . '</span>'
            ];
            $arrData['qrCodePix'] = '';
            if ($r->finance_payload_hybrid != '') {
                $arrData['qrCodePix'] = '<img style="max-width:120px;width: auto;height:auto; float:left;" src="' . getQRCode($r->client_name, $r->client_address_city, $r->finance_payload_hybrid, 'png') . '"/> <hr /><span style="font-size: 18px; ">Sua fatura agora possui o QRCode para pagamento via PIX com todos os benefícios, como: facilidade, comodidade, rapidez, agilidade e segurança.
Passo a Passo para pagar com QRCode: é necessário entrar no aplicativo da sua instituição financeira, clicar na opção "PIX", selecionar a opção "QRCode" e com a câmera do seu celular escaneaar o QRCode impresso, conferir as informações da transação e efetuar o pagamento</span>';
            }
            if ($postVars['tipo'] == 'carne') {
                $html_carne .= replace_carne($r->wallet_bank, $billet, $arrData);
                if ($count_carne == 3 && $count_carne_geral != $contador_geral) {
                    $count_carne = '0';
                    $html_carne .= '<div style="page-break-before:always"></div>';
                }
            }
            if ($postVars['tipo'] == 'boleto') {
                if ($r->finance_ra == 'S') {
                    $arrInitRA = ['#DESCRICAO#', '#EXPIRACAO#', '#VALOR#', '#QRCODE#'];
                    $arrFiniRA = [$r->finance_description, format_date_br($r->finance_date_due) . ' 15:00:00', format_money($r->finance_value), $arrData['qrCodePix']];
                    $html_carne .= str_replace($arrInitRA, $arrFiniRA, $billet);
                    if ($contador_geral != $count_boleto) {
                        $html_carne .= '<div style="page-break-before:always"></div>';
                    }
                } else {
                    $html_carne .= replace_carne($r->wallet_bank, $billet, $arrData);
                    if ($contador_geral != $count_boleto) {
                        $html_carne .= '<div style="page-break-before:always"></div>';
                    }
                }
            }
        }

        $name_pdf = md5(date('YmiHis') . rand(100000, 999999));

        file_put_contents(__DIR__ . '/../../shared/impressao/' . $name_pdf . '.html', $html_carne);
        $this->call(
            '200',
            'Sucesso',
            '',
            "ok",
            "Operação realizada com sucesso"
        )->back(["url" => URL_BASE . '/shared/impressao/' . $name_pdf . '.html']);
        return;
    }

    protected function fieldsRequiredVerification(array $init, array $finish): bool {
        $response = '0';

        if (count($init) > '0') {
            for ($x = 0; $x < count($init); $x++) {
                if (!in_array(trim($init[$x]), array_keys($finish))) {
                    $response = '1';
                }
            }

            if ($response == '1') {
                return false;
            }

            return true;
        }

        if (!in_array($init['0'], $finish)) {
            return false;
        }
        return true;
    }

    protected function getFieldsBillet(array $fields) {

        $fieldsRequiredPost = [
            'ideWallet',
            'referencia',
            'tipoPagador',
            'cpfCnpj',
            'nome',
            'telefone',
            'numeroDocumento',
            'dataVencimento',
            'valor',
            'descricao'
        ];

        /*if (!is_numeric($fields['numeroDocumento'])) {
            return ['type' => 'error', 'msg' => 'numeroDocumento é necessário ser número'];
        }*/

        if (!$this->fieldsRequiredVerification($fieldsRequiredPost, $fields)) {
            return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
        }


        if (!isset($fields['tipoPagador'])) {
            $fields['tipoPagador'] = '1';
        }

        $arrBillet = [
            'finance_ide_wallet' => $fields['ideWallet'],
            'finance_ide' => $fields['ide'],
            'finance_reference' => $fields['referencia'],
            'finance_type_pagador' => $fields['tipoPagador'],
            'finance_doc_pagador' => clearDoc($fields['cpfCnpj']),
            'finance_name_pagador' => $fields['nome'],
            'finance_address_pagador' => $fields['endereco']['logradouro'],
            'finance_number_pagador' => $fields['endereco']['numero'],
            'finance_district_pagador' => $fields['endereco']['bairro'],
            'finance_complement_pagador' => $fields['endereco']['complemento'],
            'finance_city_pagador' => $fields['endereco']['cidade'],
            'finance_state_pagador' => $fields['endereco']['estado'],
            'finance_zip_pagador' => clearDoc($fields['endereco']['cep']),
            'finance_telefone_pagador' => clearDoc($fields['telefone']),
            'finance_email_pagador' => $fields['email'],
            'finance_our_number' => trim($fields['numeroDocumento']),
            'finance_date_due' => $fields['dataVencimento'],
            'finance_value' => $fields['valor'],
            'finance_description' => $fields['descricao'],
            'finance_number' => $fields['nossoNumero'],
            'finance_ra' => $fields['ra']
        ];
        if (isset($fields['horapix'])) {
            $arrBillet['finance_time'] = $fields['horapix'];
        }

        return ['type' => 'ok', 'fields' => $arrBillet];
    }

    protected function readBilletBank($idFinance) {
        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_id')->is($idFinance)
            ->andWhere('finance_trash')->is(0)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                if ($r->wallet_bank == '104') {
                    $bank104 = new BoletoCaixa($r->wallet_beneficiary, $r->client_doc);
                    $arrBilletCaixa['nossoNumero'] = $r->finance_number;

                    $response = $bank104->readBillet($arrBilletCaixa);

                    if ($response->CONTROLE_NEGOCIAL->COD_RETORNO == '1') {
                        return ['type' => 'error', 'msg' => $response->CONTROLE_NEGOCIAL->MENSAGENS->RETORNO];
                    }

                    return ['type' => 'ok', 'status' => 'BAIXADO'];
                }
            }
        }
    }

    protected function downBilletBank($idFinance) {
        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_id')->is($idFinance)
            ->andWhere('finance_trash')->is(0)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                if ($r->wallet_bank == '104') {
                    $bank104 = new BoletoCaixa($r->wallet_beneficiary, $r->client_doc);
                    $arrBilletCaixa['nossoNumero'] = $r->finance_number;

                    $response = $bank104->downBillet($arrBilletCaixa);
                    if ($response->CONTROLE_NEGOCIAL->COD_RETORNO == '1') {
                        return ['type' => 'error', 'msg' => $response->CONTROLE_NEGOCIAL->MENSAGENS->RETORNO];
                    }

                    return ['type' => 'ok', 'status' => 'BAIXADO'];
                }

                if ($r->wallet_bank == '999') {
                    return ['type' => 'ok', 'status' => 'BAIXADO'];
                }

                if ($r->wallet_bank == '033') {

                    $bank033 = new BoletoSantander($r->wallet_token, $r->wallet_token_secret, $r->wallet_certificate, $r->wallet_pass_certificate);

                    $arrBilletSantander['covenantCode'] = $r->wallet_beneficiary;
                    $arrBilletSantander['bankNumber'] = $r->finance_number;
                    $arrBilletSantander['operation'] = 'BAIXAR';

                    $response = $bank033->downBillet($arrBilletSantander, $r->wallet_workspace);

                    if (isset($response->_errors)) {

                        return ['type' => 'error', 'msg' => $response->_errors['0']->_message];
                    }
                    return ['type' => 'ok', 'status' => 'BAIXADO'];
                }

                if ($r->wallet_bank == '001') {
                    if ($r->finance_ra == 'S') {
                        $bank001 = new PixBB($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_bb_dev_key, $r->wallet_certificate, $r->wallet_certificate_key);

                        $arrPixCancel = ['status' => 'REMOVIDA_PELO_USUARIO_RECEBEDOR'];

                        $response = $bank001->cancelPix($arrPixCancel, $r->finance_txtId);

                        if ($response->status == 'REMOVIDA_PELO_USUARIO_RECEBEDOR') {
                            return ['type' => 'ok', 'status' => 'BAIXADO'];
                        }
                        return ['type' => 'error', 'msg' => 'Tivemos um problema ao baixar'];
                    } else {
                        $bank001 = new BoletoBB($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_bb_dev_key);
                        $arrBilletCaixa['numeroConvenio'] = $r->wallet_beneficiary;
                        $number = $r->finance_number;


                        $response = $bank001->downBillet($arrBilletCaixa, $number);

                        if (isset($response->errors)) {

                            return ['type' => 'error', 'msg' => $response->errors['0']->message];
                        }
                        return ['type' => 'ok', 'status' => 'BAIXADO'];
                    }
                }

                if ($r->wallet_bank == '077') {
                    $bank077 = new BoletoInter($r->wallet_token, $r->wallet_token_secret, $r->wallet_certificate, $r->wallet_certificate_key);
                    $arrBilletInter['motivoCancelamento'] = 'ACERTOS';
                    $number = $r->finance_number;


                    $response = $bank077->downBillet($arrBilletInter, $number);

                    if ($response == '') {
                        return ['type' => 'ok', 'status' => 'BAIXADO'];
                    }
                    return ['type' => 'error', 'msg' => 'Tivemos problemas ao baixar'];
                }

                if ($r->wallet_bank == '403') {
                    $bank403 = new BoletoCora($r->wallet_token, $r->wallet_certificate, $r->wallet_certificate_key);
                    $number = $r->finance_codigo_solicitacao;


                    $response = $bank403->downBillet($number);

                    if ($response == '') {
                        return ['type' => 'ok', 'status' => 'BAIXADO'];
                    }
                    return ['type' => 'error', 'msg' => 'Tivemos problemas ao baixar'];
                }

                if ($r->wallet_bank == '756') {

                    $bank756 = new BoletoSicoob($r->wallet_token, $r->wallet_certificate, $r->wallet_pass_certificate);
                    $arrBilletSicoob['0']['numeroContrato'] = $r->wallet_beneficiary;
                    $arrBilletSicoob['0']['modalidade'] = '1';
                    $arrBilletSicoob['0']['nossoNumero'] = $r->finance_number;
                    $arrBilletSicoob['0']['seuNumero'] = $r->finance_reference;


                    $response = $bank756->downBillet($arrBilletSicoob);


                    if ($response->resultado['0']->status->codigo == '200') {
                        return ['type' => 'ok', 'status' => 'BAIXADO'];
                    }

                    if ($response->resultado['0']->status->codigo == '400') {
                        return ['type' => 'error', 'msg' => $response->resultado['0']->status->mensagem];
                    }

                    if ($response->resultado['0']->status->codigo == '406') {
                        return ['type' => 'error', 'msg' => $response->resultado['0']->status->mensagem];
                    }

                    return ['type' => 'error', 'msg' => $response->mensagens['0']->mensagem];
                }

                if ($r->wallet_bank == '748') {

                    $bank748 = new BoletoSicredi($r->wallet_token);

                    $arrBilletSicredi['agencia'] = $r->wallet_agency;
                    $arrBilletSicredi['posto'] = $r->wallet_post;
                    $arrBilletSicredi['cedente'] = $r->wallet_beneficiary;
                    $arrBilletSicredi['nossoNumero'] = $r->finance_number;
                    $arrBilletSicredi['instrucaoComando'] = 'PEDIDO_BAIXA';
                    $arrBilletSicredi['complementoInstrucao'] = null;


                    $response = $bank748->baixaBillet($arrBilletSicredi);


                    if ($response->codigo != 'E0029') {
                        return ['type' => 'ok', 'msg' => 'BAIXADO'];
                    }
                    if ($response == '') {
                        return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                    }

                    return ['type' => 'error', 'msg' => $response->mensagem];
                }
            }
        }
    }

    protected function editBilletBank($idFinance, $arrayEdit) {
        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_id')->is($idFinance)
            ->andWhere('finance_trash')->is(0)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();
        if ($result) {
            foreach ($result as $r) {
                if ($r->wallet_bank == '104') {
                    $bank104 = new BoletoCaixa($r->wallet_beneficiary, $r->client_doc);
                    $arrBilletCaixa['nossoNumero'] = $r->finance_number;
                    $arrBilletCaixa['informarValor'] = 'S';
                    $arrBilletCaixa['valor'] = number_format($r->finance_value, 2, '.', '');
                    $arrBilletCaixa['informarVencimento'] = 'S';
                    $arrBilletCaixa['dataVencimento'] = $r->finance_date_due;

                    $response = $bank104->editBillet($arrBilletCaixa);
                    if ($response->CONTROLE_NEGOCIAL->COD_RETORNO == '1') {
                        return ['type' => 'error', 'msg' => $response->CONTROLE_NEGOCIAL->MENSAGENS->RETORNO];
                    }

                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->ALTERA_BOLETO->LINHA_DIGITAVEL, 'codigo_barras' => $response->ALTERA_BOLETO->CODIGO_BARRAS];
                }

                if ($r->wallet_bank == '001') {
                    $bank001 = new BoletoBB($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_bb_dev_key);

                    $arrEdit['numeroConvenio'] = $r->wallet_beneficiary;
                    $arrEdit['indicadorNovaDataVencimento'] = 'N';
                    $arrEdit['indicadorNovoValorNominal'] = 'N';
                    $arrEdit['indicadorAtribuirDesconto'] = 'N';
                    $arrEdit['indicadorAlterarDesconto'] = 'N';
                    $arrEdit['indicadorAlterarDataDesconto'] = 'N';
                    $arrEdit['indicadorProtestar'] = 'N';
                    $arrEdit['indicadorSustacaoProtesto'] = 'N';
                    $arrEdit['indicadorCancelarProtesto'] = 'N';
                    $arrEdit['indicadorAlterarAbatimento'] = 'N';
                    $arrEdit['indicadorCobrarJuros'] = 'N';
                    $arrEdit['indicadorDispensarJuros'] = 'N';
                    $arrEdit['indicadorCobrarMulta'] = 'N';
                    $arrEdit['indicadorDispensarMulta'] = 'N';
                    $arrEdit['indicadorNegativar'] = 'N';
                    $arrEdit['indicadorAlterarSeuNumero'] = 'N';
                    $arrEdit['indicadorAlterarEnderecoPagador'] = 'N';
                    $arrEdit['indicadorAlterarPrazoBoletoVencido'] = 'N';

                    $nossoNumero = $r->finance_number;
                    if ($arrayEdit['usarAbatimento'] == 'S') {
                        $arrEdit['indicadorIncluirAbatimento']    = 'S';
                        $arrEdit['abatimento']['valorAbatimento'] = $arrayEdit['abatimento'];
                    } else {
                        $arrEdit['indicadorIncluirAbatimento'] = 'N';
                    }
                    if ($arrayEdit['usarDesconto'] == 'S') {
                        $arrEdit['indicadorAtribuirDesconto']    = 'S';
                        if ($arrayEdit['tipoDesconto'] == '1') {
                            $arrEdit['desconto']['tipoPrimeiroDesconto'] = '1';
                            $arrEdit['desconto']['valorPrimeiroDesconto'] = $arrayEdit['valorDesconto'];
                            $arrEdit['desconto']['dataPrimeiroDesconto'] = format_date_bb($arrayEdit['dataVencimento']);
                        } else {
                            $arrEdit['desconto']['tipoPrimeiroDesconto'] = '2';
                            $arrEdit['desconto']['percentualPrimeiroDesconto'] = $arrayEdit['valorDesconto'];
                            $arrEdit['desconto']['dataPrimeiroDesconto'] = format_date_bb($arrayEdit['dataVencimento']);
                        }


                        $arrEdit['desconto']['tipoPrimeiroDesconto'] = $arrayEdit['abatimento'];
                    } else {
                        $arrEdit['indicadorAtribuirDesconto'] = 'N';
                    }

                    $response = $bank001->editBillet($r->finance_number, $arrEdit);
                    if ($response->numeroContratoCobranca) {
                        return ['type' => 'ok', 'status' => 'REGISTRADABB'];
                    }
                    if (count($response->errors) > 0) {
                        return ['type' => 'error', 'msg' => $response->errors['0']->message];
                    }
                    //print_r($response);

                    return ['type' => 'error', 'msg' => 'Erro ao atualizar na instituição'];
                }
            }
        }
    }

    protected function registerBilletBank($idFinance, $arrayEdit) {
        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_id')->is($idFinance)
            ->andWhere('finance_trash')->is(0)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                if ($r->wallet_bank == '237') {
                    $bank237 = new BoletoBradesco($r->wallet_token, $r->wallet_certificate, $r->wallet_certificate_key);

                    $arrBilletBradesco = [
                        'registraTitulo' => '1',
                        'cdTipoAcesso' => '1',
                        'clubBanco' => '000000',
                        'cdTipoContrato' => '00',
                        'nuSequenciaContrato' => '0000000000',
                        'idProduto' => '09',
                        'nuNegociacao' => $r->wallet_agency . '00000000' . $r->wallet_bill,
                        'banco' => '237',
                        'nuSequenciaContrato2' => '0',
                        'tpRegistro' => '1',
                        'cdProduto' => '90',
                        'nuTitulo' => '',
                        'nuCliente' => $r->finance_our_number,
                        'dtEmissaoTitulo' => format_date_bb($r->finance_date_release),
                        'dtVencimentoTitulo' => format_date_bb($r->finance_date_due),
                        'tpVencimento' => '0',
                        'vlNominalTitulo' => intval($r->finance_value * 100),
                        'cdEspecieTitulo' => '18',
                        'controleParticipante' => 'BOLETO EMITIDO',
                        'cdPagamentoParcial' => 'N',
                        'qtdePagamentoParcial' => '000',
                        'vlIOF' => '00000000000000000',
                        'prazoDecurso' => '99'
                    ];

                    $arrBilletBradesco['nomePagador'] = $r->finance_name_pagador;
                    $arrBilletBradesco['logradouroPagador'] = $r->finance_address_pagador;
                    $arrBilletBradesco['nuLogradouroPagador'] = 'SN';
                    $arrBilletBradesco['complementoLogradouroPagador'] = '';
                    $arrBilletBradesco['cepPagador'] = substr($r->finance_zip_pagador, 0, 5);
                    $arrBilletBradesco['complementoCepPagador'] = substr($r->finance_zip_pagador, -3);
                    $arrBilletBradesco['bairroPagador'] = $r->finance_district_pagador;
                    $arrBilletBradesco['municipioPagador'] = $r->finance_city_pagador;
                    $arrBilletBradesco['ufPagador'] = $r->finance_state_pagador;
                    $arrBilletBradesco['cdIndCpfcnpjPagador'] = $r->finance_type_pagador;
                    $arrBilletBradesco['nuCpfcnpjPagador'] = $r->finance_doc_pagador;

                    $arrBilletBradesco['nuCPFCNPJ'] = '0' . substr($r->client_doc, 0, 8);
                    $arrBilletBradesco['filialCPFCNPJ'] = substr($r->client_doc, 8, 4);
                    $arrBilletBradesco['ctrlCPFCNPJ'] = substr($r->client_doc, -2);


                    $res = $bank237->registerBillet($arrBilletBradesco);

                    if ($res->codigoRetorno == 0) {
                        return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $res->linhaDigitavel, 'codigo_barras' => linhaDigitavelParaCodigoBarras($res->linhaDigitavel), 'nossoNumero' => 0];
                    }

                    return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                }
                if ($r->wallet_bank == '001') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }

                    if ($r->finance_id_company == '13') {
                        $numeroTitulo = '000' . $r->wallet_beneficiary . str_pad($r->finance_number, 10, '0', STR_PAD_LEFT);
                    } else {
                        $numeroTitulo = '000' . $r->wallet_beneficiary . str_pad($r->finance_number, 10, '0', STR_PAD_LEFT);
                    }

                    $arrBilletBB = [
                        'numeroConvenio' => $r->wallet_beneficiary,
                        'numeroCarteira' => $r->wallet_code,
                        'numeroVariacaoCarteira' => $r->wallet_variation,
                        'codigoModalidade' => '01',
                        'dataEmissao' => format_date_bb($r->finance_date_release),
                        'dataVencimento' => format_date_bb($r->finance_date_due),
                        'valorOriginal' => number_format($r->finance_value, 2, '.', ""),
                        'indicadorAceiteTituloVencido' => 'S',
                        'numeroDiasLimiteRecebimento' => $r->wallet_day_devolution,
                        'codigoAceite' => $r->wallet_accept,
                        'indicadorPermissaoRecebimentoParcial' => 'N',
                        'numeroTituloBeneficiario' => $numeroTitulo,
                        'campoUtilizacaoBeneficiario' => $r->wallet_informative,
                        'numeroTituloCliente' => $numeroTitulo,
                        'campoUtilizacaoBeneficiario' => $r->wallet_informative,
                    ];

                    $arrBilletBB['pagador'] = [
                        'tipoInscricao' => $r->finance_type_pagador,
                        'numeroInscricao' => $r->finance_doc_pagador,
                        'nome' => $r->finance_name_pagador,
                        'endereco' => $r->finance_address_pagador,
                        'cep' => $r->finance_zip_pagador,
                        'cidade' => $r->finance_city_pagador,
                        'bairro' => $r->finance_district_pagador,
                        'uf' => $r->finance_state_pagador,
                        'telefone' => $r->finance_telefone_pagador
                    ];

                    if ($r->wallet_type_fees == '0') {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    } elseif ($r->wallet_type_fees == '1') {
                        $typeFees = '2';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['porcentagem'] = $r->wallet_fees;
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = '1';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['valor'] = $r->wallet_fees;
                    } else {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    }
                    if ($r->wallet_type_penalty == '0') {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    } elseif ($r->wallet_type_penalty == '1') {
                        $typePenalty = '2';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['porcentagem'] = $r->wallet_penalty;
                    } elseif ($r->wallet_type_penalty == '2') {
                        $typePenalty = '1';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['valor'] = $r->wallet_penalty;
                    } else {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    }

                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletBB['valorAbatimento'] = $r->wallet_value_discount_anticipated;
                    }

                    $arrBilletBB['indicadorPix'] = 'N';
                    if ($r->wallet_hybrid == '0') {
                        $arrBilletBB['indicadorPix'] = 'S';
                    }

                    if ($arrayEdit['usarDesconto'] == 'S') {
                        $arrBilletBB['desconto']['porcentagem'] = $r->wallet_value_discount1;
                        $arrBilletBB['desconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                        if ($arrayEdit['tipoDesconto'] == '1') {
                            $arrBilletBB['desconto']['tipo'] = '1';
                            $arrBilletBB['desconto']['valor'] = $arrayEdit['valorDesconto'];
                            $arrBilletBB['desconto']['dataExpiracao'] = format_date_bb($arrayEdit['dataVencimento']);
                        } else {
                            $arrBilletBB['desconto']['tipo'] = '2';
                            $arrBilletBB['desconto']['porcentagem'] = $arrayEdit['valorDesconto'];
                            $arrBilletBB['desconto']['dataExpiracao'] = format_date_bb($arrayEdit['dataVencimento']);
                        }
                    } else {
                        if ($r->wallet_inform_discount1 == 'S') {
                            $arrBilletBB['desconto']['tipo'] = '2';
                            $arrBilletBB['desconto']['porcentagem'] = $r->wallet_value_discount1;
                            $arrBilletBB['desconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount2 == 'S') {
                            $arrBilletBB['segundoDesconto']['porcentagem'] = $r->wallet_value_discount2;
                            $arrBilletBB['segundoDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount3 == 'S') {
                            $arrBilletBB['terceiroDesconto']['porcentagem'] = $r->wallet_value_discount3;
                            $arrBilletBB['terceiroDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount_anticipated == 'S') {
                            $arrBilletBB['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                        }
                    }




                    if ($r->finance_ra == 'S') {
                        $bank001 = new PixBB($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_bb_dev_key, $r->wallet_certificate, $r->wallet_certificate_key);

                        $dataInicial = new DateTime(date('Y-m-d H:i:s'));
                        $dataFinal = new DateTime($r->finance_date_due . ' ' . $r->finance_time . ':00');

                        $timestampInicial = $dataInicial->getTimestamp();
                        $timestampFinal = $dataFinal->getTimestamp();

                        $diferencaEmSegundos = $timestampFinal - $timestampInicial;

                        $arrPix = [
                            'calendario' => [
                                'expiracao' => $diferencaEmSegundos
                            ],
                            'valor' => [
                                'original' => number_format($r->finance_value, 2, '.', '')
                            ],
                            'chave' => $r->wallet_key_pix,
                            'solicitacaoPagador' => $r->finance_description
                        ];


                        if (clearDoc($r->finance_doc_pagador) != '') {
                            if (strlen(clearDoc($r->finance_doc_pagador)) == '11') {
                                $arrPix['devedor']['cpf'] = clearDoc($r->finance_doc_pagador);
                                $arrPix['devedor']['nome'] = $r->finance_name_pagador;
                            }

                            if (strlen(clearDoc($r->finance_doc_pagador)) == '14') {
                                $arrPix['devedor']['cnpj'] = clearDoc($r->finance_doc_pagador);
                                $arrPix['devedor']['nome'] = $r->finance_name_pagador;
                            }
                        }

                        $txtid = md5(date('Y-m-dH:i:s') . rand(100, 999));

                        $response = $bank001->registerPix($arrPix, $txtid);

                        //print_r($response);


                        if ($response->httpStatus) {
                            return ['type' => 'error', 'msg' => $response->details];
                        }
                        if ($response->detail) {
                            if ($response->detail == 'O campo cod. adicional não é válido.') {
                                $response->detail = 'Os campos adicionais estão nulos';
                            }
                            return ['type' => 'error', 'msg' => $response->detail];
                        }

                        if (isset($response->txid)) {
                            return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => $response->location, 'txtid' => $txtid, 'linha_digitavel' => '', 'codigo_barras' => '', 'nossoNumero' => ''];
                        }

                        return ['type' => 'error', 'msg' => 'Tivemos um problema'];
                    } /*elseif ($r->finance_ra == 'N' && $r->finance_id_company == '13') {
                        return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => '', 'txtid' => '', 'linha_digitavel' => '00000.00000 00000.000000 00000.000000 0 00000000000000', 'codigo_barras' => '00000000000000000000000000000000000000000000'];
                    }*/ else {
                        $bank001 = new BoletoBB($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_bb_dev_key);


                        $response = $bank001->registerBillet($arrBilletBB);

                        //print_r($response);

                        if (count($response->erros) > 0) {
                            return ['type' => 'error', 'msg' => $response->erros['0']->mensagem];
                        }

                        if ($response->error) {
                            return ['type' => 'error', 'msg' => $response->message];
                        }

                        return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => $response->qrCode->url, 'txtid' => $response->qrCode->txId, 'linha_digitavel' => $response->linhaDigitavel, 'codigo_barras' => $response->codigoBarraNumerico, 'nossoNumero' => $response->numero, 'copiaCola' => $response->qrCode->emv];
                    }
                }

                if ($r->wallet_bank == '999') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }
                    if ($r->wallet_type_fees == '0') {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    } elseif ($r->wallet_type_fees == '1') {
                        $typeFees = '2';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['porcentagem'] = $r->wallet_fees;
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = '1';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['valor'] = $r->wallet_fees;
                    } else {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    }
                    if ($r->wallet_type_penalty == '0') {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    } elseif ($r->wallet_type_penalty == '1') {
                        $typePenalty = '2';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['porcentagem'] = $r->wallet_penalty;
                    } elseif ($r->wallet_type_penalty == '2') {
                        $typePenalty = '1';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['valor'] = $r->wallet_penalty;
                    } else {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    }

                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletBB['valorAbatimento'] = $r->wallet_value_discount_anticipated;
                    }

                    $arrBilletBB['indicadorPix'] = 'N';
                    if ($r->wallet_hybrid == '0') {
                        $arrBilletBB['indicadorPix'] = 'S';
                    }


                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletBB['desconto']['tipo'] = '2';
                        $arrBilletBB['desconto']['porcentagem'] = $r->wallet_value_discount1;
                        $arrBilletBB['desconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletBB['segundoDesconto']['porcentagem'] = $r->wallet_value_discount2;
                        $arrBilletBB['segundoDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletBB['terceiroDesconto']['porcentagem'] = $r->wallet_value_discount3;
                        $arrBilletBB['terceiroDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletBB['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                    }

                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => '', 'txtid' => '', 'linha_digitavel' => '00000.00000 00000.000000 00000.000000 0 00000000000000', 'codigo_barras' => '00000000000000000000000000000000000000000000'];
                }

                if ($r->wallet_bank == '748') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    //$replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'DUPLICATA_MERCANTIL_INDICACAO';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }
                    if ($r->wallet_type_fees == '0' || $r->wallet_type_fees == '1') {
                        $typeFees = 'B';
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = 'A';
                    } else {
                        $typeFees = 'B';
                    }
                    if ($r->wallet_type_penalty == '0' || $r->wallet_type_penalty == '1') {
                        $typePenalty = 'B';
                    } elseif ($r->wallet_type_penalty == '2') {
                        $typePenalty = 'A';
                    } else {
                        $typePenalty = 'B';
                    }

                    if ($typePenalty == 'B') {
                        $valuePenalty = ($r->wallet_penalty / 100) * $r->finance_value;
                    }
                    $valuePenalty = $r->wallet_penalty;

                    $seuNumeroTratato = preg_replace('/\D/', '', $r->finance_our_number);
                    $arrBilletSicredi = [
                        'agencia' => $r->wallet_agency,
                        'posto' => $r->wallet_post,
                        'cedente' => $r->wallet_beneficiary,
                        'tipoPessoa' => $r->finance_type_pagador,
                        'cpfCnpj' => $r->finance_doc_pagador,
                        'nome' => substr($r->finance_name_pagador, 0, 60),
                        'endereco' => $r->finance_address_pagador,
                        'cidade' => $r->finance_city_pagador,
                        'uf' => $r->finance_state_pagador,
                        'cep' => clearDoc($r->finance_zip_pagador),
                        'telefone' => clearDoc($r->finance_telefone_pagador),
                        'email' => $r->finance_email_pagador,
                        'especieDocumento' => $replaceSpecie,
                        'seuNumero' => $seuNumeroTratato,
                        'dataVencimento' => format_date_br($r->finance_date_due),
                        'valor' => $r->finance_value,
                        'tipoDesconto' => $typeDiscount,
                        'multas' => $valuePenalty,
                        'informativo' => $r->wallet_informative,
                        'numDiasNegativacaoAuto' => $r->wallet_day_protest
                    ];
                    if ($r->finance_number != '0') {
                        $arrBilletSicredi['nossoNumero'] = $r->finance_number;
                    }

                    $arrBilletSicredi['tipoJuros'] = $typeFees;
                    $arrBilletSicredi['juros'] = $r->wallet_fees;

                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletSicredi['valorDesconto1'] = $r->wallet_value_discount1;
                        $arrBilletSicredi['dataDesconto1'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletSicredi['valorDesconto2'] = $r->wallet_value_discount2;
                        $arrBilletSicredi['dataDesconto2'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletSicredi['valorDesconto3'] = $r->wallet_value_discount3;
                        $arrBilletSicredi['dataDesconto3'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletSicredi['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                    }

                    if ($r->wallet_versao == 'v2') {
                        $bank748 = new BoletoSicrediHibrido($r->wallet_token, $r->wallet_token_secret, $r->wallet_beneficiary, $r->wallet_agency, $r->wallet_post);

                        if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                            $typeDiscount = 'B';
                        } elseif ($r->wallet_type_discount == '2') {
                            $typeDiscount = 'A';
                        } else {
                            $typeDiscount = 'B';
                        }
                        if ($r->wallet_type_fees == '0' || $r->wallet_type_fees == '1') {
                            $typeFees = 'PERCENTUAL';
                        } elseif ($r->wallet_type_fees == '2') {
                            $typeFees = 'VALOR';
                        } else {
                            $typeFees = 'PERCENTUAL';
                        }
                        if ($r->wallet_type_penalty == '0' || $r->wallet_type_penalty == '1') {
                            $typePenalty = 'PERCENTUAL';
                        } elseif ($r->wallet_type_penalty == '2') {
                            $typePenalty = 'VALOR';
                        } else {
                            $typePenalty = 'B';
                        }

                        if ($typePenalty == 'B') {
                            $valuePenalty = ($r->wallet_penalty / 100) * $r->finance_value;
                        }
                        $valuePenalty = $r->wallet_penalty;

                        $seuNumeroTratato = preg_replace('/\D/', '', $r->finance_our_number);
                        $arrBilletSicrediV2 = [
                            'tipoCobranca' => 'HIBRIDO',
                            'codigoBeneficiario' => $r->wallet_beneficiary,
                            'especieDocumento' => $replaceSpecie,
                            'seuNumero' => $seuNumeroTratato,
                            'dataVencimento' => $r->finance_date_due,
                            'valor' => $r->finance_value,
                            'informativo' => $r->wallet_informative,
                            'agencia' => $r->wallet_agency,
                            'posto' => $r->wallet_post,
                        ];
                        if ($valuePenalty < 0) {
                            $arrBilletSicrediV2['multa'] = $valuePenalty;
                        }
                        if ($r->finance_type_pagador == '1') {
                            $tipoPagador = 'PESSOA_FISICA';
                        } else {
                            $tipoPagador = 'PESSOA_JURIDICA';
                        }
                        $arrRetiraDados = ['.', '(', ')', '-'];
                        $arrColocaDados = ['', '', '', ''];
                        $arrBilletSicrediV2['pagador'] = [
                            'tipoPessoa' => $tipoPagador,
                            'documento' => $r->finance_doc_pagador,
                            'nome' => substr($r->finance_name_pagador, 0, 60),
                            'endereco' => substr($r->finance_address_pagador),
                            'cidade' => $r->finance_city_pagador,
                            'uf' => $r->finance_state_pagador,
                            'cep' => clearDoc($r->finance_zip_pagador),
                            'telefone' => str_replace($arrRetiraDados, $arrColocaDados, $r->finance_telefone_pagador),
                            'email' => $r->finance_email_pagador,
                        ];
                        if ($r->finance_number != '0') {
                            $arrBilletSicrediV2['nossoNumero'] = $r->finance_number;
                        }

                        $arrBilletSicrediV2['tipoJuros'] = $typeFees;
                        $arrBilletSicrediV2['juros'] = $r->wallet_fees;

                        if ($r->wallet_inform_discount1 == 'S') {
                            $arrBilletSicrediV2['tipoDesconto'] = $typeDiscount;
                            $arrBilletSicrediV2['valorDesconto1'] = $r->wallet_value_discount1;
                            $arrBilletSicrediV2['dataDesconto1'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount2 == 'S') {
                            $arrBilletSicrediV2['valorDesconto2'] = $r->wallet_value_discount2;
                            $arrBilletSicrediV2['dataDesconto2'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount3 == 'S') {
                            $arrBilletSicrediV2['valorDesconto3'] = $r->wallet_value_discount3;
                            $arrBilletSicrediV2['dataDesconto3'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                        }
                        if ($r->wallet_inform_discount_anticipated == 'S') {
                            $arrBilletSicrediV2['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                        }
                        $response = $bank748->registerBillet($arrBilletSicrediV2);
                        //print_r($response);


                        if ($response->txid) {
                            $explodeURL = explode('pix-qrcode.sicredi.com.br/qr/v2/cobv/', $response->qrCode);
                            $codPix = substr($explodeURL['1'], 0, 32);
                            $locationSicredi = 'pix-qrcode.sicredi.com.br/qr/v2/cobv/' . $codPix;
                            return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->linhaDigitavel, 'codigo_barras' => $response->codigoBarras, 'nossoNumero' => $response->nossoNumero, 'txtid' => $response->txid, 'copiaCola' => $response->qrCode, 'location' => $locationSicredi];
                        }
                        if ($response->code) {
                            return ['type' => 'error', 'msg' => $response->message . ' - [' . $response->parametro . ']'];
                        }

                        if ($response == '') {
                            return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                        }

                        return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                    } else {
                        $bank748 = new BoletoSicredi($r->wallet_token);

                        $response = $bank748->registerBillet($arrBilletSicredi);
                        //print_r($response);

                        if ($response->httpStatus) {
                            return ['type' => 'error', 'msg' => $response->details];
                        }
                        if ($response->mensagem) {
                            return ['type' => 'error', 'msg' => $response->mensagem . ' - [' . $response->parametro . ']'];
                        }

                        if ($response == '') {
                            return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                        }

                        return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->linhaDigitavel, 'codigo_barras' => $response->codigoBarra, 'nossoNumero' => $response->nossoNumero];
                    }
                }

                if ($r->wallet_bank == '756') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }
                    $replaceSpecie = 'A';

                    $arrBilletSicoob = [
                        'numeroContrato' => $r->wallet_beneficiary,
                        'modalidade' => '1',
                        'numeroContaCorrente' => $r->wallet_bill . $r->wallet_bill_dv,
                        'especieDocumento' => $r->wallet_species_document,
                        'seuNumero' => $r->finance_our_number,
                        'identificacaoBoletoEmpresa' => $r->finance_reference,
                        'identificacaoEmissaoBoleto' => '2',
                        'identificacaoDistribuicaoBoleto' => '2',
                        'valor' => $r->finance_value,
                        'dataVencimento' => $r->finance_date_due . 'T00:00:00-03:00',
                        'dataEmissao' => date('Y-m-d') . 'T00:00:00-03:00',
                        'numeroParcela' => '1'
                    ];

                    if ($r->wallet_type_discount == '0') {
                        $typeDiscount = '0';
                    } elseif ($r->wallet_type_discount == '1') {
                        $typeDiscount = '2';
                    } else {
                        $typeDiscount = '0';
                    }
                    if ($r->wallet_type_fees == '0') {
                        $arrBilletSicoob['tipoJurosMora'] = '3';
                    } elseif ($r->wallet_type_fees == '1') {
                        $arrBilletSicoob['tipoJurosMora'] = '2';
                        $arrBilletSicoob['dataJurosMora'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataJurosMora'] = $arrBilletSicoob['dataJurosMora'] . 'T00:00:00-03:00';
                        $arrBilletSicoob['valorJurosMora'] = ($r->wallet_fees * 30);
                    } elseif ($r->wallet_type_fees == '2') {
                        $arrBilletSicoob['tipoJurosMora'] = '1';
                        $arrBilletSicoob['dataJurosMora'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataJurosMora'] = $arrBilletSicoob['dataJurosMora'] . 'T00:00:00-03:00';
                        $arrBilletSicoob['valorJurosMora'] = $r->wallet_fees;
                    } else {
                        $arrBilletSicoob['tipoJurosMora'] = '3';
                    }

                    if ($r->wallet_type_penalty == '0') {
                        $arrBilletSicoob['tipoMulta'] = '0';
                    } elseif ($r->wallet_type_penalty == '1') {
                        $arrBilletSicoob['tipoMulta'] = '2';
                        $arrBilletSicoob['dataMulta'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataMulta'] = $arrBilletSicoob['dataMulta'] . 'T00:00:00-03:00';
                        $arrBilletSicoob['valorMulta'] = $r->wallet_penalty;
                    } elseif ($r->wallet_type_penalty == '2') {
                        $arrBilletSicoob['tipoMulta'] = '1';
                        $arrBilletSicoob['dataMulta'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataMulta'] = $arrBilletSicoob['dataMulta'] . 'T00:00:00-03:00';
                        $arrBilletSicoob['valorMulta'] = $r->wallet_penalty;
                    } else {
                        $arrBilletSicoob['tipoMulta'] = '0';
                    }


                    $arrBilletSicoob['codigoCadastrarPIX'] = '0';
                    if ($r->wallet_hybrid == '0') {
                        $arrBilletSicoob['codigoCadastrarPIX'] = '1';
                    }

                    $arrBilletSicoob['pagador']['numeroCpfCnpj'] = $r->finance_doc_pagador;
                    $arrBilletSicoob['pagador']['nome'] = $r->finance_name_pagador;
                    $arrBilletSicoob['pagador']['endereco'] = $r->finance_address_pagador;
                    $arrBilletSicoob['pagador']['bairro'] = $r->finance_district_pagador;
                    $arrBilletSicoob['pagador']['cidade'] = $r->finance_city_pagador;
                    $arrBilletSicoob['pagador']['cep'] = clearDoc($r->finance_zip_pagador);
                    $arrBilletSicoob['pagador']['uf'] = $r->finance_state_pagador;
                    $arrBilletSicoob['pagador']['email']['0'] = $r->finance_email_pagador;

                    $arrBilletSicoob['tipoDesconto'] = $typeDiscount;

                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletSicoob['valorPrimeiroDesconto'] = $r->wallet_value_discount1;
                        $arrBilletSicoob['dataPrimeiroDesconto'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataPrimeiroDesconto'] = $arrBilletSicoob['dataPrimeiroDesconto'] . 'T00:00:00-03:00';
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletSicoob['valorSegundoDesconto'] = $r->wallet_value_discount2;
                        $arrBilletSicoob['dataSegundoDesconto'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataSegundoDesconto'] = $arrBilletSicoob['dataSegundoDesconto'] . 'T00:00:00-03:00';
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletSicoob['valorTerceiroDesconto'] = $r->wallet_value_discount3;
                        $arrBilletSicoob['dataTerceiroDesconto'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                        $arrBilletSicoob['dataTerceiroDesconto'] = $arrBilletSicoob['dataTerceiroDesconto'] . 'T00:00:00-03:00';
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletSicoob['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                    }

                    $bank756 = new BoletoSicoob($r->wallet_token, $r->wallet_certificate, $r->wallet_pass_certificate);

                    $arrBilletSicoob['gerarPdf'] = true;

                    $inf['0'] = $arrBilletSicoob;

                    $response = $bank756->registerBillet($inf);

                    if ($response->resultado['0']->boleto->pdfBoleto) {
                        $nameArq = $bank756->createPDF($response->resultado['0']->boleto->pdfBoleto);
                    }

                    if ($response->resultado['0']->status->codigo != '200') {
                        return ['type' => 'error', 'msg' => $response->resultado['0']->status->mensagem];
                    }

                    if ($response->httpStatus) {
                        return ['type' => 'error', 'msg' => $response->details];
                    }
                    if ($response->mensagem) {
                        return ['type' => 'error', 'msg' => $response->mensagem];
                    }

                    if ($response == '') {
                        return ['type' => 'error', 'msg' => 'Tivemos problemas de autenticação'];
                    }

                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->resultado['0']->boleto->linhaDigitavel, 'codigo_barras' => $response->resultado['0']->boleto->codigoBarras, 'nossoNumero' => $response->resultado['0']->boleto->nossoNumero, 'link' => URL_BASE . '/shared/impressao/' . $nameArq];
                }

                if ($r->wallet_bank == '077') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }


                    if ($r->finance_type_pagador == '1') {
                        $arrBilletInter['pagador']['tipoPessoa'] = 'FISICA';
                    } else {
                        $arrBilletInter['pagador']['tipoPessoa'] = 'JURIDICA';
                    }

                    $arrBilletInter['pagador']['cpfCnpj'] = $r->finance_doc_pagador;
                    $arrBilletInter['pagador']['nome'] = $r->finance_name_pagador;
                    $arrBilletInter['pagador']['endereco'] = $r->finance_address_pagador;
                    $arrBilletInter['pagador']['cidade'] = $r->finance_city_pagador;
                    $arrBilletInter['pagador']['uf'] = $r->finance_state_pagador;
                    $arrBilletInter['pagador']['cep'] = clearDoc($r->finance_zip_pagador);

                    $arrBilletInter['seuNumero'] = $r->finance_our_number;
                    $arrBilletInter['valorNominal'] = $r->finance_value;
                    $arrBilletInter['dataVencimento'] = $r->finance_date_due;
                    $arrBilletInter['numDiasAgenda'] = '60';

                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletInter['desconto1']['codigoDesconto'] = 'PERCENTUALDATAINFORMADA';
                        $arrBilletInter['desconto1']['valor'] = $r->wallet_value_discount1;
                        $arrBilletInter['desconto1']['data'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletInter['desconto2']['codigoDesconto'] = 'PERCENTUALDATAINFORMADA';
                        $arrBilletInter['desconto2']['valor'] = $r->wallet_value_discount2;
                        $arrBilletInter['desconto2']['data'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletInter['desconto3']['codigoDesconto'] = 'PERCENTUALDATAINFORMADA';
                        $arrBilletInter['desconto3']['valor'] = $r->wallet_value_discount3;
                        $arrBilletInter['desconto3']['data'] = date('Y-m-d', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                    }


                    if ($r->wallet_hybrid == '0') {
                        if ($r->wallet_type_fees == '0') {
                        } elseif ($r->wallet_type_fees == '1') {
                            $arrBilletInter['mora']['codigo'] = 'TAXAMENSAL';
                            $arrBilletInter['mora']['taxa'] = number_format($r->wallet_fees * 30, 2);
                        } elseif ($r->wallet_type_fees == '2') {
                            $arrBilletInter['mora']['codigo'] = 'VALORDIA';
                            $arrBilletInter['mora']['valor'] = number_format($r->wallet_fees, 2);
                        } else {
                        }

                        if ($r->wallet_type_penalty == '0') {
                        } elseif ($r->wallet_type_penalty == '1') {
                            $arrBilletInter['multa']['codigo'] = 'PERCENTUAL';
                            $arrBilletInter['multa']['taxa'] = number_format($r->wallet_penalty, 2);
                        } elseif ($r->wallet_type_penalty == '2') {
                            $arrBilletInter['multa']['codigo'] = 'VALORFIXO';
                            $arrBilletInter['multa']['taxa'] = number_format($r->wallet_penalty, 2);
                        } else {
                        }
                        if ($r->finance_ra == 'S') {
                            $bank077 = new PixInter($r->wallet_bb_client_id, $r->wallet_bb_client_secret, $r->wallet_certificate, $r->wallet_certificate_key);

                            $dataInicial = new DateTime(date('Y-m-d H:i:s'));
                            $dataFinal = new DateTime($r->finance_date_due . ' ' . $r->finance_time . ':00');

                            $timestampInicial = $dataInicial->getTimestamp();
                            $timestampFinal = $dataFinal->getTimestamp();

                            $diferencaEmSegundos = $timestampFinal - $timestampInicial;

                            $arrPix = [
                                'calendario' => [
                                    'expiracao' => $diferencaEmSegundos
                                ],
                                'valor' => [
                                    'original' => number_format($r->finance_value, 2, '.', '')
                                ],
                                'chave' => $r->wallet_key_pix,
                                'solicitacaoPagador' => $r->finance_description
                            ];


                            if (clearDoc($r->finance_doc_pagador) != '') {
                                if (strlen(clearDoc($r->finance_doc_pagador)) == '11') {
                                    $arrPix['devedor']['cpf'] = clearDoc($r->finance_doc_pagador);
                                    $arrPix['devedor']['nome'] = $r->finance_name_pagador;
                                }

                                if (strlen(clearDoc($r->finance_doc_pagador)) == '14') {
                                    $arrPix['devedor']['cnpj'] = clearDoc($r->finance_doc_pagador);
                                    $arrPix['devedor']['nome'] = $r->finance_name_pagador;
                                }
                            }

                            $txtid = md5(date('Y-m-dH:i:s') . rand(100, 999));

                            $response = $bank077->registerPix($arrPix, $txtid);

                            //print_r($response);


                            if (count($response->violacoes) > '0') {
                                return ['type' => 'error', 'msg' => $response->violacoes['0']->razao];
                            }
                            if ($response->message) {
                                return ['type' => 'error', 'msg' => $response->message];
                            }
                            if ($response == '') {
                                return ['type' => 'error', 'msg' => 'Houve um problema de autenticação, verifique as credênciais e certificado'];
                            }

                            if (isset($response->status)) {
                                return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => substr($response->location, 8), 'txtid' => $txtid, 'linha_digitavel' => '', 'codigo_barras' => '', 'nossoNumero' => ''];
                            }

                            return ['type' => 'error', 'msg' => 'Tivemos um problema'];
                        } else {
                            $bank077 = new BoletoInterHibrido($r->wallet_token, $r->wallet_token_secret, $r->wallet_certificate, $r->wallet_certificate_key);

                            $response = $bank077->registerBillet($arrBilletInter);
                            //print_r($response);


                            if (isset($response->violacoes)) {
                                return ['type' => 'error', 'msg' => 'Tivemos um problema de autenticação'];
                            }

                            if ($response->codigoSolicitacao) {
                                $pdf = $bank077->printBillet($response->codigoSolicitacao);
                                $nameArq = $bank077->createPDF($pdf->pdf);
                                $resBillet = $bank077->readBillet($response->codigoSolicitacao);
                            }

                            return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $resBillet->boleto->linhaDigitavel, 'codigo_barras' => $resBillet->boleto->codigoBarras, 'nossoNumero' => $resBillet->boleto->nossoNumero, 'link' => URL_BASE . '/shared/impressao/' . $nameArq, 'txtid' => $resBillet->pix->pixCopiaECola, 'codigoSolicitacao' => $response->codigoSolicitacao];
                        }
                    } else {

                        if ($r->wallet_type_fees == '0') {
                            $arrBilletInter['mora']['codigoMora'] = 'ISENTO';
                            $arrBilletInter['mora']['taxa'] = '0';
                            $arrBilletInter['mora']['valor'] = '0';
                        } elseif ($r->wallet_type_fees == '1') {
                            $arrBilletInter['mora']['codigoMora'] = 'TAXAMENSAL';
                            $arrBilletInter['mora']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                            $arrBilletInter['mora']['taxa'] = number_format($r->wallet_fees * 30, 2);
                            $arrBilletInter['mora']['valor'] = '0';
                        } elseif ($r->wallet_type_fees == '2') {
                            $arrBilletInter['mora']['codigoMora'] = 'VALORDIA';
                            $arrBilletInter['mora']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                            $arrBilletInter['mora']['valor'] = number_format($r->wallet_fees, 2);
                        } else {
                            $arrBilletInter['mora']['codigoMora'] = 'ISENTO';
                            $arrBilletInter['mora']['taxa'] = '0';
                            $arrBilletInter['mora']['valor'] = '0';
                        }

                        if ($r->wallet_type_penalty == '0') {
                            $arrBilletInter['multa']['codigoMulta'] = 'NAOTEMMULTA';
                            $arrBilletInter['multa']['taxa'] = '0';
                            $arrBilletInter['multa']['valor'] = '0';
                        } elseif ($r->wallet_type_penalty == '1') {
                            $arrBilletInter['multa']['codigoMulta'] = 'PERCENTUAL';
                            $arrBilletInter['multa']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                            $arrBilletInter['multa']['taxa'] = number_format($r->wallet_penalty, 2);
                            $arrBilletInter['multa']['valor'] = '0';
                        } elseif ($r->wallet_type_penalty == '2') {
                            $arrBilletInter['multa']['codigoMulta'] = 'VALORFIXO';
                            $arrBilletInter['multa']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                            $arrBilletInter['multa']['valor'] = number_format($r->wallet_penalty, 2);
                        } else {
                            $arrBilletInter['multa']['codigoMulta'] = 'NAOTEMMULTA';
                            $arrBilletInter['multa']['taxa'] = '0';
                            $arrBilletInter['multa']['valor'] = '0';
                        }
                        $bank077 = new BoletoInter($r->wallet_token, $r->wallet_token_secret, $r->wallet_certificate, $r->wallet_certificate_key);
                        $response = $bank077->registerBillet($arrBilletInter);

                        //print_r($response);


                        if ($response == '') {
                            return ['type' => 'error', 'msg' => 'Tivemos um problema de autenticação'];
                        }
                        if ($response->message) {
                            return ['type' => 'error', 'msg' => $response->message];
                        }
                        if ($response->title) {
                            return ['type' => 'error', 'msg' => $response->detail];
                        }

                        if ($response->detail) {
                            return ['type' => 'error', 'msg' => $response->violacoes[0]->razao];
                        }


                        if ($response->nossoNumero) {
                            $pdf = $bank077->printBillet($response->nossoNumero);
                            $nameArq = $bank077->createPDF($pdf->pdf);
                        }

                        return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->linhaDigitavel, 'codigo_barras' => $response->codigoBarras, 'nossoNumero' => $response->nossoNumero, 'link' => URL_BASE . '/shared/impressao/' . $nameArq];
                    }
                }

                if ($r->wallet_bank == '403') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }


                    if ($r->finance_type_pagador == '1') {
                        $arrBilletCora['customer']['document']['type'] = 'CPF';
                    } else {
                        $arrBilletCora['customer']['document']['type'] = 'CNPJ';
                    }

                    $arrBilletCora['customer']['document']['identity'] = $r->finance_doc_pagador;
                    $arrBilletCora['customer']['name'] = $r->finance_name_pagador;
                    $arrBilletCora['customer']['email'] = $r->finance_email_pagador;
                    $arrBilletCora['customer']['address']['street'] = $r->finance_address_pagador;
                    $arrBilletCora['customer']['address']['number'] = $r->finance_number_pagador;
                    $arrBilletCora['customer']['address']['district'] = $r->finance_district_pagador;
                    $arrBilletCora['customer']['address']['complement'] = $r->finance_complement_pagador;
                    $arrBilletCora['customer']['address']['city'] = $r->finance_city_pagador;
                    $arrBilletCora['customer']['address']['state'] = $r->finance_state_pagador;
                    $arrBilletCora['customer']['address']['zip_code'] = clearDoc($r->finance_zip_pagador);

                    $arrBilletCora['services']['0']['amount'] = $r->finance_value * 100;
                    $arrBilletCora['services']['0']['name'] = $r->finance_description;
                    $arrBilletCora['services']['0']['description'] = $r->finance_description;

                    $arrBilletCora['payment_terms']['due_date'] = $r->finance_date_due;

                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletCora['payment_terms']['discount']['type'] = 'FIXED';
                        $arrBilletCora['payment_terms']['discount']['value'] = $r->wallet_value_discount1;
                    }

                    $arrBilletCora['payment_forms']['0'] = 'BANK_SLIP';
                    $arrBilletCora['payment_forms']['1'] = 'PIX';


                    if ($r->wallet_type_fees == '0') {
                        /*$arrBilletInter['mora']['codigoMora'] = 'ISENTO';
                        $arrBilletInter['mora']['taxa'] = '0';
                        $arrBilletInter['mora']['valor'] = '0';*/
                    } elseif ($r->wallet_type_fees == '1') {
                        //$arrBilletCora['payment_terms']['codigoMora'] = 'TAXAMENSAL';
                        //$arrBilletCora['payment_terms']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletCora['payment_terms']['interest']['rate'] = number_format($r->wallet_fees * 30, 2);
                        //$arrBilletCora['payment_terms']['interest'] = '0';
                    } elseif ($r->wallet_type_fees == '2') {
                        //$arrBilletCora['payment_terms']['codigoMora'] = 'VALORDIA';
                        //$arrBilletCora['payment_terms']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletCora['payment_terms']['interest']['rate'] = number_format($r->wallet_fees * 30, 2);
                    } else {
                        /*$arrBilletInter['payment_terms']['codigoMora'] = 'ISENTO';
                        $arrBilletInter['mora']['taxa'] = '0';
                        $arrBilletInter['mora']['valor'] = '0';*/
                    }

                    if ($r->wallet_type_penalty == '0') {
                        /*$arrBilletCora['fine']['codigoMulta'] = 'NAOTEMMULTA';
                        $arrBilletCora['fine']['taxa'] = '0';
                        $arrBilletCora['fine']['valor'] = '0';*/
                    } elseif ($r->wallet_type_penalty == '1') {
                        //$arrBilletCora['fine']['codigoMulta'] = 'PERCENTUAL';
                        //$arrBilletCora['fine']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        //$arrBilletCora['fine']['taxa'] = number_format($r->wallet_penalty, 2);
                        $arrBilletCora['payment_terms']['fine']['rate'] = number_format($r->wallet_penalty, 2);
                    } elseif ($r->wallet_type_penalty == '2') {
                        //$arrBilletCora['fine']['codigoMulta'] = 'VALORFIXO';
                        //$arrBilletCora['fine']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletCora['payment_terms']['fine']['amount'] = $r->wallet_penalty * 100;
                    } else {
                        /*$arrBilletCora['fine']['codigoMulta'] = 'NAOTEMMULTA';
                        $arrBilletCora['fine']['taxa'] = '0';
                        $arrBilletCora['fine']['valor'] = '0';*/
                    }
                    $bank403 = new BoletoCora($r->wallet_token, $r->wallet_certificate, $r->wallet_certificate_key);
                    $response = $bank403->registerBillet($arrBilletCora);

                    if ($response->id != '') {
                        return ['type' => 'ok', 'id' => $response->id, 'status' => 'REGISTRADA', 'linha_digitavel' => $response->payment_options->bank_slip->digitable, 'codigo_barras' => $response->payment_options->bank_slip->barcode, 'nossoNumero' => $response->payment_options->bank_slip->our_number, 'link' => $response->payment_options->bank_slip->url];
                    }

                    if ($response == '') {
                        return ['type' => 'error', 'msg' => 'Tivemos um problema de autenticação'];
                    }
                    if ($response->message) {
                        return ['type' => 'error', 'msg' => $response->message];
                    }
                    if ($response->title) {
                        return ['type' => 'error', 'msg' => $response->detail];
                    }

                    if ($response->detail) {
                        return ['type' => 'error', 'msg' => $response->violacoes[0]->razao];
                    }
                }

                if ($r->wallet_bank == '104') {
                    $specieDoc = '99';

                    $iniDoc = ['CH', 'DM', 'DMI', 'DS', 'DSI', 'DR', 'LC', 'NCC', 'NCE', 'NCI', 'NCR', 'NP', 'NPR', 'TM', 'TS', 'NS', 'RC', 'FAT', 'ND', 'AP', 'ME', 'PC', 'NF', 'DD', 'CPR', 'CC', 'BP', 'OU'];
                    $finDoc = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '31', '32', '99'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }
                    if ($r->wallet_type_fees == '0' || $r->wallet_type_fees == '1') {
                        $typeFees = 'B';
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = 'A';
                    } else {
                        $typeFees = 'B';
                    }

                    $arrBilletCaixa = [
                        'nossoNumero' => $r->finance_number,
                        'numeroDocumento' => $r->finance_our_number,
                        'dataVencimento' => $r->finance_date_due,
                        'dataEmissao' => $r->finance_date_release,
                        'valor' => number_format($r->finance_value, 2, '.', ''),
                        'tipoEspecie' => $replaceSpecie,
                        'aceite' => $r->wallet_accept,
                        'codigoMoeda' => '09',
                    ];

                    if ($r->wallet_type_penalty == '1') {
                        $arrBilletCaixa['multa']['tipo'] = 'TAXA';
                        $arrBilletCaixa['multa']['valor'] = $r->wallet_penalty;
                    } elseif ($r->wallet_type_penalty == '2') {
                        $arrBilletCaixa['multa']['tipo'] = 'VALOR';
                        $arrBilletCaixa['multa']['valor'] = $r->wallet_penalty;
                    } else {
                    }

                    if ($r->wallet_type_fees == '1') {
                        $arrBilletCaixa['juros']['tipo'] = 'TAXA_MENSAL';
                        $arrBilletCaixa['juros']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletCaixa['juros']['valor'] = $r->wallet_fees;
                    } elseif ($r->wallet_type_fees == '2') {
                        $arrBilletCaixa['juros']['tipo'] = 'VALOR_POR_DIA';
                        $arrBilletCaixa['juros']['data'] = date('Y-m-d', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletCaixa['juros']['valor'] = $r->wallet_fees;
                    } else {
                        $arrBilletCaixa['juros']['tipo'] = 'ISENTO';
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletCaixa['abatimento'] = $r->wallet_value_discount_anticipated;
                    }

                    $arrBilletCaixa['pagador']['tipo'] = $r->finance_type_pagador;
                    $arrBilletCaixa['pagador']['doc'] = $r->finance_doc_pagador;
                    $arrBilletCaixa['pagador']['nome'] = $r->finance_name_pagador;

                    if ($r->finance_address_pagador != '') {
                        $arrBilletCaixa['pagador']['informarEndereco'] = 'S';
                        $arrBilletCaixa['pagador']['endereco']['logradouro'] = $r->finance_address_pagador;
                        $arrBilletCaixa['pagador']['endereco']['bairro'] = $r->finance_district_pagador;
                        $arrBilletCaixa['pagador']['endereco']['cidade'] = $r->finance_city_pagador;
                        $arrBilletCaixa['pagador']['endereco']['uf'] = $r->finance_state_pagador;
                        $arrBilletCaixa['pagador']['endereco']['cep'] = $r->finance_zip_pagador;
                    }

                    $arrBilletCaixa['posVencimento']['acao'] = 'DEVOLVER';
                    $arrBilletCaixa['posVencimento']['dias'] = '90';
                    if ($r->wallet_day_devolution > '0') {
                        $arrBilletCaixa['posVencimento']['acao'] = 'DEVOLVER';
                        $arrBilletCaixa['posVencimento']['dias'] = $r->wallet_day_devolution;
                    }


                    $bank104 = new BoletoCaixa($r->wallet_beneficiary, $r->client_doc);

                    $response = $bank104->registerBillet($arrBilletCaixa);
                    if ($response->CONTROLE_NEGOCIAL->COD_RETORNO == '1') {
                        return ['type' => 'error', 'msg' => $response->CONTROLE_NEGOCIAL->MENSAGENS->RETORNO];
                    }

                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->INCLUI_BOLETO->LINHA_DIGITAVEL, 'codigo_barras' => $response->INCLUI_BOLETO->CODIGO_BARRAS];
                }


                if ($r->wallet_bank == '033') {
                    $specieDoc = 'A';

                    if ($r->wallet_species_document == 'DM') {
                        $replaceSpecie = 'DUPLICATA_MERCANTIL';
                    } elseif ($r->wallet_species_document == 'DR') {
                        $replaceSpecie = 'DUPLICATA_MERCANTIL';
                    } elseif ($r->wallet_species_document == 'NP') {
                        $replaceSpecie = 'NOTA_PROMISSORIA';
                    } elseif ($r->wallet_species_document == 'NR') {
                        $replaceSpecie = 'NOTA_PROMISSORIA_RURAL';
                    } elseif ($r->wallet_species_document == 'NS') {
                        $replaceSpecie = 'APOLICE_SEGURO';
                    } elseif ($r->wallet_species_document == 'RC') {
                        $replaceSpecie = 'RECIBO';
                    } elseif ($r->wallet_species_document == 'LC') {
                        $replaceSpecie = 'OUTROS';
                    } elseif ($r->wallet_species_document == 'ND') {
                        $replaceSpecie = 'OUTROS';
                    } elseif ($r->wallet_species_document == 'DS') {
                        $replaceSpecie = 'DUPLICATA_SERVICO';
                    } elseif ($r->wallet_species_document == 'OS') {
                        $replaceSpecie = 'OUTROS';
                    } elseif ($r->wallet_species_document == 'OFE') {
                        $replaceSpecie = 'OUTROS';
                    } else {
                        $replaceSpecie = 'DUPLICATA_MERCANTIL';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }

                    $arrBilletSantander = [
                        'environment' => 'PRODUCAO',
                        'nsuCode' => date('Ymd') . $r->finance_number,
                        'nsuDate' => date('Y-m-d'),
                        'covenantCode' => $r->wallet_beneficiary,
                        'issueDate' => date('Y-m-d'),
                        'dueDate' => $r->finance_date_due,
                        'bankNumber' => $r->finance_number,
                        'clientNumber' => $r->finance_our_number,
                        'nominalValue' => number_format($r->finance_value, 2, '.', ''),
                        'documentKind' => $replaceSpecie,
                        'paymentType' => 'REGISTRO'
                    ];

                    if ($r->finance_type_pagador == '1') {
                        $tipoDoc = 'CPF';
                    } else {
                        $tipoDoc = 'CNPJ';
                    }
                    $arrBilletSantander['payer'] = [
                        'documentType' => $tipoDoc,
                        'documentNumber' => $r->finance_doc_pagador,
                        'name' => $r->finance_name_pagador,
                        'address' => $r->finance_address_pagador,
                        'zipCode' => mask('#####-###', $r->finance_zip_pagador),
                        'city' => $r->finance_city_pagador,
                        'neighborhood' => $r->finance_district_pagador,
                        'state' => $r->finance_state_pagador
                    ];

                    if ($r->wallet_type_fees == '0') {
                        $typeFees = '0';
                        //$arrBilletSantander['jurosMora']['tipo'] = $typeFees;
                    } elseif ($r->wallet_type_fees == '1') {
                        $typeFees = '2';
                        $arrBilletSantander['interestPercentage'] = number_format($r->wallet_fees, 2, '.', '');
                        //$arrBilletSantander['fineQuantityDays'] = '1';
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = '1';
                        //$arrBilletSantander['jurosMora']['tipo'] = $typeFees;
                        //$arrBilletSantander['jurosMora']['valor'] = $r->wallet_fees;
                    } else {
                        $typeFees = '0';
                        //$arrBilletSantander['jurosMora']['tipo'] = $typeFees;
                    }
                    if ($r->wallet_type_penalty == '0') {
                        $typePenalty = '0';
                        //$arrBilletSantander['multa']['tipo'] = $typePenalty;
                    } elseif ($r->wallet_type_penalty == '1') {
                        $typePenalty = '2';
                        $arrBilletSantander['finePercentage'] = number_format($r->wallet_penalty, 2, '.', '');
                        $arrBilletSantander['fineQuantityDays'] = '1';
                    } elseif ($r->wallet_type_penalty == '2') {
                        $typePenalty = '1';
                        //$arrBilletSantander['multa']['tipo'] = $typePenalty;
                        //$arrBilletSantander['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        //$arrBilletSantander['multa']['valor'] = $r->wallet_penalty;
                    } else {
                        $typePenalty = '0';
                        //$arrBilletSantander['multa']['tipo'] = $typePenalty;
                    }

                    /*if($r->wallet_inform_discount_anticipated == 'S'){
                                  $arrBilletSantander['valorAbatimento'] = $r->wallet_value_discount_anticipated;
                              }

                              $arrBilletSantander['indicadorPix'] = 'N';
                              if($r->wallet_hybrid == '0'){
                                  $arrBilletSantander['indicadorPix'] = 'S';
                              }*/


                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletSantander['discount']['type'] = 'VALOR_DATA_FIXA';
                        $arrBilletSantander['discount']['discountOne']['value'] = $r->wallet_value_discount1;
                        $arrBilletSantander['discount']['discountOne']['limitDate'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletSantander['discount']['discountTwo']['value'] = $r->wallet_value_discount2;
                        $arrBilletSantander['discount']['discountTwo']['limitDate'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletSantander['discount']['discountThree']['value'] = $r->wallet_value_discount3;
                        $arrBilletSantander['discount']['discountThree']['limitDate'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletSantander['deductionValue'] = $r->wallet_value_discount_anticipated;
                    }

                    $bank033 = new BoletoSantander($r->wallet_token, $r->wallet_token_secret, $r->wallet_certificate, $r->wallet_pass_certificate);


                    $response = $bank033->registerBillet($arrBilletSantander, $r->wallet_workspace);

                    if (isset($response->_errorCode)) {
                        return ['type' => 'error', 'msg' => $response->_errors['0']->_message];
                    }

                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'linha_digitavel' => $response->digitableLine, 'codigo_barras' => linhaDigitavelParaCodigoBarras($response->digitableLine), 'nossoNumero' => $response->bankNumber];
                }
            }
        }
    }

    protected function registerBilletBankSR($idFinance, $arrayEdit) {
        $result = $this->db()->from('finance')
            ->join('wallet', function ($join) {
                $join->on('wallet_ide', 'finance_ide_wallet');
            })
            ->join('client', function ($join2) {
                $join2->on('client_ide', 'wallet_ide_client');
            })
            ->where('finance_id')->is($idFinance)
            ->andWhere('finance_trash')->is(0)
            ->orderBy('finance_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {

                $url = 'https://fiscal.sdsys.app/api/index.php';

                if ($r->wallet_bank == '001' || $r->wallet_bank == '748' || $r->wallet_bank == '756' || $r->wallet_bank == '004') {
                    $specieDoc = 'A';

                    $iniDoc = ['DM', 'DR', 'NP', 'NR', 'NS', 'RC', 'LC', 'ND', 'DS', 'OS', 'OFE'];
                    $finDoc = ['A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'O'];
                    $replaceSpecie = str_replace($iniDoc, $finDoc, $r->wallet_species_document);
                    if (strlen($replaceSpecie) < 2) {
                        $replaceSpecie = 'A';
                    }

                    if ($r->wallet_type_discount == '0' || $r->wallet_type_discount == '1') {
                        $typeDiscount = 'B';
                    } elseif ($r->wallet_type_discount == '2') {
                        $typeDiscount = 'A';
                    } else {
                        $typeDiscount = 'B';
                    }
                    if ($r->wallet_type_fees == '0') {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    } elseif ($r->wallet_type_fees == '1') {
                        $typeFees = '2';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['porcentagem'] = $r->wallet_fees;
                    } elseif ($r->wallet_type_fees == '2') {
                        $typeFees = '1';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                        $arrBilletBB['jurosMora']['valor'] = $r->wallet_fees;
                    } else {
                        $typeFees = '0';
                        $arrBilletBB['jurosMora']['tipo'] = $typeFees;
                    }
                    if ($r->wallet_type_penalty == '0') {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    } elseif ($r->wallet_type_penalty == '1') {
                        $typePenalty = '2';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['porcentagem'] = $r->wallet_penalty;
                    } elseif ($r->wallet_type_penalty == '2') {
                        $typePenalty = '1';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                        $arrBilletBB['multa']['data'] = date('d.m.Y', strtotime('+1 days', strtotime($r->finance_date_due)));
                        $arrBilletBB['multa']['valor'] = $r->wallet_penalty;
                    } else {
                        $typePenalty = '0';
                        $arrBilletBB['multa']['tipo'] = $typePenalty;
                    }

                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletBB['valorAbatimento'] = $r->wallet_value_discount_anticipated;
                    }

                    $arrBilletBB['indicadorPix'] = 'N';
                    if ($r->wallet_hybrid == '0') {
                        $arrBilletBB['indicadorPix'] = 'S';
                    }


                    if ($r->wallet_inform_discount1 == 'S') {
                        $arrBilletBB['desconto']['tipo'] = '2';
                        $arrBilletBB['desconto']['porcentagem'] = $r->wallet_value_discount1;
                        $arrBilletBB['desconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount1 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount2 == 'S') {
                        $arrBilletBB['segundoDesconto']['porcentagem'] = $r->wallet_value_discount2;
                        $arrBilletBB['segundoDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount2 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount3 == 'S') {
                        $arrBilletBB['terceiroDesconto']['porcentagem'] = $r->wallet_value_discount3;
                        $arrBilletBB['terceiroDesconto']['dataExpiracao'] = date('d/m/Y', strtotime('-' . $r->wallet_day_discount3 . ' days', strtotime($r->finance_date_due)));
                    }
                    if ($r->wallet_inform_discount_anticipated == 'S') {
                        $arrBilletBB['descontoAntecipado'] = $r->wallet_value_discount_anticipated;
                    }

                    if ($r->wallet_bank == '001') {
                        $dados = [
                            'banco' => 'BANCO_DO_BRASIL',
                            'dados' => [
                                'convenio' => $r->wallet_beneficiary,
                                'carteira' => $r->wallet_code,
                                'agencia' => $r->wallet_agency,
                                'conta' => $r->wallet_bill,
                                'valor' => $r->finance_value,
                                'vencimento' => $r->finance_date_due,
                                'nosso_numero' => $r->finance_our_number
                            ]
                        ];
                    }

                    if ($r->wallet_bank == '748') {
                        $dados = [
                            'banco' => 'SICREDI',
                            'dados' => [
                                'convenio' => $r->wallet_beneficiary,
                                'posto' => $r->wallet_post,
                                'carteira' => $r->wallet_code,
                                'agencia' => $r->wallet_agency,
                                'conta' => $r->wallet_bill,
                                'valor' => $r->finance_value,
                                'vencimento' => $r->finance_date_due,
                                'ano' => substr($r->finance_date_due, 0, 4),
                                'nosso_numero' => $r->finance_our_number
                            ]
                        ];
                    }

                    if ($r->wallet_bank == '756') {
                        $dados = [
                            'banco' => 'SICOOB',
                            'dados' => [
                                'modalidade' => '01',
                                'convenio' => $r->wallet_beneficiary,
                                'carteira' => $r->wallet_code,
                                'agencia' => $r->wallet_agency,
                                'conta' => $r->wallet_bill,
                                'valor' => $r->finance_value,
                                'vencimento' => $r->finance_date_due,
                                'sequencial' => $r->finance_our_number,
                                'num_parcelas' => '001'
                            ]
                        ];
                    }

                    if ($r->wallet_bank == '004') {
                        $dados = [
                            'banco' => 'BANCO_DO_NORDESTE',
                            'dados' => [
                                'carteira' => $r->wallet_code,
                                'agencia' => $r->wallet_agency,
                                'conta' => $r->wallet_bill,
                                'conta_dv' => $r->wallet_bill_dv,
                                'valor' => $r->finance_value,
                                'nosso_numero' => $r->finance_number,
                                'vencimento' => $r->finance_date_due,
                                'tipo_operacao' => '21'
                            ]
                        ];
                    }

                    // Inicializa o cURL
                    $ch = curl_init($url);

                    // Configura o cURL
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Retorna a resposta como string
                    curl_setopt($ch, CURLOPT_POST, true);            // Método POST
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));  // Dados em JSON
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);

                    // Executa a requisição
                    $resposta = curl_exec($ch);

                    // Fecha o cURL
                    curl_close($ch);

                    // Decodifica a resposta JSON
                    $resultado = json_decode($resposta, true);

                    if (isset($resultado['erro'])) {
                        return ['type' => 'error', 'msg' => $resultado['erro']];
                    }

                    $arrRetira = ['.', ' '];
                    $arrcoloca = ['', ''];


                    return ['type' => 'ok', 'status' => 'REGISTRADA', 'location' => '', 'nossoNumero' => $resultado['nosso_numero'], 'txtid' => '', 'linha_digitavel' => str_replace($arrRetira, $arrcoloca, $resultado['linha_digitavel']), 'codigo_barras' => $resultado['codigo_barras']];
                }
            }
        }
    }

    public function retorno() {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();


        $resultWallet = $this->db()->from('wallet')
            ->where('wallet_status')->is(0)
            ->andWhere('wallet_trash')->is(0)
            ->andWhere('wallet_ide')->is($postVars['ideWallet'])
            ->andWhere('wallet_id_company')->is($idReg)
            ->andWhere('wallet_bank')->isNot('999')
            ->select()
            ->all();

        if ($resultWallet) {
            foreach ($resultWallet as $rWallet) {
                if ($rWallet->wallet_bank == '077') {
                    $dataInicialPagoInter = addslashes($postVars['dataInicial']);
                    $dataFinalPagoInter = addslashes($postVars['dataFinal']);

                    $bank077 = new BoletoInterHibrido($rWallet->wallet_token, $rWallet->wallet_token_secret, $rWallet->wallet_certificate, $rWallet->wallet_certificate_key);
                    if ($bank077->getTokenAccess()->access_token) {
                        $resDown = $bank077->readPeriodoPago($dataInicialPagoInter, $dataFinalPagoInter);

                        unset($request);
                        if ($resDown->totalElementos > 0) {
                            $request['token'] = $rWebhook->webhook_token;
                            for ($x = 0; $x < count($resDown->cobrancas); $x++) {
                                $resultFinance = $this->db()->from('finance')
                                    ->where('finance_ide_wallet')->is($rWallet->wallet_ide)
                                    ->andWhere('finance_number')->is($resDown->cobrancas[$x]->boleto->nossoNumero)
                                    ->andWhere('finance_trash')->is(0)
                                    ->orderBy('finance_id', 'DESC')
                                    ->limit('1')
                                    ->select()
                                    ->all();
                                if ($resultFinance) {
                                    foreach ($resultFinance as $resFinance) {
                                        unset($requestitem);
                                        $requestitem['nossoNumero'] = $resDown->cobrancas[$x]->boleto->nossoNumero;
                                        $requestitem['seuNumero'] = $resDown->cobrancas[$x]->cobranca->seuNumero;
                                        $requestitem['dataVencimento'] = $resDown->cobrancas[$x]->cobranca->dataVencimento;
                                        $requestitem['dataPagamento'] = $resDown->cobrancas[$x]->cobranca->dataSituacao;
                                        $requestitem['dataCredito'] = $resDown->cobrancas[$x]->cobranca->dataSituacao;
                                        /*if ($resDown->content[$x]->valorTotalRecebimento > $resFinance->finance_value) {
                                            if ($resDown->content[$x]->multa->codigo == 'NAOTEMMULTA') {
                                                $requestitem['multa'] = '0';
                                            } elseif ($resDown->content[$x]->multa->codigo == 'VALORFIXO') {
                                                $requestitem['multa'] = $resDown->content[$x]->multa->valor;
                                            } elseif ($resDown->content[$x]->multa->codigo == 'PERCENTUAL') {
                                                $requestitem['multa'] = round(($resDown->content[$x]->multa->taxa / 100) * $resFinance->finance_value, 2);
                                            }
                                            $requestitem['juros'] = round($resDown->content[$x]->valorTotalRecebimento - ($resFinance->finance_value + $requestitem['multa']), 2);
                                        }*/
                                        $requestitem['valorPagoSacado'] = $resDown->cobrancas[$x]->cobranca->valorTotalRecebido;

                                        $requestitem['ide'] = $resFinance->finance_ide;
                                        $requestitem['reference'] = $resFinance->finance_reference;


                                        $postFinance = [
                                            'finance_date_payment' => $requestitem['dataPagamento'],
                                            'finance_value_payment' => $requestitem['valorPagoSacado'],
                                            'finance_status' => '1',
                                            'finance_data_credito' => $requestitem['dataPagamento'],
                                        ];


                                        $resUp = $this->db()->update('finance')->where('finance_id')->is($resFinance->finance_id)->set($postFinance);

                                        $request['cobranca'][] = $requestitem;
                                    }
                                }
                            }
                        }
                    }
                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["retorno" => $request]);
                    return;
                }

                if ($rWallet->wallet_bank == '748') {
                    if ($rWallet->wallet_versao == 'v2') {
                        $bank748 = new BoletoSicrediHibrido($rWallet->wallet_token, $rWallet->wallet_token_secret, $rWallet->wallet_beneficiary, $rWallet->wallet_agency, $rWallet->wallet_post);

                        $resDown = $bank748->readBillet($rWallet->wallet_beneficiary, date('d/m/Y', strtotime(addslashes($postVars['dataInicial']))));


                        unset($request);
                        if (isset($resDown->items) && count($resDown->items) > 0) {

                            $request['token'] = $rWebhook->webhook_token;


                            for ($x = 0; $x < count($resDown->items); $x++) {

                                $resultFinance = $this->db()->from('finance')
                                    ->where('finance_ide_wallet')->is($rWallet->wallet_ide)
                                    ->andWhere('finance_number')->is($resDown->items[$x]->nossoNumero)
                                    ->andWhere('finance_trash')->is(0)
                                    ->orderBy('finance_id', 'DESC')
                                    ->limit('1')
                                    ->select()
                                    ->all();

                                if ($resultFinance) {
                                    foreach ($resultFinance as $resFinance) {

                                        $requestitem['nossoNumero'] = $resDown->items[$x]->nossoNumero;
                                        $requestitem['seuNumero'] = $resDown->items[$x]->seuNumero;
                                        $requestitem['dataVencimento'] = $resFinance->finance_date_due;
                                        $requestitem['dataPagamento'] = $resDown->items[$x]->dataPagamento;
                                        $requestitem['dataCredito'] = $resDown->items[$x]->dataPagamento;
                                        $requestitem['valorPagoSacado'] = $resDown->items[$x]->valorLiquidado;

                                        $requestitem['juros'] = $resDown->items[$x]->jurosLiquido;
                                        $requestitem['multa'] = $resDown->items[$x]->multaLiquida;

                                        $requestitem['ide'] = $resFinance->finance_ide;
                                        $requestitem['reference'] = $resFinance->finance_reference;


                                        $postFinance = [
                                            'finance_date_payment' => $requestitem['dataPagamento'],
                                            'finance_value_payment' => $requestitem['valorPagoSacado'],
                                            'finance_status' => '1',
                                            'finance_data_credito' => $resDown->items[$x]->dataLiquidacao
                                        ];


                                        $resUp = $this->db()->update('finance')->where('finance_id')->is($resFinance->finance_id)->set($postFinance);

                                        $request['cobranca'][] = $requestitem;
                                    }
                                } else {
                                    $requestitem['nossoNumero'] = $resDown->items[$x]->nossoNumero;
                                    $requestitem['seuNumero'] = $resDown->items[$x]->seuNumero;
                                    $requestitem['dataVencimento'] = '';
                                    $requestitem['dataPagamento'] = $resDown->items[$x]->dataPagamento;
                                    $requestitem['dataCredito'] = $resDown->items[$x]->dataPagamento;
                                    $requestitem['valorPagoSacado'] = $resDown->items[$x]->valorLiquidado;

                                    $requestitem['juros'] = $resDown->items[$x]->jurosLiquido;
                                    $requestitem['multa'] = $resDown->items[$x]->multaLiquida;

                                    $requestitem['ide'] = '';
                                    $requestitem['reference'] = '';


                                    $request['cobranca'][] = $requestitem;
                                }
                            }
                        }
                    } else {
                        $bank748 = new BoletoSicredi($rWallet->wallet_token);
                        $arrBilletSicredi = [
                            'agencia' => $rWallet->wallet_agency,
                            'posto' => $rWallet->wallet_post,
                            'cedente' => $rWallet->wallet_beneficiary,
                            'tipoData' => 'DATA_LIQUIDACAO',
                            'dataInicio' => date('d/m/Y', strtotime(addslashes($postVars['dataInicial']))),
                            'dataFim' => date('d/m/Y', strtotime(addslashes($postVars['dataFinal'])))
                        ];

                        $resDown = $bank748->readBillet($arrBilletSicredi);
                        //print_r($resDown);


                        unset($request);
                        if (!isset($resDown->codigo)) {

                            $request['token'] = $rWebhook->webhook_token;


                            for ($x = 0; $x < count($resDown); $x++) {

                                $resultFinance = $this->db()->from('finance')
                                    ->where('finance_ide_wallet')->is($rWallet->wallet_ide)
                                    ->andWhere('finance_number')->is($resDown[$x]->nossoNumero)
                                    ->andWhere('finance_trash')->is(0)
                                    ->orderBy('finance_id', 'DESC')
                                    ->limit('1')
                                    ->select()
                                    ->all();

                                if ($resultFinance) {
                                    foreach ($resultFinance as $resFinance) {

                                        $requestitem['nossoNumero'] = $resDown[$x]->nossoNumero;
                                        $requestitem['seuNumero'] = $resDown[$x]->seuNumero;
                                        $requestitem['dataVencimento'] = $resDown[$x]->dataVencimento;
                                        $requestitem['dataPagamento'] = $resDown[$x]->dataLiquidacao;
                                        $requestitem['dataCredito'] = $resDown[$x]->dataLiquidacao;
                                        $requestitem['valorPagoSacado'] = $resDown[$x]->valorLiquidado;

                                        $requestitem['ide'] = $resFinance->finance_ide;
                                        $requestitem['reference'] = $resFinance->finance_reference;


                                        $postFinance = [
                                            'finance_date_payment' => $requestitem['dataPagamento'],
                                            'finance_value_payment' => $requestitem['valorPagoSacado'],
                                            'finance_status' => '1',
                                            'finance_data_credito' => $resDown[$x]->dataLiquidacao
                                        ];


                                        $resUp = $this->db()->update('finance')->where('finance_id')->is($resFinance->finance_id)->set($postFinance);

                                        $request['cobranca'][] = $requestitem;
                                    }
                                }
                            }
                        }
                    }

                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["retorno" => $request]);
                    return;
                }

                if ($rWallet->wallet_bank == '001' || $rWallet->wallet_bank == '1') {
                    $bank001 = new BoletoBB($rWallet->wallet_bb_client_id, $rWallet->wallet_bb_client_secret, $rWallet->wallet_bb_dev_key);

                    $dataInicial = date('d.m.Y', strtotime(addslashes($postVars['dataInicial'])));
                    $dataFinal = date('d.m.Y', strtotime(addslashes($postVars['dataFinal'])));

                    unset($request);
                    $res = $bank001->readPeriodoPago($rWallet->wallet_agency, $rWallet->wallet_bill, $dataInicial, $dataFinal);
                    if ($res == '') {
                        $this->call(
                            '401',
                            'Ops',
                            '',
                            "ops",
                            "Wallet não encontrada, verifique a agencia e conta"
                        )->back(["count" => 0]);
                        return;
                    }
                    if (count($res->boletos) > '0') {
                        for ($x = 0; $x < count($res->boletos); $x++) {
                            if ($res->boletos[$x]->estadoTituloCobranca == 'Liquidado') {
                                $data_obj = DateTime::createFromFormat('d.m.Y', $res->boletos[$x]->dataMovimento);
                                $data_convertida = $data_obj->format('Y-m-d');

                                $data_obj1 = DateTime::createFromFormat('d.m.Y', $res->boletos[$x]->dataCredito);
                                $data_convertida1 = $data_obj1->format('Y-m-d');

                                $jsonUpdate['finance_date_payment'] = $data_convertida;
                                $jsonUpdate['finance_data_credito'] = $data_convertida1;
                                $jsonUpdate['finance_value_payment'] = $res->boletos[$x]->valorPago;
                                $jsonUpdate['finance_status'] = '1';

                                $results = $this->db()->from('finance')
                                    ->where('finance_status')->is(0)
                                    ->andWhere('finance_status_webservice')->is(1)
                                    ->andWhere('finance_trash')->is(0)
                                    ->andWhere('finance_number')->is(trim($res->boletos[$x]->numeroBoletoBB))
                                    ->orderBy('finance_id', 'desc')
                                    ->select()
                                    ->all();

                                if ($results) {
                                    $resultUpdate = $this->db()->update('finance')->where('finance_number')->is($res->boletos[$x]->numeroBoletoBB)->andWhere('finance_id_company')->is($rWallet->wallet_id_company)->andWhere('finance_status')->is(0)->set($jsonUpdate);
                                }
                            }
                        }


                        if (count($res->boletos) > '0') {

                            $request['token'] = $rWebhook->webhook_token;


                            for ($x = 0; $x < count($res->boletos); $x++) {
                                $resultFinance = $this->db()->from('finance')
                                    ->where('finance_ide_wallet')->is($rWallet->wallet_ide)
                                    ->andWhere('finance_number')->is($res->boletos[$x]->numeroBoletoBB)
                                    ->andWhere('finance_status')->is(1)
                                    ->andWhere('finance_trash')->is(0)
                                    ->orderBy('finance_id', 'DESC')
                                    ->limit('1')
                                    ->select()
                                    ->all();

                                if ($resultFinance) {
                                    foreach ($resultFinance as $resFinance) {

                                        $requestitem['nossoNumero'] = $res->boletos[$x]->numeroBoletoBB;
                                        $requestitem['seuNumero'] = $resFinance->finance_our_number;
                                        $requestitem['dataVencimento'] = $resFinance->finance_date_due;
                                        $requestitem['dataPagamento'] = $resFinance->finance_date_payment;
                                        $requestitem['dataCredito'] = $resFinance->finance_data_credito;
                                        $requestitem['valorPagoSacado'] = $resFinance->finance_value_payment;

                                        $requestitem['ide'] = $resFinance->finance_ide;
                                        $requestitem['reference'] = $resFinance->finance_reference;

                                        $request['cobranca'][] = $requestitem;
                                    }
                                }
                            }
                        }
                    }
                    $this->call(
                        '200',
                        'Sucesso',
                        '',
                        "ok",
                        "Operação realizada com sucesso"
                    )->back(["retorno" => $request]);
                    return;
                }
            }
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Wallet não encontrada"
        )->back(["count" => 0]);
        return;
    }
}

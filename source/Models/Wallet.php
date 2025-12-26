<?php

namespace Source\Models;

use Source\Conn\DataLayer;
use Source\Facades\BoletoSantander;

class Wallet extends DataLayer {

    public function create(): void {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $postVars['ide'] = md5(date('Y-mdHis') . rand(10000, 99999));

        if ($postVars['clientSecret']) {
            $postVars['clienteSecret'] = $postVars['clientSecret'];
        }

        $requiredValidation = $this->getFieldsBank($postVars, $postVars['bankCode']);

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

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($postVars['ideClient']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "O ideClient informado não pertence ao seu cadastro"
            )->back(["count" => 0]);
            return;
        }

        $requiredValidation['fields']['wallet_id_company'] = $idReg;
        $requiredValidation['fields']['wallet_trash'] = 0;

        foreach ($requiredValidation['fields'] as $key => $val) {
            if ($val == '') {
                $requiredValidation['fields'][$key] = '0';
            }
        }
        if ($this->db()->insert($requiredValidation['fields'])->into('wallet')) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $requiredValidation['fields']['wallet_ide']]);
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

    public function select(): void {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $query_paginator = $this->db()->from('wallet')->where('wallet_id_company')->is($idReg)->andWhere('wallet_trash')->is(0);
        $query = $this->db()->from('wallet')->where('wallet_id_company')->is($idReg)->andWhere('wallet_trash')->is(0);

        if ($postVars['ide'] != '') {
            $query_paginator = $query_paginator->andWhere('wallet_ide')->is($postVars['ide']);
            $query = $query->andWhere('wallet_ide')->is($postVars['ide']);
        }
        if ($postVars['status'] != '') {
            $query_paginator = $query_paginator->andWhere('wallet_status')->is($postVars['status']);
            $query = $query->andWhere('wallet_status')->is($postVars['status']);
        }
        if ($postVars['ideClient'] != '') {
            $query_paginator = $query_paginator->andWhere('wallet_ide_client')->is($postVars['ideClient']);
            $query = $query->andWhere('wallet_ide_client')->is($postVars['ideClient']);
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
        $result = $query->orderBy('wallet_id')
            ->limit($limit)
            ->offset($offset)
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                unset($fieldsResponse);
                $fieldsResponse = [
                    'ide' => $r->wallet_ide,
                    'ideClient' => $r->wallet_ide_client,
                    'bankCode' => $r->wallet_bank,
                    'descricao' => $r->wallet_description,
                    'agencia' => $r->wallet_agency,
                    'posto' => $r->wallet_post,
                    'conta' => $r->wallet_bill,
                    'contaDv' => $r->wallet_bill_dv,
                    'cedente' => $r->wallet_beneficiary,
                    'carteira' => $r->wallet_code,
                    'diaProtesto' => $r->wallet_day_protest,
                    'tipoMulta' => $r->wallet_type_penalty,
                    'multa' => $r->wallet_penalty,
                    'tipoJuros' => $r->wallet_type_fees,
                    'juros' => $r->wallet_fees,
                    'tipoDesconto' => $r->wallet_type_discount,
                    'informarDesconto1' => $r->wallet_inform_discount1,
                    'valorDesconto1' => $r->wallet_value_discount1,
                    'diasDesconto1' => $r->wallet_day_discount1,
                    'informarDesconto2' => $r->wallet_inform_discount2,
                    'valorDesconto2' => $r->wallet_value_discount2,
                    'diasDesconto2' => $r->wallet_day_discount2,
                    'informarDesconto3' => $r->wallet_inform_discount3,
                    'valorDesconto3' => $r->wallet_value_discount3,
                    'diasDesconto3' => $r->wallet_day_discount3,
                    'informarDescontoAntecipado' => $r->wallet_inform_discount_anticipated,
                    'valorDescontoAntecipado' => $r->wallet_value_discount_anticipated,
                    'informativo' => $r->wallet_informative,
                    'especieDocumento' => $r->wallet_species_document,
                    'status' => $r->wallet_status
                ];
                $rows['data'][] = $fieldsResponse;
            }
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Registros encontrados"
            )->back(["count" => count($resultPaginator), "offset" => $offset, "limit" => $limit, "response" => $rows]);
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

    public function selectIde(): void {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $result = $this->db()->from('wallet')
            ->where('wallet_id_company')->is($idReg)
            ->andWhere('wallet_ide')->is(clearDoc($getHeaders['ide']))
            ->andWhere('wallet_trash')->is(0)
            ->orderBy('wallet_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                unset($fieldsResponse);
                $fieldsResponse = [
                    'ide' => $r->wallet_ide,
                    'ideClient' => $r->wallet_ide_client,
                    'bankCode' => $r->wallet_bank,
                    'descricao' => $r->wallet_description,
                    'agencia' => $r->wallet_agency,
                    'posto' => $r->wallet_post,
                    'conta' => $r->wallet_bill,
                    'contaDv' => $r->wallet_bill_dv,
                    'cedente' => $r->wallet_beneficiary,
                    'carteira' => $r->wallet_code,
                    'diaProtesto' => $r->wallet_day_protest,
                    'tipoMulta' => $r->wallet_type_penalty,
                    'multa' => $r->wallet_penalty,
                    'tipoJuros' => $r->wallet_type_fees,
                    'juros' => $r->wallet_fees,
                    'tipoDesconto' => $r->wallet_type_discount,
                    'informarDesconto1' => $r->wallet_inform_discount1,
                    'valorDesconto1' => $r->wallet_value_discount1,
                    'diasDesconto1' => $r->wallet_day_discount1,
                    'informarDesconto2' => $r->wallet_inform_discount2,
                    'valorDesconto2' => $r->wallet_value_discount2,
                    'diasDesconto2' => $r->wallet_day_discount2,
                    'informarDesconto3' => $r->wallet_inform_discount3,
                    'valorDesconto3' => $r->wallet_value_discount3,
                    'diasDesconto3' => $r->wallet_day_discount3,
                    'informarDescontoAntecipado' => $r->wallet_inform_discount_anticipated,
                    'valorDescontoAntecipado' => $r->wallet_value_discount_anticipated,
                    'informativo' => $r->wallet_informative,
                    'especieDocumento' => $r->wallet_species_document,
                    'status' => $r->wallet_status
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

    public function delete(): void {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $result = $this->db()->from('wallet')
            ->where('wallet_id_company')->is($idReg)
            ->andWhere('wallet_ide')->is(clearDoc($getHeaders['ide']))
            ->orderBy('wallet_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            $fieldsDelete['wallet_trash'] = '1';
            $fieldsDelete['wallet_status'] = '1';
            $result = $this->db()->update('wallet')->where('wallet_ide')->is($getHeaders['ide'])->andWhere('wallet_id_company')->is($idReg)->set($fieldsDelete);
            if ($result == '0' || $result == '1') {
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(["count" => 0]);
                return;
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

    public function update(): void {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $getHeaders = $this->getHeaders();

        if ($postVars['clientSecret']) {
            $postVars['clienteSecret'] = $postVars['clientSecret'];
        }


        $result = $this->db()->from('wallet')
            ->where('wallet_id_company')->is($idReg)
            ->andWhere('wallet_ide')->is(clearDoc($getHeaders['ide']))
            ->andWhere('wallet_trash')->is(0)
            ->orderBy('wallet_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '200',
                'Erro',
                '',
                "error",
                "Não encontramos nenhum registro"
            )->back(["count" => 0]);
            return;
        }

        foreach ($result as $r) {
            $postVars['ideClient'] = $r->wallet_ide_client;
            $postVars['ide'] = clearDoc($getHeaders['ide']);
        }

        $requiredValidation = $this->getFieldsBank($postVars, $postVars['bankCode']);



        foreach ($requiredValidation['fields'] as $key => $val) {
            if ($val == '') {
                $requiredValidation['fields'][$key] = '0';
            }
        }
        $result = $this->db()->update('wallet')->where('wallet_ide')->is($getHeaders['ide'])->andWhere('wallet_id_company')->is($idReg)->set($requiredValidation['fields']);
        if ($result == '0' || $result == '1') {
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

    protected function getFieldsBank(array $fields, string $bank) {

        $implementedBank = [
            '748',
            '001',
            '756',
            '004'
        ];

        if (!in_array($bank, $implementedBank)) {
            return ['type' => 'error', 'msg' => 'Banco ainda não implementado'];
        }

        if ($bank == '999') {
            $fieldsRequiredApiSimplesPost = [
                'ideClient',
                'descricao',
                'conta',
                'contaDv',
                'clientId',
                'clienteSecret',
                'status'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredApiSimplesPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSicoob = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_token_secret' => $fields['clienteSecret'],
                'wallet_status' => $fields['status']
            ];

            return ['type' => 'ok', 'fields' => $arrSicoob];
        }

        if ($bank == '077') {
            $fieldsRequiredSicoobPost = [
                'ideClient',
                'descricao',
                'conta',
                'contaDv',
                'clientId',
                'clienteSecret',
                'status',
                'certificate',
                'certificateKey'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredSicoobPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSicoob = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_token_secret' => $fields['clienteSecret'],
                'wallet_status' => $fields['status'],
                'wallet_certificate' => $fields['certificate'],
                'wallet_certificate_key' => $fields['certificateKey']
            ];

            return ['type' => 'ok', 'fields' => $arrSicoob];
        }

        if ($bank == '403') {
            $fieldsRequiredSicoobPost = [
                'ideClient',
                'descricao',
                'conta',
                'contaDv',
                'clientId',
                'clienteSecret',
                'status',
                'certificate',
                'certificateKey'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredSicoobPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSicoob = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_token_secret' => $fields['clienteSecret'],
                'wallet_status' => $fields['status'],
                'wallet_certificate' => $fields['certificate'],
                'wallet_certificate_key' => $fields['certificateKey']
            ];

            return ['type' => 'ok', 'fields' => $arrSicoob];
        }

        if ($bank == '237') {
            $fieldsRequiredSicoobPost = [
                'ideClient',
                'descricao',
                'conta',
                'contaDv',
                'agencia',
                'clientId',
                'status',
                'certificate',
                'certificateKey'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredSicoobPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSicoob = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_status' => $fields['status'],
                'wallet_certificate' => $fields['certificate'],
                'wallet_certificate_key' => $fields['certificateKey']
            ];

            return ['type' => 'ok', 'fields' => $arrSicoob];
        }

        if ($bank == '748') {

            if ($fields['versao'] == 'v2' || $fields['versao'] == 'v3') {
                $fieldsRequiredSicrediPost = [
                    'agencia',
                    'ideClient',
                    'posto',
                    'cedente',
                    'descricao',
                    'conta',
                    'contaDv',
                    'carteira',
                    'status'
                ];
            } else {
                $fieldsRequiredSicrediPost = [
                    'agencia',
                    'ideClient',
                    'posto',
                    'cedente',
                    'descricao',
                    'conta',
                    'contaDv',
                    'carteira',
                    'status'
                ];
            }


            if (!$this->fieldsRequiredVerification($fieldsRequiredSicrediPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }
            if ($fields['token'] != '') {
                $fields['token'] = $fields['token'];
            } else {
                $fields['token'] = $fields['clientId'];
            }

            $arrSicredi = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_post' => $fields['posto'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['token'],
                'wallet_token_secret' => $fields['clienteSecret'],
                'wallet_status' => $fields['status']
            ];

            return ['type' => 'ok', 'fields' => $arrSicredi];
        }

        if ($bank == '756') {
            $fieldsRequiredSicoobPost = [
                'agencia',
                'ideClient',
                'cedente',
                'descricao',
                'conta',
                'contaDv',
                'carteira',
                'clientId',
                'status',
                'certificate',
                'passCertificate'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredSicoobPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSicoob = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_status' => $fields['status'],
                'wallet_certificate' => $fields['certificate'],
                'wallet_pass_certificate' => $fields['passCertificate']
            ];

            return ['type' => 'ok', 'fields' => $arrSicoob];
        }

        if ($bank == '033') {
            $fieldsRequiredSicoobPost = [
                'agencia',
                'ideClient',
                'cedente',
                'descricao',
                'conta',
                'contaDv',
                'carteira',
                'clientId',
                'clienteSecret',
                'status',
                'certificate',
                'passCertificate'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredSicoobPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }

            $accept = '';

            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrSantander = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['clientId'],
                'wallet_token_secret' => $fields['clienteSecret'],
                'wallet_status' => $fields['status'],
                'wallet_certificate' => $fields['certificate'],
                'wallet_pass_certificate' => $fields['passCertificate']
            ];

            $bank033 = new BoletoSantander($fields['clientId'], $fields['clienteSecret'], $fields['certificate'], $fields['passCertificate']);

            $fieldsWorkspace['type'] = 'BILLING';
            $fieldsWorkspace['description'] = 'Workspace de Cobrança';
            $fieldsWorkspace['covenants']['0']['code'] = $fields['cedente'];
            //$fieldsWorkspace['webhookURL'] = URL_WEBHOOK_SANTANDER;
            //$fieldsWorkspace['bankSlipBillingWebhookActive'] = true;
            //$fieldsWorkspace['pixBillingWebhookActive'] = true;


            $res = $bank033->registerWorkSpace($fieldsWorkspace);
            if ($res->id) {
                $arrSantander['wallet_workspace'] = $res->id;
                return ['type' => 'ok', 'fields' => $arrSantander];
            }


            return ['type' => 'error', 'msg' => 'Tivemos problemas ao criar a carteira no banco'];
        }

        if ($bank == '104') {
            $fieldsRequiredCaixaPost = [
                'agencia',
                'ideClient',
                'cedente',
                'descricao',
                'conta',
                'contaDv',
                'carteira',
                'status',
                'diaDevolucao'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredCaixaPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }
            $accept = '';
            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }

            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrCaixa = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_post' => $fields['posto'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_token' => $fields['token'],
                'wallet_status' => $fields['status']
            ];

            return ['type' => 'ok', 'fields' => $arrCaixa];
        }

        if ($bank == '001') {
            $fieldsRequiredBBPost = [
                'agencia',
                'ideClient',
                'cedente',
                'descricao',
                'conta',
                'contaDv',
                'carteira',
                'status',
                'diaDevolucao',
                'variacao',
                'clientId',
                'clienteSecret',
                'devKey',
                'usarPix'
            ];

            if (!$this->fieldsRequiredVerification($fieldsRequiredBBPost, $fields)) {
                return ['type' => 'error', 'msg' => 'Existem campos obrigatórios a serem preenchidos'];
            }
            $accept = '';
            if (!isset($fields['usarPix'])) {
                $fields['usarPix'] = '1';
            }
            if (!isset($fields['status'])) {
                $fields['status'] = '0';
            }
            if (!isset($fields['diaProtesto'])) {
                $fields['diaProtesto'] = '0';
            }
            if (!isset($fields['diaDevolucao'])) {
                $fields['diaDevolucao'] = '90';
            }
            if ($fields['aceite'] == 'N' || $fields['aceite'] == 'S') {
                $accept = $fields['aceite'];
            }
            if (!isset($fields['aceite'])) {
                $accept = 'N';
            }
            if (!isset($fields['tipoMulta'])) {
                $fields['tipoMulta'] = '0';
                $fields['multa'] = '';
            }
            if (!isset($fields['tipoJuros'])) {
                $fields['tipoJuros'] = '0';
                $fields['juros'] = '';
            }
            if (!isset($fields['tipoDesconto'])) {
                $fields['tipoDesconto'] = '0';
            }
            if (!isset($fields['informarDesconto1'])) {
                $fields['informarDesconto1'] = 'N';
                $fields['valorDesconto1'] = '';
                $fields['diasDesconto1'] = '';
            }
            if (!isset($fields['informarDesconto2'])) {
                $fields['informarDesconto2'] = 'N';
                $fields['valorDesconto2'] = '';
                $fields['diasDesconto2'] = '';
            }
            if (!isset($fields['informarDesconto3'])) {
                $fields['informarDesconto3'] = 'N';
                $fields['valorDesconto3'] = '';
                $fields['diasDesconto3'] = '';
            }
            if (!isset($fields['informarDescontoAntecipado'])) {
                $fields['informarDescontoAntecipado'] = 'N';
                $fields['valorDescontoAntecipado'] = '';
            }

            $arrBB = [
                'wallet_ide_client' => $fields['ideClient'],
                'wallet_ide' => $fields['ide'],
                'wallet_bank' => $fields['bankCode'],
                'wallet_description' => $fields['descricao'],
                'wallet_agency' => $fields['agencia'],
                'wallet_post' => $fields['posto'],
                'wallet_bill' => $fields['conta'],
                'wallet_bill_dv' => $fields['contaDv'],
                'wallet_beneficiary' => $fields['cedente'],
                'wallet_code' => $fields['carteira'],
                'wallet_day_protest' => $fields['diaProtesto'],
                'wallet_day_devolution' => $fields['diaDevolucao'],
                'wallet_type_penalty' => $fields['tipoMulta'],
                'wallet_penalty' => $fields['multa'],
                'wallet_type_fees' => $fields['tipoJuros'],
                'wallet_fees' => $fields['juros'],
                'wallet_type_discount' => $fields['tipoDesconto'],
                'wallet_inform_discount1' => $fields['informarDesconto1'],
                'wallet_value_discount1' => $fields['valorDesconto1'],
                'wallet_day_discount1' => $fields['diasDesconto1'],
                'wallet_inform_discount2' => $fields['informarDesconto2'],
                'wallet_value_discount2' => $fields['valorDesconto2'],
                'wallet_day_discount2' => $fields['diasDesconto2'],
                'wallet_inform_discount3' => $fields['informarDesconto3'],
                'wallet_value_discount3' => $fields['valorDesconto3'],
                'wallet_day_discount3' => $fields['diasDesconto3'],
                'wallet_inform_discount_anticipated' => $fields['informarDescontoAntecipado'],
                'wallet_value_discount_anticipated' => $fields['valorDescontoAntecipado'],
                'wallet_informative' => $fields['informativo'],
                'wallet_species_document' => $fields['especieDocumento'],
                'wallet_accept' => $accept,
                'wallet_variation' => $fields['variacao'],
                'wallet_status' => $fields['status'],
                'wallet_bb_client_id' => $fields['clientId'],
                'wallet_bb_client_secret' => $fields['clienteSecret'],
                'wallet_bb_dev_key' => $fields['devKey'],
                'wallet_hybrid' => $fields['usarPix']
            ];

            return ['type' => 'ok', 'fields' => $arrBB];
        }
    }

    /*public function delete($param)
    {
        $idReg = $this->checkToken();

        $result = $this->db()->from('wallet')
            ->where('wallet_id_company')->is($idReg)
            ->andWhere('wallet_ide')->is($param['ide'])
            ->orderBy('wallet_id', 'DESC')
            ->limit('1')
            ->select(['wallet_id'])
            ->all();

        if(!$result){
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Carteira não localizada"
            )->back(["count" => 0]);
            return;
        }

        foreach($result as $r){
            $upWhats['wallet_trash'] = '1';

            $this->db()->update('wallet')->where('wallet_id_company')->is($idReg)->andWhere('wallet_ide')->is($param['ide'])->set($upWhats);
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $param['ide']]);
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
    }*/
}

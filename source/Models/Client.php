<?php

namespace Source\Models;

use Source\Conn\DataLayer;

class Client extends DataLayer
{

    public function create(): void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $fieldsRequired = ['cpfCnpj', 'razaoSocial'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
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
            ->andWhere('client_doc')->is(clearDoc($postVars['cpfCnpj']))
            ->andWhere('client_status')->is(0)
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Você já tem um cliente com esse documento"
            )->back(["count" => 0]);
            return;
        }

        if (clearDoc($postVars['cpfCnpj']) == '' || $postVars['razaoSocial'] == '') {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Existem campos obrigatórios em branco"
            )->back(["count" => 0]);
            return;
        }

        if ($postVars['status'] == '0' || $postVars['status'] == '1' || $postVars['status'] == '2') {
            $status = $postVars['status'];
        } else {
            $status = '0';
        }
        $dataAtual = date('Y-m-d H:i:s');
        $postClient = [
            'client_ide' => hash('md5', date('YmdHis') . rand(1000, 9999)),
            'client_id_company' => $idReg,
            'client_name' => $postVars['razaoSocial'],
            'client_fantasy' => $postVars['nomeFantasia'],
            'client_doc' => clearDoc($postVars['cpfCnpj']),
            'client_ie' => clearDoc($postVars['inscricaoEstadual']),
            'client_im' => clearDoc($postVars['inscricaoMunicipal']),
            'client_address_place' => $postVars['endereco']['logradouro'],
            'client_address_number' => $postVars['endereco']['numero'],
            'client_address_district' => $postVars['endereco']['bairro'],
            'client_address_zip' => $postVars['endereco']['cep'],
            'client_address_code_city' => $postVars['endereco']['codigoCidade'],
            'client_address_city' => $postVars['endereco']['cidade'],
            'client_address_state' => $postVars['endereco']['estado'],
            'client_address_complement' => $postVars['endereco']['complemento'],
            'client_phone' => $postVars['telefone']['celular'],
            'client_email' => $postVars['email'],
            'client_fixed' => $postVars['telefone']['fixo'],
            'client_whats' => $postVars['telefone']['whats'],
            'client_status' => $status,
            'client_type' => $postVars['type'],
            'client_dfe_ult_event_datetime' => date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($dataAtual))),
            'client_dfe_date_consult' => date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($dataAtual))),
            'client_dfe_ult_nsu' => '0'
        ];

        if ($this->db()->insert($postClient)->into('client')) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $postClient['client_ide']]);
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


    public function select(): void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $query_paginator = $this->db()->from('client')->where('client_id_company')->is($idReg);
        $query = $this->db()->from('client')->where('client_id_company')->is($idReg);

        if ($postVars['ide'] != '') {
            $query_paginator = $query_paginator->andWhere('client_ide')->is($postVars['ide']);
            $query = $query->andWhere('client_ide')->is($postVars['ide']);
        }
        if ($postVars['status'] != '') {
            $query_paginator = $query_paginator->andWhere('client_status')->is($postVars['status']);
            $query = $query->andWhere('client_status')->is($postVars['status']);
        }
        if ($postVars['usaDFe'] == 'S') {
            $query_paginator = $query_paginator->andWhere('client_dfe_use')->is(1);
            $query = $query->andWhere('client_dfe_use')->is(1);
        }
        if ($postVars['cpfCnpj'] != '') {
            $query_paginator = $query_paginator->andWhere('client_doc')->is($postVars['cpfCnpj']);
            $query = $query->andWhere('client_doc')->is($postVars['cpfCnpj']);
        }
        if ($postVars['razaoSocial'] != '') {
            $query_paginator = $query_paginator->andWhere('client_name')->like('%' . $postVars['razaoSocial'] . '%');
            $query = $query->andWhere('client_name')->like('%' . $postVars['razaoSocial'] . '%');
        }
        if ($postVars['nomeFantasia'] != '') {
            $query_paginator = $query_paginator->andWhere('client_fantasy')->like('%' . $postVars['nomeFantasia'] . '%');
            $query = $query->andWhere('client_fantasy')->like('%' . $postVars['nomeFantasia'] . '%');
        }
        if ($postVars['email'] != '') {
            $query_paginator = $query_paginator->andWhere('client_email')->like('%' . $postVars['email'] . '%');
            $query = $query->andWhere('client_email')->like('%' . $postVars['email'] . '%');
        }

        if ($postVars['limit'] > 50) {
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
            ->select(['client_id'])
            ->all();
        $result = $query->orderBy('client_id')
            ->limit($limit)
            ->offset($offset)
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
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
                    'type' => $r->client_type,
                    'codigo_dfe' => $r->client_dfe_ult_event_code,
                    'descricao_dfe' => $r->client_dfe_ult_event_desc,
                    'data_dfe' => $r->client_dfe_ult_event_datetime,
                    'status_certificado' => $r->client_status_certificado,
                    'data_certificado' => $r->client_validade_certificado,
					'monitor_status' => $r->client_status_comunicacao,
                    'monitor_descricao' => $r->client_status_comunicacao_texto,
                    'monitor_data' => $r->client_status_comunicacao_data
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

    public function selectIde(): void
    {
        $idReg = $this->checkToken();

        $getHeaders = $this->getHeaders();

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is(clearDoc($getHeaders['ide']))
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if ($result) {
            foreach ($result as $r) {
                unset($fieldsResponse);

                if(isset($getHeaders['forcarConsulta']) && $getHeaders['forcarConsulta'] == 'sim') {
                    $postClientForca['client_forcar_download'] = '1';
                    $this->db()->update('client')->where('client_ide')->is($r->client_ide)->andWhere('client_id_company')->is($idReg)->set($postClientForca);
                }
                if($r->client_forcar_download == '1'){
                    $status_forca = 'SIM';
                }else{
                    $status_forca = 'NÃO';
                }
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
                    'type' => $r->client_type,
                    'codigo_dfe' => $r->client_dfe_ult_event_code,
                    'descricao_dfe' => $r->client_dfe_ult_event_desc,
                    'data_dfe' => $r->client_dfe_ult_event_datetime,
                    'status_certificado' => $r->client_status_certificado,
                    'data_certificado' => $r->client_validade_certificado,
                    'consulta_forcada' => $status_forca,
					'monitor_status' => $r->client_status_comunicacao,
                    'monitor_descricao' => $r->client_status_comunicacao_texto,
                    'monitor_data' => $r->client_status_comunicacao_data
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

    public function update(): void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $getHeaders = $this->getHeaders();
        $fieldsRequired = ['cpfCnpj', 'razaoSocial'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (clearDoc($postVars['cpfCnpj']) == '' || $postVars['razaoSocial'] == '' || $getHeaders['ide'] == '') {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Existem campos obrigatórios em branco"
            )->back(["count" => 0]);
            return;
        }

        $result = $this->db()->from('client')
            ->where('client_id_company')->is($idReg)
            ->andWhere('client_ide')->is($getHeaders['ide'])
            ->orderBy('client_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "IdeCliente não localizado"
            )->back(["count" => 0]);
            return;
        }

        $postClient = [
            'client_name' => $postVars['razaoSocial'],
            'client_fantasy' => $postVars['nomeFantasia'],
            'client_doc' => clearDoc($postVars['cpfCnpj']),
            'client_ie' => clearDoc($postVars['inscricaoEstadual']),
            'client_im' => clearDoc($postVars['inscricaoMunicipal']),
            'client_address_place' => $postVars['endereco']['logradouro'],
            'client_address_number' => $postVars['endereco']['numero'],
            'client_address_district' => $postVars['endereco']['bairro'],
            'client_address_zip' => $postVars['endereco']['cep'],
            'client_address_code_city' => $postVars['endereco']['codigoCidade'],
            'client_address_city' => $postVars['endereco']['cidade'],
            'client_address_state' => $postVars['endereco']['estado'],
            'client_address_complement' => $postVars['endereco']['complemento'],
            'client_phone' => $postVars['telefone']['celular'],
            'client_email' => $postVars['email'],
            'client_type' => $postVars['type'],
            'client_status' => $postVars['status'],
            'client_fixed' => $postVars['telefone']['fixo'],
            'client_whats' => $postVars['telefone']['whats']
        ];

        $result = $this->db()->update('client')->where('client_ide')->is($getHeaders['ide'])->andWhere('client_id_company')->is($idReg)->set($postClient);
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
}

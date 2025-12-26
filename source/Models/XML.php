<?php

namespace Source\Models;

use Source\Conn\DataLayer;

class XML extends DataLayer
{
    public function create():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $postVars['ide'] = md5(date('Y-mdHis').rand(10000,99999));

        $implementedArquivo = [
            'nfe'
        ];

        if(!in_array($postVars['tipo'], $implementedArquivo)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Documento ainda não implementado"
            )->back(["count" => 0]);
            return;
        }

        if(count($postVars['chaves']) <= 0){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "É preciso inserir as chaves!"
            )->back(["count" => 0]);
            return;
        }

        for($x=0;$x<count($postVars['chaves']);$x++){
            $postChave['monitor_xml_ide'] = md5(date('Y-mdHis').rand(10000,99999));
            $postChave['monitor_xml_id_company'] = $idReg;
            $postChave['monitor_xml_chave'] = trim($postVars['chaves'][$x]);
            $postChave['monitor_xml_status'] = '0';
            $postChave['monitor_xml_data_criacao'] = date('Y-m-d H:i:s');
            $postChave['monitor_xml_tipo'] = $postVars['tipo'];
            $postChave['monitor_xml_lixeira'] = '0';
            $result = $this->db()->from('monitor_xml')
                ->where('monitor_xml_id_company')->is($idReg)
                ->andWhere('monitor_xml_chave')->is(trim($postVars['chaves'][$x]))
                ->orderBy('monitor_xml_id', 'DESC')
                ->limit('1')
                ->select()
                ->all();
            if(!$result){
                $this->db()->insert($postChave)->into('monitor_xml');
                unset($postChave);
            }

            if($result){
                $chaveError .= trim($postVars['chaves'][$x]).',';
            }
        }

        $this->call(
            '200',
            'Sucesso',
            '',
            "ok",
            "Operação realizada com sucesso"
        )->back(["chaves_problemas" => $chaveError]);
        return;
    }

    public function delete():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $upChave['monitor_xml_lixeira'] = '1';
        $this->db()->update('monitor_xml')->where('monitor_xml_chave')->is($postVars['chave'])->andWhere('monitor_xml_id_company')->is($idReg)->set($upChave);
        $this->call(
            '200',
            'Sucesso',
            '',
            "ok",
            "Operação realizada com sucesso"
        )->back(["count" => ""]);
        return;
    }

    public function webhookDelete():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();

        $upChave['monitor_xml_webhook_lixeira'] = '1';
        $this->db()->update('monitor_xml_webhook')->where('monitor_xml_webhook_ide')->is($postVars['ide'])->andWhere('monitor_xml_webhook_id_company')->is($idReg)->set($upChave);
        $this->call(
            '200',
            'Sucesso',
            '',
            "ok",
            "Operação realizada com sucesso"
        )->back(["count" => ""]);
        return;
    }

    public function webhookCreate():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $fieldsRequired = ['url'];

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

        $postWebhook['monitor_xml_webhook_id_company'] = $idReg;
        $postWebhook['monitor_xml_webhook_ide'] = md5(date('Y-mdHis').rand(10000,99999));
        $postWebhook['monitor_xml_webhook_url'] = $postVars['url'];
        $postWebhook['monitor_xml_webhook_lixeira'] = '0';



        if($this->db()->insert($postWebhook)->into('monitor_xml_webhook')){
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $postWebhook['monitor_xml_webhook_ide']]);
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

    public function chave():void
    {
        $idReg = $this->checkToken();
        $postVars = $this->postVars();



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://futuramanager.com.br/sped-nfe-master/geracao/ConsultaChave.php?chave='.$postVars['chave'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=ebf48ae23eff693ff6d9683e83853478'
            ),
        ));

        $response = curl_exec($curl);

        $arr = json_decode($response, true);
        if($arr['type'] == 'ok'){
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno_sefaz" => $arr['msg']]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            $arr['msg']
        )->back(["count" => 0]);
        return;
    }
}

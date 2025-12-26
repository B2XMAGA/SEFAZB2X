<?php

namespace Source\Models;

use Source\Conn\DataLayer;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends DataLayer{

    public function generateToken(): void
    {
        $result = $this->db()->from('company')
            ->where('company_client_id')->is($_SERVER['PHP_AUTH_USER'])
            ->andWhere('company_client_secret')->is($_SERVER['PHP_AUTH_PW'])
            ->orderBy('company_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){
            foreach($result as $r){
                $date           = new \DateTimeImmutable();
                $expire_at     = $date->modify('+1440 minutes')->getTimestamp();
                //$expire_at     = $date->modify('+5 seconds')->getTimestamp();

                $request_data = [
                    'iat'  => $date->getTimestamp(),
                    'email'  => $r->company_email,
                    'nbf'  => $date->getTimestamp(),
                    'exp'  => $expire_at,
                    'key' => $r->company_key
                ];

                $jwt = JWT::encode($request_data, JWT_KEY, 'HS256');

                $postToken['company_token'] = $jwt;
                $postToken['company_iat_token'] = $request_data['iat'];
                $postToken['company_exp_token'] = $request_data['exp'];

                $this->db()->update('company')->where('company_id')->is($r->company_id)->set($postToken);

                $this->call(
                    '200',
                    'Sucesso',
                    '.',
                    "ok",
                    "Token gerado com sucessosssssss"
                )->back(["token" => $jwt, "valid" => $request_data['exp']]);
                return;
            }
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Credenciais inválidas"
        )->back(["count" => 0]);
        return;

    }

}

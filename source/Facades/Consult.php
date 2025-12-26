<?php

namespace Source\Facades;

class Consult
{

    private $bearerToken;
    private $url;
    private $headers;
    private $fields;

    public function __construct($bearerToken, $url)
    {
        $this->bearerToken = $bearerToken;
        $this->url = $url;
    }

    protected function headers(array $headers)
    {
        if (!$headers) { return; }
        foreach ($headers as $k => $v) {
            $this->header($k,$v);
        }
    }

    protected function header(string $key, string $value)
    {
        if(!$key){ return; }
        $keys = filter_var($key, FILTER_SANITIZE_STRIPPED);
        $values = filter_var($value, FILTER_SANITIZE_STRIPPED);
        $this->headers[] = "{$keys}: {$values}";
    }

    protected function fields(array $fields, string $format="json")
    {
        if($format == "json") {
            $this->fields = (!empty($fields) ? json_encode($fields) : null);
            return;
        }
        if($format == "query"){
            $this->fields = (!empty($fields) ? http_build_query($fields) : null);
            return;
        }
    }


    public function consultCnpj($cnpj)
    {
        $this->headers([
            "Authorization" => $this->bearerToken
        ]);

        $ci = curl_init("{$this->url}/{$cnpj}?strategy=CACHE_IF_ERROR&simples=true");
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => ($this->headers),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function consultIE($cnpj)
    {
        $this->headers([
            "Authorization" => $this->bearerToken
        ]);

        $ci = curl_init("{$this->url}/ccc?taxId={$cnpj}&states=BR");
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => ($this->headers),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function consultCep($cep)
    {
        $ci = curl_init("{$this->url}/{$cep}");
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function ecacCaixaPostal($pkcs12_cert, $pkcs12_pass, $perfil_procurador_cnpj, $perfil_sucessora_sucedida_cnpj, $ignora_nao_lidas, $ignora_lidas)
    {
        $params = array(
          "pkcs12_cert"                    => aes_encrypt($pkcs12_cert, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "pkcs12_pass"                    => aes_encrypt($pkcs12_pass, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "perfil_procurador_cnpj"         => $perfil_procurador_cnpj,
          "perfil_sucessora_sucedida_cnpj" => $perfil_sucessora_sucedida_cnpj,
          "ignora_nao_lidas"               => $ignora_nao_lidas,
          "ignora_lidas"                   => $ignora_lidas,
          "token"                          => "HzCZXFnG607oo04ZQGO2l09tal0kGwmOq1D1kEGh",
          "timeout"                        => 300
        );

        $urlD = base64_decode('aHR0cHM6Ly9hcGkuaW5mb3NpbXBsZXMuY29tL2FwaS92Mi9jb25zdWx0YXMvZWNhYy9jYWl4YS1wb3N0YWw=');
        $ci = curl_init($urlD);
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }


    public function ecacComprovantePagamento($pkcs12_cert, $pkcs12_pass, $perfil_procurador_cnpj, $data_inicio, $data_fim, $documento_numero)
    {
        $params = array(
          "pkcs12_cert"                    => aes_encrypt($pkcs12_cert, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "pkcs12_pass"                    => aes_encrypt($pkcs12_pass, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "perfil_procurador_cnpj"         => $perfil_procurador_cnpj,
          "data_inicio"                    => $data_inicio,
          "data_fim"                       => $data_fim,
          "documento_numero"               => $documento_numero,
          "token"                          => "HzCZXFnG607oo04ZQGO2l09tal0kGwmOq1D1kEGh",
          "timeout"                        => 300
        );

        $urlD = base64_decode('aHR0cHM6Ly9hcGkuaW5mb3NpbXBsZXMuY29tL2FwaS92Mi9jb25zdWx0YXMvZWNhYy9jb21wcm92YW50ZS1wYWdhbWVudG8=');
        $ci = curl_init($urlD);
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function ecacSituacaoFiscal($pkcs12_cert, $pkcs12_pass, $perfil_procurador_cnpj)
    {
        $params = array(
          "pkcs12_cert"                    => aes_encrypt($pkcs12_cert, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "pkcs12_pass"                    => aes_encrypt($pkcs12_pass, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "perfil_procurador_cnpj"         => $perfil_procurador_cnpj,
          "token"                          => "HzCZXFnG607oo04ZQGO2l09tal0kGwmOq1D1kEGh",
          "timeout"                        => 300
        );

        $urlD = base64_decode('aHR0cHM6Ly9hcGkuaW5mb3NpbXBsZXMuY29tL2FwaS92Mi9jb25zdWx0YXMvZWNhYy9zaXR1YWNhby1maXNjYWw=');
        $ci = curl_init($urlD);
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function ecacPedidoRestituicao($pkcs12_cert, $pkcs12_pass, $perfil_procurador_cnpj, $perdcomp)
    {
        $params = array(
          "pkcs12_cert"                    => aes_encrypt($pkcs12_cert, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "pkcs12_pass"                    => aes_encrypt($pkcs12_pass, "w8hA4SptesbgBaAejywHITsUGVXFGMNwiD2SeexB"),
          "perfil_procurador_cnpj"         => $perfil_procurador_cnpj,
          "perdcomp"                       => $perdcomp,
          "token"                          => "HzCZXFnG607oo04ZQGO2l09tal0kGwmOq1D1kEGh",
          "timeout"                        => 300
        );

        $urlD = base64_decode('aHR0cHM6Ly9hcGkuaW5mb3NpbXBsZXMuY29tL2FwaS92Mi9jb25zdWx0YXMvZWNhYy9wZXJkY29tcA==');
        $ci = curl_init($urlD);
        curl_setopt_array($ci,[
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => true
        ]);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

}

<?php


namespace Source\Conn;


use Opis\Database\Connection;
use Opis\Database\Database;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

/**
 * Class DataLayer
 * @package Source\Conn
 */
class DataLayer
{
    /**
     * @var string
     */
    protected $dsn = DSN;
    /**
     * @var string
     */
    protected $user = USER;
    /**
     * @var string
     */
    protected $pass = PASS;

    /**
     * @var
     */
    private $response;

    /**
     * @return Connection
     */


    private function conn()
    {
        $connection = new Connection(
            $this->dsn,
            $this->user,
            $this->pass
        );
        $connection->persistent();
        return $connection;
    }

    /**
     * @return Database
     */
    public function db()
    {
        return new Database($this->conn());
    }

    /**
     * @param $table
     * @param $column
     * @param $register
     * @return void
     */
    public function lastReg($table, $column, $register)
    {
        $result = $this->db()->from($table)
            ->orderBy($column, 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if($result){
            foreach ($result as $r){
                return $r->$register;
            }

        }
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $getHeaders = getallheaders();
        if($getHeaders['Ide']){
            $getHeaders['ide'] = $getHeaders['Ide'];
        }
        if($getHeaders['ide']){
            $getHeaders['Ide'] = $getHeaders['ide'];
        }
        if($getHeaders['Ideaccount']){
            $getHeaders['ideAccount'] = $getHeaders['Ideaccount'];
        }
        if($getHeaders['Month']){
            $getHeaders['month'] = $getHeaders['Month'];
        }
        if($getHeaders['Year']){
            $getHeaders['year'] = $getHeaders['Year'];
        }

        if($getHeaders['Ideclient']){
            $getHeaders['ideClient'] = $getHeaders['Ideclient'];
        }

        if($getHeaders['Forcarconsulta']){
            $getHeaders['forcarConsulta'] = $getHeaders['Forcarconsulta'];
        }
        return $getHeaders;
    }

    public function fieldsRequired(array $fields, array $data): bool
    {
        if (count(array_diff($fields, array_keys($data))) > 0) {
            return false;
        }

        return true;

    }

    /**
     * @return mixed
     */
    public function postVars(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    public function checkToken()
    {


        $bearer = $_SERVER['HTTP_AUTHORIZATION'];

        $token = explode('Bearer', $bearer);

        try {
            $decoded = JWT::decode(trim($token['1']), new Key(JWT_KEY, 'HS256'));


            $date = new \DateTimeImmutable();

            $result = $this->db()->from('company')
                ->where('company_key')->is(trim($decoded->key))
                ->orderBy('company_id', 'DESC')
                ->limit('1')
                ->select()
                ->all();

            if($result){
                if($date->getTimestamp() > $decoded->exp){
                    $postDebug['debug_id_company'] = '0';
                    $postDebug['debug_end'] = parse_url($_SERVER["REQUEST_URI"])["path"];
                    $postDebug['debug_header'] = json_encode(getallheaders());
                    $postDebug['debug_body'] = file_get_contents('php://input');
                    $postDebug['debug_date_hora'] = date('Y-m-d H:i:s');
                    $this->db()->insert($postDebug)->into('debug');
                    $this->call(
                        '401',
                        'Ops',
                        '',
                        "ops",
                        "Token expirado"
                    )->back(["count" => 0]);
                    exit;
                    return;
                }
                foreach($result as $r){

                    $postDebug['debug_id_company'] = $r->company_id;
                    $postDebug['debug_end'] = parse_url($_SERVER["REQUEST_URI"])["path"];
                    $postDebug['debug_header'] = json_encode(getallheaders());
                    $postDebug['debug_body'] = file_get_contents('php://input');
                    $postDebug['debug_date_hora'] = date('Y-m-d H:i:s');
                    $this->db()->insert($postDebug)->into('debug');

                    return $r->company_id;
                }
            }

            $postDebug['debug_id_company'] = '0';
            $postDebug['debug_end'] = parse_url($_SERVER["REQUEST_URI"])["path"];
            $postDebug['debug_header'] = json_encode(getallheaders());
            $postDebug['debug_body'] = file_get_contents('php://input');
            $postDebug['debug_date_hora'] = date('Y-m-d H:i:s');
            $this->db()->insert($postDebug)->into('debug');
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Token inválido"
            )->back(["count" => 0]);
            exit;
            return;


        }catch (\Exception $e){
            $postDebug['debug_id_company'] = '0';
            $postDebug['debug_end'] = parse_url($_SERVER["REQUEST_URI"])["path"];
            $postDebug['debug_header'] = json_encode(getallheaders());
            $postDebug['debug_body'] = file_get_contents('php://input');
            $postDebug['debug_date_hora'] = date('Y-m-d H:i:s');
            $this->db()->insert($postDebug)->into('debug');
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Token inválido ou expirado"
            )->back(["count" => 0]);
            exit;
            return;
        }
    }

    /**
     * @param int $code
     * @param string $title
     * @param string $footer
     * @param string|null $type
     * @param string|null $message
     * @param string $rule
     * @return $this
     */
    public function call(int $code, string $title, string $footer, string $type = null, string $message = null, string $rule = ''): DataLayer
    {
        http_response_code($code);

        if (!empty($type)) {
            $this->response = [
                "code" => $code,
                "title" => $title,
                "footer" => $footer,
                "type" => $type,
                "message" => (!empty($message) ? $message : null)
            ];
        }
        return $this;
    }

    /**
     * @param array|null $response
     * @return $this
     */
    public function back(array $response = null): DataLayer
    {
        if (!empty($response)) {
            $this->response = (!empty($this->response) ? array_merge($this->response, $response) : $response);
        }

        echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }
}

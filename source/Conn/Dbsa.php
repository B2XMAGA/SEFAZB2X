<?php


namespace Source\Conn;

use Opis\Database\Connection;
use Opis\Database\Database;

/**
 * Class Dbsa
 * @package Source\Conn
 */
class Dbsa
{
    /**
     * @var string
     */
    private $dsn = DSN;
    /**
     * @var string
     */
    private $user = USER;
    /**
     * @var string
     */
    private $pass = PASS;

    /**
     * @var
     */
    private $response;


    /**
     * @return Connection
     */
    public function connection()
    {
        $connection = new Connection(
            $this->dsn,
            $this->user,
            $this->pass
        );
        return $connection;
    }

    /**
     * @return Database
     */
    public function db()
    {
        $db = new Database($this->connection());

        return $db;
    }

    public function call(int $code, string $type = null, string $message = null)
    {
        http_response_code($code);

        if (!empty($type)) {
            $this->response = [
                $rule => [
                    "code" => $code,
                    "type" => $type,
                    "message" => (!empty($message) ? $message : null)
                ]
            ];
        }
        return $this;
    }

    public function back(array $response = null)
    {
        if (!empty($response)) {
            $this->response = (!empty($this->response) ? array_merge($this->response, $response) : $response);
        }

        echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }
}
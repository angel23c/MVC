<?php

namespace App\Modelos;
use App\Modelos\Usuarios;
use PDO;
use PDOException;

class Basededatos
{
    public $servidor = DB_HOST;
    public $dbname = DB_NAME;
    public $username = DB_USER;
    public $password = DB_PASS;
    public $connection = "";
    protected $table = "";
    protected $primarykey = "id";

    public $sql = "";

    function __construct()
    {
        $dsn = "mysql:host=198.57.192.18;dbname=awsmx_playtest;charset=utf8mb4";
        $options = [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->connection = new PDO($dsn, 'awsmx_usplaytest', 'Padel2023$$', $options);
        } catch (PDOException $e) {
            error_log('PDOException - ' . $e->getMessage(), 0);
            http_response_code(500);
            die($e->getMessage());
        }
    }

    function all()
    {
        try {
            $this->sql = "SELECT * FROM {$this->table}";
            $sth = $this->connection->prepare($this->sql);
            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            $result = ["data" => $result];
            return json_encode($result);
        } catch (PDOException $e) {
            error_log('PDOException - ' . $e->getMessage(), 0);
            http_response_code(500);
            die($e->getMessage());
        }
    }

    function find($id)
    {
        $this->sql = "SELECT * FROM {$this->table} WHERE {$this->primarykey} = {$id}";
        return $this;
    }

    function get()
    {
        $setence = $this->connection->prepare($this->sql);
        $setence->execute();
        return json_encode($setence->fetchAll(PDO::FETCH_ASSOC));
    }

    function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = "=";
        }

        if (gettype($value) == "string") {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} '{$value}'";
        } else {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} {$value}";
        }

        return $this;
    }

    function limit($column, $order, $limit = null)
    {
        $this->sql .= " GROUP BY {$column} {$order} LIMIT {$limit}";
        return $this;
    }

    function query($sql)
    {   
        $setence = $this->connection->prepare($sql);
        $setence->execute();
        return json_encode($setence->fetchAll(PDO::FETCH_ASSOC));
    }

    function login($column, $value, $clave)
    {
        if (gettype($value) == "string") {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} = '{$value}'";
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} = {$value}";
        }

        $setence = $this->connection->prepare($sql);
        $setence->execute();
        $setence->setFetchMode(PDO::FETCH_ASSOC);

        $condicion = 0;

        while ($row = $setence->fetch()) {

            if (password_verify($clave, $row["clave"]) && $row["verificado"] == 1) {
                $condicion = 1;
                $id = $row["idusuarios_app"];
                $resetgenerateapi = 0;
                $apiKey = null;
                do {
                    $apiKey = bin2hex(random_bytes(100));
                    $usuario = new Usuarios();
                    $usuario = $usuario->where("api_key", $apiKey)->get();
                    $usuarioEncontrado = json_decode($usuario, true);
                    if (count($usuarioEncontrado) == 0) {
                        $resetgenerateapi = 1;
                    }
                } while ($resetgenerateapi == 0);
                $this->query("UPDATE usuarios_app SET api_key = '$apiKey' WHERE idusuarios_app = '$id'");
                echo json_encode(["data" => [["msg" => $apiKey]]]);

                break; // Salir del bucle si se encuentra una coincidencia
            }
            if (password_verify($clave, $row["clave"])) {
                $condicion = 2;
                break; // Salir del bucle si se encuentra una coincidencia
            }
        }

         if($condicion==2){
            header('HTTP/1.1 200 OK');
            echo json_encode(["data" => [["msg" => "No te has verificado revisa tu correo"]]]);
        }
        else if($condicion ==0) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(["data" => [["msg" => "Datos incorrectos"]]]);
        }
    }

    function save($instance)
    {
        $data = [];
        $vars_clase = get_object_vars($instance);
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $setence = $this->connection->prepare($sql);
        $setence->execute();
        $response = [];

        foreach ($setence as $column) {
            $field = $column["Field"];

            if (array_key_exists($field, $vars_clase) && $vars_clase[$field] != null) {
                $data[$field] = $vars_clase[$field];
                $response[] = $field;
            }
        }

        $response = implode(',', $response);
        $values = "'" . implode("','", $data) . "'";
        $sql = "INSERT INTO {$this->table} ({$response}) VALUES ({$values})";

        $setence = $this->connection->prepare($sql);
        $setence->execute();

        $this->sql = "SELECT * FROM {$this->table} ORDER BY {$this->primarykey} DESC LIMIT 1";
        $result = $this->get();
        return $result;
    }

    function update($instance, $columna, $operator, $val = null)
    {
        $data = [];
        $vars_clase = get_object_vars($instance);
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $setence = $this->connection->prepare($sql);
        $setence->execute();
        $sql = "UPDATE {$this->table} SET ";

        foreach ($setence as $column) {
            $field = $column["Field"];

            if (array_key_exists($field, $vars_clase) && $vars_clase[$field] != null) {
                $sql .= "{$field} = '{$vars_clase[$field]}',";
                
            }
        }

        $sql = rtrim($sql, ",");

        if ($val === null) {
            $val = $operator;
            $columna = "=";
        }
        if (gettype($val) == "string") {
            $sql .= " WHERE {$columna} {$operator} '{$val}'";
        } else {
            $sql .= " WHERE {$columna} {$operator} {$val}";
        }
        $setence = $this->connection->prepare($sql);
        $setence->execute();
    }

    function delete()
    {
        $condition = "";
        $setence = $this->connection->prepare($this->sql);
        $setence->execute();
        foreach ($setence as $row) {
            $condition = $row[$this->primarykey];
            break;
        }

        $this->sql = "DELETE FROM {$this->table} WHERE {$this->primarykey} = {$condition}";
        $setence = $this->connection->prepare($this->sql);
        $setence->execute();
        return json_encode($setence->fetchAll(PDO::FETCH_ASSOC));
    }

    function email($person, $codigo)
    {
        $to = $person;
        $subject = 'Correo de verificación';
        $message = "
        
        https://awsmx.org/playpadel_test/MVC/verificacion/$codigo";
        
        // Encabezados del correo
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= "Content-type: text/html; charset=utf-8\r\n";
        
        // Cabeceras adicionales
        $cabeceras .= 'From: PLAY PADEL MÉXICO <reservas@padelizer.com>' . "\r\n";
        $cabeceras .= 'Cc: reservas@padelizer.com' . "\r\n";
        $cabeceras .= 'Bcc: reservas@padelizer.com' . "\r\n";
        
        // Enviar el correo
        $result = mb_send_mail($to, $subject, $message, $cabeceras);
        
    }
    function emailcambioclave($person, $codigo)
    {
        $to = $person;
        $subject = 'Correo Cambio de clave';
        $message = "
        
        https://awsmx.org/playpadel_test/MVC/emailcambiarclave/$codigo";
        
        // Encabezados del correo
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= "Content-type: text/html; charset=utf-8\r\n";
        
        // Cabeceras adicionales
        $cabeceras .= 'From: PLAY PADEL MÉXICO <reservas@padelizer.com>' . "\r\n";
        $cabeceras .= 'Cc: reservas@padelizer.com' . "\r\n";
        $cabeceras .= 'Bcc: reservas@padelizer.com' . "\r\n";
        
        // Enviar el correo
        $result = mb_send_mail($to, $subject, $message, $cabeceras);
        
    }
    public function newRandomCodigo()
    {
        $codigo = "";
        for ($i = 0; $i < 12; $i++) {
            $numeroAleatorio = rand(0, 9);
            $letraAleatoria = chr(rand(65, 90));
            $codigo .= $numeroAleatorio . $letraAleatoria;
        }
        return $codigo;
    }
}

<?php
namespace Rutas;
use App\Modelos\Usuarios;

class Ruts {
    private static $permitirkey = 0;
    private static $instances = [];
    private static $respuesta = 0;
    private static $name;
    private static $instancia1 = 0;
    private static $routes = [];
    private static $parametros = [];
    private static $condicion = 0;
      
    public static function clearRoutes() {
        self::$routes = [];
    }
    
    public static function init() {
        self::$respuesta = 0;
        return new self();
    }
    
    public static function getInstance($name = 'default') {
        if (self::$instances == 0) {
            self::$name = $name;
            self::$instancia1 = 1;
            self::$instances[$name] = new self($name);
        } else if (self::$name != $name) {
            self::clearRoutes();
            self::$instances[$name] = new self($name);
        }
        
        return self::$instances[$name];
    }
   
    public static function api() {
        self::$condicion = 0;
        return new self();
    }
    
    public static function key() {   
        self::$condicion = 1;
        self::$permitirkey = 1;
        $headers = getallheaders();
        $usuario = new Usuarios();
        $usuario = $usuario->where("api_key", $headers["Key"])->get();

        $usuarioEncontrado = json_decode($usuario, true);
        if (count($usuarioEncontrado) > 0) {
            self::$condicion = 0;
        }
        

        return new self();
    }

    public static function obtenerparametros() {
        return self::$parametros;
    }
    
    public static function get($uri, $callback) {
        $uri = trim($uri, '/');
        $explodedUri = explode('/', $uri);
        $uri = implode('/', array_slice($explodedUri, 0, 1));

        self::$routes["GET"][$uri] = $callback;
        return new self();
    }
    
    public static function post($uri, $callback) {
        $uri = trim($uri, '/');
        self::$routes["POST"][$uri] = $callback;
        return new self();
    }
    
    public static function put($uri, $callback) {
        $uri = trim($uri, '/');
        self::$routes["PUT"][$uri] = $callback;
        return new self();
    }
    
    public static function delete($uri, $callback) {
        $uri = trim($uri, '/');
        self::$routes["DELETE"][$uri] = $callback;
        return new self();
    }
   
    public static function dispatch() {
        global $mensajeImpreso;
        if ($mensajeImpreso) {
            return;
        }
        $uri = $_SERVER["REQUEST_URI"];
        $uri = trim($uri, '/');
        $explodedUri = explode('/', $uri);
        $values = array_slice($explodedUri, 3);
        self::$parametros = array();
        
        foreach ($values as $index => $value) {
            self::$parametros["var" . ($index + 1)] = $value;
        }
        
        self::$parametros = json_decode(json_encode(self::$parametros), false);
        $uri = implode('/', array_slice($explodedUri, 0, 3));
        $method = $_SERVER["REQUEST_METHOD"];

        foreach (self::$routes[$method] as $route => $callback) {
            if (self::$condicion == 0) {
                if ($route == substr($uri, 19)) {
                    if (is_callable($callback)) {
                        $response = $callback();
                    }
                    if (is_array($callback)) {
                        $controller = new $callback[0];
                        $mensajeImpreso =true;

                        foreach ($callback[1] as $methods => $function) {
                            if ($callback[1] === $function) {
                                self::$respuesta = 1;
                                $returndata = $controller->$function();
                                
                                if (isset($returndata)) {
                                    echo $returndata;
                                }
                                
                                return;
                            }
                        }
                    }
                    
                    return;
                }
            } else {
                if (self::$respuesta == 0 || self::$condicion == 1) { 
                    self::$respuesta = 1;
                    echo json_encode(["data" => [["msg" => "El usuario no cuenta con su api_key o es incorrecta"]]]);

                    $mensajeImpreso =true;

                    // Actualizar el valor de $mensajeImpreso
                    return;
                }
            }
        }
        
        if (self::$respuesta == 0) { 
            self::$respuesta = 1;
            http_response_code(404); 
        }
        
        self::$condicion = 0; // Restablecer la condición a 0
        self::$permitirkey = 0; // Restablecer la bandera de permitir key a 0
    }
}

?>
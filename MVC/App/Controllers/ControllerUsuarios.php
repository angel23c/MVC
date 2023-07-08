<?php
namespace App\Controllers;

use Rutas\Ruts;
use App\Modelos\Usuarios;
use DateTime; // Asegúrate de importar la clase DateTime
use App\Controllers\ControllerExtension;
class ControllerUsuarios {
    public function registerapp()
    {
        date_default_timezone_set("America/Phoenix");
        $usuario = new Usuarios();
        global $datos;
            $fechaActual = new DateTime();
            $fechaFormateada = $fechaActual->format('Y-m-d H:i:s');
            $codigo = $usuario->newrandomcodigo();
            $usuario->usuario = $datos->usuario;
            $usuario->clave = password_hash($datos->clave, PASSWORD_DEFAULT);
            $usuario->correo = $datos->correo;
            $usuario->telefono = $datos->telefono;
            $usuario->ultimo_acceso = $fechaFormateada;
            $usuario->codigo_verificacion =$codigo ; // Asignar el código generado
            $apiKey = null;
            do {
                $apiKey = bin2hex(random_bytes(100));
                $apiusuario = new Usuarios();
                $apiusuario = $apiusuario->where("api_key", $apiKey)->get();
                $usuarioEncontrado = json_decode($apiusuario, true);
                if (count($usuarioEncontrado) == 0) {
                    $resetgenerateapi = 1;
                }
            } while ($resetgenerateapi == 0);

            $usuario->api_key =$apiKey ; // Asignar el código generado
            $usuario->email($datos->correo, $codigo);
             $usuario->save($usuario);
            $obtenerusuario = new Usuarios();
            $obtenerusuario =  $obtenerusuario->query("SELECT * FROM usuarios_app order by idusuarios_app desc limit 1");
            $usuarioEncontrado = json_decode($obtenerusuario, true);
            echo json_encode(["data" => [["msg" =>$usuarioEncontrado[0]["api_key"] ]]]);


    }
    public static function loginapi()
    {   
        $headers = getallheaders();
        $usuario = new Usuarios();
        $usuario =$usuario->where("api_key",$headers["Key"])->get();
        $usuarioEncontrado = json_decode($usuario, true);
        if(count($usuarioEncontrado) > 0){
            echo json_encode(["data" => [["msg" =>"Usuario Logeado" ]]]);

        }
 


    }
    public function loginapp()
    {
             global $datos;
            $usuario = new Usuarios();
            $usuario->login("correo", $datos->correo, $datos->clave);   
    }

    public function verificacion()
{       
        $usuario = new Usuarios();
        $parametros = Ruts::obtenerparametros();
        $resultado = $usuario->where("codigo_verificacion", $parametros->var1)->get();
        $usuarioEncontrado = json_decode($resultado, true);
        if (!empty($usuarioEncontrado)) {
           // ;
                $usuarioupdate = new Usuarios();
                $usuario->find($usuarioEncontrado[0]["idusuarios_app"])->get();
                $usuarioEncontrado = json_decode($resultado, true);
                $usuarioupdate->verificado =1;
                $usuarioupdate->update($usuarioupdate,"idusuarios_app","=",
                $usuarioEncontrado[0]["idusuarios_app"]
                );
                echo "Ya estas verificado ". $usuarioEncontrado[0]["usuario"];
        } else {
            echo "Usuario no encontrado";
        }
}
    public function correocambioclave(){
        global $datos;
        $usuario = new Usuarios();
        $resultado = $usuario->where("correo", $datos->correo)->get();
        $usuarioEncontrado = json_decode($resultado, true);
        $usuario->emailcambioclave($datos->correo, $usuarioEncontrado[0]["idusuarios_app"]);
        echo json_encode(["data" => [["msg" => "Correo Enviado"]]]);
    }

    public function cambiarclave() {
        global $datos;
        $usuario = new Usuarios();
        $usuario->clave = password_hash($datos->clave, PASSWORD_DEFAULT);
        $usuario->update($usuario,"idusuarios_app","=",$datos->idusuarios_app); 
        
    }
    public function emailcambiarclave()
    {       
        $usuario = new Usuarios();
        $parametros = Ruts::obtenerparametros();
       return ControllerExtension::view("Users",[
            "idusuario" => $parametros->var1,
         ]);
    } 
    public function verusuarios(){
        $usuario = new Usuarios();
       echo $usuario->all();
    }
    

}
?>

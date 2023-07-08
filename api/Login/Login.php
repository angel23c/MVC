<?php
if(file_exists("../mysql/MySqlConnection.php")){
  require_once("../mysql/MySqlConnection.php");
}
else{
  if(file_exists("./mysql/MySqlConnection.php")){
    require_once("./mysql/MySqlConnection.php");
  }
  else if(file_exists("../mysql/MySqlConnection.php")){
    require_once("../mysql/MySqlConnection.php");
  }
  else if(file_exists("../../mysql/MySqlConnection.php")){
    require_once("../../mysql/MySqlConnection.php");
  }
}
class Promociones extends MySqlConnection
{
    function insertarPromocion($nombre,$descripcion,$fecha_inicio,$fecha_final,$ruta){
        $query = "insert into promociones(nombre,descripcion,fecha_inicio,fecha_final,imagen,status)
        values(?,?,?,?,?,?)";
        echo $query;
        return $this->getLastID($query, array($nombre,$descripcion,$fecha_inicio,$fecha_final,$ruta,1));
      }
      function obtenerPromociones(){
        $query = "select p.*, DATE_FORMAT(p.fecha_inicio, '%d/%m/%Y') as fecha_inicio_d, DATE_FORMAT(p.fecha_final, '%d/%m/%Y') as fecha_final_d from promociones as p;
        ";
        return json_encode($this->ExecuteReader($query));
      }
      function CambiarStatusPromocion($status,$idpromociones){
        $query = "update promociones set status = ? where idpromociones = ?";
         echo $query;
        return $this->ExecuteQuery($query, array($status,$idpromociones));
      }
      function actualizarPromocion($nombre,$descripcion,$fecha_inicio,$fecha_final,$imagen,$idpromociones){
        if($imagen !=null){
            $query = "update promociones set nombre = ?, descripcion = ?,
            fecha_inicio = ?, fecha_final = ?, imagen = ? where idpromociones = ?";
            return $this->ExecuteQuery($query, array($nombre,$descripcion,$fecha_inicio,$fecha_final,$imagen,$idpromociones));
        }
        if($imagen ==null){
            $query = "update promociones set nombre = ?, descripcion = ?, 
            fecha_inicio = ?, fecha_final = ? where idpromociones = ?";
            return $this->ExecuteQuery($query, array($nombre,$descripcion,$fecha_inicio,$fecha_final,$idpromociones));
        }
      }
    }
?>
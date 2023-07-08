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
class Membresias extends MySqlConnection
{
  function insertarMembresias($nombre,$pago_membresia,$aplica_inscripcion,$costo_inscripcion,$costo_membresia,$total_personas,$idcategoria_membresia,$costom_domiciliado){
    $query = "insert into membresias(nombre,pago_membresia,aplica_inscripcion,costo_inscripcion,costo_membresia,total_personas,idcategoria_membresia,costom_domiciliado)
    values(?,?,?,?,?,?,?,?)";
    return $this->getLastID($query, array($nombre,$pago_membresia,$aplica_inscripcion,$costo_inscripcion,$costo_membresia,$total_personas,$idcategoria_membresia,$costom_domiciliado));
  }

  function obtenerMembresias(){
    $query = "select m.*, cm.nombre as tipo from membresias as m
    inner join categoria_membresia as cm on cm.idcategoria_membresia = m.idcategoria_membresia";
    return json_encode($this->ExecuteReader($query));
  }
  
  function ActualizarMembresia($nombre,$pago_membresia,$aplica_inscripcion,$costo_inscripcion,$costo_membresia,$total_personas,$idcategoria_membresia,$costom_domiciliado,$idmembresia){
    $query = "update membresias set nombre = ?, pago_membresia = ?,
    aplica_inscripcion = ?, costo_inscripcion = ?, costo_membresia = ?, 
    total_personas = ?, idcategoria_membresia = ?, 
    costom_domiciliado = ? where idmembresias = ?";
    return $this->getLastID($query, array($nombre,$pago_membresia,$aplica_inscripcion,$costo_inscripcion,$costo_membresia,$total_personas,$idcategoria_membresia,$costom_domiciliado,$idmembresia));
  }

  function CambiarStatusMembresia($status,$idmembresias){
    $query = "update membresias set status = ? where idmembresias = ?";
    return $this->ExecuteQuery($query, array($status,$idmembresias));
  }

  function obtenerMembresia($idmembresias){
    $query = "select * from membresias where idmembresias = '$idmembresias'";
    return json_encode($this->ExecuteReader($query));
  }

  function traerCatMembresias(){
    $query = "select * from categoria_membresia order by idcategoria_membresia desc";
    return json_encode($this->ExecuteReader($query));
  }

  function insertarCatMembresia($nombre,$accesos_mes,$descuento_torneo,$nopenalizacion,$dias_anticipacion){
    $query = "insert into categoria_membresia(nombre,accesos_mes,descuento_torneo,nopenalizacion,dias_anticipacion)
    values(?,?,?,?,?)";
    return $this->ExecuteQuery($query, array($nombre,$accesos_mes,$descuento_torneo,$nopenalizacion,$dias_anticipacion));
  }

  function actualizarCatMembresia($nombre,$accesos_mes,$descuento_torneo,$nopenalizacion,$dias_anticipacion,$idcategoria_membresia){
    $query = "update categoria_membresia set nombre = ?,accesos_mes = ?,descuento_torneo = ?,
    nopenalizacion = ?,dias_anticipacion = ? where idcategoria_membresia = ?";
    return $this->ExecuteQuery($query, array($nombre,$accesos_mes,$descuento_torneo,$nopenalizacion,$dias_anticipacion,$idcategoria_membresia));
  }

  function obtenerCatMembresia($idcategoria_membresia){
    $query = "select * from categoria_membresia where idcategoria_membresia = '$idcategoria_membresia'";
    return json_encode($this->ExecuteReader($query));
  }
  function obteneraccesosocios(){
    $query = "select s.idsocios,s.nombre,cm.accesos_mes,count(rj.jugador_socio) as 'accesos_usados'
    from reservas as r inner join reservas_jugadores as rj on rj.reservas = r.idreservas
    inner join socios as s on s.idsocios = rj.jugador_socio inner join membresias as m on m.idmembresias 
    = s.idmembresias inner join categoria_membresia as cm on cm.idcategoria_membresia = m.idcategoria_membresia
    where MONTH(r.fecha_inicio) = MONTH(CURDATE()) and r.status =1
    group by s.idsocios;
  
    ";
    return json_encode($this->ExecuteReader($query));
  }
}
?>
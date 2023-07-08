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
class Reservas extends MySqlConnection
{
  function insertarReserva($fecha_inicio,$hora_inicio,$hora_fin,$tipo_reserva,$observacion,$descripcion){
    $idadministrador = $_COOKIE["idadministradores"];
    $query = "insert into reservas(fecha_inicio,hora_inicio,hora_fin,tipo_reserva,observacion,descripcion,idadministradores)
    values(?,?,?,?,?,?,?)";
    return $this->getLastID($query, array($fecha_inicio,$hora_inicio,$hora_fin,$tipo_reserva,$observacion,$descripcion,$idadministrador));
    
  }
  function InsertarReporteReserva($idreserva,$accion){
    $idadministrador = $_COOKIE["idadministradores"];
    $query = "insert into reportes_reserva(idreserva,accion,id_administrador)
    values(?,?,?)";
    return $this->getLastID($query, array($idreserva,$accion,$idadministrador));
    
  }
  function ActualizarReporteReserva($idreserva,$accion){
    $idadministrador = $_COOKIE["idadministradores"];
    $query = "insert into reportes_reserva(idreserva,accion,id_administrador)
    values(?,?,?)";
    return $this->getLastID($query, array($idreserva,$accion,$idadministrador));
    
  }
  function ReporteReserva($idreserva){
    $query = "select ad.nombre from reportes_reserva as rp inner join administradores as ad on ad.idadministradores = rp.id_administrador 
    where rp.accion =2 and rp.idreserva = '$idreserva' order by ad.nombre desc limit 1";
    return json_encode($this->ExecuteReader($query));
  }
  function obtenerReservasMultiples($id){
   
    $query = " select * from reservas where reservas.idpadre is null and reservas.idreservas ='$id'
    union all
    select * from reservas where  reservas.idpadre ='$id'";
    return json_encode($this->ExecuteReader($query));
  }
  function obtenercanchasreservas($id){
    $query = "select canchas.nombre,canchas.idcanchas from reservas inner join reservas_canchas on reservas_canchas.idreservas = reservas.idreservas
    inner join canchas on canchas.idcanchas = reservas_canchas.idcancha where reservas.idreservas = '$id'";
    return json_encode($this->ExecuteReader($query));
  }
  function obtenerReservasxCliente(){
    $query = "
    SELECT * FROM(
        SELECT Conteo.fecha_inicio, Conteo.hora_fin,Conteo.idpersona,count(Conteo.idpersona) as reservas_dia,Conteo.nombre,Conteo.tipo FROM (
             SELECT * FROM (
             select reservas.fecha_inicio,reservas.hora_fin,CONVERT(ADDTIME( group_concat(reservas.fecha_inicio, ' 00:00:00'), reservas.hora_fin),DATETIME) as dtf,
             socios.idsocios as idpersona,count(socios.idsocios) as reservas_dia,socios.nombre,'Socio' as tipo
                   from socios
                    inner join reservas_jugadores on reservas_jugadores.jugador_socio = socios.idsocios
                    inner join reservas on reservas.idreservas=
                   reservas_jugadores.reservas where reservas.status =1 and reservas.penalizacion = 0
              group by reservas_jugadores.idreservas_jugadores
             ) as t
             WHERE t.fecha_inicio>DATE(NOW()) or t.fecha_inicio=DATE(NOW()) and t.hora_fin >=DATE_SUB(CURTIME(), INTERVAL +1 HOUR)
             UNION ALL
             SELECT * FROM (
             select reservas.fecha_inicio,reservas.hora_fin,CONVERT(ADDTIME( group_concat(reservas.fecha_inicio,  '00:00:00'), reservas.hora_fin),DATETIME) as dtf,
             invitados.idinvitados as idpersona,count(invitados.idinvitados) as reservas_dia,invitados.nombre,'Invitado' as tipo
                   from invitados
                    inner join reservas_jugadores on reservas_jugadores.jugador_invitado = invitados.idinvitados
                    inner join reservas on reservas.idreservas=
                   reservas_jugadores.reservas where reservas.status =1 and reservas.penalizacion = 0
              group by reservas_jugadores.idreservas_jugadores
             ) as t
             WHERE t.fecha_inicio>DATE(NOW()) or t.fecha_inicio=DATE(NOW()) and t.hora_fin >=DATE_SUB(CURTIME(), INTERVAL +1 HOUR)
             ) AS Conteo 
             group by Conteo.idpersona 
       ) as Respuesta
       where Respuesta.reservas_dia>=2 
    ";
    return json_encode($this->ExecuteReader($query));
  }
  function inscritosclinicas($idreserva){
    $query = "select count(*) as inscritos_clinica
    from inscritos_clinica as ic
    inner join pagos_clinicas as pc on pc.idpagos_clinicas = ic.idpagos_clinicas
    where pc.idclinicas = '$idreserva'";
    return json_encode($this->ExecuteReader($query));
  }
  function obteneridpadre($id){
   
    $query = "select * from reservas where reservas.idreservas ='$id'";
    return json_encode($this->ExecuteReader($query));
  }

  function insertarReservaHija($fecha_inicio,$hora_inicio,$hora_fin,$tipo_reserva,$observacion,$descripcion,$idpadre){
    $idadministrador = $_COOKIE["idadministradores"];
    $query = "insert into reservas(fecha_inicio,hora_inicio,hora_fin,tipo_reserva,observacion,descripcion,idadministradores,idpadre)
    values(?,?,?,?,?,?,?,?)";
    return $this->getLastID($query, array($fecha_inicio,$hora_inicio,$hora_fin,$tipo_reserva,$observacion,$descripcion,$idadministrador,$idpadre));
  }
  function obtenerReservas($fecha){
    $query = "select reservas.*,canchas.nombre as cancha,canchas.idcanchas as idcanchas,administradores.nombre as administrador,reportes_reserva.accion from reservas INNER JOIN reservas_canchas on reservas_canchas.idreservas 
    = reservas.idreservas INNER JOIN canchas on canchas.idcanchas = reservas_canchas.idcancha INNER JOIN administradores on administradores.idadministradores
    = reservas.idadministradores LEFT JOIN reportes_reserva on reportes_reserva.idreserva = reservas.idreservas   
    where fecha_inicio = '$fecha' and reservas.status=1 and reservas.penalizacion =0 order by  hora_inicio";
    return json_encode($this->ExecuteReader($query));
  }
  
  function obtenerhorariosocupados(){
    $query = "select reservas.*,canchas.nombre as cancha,socios.nombre as nombre from reservas INNER JOIN reservas_canchas on reservas_canchas.idreservas = reservas.idreservas INNER JOIN socios on socios.idsocios = reservas.cliente INNER JOIN canchas on canchas.idcanchas = reservas_canchas.idcancha";
    return json_encode($this->ExecuteReader($query));
  }
 
  function obtenerultimoid(){
    $query = "select MAX(idreservas) as id FROM reservas;";
    return json_encode($this->ExecuteReader($query));
  }
  function InsertarJugadores($reservas,$jugadores,$hora_ini,$hora_fin,$fecha,$idcancha,$accion){
    $jugadores = json_decode($jugadores);
    $arrayjugadores = array();

    foreach ($jugadores as $key => $value) {
        $query = "insert into reservas_jugadores(reservas,jugador_socio,jugador_invitado,status)
        values(?,?,?,?)
        ";
    if($value->tipo == "Socio"){
       
        $this->getLastID($query, array($reservas,$value->id,0,1));
        $query = "select * from socios where idsocios ='$value->id';
        ";
         $socio = json_encode($this->ExecuteReader($query));
        $socio = json_decode($socio);
        foreach($socio as $value){
            // echo $value->nombre;
            // echo $value->correo;
            // echo $value->celular;
          $arrayjugador = array(
            "nombre"=>$value->nombre,
            "correo"=>$value->correo,
            "telefono"=>$value->telefono,
          );
          array_push($arrayjugadores, $arrayjugador);

          }
    }
    else if($value->tipo ="Invitado")
    {
       
         $this->getLastID($query, array($reservas,0,$value->id,0));
         $query = "select * from invitados where idinvitados ='$value->id';
         ";
         $invitado = json_encode($this->ExecuteReader($query));
         $invitado = json_decode($invitado);
         foreach($invitado as $value){
            //  echo $value->nombre;
            //  echo $value->correo;
            //  echo $value->celular;
          $arrayjugador = array(
            "nombre"=>$value->nombre,
            "correo"=>$value->correo,
            "telefono"=>$value->telefono,
          );

          array_push($arrayjugadores, $arrayjugador);


         }
    }
    }
    foreach($arrayjugadores as $jugador)
    {
        $this->enviareserva($reservas,$jugador["correo"],$jugador["nombre"],$jugador["telefono"],$hora_ini,$hora_fin,$fecha,$idcancha,$accion);

     
    }


    return;
  }
//,$nombre,$fecha,$hora_ini,$hora_fin,$lugar,$cel
   function enviareserva($reservas,$correo,$nombre,$celular,$hora_ini,$hora_fin,$fecha,$idcancha,$accion,$cancha=null){
    date_default_timezone_set("America/Phoenix");
    $query = "(select s.nombre as jugador, 'Socio' as tipo, rj.status as pagado,
    rj.jugador_socio as idjugador, rj.idreservas_jugadores
    from awsmx_playtest.reservas_jugadores as rj
    inner join awsmx_playtest.socios as s on s.idsocios = rj.jugador_socio
    where rj.reservas = '$reservas')
    union
    (select i.nombre as jugador, 'Invitado' as tipo, rj.status as pagado,
    rj.jugador_invitado as idjugador, rj.idreservas_jugadores
    from awsmx_playtest.reservas_jugadores as rj
    inner join awsmx_playtest.invitados as i on i.idinvitados = rj.jugador_invitado
    where rj.reservas = '$reservas') order by idreservas_jugadores";
    $encontrarjugadores = json_encode($this->ExecuteReader($query));
    $encontrarjugadores = json_decode($encontrarjugadores);

    $destinatario = $correo;
    // Asunto
    $asunto = null;
    $query = "select * from canchas where idcanchas ='$idcancha';
    ";
    $cancha_nombre = null;
     $canchas = json_encode($this->ExecuteReader($query));
    $canchas = json_decode($canchas);
    foreach ($canchas as $key => $value) {
      $cancha_nombre = $value->nombre;
    }
    // Cuerpo o mensaje
   if ($accion ==1) {
    $asunto = 'Play Padel - Confirmación de reserva / TIcket';
    $vista="
    <!doctype html>
    <html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
        <!-- H E A D -->
        <head>
        <meta charset='UTF-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <meta name='color-scheme' content='light dark'>
            <meta name='supported-color-schemes' content='light dark'>
        
            <style type='text/css'>
                @import url(https://fonts.googleapis.com/css2?family=Poppins:wght@400;600);
                @media (prefers-color-scheme: dark){
                    .yaydoo-logo-dark{
                        display: none !important;
                    }
                    
                    .yaydoo-logo-light{
                        display: initial !important;
                    }
                }
                
                .yaydoo-logo-dark{
                    display: initial;
                }
                    
                .yaydoo-logo-light{
                    display: none;
                }
                
                #outlook a,
                a{
                    padding: 0;
                    cursor: pointer;
                    text-decoration: none;
                    color: #1890FF;
                }
            
                a.footer{
                    color: #677685;
                    text-decoration: underline;
                }
                
                div{
                    color: #011B34;
                    width: 100%;
                }
                
                body{
                    margin: 0;
                    padding: 0;
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                    font-family: 'Poppins', Arial, sans-serif;
                    word-spacing: normal;
                    background-color: #EFF2F8;
                }
                
                table,
                td{
                    border-collapse: collapse;
                    border-spacing: 0;
                    padding: 0;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    border: 0;
                    width: 100%;
                    word-break: break-word;
                }
                
                p{
                    display: block;
                    margin: 0;
                }
                
                .p-lr-56{
                    padding-left: 56px;
                    padding-right: 56px;
                }
                
                .p-l-56{
                    padding-left: 56px;
                }
                
                .p-l-56{
                    padding-right: 56px;
                }
                
                .p-t-48{
                    padding-top: 48px;
                }
                
                .header-background{
                    background: linear-gradient(90deg, rgba(3,14,52,1) 0%, rgba(37,57,130,1) 100%);
                }
                
                .header-style{
                    padding: 40px 0;
                    text-align: center;
                }
                
                /* SUMMARY TABLE START */
                .st-title{
                    font-size: 20px;
                    font-weight: 600;
                    display: flex;
                    line-height: 36px;
                    padding: 12px 0;
                }
                
                .st-title-lc{
                    max-width: 100%;
                    padding-right: 12px;
                }
                
                .st-title-rc{
                    max-width: 100%;
                    padding-left: 12px;
                    text-align: right;
                }
                
                .st-row{
                    font-size: 16px;
                    display: flex;
                    line-height: 28px;
                    padding: 12px 0;
                }
                
                .st-row-lc{
                    font-weight: 600;
                    max-width: 160px !important;
                    padding-right: 12px;
                }
                
                .st-row-rc{
                    font-weight: 400;
                    width: 100%;
                    padding-left: 12px;
                }
                /* SUMMARY TABLE END */
                
                .buyer-logo-dark{
                    display: initial;
                }
                
                .buyer-logo-light{
                    display: none;
                }
                
                hr{
                    background-color: #D1D4DD;
                    border-width: 0;
                    height: 1px;
                    margin: 0;
                }
                
                .btn{
                    padding: 14px 24px;
                    border-radius: 4px;
                    font-weight: 600;
                    font-size: 18px;
                    line-height: 30px;
                    color: #F8FAFC;
                    background-color: #1890FF;
                    letter-spacing: 0.08px;
                    min-width: 240px;
                    width: 100%;
                    text-decoration: none;
                    text-align: center;
                    border-width: 0;
                    display: inline-block;
                    transition-duration: 0.4s;
                }
                
                  .btn:hover{
                    background-color: #1375CF;
                  }
                
                .br-bottom-16{
                    border-radius: 0 0 16px 16px;
                }
                
                /* R E S P O N S I V E */
                @media only screen and (max-width:480px){
                    .p-lr-56{
                        padding-left: 17px;
                        padding-right: 17px;
                    }
                    
                    .p-l-56{
                        padding-left: 17px;
                    }
                    
                    .p-r-56{
                        padding-right: 17px;
                    }
                    
                    .p-t-48{
                        padding-top: 0;
                    }
                    
                    .header-style{
                        padding: 24px 17px;
                        text-align: left;
                    }
                    
                    .header-background{
                        background: #fff;
                    }
                    
                    .buyer-logo-dark{
                        display: none;
                    }
                    
                    .buyer-logo-light{
                        display: initial;
                    }
                    
                    @media (prefers-color-scheme: dark){
                        .buyer-logo-dark{
                            display: initial !important;
                        }
                        
                        .buyer-logo-light{
                            display: none !important;
                        }
                    }
                    
                    /* SUMMARY TABLE START */
                    .st-title{
                        font-size: 20px;
                        line-height: 32px;
                        padding: 8px 0;
                    }
                    
                    .st-title-lc{
                        padding-right: 8px;
                    }
                    
                    .st-title-rc{
                        padding-left: 8px;
                        text-align: right;
                    }
                    
                    .st-row{
                        font-size: 14px;
                        display: flex;
                        line-height: 22px;
                        padding: 12px 0;
                    }
                    
                    .st-row-lc{
                        max-width: 144px !important;
                        padding-right: 8px;
                    }
                    
                    .st-row-rc{
                        font-weight: 400;
                        padding-left: 8px;
                    }
                    /* SUMMARY TABLE END */
                    
                    .br-bottom-16{
                        border-radius: 0;
                    }
                }
            </style>
        </head>
        
        <body>
            <div>
                <!-- H E A D E R -->
                <table role='Header' class='header-background' style='width: 100%;'>
                    <thead>
                        <tr>
                            <td>
                                <div style='margin: auto; max-width: 648px;'>
                                    <table>
                                        <tr>
                                            <td>
                                                <!-- Buyer Logo-->
                                                <div>
                                                    <table role='Buyer Logo'>
                                                        <tr>
                                                            <th class='header-style'>
                                                                <a href='https://yaydoo.com/buyer' target='_blank'>
                                                                    <img class='buyer-logo-dark' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                                    <img class='buyer-logo-light' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                                </a>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <!-- Text -->
                                                <div style='border-radius: 16px 16px 0 0; background: #fff;'>
                                                    <table role='Body Title'>
                                                        <tr>
                                                            <th class='p-lr-56 p-t-48' style='padding-bottom: 24px; text-align: left;'>
                                                                <!-- Title -->
                                                                <p style='font-size: 24px; font-weight: 600;'>¡La cancha es tuya!</p>
                                                                <!-- Text-->
                                                                <p style='padding-top: 24px; font-size: 18px; font-weight: 600;'>Hola $nombre<br>
                                                                </p>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </thead>
                </table>
                
                <!-- B O D Y -->
                <table role='Body'>
                    <tbody>
                        <tr>
                            <td>
                                <div style='margin: auto; max-width: 648px;'>
                                    <table>
                                        <tr>
                                            <td>
                                                <!-- Buyer Logo-->
                                                <div style='background: #fff;'>
                                                    <table>
                                                        <tr>
                                                            <td class='p-lr-56'>
                                                                <div class='st-title'>
                                                                    <!-- class='st-title-lc' -->
                                                                    <div>Datos de inscripción al partido</div>
                                                                    <!-- <div class='st-title-rc'></div> -->
                                                                </div>
                                                                <hr style='margin-bottom: 4px;'>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Jugadores</div>
                                                         
                                                        
                                                                    <div class='st-row-rc' style='color: #1890FF'>
                                                                    ";
                                                                    $cont = 1;
                                                                    foreach ($encontrarjugadores as $key => $value) {
                                                                     $vista.= "<p>$cont  -  $value->jugador</p>";
                                                                     $cont ++;
                                                                     } 
                                                                  
                                                                    while ($cont<= 4) {
                                                                        $vista.=$cont ." - ". "<br>";
                                                                        $cont ++;
                                                                    }
                                                                
                                                                $vista.=
                                                              
                                                                "
                                                                </div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Fecha</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('d-m-Y', strtotime(str_replace('/', '-', $fecha)));  $vista.="</div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Hora inicio</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('g:i A', strtotime($hora_ini));  $vista.="</div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Hora termino</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('g:i A', strtotime($hora_fin));  $vista.="</div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Cancha</div>
                                                                    <div class='st-row-rc'>$idcancha - $cancha_nombre</div>
                                                                </div>
                                                                
                                                                <hr style='margin-top: 4px;'>
                                                                <!-- Button -->
                                                                <div style='margin-top: 32px; display: grid;'>
                                                                    <a href='{{url}}' target='_blank'>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- F O O T E R -->
                <table role='Footer' style='width: 100%;'>
                    <tr>
                        <td>
                            <div style='margin: auto; max-width: 648px;'>
                                <table>
                                    <tr>
                                        <td>
                                            <div class='br-bottom-16' style='background: #fff;'>
                                                <table role='Footer'>
                                                    <!-- Yaydoo Logo -->
                                                    <tr>
                                                        <td class='p-lr-56' style='padding-top: 32px; padding-bottom: 12px; text-align: center;'>
                                                            <div style='text-align: left; font-size: 14px; font-weight: 400; padding-bottom: 48px;'>
                                                                <span style='font-size: 16px; font-weight: 600;'>Agradecemos tu preferencia</span><br><br>
                                                                Atte<br>
                                                                <a href='#'>Play Padel México</a>
                                                            </div>
                                                            <hr>
                                                            <a href='https://yaydoo.com' target='_blank'>
                                                                <img class='yaydoo-logo-dark' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                                <img class='yaydoo-logo-light' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                            </a>
                                                            <p style='padding: 4px 0 0 0; font-size: 14px; font-weight: 400;'>
                                                            
                                                            <h4 class='col-md-12'>Información  adicional</h4>
                                                                        <p class='col-md-8'>
                                                                El club se reserva el derecho a modificar la cancha asignada por otra de similares características, por lo que recomendamos confirmar la cancha en la recepción del club.
                                                                Si deseas ver o anular una reserva, puedes hacerlo, directamente desde tu cuenta a través de la app o página web.
    
                                                                
                                                                        </p>
                                                                        <br>
                                                                        <p class='col-md-10'>
    
                                                                        Desde la web
                                                                        </p>
                                                                        <br>
                                                                        <p class='col-md-10'>
    
                                                                Menú> Mi cuenta > Mis reservas
                                                                </p>
                                                                        <br>
                                                                        <p class='col-md-10'>
                                                                        Desde la App
                                                                </p>
                                                                        <br>
                                                                        <p class='col-md-10'>
                                                                Menú> Mis reservas 
                                                                </p>
                                                                        <br>
                                                                        <p class='col-md-8'>
                                                                En caso de no poder gestionarla a través de nuestro sistema o estar fuera de plazo de cancelación, puedes ponerte en contacto con el club en el que hayas
                                                                realizado la reserva directamente.
                                                                </p>
                                                                        <br>
                                                                        <p class='col-md-8'>
                                                                Entiendo que mis datos personales serán comunicados a Play Padel México para la gestión de la reserva, siendo base jurídica del tratamiento la ejecución del contrato de 
                                                                Prestación de servicios. Podre ejercitar mis derechos, entre otros, de acceso, rectificación y supresión contactando con la instalación deportiva
                                                                </p>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='p-lr-56' style='padding-top: 0; padding-bottom: 40px; text-align: center;'>
                                                            <div>
                                                                <!-- Facebook -->
                                                                <a href='https://www.facebook.com/YAYDOO/' target='_blank'>
                                                                    <img alt='Yaydoo Facebook' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Facebook.png'/>
                                                                </a>
                                                                <!-- Twitter -->
                                                                <a href='https://twitter.com/yaydooapp' target='_blank'>
                                                                    <img alt='Yaydoo Twitter' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Twitter.png'/>
                                                                </a>
                                                                <!-- LinkedIn -->
                                                                <a href='https://www.linkedin.com/company/yaydoo/mycompany/' target='_blank'>
                                                                    <img alt='Yaydoo LinkedIn' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-LinkedIn.png'/>
                                                                </a>
                                                                <!-- Instagram -->
                                                                <a href='https://www.instagram.com/yaydoo/' target='_blank'>
                                                                    <img alt='Yaydoo Instagram' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/e9d7e407-a2ac-4536-8347-e03f7be2d586/30x30.png'/>
                                                                </a>
                                                                <!-- YouTube -->
                                                                <a href='https://www.youtube.com/channel/UCdFXS0VMDo6DJtWN_IaneUA' target='_blank'>
                                                                    <img alt='Yaydoo YouTube' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/99b432f1-d992-428f-82a8-c357f6d88a3b/30x30.png'/>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div style='margin: auto; max-width: 648px;'>
                                <table role='Body Title'>
                                    <tr>
                                        <th class='p-lr-56' style='padding-top: 32px; padding-bottom: 64px;'>
                                            <!-- footer -->
                                       
                                        </th>
                                    </tr>
                                </table>
                            </div>  
                        </td>
                    </tr>
                </table>
            </div>
        </body>
    </html>
    ";
   }
if ($accion ==2) {
  $asunto = 'Play Padel - La Información de tu partido ha sido actualizada';

  $fechaActual = date("d-m-Y");
  $hora_actual = new DateTime();
  $hora_actual->modify('-1 hour');
$hora_actual->setTimezone(new DateTimeZone('America/Monterrey'));
$horaActual = $hora_actual->format('H:i:s');
  $vista="
  <!doctype html>
  <html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
      <!-- H E A D -->
      <head>
      <meta charset='UTF-8'>

          <meta http-equiv='X-UA-Compatible' content='IE=edge'>
          <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1'>
          <meta name='color-scheme' content='light dark'>
          <meta name='supported-color-schemes' content='light dark'>
      
          <style type='text/css'>
              @import url(https://fonts.googleapis.com/css2?family=Poppins:wght@400;600);
              @media (prefers-color-scheme: dark){
                  .yaydoo-logo-dark{
                      display: none !important;
                  }
                  
                  .yaydoo-logo-light{
                      display: initial !important;
                  }
              }
              
              .yaydoo-logo-dark{
                  display: initial;
              }
                  
              .yaydoo-logo-light{
                  display: none;
              }
              
              #outlook a,
              a{
                  padding: 0;
                  cursor: pointer;
                  text-decoration: none;
                  color: #1890FF;
              }
          
              a.footer{
                  color: #677685;
                  text-decoration: underline;
              }
              
              div{
                  color: #011B34;
                  width: 100%;
              }
              
              body{
                  margin: 0;
                  padding: 0;
                  -webkit-text-size-adjust: 100%;
                  -ms-text-size-adjust: 100%;
                  font-family: 'Poppins', Arial, sans-serif;
                  word-spacing: normal;
                  background-color: #EFF2F8;
              }
              
              table,
              td{
                  border-collapse: collapse;
                  border-spacing: 0;
                  padding: 0;
                  mso-table-lspace: 0pt;
                  mso-table-rspace: 0pt;
                  border: 0;
                  width: 100%;
                  word-break: break-word;
              }
              
              p{
                  display: block;
                  margin: 0;
              }
              
              .p-lr-56{
                  padding-left: 56px;
                  padding-right: 56px;
              }
              
              .p-l-56{
                  padding-left: 56px;
              }
              
              .p-l-56{
                  padding-right: 56px;
              }
              
              .p-t-48{
                  padding-top: 48px;
              }
              
              .header-background{
                  background: linear-gradient(90deg, rgba(3,14,52,1) 0%, rgba(37,57,130,1) 100%);
              }
              
              .header-style{
                  padding: 40px 0;
                  text-align: center;
              }
              
              /* SUMMARY TABLE START */
              .st-title{
                  font-size: 20px;
                  font-weight: 600;
                  display: flex;
                  line-height: 36px;
                  padding: 12px 0;
              }
              
              .st-title-lc{
                  max-width: 100%;
                  padding-right: 12px;
              }
              
              .st-title-rc{
                  max-width: 100%;
                  padding-left: 12px;
                  text-align: right;
              }
              
              .st-row{
                  font-size: 16px;
                  display: flex;
                  line-height: 28px;
                  padding: 12px 0;
              }
              
              .st-row-lc{
                  font-weight: 600;
                  max-width: 160px !important;
                  padding-right: 12px;
              }
              
              .st-row-rc{
                  font-weight: 400;
                  width: 100%;
                  padding-left: 12px;
              }
              /* SUMMARY TABLE END */
              
              .buyer-logo-dark{
                  display: initial;
              }
              
              .buyer-logo-light{
                  display: none;
              }
              
              hr{
                  background-color: #D1D4DD;
                  border-width: 0;
                  height: 1px;
                  margin: 0;
              }
              
              .btn{
                  padding: 14px 24px;
                  border-radius: 4px;
                  font-weight: 600;
                  font-size: 18px;
                  line-height: 30px;
                  color: #F8FAFC;
                  background-color: #1890FF;
                  letter-spacing: 0.08px;
                  min-width: 240px;
                  width: 100%;
                  text-decoration: none;
                  text-align: center;
                  border-width: 0;
                  display: inline-block;
                  transition-duration: 0.4s;
              }
              
                .btn:hover{
                  background-color: #1375CF;
                }
              
              .br-bottom-16{
                  border-radius: 0 0 16px 16px;
              }
              
              /* R E S P O N S I V E */
              @media only screen and (max-width:480px){
                  .p-lr-56{
                      padding-left: 17px;
                      padding-right: 17px;
                  }
                  
                  .p-l-56{
                      padding-left: 17px;
                  }
                  
                  .p-r-56{
                      padding-right: 17px;
                  }
                  
                  .p-t-48{
                      padding-top: 0;
                  }
                  
                  .header-style{
                      padding: 24px 17px;
                      text-align: left;
                  }
                  
                  .header-background{
                      background: #fff;
                  }
                  
                  .buyer-logo-dark{
                      display: none;
                  }
                  
                  .buyer-logo-light{
                      display: initial;
                  }
                  
                  @media (prefers-color-scheme: dark){
                      .buyer-logo-dark{
                          display: initial !important;
                      }
                      
                      .buyer-logo-light{
                          display: none !important;
                      }
                  }
                  
                  /* SUMMARY TABLE START */
                  .st-title{
                      font-size: 20px;
                      line-height: 32px;
                      padding: 8px 0;
                  }
                  
                  .st-title-lc{
                      padding-right: 8px;
                  }
                  
                  .st-title-rc{
                      padding-left: 8px;
                      text-align: right;
                  }
                  
                  .st-row{
                      font-size: 14px;
                      display: flex;
                      line-height: 22px;
                      padding: 12px 0;
                  }
                  
                  .st-row-lc{
                      max-width: 144px !important;
                      padding-right: 8px;
                  }
                  
                  .st-row-rc{
                      font-weight: 400;
                      padding-left: 8px;
                  }
                  /* SUMMARY TABLE END */
                  
                  .br-bottom-16{
                      border-radius: 0;
                  }
              }
          </style>
      </head>
      
      <body>
          <div>
              <!-- H E A D E R -->
              <table role='Header' class='header-background' style='width: 100%;'>
                  <thead>
                      <tr>
                          <td>
                              <div style='margin: auto; max-width: 648px;'>
                                  <table>
                                      <tr>
                                          <td>
                                              <!-- Buyer Logo-->
                                              <div>
                                                  <table role='Buyer Logo'>
                                                      <tr>
                                                          <th class='header-style'>
                                                              <a href='https://yaydoo.com/buyer' target='_blank'>
                                                                  <img class='buyer-logo-dark' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                                  <img class='buyer-logo-light' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                              </a>
                                                          </th>
                                                      </tr>
                                                  </table>
                                              </div>
                                              <!-- Text -->
                                              <div style='border-radius: 16px 16px 0 0; background: #fff;'>
                                                  <table role='Body Title'>
                                                      <tr>
                                                          <th class='p-lr-56 p-t-48' style='padding-bottom: 24px; text-align: left;'>
                                                              <!-- Title -->
                                                              <p style='font-size: 24px; font-weight: 600;'>¡La información ha sido actualizada!</p>
                                                              <!-- Text-->
                                                              <p style='padding-top: 24px; font-size: 18px; font-weight: 600;'>Hola $nombre<br>
                                                              </p>
                                                          </th>
                                                      </tr>
                                                  </table>
                                              </div>
                                          </td>
                                      </tr>
                                  </table>
                              </div>
                          </td>
                      </tr>
                  </thead>
              </table>
              
              <!-- B O D Y -->
              <table role='Body'>
                  <tbody>
                      <tr>
                          <td>
                              <div style='margin: auto; max-width: 648px;'>
                                  <table>
                                      <tr>
                                          <td>
                                              <!-- Buyer Logo-->
                                              <div style='background: #fff;'>
                                                  <table>
                                                      <tr>
                                                          <td class='p-lr-56'>
                                                              <div class='st-title'>
                                                                  <!-- class='st-title-lc' -->
                                                                  <div>Datos del partido</div>
                                                                  <!-- <div class='st-title-rc'></div> -->
                                                              </div>
                                                              <hr style='margin-bottom: 4px;'>
                                                              <div class='st-row' >
                                                                  <div class='st-row-lc'>Jugadores</div>
                                                       
                                                      
                                                                  <div class='st-row-rc' style='color: #1890FF'>
                                                                  ";
                                                                  $cont = 1;
                                                                  foreach ($encontrarjugadores as $key => $value) {
                                                                   $vista.= "<p>$cont  -  $value->jugador</p>";
                                                                   $cont ++;
                                                                   } 
                                                                
                                                                  while ($cont<= 4) {
                                                                      $vista.=$cont ." - ". "<br>";
                                                                      $cont ++;
                                                                  }
                                                                  
                                                                 
                                                              
                                                              $vista.=
                                                            
                                                              "
                                                              </div>
                                                              </div>
                                                              <div class='st-row' >
                                                                    <div class='st-row-lc'>Fecha</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('d-m-Y', strtotime(str_replace('/', '-', $fecha)));  $vista.="</div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Hora inicio</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('g:i A', strtotime($hora_ini));  $vista.="</div>
                                                                </div>
                                                                <div class='st-row' >
                                                                    <div class='st-row-lc'>Hora termino</div>
                                                                    <div class='st-row-rc'> " ; $vista.= date('g:i A', strtotime($hora_fin));  $vista.="</div>
                                                                </div>
                                                              <div class='st-row' >
                                                                  <div class='st-row-lc'>Cancha</div>
                                                                  <div class='st-row-rc'>$idcancha - $cancha_nombre</div>
                                                              </div>
                                                              
                                                              <hr style='margin-top: 4px;'>
                                                              <!-- Button -->
                                                              <div style='margin-top: 32px; display: grid;'>
                                                                  <a href='{{url}}' target='_blank'>
                                                                  </a>
                                                              </div>
                                                          </td>
                                                      </tr>
                                                  </table>
                                              </div>
                                          </td>
                                      </tr>
                                  </table>
                              </div>
                          </td>
                      </tr>
                  </tbody>
              </table>
              
              <!-- F O O T E R -->
              <table role='Footer' style='width: 100%;'>
                  <tr>
                      <td>
                          <div style='margin: auto; max-width: 648px;'>
                              <table>
                                  <tr>
                                      <td>
                                          <div class='br-bottom-16' style='background: #fff;'>
                                              <table role='Footer'>
                                                  <!-- Yaydoo Logo -->
                                                  <tr>
                                                      <td class='p-lr-56' style='padding-top: 32px; padding-bottom: 12px; text-align: center;'>
                                                          <div style='text-align: left; font-size: 14px; font-weight: 400; padding-bottom: 48px;'>
                                                              <span style='font-size: 16px; font-weight: 600;'>Agradecemos tu preferencia</span><br><br>
                                                              Atte<br>
                                                              <a href='#'>Play Padel México</a>
                                                          </div>
                                                          <hr>
                                                          <a href='https://yaydoo.com' target='_blank'>
                                                              <img class='yaydoo-logo-dark' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                              <img class='yaydoo-logo-light' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                          </a>
                                                          <p style='padding: 4px 0 0 0; font-size: 14px; font-weight: 400;'>
                                                          <p class='col-md-12'>Fecha y hora Actualizada : ";$vista.= date('d-m-Y', strtotime(str_replace('/', '-', $fechaActual))) . " " . date('g:i A', strtotime($horaActual));$vista."  </p>

                                                         
                                                          </p>
                                                      </td>
                                                  </tr>
                                                  <tr>
                                                      <td class='p-lr-56' style='padding-top: 0; padding-bottom: 40px; text-align: center;'>
                                                          <div>
                                                              <!-- Facebook -->
                                                              <a href='https://www.facebook.com/YAYDOO/' target='_blank'>
                                                                  <img alt='Yaydoo Facebook' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Facebook.png'/>
                                                              </a>
                                                              <!-- Twitter -->
                                                              <a href='https://twitter.com/yaydooapp' target='_blank'>
                                                                  <img alt='Yaydoo Twitter' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Twitter.png'/>
                                                              </a>
                                                              <!-- LinkedIn -->
                                                              <a href='https://www.linkedin.com/company/yaydoo/mycompany/' target='_blank'>
                                                                  <img alt='Yaydoo LinkedIn' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-LinkedIn.png'/>
                                                              </a>
                                                              <!-- Instagram -->
                                                              <a href='https://www.instagram.com/yaydoo/' target='_blank'>
                                                                  <img alt='Yaydoo Instagram' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/e9d7e407-a2ac-4536-8347-e03f7be2d586/30x30.png'/>
                                                              </a>
                                                              <!-- YouTube -->
                                                              <a href='https://www.youtube.com/channel/UCdFXS0VMDo6DJtWN_IaneUA' target='_blank'>
                                                                  <img alt='Yaydoo YouTube' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/99b432f1-d992-428f-82a8-c357f6d88a3b/30x30.png'/>
                                                              </a>
                                                          </div>
                                                      </td>
                                                  </tr>
                                              </table>
                                          </div>
                                      </td>
                                  </tr>
                              </table>
                          </div>
                          <div style='margin: auto; max-width: 648px;'>
                              <table role='Body Title'>
                                  <tr>
                                      <th class='p-lr-56' style='padding-top: 32px; padding-bottom: 64px;'>
                                          <!-- footer -->
                                     
                                      </th>
                                  </tr>
                              </table>
                          </div>  
                      </td>
                  </tr>
              </table>
          </div>
      </body>
  </html>

  
      ";
}
if ($accion ==3) {
  $asunto = 'Tu partido de Padel ha sido cancelado';

  $fechaActual = date("d-m-Y");
  $hora_actual = new DateTime();
  $hora_actual->modify('-1 hour');
$hora_actual->setTimezone(new DateTimeZone('America/Monterrey'));
$horaActual = $hora_actual->format('H:i:s');
  $vista ="
  <!doctype html>
     <html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
         <!-- H E A D -->
         <head>
         <meta charset='UTF-8'>

             <meta http-equiv='X-UA-Compatible' content='IE=edge'>
             <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
             <meta name='viewport' content='width=device-width, initial-scale=1'>
             <meta name='color-scheme' content='light dark'>
             <meta name='supported-color-schemes' content='light dark'>
         
             <style type='text/css'>
                 @import url(https://fonts.googleapis.com/css2?family=Poppins:wght@400;600);
                 @media (prefers-color-scheme: dark){
                     .yaydoo-logo-dark{
                         display: none !important;
                     }
                     
                     .yaydoo-logo-light{
                         display: initial !important;
                     }
                 }
                 
                 .yaydoo-logo-dark{
                     display: initial;
                 }
                     
                 .yaydoo-logo-light{
                     display: none;
                 }
                 
                 #outlook a,
                 a{
                     padding: 0;
                     cursor: pointer;
                     text-decoration: none;
                     color: #1890FF;
                 }
             
                 a.footer{
                     color: #677685;
                     text-decoration: underline;
                 }
                 
                 div{
                     color: #011B34;
                     width: 100%;
                 }
                 
                 body{
                     margin: 0;
                     padding: 0;
                     -webkit-text-size-adjust: 100%;
                     -ms-text-size-adjust: 100%;
                     font-family: 'Poppins', Arial, sans-serif;
                     word-spacing: normal;
                     background-color: #EFF2F8;
                 }
                 
                 table,
                 td{
                     border-collapse: collapse;
                     border-spacing: 0;
                     padding: 0;
                     mso-table-lspace: 0pt;
                     mso-table-rspace: 0pt;
                     border: 0;
                     width: 100%;
                     word-break: break-word;
                 }
                 
                 p{
                     display: block;
                     margin: 0;
                 }
                 
                 .p-lr-56{
                     padding-left: 56px;
                     padding-right: 56px;
                 }
                 
                 .p-l-56{
                     padding-left: 56px;
                 }
                 
                 .p-l-56{
                     padding-right: 56px;
                 }
                 
                 .p-t-48{
                     padding-top: 48px;
                 }
                 
                 .header-background{
                     background: linear-gradient(90deg, rgba(3,14,52,1) 0%, rgba(37,57,130,1) 100%);
                 }
                 
                 .header-style{
                     padding: 40px 0;
                     text-align: center;
                 }
                 
                 /* SUMMARY TABLE START */
                 .st-title{
                     font-size: 20px;
                     font-weight: 600;
                     display: flex;
                     line-height: 36px;
                     padding: 12px 0;
                 }
                 
                 .st-title-lc{
                     max-width: 100%;
                     padding-right: 12px;
                 }
                 
                 .st-title-rc{
                     max-width: 100%;
                     padding-left: 12px;
                     text-align: right;
                 }
                 
                 .st-row{
                     font-size: 16px;
                     display: flex;
                     line-height: 28px;
                     padding: 12px 0;
                 }
                 
                 .st-row-lc{
                     font-weight: 600;
                     max-width: 160px !important;
                     padding-right: 12px;
                 }
                 
                 .st-row-rc{
                     font-weight: 400;
                     width: 100%;
                     padding-left: 12px;
                 }
                 /* SUMMARY TABLE END */
                 
                 .buyer-logo-dark{
                     display: initial;
                 }
                 
                 .buyer-logo-light{
                     display: none;
                 }
                 
                 hr{
                     background-color: #D1D4DD;
                     border-width: 0;
                     height: 1px;
                     margin: 0;
                 }
                 
                 .btn{
                     padding: 14px 24px;
                     border-radius: 4px;
                     font-weight: 600;
                     font-size: 18px;
                     line-height: 30px;
                     color: #F8FAFC;
                     background-color: #1890FF;
                     letter-spacing: 0.08px;
                     min-width: 240px;
                     width: 100%;
                     text-decoration: none;
                     text-align: center;
                     border-width: 0;
                     display: inline-block;
                     transition-duration: 0.4s;
                 }
                 
                   .btn:hover{
                     background-color: #1375CF;
                   }
                 
                 .br-bottom-16{
                     border-radius: 0 0 16px 16px;
                 }
                 
                 /* R E S P O N S I V E */
                 @media only screen and (max-width:480px){
                     .p-lr-56{
                         padding-left: 17px;
                         padding-right: 17px;
                     }
                     
                     .p-l-56{
                         padding-left: 17px;
                     }
                     
                     .p-r-56{
                         padding-right: 17px;
                     }
                     
                     .p-t-48{
                         padding-top: 0;
                     }
                     
                     .header-style{
                         padding: 24px 17px;
                         text-align: left;
                     }
                     
                     .header-background{
                         background: #fff;
                     }
                     
                     .buyer-logo-dark{
                         display: none;
                     }
                     
                     .buyer-logo-light{
                         display: initial;
                     }
                     
                     @media (prefers-color-scheme: dark){
                         .buyer-logo-dark{
                             display: initial !important;
                         }
                         
                         .buyer-logo-light{
                             display: none !important;
                         }
                     }
                     
                     /* SUMMARY TABLE START */
                     .st-title{
                         font-size: 20px;
                         line-height: 32px;
                         padding: 8px 0;
                     }
                     
                     .st-title-lc{
                         padding-right: 8px;
                     }
                     
                     .st-title-rc{
                         padding-left: 8px;
                         text-align: right;
                     }
                     
                     .st-row{
                         font-size: 14px;
                         display: flex;
                         line-height: 22px;
                         padding: 12px 0;
                     }
                     
                     .st-row-lc{
                         max-width: 144px !important;
                         padding-right: 8px;
                     }
                     
                     .st-row-rc{
                         font-weight: 400;
                         padding-left: 8px;
                     }
                     /* SUMMARY TABLE END */
                     
                     .br-bottom-16{
                         border-radius: 0;
                     }
                 }
             </style>
         </head>
         
         <body>
             <div>
                 <!-- H E A D E R -->
                 <table role='Header' class='header-background' style='width: 100%;'>
                     <thead>
                         <tr>
                             <td>
                                 <div style='margin: auto; max-width: 648px;'>
                                     <table>
                                         <tr>
                                             <td>
                                                 <!-- Buyer Logo-->
                                                 <div>
                                                     <table role='Buyer Logo'>
                                                         <tr>
                                                             <th class='header-style'>
                                                                 <a href='https://yaydoo.com/buyer' target='_blank'>
                                                                     <img class='buyer-logo-dark' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                                     <img class='buyer-logo-light' style='height: 64px; width: 100px;' alt='Buyer Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg'/>
                                                                 </a>
                                                             </th>
                                                         </tr>
                                                     </table>
                                                 </div>
                                                 <!-- Text -->
                                                 <div style='border-radius: 16px 16px 0 0; background: #fff;'>
                                                     <table role='Body Title'>
                                                         <tr>
                                                             <th class='p-lr-56 p-t-48' style='padding-bottom: 24px; text-align: left;'>
                                                                 <!-- Title -->
                                                                 <!-- Text-->
                                                                 <p style='padding-top: 24px; font-size: 18px; font-weight: 600;'>Hola $nombre<br>
                                                                 </p>
                                                             </th>
                                                         </tr>
                                                     </table>
                                                 </div>
                                             </td>
                                         </tr>
                                     </table>
                                 </div>
                             </td>
                         </tr>
                     </thead>
                 </table>
                 
                 <!-- B O D Y -->
                 <table role='Body'>
                     <tbody>
                         <tr>
                             <td>
                                 <div style='margin: auto; max-width: 648px;'>
                                     <table>
                                         <tr>
                                             <td>
                                                 <!-- Buyer Logo-->
                                                 <div style='background: #fff;'>
                                                     <table>
                                                         <tr>
                                                             <td class='p-lr-56'>
                                                                 <div class='st-title'>
                                                                     <!-- class='st-title-lc' -->
                                                                     <div>El  partido ha sido cancelado por el organizador</div>
                                                                     <!-- <div class='st-title-rc'></div> -->
                                                                 </div>
                                                                 <hr style='margin-bottom: 4px;'>
                                                                 <div class='st-row' >
                                                                
                                                                 </div>
                                                                 <div class='st-row' >
                                                                 <div class='st-row-lc'>Fecha de cancelación</div>
                                                                 <div class='st-row-rc'> " ; $vista.= date('d-m-Y', strtotime(str_replace('/', '-', $fechaActual)));  $vista.="</div>
                                                             </div>
                                                             <div class='st-row' >
                                                                 <div class='st-row-lc'>Hora de cancelación</div>
                                                                 <div class='st-row-rc'> " ; $vista.= date('g:i A', strtotime($horaActual));  $vista.="</div>
                                                             </div>
                                                             
                                                               
                                                                 <div class='st-row' >
                                                                     <div class='st-row-lc'>Cancha</div>
                                                                     <div class='st-row-rc'>$cancha</div>
                                                                 </div>
                                                                 
                                                                 <hr style='margin-top: 4px;'>
                                                                 <!-- Button -->
                                                                 <div style='margin-top: 32px; display: grid;'>
                                                                     <a href='{{url}}' target='_blank'>
                                                                     </a>
                                                                 </div>
                                                             </td>
                                                         </tr>
                                                     </table>
                                                 </div>
                                             </td>
                                         </tr>
                                     </table>
                                 </div>
                             </td>
                         </tr>
                     </tbody>
                 </table>
                 
                 <!-- F O O T E R -->
                 <table role='Footer' style='width: 100%;'>
                     <tr>
                         <td>
                             <div style='margin: auto; max-width: 648px;'>
                                 <table>
                                     <tr>
                                         <td>
                                             <div class='br-bottom-16' style='background: #fff;'>
                                                 <table role='Footer'>
                                                     <!-- Yaydoo Logo -->
                                                     <tr>
                                                         <td class='p-lr-56' style='padding-top: 32px; padding-bottom: 12px; text-align: center;'>
                                                             <div style='text-align: left; font-size: 14px; font-weight: 400; padding-bottom: 48px;'>
                                                                 <span style='font-size: 16px; font-weight: 600;'>Agradecemos tu preferencia</span><br><br>
                                                                 Atte<br>
                                                                 <a href='mailto: soporte@buyer.com'>Play Padel México</a>
                                                             </div>
                                                             <hr>
                                                             <a href='https://yaydoo.com' target='_blank'>
                                                                 <img class='yaydoo-logo-dark' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                                 <img class='yaydoo-logo-light' alt='Yaydoo Logo' src='https://awsmx.org/playpadel_test/images/logo.jpg' style='padding-top:24px; height: 32px; width: auto;'/>
                                                             </a>
                                                             <p style='padding: 4px 0 0 0; font-size: 14px; font-weight: 400;'>
                                                             

                                                             </p>
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td class='p-lr-56' style='padding-top: 0; padding-bottom: 40px; text-align: center;'>
                                                             <div>
                                                                 <!-- Facebook -->
                                                                 <a href='https://www.facebook.com/YAYDOO/' target='_blank'>
                                                                     <img alt='Yaydoo Facebook' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Facebook.png'/>
                                                                 </a>
                                                                 <!-- Twitter -->
                                                                 <a href='https://twitter.com/yaydooapp' target='_blank'>
                                                                     <img alt='Yaydoo Twitter' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-Twitter.png'/>
                                                                 </a>
                                                                 <!-- LinkedIn -->
                                                                 <a href='https://www.linkedin.com/company/yaydoo/mycompany/' target='_blank'>
                                                                     <img alt='Yaydoo LinkedIn' style='padding: 0 6px; height: 24px; width: auto;' src='https://develop.de9ghdunpsz3a.amplifyapp.com/img/icon-LinkedIn.png'/>
                                                                 </a>
                                                                 <!-- Instagram -->
                                                                 <a href='https://www.instagram.com/yaydoo/' target='_blank'>
                                                                     <img alt='Yaydoo Instagram' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/e9d7e407-a2ac-4536-8347-e03f7be2d586/30x30.png'/>
                                                                 </a>
                                                                 <!-- YouTube -->
                                                                 <a href='https://www.youtube.com/channel/UCdFXS0VMDo6DJtWN_IaneUA' target='_blank'>
                                                                     <img alt='Yaydoo YouTube' style='padding: 0 6px; height: 27px; width: auto;' src='http://cdn.mcauto-images-production.sendgrid.net/b5993028aa7c9eef/99b432f1-d992-428f-82a8-c357f6d88a3b/30x30.png'/>
                                                                 </a>
                                                             </div>
                                                         </td>
                                                     </tr>
                                                 </table>
                                             </div>
                                         </td>
                                     </tr>
                                 </table>
                             </div>
                             <div style='margin: auto; max-width: 648px;'>
                                 <table role='Body Title'>
                                     <tr>
                                         <th class='p-lr-56' style='padding-top: 32px; padding-bottom: 64px;'>
                                             <!-- footer -->
                                            
                                         </th>
                                     </tr>
                                 </table>
                             </div>  
                         </td>
                     </tr>
                 </table>
             </div>
         </body>
     </html>
  ";
}



    // Cabecera que especifica que es un HMTL
    $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= "Content-type: text/html; charset=utf-8\r\n";

    // Cabeceras adicionales
    $cabeceras .= 'From: PLAY PADEL MÉXICO <reservas@padelizer.com>' . "\r\n";
    $cabeceras .= 'Cc: reservas@padelizer.com' . "\r\n";
    $cabeceras .= 'Bcc: reservas@padelizer.com' . "\r\n";
    
    // enviamos el correo!
    $mail_enviado = mail($destinatario, $asunto, $vista, $cabeceras);
    if ($mail_enviado) {
      echo "Correo enviado correctamente.";
  } else {
      echo "Error al enviar el correo.";
  }
  }

  function InsertarMultipleCanchas($reservas,$canchas){
    $canchas = json_decode($canchas);
    foreach ($canchas as $key => $value) {
      $query = "insert into reservas_canchas(idreservas,idcancha)
      values(?,?)
      ";
        $this->getLastID($query, array($reservas,$value->cancha));

    }
    return;
  }


  function InsertarCancha($reservas,$cancha){
        $query = "insert into reservas_canchas(idreservas,idcancha)
        values(?,?)
        ";
         $this->getLastID($query, array($reservas,$cancha));

    }
  
  function encontrarJugadores($idreserva){
    $query = "select socios.idsocios as idpersona,socios.nombre,IF(socios.idsocios>0, 'Socio','') as tipo FROM reservas_jugadores LEFT JOIN socios on socios.idsocios =reservas_jugadores.jugador_socio where reservas_jugadores.reservas = '$idreserva' UNION ALL select invitados.idinvitados as idpersona,invitados.nombre,IF(invitados.idinvitados>0, 'Invitado','')as tipo FROM reservas_jugadores LEFT JOIN invitados on invitados.idinvitados =reservas_jugadores.jugador_invitado where reservas_jugadores.reservas = '$idreserva';";
    return json_encode($this->ExecuteReader($query));
  }
  function diasdelasemana(){
    $query = "select DATE(DATE_ADD(NOW(), INTERVAL -WEEKDAY(NOW()) DAY)) lunes, DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+1) martes,DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+2)miercoles,DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+3)jueves,DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+4)viernes,DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+5) sabado ,DATE(DATE_ADD(DATE(NOW()), INTERVAL -WEEKDAY(NOW()) DAY)+6) domingo;";
    return json_encode($this->ExecuteReader($query));
  }
  function obtenereserva($idreservas){
    $query="Select reservas.*,ADDTIME(reservas.hora_fin, -reservas.hora_inicio) AS tiempo_dif,ADDTIME(reservas.hora_fin, '00:30:00') AS tiempo_extra  from reservas where idreservas='$idreservas'";
    return json_encode($this->ExecuteReader($query));
  }
  function ReservasCalendario(){
    $query = "select reservas.fecha_inicio as fecha,reservas.hora_inicio as hora_inicio,reservas.hora_fin as hora_fin ,canchas.nombre as cancha,IF(reservas.idreservas>0, 'reserva','') as tipo, canchas.idcanchas as idcanchas from reservas INNER JOIN reservas_canchas on reservas_canchas.idreservas = reservas.idreservas INNER JOIN canchas on canchas.idcanchas = reservas_canchas.idcancha UNION ALL select clinicas_fechas.fecha,clinicas.horario_entrada as hora_inicio, clinicas.horario_salida as hora_fin,canchas.nombre,IF(clinicas.idclinicas>0, 'clinica','') as tipo,canchas.idcanchas as idcanchas from clinicas INNER JOIN clinicas_fechas on clinicas_fechas.idclinicas = clinicas.idclinicas INNER JOIN canchas on canchas.idcanchas = clinicas.idcanchas; ";
    return json_encode($this->ExecuteReader($query));
  }
  function Comprobaciondereserva($hora,$fecha,$cancha){
    $query = "select canchas.nombre,reservas.fecha_inicio,reservas.hora_inicio FROM reservas inner join reservas_canchas on reservas.idreservas = reservas_canchas.idreservas
    inner join canchas on canchas.idcanchas=reservas_canchas.idcancha where '$hora' BETWEEN reservas.hora_inicio and reservas.hora_fin and reservas.fecha_inicio='$fecha' and canchas.nombre = '$cancha';";
    return json_encode($this->ExecuteReader($query));
  }
  function ActualizarReservaMultiple($observacion,$descripcion,$hora_inicio,$hora_fin,$idreserva){
    $query = "update reservas set observacion ='$observacion',descripcion ='$descripcion', hora_inicio='$hora_inicio',hora_fin='$hora_fin' where idreservas = '$idreserva'";
    return json_encode($this->ExecuteReader($query));
  }
  function CancelarReserva($idreserva,$penalizacion){
    $query ="";
    if ($penalizacion == null) {
        $query .= "update reservas set status =0 where idreservas = '$idreserva'";

    }
    else{
        $query .= "update reservas set status =1,penalizacion =1 where idreservas = '$idreserva'";

    }
    echo "dassd";
   json_encode($this->ExecuteReader($query));
    $query ="
    select s.nombre,s.correo,s.telefono,c.idcanchas,c.nombre as cancha from reservas_jugadores as rj inner join socios as s on rj.jugador_socio= s.idsocios
    inner join reservas as r on r.idreservas = rj.reservas inner join reservas_canchas as rc on rc.idreservas = r.idreservas 
    inner join canchas as c on c.idcanchas = rc.idcancha
      where  r.idreservas ='$idreserva'
    UNION ALL
    select i.nombre,i.correo,i.telefono,c.idcanchas,c.nombre  as cancha from reservas_jugadores as rj inner join invitados as i on rj.jugador_invitado =i.idinvitados inner join reservas as r
    on r.idreservas = rj.reservas  inner join reservas_canchas as rc on rc.idreservas = r.idreservas 
    inner join canchas as c on c.idcanchas = rc.idcancha
      where  r.idreservas ='$idreserva'
          ";
          $clientes = json_encode($this->ExecuteReader($query));
          $clientes = json_decode($clientes);
          $accion = 3;
          foreach($clientes as $value){
             //  echo $value->nombre;
             //  echo $value->correo;
             //  echo $value->celular;
             
             $this->enviareserva($idreserva,$value->correo,$value->nombre,$value->telefono,null,null,null,null,$accion,$value->idcanchas." - ".$value->cancha);
 
          }      
  }
//   function jugadoresreservas()
//   {
//     $query = "(select s.idsocios as idpersona, s.nombre as jugador, s.idmembresias, 'Socio' as tipo,COUNT(s.nombre)
//      as total from socios as s INNER JOIN reservas_jugadores as rj on rj.jugador_socio = s.idsocios INNER JOIN reservas
//       as r on r.idreservas = rj.reservas WHERE r.hora_fin >DATE_FORMAT(NOW( ), "%H:%i:%S" ) and s.status =1 GROUP BY s.nombre) 
//       union all (select i.idinvitados as idpersona, i.nombre as jugador, 0 as idmembresias, 'Invitado' as tipo,COUNT(i.nombre) as total from 
//       invitados as i INNER JOIN reservas_jugadores as rj on rj.jugador_invitado = i.idinvitados INNER JOIN reservas as r on r.idreservas = 
//       rj.reservas WHERE r.hora_fin >DATE_FORMAT(NOW( ), "%H:%i:%S" ) and i.status = 1 and i.status_invitado = 1 GROUP BY i.nombre);";
//     return json_encode($this->ExecuteReader($query));
//   }
  function EliminarJugadores($idreserva){
    $query = "delete FROM `reservas_jugadores` where reservas_jugadores.reservas = '$idreserva'";
    return json_encode($this->ExecuteReader($query));
  }
  function EliminarCanchas($idreserva){
    $query = "delete FROM `reservas_canchas` where reservas_canchas.idreservas = '$idreserva'";
    return json_encode($this->ExecuteReader($query));
  }
  function Reservasdelasemana($fecha){
    $query = " select canchas.nombre,reservas.fecha_inicio,MAX(reservas.hora_fin) as fin,ADDTIME(MAX(reservas.hora_fin), '00:30:00') AS intervalo,
    ADDTIME(MAX(reservas.hora_fin), '01:00:00') AS intervalo2 FROM reservas inner join reservas_canchas on reservas.idreservas = reservas_canchas.idreservas
     inner join canchas on canchas.idcanchas=reservas_canchas.idcancha where reservas.fecha_inicio='$fecha'
     group by canchas.nombre ";
    return json_encode($this->ExecuteReader($query));
  }
  function Reservasdelasemanahoraini($fecha){
    $query = "
    SELECT canchas.nombre,reservas.fecha_inicio,reservas.hora_inicio FROM reservas inner join reservas_canchas on reservas.idreservas = reservas_canchas.idreservas
    inner join canchas on canchas.idcanchas=reservas_canchas.idcancha where reservas.fecha_inicio='$fecha'";
   
    return json_encode($this->ExecuteReader($query));
  }
  function Comprobartiempo($hora,$fecha,$cancha){
    $query = "
    select reservas.idreservas,reservas.hora_inicio,reservas.hora_fin,canchas.nombre from reservas inner join reservas_canchas on reservas_canchas.idreservas = reservas.idreservas inner join
    canchas on canchas.idcanchas= reservas_canchas.idcancha where '$hora' between reservas.hora_inicio and reservas.hora_fin and
   reservas.fecha_inicio='$fecha' and canchas.idcanchas = '$cancha'  and reservas.status =1 OR reservas.hora_inicio ='$hora' and reservas.fecha_inicio ='$fecha' 
   and  canchas.idcanchas = '$cancha' and reservas.status =1
   UNION ALL 
   select t.idtorneos as idreservas,t.horario_inicio as hora_inicio,t.horario_fin as hora_fin,c.nombre from torneos_fechas as tf inner join torneos as t on tf.idtorneos = t.idtorneos inner join torneos_canchas as tc on tc.idtorneos = t.idtorneos
   inner join canchas as c on c.idcanchas = tc.cancha
    where '$hora' between tf.hora_inicio and tf.hora_fin and tf.fecha ='$fecha' and c.idcanchas = '$cancha'
    UNION ALL 
    select cl.idclinicas as idreservas,cl.horario_entrada as hora_inicio,cl.horario_salida as hora_fin,c.nombre from clinicas as cl inner join clinicas_fechas as cf on cf.idclinicas = cl.idclinicas inner join canchas as c on c.idcanchas = cl.idcanchas
     where '$hora' between cl.horario_entrada and cl.horario_salida and cf.fecha ='$fecha' and c.idcanchas ='$cancha'
   ;
   

    ";
    return json_encode($this->ExecuteReader($query));
  }
  function ActualizarReserva($fecha_inicio,$observacion,$Editiempoextra,$hora_inicio,$hora_fin,$idreserva){
    if ($Editiempoextra ==null) {
      $query = "update reservas set fecha_inicio = '$fecha_inicio',observacion ='$observacion',hora_inicio='$hora_inicio',hora_fin='$hora_fin' where idreservas = '$idreserva'";
      return json_encode($this->ExecuteReader($query));
    }
    else{
      $query = "update reservas set fecha_inicio = '$fecha_inicio',observacion ='$observacion',hora_inicio='$hora_inicio',hora_fin='$Editiempoextra' where idreservas = '$idreserva'";
      return json_encode($this->ExecuteReader($query));
    }
   
  }
  function deletejugadoresreserva($idreserva){
    $query = "Delete from reservas_jugadores where reservas_jugadores.reservas ='$idreserva'";
    return json_encode($this->ExecuteReader($query));

  }
  function deletecanchasreserva($idreserva){
    $query = "Delete from reservas_canchas where reservas_canchas.idreservas ='$idreserva'";
    return json_encode($this->ExecuteReader($query));
  }
  function comparartiempos($hora,$hora2,$fecha,$cancha,$idreserva){
  
    $query = "select group_concat(c.idcanchas,' - ',c.nombre) as id,'torneo' as tipo from torneos_fechas as tf 
    inner join torneos as t on t.idtorneos = tf.idtorneos inner join torneos_canchas as tc on tc.idtorneos = t.idtorneos 
    inner join canchas as c on c.idcanchas=tc.cancha 
    where '$hora' between tf.hora_inicio and tf.hora_fin and tf.fecha='$fecha'and tc.cancha ='$cancha' 
    or  tf.hora_fin= '$hora' and tf.fecha ='$fecha' 
     and tc.cancha ='$cancha'
     UNION ALL 
     select group_concat(c.idcanchas,' - ',c.nombre) as id,'clinica' as tipo from clinicas as cl inner join clinicas_fechas as cf on cl.idclinicas = cf.idclinicas
     inner join canchas as c on c.idcanchas = cl.idcanchas
     where '$hora'
     between cl.horario_entrada and cl.horario_salida and cf.fecha ='$fecha' and c.idcanchas ='$cancha' 
     or  cl.horario_salida= '$hora' and cf.fecha ='$fecha' 
     and c.idcanchas ='$cancha'
     UNION ALL 
     select group_concat(c.idcanchas,' - ',c.nombre) as id,'reserva' as tipo from reservas as r inner join reservas_canchas as rc on rc.idreservas = r.idreservas
     inner join canchas as c on c.idcanchas = rc.idcancha and r.status = 1
     where '$hora'
     between r.hora_inicio and r.hora_fin and r.fecha_inicio ='$fecha' and rc.idcancha ='$cancha' and r.idreservas != '$idreserva'  
     or  r.hora_fin= '$hora' and r.fecha_inicio ='$fecha'  
     and rc.idcancha ='$cancha' and r.idreservas != '$idreserva'
     UNION ALL
     select group_concat(c.idcanchas,' - ',c.nombre) as id,'torneo' as tipo from torneos_fechas as tf 
    inner join torneos as t on t.idtorneos = tf.idtorneos inner join torneos_canchas as tc on tc.idtorneos = t.idtorneos 
    inner join canchas as c on c.idcanchas=tc.cancha 
    where '$hora2' between tf.hora_inicio and tf.hora_fin and tf.fecha='$fecha'and tc.cancha ='$cancha'
    or  tf.hora_inicio= '$hora2' and tf.fecha ='$fecha' 
     and tc.cancha ='$cancha'
     UNION ALL 
     select group_concat(c.idcanchas,' - ',c.nombre) as id,'clinica' as tipo from clinicas as cl inner join clinicas_fechas as cf on cl.idclinicas = cf.idclinicas
     inner join canchas as c on c.idcanchas = cl.idcanchas
     where '$hora2'
     between cl.horario_entrada and cl.horario_salida and cf.fecha ='$fecha' and c.idcanchas ='$cancha' 
     or  cl.horario_entrada= '$hora2' and cf.fecha ='$fecha' 
     and c.idcanchas ='$cancha'
     UNION ALL 
     select group_concat(c.idcanchas,' - ',c.nombre) as id,'reserva' as tipo from reservas as r inner join reservas_canchas as rc on rc.idreservas = r.idreservas
     inner join canchas as c on c.idcanchas = rc.idcancha and r.status = 1
     where '$hora2'
     between r.hora_inicio and r.hora_fin and r.fecha_inicio ='$fecha' and rc.idcancha ='$cancha' and r.idreservas != '$idreserva'
     or  r.hora_inicio= '$hora2' and r.fecha_inicio ='$fecha' 
     and rc.idcancha ='$cancha' and r.idreservas != '$idreserva';
    ;";
     return json_encode($this->ExecuteReader($query));
    
 }
  

 function precioreserva($hora,$hora2,$fecha,$idcancha,$idreserva,$dia,$numdia){

  $query = "
  (SELECT c.nombre,cd.hora_inicio,cd.hora_fin,cd.costo,cd.costo_120, if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=90,'SI','NO') as tiempo90m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=120,'SI','NO') as tiempo120m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)>120,TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)-120,'NO') 
  as tiempoextra,
  'hora_descuento' as horario
  FROM reservas as r 
  inner join reservas_canchas as rc on r.idreservas = rc.idreservas
  inner join canchas as c on c.idcanchas = rc.idcancha
  inner join canchas_descuento as cd on cd.cancha = c.idcanchas
  where r.idreservas ='$idreserva' and cd.dia='$dia' and '$hora' between cd.hora_inicio and cd.hora_fin
  and c.idcanchas ='$idcancha' and r.fecha_inicio = '$fecha'
  or 
  r.idreservas ='$idreserva' and cd.dia='$dia'  and cd.hora_inicio= '$hora'
  and c.idcanchas = '$idcancha' and r.fecha_inicio = '$fecha'
  or
  r.idreservas ='$idreserva' and cd.dia='$dia'  and cd.hora_fin= '$hora2'  and r.fecha_inicio = '$fecha'
  and c.idcanchas = '$idcancha')
  UNION ALL
  (
  SELECT c.nombre,cp.hora_inicio,cp.hora_fin,cp.costo,cp.costo_120, if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=90,'SI','NO') as tiempo90m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=120,'SI','NO') as tiempo120m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)>120,TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)-120,'NO') 
  as tiempoextra,
  'hora_premium' as horario
  FROM reservas as r 
  inner join reservas_canchas as rc on r.idreservas = rc.idreservas
  inner join canchas as c on c.idcanchas = rc.idcancha
  inner join canchas_premium as cp on cp.cancha = c.idcanchas
  where r.idreservas ='$idreserva' and cp.dia='$dia' and '$hora' between cp.hora_inicio and cp.hora_fin
  and c.idcanchas = '$idcancha' and r.fecha_inicio = '$fecha'
  or 
  r.idreservas ='$idreserva' and cp.dia='$dia' and cp.hora_inicio= '$hora'
  and c.idcanchas = '$idcancha'  and r.fecha_inicio = '$fecha'
  or
  r.idreservas ='$idreserva' and cp.dia='$dia' and cp.hora_fin= '$hora2'
  and c.idcanchas = '$idcancha'  and r.fecha_inicio = '$fecha'
  )
  UNION ALL
  (
  select c.nombre,hc.hora_inicio,hc.hora_fin,c.costo,c.costo_120,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=90,'SI','NO') as tiempo90m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)=120,'SI','NO') as tiempo120m,
  if(TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)>120,TIMESTAMPDIFF(MINUTE,r.hora_inicio, r.hora_fin)-120,'NO') 
  as tiempoextra,'horario_general' as horario
  from horarios_cancha as hc  inner join canchas as c on c.idcanchas =hc.idcanchas inner join reservas_canchas as rc on rc.idcancha = c.idcanchas
  INNER JOIN reservas as r on r.idreservas =rc.idreservas
  where '$hora' BETWEEN r.hora_inicio and r.hora_fin and hc.dia ='$numdia' and c.idcanchas='$idcancha' and r.fecha_inicio ='$fecha' and r.idreservas ='$idreserva'
  or 
  r.idreservas ='$idreserva' and r.hora_inicio ='$hora' and hc.dia ='$numdia' and c.idcanchas='$idcancha' and r.fecha_inicio ='$fecha'
  or 
  r.idreservas ='$idreserva' and r.hora_fin ='$hora2' and hc.dia ='$numdia' and c.idcanchas='$numdia' and r.fecha_inicio ='$fecha'
  )
  
";
return json_encode($this->ExecuteReader($query));
  
}
}
?>
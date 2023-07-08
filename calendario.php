<!DOCTYPE html>
<html lang="en">
<?php include("estilos.php"); ?>
<title>Play Padel - Calendario</title>
<style type="text/css">
    
  html,body{
    max-width: 100% !important;
    max-height: 100% !important;
  }
  .dataTables_filter{
    display: none;
  }
  .horas_dibujadas{
    width: 63px;
    max-width: 63px;
    height: 50px;
    font-size: .7em;
    text-align: center;
    align-items: center;
    padding: auto;
    
  }

  textarea {
    resize: none;
  }
  .acomodarsquard{
    font-size: .7em;
    text-align: center;
    align-items: center;
    padding: auto;
    width: 63px;
    max-width: 63px;

  }
  .canchas_title{
    max-width: 63px;
    font-size: 11px;
    width: 63px;
    height: 30px;
    margin-left: .5em;
    margin-right: .5em;
    justify-content: center;
    text-align: center;
    margin-bottom: 1em;
   
  }
  .canchas{
    overflow-x:scroll;
    display: flex;
  }

.title {
    position: relative;
    bottom: 5px;
    text-align: start;
    display: inline-block;
    font-size: 20px;
    font-weight: 700;
    color: #005b81;
}
.clinica{
  background-color:#78781f;
  cursor: pointer;
  position: relative;
}
.reserva_internet_pagado{
  background-color:#4169ef;
  cursor: pointer;
  position: relative;
}
.reserva_internet_multiple{
  background-color:#d0b5eb !important;
  cursor: pointer;
  position: relative;
}
.title_info{
  
    font-weight: bold;
    display: inline-block;
    text-align: end;
}


.info_entrenadores{
  padding-left: 15px;
  display: inline-block;
  text-align: end;
}
.torneo{
  background-color:#48581b;
  cursor: pointer;
  position: relative;

}
.info_d{
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 25em;
  height: max-content;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 2em;
  text-align: start;


}
.info_d_b{
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  top: -8em;
  position: relative;
  width: 25em;
  max-width: 25em;
  overflow-y: auto;
  height: max-content;
  border-radius: 18px;
  background-color: #fff;
  max-height: 25em;
  overflow-y: auto;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 2em;
  text-align: start;
  overflow-x: hidden;


}
.info_i_b{
  z-index: 1000;
  left: -23em;
  top: -8em;
  font-size: 1.3em;
  position: relative;
  width: 25em;
  max-height: 25em;
  overflow-y: auto;
  height: max-content;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 2em;
  text-align: start;

  overflow-x: hidden;
  

}
.info_i{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 25em;

  z-index: 1000;
  left: -23em;
  font-size: 1.3em;
  position: relative;
  width: 25em;
  height: max-content;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 2em;
  text-align: start;



}
.info_i_lg_info{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 20em;
  overflow-y: auto;
  overflow-x: hidden;
  height: 20em;
  z-index: 1000;
  left: -6em;
  bottom: 14.5em;
  font-size: 1.3em;
  position: relative;
  width: 8em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;

}
.info_i_lg{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 20em;
  overflow-y: auto;
  overflow-x: hidden;
  height: 20em;
  z-index: 1000;
  left: -28em;
  bottom: 14.5em;
  font-size: 1.3em;
  position: relative;
  width: 28em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;

}
.info_i_b_lg{
  max-height: 18em;
  height: 18em;
  overflow-y: auto;
  z-index: 1000;
  left: -28em;
  top: -8em;
  font-size: 1.3em;
  position: relative;
  width: 28em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;

  overflow-x: hidden;

}
.info_i_b_lg_info{
  max-height: 12em;
  z-index: 1000;
  left: -9em;
  bottom: 20em;
  font-size: 1.3em;
  position: relative;
  width: 12em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;


}
.info_d_lg{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 25em;
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 28em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;
}
.info_d_lg_info{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 5em;
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 8em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;
  position: absolute;
  right: 40em;
}
.info_d_b_lg{
  max-height: 18em;
  overflow-y: auto;
  overflow-x: hidden;
  top: -4em;
  left: 1em;
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 28em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;
}

.info_d_lg_info{
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 5em;
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 8em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;
}
.info_d_b_lg_info{
  max-height: 5em;
  overflow-y: auto;
  overflow-x: hidden;
  top: -4em;
  left: 1em;
  z-index: 1000;
  margin-left: 2.5em;
  font-size: 1.3em;
  position: relative;
  width: 8em;
  height: fit-content;
  margin-bottom: 1em;
  border-radius: 18px;
  background-color: #fff;
  color: #000;
  display: none;
  padding: .5em;
  padding-left: 1em;
  text-align: start;
}

.reserva_internet{
  background-color: #f2b929 !important;
  cursor: pointer;
}
.torneo:hover>.info_d{
  display: inline-block;
  cursor: default;

}
.reserva_internet:hover>.info_i_b_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_i_b_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_d_b_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_d_b_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_d_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_d_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_i_b_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_i_b_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_d_b_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_d_b_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_d_lg{
  cursor: default;
  display: inline-block;
}
.reserva_internet_multiple:hover>.info_d_lg_info{
  cursor: default;
  display: inline-block;
}

.reserva_internet_multiple:hover>.info_d_lg_info{
  cursor: default;
  display: inline-block;
}
.reserva_internet:hover>.info_d_lg_info{
  cursor: default;
  display: inline-block;
}
.clinica:hover>.info_d{
  display: inline-block;
  cursor: default;

}
.torneo:hover>.info_i{
  display: inline-block;
  cursor: default;

}

.torneo:hover>.info_i_b{
  display: inline-block;
  cursor: default;

}
.torneo:hover>.info_d_b{
  display: inline-block;
  cursor: default;

}
.clinica:hover>.info_i{
  display: inline-block;
  cursor: default;

}
.clinica:hover>.info_i_b{
  display: inline-block;
  cursor: default;

}
.clinica:hover>.info_d_b{
  display: inline-block;
  cursor: default;

}
.reserva_internet:hover>.info_i_lg{
  cursor: default;

  display: inline-block;
}
.reserva_internet_multiple:hover>.info_i_lg{
  cursor: default;

  display: inline-block;
}
.reserva_internet:hover>.info_i_lg_info{
  cursor: default;

  display: inline-block;
}
.reserva_internet_multiple:hover>.info_i_lg_info{
  cursor: default;

  display: inline-block;
}
.tpasado{
    background-color:#d3d3d3 !important;
  cursor: default !important;  
}
.disponible{
  background-color:#aaee7c;
  cursor: pointer;
}
.premium{
  background-color:#aaee7c;
  cursor: pointer;
  position: relative;
}
.premium_nav{
  background-color:#00FF9B;
  cursor: pointer;
  position: relative;
}
.descuento_nav{
  background-color:#CDEC0A;
  cursor: pointer;
  position: relative;

}
.premium::after{
   font-size: larger;
    content: 'P';
    color: #000;
    background-color: #fff !important;
    width: 22px;
    height: 25px;
    position: absolute;
    bottom: 0;
    right: 0;
  }
 
.descuento{
  background-color:#aaee7c;
  cursor: pointer;
  position: relative;

}
.descuento::after{
   font-size: larger;
    content: 'D';
    color: #000;
    background-color: #fff !important;
    width: 22px;
    height: 25px;
    position: absolute;
    bottom: 0;
    right: 0;
  }
.cerrado{
  background-color:#FF0000;
  cursor: not-allowed;
  color: #fff;
}
.buttons_options{
  width: max-content;
  height: 1.8em;
  line-height: 1.2em;
  background-color: #017069;
  color: #fff;
  font-size: 13px;
  margin-left: .4em;
  margin-right: .4em;
  text-align: center;
  border-radius: 3px;
}
.buttons_exception{
  width: max-content;
  height: 1.8em;
  line-height: 1.2em;
  background-color: #FF0202;
  color: #fff;
  font-size: 13px;
  margin-left: .4em;
  margin-right: .4em;
  text-align: center;
  border-radius: 3px;
}
.text_admin{
  width: max-content;
  height: 1.8em;
  line-height: 1.2em;
  color: #000;
  font-size: 13px;
  margin-left: .4em;
  margin-right: .4em;
  text-align: center;
  border-radius: 3px;
  padding: 5px;
  
  display: inline-block;
 position: absolute;
right: 0;  
}
.text_admin_edit{
  width: max-content;
  height: 1.8em;
  line-height: 1.2em;
  background-color: #51FE9A;
  color: #fff;
  font-size: 13px;
  margin-left: .4em;
  margin-right: .4em;
  text-align: center;
  border-radius: 3px;
  padding: 5px;
  box-shadow: 1px 1px 1px 2px #CAB5F4 ;
  display: inline-block;
 float: right;
 position: absolute;
 right: 0;
 margin-top:.4rem ;
 margin-bottom: .4rem;

}
.tadmin{
    background-color:#49FF00 !important;
  cursor: default !important;  
}
.tadminedith{
    background-color:#51FE9A !important;
  cursor: default !important;  
}
.text_admin_nombre{
  width: max-content;
  font-weight: bold;
  color: #fff;
  
}

.buttons_options:hover{
 
  background-color: #014B47;
  
}
.infomovimientos{
  right: 0;
  margin-right: 3em;
  width: 100px;
  height: 100px;
  background-color: #00FF9B;
  position: absolute;
  z-index: 1000;
}

.description_squards{
  width: 1.8em;
  height: 1.8em;
  line-height: 1.2em;
  color: #f4f6f9;
  font-size: 13px;
  margin-left: .4em;
  margin-right: .4em;
  text-align: center;
  border-radius: 3px;
  display: inline-block;

  cursor: default;
}
.description_text{
    position: relative;
    bottom: 5px;
  width: max-content;
  height: max-content;
  text-align: start;
  font-size: 16px;
  color: #000;
  font-weight: 600;
  display: inline-block;

}
.description_info{
  width: max-content;
  height: max-content;
  text-align: start;
  font-size: 10px;
  color: #000;
  font-weight: 600;
  
}
.date{
  width: max-content;
  height: max-content;
}
.title_date{
  margin-left: 1.5em;
  margin-right: .3em;
}
.svg{
  position: relative;
}
.svg svg{
  position: relative;
  top: -3px;
  left: -6px;
}


.fixed-div {
    top: -5px;
  width: 100%;
  position: fixed;
  height: max-content;
  padding-bottom: 1em;
  background-color: #f4f6f9;

  z-index: 100;
}

.top-div {
    justify-content: center;
    align-items: center;
    text-align: start;
    margin-top: 15px;
 width: 100%;
z-index: 100;

}

.bottom-div {
  bottom: 0;

}

</style>

<body class="sidebar-mini layout-fixed sidebar-collapse ">
  <div class="wrapper">
    <?php include("menu.php") ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-8">
            </div>
          </div>
        </div>
      </div>
      <div class="fixed-div">
      <div class="top-div">
      <h3 class="title" >Reservas del <span id="dia_hoy"></span></h3>
      <div class="description_squards tpasado"></div>
        <div class="description_text">Tiempo Pasado</div>
        <div class="description_squards disponible"></div>
        <div class="description_text">Disponible</div>
        <div class="description_squards premium_nav"></div>
        <div class="description_text">Premium</div>
        <div class="description_squards descuento_nav"></div>
        <div class="description_text">Descuento</div>
        <div class="description_squards cerrado"></div>
        <div class="description_text">Cerrado</div>
        <div class="description_squards clinica"></div>
        <div class="description_text">Clínica</div>
        <div class="description_squards torneo"></div>
        <div class="description_text">Torneo</div>
        <div class="description_squards reserva_internet"></div>
        <div class="description_text">Reserva Play Padel</div>
      
    </div>
<div class="bottom-div">

        <span class="title_date">Fecha : </span>  <input class="date" type="date" id="dia_seleccionado_buscado">
          <button id="menos_dia" class="buttons_options">Dia -</button>
          <button id="ayer" class="buttons_options">Ayer</button>
           <button id="today" class="buttons_options">Hoy</button>
           <button id="manana"  class="buttons_options">Mañana</button>
           <button id="pasado_manana" class="buttons_options">Pasado Mañana</button>
           <button id="mas_dia" class="buttons_options">Dia +</button>
           <button class="buttons_options" onclick="modal()">Configurar App</button>
      
</div>

      </div>
       
   
      <section class="content justify-content-center " style="background-color: #fff; margin-top:1em;">
      <div class="row">
        <div id="horas_izquierda" class="col-1">
        <div class="canchas_title"> Hora</div>
      </div>
        <div id="canchas" style="overflow-y: hidden;" class="canchas col-10">
       
        </div>
        <div id="horas_derecha" class="col-1">
        <div class="canchas_title"> Hora</div>
      </div>
      </div>
      </section>
</div>
<div class="modal fade bd-example-modal-lg" id="modalReservas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> 
          <span id="dia_seleccionado_"></span> 
          <br>
          <span id="hora_selecionada"></span> 
          <br>
          <span id="cancha_selecionada"></span>
          <br>
          <span id="Precio"></span>

        </h5>
       
      </div>
      <div class="modal-body">
            <div class="row" id="opciones">
              <p  class="col-md-12">Opciones de reserva</p>
              <button class="buttons_options" onclick="reserva()">Reserva</button>
            </div>

             
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

      </div>
    </div>
  </div>
</div>



<div class="modal fade bd-example-modal-lg" id="modalReserva" >
  <div class="modal-dialog modal-lg  modal-dialog-centered" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center mx-auto" id="exampleModalLabel"> 
        Reserva
        </h5>
      </div>
      <div class="modal-body">
      <form id="FormReserva" action="" method="post">
              <div class="modal-body">
              <div class="row">
                <div class="col-md-3">
                    <label class="form-check-label">Cancha</label>
                    <input disabled id="cancha_reserva" class="form-control"></input>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Fecha inicio</label>
                    <input style="font-size: small;" disabled id="fecha_hoy" class="form-control"></input>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Hora inicio</label>
                    <input type="text" id="hora_inicio" disabled required class="form-control">
                    
                </div>
                
                <div class="col-md-3">
                            <label  class="text-center">Duración</label>
                            <div class="form-group row">
                              <div  class="col-md-12">
                                <div class=" form-check-inline ">
                                  <label class="form-check-label" for="inlineRadio1"> 90 min&nbsp;</label>
                                  <input type="radio" value="90 min" id="duracion" checked name="duracion">
                                </div>
                                <div class=" form-check-inline">
                                  <label class="form-check-label" for="inlineRadio2"> 120 min &nbsp;</label>
                                  <input type="radio" value="120 min" id="duracion" name="duracion">
                                </div>
                              </div>
                            </div>
                          </div>

                <div class="col-md-3">
                    <label class="form-check-label">Jugador 1</label>
                    <select name="" id="clientes" required class="form-control select2 select2-hidden-accessible"></select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 2</label>
                    <select name="" id="clientes_2" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 3</label>
                    <select name="" id="clientes_3" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 4</label>
                    <select name="" id="clientes_4" class="form-control select2 select2-hidden-accessible"></select>
                </div>

                <div class="col-md-4">
                    <label class="form-check-label">Tipo de reserva</label>
                    <input type="text" name="" id="tipo_reserva" value="Play Padel" required class="form-control" disabled>
                    </input>
                </div>
                <div class="col-md-8">
                    <label class="form-check-label">Observaciones</label>
                    <textarea type="text" name="observaciones" id="observaciones" autocomplete="off" class="form-control" col="40" row="10" ></textarea>
                </div>
              
              
            </div>
          
      </div>
      <div class="modal-footer">
      <input type="submit" id="btnReserva" class="btn btn-success btn-flat" value="Reserva">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

      </div>
      </form>           

    </div>
  </div>
</div>



</div>


<div class="modal fade bd-example-modal-lg" id="modalReservaMultiple" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center mx-auto" id="exampleModalLabel"> 
        Reserva Multiple
        </h5>
      </div>
      <div class="modal-body">
      <form id="FormReservaMultiple" action="" method="post">
              <div class="modal-body">
              <div class="row">
              
             
                <div class="col-md-3">
                    <label class="form-check-label">Hora inicio</label>
                    <input type="text" id="hora_inicio_multiple" disabled required class="form-control">
                    
                </div>
                
                <div class="col-md-3">
                            <label id="duracion_text" class="text-center form-check-label"></label>
                            <div class="form-group row">
                              <div id="duracion_contenedor" class="col-md-12">
                              
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4 ">
                    <label class="form-check-label">Tipo de reserva</label>
                    <input type="text" name="" id="tipo_reserva_multiple" disabled required class="form-control" value="Play Padel Multiple">
                    

                    </input>
                </div>  
                <div class="  col-md-3">
                    <label class="form-check-label">Jugador 1</label>
                    <select name="" id="clientes_multiple" required class="form-control select2 select2-hidden-accessible"></select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 2</label>
                    <select name="" id="clientes_2_multiple" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 3</label>
                    <select name="" id="clientes_3_multiple" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 4</label>
                    <select name="" id="clientes_4_multiple" class="form-control select2 select2-hidden-accessible"></select>
                </div>

                
                <div class="col-md-6">
                    <label class="form-check-label">Descripcion</label>
                    <textarea type="text" name="descripcion_multiple" id="descripcion_multiple" autocomplete="off" class="form-control" col="40" row="10" required></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-check-label">Observaciones</label>
                    <textarea type="text" name="observaciones_multiple" id="observaciones_multiple" autocomplete="off" class="form-control" col="40" row="10" required></textarea>
                </div>
                <div class="col-md-12 pt-3 row">
                    <label class="col-md-12" class="form-check-label">Días</label>
                         <div class="form-check">
                          <input type="checkbox" value="1"  class="form-check-input" id="dias">
                          <label class="form-check-label"  for="exampleCheck1">Lunes</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox" value="2"  class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Martes</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox" value="3"  class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Miércoles</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox"value="4"   class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Jueves</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox"value="5"  class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Viernes</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox"value="6"  class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Sábado</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-check">
                          <input type="checkbox" value="7"  class="form-check-input" id="dias">
                          <label class="form-check-label" for="exampleCheck1">Domingo</label>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                <br>                <br>

                </div>

                <br>
                <div id="canchas_checkboxs" class="col-md-12 row ml-auto">
                </div>

                <div class="col-md-4"><br>
                    <label class="form-check-label">Enviar correo electrónico con confirmación</label>
                    <input type="checkbox" name="enviar_correo_multiple" id="enviar_correo_multiple" autocomplete="off" checked></input>
                </div>
                
              
            </div>
          
      </div>
      <div class="modal-footer">
      <input type="submit" id="btnReservaMultiple" class="btn btn-success btn-flat" value="Reserva">
        <button type="button" class="btn btn-secondary" id="cerrarmultiple" data-dismiss="modal">Cerrar</button>

      </div>
      </form>           

    </div>
  </div>
</div>



</div>
<div class="modal fade bd-example-modal-lg" id="EditarReserva" >
  <div class="modal-dialog modal-lg  modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center mx-auto" id="exampleModalLabel" > 
        Reserva
        </h5>
      </div>
      <div class="modal-body">
      <form id="EditFormReserva" action="" method="post">
              <div class="modal-body">
              <div class="row">
              <div class="col-md-3">
                      <label class="form-check-label">Selecciona una cancha</label>
                      <select id="canchas_select" class="form-control select2"  autocomplete="off" required></select>
                    </div>
                <div class="col-md-3">
                    <label class="form-check-label">Fecha inicio</label>
                    <input type="date" style="font-size: small;" disabled required id="Editfecha_hoy" class="form-control"></input>
                </div>
                <div class="col-md-2">
                    <label class="form-check-label">Hora inicio</label>
                    <input type="time" id="Edithora_inicio" disabled required class="form-control">
                    
                </div>
                <div class="col-md-2">
                    <label class="form-check-label">Hora fin</label>
                    <input name="" style="font-size: small;" disabled id="Editduracion" disabled required class="form-control"></input>
                </div>
                <div class="col-md-1">
                    <label class="form-check-label">+ 30m</label>
                    <br>
                    <input type="checkbox" name="Editiempoextra" id="Editiempoextras" ></input>
                </div>

                <div class="col-md-3">
                    <label class="form-check-label">Jugador 1</label>
                    <select name="" id="Editclientes" required class="form-control select2 select2-hidden-accessible"></select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 2</label>
                    <select name="" id="Editclientes_2" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 3</label>
                    <select name="" id="Editclientes_3" class="form-control select2 select2-hidden-accessible"></select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">Jugador 4</label>
                    <select name="" id="Editclientes_4" class="form-control select2 select2-hidden-accessible"></select>
                </div>

                <div class="col-md-4">
                    <label class="form-check-label">Tipo de reserva</label>
                    <input type="text" name="" id="Edittipo_reserva" value="Play Padel" required class="form-control" disabled>
                    </input>
                </div>
                <div class="col-md-8">
                    <label class="form-check-label">Observaciones</label>
                    <textarea type="text" name="observaciones" id="Editobservaciones" autocomplete="off" class="form-control" col="40" row="10" ></textarea>
                </div>
                
                
              
            </div>
          
      </div>
      <div class="modal-footer">
      <input type="submit" id="EditbtnReserva" class="btn btn-success btn-flat" value="Actualizar Reserva">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

      </div>
      </form>           

    </div>
  </div>
</div>



</div>



<div class="modal fade bd-example-modal-sm" id="modalapp" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
          <div class="modal-content">
              <div class="modal-body">
                <div class="form-group row">
                  <div class="col-md-12 mx-auto">
                    <h4>Configuracion de intervalos de 90 min</h4>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-8 mx-auto">
                    <label class="form-check-label">A partir de que hora se usaran los intervalos</label>
                    <input type="time" class=" form-control " name="nombre" id="intervalo90min"  required>
                  </div>
                
                 
                 
                </div>
              <div class="modal-footer">
                <input type="submit" id="btnapp" class="btn btn-success btn-flat" value="Guardar Configuracion">
                <input type="button" class="btn btn-secondary btn-flat" data-dismiss="modal" value="Cerrar">
              </div>
          </div>
        </div>
      </div>
    </div>



    <?php include("footer.php"); ?>
  </div>
</div>
<?php include("scripts.php"); ?>
<script type="text/javascript"  src="scripts/calendarios.js"></script>
</body>
</html>













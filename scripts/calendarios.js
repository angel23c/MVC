$(document).ready(function(){
    createintervalosizquierda()
    createintervalosderecha()
    obtenerdiasemana()
    ObtenerTiempoMuerto()
    obtenerCanchasEdit()
    obtenerReservasxCliente()
    obteneracessosocios()
    obtenerConfiguraciones()
    obtenerHorariosApp()
    setInterval(function () {
      let minutos_transcurridos = new Date();
      let minutes = String(minutos_transcurridos.getMinutes());
      if (minutes =="0"|| minutes=="30") {
      dibujar_contenedores()
      }
    }, 60000);
    
  });
  let cancelacion =0;
  let accesos_socios =[];
  let maxreservasclientes = [];
  let tiemposinicialesreservas =[];
  let tiemposmuertosemana = [];
  let tiempo_muerto = undefined;
  let peticion_encontrarjugadores = 0;
  let encontrarjugadores_array =[];
  let idcanchaedit = 0;
  let actualizarmultiple = 0;
  let ideditareservamultiple = undefined;
  let horas_cancelacion = undefined;
  let horas_modificacion = undefined;
 
  $('#mySelect2').select2({
    dropdownParent: $('#modalReserva .modal-body')
});

  function obtenerHorariosApp(){
	$.ajax({
		type : 'POST',
		data : { op : 'obtenerhorarios' },
		url : 'mysql/HorariosApp/index.php',
		beforeSend : function(){
			
		},
		success : function(response){
			response = JSON.parse(response.trim());
			intervalo90min.value = response[0].horainiciointervalos;
		},
		error : function(e){
			console.log(e.responseText);
		},
		complete : function(){
			$.unblockUI();
		}
	});
}
  function modal(){
	$("#modalapp").modal({backdrop: 'static', keyboard: false}, 'show');
    
}


btnapp.addEventListener("click",(e)=>{
   if (intervalo90min.value =="") {
    return toastr.warning("asigne una hora")
   }
   else{
    $.ajax({
        type : 'POST',
        data : { op : 'insertarhorariosapp',horainiciointervalo:intervalo90min.value},
        url : 'mysql/HorariosApp/index.php',
        beforeSend : function(){
            $("#modalapp").hide();

        },
        success : function(response){
            toastr.success("HORA ASIGNADA")
        },
        error : function(e){
            toastr.error(e.responseText);

       },
        complete : function(){
            $.unblockUI();
            $("#modalapp").modal("hide");
            obtenerHorariosApp()
        }
    })
   }
})


intervalo90min.addEventListener("change",(e)=>{
    let hora_ = intervalo90min.value.split(":");
    if(hora_[1] !="30" && hora_[1] !="00"){
        intervalo90min.value = "";
      toastr.warning("SOLO SE PERMITEN INTERVALOS DE 3OM O 1H")
    }
})


  function obtenerCanchasEdit(){
    $.ajax({
        type : 'POST',
        data : { op : 'obtenerCanchas'},
        url : 'mysql/Canchas/index.php',
        beforeSend : function(){
            $("#canchas_select").empty();
        },
        success : function(response){
            response = JSON.parse(response.trim());
            for (var i = 0; i < response.length; i++) {
                if(response[i].status == "1"){
                        $("#canchas_select").append(`<option value="${response[i].idcanchas}">${response[i].idcanchas} - ${response[i].nombre}</option>`);
                    
                }
            }
        },
        error : function(e){
       },
        complete : function(){
            $("#canchas_select").trigger("change");
        }
    })
  }
  
  function obtenerConfiguraciones() {
    $.ajax({
      type: "POST",
      data: { op: "obtenerConfiguraciones"},
      url: "mysql/Configuraciones/index.php",
      beforeSend: function () {
        $.blockUI({ });
      },
      success: function (response) {
        response = JSON.parse(response.trim());
        horas_cancelacion = response[0].horas_cancelacion;
        horas_modificacion = response[0].horas_modificacion;
  
      },
      error: function (e) {
        console.log(e.responseText);
      },
      complete: function () {
        $.unblockUI();
  
      },
    });
  }
  
  
  $(document).ready(function(){
  
    $('.carousel').carousel({
      interval: 0
    });
  });
  let dias_semana = document.querySelectorAll("#dias");
  let obtencion_evento_click= [];
  let actualizar_evento_click= [];
  
  let existe = 0;
  
  function intervaloshorasmultiples(cancha,fecha){
    let tardedia = localStorage.getItem("hora").split(" ");
    let hora_selecionada = horastringsinletras(localStorage.getItem("hora"));
    let hora_comparar = new Date();
    let tiempo_mas =0;
    let canchatiempo = false;
    // if (hora_selecionada[0]="12") {
    //   tardedia[1]="pm";
    // }
    let hora_=formato24h(hora_selecionada[0],":"+hora_selecionada[1],":00",tardedia[1])
    hora_ = hora_.split(":");
  for (let i = 0; i <=4; i++) {
    hora_comparar.setHours(hora_[0],hora_[1],hora_[2]);
      hora_comparar.setMinutes(tiempo_mas,0,0);
          canchatiempo= encontrarlimitantehorario(hora_comparar.getHours()+":"+addZero(hora_comparar.getMinutes()) +":"+addZero(hora_comparar.getSeconds()),cancha,fecha);
          if (canchatiempo !=undefined) {
            horario_ini =formato24h(hora_selecionada[0],":"+hora_selecionada[1],":00",tardedia[1]);
         
             return diferencia_tiempo(horario_ini,canchatiempo);
          }
      tiempo_mas = tiempo_mas +30;
          
        }
  }
  function encontrarlimitantehorario(horario_ini,cancha,fecha){
  
    for (let i = 0; i < tiemposinicialesreservas.length; i++) {
          if (tiemposinicialesreservas[i].nombre ==cancha && tiemposinicialesreservas[i].fecha_inicio ==fecha && tiemposinicialesreservas[i].hora_inicio ==horario_ini) {
            return tiemposinicialesreservas[i].hora_inicio;
          }
          else{
          }
    }
  }
   async function tiemposmuertos(){
    tiemposmuertosemana = [];
    fechas_semana.forEach((c)=>{
      $.ajax({
        type : 'POST',
        data : { op : 'Reservasdelasemana',fecha:c.fecha},
        url : 'mysql/Reservas/index.php',
        beforeSend : function(){
        },
        success : function(response){
          response = JSON.parse(response.trim());
          response.forEach((e)=>{
            tiemposmuertosemana.push(e)
        })
        },
        error : function(e){
          console.log(e.responseText);
        },
        complete : function(){
          $.unblockUI();
    
        }
      });    
    })
  
  
  }
  
  async function Reservasdelasemanahorainiciales(){
    tiemposinicialesreservas = [];
    fechas_semana.forEach((e)=>{
      $.ajax({
        type : 'POST',
        data : { op : 'Reservasdelasemanahoraini',fecha:e.fecha},
        url : 'mysql/Reservas/index.php',
        beforeSend : function(){
        },
        success : function(response){
          response = JSON.parse(response.trim());
    
          response.forEach((e)=>
          tiemposinicialesreservas.push(e)
          )
        },
        error : function(e){
          console.log(e.responseText);
        },
        complete : function(){
          $.unblockUI();
    
        }
      });
    })  
  
    
  
  
  }
  
  
  function comprobartiemposmuertos(cancha,fecha){
  let comprobartiempomuerto = new Date();
  let comprobartiemposeleccionado = new Date();
  let permitirtiemposmuertos = false;
  let tardediamuerto = tiempo_muerto.split(" ");
  let tiempomuertoseleccionado = horastringsinletras(tiempo_muerto);
  let tardedia = localStorage.getItem("hora").split(" ");
  let hora_selecionada = horastringsinletras(localStorage.getItem("hora"));
  hora_muerta =formato24h(tiempomuertoseleccionado[0],":"+tiempomuertoseleccionado[1],":00",tardediamuerto[1]);
  hora_selecionada=formato24h(hora_selecionada[0],":"+hora_selecionada[1],":00",tardedia[1]);
  let hora_comparar = hora_selecionada;
  hora_muerta= hora_muerta.split(":");
  hora_selecionada= hora_selecionada.split(":");
  comprobartiempomuerto.setHours(hora_muerta[0],hora_muerta[1],hora_muerta[2]);
  comprobartiemposeleccionado.setHours(hora_selecionada[0],hora_selecionada[1],hora_selecionada[2]);
  if (comprobartiemposeleccionado>comprobartiempomuerto) {
    permitirtiemposmuertos =true;
  }
  if (permitirtiemposmuertos == true) {
    for (let i = 0; i < tiemposmuertosemana.length; i++) {
      if (tiemposmuertosemana[i].fecha_inicio==fecha && tiemposmuertosemana[i].nombre ==cancha) {
        if (tiemposmuertosemana[i].intervalo ==hora_comparar || tiemposmuertosemana[i].intervalo2 ==hora_comparar) {
           return true;
        }
      }
    }  
  }
  
  
  
  }
  
  
  
  function formato24h(hora_,minutes,seconds,intervalo)
  {
    if (parseInt(hora_)==12) {
  
      
    }
    else if(intervalo=="pm")
    {
        hora_=parseInt(hora_)+ 12;
    }
    hora_ =hora_ +minutes + seconds;
    return hora_.replace(/ /g, "");
  }
  
  function diferencia_tiempo(hora_ini,hora_fin){
                  let total = 0;
                  var horas = 0;
                  var minutos_inicio = hora_ini.split(':')
                  .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                  var minutos_final = hora_fin.split(':')
                  .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
              // Si la hora final es anterior a la hora inicial sale
                    if (minutos_final < minutos_inicio) return;
                    
                    var diferencia = minutos_final - minutos_inicio;
                    // Cálculo de horas y minutos de la diferencia
                    if (diferencia >=3600) {
                        horas = Math.floor(diferencia / 3600);
                        let menos = 3600 * horas;
                        
                        diferencia = diferencia -menos;
                    }
                    
                    var minutos = diferencia / 60;
                    total = horas *2;
                    if (minutos >29) {
                        total = total +1;
                    }
                    return total;
  }
  
  
  
    function horastringsinletras(hora_){
      let hora_selecionada = hora_;
      hora_selecionada=hora_selecionada.split(":");
      const regex = /[pmam]/g
      hora_selecionada[1] =  hora_selecionada[1].replaceAll(regex,"")
      return hora_selecionada;
    }
  
  
  
  
  for (let i = 0; i < dias_semana.length; i++) {
    dias_semana[i].addEventListener("click",(e,indice)=>{
      canchas_list = $('[id^="canchas_list"]');
      for (let c = 0; c < fechas_semana.length; c++) {
                if (dias_semana[i].nextElementSibling.textContent.trim() == fechas_semana[c].dia ) {
                  for (let index = 0; index < canchas_list.length; index++) {
                    let fecha_dia = fechas_semana[c].fecha.split("-");
  
                         let  cancha = intervaloshorasmultiples(canchas_list[index].getAttribute("nombre"),fecha_dia[0] +"-"+fecha_dia[1] +"-"+ fecha_dia[2]);
                         let tiempo__muertos=comprobartiemposmuertos(canchas_list[index].getAttribute("nombre"),fecha_dia[0] +"-"+fecha_dia[1] +"-"+ fecha_dia[2])
                         if (tiempo__muertos==true && actualizarmultiple==0) {
                            toastr.warning(`EXISTE TIEMPO MUERTO EN LA CANCHA ${canchas_list[index].getAttribute("nombre")}`)
                            obtencion_evento_click.push({"dia":dias_semana[i].nextElementSibling.textContent.trim(),"indice":index});
                            document.getElementById(`canchas_list${index}`).checked = false;
                            document.getElementById(`canchas_list${index}`).disabled = true;
                          }
                          if(cancha==3 && actualizarmultiple==0)
                          {
                            duracion_multiple[1].disabled =true;
                            obtencion_evento_click.push({"dia":fechas_semana[c].dia,"indice":index,"desabilitado": 1});
  
                          }
                          
                          if (cancha>0 &&cancha< 3  && actualizarmultiple==0) {
                          obtencion_evento_click.push({"dia":fechas_semana[c].dia,"indice":index});
                            document.getElementById(`canchas_list${index}`).checked = false;
                            document.getElementById(`canchas_list${index}`).disabled = true;
                          }
                          let tardedia = localStorage.getItem("hora").split(" ");
                          let hora_selecionada = horastringsinletras(localStorage.getItem("hora"));
                      
                          hora_selecionada=formato24h(hora_selecionada[0],":"+hora_selecionada[1],":00",tardedia[1]);
                          hora_selecionada=hora_selecionada.split(":");
                          hora_selecionada = addZero(parseInt(hora_selecionada[0])) +":"+hora_selecionada[1] +":"+hora_selecionada[2];
                          if (dias_semana[i].checked ==true && actualizarmultiple==0) {
                            $.ajax({
                              type : 'POST',
                              data : { op : 'Comprobartiempo',hora:hora_selecionada,fecha:fechas_semana[c].fecha,cancha:canchas_list[index].getAttribute("canchanombre") },
                              url : 'mysql/Reservas/index.php',
                              beforeSend : function(){
                              },
                              success : function(response){
                                response = JSON.parse(response.trim());
                                // alert(JSON.stringify(response))
                                if (response.length>0) {
                                  if (response[0].hora_fin != hora_selecionada) {
                                    for (let i = 0; i < response.length; i++) {
                                      document.getElementById(`canchas_list${index}`).checked = false;
                                      document.getElementById(`canchas_list${index}`).disabled = true;
                                      obtencion_evento_click.push({"dia":fechas_semana[c].dia,"indice":index});
                                      
                                    }
                                  }
                                }
                              },
                              error : function(e){
                                console.log(e.responseText);
                              },
                              complete : function(){
                                $.unblockUI();
                              }
                            });    
                          }
                        
        
                    
                  } 
                  
                  if (dias_semana[i].checked == false) {
  
                    obtencion_evento_click.forEach((e,i)=>{
                          if (e.dia.trim()==fechas_semana[c].dia) {
                           let check = obtencion_evento_click.filter(item => item.dia != e.dia && item.indice == e.indice )
  
                           if (check.length<1) {
                            document.getElementById(`canchas_list${e.indice}`).checked = true;
                              document.getElementById(`canchas_list${e.indice}`).disabled = false;     
                            }
                            
                               obtencion_evento_click = obtencion_evento_click.filter(item => item.dia !== e.dia)
  
                          
                        }
                        
                      })
                      actualizar_evento_click.forEach((e,i)=>{
                        if (e.dia.trim()==fechas_semana[c].dia) {
                         let check = actualizar_evento_click.filter(item => item.dia != e.dia && item.indice == e.indice && item.visible ==1 )
  
                         if (check.length<1) {
                          document.getElementById(`canchas_list${e.indice}`).checked = false;
                      // document.getElementById(`canchas_list${e.indice}`).disabled = false;     
                          }
                          else{
                            e.visible = 0;
                          }
                            // alert(JSON.stringify(actualizar_evento_click))
                          // actualizar_evento_click = actualizar_evento_click.filter(item => item.dia !== e.dia)
  
                        
                      }
                      
                    })
  
                  }
                  if (dias_semana[i].checked == true) {
  
                      actualizar_evento_click.forEach((e,i)=>{
                        if (e.dia.trim()==fechas_semana[c].dia) {
                    
                          document.getElementById(`canchas_list${e.indice}`).checked = true;
                      // document.getElementById(`canchas_list${e.indice}`).disabled = false;     
                         
                            e.visible = 1;
                       
                          // alert(JSON.stringify(actualizar_evento_click))
  
                          // actualizar_evento_click = actualizar_evento_click.filter(item => item.dia !== e.dia)
  
                        
                      }
                      
                    })
  
                  }
                  
                }
          
        
      }
      })
    
  }
  
  let cancha_nombre = undefined;
  let hoy = new Date();
  let fechas_semana = [];
  dia = hoy.getDay()-1;;
  let total_mediahora =37;
  let array_eventos_marcados =[];
  let array_canchas =[];
  let dias = [
    "Lunes",
    "Martes",
    "Miercoles",
    "Jueves",
    "Viernes",
    "Sabado",
    "Domingo",
   ]
  
  let corregidor_hora =[
    {"hora_obtencion":  "6:00 am","hora_extraida":"6:00"},
    {"hora_obtencion":   "6:30 am","hora_extraida": "6:30"},
    {"hora_obtencion":  "7:00 am","hora_extraida": "7:00"},
    {"hora_obtencion":   "7:30 am", "hora_extraida":"7:30"},
    {"hora_obtencion":   "8:00 am","hora_extraida":"8:00"},
    {"hora_obtencion":   "8:30 am","hora_extraida":"8:30"},
    {"hora_obtencion":   "9:00 am", "hora_extraida":"9:00"},
    {"hora_obtencion":  "9:30 am","hora_extraida":"9:30"},
    {"hora_obtencion":   "10:00 am","hora_extraida":"10:00"},
    {"hora_obtencion":   "10:30 am","hora_extraida": "10:30"},
    {"hora_obtencion":   "11:00 am","hora_extraida": "11:00"},
    {"hora_obtencion":   "11:30 am","hora_extraida":"11:30"},
    {"hora_obtencion":   "12:00 pm","hora_extraida":"12:00"},
    {"hora_obtencion":   "12:30 pm","hora_extraida": "12:30"},
    {"hora_obtencion":   "1:00 pm", "hora_extraida":"13:00"},
    {"hora_obtencion":   "1:30 pm","hora_extraida":"13:30"},
    {"hora_obtencion":   "2:00 pm","hora_extraida": "14:00"},
    {"hora_obtencion":   "2:30 pm","hora_extraida":"14:30"},
    {"hora_obtencion":   "3:00 pm","hora_extraida":"15:00"},
    {"hora_obtencion":   "3:30 pm","hora_extraida":"15:30"},
    {"hora_obtencion":   "4:00 pm","hora_extraida":"16:00"},
    {"hora_obtencion":   "4:30 pm","hora_extraida":"16:30"},
    {"hora_obtencion":   "5:00 pm","hora_extraida":"17:00"},
    {"hora_obtencion":   "5:30 pm","hora_extraida":"17:30"},
    {"hora_obtencion":   "6:00 pm","hora_extraida":"18:00"},
    {"hora_obtencion":  "6:30 pm","hora_extraida":"18:30"},
    {"hora_obtencion":    "7:00 pm","hora_extraida":"19:00"},
    {"hora_obtencion":   "7:30 pm","hora_extraida":"19:30"},
    {"hora_obtencion":    "8:00 pm","hora_extraida":"20:00"},
    {"hora_obtencion":   "8:30 pm","hora_extraida":"20:30"},
    {"hora_obtencion":   "9:00 pm","hora_extraida":"21:00"},
    {"hora_obtencion":   "9:30 pm","hora_extraida":"21:30"},
    {"hora_obtencion":   "10:00 pm","hora_extraida":"22:00"},
    {"hora_obtencion":   "10:30 pm","hora_extraida":"22:30"},
    {"hora_obtencion":    "11:00 pm","hora_extraida":"23:00"},
    {"hora_obtencion":   "11:30 pm","hora_extraida":"23:30"},
    {"hora_obtencion":  "12:00 pm","hora_extraida":"00:00"},
  
  ]
  
  
  
   let buscar_bd=
   [
    {"dia": "Domingo", "numero":0},
    {"dia":"Lunes","numero":1},
    {"dia":"Martes","numero":2},
    {"dia":"Miercoles","numero":3},
    {"dia":"Jueves","numero":4},
    {"dia":"Viernes","numero":5},
    {"dia":"Sabado","numero":6}
   ]
   let meses =[
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo", 
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre"
   ]
   let clientes_1_id = undefined;
   let clientes_2_id = undefined;
   let clientes_3_id = undefined;
   let clientes_4_id = undefined;
   let clientes_1_id_multiple = undefined;
   let clientes_2_id_multiple = undefined;
   let clientes_3_id_multiple = undefined;
   let clientes_4_id_multiple = undefined;
   function meshoy(mes){
    if (mes<10) {
      return  "0"+mes;
    }
   }
   dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
  
   dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
   function obtenerReservas(){
    $.ajax({
          type : 'POST',
          data : { op : 'ReservasCalendario' },
          url : 'mysql/Reservas/index.php',
          beforeSend : function(){
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
        for (let i = 0; i < response.length; i++) {
          array_eventos_marcados.push({"fecha_inicio":response[i].fecha,"horario_ini":horastring(response[i].hora_inicio),"horario_fin":horastring(response[i].hora_fin),"cancha":response[i].cancha.replace(/\s+/g, ''),"tipo":response[i].tipo,"idcanchas":response[i].idcanchas});
          obtenerHorasIntervalosCalendario(response[i].hora_inicio,response[i].hora_fin,response[i].fecha,response[i].cancha.replace(/\s+/g, ''));
          
        }
          
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              
          }
      });
   }
  
  
   function dibujar_contenedores(){
    canchas.innerHTML = "";
    buscar_bd.forEach(e => {
      if (e.dia.trim() == dias[dia]) {
        obtenerCanchasDia(e.numero);
      }
     });
   }
   dibujar_contenedores();
  
   function dibujar_contenedores_buttons(dia){
    array_eventos_marcados = [];
  
    canchas.innerHTML = "";
    buscar_bd.forEach(e => {
      if (e.dia.trim() == dias[dia]) {
        obtenerCanchasDia(e.numero);
      }
     });
     reserva_canchas = [];
     recoger_fechas = [];
     recoger_canchas = [];
   }
  
   function obtenerCanchasPremiumDia(dia){
      $.ajax({
          type : 'POST',
          data : { op : 'obtenerCanchasPremiumDia',dia:dia},
          url : 'mysql/Canchas/index.php',
          beforeSend : function(){
              $.blockUI({ message: '<h4> Cargando canchas...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
       
            for (let d = 0; d < response.length; d++) {
              let condition = 0;
  
                      let squard_select = document.querySelectorAll(`#${response[d].nombre.replace(/ /g, "")}`);
                       for (let index = 0; index < squard_select.length; index++) {
                      let  hora_inicio  = horastring(response[d].hora_inicio);
                      let  hora_fin = horastring(response[d].hora_fin);
                        if (squard_select[index].textContent.trim() == hora_inicio) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  condition =1;
  
                        }
                      
                        if (condition ==1) 
                        {
                            if (!squard_select[index].classList.contains("reserva_internet") && !squard_select[index].classList.contains("clinica") && !squard_select[index].classList.contains("torneo")) {
                                    if (squard_select[index].classList.contains("disponible"))
                                    {
                                        squard_select[index].classList.remove("disponible");
                                    }
                                    if (squard_select[index].classList.contains("cerrado")) 
                                    {
                                        squard_select[index].classList.remove("cerrado");
                                    }
                                        squard_select[index].classList.add("premium");
                                        squard_select[index].title = "premium";
                            }    
                            
  
                        }     
                      
                        if (squard_select[index].textContent.trim() == hora_fin) 
                        {
  
                              
                                  condition =0;
                        } 
                        
                       }
                  // }
              
            }
        // }
              for (var i = 0; i < response.length; i++) {
      
              }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              obtenerCanchasDescuentoDia(dia);
              
          }
      })
  }
  
  
  
  function obtenerCanchasDescuentoDia(dia){
      $.ajax({
          type : 'POST',
          data : { op : 'obtenerCanchasDescuentoDia',dia:dia},
          url : 'mysql/Canchas/index.php',
          beforeSend : function(){
              $.blockUI({ message: '<h4> Cargando canchas...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
        // let squards = document.querySelectorAll("#canchas>div>.canchas_title");
        // for (let i = 0; i < squards.length; i++) {
            for (let d = 0; d < response.length; d++) {
              let condition = 0;
  
                      let squard_select = document.querySelectorAll(`#${response[d].nombre.replace(/ /g, "")}`);
                       for (let index = 0; index < squard_select.length; index++) {
                      let  hora_inicio  = horastring(response[d].hora_inicio);
                      let  hora_fin = horastring(response[d].hora_fin);
                        if (squard_select[index].textContent.trim() == hora_inicio) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  condition =1;
  
                        }
                        
                      
                        if (condition ==1) 
                        {
                             if (!squard_select[index].classList.contains("reserva_internet") && !squard_select[index].classList.contains("clinica") && !squard_select[index].classList.contains("torneo")) {

                                if (squard_select[index].classList.contains("disponible"))
                                {
                                    squard_select[index].classList.remove("disponible");
                                }
                                if (squard_select[index].classList.contains("cerrado")) 
                                {
                                    squard_select[index].classList.remove("cerrado");
                                }
                                    squard_select[index].classList.add("descuento");
                                    squard_select[index].title ="descuento";
                        }
                        }
                        if (squard_select[index].textContent.trim() == hora_fin) 
                        {
                                  condition =0;
                        }  
                        
                       }
                  // }
              
            }
        // }
              for (var i = 0; i < response.length; i++) {
      
              }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              fecha = hoy.getFullYear() + "-" +addZero((hoy.getMonth() + 1))+ "-" +addZero(hoy.getDate());
              obtenerReservasFecha(fecha);
             
          }
      })
  }
  
  function obtenerHorasIntervalosCalendario(hora_ini,hora_fin,fecha ,cancha){
    let hora_nueva = new Date();
    let hora_terminal = new Date();
     let hora_f= hora_fin.split(':');
     hora_terminal.setHours(hora_f[0],hora_f[1],hora_f[2]);
    var horas = 0;
    var minutos_inicio = hora_ini.split(':')
    .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
  var minutos_final = hora_fin.split(':')
    .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
  
  // Si la hora final es anterior a la hora inicial sale
  if (minutos_final < minutos_inicio) return;
  
  var diferencia = minutos_final - minutos_inicio;
  // Cálculo de horas y minutos de la diferencia
  if (diferencia >=3600) {
     horas = Math.floor(diferencia / 3600);
    let menos = 3600 * horas;
    
    diferencia = diferencia -menos;
  }
  
  var minutos = diferencia / 60;
  // alert((horas + ':'+ (minutos < 10 ? '0' : '') + minutos))
  let total = horas *2;
  if (minutos >29) {
    total = total +1;
    
  }
  minutos_inicio = hora_ini.split(':');
  let mediah =30;
    for (let i = 0; i < total-1; i++) {
      hora_nueva.setHours(minutos_inicio[0], minutos_inicio[1],minutos_inicio[2]);
      hora_nueva.setMinutes(mediah,0,0);    
           if (hora_nueva<hora_terminal) {
            // alert(cancha + " " + hora(hora_nueva.getHours(),hora_nueva.getMinutes()))
                array_eventos_marcados.push({"horario_ini":hora(hora_nueva.getHours(),hora_nueva.getMinutes()),"cancha":cancha,"fecha_inicio":fecha})      
           }
           mediah +=30;
  
    }
  }
  
  function tiempovalidoreserva(row,id){
    let lista_squards_cancha = document.querySelectorAll(`div[id="${id}"][row]`);
    let condition = 0;
      for (let i = 0; i < lista_squards_cancha.length; i++) {
         if (lista_squards_cancha[i].hasAttribute("row")) {
            if (lista_squards_cancha[i].getAttribute("row").trim()==row) {
                row =i;
                condition = 1;
             }
             if (condition >=1) {
              condition ++;
              
             }
         }
        
      }
    let colordistinto = 0;
    if (condition >=3) {
            for (let i = row; i < lista_squards_cancha.length; i++) {
              if (lista_squards_cancha[i].hasAttribute("row")) {
                        if (!lista_squards_cancha[i].classList.contains("disponible")&& !lista_squards_cancha[i].classList.contains("descuento") && !lista_squards_cancha[i].classList.contains("cerrado") && !lista_squards_cancha[i].classList.contains("premium")) {
                          colordistinto = 1;
                          break;
                      }
                      existe ++;
                      if (existe ==3) {
                          duracion[0].disabled = false;
                        }
                        if (existe == 4) {
                          duracion[1].disabled = false;
                        }
                       
  
                      }
          }
      }
    if (colordistinto =1 && existe<3) {
      existe = 0;
      return false;
    }
  
    if (existe<4) {
      duracion[1].disabled = true;
  
    }
    if (existe<3) {
      duracion[0].disabled = true;
      return false;
    }
    if(existe>=3){
      return true;
    }
  
  
    // 0 1 2 3 aceptar 90 m 0 120m  // - leng del total 
  }
  
  
  
  function Tiempopasado(hora_fin,fecha){
      let hoy_mismo = new Date();
      let total = 0;
      if(hoy_mismo.getDate()=== fecha.getDate() && hoy_mismo.getMonth() === fecha.getMonth() && hoy_mismo.getFullYear() === fecha.getFullYear()){
      
  
                  hora_ini ="06:00:00";
                  var horas = 0;
                  var minutos_inicio = hora_ini.split(':')
                  .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
                  var minutos_final = hora_fin.split(':')
                  .reduce((p, c) => parseInt(p) * 60 + parseInt(c));
              // Si la hora final es anterior a la hora inicial sale
                    if (minutos_final < minutos_inicio) return;
                    
                    var diferencia = minutos_final - minutos_inicio;
                    // Cálculo de horas y minutos de la diferencia
                    if (diferencia >=3600) {
                        horas = Math.floor(diferencia / 3600);
                        let menos = 3600 * horas;
                        
                        diferencia = diferencia -menos;
                    }
                    
                    var minutos = diferencia / 60;
                    // alert((horas + ':'+ (minutos < 10 ? '0' : '') + minutos))
                    total = horas *2;
                    if (minutos >29) {
                        total = total +1;
                    }
  
  
    } 
    else if(hoy_mismo>fecha)
    {
       total = total_mediahora;
    }
    return total;
  }
  
  
  function obtenerCanchasClinicasFecha(fecha){
      $.ajax({
          type : 'POST',
          data : { op : 'obtenerClinicasCalendario',fecha:fecha},
          url : 'mysql/Clinicas/index.php',
          beforeSend : function(){
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
            for (let d = 0; d < response.length; d++) {
              let condition = 0;
              let conditon_union_squards = 0;
              let nombre = null;
              let cont = 0;
                      let squard_select = document.querySelectorAll(`#${response[d].cancha.replace(/ /g, "")}`);
                       for (let index = 0; index < squard_select.length; index++) {
                      let  hora_inicio  = horastring(response[d].horario_entrada);
                      let  hora_fin = horastring(response[d].horario_salida);
                      if (squard_select[index].textContent.trim() == hora_inicio) 
                        {

                                // alert(squard_select[index].textContent.trim())

                                  if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("descuento")) 
                                  {
                                    squard_select[index].classList.remove("descuento");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                                  condition =1;
  
                        }
                        if (squard_select[index].textContent.trim() == hora_fin) 
                        {
  
                                  condition =0;
                                 
                        }
                        if (condition ==1) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("premium"))
                                  {
  
                                    squard_select[index].classList.remove("premium");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("descuento")) 
                                  {
                                    squard_select[index].classList.remove("descuento");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                                  if (!squard_select[index].classList.contains("reserva_internet")) {
                                    squard_select[index].classList.add("clinica");
                                    squard_select[index].title =response[d].nombre;
                                    nombre =response[d].nombre;
                                  }
                                  else{
                                    break;

                                  }

                              cont =1;
                        }
                        
                           
                        
                       }
                       if (cont>0) {
                        cont = 0;
                        let tamaño_squard = 0;
                        let pos1 = Node;
                        let text ="";
                      let title = document.querySelectorAll(`.clinica[title="${nombre}"]`); // Lila
                      let fecha_js = hoy;
                      for (let c = 0; c < title.length; c++) {
                         
                        let mes =fecha_js.getMonth() +1;
                      //   array_eventos_marcados.push({"horario_ini":title[c].textContent.trim(),"cancha":title[c].id.trim(),"fecha_inicio":fecha_js.getFullYear() +"-"+mes+"-"+fecha_js.getDate()})
                        if (conditon_union_squards== 0) {
  
  
                              pos1 = title[c];
                              pos1.classList.remove("horas_dibujadas");
                              text += title[c].textContent.trim() + " - ";
                              text += title[title.length-1].nextElementSibling.getAttribute("hora_dibujo");
                              conditon_union_squards = 1;
                            }
                            else{
                              title[c].remove();
                            }
                            tamaño_squard += 50;
                          
                      }
                      let clinica_info = document.createElement("div");
                      clinica_info.innerHTML =`
                      <div class="row justify-content-start">
                      <p class="title_info">Tipo   :</p> <span>&nbspClinica</span> <br>
                      </div>
                      <div class="row justify-content-start">
                      <p class="title_info">Nombre   :</p> <span>&nbsp${response[d].nombre}</span> <br>
                      </div>
                      <div class="row justify-content-start">
                      <p class="title_info">Horario :</p> <span>&nbsp${text}</span> <br>
                      </div>
                      <div class="row justify-content-start">
                      <p class="title_info">Entrenadores :</p> <br>
                      </div>
                    `;
                    $.ajax({
                      type : 'POST',
                      data : { op : 'obtenerentrenadores',idclinicas:response[d].idclinicas },
                      url : 'mysql/Clinicas/index.php',
                      beforeSend : function(){
                        $.blockUI();
                    
                      },
                      success : function(response){
                        response = JSON.parse(response.trim());
                        for (var k = 0; k < response.length; k++) {
                          clinica_info.innerHTML +=`
                          <p class="info_entrenadores">${response[k].nombre} </p> <br>
                          `;
                        }
                      },
                      error : function(e){
                        console.log(e.responseText);
                      },
                      complete : function(){
                        $.unblockUI();
                        clinica_info.innerHTML +=`
                        <div class="row justify-content-start">
                        <p class="title_info">Cupo :</p> <span>&nbsp${response[d].numero_de_participantes}</span> <br>
                        </div>
                        <div class="row justify-content-start">
                        <p class="title_info">Inscritos :</p> <span>&nbsp${response[d].inscritos_clinica}</span> <br>
                        </div>
                        `;
                      }
                    });
                
                      canchas = document.getElementById("canchas");
                      let cords_canchas = canchas.getBoundingClientRect();
                      let cords = pos1.getBoundingClientRect();
  
                      if (cords_canchas.right<cords.right +425 && cords_canchas.bottom >cords.bottom+425) {
                        clinica_info.classList.add("info_i");
  
                      }
                      else if( cords_canchas.bottom >cords.bottom+425){
                        clinica_info.classList.add("info_d");
  
                      }
                      else if(cords_canchas.bottom <cords.bottom+425 && cords_canchas.right<cords.right +425){
                        clinica_info.classList.add("info_i_b");
                        
                      }
                      else{
                        clinica_info.classList.add("info_d_b");
  
                      }
                      pos1.style.height = tamaño_squard +"px";
                      pos1.classList.add("acomodarsquard");
                      pos1.textContent = text;
                      pos1.appendChild(clinica_info);
                      pos1.setAttribute("hora",`${text}`)
                      pos1.title = "";
                      pos1.removeEventListener("click", clickHandler);
  
                      // pos1.removeEventListener();
                      }
              
            }
        // }
              for (var i = 0; i < response.length; i++) {
      
              }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              obtenerCanchasTorneosFecha(fecha);
              
          }
      })
  }
   function clickHandler(e) {
    let squard = e.currentTarget;
    
    localStorage.setItem("cancha",squard.parentNode.firstChild.textContent.trim())
    localStorage.setItem("hora",squard.getAttribute("hora"))
    localStorage.setItem("row",squard.getAttribute("row"))
    localStorage.setItem("cancha_id",squard.getAttribute("id"))
    let abrirmodal = 0;
    let comprobartiempomuerto = new Date();
    let comprobartiemposeleccionado = new Date();
    let permitirtiemposmuertos = false;
    let tardediamuerto = tiempo_muerto.split(" ");
    let tiempomuertoseleccionado = horastringsinletras(tiempo_muerto);
    let tardedia = localStorage.getItem("hora").split(" ");
    let hora_selecionada = horastringsinletras(localStorage.getItem("hora"));
  
  
    hora_muerta =formato24h(tiempomuertoseleccionado[0],":"+tiempomuertoseleccionado[1],":00",tardediamuerto[1]);
    hora_selecionada=formato24h(hora_selecionada[0],":"+hora_selecionada[1],":00",tardedia[1]);
    hora_muerta= hora_muerta.split(":");
    hora_selecionada= hora_selecionada.split(":");
    comprobartiempomuerto.setHours(hora_muerta[0],hora_muerta[1],hora_muerta[2]);
    comprobartiemposeleccionado.setHours(hora_selecionada[0],hora_selecionada[1],hora_selecionada[2]);
    if (comprobartiemposeleccionado>comprobartiempomuerto) {
      permitirtiemposmuertos =true;
    }
    
    existe = 0;
  
    cancha_selecionada.textContent = "Cancha: " + squard.parentNode.firstChild.textContent.trim();
    hora_selecionada.textContent = " Hora: " + squard.getAttribute("hora");
    dia_seleccionado_.textContent = " Dia: " + dias[hoy.getDay()-1] + " "+ hoy.getDate() + " "+ meses[hoy.getMonth()] +" "+ hoy.getFullYear();
    var numeros = squard.parentNode.firstChild.textContent.trim().match(/\d+/g);
     let hora_ =horastringsinletras(squard.getAttribute("hora"));
     let horastiempo = squard.getAttribute("hora").split(" ");
     $.ajax({
      type: "POST",
      data: { op: "obtenerhorariosprecioscalendario",dia:dias[hoy.getDay()-1],idcancha:parseInt(numeros.map(Number)),hora:formato24h(addZero(hora_[0]),":"+hora_[1],":00",horastiempo[1]),numdia:hoy.getDay()-1 },
      url: "mysql/Canchas/index.php",
      beforeSend: function () {
        $.blockUI({ });
      },
      success: function (response) {
        response = JSON.parse(response.trim());
        if (response.length >=2) {
           Precio.textContent = `Precio:  de 90 min $${response[0].c90!=null?response[0].c90:"0"} y 120 min $${response[0].c120!=null?response[0].c120:"0"}`;    
      }
        else if (response.length>0) 
            
        {
          Precio.textContent = `Precio: de  90 min $${response[0].c90!=null?response[0].c90:"0"} y 120 min $${response[0].c120!=null?response[0].c120:"0"}`;    
  
        }
  
        if (permitirtiemposmuertos== true) {
          let cuadros = document.querySelectorAll(`#${squard.getAttribute("id")}`);
          for (let i = 0; i < cuadros.length; i++) {
                if (cuadros[i].getAttribute("row")==squard.getAttribute("row")) {
                    if (i- 2 > 0 && cuadros[i-2].classList.contains("reserva_internet") ||i- 2 > 0 && cuadros[i-2].classList.contains("clinica")
                    ||i- 2 > 0 && cuadros[i-2].classList.contains("torneo") || i- 3 > 0 && cuadros[i-3].classList.contains("reserva_internet") ||i- 3 > 0 &&
                     cuadros[i-3].classList.contains("clinica")
                    ||i- 3 > 0 && cuadros[i-3].classList.contains("torneo")  )
                     {
                      if (i- 1> 0 && cuadros[i-1].classList.contains("reserva_internet") ||i- 1> 0 && cuadros[i-1].classList.contains("clinica")
                      ||i- 1 > 0 && cuadros[i-1].classList.contains("torneo"))
                      {

                      }
                      else{
                        toastr.warning("TIEMPO MUERTO");
                        abrirmodal =1;
                      }
                      

                    }
                }
               
            
          }
          $("#FormReservaMultiple").trigger("reset");
          $("#FormReserva").trigger("reset");
          clientes.value = "";
          clientes_2.value = "";
          clientes_3.value = "";
          clientes_4.value = "";
          clientes_multiple.value = "";
          clientes_2_multiple.value = "";
          clientes_3_multiple.value = "";
          clientes_4_multiple.value = "";
          $("#clientes").trigger("change");
          $("#clientes_2").trigger("change");
          $("#clientes_3").trigger("change");
          $("#clientes_4").trigger("change");
          $("#clientes_multiple").trigger("change");
          $("#clientes_2_multiple").trigger("change");
          $("#clientes_3_multiple").trigger("change");
          $("#clientes_4_multiple").trigger("change");
        // alert(tiempo_muerto);
        obtencion_evento_click= [];
        canchas_checkboxs.innerHTML = "";
        obtenerCanchas();
        dias_semana.forEach((e)=>{
          if (e.value>=hoy.getDay()) {
            if(e.hasAttribute("disabled"))
            {
              e.removeAttribute("disabled");
            }
          }
          else{
            e.disabled = true;
          }
        })
      }
      },
      error: function (e) {
        console.log(e.responseText);
      },
      complete: function () {
        $.unblockUI();
        if (abrirmodal == 0) {
            obtenerReservasxCliente()
             obteneracessosocios()
          $("#modalReservas").modal({backdrop: 'static', keyboard: false}, 'show');
        }

      },
    });
   
  
  
  
  }
  
  function obtenerReservasFecha(fecha){
      $.ajax({
          type : 'POST',
          data : { op : 'obtenerReservas',fecha:fecha},
          url : 'mysql/Reservas/index.php',
          beforeSend : function(){
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
            for (let d = 0; d < response.length; d++) {
              let condition = 0;
              let conditon_union_squards = 0;
              let nombre = null;
              let cont = 0;
              let  hora_inicio  = horastring(response[d].hora_inicio);
              let  hora_fin = horastring(response[d].hora_fin	);
              let squard_select = document.querySelectorAll(`#${response[d].cancha.replace(/ /g, "")}`);
  
              for (let index = 0; index < squard_select.length; index++) {
                      
                      if (squard_select[index].textContent.trim() == hora_inicio) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("premium"))
                                  {
  
                                    squard_select[index].classList.remove("premium");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                                  condition =1;
                                  
                                    
                                  
                        }
                        if (squard_select[index].textContent.trim() == hora_fin) 
                        { 
  
                          condition =0;
                                 
                        }
                        if (condition ==1) 
                        {
  
                          if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("premium"))
                                  {
  
                                    squard_select[index].classList.remove("premium");
                                  }
                                  if (squard_select[index].classList.contains("descuento"))
                                  {
  
                                    squard_select[index].classList.remove("descuento");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                              if (response[d].tipo_reserva=="Play Padel Multiple") {
                                squard_select[index].classList.add("reserva_internet_multiple");
  
                              }
                              else{
                                squard_select[index].classList.add("reserva_internet");
  
                              }
  
                              squard_select[index].title =response[d].nombre;
                              nombre =response[d].nombre;
                              cont =1;
                        }
                         
                           
                        
                       }
                       if (cont>0) {
                        cont = 0;
                        let tamaño_squard = 0;
                        let pos1 = Node;
                        let text ="";
                      let title = document.querySelectorAll(`.reserva_internet[title="${nombre}"]`);
                      if (title.length == 0) {
                        title = document.querySelectorAll(`.reserva_internet_multiple[title="${nombre}"]`);
                      } // Lila
                        for (let c = 0; c < title.length; c++) {
                          let fecha_js = hoy; 
                          let mes =fecha_js.getMonth() +1;
                          // array_eventos_marcados.push({"horario_ini":title[c].textContent.trim(),"cancha":title[c].id.trim(),"fecha_inicio":fecha_js.getFullYear() +"-"+mes+"-"+fecha_js.getDate()})
                            if (conditon_union_squards== 0) {
                              pos1 = title[c];
                              pos1.classList.remove("horas_dibujadas");
                              
                              text += title[c].textContent.trim() + " - ";
                              text += title[title.length-1].nextElementSibling.getAttribute("hora_dibujo");
                              conditon_union_squards = 1;
                            }
                            else{
                              title[c].remove();
                            }
                            tamaño_squard += 50;
                          
                      }
                      let torneo_info = document.createElement("div");
                      
                      canchas = document.getElementById("canchas");
                      let cords_canchas = canchas.getBoundingClientRect();
                      let cords = pos1.getBoundingClientRect();
                      if (cords_canchas.right<cords.right +425 && cords_canchas.bottom >cords.bottom+425) {
                        torneo_info.classList.add("info_i_lg");
  
                      }
                      else if( cords_canchas.bottom >cords.bottom+425){
                        torneo_info.classList.add("info_d_lg");
  
                      }
                      else if(cords_canchas.bottom <cords.bottom+425 && cords_canchas.right<cords.right +425){
                        torneo_info.classList.add("info_i_b_lg");
  
                      }
                      else{
                        torneo_info.classList.add("info_d_b_lg");
  
                      }
                      pos1.addEventListener("mouseover", (e)=>{
                        if (torneo_info.innerHTML == "") {
                          torneo_info.innerHTML = "";
                          encontrarjugadores(response[d].idreservas,torneo_info,text,response[d].fecha_inicio,response[d].hora_inicio,response[d].hora_fin,response[d].administrador,response[d].idcanchas);
                        }
                      });
                     
                      pos1.style.height = tamaño_squard +"px";
                      pos1.classList.add("acomodarsquard");
                      pos1.textContent = text;
                      pos1.title = ``;
                      pos1.appendChild(torneo_info);
                      pos1.setAttribute("hora",`${text}`)
                      pos1.removeEventListener("click", clickHandler);
                      }
              
            }
        // }
              for (var i = 0; i < response.length; i++) {
      
              }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              obtenerCanchasClinicasFecha(fecha);
    
          }
      })
  }
  function diff_hours(dt2, dt1) 
   {
  
    var diff =(dt2.getTime() - dt1.getTime()) / 1000;
    diff /= (60 * 60);
    return Math.abs(Math.round(diff));
    
   }
  
  function encontrarjugadores(idreserva,div,text,fecha,hora,hora_fin,nombre,idcanchas){
  let tiporeserva = "Reserva";
  let crearbotontes = true;
  let crearedit = true;
  let fecha_condicion = new Date(fecha)
  fecha_condicion.setMonth(fecha_condicion.getMonth()+1)
  fecha_condicion.setDate(fecha_condicion.getDate()+1);
  let hora_condicion = hora; 
  hora = hora.split(":");
  fecha_condicion.setHours(hora[0],hora[1],hora[2]);
  let fecha_actual = new Date();
  fecha_condicion_ = fecha_condicion;
  fecha_actual.setMonth(fecha_actual.getMonth()+1)
  //  fecha_condicion = addZero(fecha_condicion.getDate()) + "-"+addZero(fecha_condicion.getMonth()) + "-"+fecha_condicion.getFullYear()+ " "+ addZero(fecha_condicion.getHours()) +":"+addZero(fecha_condicion.getMinutes())+":"+addZero(fecha_condicion.getSeconds())
  //  fecha_actual = addZero(fecha_actual.getDate()) + "-"+addZero(fecha_actual.getMonth()) + "-"+fecha_actual.getFullYear()+ " "+ addZero(fecha_actual.getHours()) +":"+addZero(fecha_actual.getMinutes())+":"+addZero(fecha_actual.getSeconds())
  
  //  var startTime = moment(fecha_actual, 'DD-MM-YYYY hh:mm:ss');
  // var endTime = moment(fecha_condicion, 'DD-MM-YYYY hh:mm:ss');
  // var hoursDiff = endTime.diff(startTime, 'hours');
  
  
  if(div.parentNode.classList.contains("reserva_internet_multiple"))
  {
      tiporeserva ="Reserva Mutliple";
  }

    let cont_jugadores =0;
    let cont_socios =0;
      div.setAttribute("idreserva",idreserva);
      $.ajax({
        type : 'POST',
        data : { op : 'traerJugadoresReserva',idreservas:idreserva },
        url : 'mysql/Pagos/index.php',
        beforeSend : function(){
        },
        success : function(response){
          response = JSON.parse(response.trim());
          div.innerHTML =`
          <div class="">
             <p class="title_info">Tipo   :</p> <span>${tiporeserva}</span> <br>
             <p class="title_info">Horario :</p> <span>${text}</span> <br>
             <p class="title_info">Cancha :</p> <span>${div.parentNode.parentNode.firstChild.textContent}</span> <br>
             <p class="title_info">Reservador por :</p>  <br>
  
          </div>
     
          `;
  
        
          let icon=`<span class="col-md-2">  
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
          <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
          <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2H3z"/>
        </svg>`;
          for (var i = 0; i < response.length; i++) {
                div.innerHTML +=`
                <p class="col-md-12 row justify-content-start ml-auto"><span class="col-md-6">${response[i].jugador}</span><span class="col-md-4">${response[i].tipo}</span>
                </p>
                `;
                  
           }
         
     
  
           let condicion_cancelacion = parseInt(fecha_actual.getHours())+ parseInt(horas_cancelacion);
           let condicion_modificar = parseInt(fecha_actual.getHours())+ parseInt(horas_modificacion);
         
           
          
         
        
        },
        error : function(e){
          console.log(e.responseText);
        },
        complete : function(){
          dia = hoy.getDay()-1;
          
          $.ajax({
            type: "POST",
            url: "mysql/Reservas/index.php",
            data : { op : 'precioreserva',idreservas:idreserva,hora:hora_condicion,hora2:hora_fin,fecha:fecha,numdia:hoy.getDay(),dia:dias[dia],idcancha:idcanchas},
            beforeSend : function(){
              $.blockUI({});
            },
            success: function (data) {
             let response = JSON.parse(data.trim());
            let condicion =0;
              
              if (response[0].horario=="hora_premium" || response[0].horario=="hora_descuento") {
                condicion = 1;  
                if (response[0].tiempo90m =="SI") {
                  div.innerHTML += `
                  <p class="title_info">Costo de la reserva  :</p> <span>$${response[0].costo}</span> <br>
                  `;
                }
                else if(response[0].tiempo120m =="SI"){
                  div.innerHTML += `
                  <p class="title_info">Costo de la reserva  :</p> <span>$${response[0].costo_120==null?0:response[i].costo_120}</span> <br>

                  `;
                }
                else{
                  let textra = parseInt(response[0].tiempoextra)/30;
                  let precio = parseInt(response[0].costo_120)/4;
                  let costo = (precio* textra) + parseInt(response[0].costo_120);
                  div.innerHTML += `
                  <p class="title_info">Costo de la reserva  :</p> <span>$${costo}</span> <br>

                  `;
                   
                }
              }
              else if (condicion==0 && response[0].horario=="horario_general" ) {
                  if (response[0].tiempo90m =="SI") {
                    div.innerHTML += `
                    <p class="title_info">Costo de la reserva  :</p> <span>$${response[0].costo}</span> <br>
                    `;
                  }
                  else if(response[0].tiempo120m =="SI"){
                    div.innerHTML += `
                    <p class="title_info">Costo de la reserva  :</p> <span>$${response[0].costo_120}</span> <br>

                    `;
                  }
                  else{
                    let textra = parseInt(response[0].tiempoextra)/30;
                    let precio = parseInt(response[0].costo_120)/4;
                    let costo = (precio* textra) + parseInt(response[0].costo_120);
                    div.innerHTML += `
                    <p class="title_info">Costo de la reserva  :</p> <span>$${costo}</span> <br>

                    `;
                     
                  }
                  
              }

              if (crearbotontes == true) {
                crearbotontes = false;
              
              var diferenciaMs = fecha_condicion.getTime() - fecha_actual.getTime();
             var diferenciaHoras = diferenciaMs / 3600000;
             if (parseInt(addZero(fecha_condicion.getHours()))>="12" ) {
                 if (diferenciaHoras >7) {
                  cancelacion =0;
                   div.innerHTML += `
                   <button  id="editar_reserva" onclick ="editareserva(this)" class="buttons_options">Editar</button>

                   <button id="cancelar_reserva" onclick ="cancelareserva(this)" class="buttons_options">Cancelar</button>
                   `;
                 }else{
                  cancelacion =1;
                    div.innerHTML += `

                    <button  id="editar_reserva" onclick ="editareserva(this)" class="buttons_options">Editar</button>

                    <button id="cancelar_reserva" onclick ="cancelareserva(this,1)" class="buttons_exception">Cancelar</button>
                    `; 
                 }
             }
             else if ( parseInt(addZero(fecha_condicion.getHours()))<"12") {
               if (diferenciaHoras >14) {
                cancelacion =0;

                 div.innerHTML += `
                 <button  id="editar_reserva" onclick ="editareserva(this)" class="buttons_options">Editar</button>

                 <button id="cancelar_reserva" onclick ="cancelareserva(this)" class="buttons_options">Cancelar</button>
                 `;
               }
               else{
                cancelacion =1;
                div.innerHTML += `
                

                <button  id="editar_reserva" onclick ="editareserva(this)" class="buttons_options">Editar</button>

                <button id="cancelar_reserva" onclick ="cancelareserva(this,1)" class="buttons_exception">Cancelar</button>
                `;
             }
             }
             
     
               div.innerHTML +=`
               <p class="text_admin ml-auto"><span class="">Creada por ${nombre}</span></p>
     
     
               `; 
              }  
              
             
             
            },
            error: function (e) {
            },
            complete : function(){
              $.unblockUI();

           
               $.ajax({
                type : 'POST',
                data : {op:"ReporteReserva",idreservas:idreserva},
                url : 'mysql/Reservas/index.php',
                beforeSend : function(){
                    // $.blockUI({ message: '<h4> Cargando Lugares...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                },
                success : function(response){
                  response = JSON.parse(response.trim());
                  if (crearedit ==true && response.length>0) {
                      crearedit =false;
                      div.innerHTML +=`
                      <br>
                       <p class="text_admin ml-auto"><span class="">Editada por ${response[0].nombre}</span></p>
                  `; 
                    }
                 
                  
                
                    
                },
                error : function(e){
                    console.log(e.responseText);
                },
                complete : function(){
                    $.unblockUI();
                }
            });
            }
          });
          $.unblockUI();

        }
      });
    
      
    
  }
  function cancelareserva(e,penalizacion=null){
  if(e.parentNode.parentNode.classList.contains("reserva_internet")){
      $.ajax({
      type : 'POST',
      data : {op:"CancelarReserva",idreservas:e.parentNode.getAttribute("idreserva"),penalizacion:penalizacion==null?null:penalizacion},
      url : 'mysql/Reservas/index.php',
      beforeSend : function(){
          // $.blockUI({ message: '<h4> Cargando Lugares...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
      },
      success : function(response){
        if (penalizacion ==null) {
          toastr.success("RESERVA CANCELADA");                    
        }
        else{
          toastr.success("RESERVA CANCELADA CON PENALIZACIÓN");                    

        }
           //   hoy = new Date();
             // dia = hoy.getDay()-1;
            dibujar_contenedores();
            tiemposmuertos();
            Reservasdelasemanahorainiciales()
          
      },
      error : function(e){
          console.log(e.responseText);
      },
      complete : function(){
          $.unblockUI();
          obtenerReservasxCliente()

      }
  });
  }
  else if(e.parentNode.parentNode.classList.contains("reserva_internet_multiple")){
      $.ajax({
          type : 'POST',
          data : {op:"CancelarReservasMultiples",idreservas:e.parentNode.getAttribute("idreserva")},
          url : 'mysql/Reservas/index.php',
          beforeSend : function(){
              // $.blockUI({ message: '<h4> Cargando Lugares...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
          },
          success : function(response){
                  if (cancelacion == 0) {
                    toastr.success("RESERVA CANCELADA");                    
                  }
                  else{
                    toastr.success("RESERVA CANCELADA CON PENALIZACIÓN");                    

                  }
                  hoy = new Date();
                  dia = hoy.getDay()-1;
                dibujar_contenedores();
                tiemposmuertos();
                Reservasdelasemanahorainiciales()
              
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
          }
      });
  }
  }
  
  function editareserva(e) {
    if (Editiempoextras.disabled==true) {
      Editiempoextras.removeAttribute("disabled");
    }
    
    localStorage.setItem("idreserva",e.parentNode.getAttribute("idreserva"));
    $("#Editclientes").val("").trigger("change");
    $("#Editclientes_2").val("").trigger("change");
    $("#Editclientes_3").val("").trigger("change");
    $("#Editclientes_4").val("").trigger("change");
    $.ajax({
      type : 'POST',
      data : { op : 'obtenereserva',idreservas:e.parentNode.getAttribute("idreserva")},
      url : 'mysql/Reservas/index.php',
      beforeSend : function(){
      },
      success : function(response){
        
        response = JSON.parse(response.trim());
        localStorage.setItem("tiempo_extra",response[0].tiempo_extra);
        if (response[0].tipo_reserva =="Play Padel") {
          $("#EditarReserva").modal({backdrop: 'static', keyboard: false}, 'show');
          idcanchaedit = e.parentNode.parentNode.parentNode.firstChild.textContent.trim().replace(/\D/g,'');
          $("#canchas_select").val(idcanchaedit).trigger("change");
  
           localStorage.setItem("hora_fin_reserva",response[0].hora_fin);
           Editfecha_hoy.value =response[0].fecha_inicio;
          Edithora_inicio.value = response[0].hora_inicio;
          Edittipo_reserva.value = response[0].tipo_reserva;
          Editobservaciones.value = response[0].observacion;
          let tiempo_antes = horastringsinletras(response[0].hora_inicio);
          let vieja_hora = new Date();
          vieja_hora.setHours(tiempo_antes[0],tiempo_antes[1],0);
          let tiempo_dif = horastring(response[0].tiempo_dif);
          tiempo_dif = horastringsinletras(tiempo_dif);
          vieja_hora.setHours(vieja_hora.getHours()+parseInt(tiempo_dif[0]));
          let hora_antigua = response[0].hora_fin.split(":");
          vieja_hora.setMinutes(vieja_hora.getMinutes()+parseInt(tiempo_dif[1]));
          let recoger_hora = undefined;
          if (parseInt(hora_antigua[0])>11) {
            if (parseInt(hora_antigua[0])==12) {
              let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
              let hora_nueva = parseInt(horas[0])-12;
              Editduracion.value =hora_nueva +":"+horas[1] +" " +"pm";  
            }
            else{
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            let hora_nueva =horas[0];
            if (parseInt(hora_nueva)>11) {
              hora_nueva=  parseInt(horas[0])-12;
              
            }
            Editduracion.value =hora_nueva +":"+horas[1] +" " +"pm";  
            }
          }
          else{
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            Editduracion.value =horas[0] +":"+horas[1] +" " +"am"; 
          }
  
  
          Edithora_inicio.addEventListener("change",(e)=>{
          let tiempo_antes = Edithora_inicio.value.split(":");
          let vieja_hora = new Date();
          vieja_hora.setHours(tiempo_antes[0],tiempo_antes[1],0);
          let tiempo_dif = horastring(response[0].tiempo_dif);
          tiempo_dif = horastringsinletras(tiempo_dif);
          vieja_hora.setHours(vieja_hora.getHours()+parseInt(tiempo_dif[0]));
          let hora_antigua = Edithora_inicio.value.split(":");
          vieja_hora.setMinutes(vieja_hora.getMinutes()+parseInt(tiempo_dif[1]));
          recuperarhora = vieja_hora;
  
  
          
          if (parseInt(hora_antigua[0])>11) {
  
            if (parseInt(hora_antigua[0])==12) {
              let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
              Editduracion.value =hora(horas[0],horas[1]);  
            }
            else{
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            Editduracion.value =hora(horas[0],horas[1]);  
            }
          }
          else{
  
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            Editduracion.value =hora(horas[0],horas[1]); 
          }
  
          
      
  
  
          })
  
          vieja_hora.setMinutes(vieja_hora.getMinutes()+60);
          let cancha = document.getElementById("select2-canchas_select-container").textContent.match(/\d+/g)
          cancha = parseInt(cancha)
          $.ajax({
            type : 'POST',
            data : { op : 'Comprobartiempo',hora:vieja_hora.getHours()+":"+addZero(vieja_hora.getMinutes())+ ":"+addZero(vieja_hora.getSeconds()),fecha:response[0].fecha_inicio,cancha:cancha},
            url : 'mysql/Reservas/index.php',
            beforeSend : function(){
            },
  
            success : function(response){
              response = JSON.parse(response.trim());
              hora_encontrada = horastringsinletras( response[0].hora_inicio);
                hora_encontradafecha = new Date();
                hora_encontradafecha.setHours(hora_encontrada[0],hora_encontrada[1],hora_encontrada[2]);
                hora_encontradafecha.setMinutes(hora_encontradafecha.getMinutes()+30);
              if (response.length>0 && hora_encontradafecha.getHours()+":"+addZero(hora_encontradafecha.getMinutes())+ ":"+addZero(hora_encontradafecha.getSeconds())!=vieja_hora.getHours()+":"+addZero(vieja_hora.getMinutes())+ ":"+addZero(vieja_hora.getSeconds())) {
                // alert(horastringsinletras( response[0].hora_inicio))
                Editiempoextras.disabled = true;
              }
            },
            error : function(e){
              console.log(e.responseText);
            },
            complete : function(){
              $.unblockUI();
            }
          });    
     
  
  
  
          $.ajax({
            type : 'POST',
            data : { op : 'traerJugadoresReserva',idreservas:e.parentNode.getAttribute("idreserva") },
            url : 'mysql/Pagos/index.php',
            beforeSend : function(){
              $.blockUI({ message: '<h4> Cargando Jugadores...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
              $("#tabla").dataTable().fnClearTable();
            },
            success : function(response){
              response = JSON.parse(response.trim());
              for (var i = 0; i < response.length; i++) {
                  if (i == 0) {
                    $("#Editclientes").val(response[i].idjugador).trigger("change");
                    editcliente = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                     
                  }
                  if (i == 1) {
                    $("#Editclientes_2").val(response[i].idjugador).trigger("change");
                    editcliente2 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                 
                  }
                  if (i == 2) {
                    $("#Editclientes_3").val(response[i].idjugador).trigger("change");
                    editcliente3 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                  
                  }
                  if (i == 3) {
                    editcliente4 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                    $("#Editclientes_4").val(response[i].idjugador).trigger("change");
                   
                  }
              }
            },
            error : function(e){
              console.log(e.responseText);
            },
            complete : function(){
              $.unblockUI();
            }
          });
  
                  
        }
  
     
        
        else if(response[0].tipo_reserva =="Play Padel Multiple"){
           canchas_list = $('[id^="canchas_list"]');
            for (let x = 0; x < canchas_list.length; x++) {
                      canchas_list[x].addEventListener("change",consultareservas);
                    
              
            }
          let tiempo_antes = horastring(response[0].hora_inicio);
        
          duracion_text.textContent ="Hora fin";
          duracion_contenedor.innerHTML =`<input name="" style="font-size: small;" id="EditduracionMultiple" type="time" required="" class="form-control">`;
          hora_inicio_multiple.type = "time"
          hora_inicio_multiple.removeAttribute("disabled")
          actualizarmultiple =1;
          canchas_list = $('[id^="canchas_list"]');
          hora_inicio_multiple.addEventListener("change",(e)=>{
            let hora_ = Edithora_inicio.value.split(":");
            if(hora_[1] !="30" && hora_[1] !="00"){
              hora_inicio_multiple.value = "";
              toastr.warning("SOLO SE PERMITEN INTERVALOS DE 3OM O 1H")
            }
            })
            EditduracionMultiple.addEventListener("change",(e)=>{
              let hora_ = Edithora_inicio.value.split(":");
              if(hora_[1] !="30" && hora_[1] !="00"){
                EditduracionMultiple.value = "";
                toastr.warning("SOLO SE PERMITEN INTERVALOS DE 3OM O 1H")
              }
              })
          // if (parseInt(hora_antigua[0])>11) {
          //   if (parseInt(hora_antigua[0])==12) {
          //     let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
          //     let hora_nueva = parseInt(horas[0])-12;
          //     EditduracionMultiple.value =hora_nueva +":"+horas[1] +" " +"pm";  
          //   }
          //   else{
          //   let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
          //   let hora_nueva =horas[0];
          //   if (parseInt(hora_nueva)>11) {
          //     hora_nueva=  parseInt(horas[0])-12;
              
          //   }
          //   EditduracionMultiple.value =hora_nueva +":"+horas[1] +" " +"pm";  
          //   }
          // }
          // else{
          //   let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
          //   EditduracionMultiple.value =horas[0] +":"+horas[1] +" " +"am"; 
          // }
  
          EditduracionMultiple.value = response[0].hora_fin;
          btnReservaMultiple.value = "Actualizar Reserva"
  
          for (let i = 0; i < canchas_list.length; i++) {
            canchas_list[i].removeAttribute("checked")        
          }
          $("#modalReservaMultiple").modal({backdrop: 'static', keyboard: false}, 'show');
          hora_inicio_multiple.value = response[0].hora_inicio;
          tipo_reserva_multiple.value = response[0].tipo_reserva;
          observaciones_multiple.value = response[0].observacion;
          descripcion_multiple.value = response[0].descripcion;
            // alert(e.parentNode.getAttribute("idreserva"))
            $.ajax({
              type : 'POST',
              data : { op : 'traerJugadoresReserva',idreservas:e.parentNode.getAttribute("idreserva") },
              url : 'mysql/Pagos/index.php',
              beforeSend : function(){
                $.blockUI({ message: '<h4> Cargando Jugadores...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
              },
              success : function(response){
                response = JSON.parse(response.trim());
                for (var i = 0; i < response.length; i++) {
                    if (i == 0) {
                      $("#clientes_multiple").val(response[i].idjugador).trigger("change");
                      // editcliente = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                        clientes_1_id_multiple = {"id":response[i].idjugador ,"tipo":response[i].tipo};       
                    }
                    if (i == 1) {
                      $("#clientes_2_multiple").val(response[i].idjugador).trigger("change");
                      // editcliente2 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                      clientes_2_id_multiple = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                    }
                    if (i == 2) {
                      $("#clientes_3_multiple").val(response[i].idjugador).trigger("change");
                      // editcliente3 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                      clientes_3_id_multiple= {"id":response[i].idjugador ,"tipo":response[i].tipo};                
                      }
                    if (i == 3) {
                      // editcliente4 = {"id":response[i].idjugador ,"tipo":response[i].tipo};
                      $("#clientes_4_multiple").val(response[i].idjugador).trigger("change");
                      clientes_4_id_multiple= {"id":response[i].idjugador ,"tipo":response[i].tipo};
  
                    }
                }
              },
              error : function(e){
                console.log(e.responseText);
              },
              complete : function(){
                $.unblockUI();
  
              }
            });
            ideditareservamultiple =e.parentNode.getAttribute("idreserva");
            $.ajax({
              type : 'POST',
              data : { op : 'obtenerReservasMultiples',idreservas:e.parentNode.getAttribute("idreserva") },
              url : 'mysql/Reservas/index.php',
              beforeSend : function(){
                $.blockUI({ message: '<h4> Cargando Jugadores...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
              },
              success : function(response){
                response = JSON.parse(response.trim());
                for (var i = 0; i < response.length; i++) {
                  for (let d = 0; d < fechas_semana.length; d++) {
                          if (fechas_semana[d].fecha== response[i].fecha_inicio) {
                            dias_semana.forEach((c)=>{
                                  c.checked=false
                                  if (c.value<hoy.getDay())
                                  { 
                                    c.disabled = true;
                                  }
                            })
                            dias_semana.forEach((c)=>{
                                if (c.nextElementSibling.textContent.trim() ==fechas_semana[d].dia ) {
                                    c.checked = true;                                
  
                                    $.ajax({
                                      type : 'POST',
                                      data : { op : 'obtenercanchasreservas',idreservas: response[i].idreservas},
                                      url : 'mysql/Reservas/index.php',
                                      beforeSend : function(){
                                        // $.blockUI({ message: '<h4> Cargando Jugadores...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                                      },
                                      success : function(response){
                                        response = JSON.parse(response.trim());
                                        canchas_list = $('[id^="canchas_list"]');
                                          for (let index = 0; index < response.length; index++) {
                                              for (let g = 0; g < canchas_list.length; g++) {
                                                if(canchas_list[g].nextElementSibling.textContent.trim() ==response[index].idcanchas +" - "+response[index].nombre ){
                                                           c.checked = true;
                                                          //  canchas_list[g].setAttribute("checked",true)
                                                           canchas_list[g].checked = true;
                                                           canchas_list[g].removeEventListener("change",consultareservas);
  
                                                           actualizar_evento_click.push({"dia":c.nextElementSibling.textContent.trim(),"indice":g,"visible":1}); 
                                                             
                                                  }
                                            }   
                                          }
                                       
  
                                      },
                                      error : function(e){
                                        console.log(e.responseText);
                                      },
                                      complete : function(){
                                        $.unblockUI();
  
                                      }
                                    });
  
  
                                }
                              })
                          }  
                  }
                }
              },
              error : function(e){
                console.log(e.responseText);
              },
              complete : function(){
                $.unblockUI();
              }
            });
           
        }
  
       ;
  
      },
      error : function(e){
        console.log(e.responseText);
      },
      complete : function(){
        encontrarjugadores_array = [];
  
        $.unblockUI();
      }
    });
  
  
  }
  function consultareservas(e){
    let squard = e.currentTarget;
    let cancha = squard.nextElementSibling.textContent.trim().replace(/\d+/g, '');
    cancha = cancha.replace("-","").trim();
    dias_semana.forEach((g)=>{
        if (g.checked == true) {
          fechas_semana.forEach((e)=>{
              if (g.nextElementSibling.textContent.trim() == e.dia) {
                $.ajax({
                  type : 'POST',
                  url : 'mysql/Reservas/index.php',
                  data : { op : 'Comprobaciondereserva',hora:hora_inicio_multiple.value,fecha:e.fecha,cancha:cancha},
                  beforeSend : function(){
                  },
                  success : function(response){
                    json = JSON.parse(response.trim());
                    if (json.length>0) {
                        squard.checked =false;
                        toastr.warning(`La cancha ${squard.nextElementSibling.textContent} ya contiene una reserva el dia ${e.dia}`)
                    }
                  },
                  error : function(e){
                    console.log(e.responseText);
                  },
                  complete : function(){
              
                  }
                });
              }
          })
        }
    })
    
  
  
  }
  cerrarmultiple.addEventListener("click",(e)=>{
    actualizarmultiple = 0;
  
  })
  
  
  function obtenerCanchasTorneosFecha(fecha){
      $.ajax({
          type : 'POST',
          data : { op : 'Torneosdeldia',fecha:fecha},
          url : 'mysql/Torneos/index.php',
          beforeSend : function(){
          
          },
          success : function(response){
              response = JSON.parse(response.trim());
            for (let d = 0; d < response.length; d++) {
              let nombre_torneo = response[d].nombre;
              let condition = 0;
              let conditon_union_squards = 0;
              let nombre = null;
              let cont = 0;
                      let squard_select = document.querySelectorAll(`#${response[d].cancha.replace(/ /g, "")}`);
                       for (let index = 0; index < squard_select.length; index++) {
                      let  hora_inicio  = horastring(response[d].horario_inicio);
                      let  hora_fin = horastring(response[d].horario_fin);
                      if (squard_select[index].textContent.trim() == hora_inicio) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("premium"))
                                  {
  
                                    squard_select[index].classList.remove("premium");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                                  condition =1;
  
                        }
                        if (squard_select[index].textContent.trim() == hora_fin) 
                        {
  
                                 
                                  condition =0;
                                 
                        }
                      
                      
                        if (condition ==1) 
                        {
                                  if (squard_select[index].classList.contains("disponible"))
                                  {
  
                                    squard_select[index].classList.remove("disponible");
                                  }
                                  if (squard_select[index].classList.contains("premium"))
                                  {
  
                                    squard_select[index].classList.remove("premium");
                                  }
                                  if (squard_select[index].classList.contains("cerrado")) 
                                  {
                                    squard_select[index].classList.remove("cerrado");
                                  }
                                  if (squard_select[index].classList.contains("descuento")) 
                                  {
                                    squard_select[index].classList.remove("descuento");
                                  }
                                  if (squard_select[index].classList.contains("tpasado")) 
                                  {
                                    squard_select[index].classList.remove("tpasado");
                                  }
                                  if (!squard_select[index].classList.contains("reserva_internet") && !squard_select[index].classList.contains("clinica")) {
                                    squard_select[index].classList.add("torneo");
                                    squard_select[index].title =response[d].nombre;
                                    nombre =response[d].nombre;
                                  }
                                  else{
                                    break;

                                  }
                              cont =1;
                        }
                       
                           
                        
                       }
                       if (cont>0) {
                        cont = 0;
                        let tamaño_squard = 0;
                        let pos1 = Node;
                        let text ="";
                      let title = document.querySelectorAll(`.torneo[title="${nombre}"]`); // Lila
                        for (let c = 0; c < title.length; c++) {
                          let fecha_js = hoy;
                          let mes =fecha_js.getMonth() +1;
                          if (conditon_union_squards== 0) {
                              pos1 = title[c];
                              pos1.classList.remove("horas_dibujadas");
                              text += title[c].textContent.trim() + " - ";
                              text += title[title.length-1].nextElementSibling.getAttribute("hora_dibujo");
                              conditon_union_squards = 1;
                            }
                            else{
                              title[c].remove();
                            }
                            tamaño_squard += 50;
                          
                      }
                      let torneo_info = document.createElement("div");
                    
                      $.ajax({
                        type : 'POST',
                        data : { op : 'InscritosTorneo',idtorneos:response[d].idtorneos },
                        url : 'mysql/Torneos/index.php',
                        beforeSend : function(){
                          $.blockUI();
                        },
                        success : function(response){
                          response = JSON.parse(response.trim());
                          torneo_info.innerHTML =`
                          <div class="row justify-content-start">
                          <p class="title_info">Tipo   :</p> <span>&nbspTorneo</span> <br>
                          </div>
                          <div class="row justify-content-start">
                          <p class="title_info">Nombre :</p><span>&nbsp${nombre_torneo}</span> <br>
                          </div>
                          <div class="row justify-content-start">
                          <p class="title_info">Horario :</p> <span>&nbsp${text}</span> <br>
                          </div>
                         
                          <div class="row justify-content-start">
                          <p class="title_info">Inscritos :</p><span>&nbsp${response[0].pagos_torneos}</span> <br>
                          </div>
                          
                          `;
                     
                        },
                        error : function(e){
                          console.log(e.responseText);
                        },
                        complete : function(){
                          $.unblockUI();
                       
                        }
                      });
  
  
                      canchas = document.getElementById("canchas");
                      let cords_canchas = canchas.getBoundingClientRect();
                      let cords = pos1.getBoundingClientRect();
  
                   
                      if (cords_canchas.right<cords.right +425 && cords_canchas.bottom >cords.bottom+425) {
                        torneo_info.classList.add("info_i");
  
                      }
                      else if( cords_canchas.bottom >cords.bottom+425){
                        torneo_info.classList.add("info_d");
  
                      }
                      else if(cords_canchas.bottom <cords.bottom+425 && cords_canchas.right<cords.right +425){
                        torneo_info.classList.add("info_i_b");
                        
                      }
                      else{
                        torneo_info.classList.add("info_d_b");
  
                      }
                      pos1.style.height = tamaño_squard +"px";
                      pos1.classList.add("acomodarsquard");
                      pos1.textContent = text;
                      pos1.title = ``;
                      pos1.appendChild(torneo_info);
                      pos1.setAttribute("hora",`${text}`)
                      pos1.removeEventListener("click", clickHandler);
                      }
              
            }
        // }
              for (var i = 0; i < response.length; i++) {
      
              }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
              obtenerReservas()
          }
      })
  }
  
  
  function horastring(hora_){
   let fecha = new Date();
     hora_ = hora_.split(":");
    fecha.setHours(hora_[0],hora_[1],hora_[2],0);
    hora_ = hora(fecha.getHours(),fecha.getMinutes());
    return hora_;
  }
  
   function obtenerCanchasDia(dia){
      $.ajax({
          type : 'POST',
          data : { op : 'obtenerCanchasDia',dia:dia},
          url : 'mysql/Canchas/index.php',
          beforeSend : function(){
              $.blockUI({ message: '<h4> Cargando canchas...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
          
          },
          success : function(response){
        array_canchas = [];
              response = JSON.parse(response.trim());
        let hora_actual = new Date();
        let tiempo_pasado =  Tiempopasado(hora_actual.getHours()+ ":"+hora_actual.getMinutes() + ":00",hoy)
              for (var i = 0; i < response.length; i++) {
                      
          if(response[i].status == "1"){
              array_canchas.push({"cancha":response[i].idcanchas + " - "+ response[i].nombre,"id":response[i].idcanchas});
          let cancha = document.createElement("div");
          cancha.classList.add("text-center");
          let div_cancha = document.createElement("div");
          div_cancha.classList.add("canchas_title");
          let empezar_dibujo  = horainicio(response[i].hora_inicio);
          var fecha = new Date();
          let timemore = 0;
          div_cancha.textContent =  `${response[i].idcanchas} - ${response[i].nombre}`;
          cancha.appendChild(div_cancha)
          let hora_cierre = horacerrar(response[i].hora_fin);
          hora_cierre = total_mediahora -hora_cierre;
          let horas = document.querySelectorAll("#horas_izquierda>.text-start");
              for (let c = 0; c < total_mediahora; c++) {
                let squard = document.createElement("div");
                squard.classList.add("text-center");
                squard.classList.add("horas_dibujadas");
                squard.style.border= "1px solid #fff";
                fecha.setHours(6,timemore,0,0);
                squard.id = response[i].nombre.replace(/ /g, "");
                intervalo=hora(fecha.getHours(),fecha.getMinutes());
                squard.setAttribute("hora_dibujo",intervalo);
                 if (c<empezar_dibujo || c>=hora_cierre) {
                  squard.classList.add("cerrado");
                  squard.textContent=`${intervalo}`;
                  squard.title=`Cerrado`;
                  squard.setAttribute("row",c);
                 }  
                 else if (c<=tiempo_pasado) {
                  squard.classList.add("tpasado");
                  squard.textContent=`${intervalo}`;
                  squard.title=`Tiempo pasado`;
                }
                 else if (c >= empezar_dibujo && c<hora_cierre) {
                  squard.classList.add("disponible");
                  squard.title ="disponible";
                  squard.textContent=`${intervalo}`;
                  squard.setAttribute("hora",intervalo);
                  squard.addEventListener("click",clickHandler);
                  squard.setAttribute("row",c);
             
                }  
                cancha.appendChild(squard);
                timemore += 30;
  
              }
             canchas.appendChild(cancha);
              }
          }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
        buscar_bd.forEach((e)=>{
          if (dia ==e.numero) {
            obtenerTorneos(e.dia)
          
            return;
          }
        })
  
  
          }
      })
  }
  function horainicio(hora) 
   {
  
    hora_1 = returnhora(hora);
   let hora_2 = new Date();
   hora_2.setHours(6);
   hora_2.setMinutes(0);
   hora_2.setSeconds(0);
    var diff =(hora_1.getTime() - hora_2.getTime()) / 1000;
    diff /= (60 * 60);
    cantidad_cuadros  = Math.abs(Math.round(diff));
    cantidad_cuadros = cantidad_cuadros * 2;
    return cantidad_cuadros;
  
  }
  function horacerrar(hora){
    hora_1 = returnhora(hora);
   let hora_2 = new Date();
   hora_2.setHours(24);
   hora_2.setMinutes(0);
   hora_2.setSeconds(0);
    var diff =(hora_1.getTime() - hora_2.getTime()) / 1000;
    diff /= (60 * 60);
    cantidad_cuadros  = Math.abs(Math.round(diff));
    cantidad_cuadros = cantidad_cuadros * 2;
    return cantidad_cuadros;
  
  }
  function returnhora(hora_){
    let hora =hora_.split(":");
    hora_ = new Date();
    hora_.setHours(hora[0]);
    hora_.setMinutes(hora[1]);
    hora_.setSeconds(hora[2]);
    return hora_;
  }
  
  function createintervalosizquierda(){
  
    var fecha = new Date();
    let timemore = 0;
    for (let i = 0; i < total_mediahora; i++) {
      fecha.setHours(6,timemore,0,0);
      timemore += 30;
  
      let time = document.createElement("div");
      time.classList.add("text-start")
      time.style.border = "none";
      time.style.border= "1px solid #fff";
      time.classList.add("horas_dibujadas");
  
      intervalo=hora(fecha.getHours(),fecha.getMinutes());
      time.textContent=`${intervalo}`;
      horas_izquierda.appendChild(time);
    }
   
  
  }
  function createintervalosderecha(){
    var fecha = new Date();
    let timemore = 0;
    for (let i = 0; i < total_mediahora; i++) {
      fecha.setHours(6,timemore,0,0);
      timemore += 30;
  
      let time = document.createElement("div");
      time.classList.add("text-start")
      time.style.border = "none";
      time.style.border= "1px solid #fff";
      time.classList.add("horas_dibujadas");
  
      intervalo=hora(fecha.getHours(),fecha.getMinutes());
      time.textContent=`${intervalo}`;
      horas_derecha.appendChild(time);
    }
   
  
  }
  function hora(hora,minuts) {
    let hora_ = undefined;
    let minutes = minuts == 0 ? "00" : minuts;
    if (hora > 12) {
      hora = hora - 12;
      hora_=   String(hora) + ":" + minutes + " pm";
    } else if (hora) {
      hora_=  String(hora) + ":" + minutes + " am";
    }
     if (hora == 12) {
      hora_= "12"  +":" + minutes + " pm";
    }
     if (hora == 0) {
      hora_= "12"  +":" + minutes + " am";
    }
    return hora_;
  }
  
  function hora_definida(hora) {
    let hora_ = undefined;
    if (hora > 12) {
      hora = hora - 12;
      hora_=   String(hora) + " pm";
    } else if (hora) {
      hora_=  String(hora) + " am";
    }
     if (hora == 12) {
      hora_= "12"  +" pm";
    }
     if (hora == 0) {
      hora_= "12"  + " am";
    }
    return hora_;
  }
  function obtenerdiasemana(){
    $.ajax({
          type : 'POST',
          data : { op : 'diasdelasemana' },
          url : 'mysql/Reservas/index.php',
          beforeSend : function(){
          
          },
          success : function(response){
        let nueva_fecha = undefined;
              response = JSON.parse(response.trim()); 
        fechas_semana.push({ "dia":"Lunes", "fecha": response[0].lunes,"numero":"1"});
        if (response[0].martes==null) {
             nueva_fecha = new Date(response[0].lunes);
            nueva_fecha.setDate(nueva_fecha.getDate()+1);
            nueva_fecha.setMonth(nueva_fecha.getMonth()+1)
            fechas_semana.push({ "dia":"Martes", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"2"})
      
        }
        else
        {
           nueva_fecha = new Date(response[0].lunes);
           nueva_fecha.setDate(nueva_fecha.getDate()+1);
           nueva_fecha.setMonth(nueva_fecha.getMonth()+1)
          fechas_semana.push({ "dia":"Martes", "fecha": response[0].martes,"numero":"2"})
        
        }
        if (response[0].miercoles==null) {
          nueva_fecha.setDate(nueva_fecha.getDate()+1);
          fechas_semana.push({ "dia":"Miércoles", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"3"})
  
       }
       else
       {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
  
        fechas_semana.push({ "dia":"Miércoles",  "fecha":response[0].miercoles,"numero":"3"})
      
       }
      if (response[0].jueves==null) {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
        fechas_semana.push({ "dia":"Jueves", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"4"})
  
      }
      else
      {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
  
        fechas_semana.push({ "dia":"Jueves", "fecha": response[0].jueves,"numero":"4"})
     
      }
      if (response[0].viernes==null) {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
        fechas_semana.push({ "dia":"Viernes", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"5"})
  
      }
      else
      {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
  
        fechas_semana.push({ "dia":"Viernes", "fecha": response[0].viernes,"numero":"5"})
        
      }
      if (response[0].sabado==null) {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
        fechas_semana.push({ "dia":"Sábado", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"6"})
  
  
      }
      else
      {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
  
        fechas_semana.push({ "dia":"Sábado",  "fecha":response[0].sabado,"numero":"6"})
      }
      if (response[0].domingo==null) {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
        fechas_semana.push({ "dia":"Domingo", "fecha": nueva_fecha.getFullYear()+"-"+addZero(nueva_fecha.getMonth())+ "-"+addZero(nueva_fecha.getDate()),"numero":"7"})
      }
      else
      {
        nueva_fecha.setDate(nueva_fecha.getDate()+1);
  
        fechas_semana.push({ "dia":"Domingo", "fecha": response[0].domingo,"numero":"7"})
  
      }
       
          
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
        Reservasdelasemanahorainiciales()
        tiemposmuertos()
  
          }
      });
  }
  
  
  
  menos_dia.addEventListener("click",(e)=>{
   hoy.setDate(hoy.getDate()-1)
   dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
   dia_hoy.textContent =  " "+dias[dia]  + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
   dibujar_contenedores_buttons(dia);
  
  })
  mas_dia.addEventListener("click",(e)=>{
    hoy.setDate(hoy.getDate()+1)
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  })
  today.addEventListener("click",(e)=>{
    hoy = new Date();
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  })
  ayer.addEventListener("click",(e)=>{
    hoy = new Date();
    hoy.setDate(hoy.getDate()-1)
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  })
  manana.addEventListener("click",(e)=>{
    hoy = new Date();
    hoy.setDate(hoy.getDate()+1)
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  })
  pasado_manana.addEventListener("click",(e)=>{
    hoy = new Date();
    hoy.setDate(hoy.getDate()+2)
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  })
  dia_seleccionado_buscado.addEventListener("change",(e)=>{
    hoy = new Date(dia_seleccionado_buscado.value);
    hoy.setDate(hoy.getDate()+1)
    dia = hoy.getDay()-1;;
    if (dia <0) {
      dia = dias.length-1;
    }
    dia_seleccionado_buscado.setAttribute("value",`${hoy.getFullYear()}-`+meshoy(hoy.getMonth()+1)+`-${addZero(hoy.getDate())}`);
    dia_hoy.textContent =  " "+ dias[dia] + "," +hoy.getDate() + " " +meses[hoy.getMonth()] + " de " + hoy.getFullYear();
    dibujar_contenedores_buttons(dia);
  
  });
  
   async function reserva(){
  
  
    $("#modalReservas").modal('hide')
    cancha_reserva.value = localStorage.getItem("cancha");
    hora_inicio.value = localStorage.getItem("hora");
  
              fecha_hoy.value = dias[hoy.getDay()] + " "+ hoy.getDate() + " "+ meses[hoy.getMonth()] +" "+ hoy.getFullYear();
              let bool= tiempovalidoreserva(localStorage.getItem("row"),localStorage.getItem("cancha_id"))
             if (bool == true) {
                observaciones.value ="";
               $("#modalReserva").modal({backdrop: 'static', keyboard: false}, 'show');
             }
             else{
               toastr.warning("NO SE PUEDE ASIGNAR DEBIDO A QUE NO SE TIENE EL MÍNIMO TIEMPO DE 90 MINUTOS PARA SU ASIGNACIÓN")
           
             }         
  
  }
  
  $("#clientes").on("select2:select", function (e) {
    clientes_1_id = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes").val("").trigger("change");
    }
    if (e.params.data.title =="Socio") {
      accesos_socios.forEach((d)=>{
          if (d.idsocios ==e.params.data.id ) {
                      if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
                            toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                            $("#clientes").val("").trigger("change");
  
                      }
          }
      })
    }
  })
  $("#clientes_2").on("select2:select", function (e) {
    clientes_2_id = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_2").val("").trigger("change");
    }
    if (e.params.data.title =="Socio") {
      accesos_socios.forEach((d)=>{
          if (d.idsocios ==e.params.data.id ) {
            if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
              toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                        $("#clientes_2").val("").trigger("change");
                  }
          }
      })
    }
  })
  $("#clientes_3").on("select2:select", function (e) {
    clientes_3_id= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_3").val("").trigger("change");
    }
    if (e.params.data.title =="Socio") {
      accesos_socios.forEach((d)=>{
          if (d.idsocios ==e.params.data.id ) {
            if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
              toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                        $("#clientes_3").val("").trigger("change");
                      }
          }
      })
    }
  })
  $("#clientes_4").on("select2:select", function (e) {
    clientes_4_id= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_4").val("").trigger("change");
    }
    if (e.params.data.title =="Socio") {
      accesos_socios.forEach((d)=>{
          if (d.idsocios ==e.params.data.id ) {
            if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
              toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                        $("#clientes_4").val("").trigger("change");
                      }
          }
      })
    }
  })
  $("#clientes_multiple").on("select2:select", function (e) {
    clientes_1_id_multiple = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_multiple").val("").trigger("change");
    }
  })
  $("#clientes_2_multiple").on("select2:select", function (e) {
    clientes_2_id_multiple = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_2_multiple").val("").trigger("change");
    }
  })
  $("#clientes_3_multiple").on("select2:select", function (e) {
    clientes_3_id_multiple= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
  
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_3_multiple").val("").trigger("change");
    }
  })
  // $("#clientes_4_multiple").on("select2:selecting", function (e) {
    
  // })
  $("#clientes_4_multiple").on("select2:select", function (e) {
    clientes_4_id_multiple= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
  
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#clientes_4_multiple").val("").trigger("change");
    }
  })
  
  
  
  $("#btnReserva").click(function(event){
      if (document.getElementById("FormReserva").checkValidity()) {
          event.preventDefault();
      
      
      let jugadores = [clientes_1_id];
      if (clientes_2_id!=undefined) {
        jugadores.push(clientes_2_id);
      }
      if (clientes_3_id!=undefined) {
        jugadores.push(clientes_3_id);
  
      }
      if (clientes_4_id!=undefined) {
        jugadores.push(clientes_4_id);
  
      }
  
      const data = new FormData();
      // array_canchas.push({"cancha":response[i].idcanchas + " - "+ response[i].nombre,"id":response[i].idcanchas});
      idcancha = cancha_reserva.value;
      idcancha = idcancha.split(" ");
  
       let hoy_mismo = hoy;
      dia = hoy_mismo.getDate();
      mes = hoy_mismo.getMonth() +1;
      anio= hoy_mismo.getFullYear();
      fecha_actual = String(anio+"-"+mes+"-"+dia);
      let duracion = document.querySelectorAll("#duracion");
      data.append("idcancha",idcancha[0]);
      data.append("fecha_inicio",fecha_actual);
      data.append("tipo_reserva",$("#tipo_reserva").val());
  
      let condition = 0;
      corregidor_hora.forEach((e)=>{
        if (e.hora_obtencion.trim() == hora_inicio.value.trim() && condition ==0) {
          data.append("hora_inicio",e.hora_extraida);
          let tiempo_split = e.hora_extraida.split(":");
          hoy_mismo.setHours(tiempo_split[0],tiempo_split[1],0,0);
          condition =1;
        }
      })
      let tiempo = 0;
      duracion.forEach((e)=>{
        if (e.checked) {
          tiempo = 1;
            if (e.value == "90 min") {
  
              hoy_mismo.setMinutes(hoy_mismo.getMinutes()+90);
            }
            if (e.value == "120 min") {
              hoy_mismo.setMinutes(hoy_mismo.getMinutes()+120);
            }
            if (hoy_mismo.getMinutes()=="0") {
              data.append("hora_fin",hoy_mismo.getHours() +":"+"00"+ ":" +"00");
  
            }
            else
             {
              data.append("hora_fin",hoy_mismo.getHours() +":"+hoy_mismo.getMinutes()+ ":" +"00");
             }
            }
      });
      if (tiempo == 0) {
        return toastr.warning("NO SE ASIGNADO LA DURACION");
      }
      data.append("cliente",$("#clientes").val());
      data.append("observaciones",$("#observaciones").val());
      data.append("jugadores",JSON.stringify(jugadores));
      jugadores.pop();
        data.append("op","InsertarReserva");
  
          $.ajax({
              type: "POST",
              enctype: 'multipart/form-data',
              url: "mysql/Reservas/index.php",
              data: data,
              processData: false,
              contentType: false,
              cache: false,
              timeout: 600000,
              beforeSend : function(){
                  $.blockUI({ message: '<h4> Realizando petición...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                  $("#modalReserva").hide();
              },
              success: function (data) {
                  toastr.success("RESERVA REGISTRADA");
                  
                  $("#modalReserva").modal("hide");
                    
              
              
                },
              error: function (e) {
                  toastr.error(e.responseText);
          $("#modalReserva").modal({backdrop: 'static', keyboard: false}, 'show');
              },
              complete : function(){
          reserva_canchas = [];
         // hoy = new Date();
        dia = hoy.getDay()-1;
          obtenerReservasxCliente()
        dibujar_contenedores();
        tiemposmuertos();
        Reservasdelasemanahorainiciales()
        clientes_1_id = undefined;
        clientes_2_id = undefined;
        clientes_3_id = undefined;
        clientes_4_id = undefined;
                  $.unblockUI();
              }
          });
      }
  });
  
  
  let editcliente = undefined;
  let editcliente2 = undefined;
  let editcliente3 = undefined;
  let editcliente4 = undefined;
  
  $("#Editclientes").on("select2:select", function (e) {
    editcliente = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#Editclientes").val("").trigger("change");
    }
    accesos_socios.forEach((d)=>{
      if (d.idsocios ==e.params.data.id ) {
        if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
          toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                    $("#Editclientes").val("").trigger("change");
              }
      }
  })
  })
  $("#Editclientes_2").on("select2:select", function (e) {
    editcliente2 = {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#Editclientes_2").val("").trigger("change");
  
    }
    accesos_socios.forEach((d)=>{
      if (d.idsocios ==e.params.data.id ) {
        if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
          toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                    $("#Editclientes_2").val("").trigger("change");
              }
      }
  })
  })
  $("#Editclientes_3").on("select2:select", function (e) {
    editcliente3= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#Editclientes_3").val("").trigger("change");
  
    }
    accesos_socios.forEach((d)=>{
      if (d.idsocios ==e.params.data.id ) {
        if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
          toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                    $("#Editclientes_3").val("").trigger("change");
              }
      }
  })
  })
  $("#Editclientes_4").on("select2:select", function (e) {
    editcliente4= {"id":e.params.data.id ,"tipo":e.params.data.title};
    let condicion = maxreservasclientes.filter(item => item.idpersona == e.params.data.id && item.tipo ==e.params.data.title)
    if (condicion.length>0) {
      toastr.warning("NO SE PUEDE SUPERA EL MAXIMO DE RESERVAS POR CLIENTE")
      $("#Editclientes_4").val("").trigger("change");
  
    }
    accesos_socios.forEach((d)=>{
      if (d.idsocios ==e.params.data.id ) {
        if (parseInt(d.accesos_usados) >= parseInt( d.accesos_mes)) {
          toastr.warning("SE TIENE EL LIMITE DE ACCESOS USADOS POR LA MEMBRESIA COMPRADA");
                    $("#Editclientes_4").val("").trigger("change");
              }
      }
  })
  })
  

  hora_inicio_multiple.addEventListener("change",(e)=>{
    let tiempo_antes = Edithora_inicio.value.split(":");
          let vieja_hora = new Date();
          vieja_hora.setHours(tiempo_antes[0],tiempo_antes[1],0);
          let tiempo_dif = horastring(response[0].tiempo_dif);
          tiempo_dif = horastringsinletras(tiempo_dif);
          vieja_hora.setHours(vieja_hora.getHours()+parseInt(tiempo_dif[0]));
          let hora_antigua = Edithora_inicio.value.split(":");
          vieja_hora.setMinutes(vieja_hora.getMinutes()+parseInt(tiempo_dif[1]));
          recuperarhora = vieja_hora;
  
  
          
          if (parseInt(hora_antigua[0])>11) {
  
            if (parseInt(hora_antigua[0])==12) {
              let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
              Editduracion.value =hora(horas[0],horas[1]);  
            }
            else{
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            Editduracion.value =hora(horas[0],horas[1]);  
            }
          }
          else{
  
            let horas = horastringsinletras(vieja_hora.getHours()+ ":"+addZero(vieja_hora.getMinutes()));
            Editduracion.value =hora(horas[0],horas[1]); 
          }
    // let cancha = document.getElementById("select2-canchas_select-container").textContent.replace(/\d+/g, '');
    // cancha = cancha.replace("-","").trimStart();
    // $.ajax({
    //   type : 'POST',
    //   data : { op : 'Comprobartiempo',hora:hora_inicio_multiple.value,fecha:Editfecha_hoy.value,cancha:cancha},
    //   url : 'mysql/Reservas/index.php',
    //   beforeSend : function(){
        
    //   },
    //   success : function(response){
    //     response = JSON.parse(response.trim());
    //     if (response.length>0) {
    //       Editfecha_hoy.value ="";
    //       toastr.warning("SELECCIONE OTRA FECHA O CANCHA YA QUE SE EMPALMAN LOS HORARIOS")
    //     }
    //     let hora_ = horastringsinletras(Editduracion.value);
    //     let hora_intervalo = Editduracion.value.split(" ");
    //     hora_ = formato24h(hora_[0],":"+hora_[1],":00",hora_intervalo);
    //     let vieja_hora = new Date();
    //     hora_ = hora_.split(":");
    //     vieja_hora.setHours(addZero(hora_[0]),hora_[1],hora_[2]);
    //       // alert(vieja_hora)
    //           cancha = document.getElementById("select2-canchas_select-container").textContent.replace(/\d+/g, '');
    //           cancha = cancha.replace("-","").trimStart();
    //           $.ajax({
    //             type : 'POST',
    //             data : { op : 'Comprobartiempo',hora:vieja_hora.getHours()+":"+addZero(vieja_hora.getMinutes())+ ":"+addZero(vieja_hora.getSeconds()),fecha:Editfecha_hoy.value,cancha:cancha},
    //             url : 'mysql/Reservas/index.php',
    //             beforeSend : function(){
    //             },
    //             success : function(response){
    //               response = JSON.parse(response.trim());
    //               if (response.length>0 && response[0].hora_inicio!=vieja_hora.getHours()+":"+addZero(vieja_hora.getMinutes())+ ":"+addZero(vieja_hora.getSeconds())) {
    //                 Editiempoextras.disabled = true;
    //               }
    //             },
    //             error : function(e){
    //               console.log(e.responseText);
    //             },
    //             complete : function(){
    //               $.unblockUI();
    //             }
    //           });    
      
   
    //   },
    //   error : function(e){
    //     console.log(e.responseText);
    //   },
    //   complete : function(){
    //     $.unblockUI();
    //   }
    // });    
  
  })
  
  
  
  $("#EditbtnReserva").click(function(event){
      if (document.getElementById("EditFormReserva").checkValidity()) {
          event.preventDefault();
      let pasacondicion = 0;
      let jugadores = [editcliente];
      if (editcliente2!=undefined) {
        jugadores.push(editcliente2);
      }
      if (editcliente3!=undefined) {
        jugadores.push(editcliente3);
  
      }
      if (editcliente4!=undefined) {
        jugadores.push(editcliente4);
  
      }
          const data = new FormData();
      idcancha = cancha_reserva.value;
      idcancha = idcancha.split(" ");
      data.append("idreserva",localStorage.getItem("idreserva"));
      data.append("fecha_inicio",Editfecha_hoy.value);
      data.append("observacion",Editobservaciones.value);
     
      data.append("jugadores",JSON.stringify(jugadores));
  
      // Editiempoextras.checked=false;
      data.append("cancha",JSON.stringify([{"cancha":canchas_select.value}]));
      
      data.append("tipo_reserva",$("#tipo_reserva").val());
      let conv24h =$("#Editduracion").val().split(":");
      str = conv24h[1].replace(/[^0-9]+/g, "");
     let intervalo = conv24h[1].trim().replace(/[0-9]+/g, "");
     let horat= undefined;
     if (intervalo.trim()=="pm") {
      horat= parseInt(conv24h[0])+12;
     }
     else{
      horat =conv24h[0];
     }
    
     ft24 = `${horat}`+":"+str+":00",intervalo;
      data.append("hora_inicio",Edithora_inicio.value)
      data.append("hora_fin",ft24);
  
      if (Editiempoextras.checked) {
        let fecha_hora = new Date();
        fecha_hora.setHours(horat,str,"00");
        fecha_hora.setMinutes(fecha_hora.getMinutes()+30);    
        data.append("Editiempoextra",addZero(fecha_hora.getHours()) + ":"+addZero(fecha_hora.getMinutes())+":"+fecha_hora.getSeconds());
      }  
     
      
        data.append("op","ActualizarReserva");
        data.append("idcancha",canchas_select.value);

        let hora_inicio = data.get("hora_inicio").split(":");
        let hora_ini =new Date() ;
        let horario_finn = undefined;
        if (Editiempoextras.checked) {
            horario_finn = data.get("Editiempoextra").split(":");
        }
        else{
            horario_finn = data.get("hora_fin").split(":");

        }
        let hora_fin = new Date();
       
        hora_fin.setHours(horario_finn[0],horario_finn[1],0);
        hora_fin.setMinutes(hora_fin.getMinutes()-30,0);
        hora_ini.setHours(hora_inicio[0],hora_inicio[1],0);
        hora_ini.setMinutes(hora_ini.getMinutes()+30,0);
        $.ajax({
            type: "POST",
            data : { op : 'comparartiempos',idreserva:localStorage.getItem("idreserva"),hora:addZero(hora_ini.getHours())+":"+addZero(hora_ini.getMinutes())+":"+addZero(hora_ini.getSeconds()), hora2:addZero(hora_fin.getHours())+":"+addZero(hora_fin.getMinutes())+":"+addZero(hora_fin.getSeconds()),fecha:Editfecha_hoy.value,cancha:document.getElementById("canchas_select").value},
            url : 'mysql/Reservas/index.php',
          
            beforeSend: function () {
              $.blockUI({
              });
      
            },
            success: function (data) {
                response = JSON.parse(data.trim());
                let cont = 0;
                for (let i = 0; i < response.length; i++) {
                        if (response[i].id!=null) {
                            if (cont ==0) {
                                toastr.warning(`Ya esta reservada en esos horarios por ${response[i].tipo}`)
                                cont =1; 
                                pasacondicion = 1;                               
                            }
                        }

                }
            },
            error: function (e) {
            
            },
            complete: function () {
              $.unblockUI();
              if (pasacondicion == 0) {
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "mysql/Reservas/index.php",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    beforeSend : function(){
                        $.blockUI({ message: '<h4> Realizando petición...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                        $("#EditarReserva").hide();
                    },
                    success: function (data) {
                        toastr.success("RESERVA ACTUALIZADA");
                        
                        $("#EditarReserva").modal("hide");
                    },
                    error: function (e) {
                        toastr.error(e.responseText);
                $("#EditarReserva").modal({backdrop: 'static', keyboard: false}, 'show');
                    },
                    complete : function(){
                reserva_canchas = [];
              //  hoy = new Date();
             // dia = hoy.getDay()-1;
                obtenerReservasxCliente()
                editcliente = undefined;
                editcliente2 = undefined; 
                editcliente3 = undefined;
                editcliente4 = undefined;
              dibujar_contenedores();
              tiemposmuertos();
              Reservasdelasemanahorainiciales()
              Editiempoextras.checked=false;
                        $.unblockUI();
                    }
                });
            }
            },
          });
      
      }
  });
  
  $("#btnReservaMultiple").click(function(event){
      if (document.getElementById("FormReservaMultiple").checkValidity()) {
          event.preventDefault();
  
      let recoger_fechas = [];
      let recoger_canchas = [];
      let hoy_mismo = hoy;
      dia = hoy_mismo.getDate();
      mes = hoy_mismo.getMonth() +1;
      anio= hoy_mismo.getFullYear();
      fecha_actual = String(anio+"-"+mes+"-"+dia);
      let jugadores = [clientes_1_id_multiple];
      if (clientes_2_id_multiple!=undefined) {
        jugadores.push(clientes_2_id_multiple);
      }
      if (clientes_3_id_multiple!=undefined) {
        jugadores.push(clientes_3_id_multiple);
  
      }
      if (clientes_4_id_multiple!=undefined) {
        jugadores.push(clientes_4_id_multiple);
  
      }
      let contiene_dias = 0;
      for (let i = 0; i < dias_semana.length; i++) {
          if (dias_semana[i].checked) {
              for (let c = 0; c < fechas_semana.length; c++) {
                    if (dias_semana[i].nextElementSibling.textContent.trim() == fechas_semana[c].dia) {
                      let greaterTen2 = array_eventos_marcados.filter(e => e.fecha_inicio == fechas_semana[c] );
                      recoger_fechas.push({"fecha":fechas_semana[c].fecha});
                       contiene_dias = 1;
                    }
              }
            
          }
        
      }
      if (contiene_dias == 0) {
          return toastr.warning("NO A SELECIONADO NINGUN DIA");
      }
      canchas_list = $('[id^="canchas_list"]');
      for (let i = 0; i < canchas_list.length; i++) {
          if (canchas_list[i].checked) {
              recoger_canchas.push({"cancha":canchas_list[i].value})
            }
      }
      let ids_reservas = [];
  
          const data = new FormData();
  
      let duracion = document.querySelectorAll("#duracion_multiple");
      data.append("idcancha",JSON.stringify(recoger_canchas));
      data.append("fecha_inicio",JSON.stringify(recoger_fechas));
      data.append("tipo_reserva",$("#tipo_reserva_multiple").val());
  
      let condition = 0;
      corregidor_hora.forEach((e)=>{
        if (e.hora_obtencion.trim() == hora_inicio_multiple.value.trim() && condition ==0) {
          data.append("hora_inicio",e.hora_extraida);
          let tiempo_split = e.hora_extraida.split(":");
          hoy_mismo.setHours(tiempo_split[0],tiempo_split[1],0,0);
          condition =1;
        }
      })
      let tiempo = 0;
      if (btnReservaMultiple.value =="Reserva") {
  
      duracion.forEach((e)=>{
        if (e.checked) {
          tiempo = 1;
            if (e.value == "90 min") {
  
              hoy_mismo.setMinutes(hoy_mismo.getMinutes()+90);
            }
            if (e.value == "120 min") {
              hoy_mismo.setMinutes(hoy_mismo.getMinutes()+120);
            }
            if (hoy_mismo.getMinutes()=="0") {
              data.append("hora_fin",hoy_mismo.getHours() +":"+"00"+ ":" +"00");
  
            }
            else
             {
              data.append("hora_fin",hoy_mismo.getHours() +":"+hoy_mismo.getMinutes()+ ":" +"00");
  
            }
            }
      });
      
      if (tiempo == 0) {
        return toastr.warning("NO SE ASIGNADO LA DURACION");
      }
    }
      data.append("cliente",$("#clientes").val());
      data.append("observaciones",$("#observaciones_multiple").val());
      data.append("descripcion",$("#descripcion_multiple").val());
  
      data.append("jugadores",JSON.stringify(jugadores));
        if (btnReservaMultiple.value =="Reserva") {
          data.append("op","InsertarReservaMultiple");        
        }
        else{
  
          data.append("hora_fin",EditduracionMultiple.value)
          data.append("hora_inicio",hora_inicio_multiple.value);
          $.ajax({
            type : 'POST',
            url : 'mysql/Reservas/index.php',
            data : { op : 'obtenerReservasMultiples',idreservas:ideditareservamultiple},
            beforeSend : function(){
            },
            success : function(response){
              json = JSON.parse(response.trim());
              for (let x = 0; x < json.length; x++) {
                  ids_reservas.push({"idreservas":json[x].idreservas})
              }
            },
            error : function(e){
              console.log(e.responseText);
            },
            complete : function(){
              data.append("ids_reservas",JSON.stringify(ids_reservas));
              data.append("op","ActualizarReservaMultiple");        
  
              $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "mysql/Reservas/index.php",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                beforeSend : function(){
                  $.blockUI({ message: '<h4> Realizando petición...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                  $("#modalReservaMultiple").hide();
                },
                success: function (data) {
                  toastr.success("RESERVA MULTIPLE REGISTRADA");
                         
                  $("#modalReservaMultiple").modal("hide");
                },
                error: function (e) {
                  toastr.error(e.responseText);
                  $("#modalReservaMultiple").modal({backdrop: 'static', keyboard: false}, 'show');
                },
                complete : function(){
                  clientes_1_id_multiple = undefined;
                  clientes_2_id_multiple = undefined;
                  clientes_3_id_multiple = undefined;
                  clientes_4_id_multiple = undefined;
                  hoy = new Date();
                  dia = hoy.getDay()-1;
                  obtenerReservasxCliente()
                dibujar_contenedores();
                tiemposmuertos();
                Reservasdelasemanahorainiciales();
                  $.unblockUI();
                }
              });
            }
          });
        }
      //   for (var pair of data.entries()) {
      //    alert(pair[0]+ ', ' + pair[1]); 
      // }
      if (btnReservaMultiple.value =="Reserva") {
          $.ajax({
              type: "POST",
              enctype: 'multipart/form-data',
              url: "mysql/Reservas/index.php",
              data: data,
              processData: false,
              contentType: false,
              cache: false,
              timeout: 600000,
              beforeSend : function(){
                  $.blockUI({ message: '<h4> Realizando petición...</h4>', css: { backgroundColor: null, color: '#fff', border: null } });
                  $("#modalReservaMultiple").hide();
              },
              success: function (data) {
                  toastr.success("RESERVA MULTIPLE REGISTRADA");
                 
                  $("#modalReservaMultiple").modal("hide");
              },
              error: function (e) {
                  toastr.error(e.responseText);
          $("#modalReservaMultiple").modal({backdrop: 'static', keyboard: false}, 'show');
              },
              complete : function(){
         
          hoy = new Date();
          dia = hoy.getDay()-1;
          obtenerReservasxCliente()
        dibujar_contenedores();
        tiemposmuertos();
        Reservasdelasemanahorainiciales();
                  $.unblockUI();
              }
          });
      }
  }
  });

  function obtenerReservasxCliente() {
      $.ajax({
        type: "POST",
        data: { op: "obtenerReservasxCliente" },
        url: "mysql/Reservas/index.php",
        beforeSend: function () {
        maxreservasclientes = [];
      Editclientes.innerHTML = "";
      Editclientes_2.innerHTML = "";
      Editclientes_3.innerHTML = "";
      Editclientes_4.innerHTML = "";
      clientes.innerHTML = "";
      clientes_2.innerHTML = "";
      clientes_3.innerHTML = "";
      clientes_4.innerHTML = "";
      clientes_multiple.innerHTML = "";
      clientes_2_multiple.innerHTML = "";
      clientes_3_multiple.innerHTML = "";
      clientes_4_multiple.innerHTML = "";
          $.blockUI({
          });
        },
        success: function (response) {
          response = JSON.parse(response.trim());
      response.forEach((e)=>{
          maxreservasclientes.push(e);
        })
        },
        error: function (e) {
          console.log(e.responseText);
        },
        complete: function () {
          $.unblockUI();
      obtencionsocios()
  
        },
      });
    }
  
  
  function obtencionsocios() {
      $.ajax({
        type: "POST",
        data: { op: "obtencionsocios" },
        url: "mysql/Socios/index.php",
        beforeSend: function () {
          $.blockUI({
      
          });
        },
        success: function (response) {
  
          response = JSON.parse(response.trim());
      for (var i = 0; i < response.length; i++) {
          let option = document.createElement("option");
          option.textContent = response[i].jugador;
          option.value = response[i].idpersona;
          option.title=response[i].tipo;
          
          clientes.appendChild(option);   	 
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
        clientes_2.appendChild(option);		
      }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
      
        clientes_3.appendChild(option);		
      }
  
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            clientes_4.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            clientes_multiple.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
        let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
      
            clientes_2_multiple.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            clientes_3_multiple.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            clientes_4_multiple.appendChild(option);
          }
  
  
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
        
            Editclientes.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            Editclientes_2.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
      
            Editclientes_3.appendChild(option);
          }
      for (var i = 0; i < response.length; i++) {
            let option = document.createElement("option");
            option.textContent = response[i].jugador;
            option.value = response[i].idpersona;
        option.title=response[i].tipo;
       
            Editclientes_4.appendChild(option);
          }
     
   
  
      Editclientes.value = "";
      Editclientes_2.value = "";
      Editclientes_3.value = "";
      Editclientes_4.value = "";
      clientes.value = "";
      clientes_2.value = "";
      clientes_3.value = "";
      clientes_4.value = "";
      clientes_multiple.value = "";
      clientes_2_multiple.value = "";
      clientes_3_multiple.value = "";
      clientes_4_multiple.value = "";
      $("#clientes").trigger("change");
      $("#clientes_2").trigger("change");
      $("#clientes_3").trigger("change");
      $("#clientes_4").trigger("change");
      $("#clientes_multiple").trigger("change");
      $("#clientes_2_multiple").trigger("change");
      $("#clientes_3_multiple").trigger("change");
      $("#clientes_4_multiple").trigger("change");
      
      obtencion_evento_click= [];
        canchas_checkboxs.innerHTML = "";
        obtenerCanchas();
        canchas_list = $('[id^="canchas_list"]');
        for (let i = 0; i < canchas_list.length; i++) {
          if (canchas_list[i].hasAttribute("disabled")) {
            canchas_list[i].removeAttribute("disabled")
          }        
        }
        },
        error: function (e) {
          console.log(e.responseText);
        },
        complete: function () {
          $.unblockUI();
        },
      });
    }
    function maximoreservas(){
      toastr.warning("NO SE PUEDE SELECCIONAR YA QUE TIENE 2 RESERVAS SIN USAR EN EL DIA")
    }
    function reservamultiple(){
      hora_inicio_multiple.type = "text" 
      hora_inicio_multiple.setAttribute("disabled",true);
      hora_inicio_multiple.value = localStorage.getItem("hora");
      btnReservaMultiple.value = "Reserva"
      
      // fecha_hoy_multiple.value = dias[hoy.getDay()] + " "+ hoy.getDate() + " "+ meses[hoy.getMonth()] +" "+ hoy.getFullYear();
      $("#modalReservas").modal('hide')
      duracion_text.textContent ="Duración";
      duracion_contenedor.innerHTML =`
      <div   class=" form-check-inline ">
      <label class="form-check-label" for="inlineRadio1"> 90 min&nbsp;</label>
      <input type="radio" value="90 min" id="duracion_multiple" name="duracion">
      </div>
      <div class=" form-check-inline">
      <label class="form-check-label" for="inlineRadio2"> 120 min &nbsp;</label>
      <input type="radio" value="120 min" id="duracion_multiple" name="duracion">
    </div>
      `;
      $("#modalReservaMultiple").modal({backdrop: 'static', keyboard: false}, 'show');
    }
    function obtenerCanchas() {
      $.ajax({
        type: "POST",
        data: { op: "obtenerCanchasTorneos" },
        url: "mysql/Canchas/index.php",
        beforeSend: function () {
          $.blockUI({
            message: "<h4> Cargando canchas...</h4>",
            css: { backgroundColor: null, color: "#fff", border: null },
          });
        },
        success: function (response) {
          // canchas = document.getElementById("canchas_select");
          response = JSON.parse(response.trim());
          let div_cols = document.createElement("div");
          div_cols.classList.add("col-md-4");
          for (var i = 0; i < response.length; i++) {
            let div = document.createElement("div");
            if (i%5==0) {
              div_cols = document.createElement("div");
              div_cols.classList.add("col-md-4");
            }
            div.classList.add("form-check","col-md-12");
            let nombre = response[i].nombre.replace(/\s+/g, '');
            div.innerHTML = `
            <input class="form-check-input col-md-1" checked type="checkbox" value="${response[i].idcanchas}" id="canchas_list${i}"  nombre="${nombre}" canchanombre ="${response[i].nombre}" onclick="" >
            <label class="form-check-label text-left col-md-11" for="flexCheckDefault">
              ` + response[i].idcanchas +" - " +response[i].nombre +` 
            </label>
            `;
          
            div_cols.appendChild(div);
            canchas_checkboxs.appendChild(div_cols);
          
          }
        },
        error: function (e) {
          console.log(e.responseText);
        },
        complete: function () {
          $.unblockUI();
        },
      });
    } 
  function obtenerTorneos(dia) {
    $.ajax({
      type: "POST",
      data: { op: "obtenerTorneosCalendario"},
      url: "mysql/Torneos/index.php",
      beforeSend: function () {
        $.blockUI({ });
      },
      success: function (response) {
        response = JSON.parse(response.trim());
        for (var i = 0; i < response.length; i++) {
         obtenerangodefechastorneos(response[i].fecha,response[i].fecha_fin,response[i].horario_inicio,response[i].horario_fin,response[i].cancha,response[i].idcanchas)
        }
      },
      error: function (e) {
        console.log(e.responseText);
      },
      complete: function () {
        $.unblockUI();
        obtenerCanchasPremiumDia(dia);
     
      },
    });
  }
  
  function obtenerangodefechastorneos(fecha_ini,fecha_fin,hora_inicio,hora_fin,cancha,idcanchas) {
    $.ajax({
      type: "POST",
      data: { op: "obtenerfechasenrango",fecha_fin:fecha_fin,fecha_ini:fecha_ini},
      url: "mysql/Torneos/index.php",
      beforeSend: function () {
        $.blockUI({ });
      },
      success: function (response) {
        response = JSON.parse(response.trim());
        for (var i = 0; i < response.length; i++) {
          array_eventos_marcados.push({"fecha_inicio":response[i].fecha_inicio,"horario_ini":horastring(hora_inicio),"horario_fin":horastring(hora_fin),"cancha":cancha.replace(/\s+/g, ''),"tipo":"torneo","idcanchas":idcanchas});
          obtenerHorasIntervalosCalendario(hora_inicio,hora_fin,response[i].fecha_inicio,cancha.replace(/\s+/g, ''));
  
        }
      },
      error: function (e) {
        console.log(e.responseText);
      },
      complete: function () {
        $.unblockUI();
      },
    });
  }
  function ObtenerTiempoMuerto(){
      $.ajax({
          type : 'POST',
          data : { op : 'cargarHorariosGral' },
          url : 'mysql/Canchas/index.php',
          beforeSend : function (){
              $.blockUI();
          },
          success : function(response){
              response = JSON.parse(response.trim());
          
        tiempo_muerto =horastring(response[0].tiempo_muerto);
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              $.unblockUI();
          }
      })
  }
  
  Edithora_inicio.addEventListener("change",(e)=>{
  let hora_ = Edithora_inicio.value.split(":");
  if(hora_[1] !="30" && hora_[1] !="00"){
    Edithora_inicio.value = "";
    toastr.warning("SOLO SE PERMITEN INTERVALOS DE 3OM O 1H")
  }
  })
  function obteneracessosocios(){
      $.ajax({
          type : 'POST',
          data : { op : 'obteneraccesosocios'},
          url : 'mysql/Membresias/index.php',
          beforeSend : function(){
              // $("#selectPatrocinador").empty();
          },
          success : function(response){
              response = JSON.parse(response.trim());
              response.forEach((e)=>{
                accesos_socios.push(e);
              })
              // for (var i = 0; i < response.length; i++) {
              // 	// $("#selectPatrocinador").append(`<option value="${response[i].idpatrocinadores}">${response[i].nombre}</option>`);
              // }
          },
          error : function(e){
              console.log(e.responseText);
          },
          complete : function(){
              // $("#selectPatrocinador").trigger("change");
          }
      })
  }
<!DOCTYPE html>
<html lang="en">
<?php include("estilos.php"); ?>
<title>Play Padel - Clínicas/Inscritos</title>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include("menu.php") ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-8">
              <h1 class="m-0">Clínicas/Inscritos</h1>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="form-group row centeredCol">
          <div class="col-md-3">
            <label class="form-check-label">Fecha de inicio</label>
            <input type="date" class="form-control inDate" id="fecha_ini">
          </div>
          <div class="col-md-3">
            <label class="form-check-label">Fecha de término</label>
            <input type="date" class="form-control inDate" id="fecha_fin">
          </div>
          <div class="col-md-3">
            <label class="form-check-label">Selecciona una clínica</label>
            <select class="form-control select2" id="selectClinica"></select>
          </div>
          <div class="col-md-2">
            <br>
            <button class="btn btn-success btn-flat btn-sm" id="btnExcel">
             <i class="fa fa-file-excel" aria-hidden="true"></i>
             EXCEL
           </button>
         </div>
       </div>
       <div class="form-group row">
        <div class="col-12 col-lg-12">
          <div class="tarjeta">
            <p class="tituloTarjeta">Listado</p>
            <div class="cuerpoTarjeta">
              <table id="tabla" class="table table-bordered table-sm" style="background: white">
                <thead>
                  <tr>
                    <th>Folio</th>
                    <th>Participante/s</th>
                    <th>Tipo</th>
                    <th>Clínica</th>
                    <th>Entrenador/es</th>
                    <th>Fecha de inscripción</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include("footer.php"); ?>
</div>
</div>

<div class="modal fade" id="mExample">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row centeredCol">
          <div class="col-md-12">
            <h6 id="tituloFolio"></h6>
            <h6 id="conceptoPago"></h6>
          </div>
        </div>  
        <div class="form-group row">
          <div class="col-md-12">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">DETALLE DE PAGO</a>
                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">TARJETA/S</a>
                <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">ARCHIVO/S</a>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="form-group row">
                  <div class="col-md-10">
                    <label for="">Información de pago</label>
                    <div class="row">
                      <div class="col-md-6">
                        <h6>EFECTIVO: </h6>
                      </div>
                      <div class="col-md-6" style="text-align:right">
                        <h6 id="totalEfectivo"></h6>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <h6>TRANSFERENCIA: </h6>
                      </div>
                      <div class="col-md-6" style="text-align:right">
                        <h6 id="totalTransferencia"></h6>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <h6># TRANSACCIÓN: </h6>
                      </div>
                      <div class="col-md-6" style="text-align:right">
                        <h6 id="totalNoTransaccion"></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="form-group row">
                  <div class="col-md-12">
                    <table id="tablaTarjetas" class="table table-bordered table-sm" style="width:100%">
                      <thead class="text-center">
                        <tr>                  
                          <th>Tarjeta</th>
                          <th>Monto</th>
                          <th>Voucher</th>
                          <th>Dígitos</th>
                        </tr>
                      </thead>
                      <tbody id="cuerpoTarjetas"></tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <div class="row" id="divArchivos"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-flat btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php include("scripts.php"); ?>
<script type="text/javascript" src="scripts/xlsx.js"></script>
<script type="text/javascript" src="scripts/clinicas_inscritos.js"></script>
</body>
</html>

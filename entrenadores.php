<!DOCTYPE html>
<html lang="en">
<?php include("estilos.php"); ?>
<title>Play Padel - Entrenadores</title>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include("menu.php") ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-9">
              <h1 class="m-0">Entrenadores</h1>
            </div>
            <div class="col-sm-3" style="text-align:right">
              <button class="btn btn-primary btn-flat btn-sm" id="btnLimpiar" onclick="limpiar()">
                REGISTRAR ENTRENADOR
              </button>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="row" style="text-align:center">
          <div class="col-12 col-lg-12">
            <div class="tarjeta">
              <p class="tituloTarjeta">Listado</p>
              <div class="cuerpoTarjeta">
                <table id="tabla" class="table table-bordered table-sm" style="background: white">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Teléfono</th>
                      <th>Dirección</th>
                      <th>Correo</th>
                      <th>Fecha de nacimiento</th>
                      <th>Edad</th>
                      <th>Estatus</th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="modal fade" id="modalEntrenadores" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <form id="FormEntrenador" action="mysql/Entrenadores/index.php" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group row">
                  <div class="col-md-12">
                    <h3>Registro/Actualización de Entrenadores</h3>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-3">
                    <label class="form-check-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                  </div>
                  <div class="col-md-2">
                    <label class="form-check-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                  </div>
                  <div class=" col-md-6">
                    <label class="form-check-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control" required>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-3">
                    <label class="form-check-label">Correo</label>
                    <input type="text" name="correo" id="correo" class="form-control" required>
                  </div>
                  <div class="col-md-2">
                    <label class="form-check-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-check-label">Fotografía</label>
                    <input type="file" name="foto" id="foto" class="form-control" required>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-12">
                    <label class="form-check-label">Comentarios adicionales</label>
                    <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <input type="submit" id="btnEntrenadores" class="btn btn-success btn-flat" value="Guardar entrenador">
                <input type="button" class="btn btn-secondary btn-flat" data-dismiss="modal" value="Cerrar">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php include("footer.php"); ?>
  </div>
  <?php include("scripts.php"); ?>
  <script type="text/javascript" src="scripts/entrenadores.js"></script>
</body>
</html>
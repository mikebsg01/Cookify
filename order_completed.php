<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function makeOrderController() {
  if (! isLoggued()) {
    header('Location: login.php');
    return;
  }

  if (existsFlash('ALERT_SUCCESS')) {
    getFlash('ALERT_SUCCESS');
    return;
  }

  header('Location: index.php');
  return;
}

makeOrderController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<div class="page-order-completed row">
  <div class="col s12">
    <div class="col offset-s3 s6">
      <div class="order-completed-card card">
        <div class="card-content">
          <div class="row">
            <div class="col s12 center-align">
              <span class="card-title">
                ¡Su pedido ha sido realizado exitosamente!
              </span>
              <div class="center-align">
                <i class="order-completed-icon material-icons">assignment_turned_in</i>
              </div>
              <div class="order-completed-notice center-align">
                <p>Su pedido llegará hasta su ubicación en un tiempo máximo de 30 minutos. Le pedimos por favor esté pendiente.</p>
              </div>
              <div class="order-completed-thanks center-align">
                <p>El equipo <b>Cookify</b> agradece su preferencia :)</p>
              </div>
              <div class="order-completed-back-link center-align">
                <a href="index.php" class="btn btn-primary"><i class="material-icons left">keyboard_backspace</i> Volver al Inicio</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once 'templates/scripts.php' ?>
<?php include_once 'templates/footer.php' ?>

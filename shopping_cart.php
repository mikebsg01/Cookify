<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$shopping_cart = [];

function shoppingCartController() {
  global $shopping_cart;

  if (! isLoggued()) {
    header('Location: login.php');
    return;
  }

  $shopping_cart = getShoppingCart();

  App::print("funciona", $shopping_cart);
}

shoppingCartController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<div class="page-login row">
  <?php if (existsFlash('ALERT_INFO')): ?>
    <div class="card-panel orange darken-1 alert-info">
      <span class="white-text"><?php echo getFlash('ALERT_INFO'); ?></span>
      <i class="material-icons right app-close-alert">close</i>
    </div>
  <?php endif; ?>
  <div class="col s12">
    <div class="col offset-s3 s6">
      <div class="card login-card">
        <div class="card-content">
          <div class="row">
            <div class="col s12">
              <span class="card-title">Mi Carrito</span>
            </div>
            <div class="col s12">
              <?php foreach($shopping_cart as $plate_slug => $amount): ?>
                <?php App::print($plate_slug, $amount); ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once 'templates/scripts.php' ?>
<?php include_once 'templates/footer.php' ?>
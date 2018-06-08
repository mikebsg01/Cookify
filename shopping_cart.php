<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$added_plates = [];
$payment = null;

function shoppingCartController() {
  global $added_plates, $payment;

  if (! isLoggued()) {
    header('Location: login.php');
    return;
  }

  $shopping_cart_details  = getShoppingCartDetails();
  $added_plates           = $shopping_cart_details->added_plates;
  $payment                = $shopping_cart_details->payment;
}

shoppingCartController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<div class="page-shopping-cart row">
  <?php if (existsFlash('ALERT_INFO')): ?>
    <div class="card-panel orange darken-1 alert-info">
      <span class="white-text"><?php echo getFlash('ALERT_INFO'); ?></span>
      <i class="material-icons right app-close-alert">close</i>
    </div>
  <?php endif; ?>
  <div class="col s12">
    <div class="col offset-s3 s6">
      <div class="card">
        <div class="card-content">
          <div class="row">
            <div class="col s6">
              <span class="card-title"><i class="material-icons left">shopping_cart</i>Mi Carrito</span>
            </div>
            <div class="col s6 right-align">
              <?php if ($total_plates_added > 0): ?>
                <form action="reset_shopping_cart.php" method="POST">
                  <input type="hidden" name="empty" value="1">
                  <button type="submit" class="btn white color-primary"><i class="material-icons left">delete</i>Vaciar Carrito</button>
                </form>
              <?php endif; ?>
            </div>
            <div class="col s12">
              <table class="striped shopping-cart-table">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th class="center-align">Precio Unitario</th>
                    <th class="center-align">Cantidad</th>
                    <th class="center-align">Precio Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($total_plates_added > 0): ?>
                    <?php foreach($added_plates as $added_plate): ?>
                      <tr>
                        <td><?php echo capitalize($added_plate->name) ?></td>
                        <td class="right-align"><?php echo toMoney($added_plate->unit_price) ?></td>
                        <td class="center-align"><?php echo $added_plate->amount ?></td>
                        <td class="right-align"><b><?php echo toMoney($added_plate->total_price) ?></b></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="center-align"><span>&laquo; No ha agregado platillos al carrito de compras todavía. &raquo;</span></td>
                    </tr>
                  <?php endif; ?>
                  <?php if ($total_plates_added > 0): ?>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                      <td><b>Subtotal</b></td>
                      <td class="right-align"><?php echo toMoney($payment->subtotal); ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                      <td><b>IVA</b></td>
                      <td class="right-align"><?php echo ($payment->iva ?: '16.00') ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                      <td class="payment-total"><b>Total</b></td>
                      <td class="right-align payment-total"><?php echo toMoney($payment->total); ?></td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
              <?php if ($total_plates_added > 0): ?>
                <div class="col s12 center-align make-order-btn-container">
                  <form action="make_order.php" method="POST">
                    <input type="hidden" name="make" value="1">
                    <button type="submit" class="btn btn-primary"><i class="material-icons left">check</i>Realizar Pedido</button>
                  </form>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once 'templates/scripts.php' ?>
<?php include_once 'templates/footer.php' ?>
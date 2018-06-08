<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function makeOrderValidation($data) {
  $errors = 0;

  # Address Validation:
  if (empty($data['address'])) {
    ++$errors;
    makeError('address', 'El campo de dirección es requerido.');
  }

  return ! ($errors > 0);
}

function makeOrderController() {
  if (! isLoggued()) {
    header('Location: login.php');
    return;
  }

  if (isset($_POST['make']) and $_POST['make'] and getTotalPlatesAdded() > 0) {
    return;
  }

  if (!empty($_POST['order'])) {
    $order_data = filterData($_POST['order'], [
      'address',
      'comment'
    ]);

    if (makeOrderValidation($order_data)) {
      $user_data  = (object) getUserData(['id']);
      $user_id    = (int) $user_data->id;

      $save_order_query = "INSERT INTO `orders` (`address`, `comment`, `user_id`)
                           VALUES('{$order_data['address']}', '{$order_data['comment']}', '{$user_id}')";

      $shopping_cart = getShoppingCart() ?: [];

      $get_plates_id_query = "";
      $i = 0;

      foreach($shopping_cart as $plate_slug => $amount) {
        if ($i > 0) {
          $get_plates_id_query .= " UNION ALL ";
        }

        $get_plates_id_query .= "SELECT `id`, {$amount} AS `amount` 
                                 FROM `plates` 
                                 WHERE `plates`.`slug` = '{$plate_slug}'";
        ++$i;
      }

      $order_transaction = dbTransaction(
        $save_order_query,
        function ($conn, &$previous_results) use ($get_plates_id_query) {
          $order_id           = (int) $conn->insert_id;
          $result             = $conn->query($get_plates_id_query);
          $previous_results[] = $result;

          if (! $result) {
            throw new Exception('MYSQL TRANSACTION FAILED');
          }

          $n            = count($previous_results);
          $last_result  = $previous_results[$n - 1];
          $plates_added = [];
          
          if ($last_result->num_rows > 0) {
            while ($row = $last_result->fetch_assoc()) {
              array_push($plates_added, (object) [
                'id'      => (int) $row['id'],
                'amount'  => (int) $row['amount']
              ]);
            }

            foreach ($plates_added as $plate_added) {
              $result = $conn->query("INSERT INTO `orders_has_plates` 
                                      (`order_id`, `plate_id`, `amount`)
                                      VALUES('{$order_id}', '{$plate_added->id}',
                                             '{$plate_added->amount}')");
              $previous_results[] = $result;   
              
              if (! $result) {
                throw new Exception('MYSQL TRANSACTION FAILED');
              }
            }
          }

          $shopping_cart_details  = getShoppingCartDetails();
          $payment                = $shopping_cart_details->payment;
          
          $result = $conn->query("INSERT INTO `invoices`
                                  (`subtotal`, `iva`, `total`, `order_id`)
                                  VALUES('{$payment->subtotal}', '{$payment->iva}', 
                                         '{$payment->total}', '{$order_id}')");
          $previous_results[] = $result;   
              
          if (! $result) {
            throw new Exception('MYSQL TRANSACTION FAILED');
          }
        }
      );

      if ($order_transaction->all_query_ok) {
        resetShoppingCart();
        makeFlash('ALERT_SUCCESS', 1);

        header('Location: order_completed.php');
        return;
      }
    }
    return;
  }

  header('Location: index.php');
  return;
}

makeOrderController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<div class="page-make-order row">
  <div class="col s12">
    <div class="col offset-s3 s6">
      <div class="card">
        <div class="card-content">
          <div class="row">
            <div class="col s12">
              <span class="card-title"><i class="material-icons left">assignment</i>Finalizando Pedido</span>
            </div>
            <div class="col s12">
              <form action="make_order.php" method="POST">
                <div class="input-field col s12">
                  <textarea id="address" name="order[address]" class="materialize-textarea validate" required="required"></textarea>
                  <label for="address">Dirección<sup>*</sup></label>
                  <?php if (existsError('address')): ?>
                    <span class="lbl-error"><?php echo getError('address')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <textarea id="comment" name="order[comment]" class="materialize-textarea validate"></textarea>
                  <label for="comment">Comentario <sup>(opcional)</sup></label>
                </div>
                <div class="col s12 center-align">
                  <button type="submit" class="btn btn-primary"><i class="material-icons left">check</i>Finalizar Pedido</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once 'templates/scripts.php' ?>
<?php include_once 'templates/footer.php' ?>

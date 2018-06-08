<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function resetShoppingCartController() {
  if (! isLoggued()) {
    header('Location: login.php');
    return;
  }

  if (isset($_POST['empty']) and $_POST['empty']) {
    resetShoppingCart();
    header('Location: shopping_cart.php');
    return;
  }

  header('Location: login.php');
  return;
}

resetShoppingCartController();
?>

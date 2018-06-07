<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function addToShoppingCartController() {
  if (! isLoggued()) {
    makeFlash('ALERT_INFO', 'Por favor, inicie sesión para poder agregar platillos al carrito de compras.');
    header('Location: login.php');
    return;
  }

  if (!empty($_POST['add_plate'])) {
    $add_plate = $_POST['add_plate'];

    App::print($add_plate);
    return;
  }

  header('Location: login.php');
  return;
}

addToShoppingCartController();
?>

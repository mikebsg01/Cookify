<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$is_loggued = false;
$user_data = null;
$total_plates_added = 0;
$current_action = null;

function headerController() {
  global $is_loggued, $current_action, $user_data, $total_plates_added;

  $is_loggued = isLoggued();

  if ($is_loggued) {
    $user_data = getUserData(['first_name', 'last_name']);
  }

  $current_action     = App::getCurrentAction();
  $total_plates_added = getTotalPlatesAdded();
}

headerController();
?>
<div class="navbar-fixed">
  <nav class="app-nav">
    <div class="nav-wrapper">
      <a href="index.php" title="Ir al Inicio" class="brand-logo">Cookify<i class="material-icons right">restaurant</i></a>
      <ul class="right hide-on-med-and-down">
        <?php if ($is_loggued): ?>
          <li class="<?php echo ($current_action == 'menu' ? 'active' : '') ?>"><a href="menu.php"><i class="material-icons left">list</i> Ver Menú Completo</a></li>
          <li class="<?php echo ($current_action == 'shopping_cart' ? 'active' : '') ?>">
            <a href="shopping_cart.php">
              <?php if ($total_plates_added > 0): ?>
                <span class="shopping-cart-notification-badge new badge" data-badge-caption="<?php echo $total_plates_added; ?>"></span>
              <?php endif; ?>
              <i class="material-icons left">shopping_cart</i> Mi carrito
            </a>
          </li>
          <li>
            <a href="#" class="dropdown-trigger" href="#!" data-target="dropdown-menu-user"><i class="material-icons left">person</i><?php echo getShortName($user_data['first_name'], $user_data['last_name']); ?><i class="material-icons right">arrow_drop_down</i></a>
          </li>
          <ul id="dropdown-menu-user" class="dropdown-content">
            <li class="divider"></li>
            <li>
              <a id="logout-link" href="#">Cerrar sesión<i class="material-icons right">exit_to_app</i></a>
              <form method="POST" action="logout.php" accept-charset="UTF-8" id="logout-form">
                <input type="hidden" name="logout" value="1">
              </form>
            </li>
          </ul>
        <?php else: ?>
          <li><a href="menu.php"><i class="material-icons left">list</i> Ver Menú Completo</a></li>
          <li><a href="login.php"><i class="material-icons left">person</i> Iniciar sesión</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</div>
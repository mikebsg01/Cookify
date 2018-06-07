<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function logoutController() {
  if (isset($_POST['logout']) and $_POST['logout'] and 
      isLoggued()) {
    logout();
  }

  header('Location: index.php');
  return;
}

logoutController();

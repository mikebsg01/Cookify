<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function loginValidation($data) {
  $errors = 0;

  # Email Validation:
  if (empty($data['email'])) {
    ++$errors;
    makeError('email', 'El campo de correo electrónico es requerido.');
  } else if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    ++$errors;
    makeError('email', 'Debe ingresar un correo electrónico válido.');
  }

  # Password Validation:
  if (empty($data['password'])) {
    ++$errors;
    makeError('password', 'El campo de contraseña es requerido.');
  }

  return ! ($errors > 0);
}

function loginController() {
  if (isLoggued()) {
    header('Location: index.php');
    return;
  }

  if (!empty($_POST['user'])) {
    $user_data = filterData($_POST['user'], [
      'email',
      'password'
    ]);

    if (loginValidation($user_data)) {
      $result = dbQuery("SELECT count(*) as `counter` FROM `users`
                         WHERE `users`.`email` = '{$user_data['email']}' AND
                               `users`.`password` = SHA2('{$user_data['password']}', 256)");

      if (getCounter($result) == 1) {
        login($user_data['email']);
        header('Location: login.php');
        return;
      } else {
        makeError('password', 'El usuario y/o contraseña es incorrecto.');
      }
    }
  }
}

loginController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<div class="page-login row">
  <?php if (existsFlash('ALERT_SUCCESS')): ?>
    <div class="card-panel green darken-1 alert-success">
      <span class="white-text"><?php echo getFlash('ALERT_SUCCESS'); ?></span>
      <i class="material-icons right app-close-alert">close</i>
    </div>
  <?php endif; ?>
  <?php if (existsFlash('ALERT_INFO')): ?>
    <div class="card-panel orange darken-1 alert-info">
      <span class="white-text"><?php echo getFlash('ALERT_INFO'); ?></span>
      <i class="material-icons right app-close-alert">close</i>
    </div>
  <?php endif; ?>
  <div class="col s12">
    <div class="col offset-s4 s4">
      <div class="card login-card">
        <div class="card-content">
          <div class="row">
            <div class="col s12">
              <span class="card-title">Iniciar sesión</span>
            </div>
            <div class="col s12">
              <form action="login.php" method="POST">
                <div class="input-field col s12">
                  <input id="email" type="email" name="user[email]" class="validate" required="required">
                  <label for="email">Correo electrónico</label>
                  <?php if (existsError('email')): ?>
                    <span class="lbl-error"><?php echo getError('email')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="password" type="password" name="user[password]" class="validate" required="required">
                  <label for="password">Contraseña</label>
                  <?php if (existsError('password')): ?>
                    <span class="lbl-error"><?php echo getError('password')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="center-align">
                  <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
                <div class="center-align link-register">
                  <span><a href="signup.php">¿Aún no estás registrado?</a> Haz click aquí para <a href="signup.php">registrarte.</a></span>
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

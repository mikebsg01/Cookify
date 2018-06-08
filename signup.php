<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$old_user_data = null;

function signupValidation($data) {
  $errors = 0;

  # First Name Validation: 
  if (empty($data['first_name'])) {
    ++$errors;
    makeError('first_name', 'El campo nombre(s) es requerido');
  } else if (strlen($data['first_name']) > 25) {
    ++$errors;
    makeError('first_name', 'El campo nombre(s) no debe ser mayor a 25 caracteres.');
  }

  # Last Name Validation: 
  if (empty($data['last_name'])) {
    ++$errors;
    makeError('last_name', 'El campo apellido(s) es requerido.');
  } else if (strlen($data['last_name']) > 25) {
    ++$errors;
    makeError('last_name', 'El campo apellido(s) no debe ser mayor a 25 caracteres.');
  }

  # Phone Number Validation: 
  if (empty($data['phone_number'])) {
    ++$errors;
    makeError('phone_number', 'El campo teléfono es requerido.');
  } else if (! is_numeric($data['phone_number']) or 
             strlen($data['phone_number']) !== 10) {
    ++$errors;
    makeError('phone_number', 'El campo teléfono debe ser un número de 10 dígitos.');
  }

  # Email Validation:
  if (empty($data['email'])) {
    ++$errors;
    makeError('email', 'El campo de correo electrónico es requerido.');
  } else if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    ++$errors;
    makeError('email', 'Debe ingresar un correo electrónico válido.');
  } else {
    $result = dbQuery("SELECT count(*) as `counter` FROM `users` 
                       WHERE `users`.`email` = '{$data['email']}'");

    if (getCounter($result) > 0) {
      ++$errors;

      makeError('email', 'El correo electrónico ingresado ya existe.');
    }
  }

  # Password Validation:
  if (empty($data['password'])) {
    ++$errors;
    makeError('password', 'El campo de contraseña es requerido.');
  } else if (strlen($data['password']) < 8) {
    ++$errors;
    makeError('password', 'La contraseña debe ser mayor a 8 caracteres.');
  }

  if (empty($data['password_confirm'])) {
    ++$errors;
    makeError('password_confirm', 'El campo de confirmación de contraseña es requerido.');
  }
  
  if (! empty($data['password']) and ! empty($data['password_confirm']) and
      $data['password_confirm'] !== $data['password']) {
    ++$errors;
    makeError('password_confirm', 'La confirmación de contraseña no coincide.');
  }

  return ! $errors;
}

function signupController() {
  global $old_user_data;

  if (isLoggued()) {
    header('Location: index.php');
    return;
  }

  if (!empty($_POST['user'])) {
    $user_data = filterData($_POST['user'], [
      'first_name',
      'last_name',
      'phone_number',
      'email',
      'password',
      'password_confirm'
    ]);

    $old_user_data = $user_data;

    if (signupValidation($user_data)) {
      $user_saved = dbQuery("INSERT INTO `users` 
                             (`is_admin`, `first_name`, `last_name`, `full_name`, `photo_id`, 
                              `phone_number`, `email`, `password`)
                             VALUES(0, '{$user_data['first_name']}', '{$user_data['last_name']}',
                                    '{$user_data['first_name']} {$user_data['last_name']}', 1,
                                    '{$user_data['phone_number']}', '{$user_data['email']}', 
                                    SHA2('{$user_data['password']}', 256))");
      
      if ($user_saved) {
        makeFlash('ALERT_SUCCESS', 'Se ha registrado exitosamente! Por favor, inicie sesión.');
        header('Location: login.php');
        return;
      } else {
        makeFlash('ALERT_INFO', 'Lo sentimos, ocurrió un problema en el servidor. Por favor intentelo más tarde.');
      }
    }
  }
}

signupController();

include_once 'templates/head.php';
include_once 'templates/header.php';
?>
<?php include_once 'templates/head.php' ?>
<?php include_once 'templates/header.php' ?>
<div class="page-signup row">
  <?php if (existsFlash('ALERT_INFO')): ?>
    <div class="card-panel orange darken-1 alert-info">
      <span class="white-text"><?php echo getFlash('ALERT_INFO'); ?></span>
      <i class="material-icons right app-close-alert">close</i>
    </div>
  <?php endif; ?>
  <div class="col s12">
    <div class="col offset-s4 s4">
      <div class="card signup-card">
        <div class="card-content">
          <div class="row">
            <div class="col s12">
              <span class="card-title">Registrarse</span>
            </div>
            <div class="col s12">
              <form action="signup.php" method="POST">
                <div class="input-field col s12">
                  <input id="first_name" type="text" name="user[first_name]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['first_name']}\"" : '') ?>>
                  <label for="first_name">Nombre(s)</label>
                  <?php if (existsError('first_name')): ?>
                    <span class="lbl-error"><?php echo getError('first_name')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="last_name" type="text" name="user[last_name]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['last_name']}\"" : '') ?>>
                  <label for="last_name">Apellido(s)</label>
                  <?php if (existsError('last_name')): ?>
                    <span class="lbl-error"><?php echo getError('last_name')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="phone_number" type="text" name="user[phone_number]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['phone_number']}\"" : '') ?>>
                  <label for="phone_number">Teléfono</label>
                  <?php if (existsError('phone_number')): ?>
                    <span class="lbl-error"><?php echo getError('phone_number')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="email" type="email" name="user[email]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['email']}\"" : '') ?>>
                  <label for="email">Correo electrónico</label>
                  <?php if (existsError('email')): ?>
                    <span class="lbl-error"><?php echo getError('email')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="password" type="password" name="user[password]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['password']}\"" : '') ?>>
                  <label for="password">Contraseña</label>
                  <?php if (existsError('password')): ?>
                    <span class="lbl-error"><?php echo getError('password')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="input-field col s12">
                  <input id="password_confirm" type="password" name="user[password_confirm]" class="validate" required="required"<?php echo (! is_null($old_user_data) ? " value=\"{$old_user_data['password_confirm']}\"" : '') ?>>
                  <label for="password_confirm">Confirmar Contraseña</label><?php if (existsError('password_confirm')): ?>
                    <span class="lbl-error"><?php echo getError('password_confirm')[0] ?></span>
                  <?php endif; ?>
                </div>
                <div class="center-align">
                  <button type="submit" class="btn btn-primary">Registrarme</button>
                </div>
                <div class="center-align link-login">
                  <span><a href="login.php">¿Ya tienes una cuenta?</a> Haz click aquí para <a href="login.php">iniciar sesión.</a></span>
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

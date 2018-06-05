<?php
  
?>
<?php include_once 'templates/head.php' ?>
<?php include_once 'templates/header.php' ?>
<div class="page-signup row">
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
                  <input id="first_name" type="text" name="first_name" class="validate" required="required">
                  <label for="first_name">Nombre(s)</label>
                </div>
                <div class="input-field col s12">
                  <input id="last_name" type="text" name="last_name" class="validate" required="required">
                  <label for="last_name">Apellido(s)</label>
                </div>
                <div class="input-field col s12">
                  <input id="email" type="email" name="email" class="validate" required="required">
                  <label for="email">Correo electrónico</label>
                </div>
                <div class="input-field col s12">
                  <input id="password" type="password" name="password" class="validate" required="required">
                  <label for="password">Contraseña</label>
                </div>
                <div class="input-field col s12">
                  <input id="password_confirm" type="password" name="password_confirm" class="validate" required="required">
                  <label for="password_confirm">Confirmar Contraseña</label>
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

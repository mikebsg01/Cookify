<?php
  
?>
<?php include_once 'templates/head.php' ?>
<?php include_once 'templates/header.php' ?>
<div class="page-login row">
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
                  <input id="email" type="text" name="email" class="validate">
                  <label for="email">Correo electrónico</label>
                </div>
                <div class="input-field col s12">
                  <input id="password" type="password" name="password" class="validate">
                  <label for="password">Contraseña</label>
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

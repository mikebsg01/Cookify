<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

function datos(){
  if ($_POST){
    if(isset($_POST['first_name']) and isset($_POST['last_name']) and isset($_POST['email'])){
      $first_name = $POST['first_name'];
      $last_name= $_POST['last_name'];
      $email = $_POST['email'];
    }
    $query = dbQuery("SELECT * FROM users");
    echo '<form action="perfil.php" method="POST">';
    while($r = mysqli_fetch_array($query)):
      echo '<div class="input-field col s12';
      echo "<p>{$r['last_name']}</p>";
      echo '</div>';
    endwhile; 
    echo "</form>";
  }
}

include_once 'templates/head.php';
include_once 'templates/header.php';
?>

<div class="page-perfil row">
  <div class="col s12">
    <div class="col offset-s4 s4">
      <?php 
        datos();
       ?>
      
    </div>
  </div>

</div>
<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$plates = [];

function indexController() {
  global $plates;

  $result = dbQuery("SELECT * FROM `plates` 
                     WHERE 1 
                     ORDER BY created_at ASC 
                     LIMIT 6");

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $plates[] = (object) $row;
    }
  }
}

indexController();

include_once 'templates/head.php';
include_once 'templates/header.php'; 
?>
<div class="page-index row">
  <header>
    <div class="col s12 header">
      <h1>Tasty & Fast</h1>
      <p>This App is so Amazing</p>
    </div>
  </header>
  <section>
    <div class="container">
      <div class="plates-container row">
        <div class="col s12 center-align">
          <header>
            <h2 class="plates-container-title">Platillos agregados recientemente</h2>
          </header>
        </div>
        <?php foreach ($plates as $plate): ?>
          <div class="col s4">
            <div class="plate-card card">
              <div class="plate-image card-image">
                <img src="<?php echo getImageSource($plate->image_id); ?>" alt="Imagen de <?php echo $plate->name; ?>">
                <span class="plate-name card-title"><?php echo capitalize($plate->name); ?></span>
                <form action="add_to_shopping_cart.php" method="POST">
                  <input type="hidden" name="add_plate" value="<?php echo $plate->slug; ?>">
                  <button type="submit" class="btn-floating btn-large halfway-fab waves-effect waves-light red"><i class="material-icons">add_shopping_cart</i></button>
                </form>
              </div>
              <div class="plate-description card-content">
                <p><?php echo strLimit(trim($plate->description), 80); ?></p>
              </div>
              <div class="card-action">
                <span class="plate-price"><?php echo toMoney($plate->price); ?></span>
                <span class="plate-category new badge" data-badge-caption="<?php echo getCategory($plate->category_id)->name; ?>"></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>
<?php include_once 'templates/scripts.php' ?>
<script type="text/javascript" src="assets/js/app/index.js?v=<?php echo time() ?>"></script>
<?php include_once 'templates/footer.php' ?>

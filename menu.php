<?php if (!file_exists('system/core.php')) exit("Sorry, has been ocurred an error trying to load the system.");

require_once 'system/core.php';

$plates = [];
$categories = [];

function indexController() {
  global $plates, $categories;

  if (!empty($_GET['categories'])) {
    $categories = $_GET['categories'];

    $query = "SELECT `plates`.* FROM `plates` 
              INNER JOIN `categories` ON `plates`.`category_id` = `categories`.`id` 
              WHERE ";
    $i = 0;

    foreach ($categories as $category_slug) {
      if ($i > 0) {
        $query .= " OR ";
      }

      $query .= "`categories`.`slug` = '{$category_slug}'";
      ++$i;
    }

    $result = dbQuery($query);
  } else {
    $result = dbQuery("SELECT * FROM `plates` 
                       WHERE 1 
                       ORDER BY created_at DESC");
  }

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
<div class="page-menu row">
  <section>
    <div class="col s10 offset-s1">
      <div class="plates-container row">
        <div class="col s12 center-align">
          <header>
            <h2 class="plates-container-title">Menú completo</h2>
          </header>
        </div>
        <div class="filter-form-container col s2">
          <h3 class="title">Categorias:</h3>
          <form action="menu.php" method="GET">
            <?php foreach (getAllCategories() as $category): ?>
              <div class="col s12">
                <label>
                  <input type="checkbox" name="categories[]" value="<?php echo $category->slug; ?>"<?php echo (in_array($category->slug, $categories) ? " checked=\"checked\"" : '') ?>>
                  <span><?php echo $category->name; ?></span>
                </label>
              </div>
            <?php endforeach; ?>
            <div class="left-align">
              <button type="submit" class="btn btn-primary btn-filter"><i class="material-icons left">filter_list</i>Filtrar</button>
            </div>
          </form>
        </div>
        <div class="col s10">
          <?php $i = 0; ?>
          <?php foreach ($plates as $plate): ?>
            <?php if ($i % 3 == 0): ?>
              <div class="row">
            <?php endif; ?>
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
            <?php if ($i % 3 == 2): ?>
              </div>
            <?php endif; ?>
            <?php ++$i; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</div>
<?php include_once 'templates/scripts.php' ?>
<script type="text/javascript" src="assets/js/app/menu.js?v=<?php echo time() ?>"></script>
<?php include_once 'templates/footer.php' ?>

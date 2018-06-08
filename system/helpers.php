<?php

if (! function_exists('env')) {
  function env($enviromentVariable, $defaultValue = null) {
    global $env;

    return (property_exists($env, $enviromentVariable) and ! empty($env->{$enviromentVariable})) ? $env->{$enviromentVariable} : $defaultValue;
  }
}

if (! function_exists('cstrtolower')) {
  function cstrtolower($str) {
    return mb_strtolower($str, 'UTF-8');
  }
}

if (! function_exists('cstrtoupper')) {
  function cstrtoupper($str) {
    return mb_strtoupper($str, 'UTF-8');
  }
}

if (! function_exists('cucfirst')) {
  function cucfirst($str) {
    $initial  = cstrtoupper(mb_substr($str, 0, 1));
    $ucfirst  = $initial . mb_substr($str, 1);
    return $ucfirst;
  }
}

if (! function_exists('capitalize')) {
  function capitalize($str) {
    return cucfirst(cstrtolower($str));
  }
}

if (! function_exists('toMoney')) {
  function toMoney($number) {
    return "$ " . number_format($number, 2);
  }
}

if (! function_exists('startsWith')) {
  function startsWith(string $str, string $char) {
    return strlen($str) > 0 and $str[0] === $char;
  }
}

if (! function_exists('strLimit')) {
  function strLimit(string $str, int $limit) {
    if (strlen($str) > $limit) {
      $str = trim(substr($str, 0, $limit)) . str_repeat('.', 3);
    }

    return $str;
  }
}

if (! function_exists('filterData')) {
  function filterData(array $data, $filter) {
    if (is_array($filter)) {
      $filterData = array_intersect_key($data, array_flip($filter));
      return $filterData;
    }
    return null;
  }
}

if (! function_exists('dbConnection')) {
  function dbConnection() {
    $conn = new mysqli(
      env('DB_HOST'), 
      env('DB_USER'),
      env('DB_PASSWORD'),
      env('DB_DATABASE')
    );

    if (mysqli_connect_errno()) {
      throw new Exception("Function \"dbConnection\": ".mysqli_connect_error());
    }

    if (! $conn->set_charset('utf8')) {
        throw new Exception("Function \"dbConnection\": Error loading character set utf8 - {$conn->error}");
    }
    return $conn;
  }
}

if (! function_exists('dbQuery')) {
  function dbQuery(string $query) {
    $conn   = dbConnection();
    $result = $conn->query($query . ';');
    $conn->close();

    return $result;
  }
}

if (! function_exists('dbMultiQuery')) {
  function dbMultiQuery() {
    $conn = dbConnection();
    $result = [];
    
    foreach (func_get_args() as $query) {
      $query = (string) $query;

      $result[] = $conn->query($query);
    }

    $conn->close();
    return $result;
  }
}

if (! function_exists('dbTransaction')) {
  function dbTransaction() {
    $conn         = dbConnection();
    $queries      = func_get_args();
    $all_query_ok = count($queries) > 0;
    $results      = [];

    $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    
    try {
      foreach ($queries as $query) {
        if (is_string($query)) {
          $result = $conn->query($query);
          $results[] = $result;

          if (! $result) {
            throw new Exception('MYSQL TRANSACTION FAILED');
          }
        } else if ($query instanceof Closure) {
          $query($conn, $results);
        }
      }
    } catch (Exception $e) {
      $all_query_ok = false;
      $conn->rollback();
    }

    $conn->commit();
    $conn->close();

    return (object) [
      'all_query_ok'  => $all_query_ok,
      'results'       => $results
    ];
  }
}

if (! function_exists('getCounter')) {
  function getCounter($queryResult) {
    if ($queryResult->num_rows > 0) {
      $row = $queryResult->fetch_assoc();
      $counter = (int) $row['counter'];

      return $counter;
    }
    return null;
  }
}

if (! function_exists('existsError')) {
  function existsError(string $key) {
    return isset($_SESSION['errors'][$key]);
  }
}

if (! function_exists('makeError')) {
  function makeError(string $key, $content) {
    if (existsError($key)) {
      array_push($_SESSION['errors'][$key], $content);
    } else {
      $_SESSION['errors'][$key] = [$content];
    }
  }
}

if (! function_exists('getError')) {
  function getError(string $key) {
    $content = $_SESSION['errors'][$key];
    unset($_SESSION['errors'][$key]);

    return $content;
  }
}

if (! function_exists('existsFlash')) {
  function existsFlash(string $key) {
    return isset($_SESSION['flash'][$key]);
  }
}

if (! function_exists('makeFlash')) {
  function makeFlash(string $key, $content) {
    $_SESSION['flash'][$key] = $content;
  }
}

if (! function_exists('getFlash')) {
  function getFlash(string $key) {
    $content = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $content;
  }
}

if (! function_exists('login')) {
  function login($email) {
    $_SESSION['auth'] = true;

    $_SESSION['user_data'] = [
      'email' => $email
    ];
  }
}

if (! function_exists('isLoggued')) {
  function isLoggued() {
    if (isset($_SESSION['auth']) and $_SESSION['auth'] and 
        ! empty($_SESSION['user_data']['email'])) {

      $result = dbQuery("SELECT count(*) as `counter` FROM `users` 
                         WHERE `users`.`email` = '{$_SESSION['user_data']['email']}'");

      return getCounter($result) == 1;
    }
    return false;
  }
}

if (! function_exists('logout')) {
  function logout() {
    unset($_SESSION['auth']);
    unset($_SESSION['user_data']);
  }
}

if (! function_exists('getUserData')) {
  function getUserData(array $data) {
    if (isset($_SESSION['auth']) and $_SESSION['auth'] and 
        ! empty($_SESSION['user_data']['email'])) {
      
      $fields = "";

      for ($i = 0; $i < count($data); ++$i) {
        if ($i) {
          $fields .= ", `users`.`{$data[$i]}`";
        } else {
          $fields .= "`users`.`{$data[$i]}`";
        }
      }

      $result = dbQuery("SELECT {$fields} FROM `users` 
                         WHERE `users`.`email` = '{$_SESSION['user_data']['email']}'");

      if ($result->num_rows == 1) {
        return $result->fetch_assoc();
      }
    }
    return null;
  }
}

if (! function_exists('getShortName')) {
  function getShortName(string $first_name, string $last_name) {
    $first_name = capitalize(explode(' ', $first_name)[0]);
    $last_name = capitalize(explode(' ', $last_name)[0]);

    return "{$first_name} {$last_name}";
  }
}

if (! function_exists('slugify')) {
  function slugify(string $text) {
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $text));
  }
}

/*
 * ***** Custom helpers specific for the App *****
 */
if (! function_exists('getImageSource')) {
  function getImageSource($image_id) {
    $result = dbQuery("SELECT `images`.`file_path`, 
                              `images`.`file_name`, 
                              `images`.`file_extension`
                       FROM `images` WHERE `images`.`id` = {$image_id}");

    if ($result->num_rows == 1) {
      $image = $result->fetch_assoc();

      if (startsWith($image['file_path'], '/')) {
        $image['file_path'] = substr($image['file_path'], 1);
      }

      $src = "{$image['file_path']}/{$image['file_name']}.{$image['file_extension']}";
      return $src;
    }
    return null;
  }
}

if (! function_exists('getCategory')) {
  function getCategory($category_id) {
    $result = dbQuery("SELECT `categories`.`name`, 
                              `categories`.`slug`
                       FROM `categories` WHERE `categories`.`id` = {$category_id}");

  if ($result->num_rows == 1) {
    $category = $result->fetch_assoc();

    return (object) $category;
  }
  return null;
  }
}

if (! function_exists('resetShoppingCart')) {
  function resetShoppingCart() {
    $_SESSION['shopping_cart'] = [];
  }
}

if (! function_exists('addToShoppingCart')) {
  function addToShoppingCart($plate_slug) {
    if (! isset($_SESSION['shopping_cart'])) {
      resetShoppingCart();
    }

    $amount = 1;

    if (in_array($plate_slug, array_keys($_SESSION['shopping_cart']))) {
      $amount = ((int) $_SESSION['shopping_cart'][$plate_slug]) + 1;

      $_SESSION['shopping_cart'][$plate_slug] = $amount;
    } else {
      $_SESSION['shopping_cart'] += [$plate_slug => $amount];
    }
  }
}

if (! function_exists('getShoppingCart')) {
  function getShoppingCart() {
    if (! isset($_SESSION['shopping_cart'])) {
      resetShoppingCart();
    }

    return $_SESSION['shopping_cart'];
  }
}

if (! function_exists('getShoppingCartDetails')) {
  function getShoppingCartDetails() {
    $shopping_cart = getShoppingCart() ?: [];$added_plates = [];
    $payment = null;

    $query = "";
    $i = 0;

    foreach($shopping_cart as $plate_slug => $amount) {
      if ($i > 0) {
        $query .= " UNION ALL ";
      }

      $query .= "SELECT `name`, `price` AS `unit_price`, 
                        {$amount} AS `amount`, (`price` * {$amount}) AS `total_price` 
                FROM `plates`
                WHERE `plates`.`slug` = '$plate_slug'";
      ++$i;
    }

    $result = dbQuery($query);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $added_plates[] = (object) $row;
      }
    }

    $query = "SELECT `subtotal`, `iva`, ".
                   "CAST((`subtotal` + ((`subtotal` * `iva`) / 100)) AS DECIMAL(6,2)) as `total` ".
           "FROM  (SELECT SUM(`added_plates`.`total_price`) AS `subtotal`, ".
                         "CAST(16.00 AS DECIMAL(6,2)) AS `iva` ".
                  "FROM ({$query}) AS `added_plates` ".
           ") as `payment`";

    $result = dbQuery($query);
  
    if ($result->num_rows > 0) {
      $payment = (object) $result->fetch_assoc();
    }

    return (object) [
      'added_plates'  => $added_plates,
      'payment'       => $payment
    ];
  }
}

if (! function_exists('getTotalPlatesAdded')) {
  function getTotalPlatesAdded() {
    $shopping_cart = getShoppingCart() ?: [];
    $total_plates = 0;
    
    foreach($shopping_cart as $plate_slug => $amount) {
      $total_plates += $amount;
    }

    return (int) $total_plates;
  }
}

if (! function_exists('getAllCategories')) {
  function getAllCategories() {
    $result     = dbQuery("SELECT * FROM `categories` WHERE 1");
    $categories = [];

    while ($row = $result->fetch_assoc()) {
      $categories[] = (object) $row;
    }

    return $categories;
  }
}
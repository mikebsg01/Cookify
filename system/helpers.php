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
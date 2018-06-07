<?php

if (! function_exists('env')) {
  function env($enviromentVariable, $defaultValue = null) {
    global $env;

    return (property_exists($env, $enviromentVariable) and ! empty($env->{$enviromentVariable})) ? $env->{$enviromentVariable} : $defaultValue;
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

if (! function_exists('authenticate')) {
  function authenticate($email) {
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

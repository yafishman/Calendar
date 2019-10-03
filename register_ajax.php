<?php
ini_set("session.cookie_httponly", 1);
SESSION_START();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $json_obj["username"];
$password = $json_obj["password"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calender');
$exists = True;
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
if(empty($username) or empty($password)) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please enter a username and password"
  ));
  exit;
} else {
  $stmt = $mysqli->prepare("select username from users");
  if(!$stmt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed"
    ));
    exit;
  }

  $stmt->execute();

  $stmt->bind_result($potential);
//checks to see if that login already exists
  while($stmt->fetch()){
    if ($potential==$username){
      echo json_encode(array(
        "success" => false,
        "message" => "Sorry but that username is taken"
      ));
      $exists = False;
      exit;
    }
  }
  if($exists) {
    //passwords and hashed and salted
    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
    if(!$stmt){
      echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed"
      ));
      exit;
    }

    $stmt->bind_param('ss', $username, $hashedPass);

    $stmt->execute();
    $_SESSION['username'] = $username;
    $tokens = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['tokens'] = $tokens;
    echo json_encode(array(
      "success" => true,
      "tokens" => $tokens
    ));
    exit;
  }
}
?>

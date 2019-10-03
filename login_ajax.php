<?php
//http only
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$username = $json_obj["username"];
$password = $json_obj["password"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calender');

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
  $stmt = $mysqli->prepare("select username,password from users");

  if(!$stmt){
    echo json_encode(array(
      "success" => false,
      "message" => "Query Prep Failed"
    ));
    exit;
  }

  $stmt->execute();

  $stmt->bind_result($potUser, $potPass);
  //checks to see if the user logged in with correct credentials
  while($stmt->fetch()){
    if(password_verify($password,$potPass) && ($username==$potUser)) {
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
        echo json_encode(array(
          "success" => false,
          "message" => "Incorrect Username or Password"
        ));
        exit;
  }
?>

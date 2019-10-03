<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
SESSION_START();
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Protect against CSRF attacks
if(!hash_equals($_SESSION['tokens'], $json_obj["tokens"])) {
  die("Request forgery detected");
}
$id = $json_obj["id"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calender');

if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}

//$stmt = $mysqli->prepare("delete from plans where author=? and title=? and hour=? and minute=? and day=? and month=? and year=?");
$stmt = $mysqli->prepare("delete from plans where id=?");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
//$stmt->bind_param('ssiiiii', $username, $plan, $hour,$minute,$day,$month,$year);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
echo json_encode(array(
  "success" => true
));
exit;
?>

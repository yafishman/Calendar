<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
SESSION_START();
//Event information is taken from front-end
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$day = $json_obj["day"];
$month = $json_obj["months"];
$year = $json_obj["years"];
$hour = $json_obj["hour"];
$minute = $json_obj["minute"];
$username = $_SESSION['username'];
$plan = $json_obj["plan"];
$category= $json_obj["category"];
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calender');
$daysOfMonth = array("31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31");

if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}
//checks if person is logged in
if($username=='') {
  echo json_encode(array(
    "success" => false,
    "message" => "Log in to an account to add events"
  ));
}elseif(empty($plan) ) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please fill out all fields"
  ));
  exit;
  //checks if day # selected is within month
} elseif($day > $daysOfMonth[$month]) {
  echo json_encode(array(
    "success" => false,
    "message" => "Please enter a valid date"
  ));
  exit;
}else {
  //inserts into mysql database
    $stmt = $mysqli->prepare("insert into plans (title, author,hour,minute,day,month,year,category) values (?,?,?,?,?,?,?,?)");
    if(!$stmt){
      echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed"
      ));
      exit;
    }
    //binds with specific types
    $stmt->bind_param('ssiiiiis', $plan, $username, $hour,$minute,$day,$month,$year,$category);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array(
      "success" => true
    ));
    exit;
}
?>

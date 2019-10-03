<?php
ini_set("session.cookie_httponly", 1);
session_start();
$username = $_SESSION['username'];
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calender');
if($mysqli->connect_errno) {
  echo json_encode(array(
    "success" => false,
    "message" => "No connection found"
  ));
  exit;
}

$stmt = $mysqli->prepare("select id,title,hour,minute,day,month,year,category from plans where author=?");
if(!$stmt){
  echo json_encode(array(
    "success" => false,
    "message" => "Query Prep Failed"
  ));
  exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($id,$title,$hour,$minute,$day,$month,$year,$category);
$events = array();
//htmlentities prevents XSS attacks
//creates an array of events
while (($stmt->fetch())) {
  $event = array(
    "id" => htmlentities($id),
    "title" => htmlentities($title),
    "hour" => htmlentities($hour),
    "minute" => htmlentities($minute),
    "day" => htmlentities($day),
    "month" => htmlentities($month),
    "year" => htmlentities($year),
    "category" => htmlentities($category)
  );
  array_push($events, $event);
}

echo json_encode(array(
  "success" => true,
  "events" => $events
));
exit;
?>

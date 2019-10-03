<?php
//kills session to prevent a logged out user from doing anything with cal
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
session_start();
session_destroy();
?>

<?php

session_start();
require_once 'PostNewTarget.php';
require_once 'fsq.php';

$data = file_get_contents('php://input');
$data = json_decode($data,true);

$date = date('mdY_h:i:s', time());
$areaid = md5($data['userid'].$date);
$data["areaid"]=$areaid;
$_SESSION['data'] = json_encode($data);

new fsq();

echo "Targets/".$data['userid']."/".$data["areaid"].".jpg";
?>
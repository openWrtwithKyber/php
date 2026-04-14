<?php
$file = "users.json";
$users = json_decode(file_get_contents($file), true);

$id = $_POST['id'];
$pw = $_POST['pw'];
$name = $_POST['name'];
$phone = $_POST['phone'];

if (!isset($users[$id])) {
    $users[$id] = ["pw"=>$pw,"name"=>$name,"phone"=>$phone];
    file_put_contents($file, json_encode($users));
}

header("Location: main1.php");
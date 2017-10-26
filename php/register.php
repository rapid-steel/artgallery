<?php

$db = new mysqli('localhost', 'root', '', 'gallery') or die('Connecting Database gallery Error');
$db->query('SET NAMES "utf8"');

$data = json_decode(file_get_contents('php://input'), $assoc = true);

$key = $data['attr_key'];
$value = $data['attr_value'];

$res = $db->query("SELECT * FROM users WHERE $key ='$value'")->fetch_all(MYSQLI_ASSOC);

if($res)
    echo 0;
else
    echo 1;
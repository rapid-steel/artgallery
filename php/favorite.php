<?php

$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');


SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'POST':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        $res = $db->query("INSERT INTO favorites (user_id, image_id) 
                            values ('$data[user_id]', '$data[image_id]')");
        $id = $db->insert_id;
        $db->query("UPDATE images SET fav_n = fav_n + 1 WHERE image_id = '$data[image_id]'");
        $user_id = $db->query("SELECT user_id FROM images WHERE image_id = '$data[image_id]'")->fetch_row()[0];

        $db->query("INSERT INTO system_messages (user_id, type, id) values ('$user_id', 'favorites', '$id')");

        echo json_encode($res);
        break;


    case 'DELETE':

        $array = explode('user_id=', explode('&',$_SERVER['REQUEST_URI'])[0]);
        $user_id = array_pop($array);

        $array = explode('image_id=',$_SERVER['REQUEST_URI']);
        $image_id = array_pop($array);


        $res = $db->query("DELETE FROM favorites WHERE user_id = $user_id AND image_id = $image_id");
        $db->query("UPDATE images SET fav_n = fav_n - 1 WHERE image_id = '$image_id'");
        echo json_encode($res);
        break;

}


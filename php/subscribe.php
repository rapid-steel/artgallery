<?php

$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');


SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'POST':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        $res = $db->query("INSERT INTO subscribes (subscriber_id, user_id) 
                                values ('$data[sub_id]', '$data[user_id]')");
        $id = $db->insert_id;

        $db->query("INSERT INTO system_messages (user_id, type, id) values ('$data[user_id]', 'subscribes', '$id')");

        echo json_encode($res);
        break;


    case 'DELETE':

        $array = explode('user_id=', explode('&',$_SERVER['REQUEST_URI'])[0]);
        $user_id = array_pop($array);

        $array = explode('sub_id=',$_SERVER['REQUEST_URI']);
        $sub_id = array_pop($array);

        $res = $db->query("DELETE FROM subscribes WHERE user_id = '$user_id' AND subscriber_id = '$sub_id'");
        echo json_encode($res);
        break;

}
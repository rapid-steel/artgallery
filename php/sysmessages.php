<?php

$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');

SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'GET':

        $res = $db->query("SELECT * FROM system_messages WHERE user_id = '$_GET[user_id]'")->fetch_all(MYSQLI_ASSOC);

        foreach($res as $index => $message) {


            SWITCH ($message['type']) {

                case 'subscribes':

                    $attr = $db->query("SELECT s.*, u.avatar, u.name FROM subscribes s 
                                                  LEFT JOIN users u ON s.subscriber_id = u.user_id 
                                                  WHERE s.sub_id = '$message[id]'")
                        ->fetch_assoc();

                    break;

                case 'favorites':

                    $attr = $db->query("SELECT f.*, u.name, u.avatar, i.title FROM favorites f 
                                                    LEFT JOIN users u ON f.user_id = u.user_id                                                      
                                                    LEFT JOIN images i ON f.image_id = i.image_id
                                                    WHERE f.fav_id = '$message[id]'")
                        ->fetch_assoc();

                    break;

                case 'comments';
                case 'answers':

                    $attr = $db->query("SELECT c.*, u.avatar, u.name, i.title FROM comments c
                                                   LEFT JOIN users u ON c.user_id = u.user_id  
                                                   LEFT JOIN images i ON c.image_id = i.image_id
                                                   WHERE c.comment_id = '$message[id]'")
                        ->fetch_assoc();
                    break;

                case 'images':
                    $attr = $db->query("SELECT i.*, u.avatar, u.name FROM images i
                                                   LEFT JOIN users u ON i.user_id = u.user_id  
                                                   WHERE i.image_id = '$message[id]'")
                        ->fetch_assoc();
                    break;
            }

            $res[$index]['attr'] = $attr;
        }

        echo json_encode($res);

        break;

    case 'DELETE':

        $array = explode('sm_id=',$_SERVER['REQUEST_URI']);
        $sm_id = array_pop($array);

        $res = $db->query("DELETE FROM system_messages WHERE sm_id = $sm_id");
        echo json_encode($res);

        break;


}
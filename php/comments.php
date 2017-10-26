<?php


$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');

SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'GET':

        $data = $db->query("SELECT c.*, u.name, u.avatar FROM comments c 
                            LEFT JOIN users u ON c.user_id = u.user_id 
                            WHERE c.image_id='$_GET[image_id]' ORDER BY c.date")->fetch_all(MYSQLI_ASSOC);

        echo json_encode($data);

        break;

    case 'POST':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        $keys = '';
        $values = '';

        foreach ($data as $key => $value) {
            $keys = $keys.$key.', ';
            $values=$values."\"$value\", ";
        }

        $keys = $keys.'date';
        $values = $values.' NOW()';

        $db->query("INSERT INTO comments ($keys) values ($values)");
        $data['comment_id'] = $db->insert_id;

        $db->query("UPDATE images SET com_n = com_n + 1 WHERE image_id = '$data[image_id]'");
        $data['date'] = $db->query("SELECT date FROM comments WHERE comment_id = '$data[comment_id]'")->fetch_row()[0];
        $data['name'] = $db->query("SELECT name FROM users WHERE user_id = '$data[user_id]'")->fetch_row()[0];
        $data['avatar'] = $db->query("SELECT avatar FROM users WHERE user_id = '$data[user_id]'")->fetch_row()[0];



        if ($data['answerTo']==0) {
            $user_id = $db->query("SELECT user_id FROM images WHERE image_id = '$data[image_id]'")->fetch_row()[0];
            $type = 'comments';
        } else {
            $user_id = $db->query("SELECT user_id FROM comments WHERE comment_id = '$data[answerTo]'")->fetch_row()[0];
            $type = 'answers';
        }

        $db->query("INSERT INTO system_messages (user_id, type, id) values ('$user_id', '$type', '$data[comment_id]')");

        echo json_encode($data);

        break;

    case 'PUT':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        $db->query("UPDATE comments SET body = \"$data[body]\" WHERE comment_id = $data[comment_id]");
        $data['body'] = $db->query("SELECT body FROM comments WHERE comment_id = '$data[comment_id]'")->fetch_row()[0];

        echo json_encode($data);


        break;

    case 'DELETE':

        $array = explode('=',$_SERVER['REQUEST_URI']);
        $id = array_pop($array);

        $image_id = $db->query("SELECT image_id FROM comments WHERE comment_id = $id")->fetch_row()[0];
        $db->query("DELETE FROM comments WHERE comment_id = $id");
        $db->query("UPDATE images SET com_n = com_n - 1 WHERE image_id = '$image_id'");

        echo json_encode($id);

        break;

}
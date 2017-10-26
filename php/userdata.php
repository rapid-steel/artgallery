<?php

$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');

SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'GET':

        $user = $db->query("SELECT name, avatar, birthdate, location, specialisation, profile,
                                            bio, interests, fav_artists, fav_techniques, inspiration
                                      FROM users WHERE user_id = $_GET[user_id]")->fetch_assoc();

        $birthdate = date_parse_from_format('Y-m-d', $user['birthdate']);
        $now = getdate();

        $user['age'] = $now['year'] - $birthdate['year'];

        if ( ($birthdate['month']>$now['mon']) ||
            ($birthdate['month']==$now['mon'] && $birthdate['day']>$now['mday'])) {
            $user['age']--;
        }

        echo json_encode($user);

        break;

    case 'PUT':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        $default = Array('avatar' => 'no_avatar.png', 'profile' => 'default.jpg');

        foreach ($default as $key => $default_value) {
            if($data[$key]!=$default_value)
                $data[$key] = removeImage($data[$key], $key, $data['name']);
        }

        $user_id = $data['user_id'];

        unset($data['name']);
        unset($data['user_id']);

        $set = '';

        foreach ($data as $key => $value) {
            if ($value)
                $set .= "$key = \"$value\", ";
        }

        $set = substr($set, 0, -2);

        echo json_encode($db->query("UPDATE users SET $set WHERE user_id = '$user_id' "));

        break;


}

function removeImage($imageName, $imageType, $userName) {
    $old_folder = 'C:/wamp64/www/mein/artgallery/tmp/';
    $folder = "C:/wamp64/www/mein/artgallery/content/users/$imageType/";
    $array = explode('.',$imageName);
    $type = array_pop($array);
    $name = iconv("UTF-8", "windows-1251", $userName)."_$imageType.$type";
    $old_path = $old_folder.$imageName;
    $path = $folder.$name;

    if(rename($old_path, $path))
        return $name;
    else return false;


}
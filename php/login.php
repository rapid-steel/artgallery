<?php

$array = explode(':',$_COOKIE['artgallery']);
$login = $array[0];
$password = $array[1];

$db = new mysqli('localhost', 'root', '', 'gallery') or die('Connecting Database gallery Error');
$db->query('SET NAMES "utf8"');

$password_true = $db->query("SELECT password FROM users WHERE login like '$login'")->fetch_row();

if ($password_true) {
    if ($password==$password_true[0]) {

        $user = $db->query("SELECT login, user_id, name, email, avatar FROM users WHERE login like '$login'")->fetch_assoc();

        $user['favorites'] = Array();
        $user['subscribes'] = Array();

        foreach ($db->query("SELECT image_id FROM favorites WHERE user_id = '$user[user_id]'")
                     ->fetch_all(MYSQLI_NUM) as $fav)
        {
            array_push($user['favorites'], $fav[0]);
        }

        foreach ($db->query("SELECT user_id FROM subscribes WHERE subscriber_id = '$user[user_id]'")
                     ->fetch_all(MYSQLI_NUM) as $sub)
        {
            array_push($user['subscribes'], $sub[0]);
        }

        $user['sys_messages_count'] =
            $db->query("SELECT COUNT(*) FROM system_messages 
                        WHERE user_id = '$user[user_id]' 
                        AND (type = 'favorites' OR type = 'comments' OR type = 'answers' OR type = 'subscribes')")
            ->fetch_row()[0];

        $user['subscribes_count'] =
            $db->query("SELECT COUNT(*) FROM system_messages 
                        WHERE user_id = '$user[user_id]' 
                        AND type = 'images'")
                ->fetch_row()[0];

        $json_encode_user = json_encode($user);


        echo "<input id='user' type='hidden' value='$json_encode_user'>";
        include('html/content.html');
    } else {
        include('html/logform.html');
    }
} else {
    include('html/logform.html');
}
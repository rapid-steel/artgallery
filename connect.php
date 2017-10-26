<?php

$db = new mysqli('localhost', 'root', '', 'gallery') or die('Connecting Database gallery Error');
$db->query('SET NAMES "utf8"');

$data = json_decode(file_get_contents('php://input'), $assoc = true);

$mode = $data['mode'];
unset($data['mode']);

switch($mode) {

    case 'login':

        $password = $db->query("SELECT password FROM users WHERE login like '$data[login]'")->fetch_row();

        if ($password) {
            if (password_verify($data['password'], $password[0])) {
                setcookie('artgallery', "$data[login]:$password[0]", time()+60*60*24*30);
                echo 'login';

            }
            else {
                echo 'Incorrect password';
            }
        }
        else {
            echo 'No such user is registered';
        }




        break;

    case 'register':

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $keys = '';
        $values = '';
        foreach ($data as $key => $value) {
            $keys .= "$key, ";
            $values .= "'$value', ";
        }

        $keys = substr($keys, 0, -2);
        $values = substr($values, 0, -2);

        $db->query("INSERT INTO users ($keys) values ($values)");

        break;

}
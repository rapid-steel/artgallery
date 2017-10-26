<?php


    $folder = 'C:/wamp64/www/mein/artgallery/tmp/';
    $type = str_replace('image/','',$_FILES['image']['type']);
    $name = uniqid('', true);
    $path = "$folder$name.$type";


    if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {

        echo "$name.$type";
    }

    else echo 0;

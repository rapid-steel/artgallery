<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Art Gallery</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php

if (array_key_exists('artgallery', $_COOKIE)) {
    include('php/login.php');

} else {
    include('html/logform.html');
}

?>


</body>
</html>
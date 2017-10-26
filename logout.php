<?php

setcookie('artgallery', '', time()-3600);

header('Location: http://localhost/mein/artgallery/index.php');
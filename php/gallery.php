<?php


$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');

SWITCH ($_GET['mode']){

    case 'search':

        $search = '%'.mb_strtolower($_GET['search']).'%';

        $res = $db -> query("SELECT i.image_id, i.title, i.filename, u.name, u.user_id FROM images i 
                             LEFT JOIN users u ON i.user_id = u.user_id
                             WHERE LOWER(i.title) LIKE '$search' OR 
                             LOWER(i.description) LIKE '$search' OR 
                                    LOWER(i.tags) LIKE '$search' 
                             LIMIT $_GET[limit] OFFSET $_GET[offset]")->fetch_all(MYSQLI_ASSOC);

        break;

    case 'sub':

     $res = $db -> query("SELECT s.id, s.sm_id, i.image_id, i.title, i.filename, u.name, u.user_id FROM system_messages s 
                                 LEFT JOIN images i ON s.id = i.image_id
                                 LEFT JOIN users u ON i.user_id = u.user_id
                                 WHERE s.user_id = '$_GET[user_id]' AND s.type = 'images'
                                 LIMIT $_GET[limit] OFFSET $_GET[offset]")->fetch_all(MYSQLI_ASSOC);

        break;


    case 'fav':

        $string = "f.user_id = '$_GET[user_id]'";

        if ($_GET['tag']!='')
            $string = "$string AND ( i.tags LIKE '%$_GET[tag]; %' OR i.tags LIKE '%$_GET[tag]' ) ";



        $res = $db -> query("SELECT f.image_id, i.image_id, i.title, i.filename, u.name, u.user_id FROM favorites f 
                                 LEFT JOIN images i ON f.image_id = i.image_id
                                 LEFT JOIN users u ON i.user_id = u.user_id
                                 WHERE $string
                                 LIMIT $_GET[limit] OFFSET $_GET[offset]")->fetch_all(MYSQLI_ASSOC);

        break;

    default:

        $string = '1';
        $order = '';

        if($_GET['user_id']!=-1)
            $string = "i.user_id = '$_GET[user_id]'";

        if ($_GET['order']!='')
            $order = " ORDER BY $_GET[order] DESC";


        if ($_GET['tag']!='') {
            if($string='1')
                $string = "i.tags LIKE '%$_GET[tag]; %' OR i.tags LIKE '%$_GET[tag]'";
            else
                $string = "$string AND ( i.tags LIKE '%$_GET[tag]; %' OR i.tags LIKE '%$_GET[tag]' ) ";
        }

        $res = $db -> query("SELECT i.image_id, i.title, i.filename, u.name, u.user_id FROM images i 
                             LEFT JOIN users u ON i.user_id = u.user_id
                             WHERE $string $order
                             LIMIT $_GET[limit] OFFSET $_GET[offset]")->fetch_all(MYSQLI_ASSOC);


        break;

}

        echo json_encode($res);



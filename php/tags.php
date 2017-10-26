<?php
$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');

       SWITCH ($_GET['mode']) {

           case 'fav':

             $imgs = $db -> query("SELECT i.tags FROM favorites f 
                                    LEFT JOIN images i ON i.image_id = f.image_id 
                                    WHERE f.user_id = '$_GET[user_id]'")->fetch_all(MYSQLI_ASSOC);

               break;

           default:

               if($_GET['user_id']==-1) {
                   $string = '1';
               } else {
                   $string = "user_id = $_GET[user_id]";
               }
               $imgs = $db -> query("SELECT tags FROM images WHERE $string")->fetch_all(MYSQLI_ASSOC);

               break;

       };





        $tags = Array();
        $quantity = Array();

        foreach ($imgs as $img) {
            $img_tags = explode('; ', $img['tags']);
            foreach ($img_tags as $tag) {
                if(!in_array($tag, $tags)) {
                    array_push($tags, $tag);
                    $quantity[$tag] = 0;
                }
                $quantity[$tag]++;
            }
        }

        arsort($quantity);

        echo json_encode(array_slice($quantity, 0, 50));


<?php
$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');



SWITCH ($_SERVER['REQUEST_METHOD']) {

    case 'GET':

        $img = $db->query("SELECT i.*, u.user_id, u.name FROM images i 
                    LEFT JOIN users u ON i.user_id = u.user_id WHERE i.image_id = '$_GET[image_id]'")->fetch_assoc();

        echo json_encode($img);

        break;


    case 'POST':



            $data = json_decode(file_get_contents('php://input'), $assoc = true);

            $name = loadFile($data['filename'], $data['title'], $data['name']);

            if ($name) {

                $tags = $data['tags'];
                if (substr($tags, -1) == ';') {
                    $tags = substr_replace($tags, '', -1);
                }


                $keys = "(title, filename, user_id, date, tags, description)";
                $values = "(\"$data[title]\", \"$name\", \"$data[user_id]\", NOW(), \"$tags\", \"$data[description]\")";

                $db->query("INSERT INTO images $keys values " . $values);
                $id = $db->insert_id;

                foreach($db->query("SELECT subscriber_id FROM subscribes WHERE user_id = $data[user_id]")
                            ->fetch_all(MYSQLI_NUM) as $sub_id) {
                    $db->query("INSERT INTO system_messages (user_id, id, type) values ('$sub_id[0]', '$id', 'images')");
                }

                $resp['id'] = $id;

                echo json_encode($resp);
            }



        break;

    case 'PUT':

        $data = json_decode(file_get_contents('php://input'), $assoc = true);

        if ($data['filename'])
            $name = loadFile($data['filename'], $data['title'], $data['name']);
        else
            $name = $db->query("SELECT filename FROM images WHERE image_id = '$data[image_id]'")->fetch_row()[0];

        $tags = $data['tags'];
        if (substr($tags, -1) == ';') {
            $tags = substr_replace($tags, '', -1);
        }

        $set = "title = \"$data[title]\", filename = \"$name\", tags = \"$tags\", description = \"$data[description]\"";


        $db->query("UPDATE images SET $set WHERE image_id = '$data[image_id]'");

        echo $data['image_id'];

        break;


    case 'DELETE':

        $array = explode('=',$_SERVER['REQUEST_URI']);
        $image_id = array_pop($array);
        $folder = 'C:/wamp64/www/mein/artgallery/content/';

        $db->query("DELETE FROM favorites WHERE image_id = '$image_id'");
        $db->query("DELETE FROM comments WHERE image_id = '$image_id'");
        $name = $db->query("SELECT filename FROM images WHERE image_id = '$image_id'")->fetch_row();

        unlink($folder.iconv("UTF-8", "windows-1251", $name));
        unlink($folder."thumbnails/thumb_".iconv("UTF-8", "windows-1251", $name));


        $res = $db->query("DELETE FROM images WHERE image_id = '$image_id'");

        echo json_encode($res);

        break;
}


function loadFile ($old_name, $title, $username)
{
    $old_folder = 'C:/wamp64/www/mein/artgallery/tmp/';
    $folder = 'C:/wamp64/www/mein/artgallery/content/';
    $array = explode('.',$old_name);
    $type = array_pop($array);
    $name = iconv("UTF-8", "windows-1251", $title)." by $username.$type";
    $old_path = $old_folder.$old_name;
    $path = $folder.$name;


    if (rename($old_path, $path)) {


        $thumbnail_path = $folder."thumbnails/thumb_$name";

        list($oldwidth, $oldheight) = getimagesize($path);

        $height = 300;
        $width = $oldwidth / $oldheight * $height;

        $thumbnail = imagecreatetruecolor($width, $height);

        SWITCH ($type) {
            case 'jpeg':
                $source = @imagecreatefromjpeg($path);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);
                imagejpeg($thumbnail, $thumbnail_path, 100);
                break;
            case 'png':
                $source = @imagecreatefrompng($path);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);
                imagepng($thumbnail, $thumbnail_path);
                break;
            case 'gif':
                $source = @imagecreatefromgif($path);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);
                imagegif($thumbnail, $thumbnail_path);
                break;
        }

        @imagedestroy($thumbnail);
        @imagedestroy($source);


        return "$title by $username.$type";
    }

    else return false;
}


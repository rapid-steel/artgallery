<?php


$db = new mysqli('localhost','root','','gallery') or die('Connecting Database music Error');
$db->query('SET NAMES "utf8"');



$data = $db->query("SELECT * FROM comments WHERE image_id=11")->fetch_all(MYSQLI_ASSOC);

$data_sort = Array();


for ($index = 0; $index <= count($data); $index++) {

    array_push($data_sort, $data[$index]);

    $data_copy = $data;
    array_splice($data_copy, $index, 1);

    echo json_encode($data).'____________________________';


    $answers = Array();


    foreach ($data_copy as $indexAnswer => $valueAnswer) {
        if($data[$index]['comment_id']==$valueAnswer['answerTo']) {
            array_push($answers, $valueAnswer);


            array_splice($data_copy, $indexAnswer, 1);
        }
    }

    $data = array_merge($answers, $data_copy);
}

//echo json_encode($data_sort);
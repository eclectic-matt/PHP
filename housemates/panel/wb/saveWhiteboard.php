<?php

if (isset($_POST)){
    var_dump($_POST);
    file_put_contents('whiteboardData.txt',$_POST['img']);
    echo 'Whiteboard data saved';
}

?>
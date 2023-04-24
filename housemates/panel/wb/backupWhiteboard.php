<?php

if (isset($_POST)){
    var_dump($_POST);
	$date = date('Ymd_His', time());
	$backupTitle = './backups/whiteboardBackup' . $date . '.txt';
	$fh = fopen($backupTitle,'w') or die("Unable to open file!");;
	fwrite($fh, $_POST['img']);
	fclose($fh);
    //file_put_contents($backupTitle,$_POST['img']);
    echo 'Whiteboard data (' . $backupTitle . ') backed up';
}

?>
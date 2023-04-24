<?php
/*$todoFileName = 'todo.json';
$todoFile = file_get_contents($todoFileName);
$todoJson = json_decode($todoFile);
$taskCount = count($todoJson->list);
$classLow = 'w3-grey';
$classMedium = 'w3-white';
$classUrgent = 'w3-red';
$classCompleted = 'w3-black';*/

require_once 'classes/TodoList.class';
$todo = new TodoList();

//ADD TASK
if (isset($_POST)){
	
	//var_dump($_POST);

	if (count($_POST) > 0){

		echo '<div class="w3-red w3-center w3-small">';
		echo '<h2>Marked todo(s) completed!</h2>';


		$todo->markItemsAsCompleted($_POST);


		//echo '<em>' . implode(', ', array_keys($_POST)) . '</em>';

		/*
		foreach($_POST as $checked => $status){

			if (strpos($checked, '_') !== false){
				//DEALING WITH SUBTASK
				$checkId = str_ireplace('check', '', $checked);
				$mainId = substr($checkId,0,strpos($checked, '_'));
				$subtaskId = str_ireplace('_', '.', $checkId);
				//$subId = substr($checkId,strpos($checked, '.'));
				foreach ($todoJson->list as $item){

					if (intval($item->id) === intval($mainId)){

						foreach ($item->subtasks as $task){

							if ($task->id == $subtaskId){

								$task->status = 'completed';
								$task->completedDate = date('Y-m-d H:i:s', time());
								echo '<em>Subtask "' . $task->title . '" marked as completed!</em><br>';
							}
						}
					}
				}

			}else{

				$checkId = str_ireplace('check', '', $checked);
				foreach ($todoJson->list as $item){

					if (intval($item->id) === intval($checkId)){

						$item->status = 'completed';
						$item->completedDate = date('Y-m-d H:i:s', time());
						echo '<em>Task "' . $item->title . '" marked as completed!</em><br>';
					}
				}
			}
		}

		echo '<br><br>';
		echo '</div>';
		//SAVE BACK INTO THE JSON FILE
		$newTodoJson = json_encode($todoJson);
		file_put_contents($todoFileName, $newTodoJson);	
		*/
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="./css/todoStyles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Complete ToDo(s)</title>
<style>

</style>
<script src='../../js/browser.js'></script>
<script src='./js/todoUpdate.js'></script>
</head>
<body class='w3-dark-grey' onload='detectIframe()'>
<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
<?php include 'navbar.php'; ?>
</div>

<div class='w3-container w3-green'>

	<h2>Complete ToDo(s)</h2>

	<form id='todo' name='todo' action='' method='POST'>


<?php
	//echo '<h1 class="w3-center">Outstanding Items</h1>';
	//outputOutstandingItems($todoJson);
	//echo $todo->todoJson;
	$todo->outputItemsForMarkingCompletion();
?>

		<br><br>

		<button class='w3-btn w3-block w3-purple w3-xxlarge' type='submit'>Save Changes</button>

		<br><br>

	</form>

</div>
</body>
</html>
<?php

function outputOutstandingItems($todoJson){

	global $classLow, $classMedium, $classUrgent;

	foreach ($todoJson->list as $item){

		if ($item->status !== "waiting") continue;

		switch ($item->urgency){
			case 0:
				//LOW
				echo '<div class="w3-container ' . $classLow . '">';
				echo '<h2>';
				echo '<input name="check' . $item->id . '" type="checkbox" class="w3-check"></input>&nbsp;&nbsp;&nbsp;';
				echo '<img src="icons/rate.png" width="25" height="25" />&nbsp;&nbsp;&nbsp;';
			break;
			case 2:
				//HIGH
				echo '<div class="w3-container ' . $classUrgent . '">';
				echo '<h2>';
				echo '<input name="check' . $item->id . '" type="checkbox" class="w3-check"></input>&nbsp;&nbsp;&nbsp;';
				echo '<img src="icons/warn.png" width="25" height="25" />&nbsp;&nbsp;&nbsp;';
			break;
			case 1:
			default:
				//MEDIUM
				echo '<div class="w3-container ' . $classMedium . '">';
				echo '<h2>';
				echo '<input name="check' . $item->id . '" type="checkbox" class="w3-check"></input>&nbsp;&nbsp;&nbsp;';
				echo '<img src="icons/list.png" width="25" height="25" />&nbsp;&nbsp;&nbsp;';
			break;
		}
		
		echo $item->title . '</h2>';
		//echo '<em>Added by: ' . $item->addedBy . '</em>';
		if (isset($item->subtasks)){
			//echo '<ul>';
			foreach ($item->subtasks as $task){
				if ($task->status !== "completed"){
					echo '<div class="w3-container w3-padding-16">';
					echo '<input name="check' . $task->id . '" type="checkbox" class="w3-check"></input>&nbsp;&nbsp;&nbsp;';
					echo $task->title . '</div>';
				}
			}
			//echo '</ul>';
		}
		echo '</div>';

	}
}


?>
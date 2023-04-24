<?php
/*
//OLD CLASS-LESS VERSION
$todoFileName = 'todo.json';
$todoFile = file_get_contents($todoFileName);
$todoJson = json_decode($todoFile);
$taskCount = count($todoJson->list);
$lists = groupItemsByList($todoJson);
$listNames = array_keys($lists);
$classLow = 'w3-grey';
$classMedium = 'w3-white';
$classUrgent = 'w3-red';
$classCompleted = 'w3-black';
*/

require_once 'classes/TodoList.class';
$todo = new TodoList();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Add ToDo(s)</title>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="./css/todoStyles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<style>
		.todoListNameSpan{
			left: 40px;
			padding-top: -20px;
			padding-left: 5px;
			padding-right: 5px;
			background-color: black;
			color: white;
			border: 1px solid red;
			font-style: italic;
			position: absolute;
		}
		img {
			margin-top: 20px !important;
			margin-left: 10px !important;
		}

		.waitingItem{
			
		}

		.completedItem{
			text-decoration: line-through;
			color: red;
		}

	</style>
	<script src='../../js/browser.js'></script>
	<script>
		function showNewListTitle(value){
			if (value === 'New List Title (enter below)'){
				document.getElementById('newListTitleInput').classList.remove('w3-hide');
			}else{
				document.getElementById('newListTitleInput').classList.add('w3-hide');
			}
		}
	</script>
</head>
<body class='w3-dark-grey' onload='detectIframe()'>
<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
<?php include 'navbar.php'; ?>
</div>

<?php

//ADD TASK
if (isset($_POST['title'])){
	if (!isset($_POST['addedBy']) || (!isset($_POST['urgency']))){
		echo '<div class="w3-red w3-center w3-small">';
		echo '<h2>Invalid Input - please try again!</h2>';
		echo '</div>';
	}else{
		echo '<div class="w3-red w3-center w3-small">';
		echo '<h2>Received todo input!</h2>';

		//NEW CLASS-BASED VERSION
		$todo->saveNewTask($_POST);
		
		/*
		//var_dump($_POST);
		//SAVE THAT BAD BOY
		$todo = new stdClass();
		$todo->id = $taskCount;
		$todo->title = htmlspecialchars($_POST['title']);
		if ($_POST['listName'] === 'New List Title (enter below)'){
			if ($_POST['list'] === ''){
				$todo->list = 'Generic';	
			}else{
				$todo->list = htmlspecialchars($_POST['list']);
			}
		}else{
			$todo->list = htmlspecialchars($_POST['listName']);
		}
		$todo->addedBy = htmlspecialchars($_POST['addedBy']);
		$todo->addedDate = date('Y-m-d H:i:s', time());
		$todo->urgency = htmlspecialchars($_POST['urgency']);
		$todo->status = 'waiting';
		if (isset($_POST['subtasks'])){
			$todo->subtasks = array();
			$index = 1;
			foreach ($_POST['subtasks'] as $task){
				$newTask = new stdClass();
				$newTask->id = 	$todo->id . '.' . $index;
				$newTask->title = htmlspecialchars($task['title']);
				$newTask->status = 'waiting';
				$todo->addedBy = htmlspecialchars($_POST['addedBy']);
				$newTask->addedDate = date('Y-m-d H:i:s', time());
				$todo->subtasks[] = $newTask;
				$index++;
			}
		}
		
		$todoJson->list[] = $todo;
		$newTodoJson = json_encode($todoJson);
		file_put_contents($todoFileName, $newTodoJson);
		*/


		echo '<h2>Saved new task!</h2>';
		echo '</div>';
	
	}

}

?>


<div class='w3-container w3-purple'>

	<h2>Add a new ToDo</h2>
	<form id='todo' name='todo' action='' method='POST'>

		<label for='title'>Title: </label>
		<input class='w3-input' name='title' type='text' placeholder='What needs to be done?'></input>
		
		<br>

		<!--label for='addedBy'>Added By: </label>
		<select class='w3-select' name='addedBy'>
			<option selected disabled>--PLEASE SELECT--</option>
			<option value='Matt T'>Matt T</option>
			<option value='Naomi L'>Naomi L</option>
			<option value='Rich L'>Rich L</option>
			<option value='Tom W'>Tom W</option>
			<option value='Other'>Other</option>
		</select-->
		<input type='hidden' name='addedBy' value='Matt T' />
		<input type='hidden' name='urgency' value='1' />

		<!--br><br-->

		<label for='list'>Add to List: </label>
		<select class="w3-select" name="listName" onchange="showNewListTitle(this.value)">
		<?php
			foreach ($todo->getListNames() as $listTitle){
				echo '<option value="' . $listTitle . '" ';
				/*if ($listTitle === $listNames[0]){
					echo 'selected';
				}*/
				echo '>' . $listTitle . '</option>';
			}
		?>
		<option value="New List Title (enter below)">New List Title (enter below)</option>
		</select>
		<input id="newListTitleInput" class='w3-hide w3-input' name='list' placeholder='New List Title'></input>

		<br><br>

		<!--label for='urgency'>Urgency: </label>
		<select class='w3-select' name='urgency'>
			<option selected value='1'>Normal</option>
			<option value='2'>Urgent</option>
			<option value='0'>Low</option>
		</select-->

		<br><br>

		<!--button onclick='addSubtask()'>Add Subtask</button-->

		<button class='w3-btn w3-block w3-green w3-xxlarge' type='submit'>Save This Task</button>
		
		<br><br>

	</form>

</div>


<?php


/*
echo '<h1 class="w3-center">Outstanding Items</h1>';
outputOutstandingItems($todoJson);

echo '<h1 class="w3-center">Completed Items</h1>';
outputCompletedItems($todoJson);
*/

echo '<h1 class="w3-center">Outstanding Items</h1>';
$todo->outputItemsByList();

echo '<h1 class="w3-center">Completed Items</h1>';
$todo->outputItemsByList(true, true);

function outputOutstandingItems($todoJson){

	global $classLow, $classMedium, $classUrgent;

	foreach ($todoJson->list as $item){

		if ($item->status !== "waiting") continue;

		switch ($item->urgency){
			case 0:
				//LOW
				echo '<div class="w3-container ' . $classLow . '"><h2>';
			break;
			case 2:
				//HIGH
				echo '<div class="w3-container ' . $classUrgent . '">';
				echo '<h2><img src="icons/warn.png" width="25" height="25" />&nbsp;&nbsp;&nbsp;';
			break;
			case 1:
			default:
				//MEDIUM
				echo '<div class="w3-container ' . $classMedium . '"><h2><img src="icons/list.png" width="25" height="25" />&nbsp;&nbsp;&nbsp;';
			break;
		}
		
		echo $item->title . '</h2>';
		//echo '<em>Added by: ' . $item->addedBy . '</em>';
		if (isset($item->subtasks)){
			echo '<ul>';
			foreach ($item->subtasks as $task){
				if ($task->status !== "completed"){
					echo '<li>' . $task->title . '</li>';
				}else{
					echo '<li class="completedTitle">' . $task->title . '</li>';
				}
			}
			echo '</ul>';
		}
		echo '</div>';

	}
}

function groupItemsByList($todoJson){
	$lists = array();
	$tasksList = $todoJson->list;
	foreach($tasksList as $item){
		if (isset($item->list)){
			$thisList = $item->list;
		}else{
			$thisList = 'Generic';
		}
		
		if (!isset($lists[$thisList])){
			$lists[$thisList] = array();
		}
		$lists[$thisList][] = $item;
	}
	return $lists;
}


function outputCompletedItems($todoJson){

	global $classCompleted;

	foreach ($todoJson->list as $item){

		if ($item->status !== "completed") continue;

		//COMPLETED BOX
		echo '<div class="w3-container ' . $classCompleted . '"><h2 class="completedTitle">' . $item->title . ' (complete)</h2>';
		//echo '<em>Added by: ' . $item->addedBy . '</em>';
		/*if (isset($item->subtasks)){
			echo '<ul>';
			foreach ($item->subtasks as $task){
				echo '<li>' . $task->title . '</li>';
			}
			echo '</ul>';
		}*/
		echo '</div>';

	}
}


?>
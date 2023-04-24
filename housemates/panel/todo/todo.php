<?php

/*

//OLD CLASS-LESS VERSION
$todoFileName = 'todo.json';
$todoFile = file_get_contents($todoFileName);
$todoJson = json_decode($todoFile);
$screenRefreshSeconds = 55;

$classLow = 'w3-grey';
$classMedium = 'w3-white';
$classUrgent = 'w3-red';
$classCompleted = 'w3-black';
*/

//NEW CLASS-BASED VERSION
//$screenRefreshSeconds = 55;
$screenRefreshSeconds = 120;

require_once 'classes/TodoList.class';
$todo = new TodoList();

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="refresh" content="<?php echo $screenRefreshSeconds; ?>" />
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="./css/todoStyles.css">
	<title>To Do List</title>
	<script src='../../js/browser.js'></script>
</head>
<script>
	var elements = [];
	var current = [];
	function pageInit(){
		//window.scrollTo(0,0);
		document.documentElement.scrollTop = 0;
		detectIframe();
		/*var isIframe = detectIframe();
		if (!isIframe){
			console.log('TODO is not in an iframe');
			document.getElementById('navBox').style.display = 'block';
		}*/
		
		scrollWindow();
	}
	function scrollWindow(){
		var calEls = document.getElementsByClassName('todoItem');
		var delay = 5000;
		var incr = 1500;
		var l = calEls.length;
		for (i = 0; i < l; i++) {
			var thisEl = calEls[i].id;
			elements.push(thisEl);
		}
		current = elements.slice();
		//console.log(current);
		setInterval(function(){ scrollToNextEl(); }, delay);
	}
	function scrollToNextEl(){
		if (current.length === 0){
			current = elements.slice();
		}
		id = current.shift();
		//console.log('Scrolling to ' + id);
		el = document.getElementById(id);
		//console.log(el);
		//document.documentElement.scrollTop = document.getElementById(id).offsetTop;
		el.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
	}

</script>

<body class='w3-dark-grey' onload='pageInit()'>

	<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
	<?php include 'navbar.php'; ?>
	</div>


<?php

/*
//v1
$urgentTasks = getTasks($todoJson, 2);
echo '<h1 class="w3-center">Urgent Items</h1>';
outputOutstandingItems($urgentTasks);

$normalTasks = getTasks($todoJson, 1);
echo '<h1 class="w3-center">Normal Items</h1>';
outputOutstandingItems($normalTasks);

$lowTasks = getTasks($todoJson, 0);
echo '<h1 class="w3-center">Low Urgency Items</h1>';
outputOutstandingItems($lowTasks);
*/
/*
//v2
echo '<h1 class="w3-center">Outstanding Items</h1>';
outputOutstandingItems($todoJson);

echo '<h1 class="w3-center">Completed Items</h1>';
outputCompletedItems($todoJson);
*/
/*
//v3
$allTasksByList = getTasks($todoJson, false);
outputItemsByList($allTasksByList);
*/

//NEW CLASS-BASED VERSION
$todo->outputItemsByList();


/**
 * Get all the tasks which match the urgency
 * @param array $todoJson A JSON object containing all the tasks
 * @param boolean $urgency The urgency of task to get (default: false)
 * 
 * @return array The array of matching tasks
 */
function getTasks($todoJson, $urgency = false){

	$arrReturn = new stdClass();
	$arrReturn->list = array();
	foreach ($todoJson->list as $item){

		//ONLY GET "INCOMPLETE" TASKS
		if ($item->status !== "waiting") continue;

		if ($urgency === false){

			$arrReturn->list[] = $item;
		//ONLY RETURN TASKS WHICH MATCH THE REQUESTED URGENCY
		}else if (intval($item->urgency) === intval($urgency)){

			$arrReturn->list[] = $item;
		}
	}
	return $arrReturn;
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

function outputItemsByList($todoJson){
	
	$lists = groupItemsByList($todoJson);
	$colours = array('w3-pale-blue', 'w3-pale-yellow', 'w3-pale-green', 'w3-pale-red', 'w3-light-green', 'w3-light-blue');
	
	foreach ($lists as $currentList){

		$listTitle = array_keys($lists, $currentList)[0];
		$listColour = $colours[rand(0, count($colours) - 1)];
		echo '<h2 class="w3-center">' . $listTitle . ' List</h2>';
		
		foreach ($currentList as $item){

			echo '<div id="item' . $item->id . '" class="todoItem w3-row ' . $listColour . '">';
			//NEW - SHOW LIST TITLE IN RESULTS
				echo '<span class="todoListNameSpan">' . $listTitle . '</span>';
				echo '<div class="w3-col s2">';
					echo '<img class="contrast" src="icons/list.png" width="50" height="50" />';
				echo '</div>';
				echo '<div class="w3-col s10"><h2>';
					echo $item->title . '</h2>';
				echo '</div>';
			echo '</div>';
		}
	}
}

function outputOutstandingItems($todoJson){

	global $classLow, $classMedium, $classUrgent;

	foreach ($todoJson->list as $item){

		if ($item->status !== "waiting") continue;

		switch ($item->urgency){
			case 0:
				//LOW
				$thisIcon = 'icons/rate.png';
				echo '<div id="item' . $item->id . '" class="todoItem w3-row ' . $classLow . '">';
				echo '<div class="w3-col s2">';
				echo '<img src="' . $thisIcon . '" width="50" height="50" />';
				echo '</div>';
				echo '<div class="w3-col s10"><h2>';
			break;
			case 2:
				//HIGH
				$thisIcon = 'icons/warn.png';
				echo '<div id="item' . $item->id . '" class="todoItem w3-row ' . $classUrgent . '">';
				echo '<div class="w3-col s2">';
				echo '<img class="contrast" src="' . $thisIcon . '" width="50" height="50" />';
				echo '</div>';
				echo '<div class="w3-col s10"><h2>';
			break;
			case 1:
			default:
				//MEDIUM
				$thisIcon = 'icons/list.png';
				echo '<div id="item' . $item->id . '" class="todoItem w3-row ' . $classMedium . '">';
				echo '<div class="w3-col s2">';
				echo '<img src="' . $thisIcon . '" width="50" height="50" />';
				echo '</div>';
				echo '<div class="w3-col s10"><h2>';
			break;
		}
		
		echo $item->title . '</h2>';
		//echo '<em>Added by: ' . $item->addedBy . '</em>';
		if (isset($item->subtasks)){
			//echo '<ul>';
			foreach ($item->subtasks as $task){
				echo '<div class="todoSubItem">';
				if ($task->status !== "completed"){
					//echo '<li><img src="' . $thisIcon . '" width="25" height="25" />' . $task->title . '</li>';
					echo '<img src="' . $thisIcon . '" width="50" height="50" />' . $task->title . '';
				}else{
					//echo '<li class="completedTitle"><img src="icons/checked.png" width="25" height="25" />' . $task->title . '</li>';
					echo '<img src="icons/checked.png" width="50" height="50" /><span class="completedTitle">' . $task->title . '</span>';
				}
				echo '</div>';
			}
			//echo '</ul>';
			
		}
		echo '</div>';
		echo '</div>';

	}
}

function outputCompletedItems($todoJson){

	global $classCompleted;
	
	foreach ($todoJson->list as $item){

		if ($item->status !== "completed") continue;

		//COMPLETED BOX
		echo '<div id="item' . $item->id . '" class="todoItem w3-container w3-row ' . $classCompleted . '">';
		echo '<div class="w3-col s2">';
		echo '<img src="icons/checked.png" width="25" height="25" />';
		echo '</div>';
		echo '<div class="w3-col s10">';
		echo '<h2 class="completedTitle">' . $item->title . ' (complete)</h2>';
		echo '</div>';
		echo '</div>';

	}
}


?>
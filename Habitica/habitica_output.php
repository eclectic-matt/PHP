/*
@subtype
  @dailys           - daily tasks (options for the API)
  @todos            - todos (options for the API)
  @habits           - habits (options for the API)
  @completedTodos   - completedTodos (options for the API)

@json_arr
  The decoded JSON response from habitica_input($a,$b);
*/

function habitica_output($subtype, $json_arr){
	
	switch ($subtype){
	
		case 'todos':
			// FALL THROUGH
		case 'completedTodos':
			// TASKS OUTPUT
			echo "<h1>Habitica " . $subtype . ":</h1>";
			$items = count($json_arr["data"]);
			if ($items == 0){
				echo "<b>No Items</b>";
			}else{
				echo "<ul>";
				for ($i = 0; $i < $items; $i++){
					echo "<li>";
					echo $json_arr["data"][$i]["text"];
					// CHECK FOR SUBTASKS (CHECKLIST)
					$subtasks = count($json_arr["data"][$i]["checklist"]);
					if ($subtasks > 0){
						echo "<ul>";
						for ($j = 0; $j < $subtasks; $j++){
							echo "<li>";
							echo $json_arr["data"][$i]["checklist"][$j]["text"];
							echo "</li>";
						}
						echo "</ul>";
					}
					echo "</li>";
				}
			}
			echo "</ul>";
			break;
			
			
		default:
			// DEFAULT OUTPUT
			echo "<h1>Habitica " . $subtype . ":</h1>";
			$items = count($json_arr["data"]);
			if ($items == 0){
				echo "<b>No Items</b>";
			}else{
				echo "<ul>";
				for ($i = 0; $i < $items; $i++){
					echo "<li>";
					echo $json_arr["data"][$i]["text"];
					echo "</li>";
				}
				echo "</ul>";
			}
			break;
	}
}

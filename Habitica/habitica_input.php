/*
@type
   @tasks        - calls the GetUserTasks API
   @challenges   - calls the GetUserChallenges API

@subtype
  @dailys           - daily tasks (options for the API)
  @todos            - todos (options for the API)
  @habits           - habits (options for the API)
  @completedTodos   - completedTodos (options for the API)

NOTE ON GLOBAL VARIABLES (HIDDEN FOR SECURITY):
$GLOBALS['userKey']		- the "x-api-user" value for the Habitica API
$GLOBALS['userId'];		- the "x-api-key" value for the Habitica API
$GLOBALS['urlTasksUser']	- the API URL for Get User Tasks
*/

function habitica_input($type, $subtype){
	
	$ch = curl_init();
	
	// Switch based on API method to call
	$additional = "";

	switch ($type){
		
		case 'tasks':			
			if (isset($subtype) && $subtype !== ""){
				$additional = "?type=" . $subtype;
			}
			$apiURL = $GLOBALS['urlTasksUser'] . $additional;
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
			
		default:
			$apiURL = $GLOBALS['urlTasksUser'] . $additional;
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
	}
	
	// ADD ADDITIONAL CURL HEADERS
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$headKey = "x-api-key: " . $GLOBALS['userKey'];
	$headUser = "x-api-user: " . $GLOBALS['userId'];

	$headers = [
		$headUser,
		$headKey,
		'Content-Type: application/json'	
	];
	//print "<br>" . $headers[0] . "<br>";
	//print $headers[1] . "<br>";
	//print $headers[2] . "<br>";
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	
	// Convert to an array for processing
	$json_arr = json_decode($server_output,true);
	// var_dump($json_arr);		//for debugging

	return $json_arr;
	
}

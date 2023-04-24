<?php


/*echo "TOO LONG STRING LENGTH = " . strlen('Boss Monster: The Dungeon Building Card Game');
echo "SHORT ENOUGH STRING LENGTH = " . strlen('Choose Your Own Adventure: House of Danger');
echo "SHORT ENOUGH STRING LENGTH = " . strlen('Clank! In! Space!: A Deck-Building Adventure');*/

    //SET CONSTANTS
    const DEFAULT_MIN_TIME = 0;
    const DEFAULT_MAX_TIME = 240;  //4 hours
    const DEFAULT_MIN_PLAYERS = 4;
    const DEFAULT_MAX_PLAYERS = 4;
    const DEFAULT_MIN_WEIGHT = 0;
    const DEFAULT_MAX_WEIGHT = 6;

	const DESCRIPTION_WEIGHT_UNDER_1 = 'party';
	const DESCRIPTION_WEIGHT_UNDER_2 = 'simple';
	const DESCRIPTION_WEIGHT_UNDER_3 = 'medium';
	const DESCRIPTION_WEIGHT_UNDER_4 = 'hard';
	const DESCRIPTION_WEIGHT_UNDER_5 = 'crunchy';

    //--------------------
    //HANDLE GET VARIABLES
    //--------------------
	//DODGY AS ALL FUCK, BUT WORKS...
	$getParamCount = extract($_GET);
	$minTime = isset($minTime) ? $minTime : DEFAULT_MIN_TIME;
	$maxTime = isset($maxTime) ? $maxTime : DEFAULT_MAX_TIME;
	$minPlayers = isset($minPlayers) ? $minPlayers : DEFAULT_MIN_PLAYERS;
	//$maxPlayers = isset($maxPlayers) ? $maxPlayers : DEFAULT_MAX_PLAYERS;
	$maxPlayers = $minPlayers;
	$minWeight = isset($minWeight) ? $minWeight : DEFAULT_MIN_WEIGHT;
	$maxWeight = isset($maxWeight) ? $maxWeight : DEFAULT_MAX_WEIGHT;
	$playStatus = isset($playStatus) ? $playStatus : 'any';
	$sortField = isset($sortField) ? $sortField: 'title';
	$sortOrder = isset($sortOrder) ? $sortOrder : 'asc';
	
    //--------------------
    //HANDLE BG DATA
    //--------------------
    $bgFileName = 'js/board-games.json';
    $bgFile = file_get_contents($bgFileName);
    $bgJson = json_decode($bgFile);

	//NEW - GET PLAYS DATA
	$playsFileName = 'playsBackup.json';
	$playedGames = getPlayedGamesArray($playsFileName);

	//$allPlays = getAllPlaysArray($playsUrl);

    //PREPARE FILTERED GAMES
    $validGames = array();
    $excludedGames = array();

	//DISPLAY SETTINGS
	$cellsPerRow = 3;
	$maxTitleLength = 60;

	//$longestTitle = 0;
	$sortHelpers = new stdClass();
	$sortHelpers->longestTitle = 0;
	$sortHelpers->shortestPlaytime = 120;
	$sortHelpers->longestPlaytime = 0;
	$sortHelpers->lowestWeight = 5;
	$sortHelpers->highestWeight = 0;

    foreach ($bgJson->games as $game){
        
        //IF THE GAME MEETS ALL CONDITIONS
        if (
            (intval($game->minPlaytime) >= intval($minTime)) &&
            (intval($game->maxPlaytime) <= intval($maxTime)) &&
            (intval($game->minPlayers) <= intval($minPlayers)) &&
            (intval($game->maxPlayers) >= intval($maxPlayers)) &&
            (floatval($game->weight) >= floatval($minWeight)) &&
            (floatval($game->weight) <= floatval($maxWeight))
        ){
			/*if (empty($validGames)){
				//ADD TO THE VALID GAMES ARRAY
				$validGames[] = $game;
			}else{
				$validGames = addToSortedArray($validGames, $game, $sortField, $sortOrder);
			}*/

			//ONLY INCLUDE BASED ON STATUS
			switch($playStatus){
				//IF ONLY DISPLAYING PLAYED GAMES
				case 'onlyPlayed':
					//IF THIS GAME IS NOT IN THE PLAYED GAMES ARRAY
					if (in_array($game->title, $playedGames) === false){
						//ADD TO THE EXCLUDED GAMES ARRAY
						$excludedGames[] = $game;
						continue 2;
					}
				break;
				//IF ONLY DISPLAYING UNPLAYED GAMES
				case 'onlyUnplayed':
					//IF THIS GAME *IS* IN THE PLAYED GAMES ARRAY
					if (in_array($game->title, $playedGames) === true){
						//ADD TO THE EXCLUDED GAMES ARRAY
						$excludedGames[] = $game;
						continue 2;
					}
				break;
			}

			$validGames[] = $game;
			
			//GET SORTING ORDER VALUES
			if (strlen($game->title) > $sortHelpers->longestTitle){
				$sortHelpers->longestTitle = strlen($game->title);
			}
			if (intval($game->maxPlaytime) <= $sortHelpers->shortestPlaytime){
				$sortHelpers->shortestPlaytime = intval($game->maxPlaytime);
			}
			if (intval($game->maxPlaytime) >= $sortHelpers->longestPlaytime){
				$sortHelpers->longestPlaytime = intval($game->maxPlaytime);
			}
			if (intval($game->weight) <= $sortHelpers->lowestWeight){
				$sortHelpers->lowestWeight = intval($game->weight);
			}
			if (intval($game->weight) >= $sortHelpers->highestWeight){
				$sortHelpers->highestWeight = intval($game->weight);
			}

		}else
		{
			//ADD TO THE EXCLUDED GAMES ARRAY
			$excludedGames[] = $game;
		}
	}

	//START OUTPUT
	?>	
    <!DOCTYPE html>
    <html>
    <head>
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<!--meta name="viewport" content="width=device-width, initial-scale=1.0"-->

		<title>Board Game Picker</title>
		<style>
		table, tr, td, th {
			border: 1px solid black;
			border-collapse: collapse;
		}
		.playedStatusSpan{
			padding-top: -15px;
			padding-left: 5px;
			padding-right: 5px;
			margin-left: -40px;
			margin-top: -5px;
			background-color: white;
			color: black;
			border: 1px solid black;
			font-style: italic;
			font-size: 0.75em;
			position: absolute;
		}
		</style>
		<script>
		function accordion(id) {
			var x = document.getElementById(id);
			if (x.className.indexOf("w3-show") == -1) {
				x.className += " w3-show";
				console.log('Showing ' + id);
			} else {
				x.className = x.className.replace(" w3-show", "");
				console.log('Hiding ' + id);
			}
		}
		</script>

		<script src='../../js/browser.js'></script>
    </head>
    <body onload="accordion('validGamesDiv'); detectIframe();">

		<div class="w3-row w3-black w3-center w3-xlarge w3-block" id="navBox">
			<br>
			<a href='/housemates/home.php'><button class='w3-red w3-xlarge'>Back to Housemates Home</button></a>
			<br><br>
			<div class='w3-row'>
				<div class='w3-col s6'>
					<a href='/housemates/panel/games/picker.php'><button class='w3-purple'>Picker</button></a>
				</div>
				<div class='w3-col s6'>
					<a href='/housemates/panel/games/stats.php'><button class='w3-green'>Stats</button></a>
				</div>
			</div>
			<br>
		</div>

    <h1 class="w3-center w3-wide">Board Game Picker</h1>

	<button class="w3-orange w3-button w3-block w3-left-align" onclick="accordion('filterFormDiv')"><h2 class="w3-center">Filter Games</h2></button>
	<div id="filterFormDiv" class="w3-hide w3-panel w3-padding w3-orange">

	<?php
	outputSelectorForm();
	echo '</div>';
	echo '<hr>';

	//OUTPUT SEARCH RESULTS HEADER
	echo '<!--button class="w3-grey w3-button w3-block w3-left-align" onclick="accordion(\'searchResultsDiv\')"><h2 class="w3-center">Search Results</h2></button>';
	echo '<div id="searchResultsDiv" class="w3-hide w3-panel w3-padding w3-grey">';
		//echo '<h3>There are ' . count($validGames) . ' suitable games detected!</h3>';
		//echo '<h3>There are ' . count($excludedGames) . ' excluded games detected!</h3>';
		echo '<table class="w3-table w3-center">';
			echo '<tr><th class="w3-center w3-large">Option</th><th class="w3-center w3-large">Value</th></tr>';
			echo '<tr><td class="w3-center w3-large">Min Time</td><td class="w3-center w3-large">' . $minTime . '</td></tr>';
			echo '<tr><td class="w3-center w3-large">Max Time</td><td class="w3-center w3-large">' . $maxTime . '</td></tr>';
			echo '<tr><td class="w3-center w3-large">Min Players</td><td class="w3-center w3-large">' . $minPlayers . '</td></tr>';
			echo '<tr><td class="w3-center w3-large">Max Players</td><td class="w3-center w3-large">' . $maxPlayers . '</td></tr>';
			echo '<tr><td class="w3-center w3-large">Min Weight</td><td class="w3-center w3-large">' . $minWeight . '</td></tr>';
			echo '<tr><td class="w3-center w3-large">Max Weight</td><td class="w3-center w3-large">' . $maxWeight . '</td></tr>';
		echo '</table>';
	echo '</div>';
	echo '<hr-->';

    //OUTPUT VALID GAMES
	echo '<button class="w3-green w3-button w3-block w3-left-align" onclick="accordion(\'validGamesDiv\')"><h2 class="w3-center">Valid Games</h2></button>';
	echo '<div id="validGamesDiv" class="w3-hide w3-panel w3-padding w3-green">';

	echo '<h3 class="w3-center">There are ' . count($validGames) . ' suitable games!</h3>';

	//OUTPUT SEARCH OPTIONS
	echo '<div class="w3-center">';
	echo '<em>Player Count: ' . $minPlayers . '</em><br>';
	echo '<em>Time: ' . $minTime . ' - ' . $maxTime . ' mins</em><br>';
	echo '<em>Weight: ' . $minWeight . ' - ' . $maxWeight . '</em><br>';
	echo '</div>';

	//echo '<h2>Suitable Games: </h2>';
	//echo '<div class="w3-row-padding">';
		echo '<div>';
		$counter = 0;
		$scaledTitleMax = floor($maxTitleLength / $cellsPerRow);
		foreach ($validGames as $game){

			if ($counter % $cellsPerRow === 0){
				echo '</div><div class="w3-row-padding">';
			}

			echo '<div class="w3-col s' . floor(12 / $cellsPerRow) . '">';
				outputGameBox($game, $scaledTitleMax);
			echo '</div>';

			$counter++;
		}
		echo '</div>';
	echo '</div>';
	echo '<hr>';
	//OUTPUT EXCLUDED GAMES
	echo '<button class="w3-red w3-button w3-block w3-left-align" onclick="accordion(\'excludedGamesDiv\')"><h2 class="w3-center">Excluded Games</h2></button>';
	echo '<div id="excludedGamesDiv" class="w3-hide w3-panel w3-padding w3-red">';
	//echo '<h2>Excluded Games: </h2>';
	echo '<h3 class="w3-center">There are ' . count($excludedGames) . ' excluded games!</h3>';
	echo '<div>';
	$counter = 0;
	foreach ($excludedGames as $game){

		if ($counter % $cellsPerRow === 0){
			echo '</div><div class="w3-row-padding">';
		}

		echo '<div class="w3-col s' . floor(12 / $cellsPerRow) . '">';
			outputGameBox($game, $scaledTitleMax);
		echo '</div>';

		$counter++;
	}
	echo '</div>';
	echo '</div>';
	echo '<br><br><br>';
	echo '</body>';
	echo '</html>';






    //--UTILITY FUNCTIONS

	function getAllPlaysArray($url){

		$playsFileName = $url;
		$playsFile = file_get_contents($playsFileName);
		$playsXML = simplexml_load_string($playsFile);
		$playsJSON = json_encode($playsXML);
		$playsArray = json_decode($playsJSON,TRUE);
		$resultsPerPage = 100;
		$totalPlays = $playsArray['@attributes']['total'];
		$resultsPages = ceil($totalPlays / $resultsPerPage);
		for ($i = 1; $i < $resultsPages; $i++){
			$nextFileName = $url . '&page=' . ($i + 1);
			$nextFile = file_get_contents($nextFileName);
			$nextFileXML = simplexml_load_string($nextFile);
			$nextJSON = json_encode($nextFileXML);
			$nextArray = json_decode($nextJSON,TRUE);
			foreach($nextArray as $play){
				$playsArray['play'][] = $play;
			}
		}
		//var_dump($playsArray);
	}


	function addToSortedArray($validGames, $game, $sortField, $sortOrder){
		//DETERMINE THE SORT TYPE
		switch ($sortField){
			case 'minPlaytime':
			case 'maxPlaytime':
			case 'minPlayers':
			case 'maxPlayers':
				$sortType = 'int';
			break;	
			case 'weight':
				$sortType = 'float';
			break;
			case 'title':
			case 'owner':
			default:
				$sortType = 'str';
			break;
		}
		//COUNT THE INDEX
		$index = 0;
		foreach($validGames as $compare){
			if ($sortOrder === 'asc'){
				switch ($sortType){
					case 'str':
						if (strcmp($game->$sortField, $compare->$sortField) < 0){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}//ELSE, NEXT CHECK $validGame
					break;
					case 'int':
						if (intval($game->$sortField) < intval($compare->$sortField) ){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}
					break;
					case 'float':
						if (floatval($game->$sortField) < floatval($compare->$sortField) ){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}
					break;
				}
			}else{
				switch ($sortType){
					case 'str':
						if (strcmp($game->$sortField, $compare->$sortField) > 0){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}//ELSE, NEXT CHECK $validGame
					break;
					case 'int':
						if (intval($game->$sortField) > intval($compare->$sortField) ){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}
					break;
					case 'float':
						if (floatval($game->$sortField) > floatval($compare->$sortField) ){
							//INSERT HERE
							array_splice($validGames, $index, 0, $game);
							//RETURN
							return $validGames;
						}
					break;
				}
			}
			$index++;
		}
		//ADD ONTO END
		array_push($validGames, $game);
		return $validGames;
	}


    function outputGameBox($game, $scaledTitleMax){

		$IMAGE_WIDTH = 20;
		global $sortHelpers, $playedGames;

		//$w3cols = array("w3-red","w3-pink","w3-purple","w3-deeppurple","w3-indigo","w3-blue","w3-lightblue","w3-cyan","w3-aqua","w3-teal","w3-green","w3-lightgreen","w3-lime","w3-sand","w3-khaki","w3-yellow","w3-amber","w3-orange","w3-deep-orange","w3-blue-gray","w3-brown","w3-light-gray","w3-gray","w3-dark-gray","w3-pale-red","w3-pale-yellow","w3-pale-green","w3-pale-blue");
		$w3cols = array(
			"w3-pink","w3-purple","w3-deep-purple","w3-indigo",
			"w3-blue","w3-light-blue","w3-cyan","w3-aqua","w3-teal",
			"w3-light-green","w3-lime","w3-sand","w3-khaki","w3-yellow",
			"w3-amber","w3-orange","w3-deep-orange","w3-blue-gray",
			"w3-brown","w3-light-gray","w3-gray","w3-dark-gray",
			"w3-pale-red","w3-pale-yellow","w3-pale-green","w3-pale-blue"
		);

        echo '<div class="w3-panel w3-padding w3-border w3-center ';
		echo $w3cols[rand(0,count($w3cols)-1)];
		echo '">';

		//OUTPUT "Played: x" or "Played: ,/"
		if (in_array($game->title, $playedGames)){
			echo '<span class="playedStatusSpan">
				<em>Played: 
					<span style="color: green;">&#x2714;</span>
				</em>
			</span>';
		}else{
			echo '<span class="playedStatusSpan">
				<em>Played: 
					<span style="color: red;">&#x2716;</span>
				</em>
			</span>';
		}

			echo '<h3 style="height: 4em; font-size: 2em; font-weight:900;">';
			if (strlen($game->title) >= $scaledTitleMax){
				echo '<span title="' . $game->title . '">' . $game->title . '</span>';
				//echo '<span title="' . $game->title . '">' . substr($game->title, 0, $scaledTitleMax) . '...</span>';
			}else{
				echo '<span title="' . $game->title . '">' . $game->title . '</span>';
			}
			echo '</h3>';

			echo '<div class="w3-row">';

				echo '<div class="w3-col s3">';

					echo '<image src="icons/group.png" width="' . $IMAGE_WIDTH . '" height="' . $IMAGE_WIDTH . '" /> ';
					echo '<span title="Player Count">' . $game->minPlayers . ' - ' . $game->maxPlayers . '</span>';

				echo '</div>';

				echo '<div class="w3-col s4">';

					echo '<image src="icons/clock.png" width="' . $IMAGE_WIDTH . '" height="' . $IMAGE_WIDTH . '" /> ';
					echo '<span title="Playing Time">' . $game->minPlaytime . ' - ' . $game->maxPlaytime . '</span>';

				echo '</div>';

				echo '<div class="w3-col s5">';

					echo '<image src="icons/weight.png" width="' . $IMAGE_WIDTH . '" height="' . $IMAGE_WIDTH . '" /> ';
					if (intval($game->weight) < 1){
						$gameWeightStr = DESCRIPTION_WEIGHT_UNDER_1;
					}else if (intval($game->weight) < 2){
						$gameWeightStr = DESCRIPTION_WEIGHT_UNDER_2;
					}else if (intval($game->weight) < 3){
						$gameWeightStr = DESCRIPTION_WEIGHT_UNDER_3;
					}else if (intval($game->weight) < 4){
						$gameWeightStr = DESCRIPTION_WEIGHT_UNDER_4;
					}else if (intval($game->weight) <= 5){
						$gameWeightStr = DESCRIPTION_WEIGHT_UNDER_5;
					}
					//echo (floor(10 * $game->weight) / 10) . '/5 ' . $gameWeightStr;
					echo '<span title="Weight (rules complexity) = ' . (floor(10 * $game->weight) / 10) . '/5 ' . '">' . strtoupper($gameWeightStr) . '</span>';
				
				echo '</div>';

			echo '</div>';

        echo '</div>';
    }

	function outputSelectorForm(){

		$arrTimes = array(0, 15, 30, 45, 60, 90, 120, 180, 240, 1440);
		$minTimeDefault = $arrTimes[0];
		$maxTimeDefault = 60;
		$arrPlayers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12);
		$minPlayersDefault = 4;
		$arrWeightNames = array('min', 'party', 'simple', 'medium', 'hard', 'crunchy', 'max');
		$arrWeights = array(0, 1, 2, 3, 4, 5, 6);
		$minWeightDefault = $arrWeights[0];
		$maxWeightDefault = $arrWeights[count($arrWeights) - 1];
		$arrPlayStatuses = array('all', 'onlyPlayed', 'onlyUnplayed');

		global $minTime, $maxTime, $minPlayers, $maxPlayers, $minWeight, $maxWeight;

		$leftCol = 's5';
		$rightCol = 's7';

		//echo '<h2>FILTER GAMES</h2>';
		echo '<form method="GET" action="">';

		//PLAYERS
		//-min
		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="minPlayers">Player Count (must support at least): ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				//echo '<select class="w3-input w3-border" id="minPlayers" name="minPlayers">';
				foreach ($arrPlayers as $player){
					//if ($player === $minPlayersDefault){
					if ($player === intval($minPlayers)){
						echo '<input class="w3-radio" type="radio" name="minPlayers" value="' . $player . '" checked>';
						echo '<label> ' . $player . '</label> '; 
						//echo '<option value="' . $player . '" selected>' . $player . '</option>';
					}else{
						echo '<input class="w3-radio" type="radio" name="minPlayers" value="' . $player . '">';
						echo '<label> ' . $player . '</label> '; 
						//echo '<option value="' . $player . '">' . $player . '</option>';
					}
				}
				//echo '</select>';
				echo ' players<br>';
			echo '</div>';
		echo '</div><br>';

		//-max
		/*echo '<label for="maxPlayers">Maximum Players (must support at most): ';
		echo '<select class="w3-input w3-border" id="maxPlayers" name="maxPlayers">';
		foreach ($arrPlayers as $player){
			if ($player === $arrPlayers[count($arrPlayers) - 1]){
				echo '<option value="' . $player . '" selected>' . $player . '</option>';
			}else{
				echo '<option value="' . $player . '">' . $player . '</option>';
			}
		}
		echo '</select>';
		//echo ' players<br><hr>';*/

		//PLAYTIME
		//-min
		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="minTime">Minimum Playing Time (play for at least): ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				//echo '<select class="w3-input w3-border" id="minTime" name="minTime">';
				foreach ($arrTimes as $time){
					//if ($time === $minTimeDefault){
					if ($time === intval($minTime)){
						echo '<input class="w3-radio" type="radio" name="minTime" value="' . $time . '" checked>';
						echo '<label> ' . $time . '</label> '; 
						//echo '<option value="' . $time . '" selected>' . $time . '</option>';
					}else{
						echo '<input class="w3-radio" type="radio" name="minTime" value="' . $time . '">';
						echo '<label> ' . $time . '</label> '; 
						//echo '<option value="' . $time . '">' . $time . '</option>';
					}
				}
				echo ' mins <br>';
				//echo '</select>';
			echo '</div>';
		echo '</div><br>';

		//-max
		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="maxTime">Maximum Playing Time (play for at most): ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				//echo '<select class="w3-input w3-border" id="maxTime" name="maxTime">';
				foreach ($arrTimes as $time){
					//if ($time === $maxTimeDefault){
					if ($time === intval($maxTime)){
						echo '<input class="w3-radio" type="radio" name="maxTime" value="' . $time . '" checked>';
						echo '<label> ' . $time . '</label> '; 
						//echo '<option value="' . $time . '" selected>' . $time . '</option>';
					}else{
						echo '<input class="w3-radio" type="radio" name="maxTime" value="' . $time . '">';
						echo '<label> ' . $time . '</label> '; 
						//echo '<option value="' . $time . '">' . $time . '</option>';
					}
				}
				//echo '</select>';
				echo ' mins<br>';
			echo '</div>';
		echo '</div><br>';

		//WEIGHT
		//-min
		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="minWeight">Minimum Weight (at least this heavy): ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				//echo '<select class="w3-input w3-border" id="minWeight" name="minWeight">';
				foreach ($arrWeights as $weight){
					//if ($weight === $minWeightDefault){
					if ($weight === intval($minWeight)){
						echo '<input class="w3-radio" type="radio" name="minWeight" value="' . $weight . '" checked>';
						echo '<label> ' . $arrWeightNames[$weight] . '</label> '; 
						//echo '<option value="' . $weight . '" selected>' . $weight . '</option>';
					}else{
						echo '<input class="w3-radio" type="radio" name="minWeight" value="' . $weight . '">';
						echo '<label> ' . $arrWeightNames[$weight] . '</label> '; 
						//echo '<option value="' . $weight . '">' . $weight . '</option>';
					}
				}
				//echo '</select>';
				//echo ' out of 5<br>';
				echo '<br>';
			echo '</div>';
		echo '</div><br>';

		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="maxWeight">Maximum Weight (at most this heavy): ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				//-max
				//echo '<label for="maxWeight">Maximum Weight (must be at most this heavy): ';
				//echo '<select class="w3-input w3-border" id="maxWeight" name="maxWeight">';
				foreach ($arrWeights as $weight){
					//if ($weight === $maxWeightDefault){
					if ($weight === intval($maxWeight)){
						echo '<input class="w3-radio" type="radio" name="maxWeight" value="' . $weight . '" checked>';
						echo '<label> ' . $arrWeightNames[$weight] . '</label> '; 
						//echo '<option value="' . $weight . '" selected>' . $weight . '</option>';
					}else{
						echo '<input class="w3-radio" type="radio" name="maxWeight" value="' . $weight . '">';
						echo '<label> ' . $arrWeightNames[$weight] . '</label> '; 
						//echo '<option value="' . $weight . '">' . $weight . '</option>';
					}
				}
				//echo '</select>';
				//echo ' out of 5<br>';
			echo '</div>';
		echo '</div><br>';

		//PLAY STATUS SELECTOR
		echo '<div class="w3-row">';

			echo '<div class="w3-col ' . $leftCol . '">';
				echo '<label for="playStatus">Play Status: ';
			echo '</div>';

			echo '<div class="w3-col ' . $rightCol . '">';
				
				foreach ($arrPlayStatuses as $status){
					
					if ($status === 'all'){

						echo '<input class="w3-radio" type="radio" name="playStatus" value="' . $status . '" checked>';
						echo '<label> ' . $status . '</label> '; 
					}else{

						echo '<input class="w3-radio" type="radio" name="playStatus" value="' . $status . '">';
						echo '<label> ' . $status . '</label> '; 
					}
				}
				echo '<br>';
			echo '</div>';
		echo '</div><br>';

			
		//SUBMIT
		echo '<button type="submit" class="w3-large w3-button w3-block w3-red w3-border">Submit</button>';

		echo '</form>';

	}

	function getPlayedGamesArray($playsFileName){

		$playsFile = file_get_contents($playsFileName);
		$playsJson = json_decode($playsFile, true);
		$playedGames = array();
		foreach ($playsJson['play'] as $play){
			$thisName = $play['item']['@attributes']['name'];
			if (!in_array($thisName, $playedGames)){
				//$playedGames = array_push($playedGames, $thisName);
				array_push($playedGames, $thisName);
			}
		}
		asort($playedGames);
		//var_dump($playedGames);
		return $playedGames;
	}

?>
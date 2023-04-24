<?php
set_include_path(__DIR__);

$playsUrl = 'https://api.geekdo.com/xmlapi2/plays?username=EclecticMatt';
$playsBackupFile = 'playsBackup.json';
$arrPlayers = array('Tom Walton', 'Matt', 'Naomi Lukianczuk', 'Rich Lee');
//$staleSeconds = 60 * 60 * 24; 	//DAILY
$staleSeconds = 60 * 60 * 3;		//TRI-HOURLY

//CHECK IF BGG DATA PULLED MORE THAN 24 HOURS AGO
if (checkStale($playsBackupFile, $staleSeconds) === true){

	//GET FRESH DATA AND PARSE
	$allPlays = getAllPlaysArray($playsUrl);
}else{

	//echo 'Getting cached data from "' . $playsBackupFile . '" now...';

	//GET CACHED DATA
	$allPlays = getBackupJSON($playsBackupFile);
}

$lastPlaysCount = 15;
$lastPlays = getLastPlays($allPlays, $lastPlaysCount);
$lastWinsCount = 10;
$lastWins = getLastWins($allPlays, $lastWinsCount);
$playersSummary = getPlayerSummaries($allPlays, $arrPlayers);


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<!-- <link rel="stylesheet" href="./css/gameStyles.css"> -->
<title>Game Stats</title>
<style>
	* {
	box-sizing: border-box;
}

h1, h2, p {
	padding: 0 5%;
}


table, tr, th, td{
	border: 1px solid black;
	border-collapse: collapse;
	padding: 0.5px;
	margin: 0;
	line-height: 0.8;
	color: black;
}
th{
	font-size: 1em;
	font-weight: bold;
}

td{
	font-size: 1.2em;
	font-weight: normal;
}

</style>

<script>

	function sortTable(n, id) {

		var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
		table = document.getElementById(id);
		switching = true;
		// Set the sorting direction to ascending:
		dir = "asc";

		/* Make a loop that will continue until
		no switching has been done: */
		while (switching) {

			// Start by saying: no switching is done:
			switching = false;
			rows = table.rows;

			/* Loop through all table rows (except the
			first, which contains table headers): */
			for (i = 1; i < (rows.length - 1); i++) {

				// Start by saying there should be no switching:
				shouldSwitch = false;
				/* Get the two elements you want to compare,
				one from current row and one from the next: */
				x = rows[i].getElementsByTagName("TD")[n];
				y = rows[i + 1].getElementsByTagName("TD")[n];
				
				/* Check if the two rows should switch place,
				based on the direction, asc or desc: */
				if (dir == "asc") {
				
					if (parseInt(x.innerHTML) == x.innerHTML){
				
						if (parseInt(x.innerHTML) > parseInt(y.innerHTML)){
				
							shouldSwitch = true;
							break;
						}
					}else if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {

						// If so, mark as a switch and break the loop:
						shouldSwitch = true;
						break;
					}
				} else if (dir == "desc") {
				
					if (parseInt(x.innerHTML) == x.innerHTML){
					
						if (parseInt(x.innerHTML) < parseInt(y.innerHTML)){
							shouldSwitch = true;
							break;
						}
					}else if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {

						// If so, mark as a switch and break the loop:
						shouldSwitch = true;
						break;
					}
				}
			}
			if (shouldSwitch) {

				/* If a switch has been marked, make the switch
				and mark that a switch has been done: */
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				// Each time a switch is done, increase this count by 1:
				switchcount ++;
			} else {

				/* If no switching has been done AND the direction is "asc",
				set the direction to "desc" and run the while loop again. */
				if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				}
			}
		}
	}

</script>
<script src='../../js/browser.js' defer></script>
</head>
<body class='w3-dark-grey' onload='init()'>

<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
<?php include 'navbar.php'; ?>
</div>



<?php

echo '<div id="statsDiv">';

//echo '<h1>Game Stats</h1>';
echo '<h4 class="w3-center">Recent Games</h4>';// - see more <a href="http://192.168.2.137:8080/housemates/panel/games/stats.php"><button class="w3-purple">Game Stats</button></a></b>';
outputRecentTable($playersSummary);

echo '<h4 class="w3-center">Win Stats</h4>';
outputWinnersTable($playersSummary);

echo '<h4 class="w3-center">All Plays</h4>';
outputAllPlaysTable($allPlays);

echo '</div>';
echo '</html>';

//END HTML OUTPUT


function getAllPlaysArray($url){
	
	global $playsBackupFile;
	//echo 'Getting first page<br>';
	$playsArray = getXMLandReturnDecodedJSON($url, true);
	$resultsPerPage = 100;
	$totalPlays = $playsArray['@attributes']['total'];

	//CHECK IF FULL PARSE NEEDED
	/*$backup = file_get_contents('playsBackup.json');
	$backupJSON = json_decode($backup, true);
	$backupPlays = $backupJSON['@attributes']['total'];

	//IF NO NEW PLAYS LOGGED, USE BACKUP
	if ($totalPlays === $backupPlays){
		return json_encode($backupPlays);
	}*/

	$resultsPages = ceil($totalPlays / $resultsPerPage);
	//echo '<h1>Found ' . $totalPlays . ' logged plays</h1>';// ' . $resultsPages . ' pages</h1>';
	for ($i = 1; $i < $resultsPages; $i++){
		$nextFileName = $url . '&page=' . ($i + 1);
		//echo 'Getting page ' . ($i+1) . '<br>';
		$nextArray = getXMLandReturnDecodedJSON($nextFileName, true);
		foreach($nextArray['play'] as $play){
			$playsArray['play'][] = $play;
		}
	}
	//TESTING - SAVE TO VIEW DATA
	$allPlaysJSON = json_encode($playsArray);
	file_put_contents($playsBackupFile, $allPlaysJSON);
	//END TESTING
	return $playsArray;
}

function getBackupJSON($file){
	//$file = './' . $file;
	//$encoded = file_get_contents($file);
	$encoded = file_get_contents($file,true);
	//$encoded = file($file);

	//TESTING
	//echo 'Dumping file "' . $file . '" now...';
	//var_dump($encoded);
	//TESTING

	$json = json_decode($encoded, true);
	return $json;
}

function checkStale($file, $staleSeconds){
	if (file_exists($file)){
		$mTime = filemtime($file);
	}else{
		$mTime = 1;
	}
	$secondsSinceMod = time() - $mTime;
	if ($secondsSinceMod > $staleSeconds){
		return true;
	}else{
		return false;
	}
}

function getXMLandReturnDecodedJSON($url, $associative = true){
	$file = file_get_contents($url);
	//TESTING
	//echo 'Dumping file "' . $url . '" now...';
	//var_dump($file);
	//TESTING
	$xml = simplexml_load_string($file);
	$json = json_encode($xml);
	return json_decode($json,$associative);
}

function outputWinnersTable($summary){
	echo '<table id="winnersTable" class="w3-table-all w3-center">';
	echo '<tr>';
	echo '<th onclick="sortTable(0, \'winnersTable\')">Name <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(1, \'winnersTable\')">Win Count <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(2, \'winnersTable\')">Win Rate (%) <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(3, \'winnersTable\')">Last Win Date <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(4, \'winnersTable\')">Last Win Game <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(5, \'winnersTable\')">Last Win Score <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '</tr>';
	foreach($summary as $player){
		echo '<tr>';
		echo '<td>' . $player['name'] . '</td>';
		echo '<td>' . $player['wins'] . '</td>';
		echo '<td>' . $player['winRate'] . '</td>';
		//echo '<td>' . date('l jS F Y', strtotime($player['lastWin']['date'])) . '</td>';
		echo '<td>' . date('j F Y', strtotime($player['lastWin']['date'])) . '</td>';
		echo '<td>' . $player['lastWin']['game'] . '</td>';
		echo '<td>' . $player['lastWin']['score'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function outputRecentTable($summary){
	echo '<table id="recentTable" class="w3-table-all w3-center">';
	echo '<tr>';
	echo '<th onclick="sortTable(0, \'recentTable\')">Name <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(1, \'recentTable\')">Total Plays <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(2, \'recentTable\')">Last Played Date <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(3, \'recentTable\')">Last Played Game <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(4, \'recentTable\')">Last Played Score <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '</tr>';
	foreach($summary as $player){
		echo '<tr>';
		echo '<td>' . $player['name'] . '</td>';
		echo '<td>' . $player['plays'] . '</td>';
		//echo '<td>' . date('l jS F Y', strtotime($player['lastPlay']['date'])) . '</td>';
		echo '<td>' . date('j F Y', strtotime($player['lastPlay']['date'])) . '</td>';
		echo '<td>' . $player['lastPlay']['game'] . '</td>';
		echo '<td>' . $player['lastPlay']['score'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function outputAllPlaysTable($plays){

	echo '<table id="allPlaysTable" class="w3-table-all w3-center">';
	echo '<tr>';
	echo '<th onclick="sortTable(0, \'allPlaysTable\')">Name <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(1, \'allPlaysTable\')">Date <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '<th onclick="sortTable(2, \'allPlaysTable\')">Players <image class="w3-right" src="icons/sort-descending.png" width="10" height="10" /></th>';
	echo '</tr>';

	foreach ($plays['play'] as $play){

		if (!isset($play['players'])) continue;
		if ($play['item']['@attributes']['name'] === 'Sooty Saves Sixpence') continue;

		$title = $play['item']['@attributes']['name'];
		$date = $play['@attributes']['date'];
		$players = array();
		foreach ($play['players']['player'] as $player){
			if (!isset($player['@attributes'])) continue;
			if ($player['@attributes']['win'] === "1"){
				$players[] = '<b>' . $player['@attributes']['name'] . '</b>';
			}else{
				$players[] = $player['@attributes']['name'];
			}
			
		}
		$playerStr = implode(', ', $players);
		echo '<tr>';
		echo '<td>' . $title . '</td>';
		echo '<td>' . date('j F Y', strtotime($date)) . '</td>';
		echo '<td>' . $playerStr . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}


/*
function outputTotalSummaryTable($summary){
	echo '<table id="summaryTable" class="w3-table-all w3-center">';
	echo '<tr>';
	echo '<th onclick="sortTable(0)">Name</th>';
	echo '<th onclick="sortTable(1)">Play Count</th>';
	echo '<th onclick="sortTable(2)">Last Played Date</th>';
	echo '<th onclick="sortTable(3)">Last Played Game</th>';
	echo '<th onclick="sortTable(4)">Last Played Score</th>';
	echo '<th onclick="sortTable(5)">Win Count</th>';
	echo '<th onclick="sortTable(5)">Win Rate (%)</th>';
	echo '<th onclick="sortTable(6)">Last Win Date</th>';
	echo '<th onclick="sortTable(7)">Last Win Game</th>';
	echo '<th onclick="sortTable(8)">Last Win Score</th>';
	echo '</tr>';
	foreach($summary as $player){
		echo '<tr>';
		echo '<td>' . $player['name'] . '</td>';
		echo '<td>' . $player['plays'] . '</td>';
		echo '<td>' . date('l jS F Y', strtotime($player['lastPlay']['date'])) . '</td>';
		echo '<td>' . $player['lastPlay']['game'] . '</td>';
		echo '<td>' . $player['lastPlay']['score'] . '</td>';
		echo '<td>' . $player['wins'] . '</td>';
		echo '<td>' . $player['winRate'] . '</td>';
		echo '<td>' . date('l jS F Y', strtotime($player['lastWin']['date'])) . '</td>';
		echo '<td>' . $player['lastWin']['game'] . '</td>';
		echo '<td>' . $player['lastWin']['score'] . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
*/

function getLastPlays($plays, $count){
	$arrLast = array();
	foreach ($plays['play'] as $play){
		$title = $play['item']['@attributes']['name'];
		if (!in_array($title, $arrLast)){
			$arrLast[] = $title;
			if (count($arrLast) >= $count) break;
		}
	}
	return $arrLast;
}

function getLastWins($plays, $count){
	$arrLast = array();
	foreach ($plays['play'] as $play){
		
		//SKIP PLAY IF NO PLAYERS FOUND
		if (!isset($play['players'])) continue;

		//GET THE PLAYERS FOR THIS LOGGED PLAY
		$thisPlayers = $play['players']['player'];
		
		$singleWinner = false;
		$winnerName = 'Nobody';
		$winScore = '';

		//ITERATE THROUGH PLAYERS FOR THIS LOGGED PLAY
		foreach($thisPlayers as $thisPlayer){

			if (!isset($thisPlayer['@attributes'])) continue;

			//CONVENIENCE VARIABLE
			$playerName = $thisPlayer['@attributes']['name'];
			
			if ($thisPlayer['@attributes']['win'] === "1"){
				
				if ($singleWinner === false){
					$singleWinner = true;
					$winnerName = $playerName;
					$winScore = $thisPlayer['@attributes']['score'];
				}else{
					//MORE THAN ONE WINNER
					$singleWinner = null;
					break;
				}
			}
		}

		if ($singleWinner !== null){
			//IF REACHED HERE, ONLY 1 WINNER
			$win = new stdClass();
			$win->game = $play['item']['@attributes']['name'];
			$win->player = $winnerName;
			$win->score = $winScore;
			if (!in_array($win, $arrLast)){
				$arrLast[] = $win;
				if (count($arrLast) >= $count){
					return $arrLast;
				}
			}
		}
	}
	return $arrLast;
}


function getPlayerSummaries($plays, $players){

	$summary = array();

	foreach($players as $player){
		$summary[$player] = [];
		$summary[$player]['name'] = $player;
		$summary[$player]['plays'] = 0;
		$summary[$player]['wins'] = 0;
		$summary[$player]['winRate'] = 0;
		$summary[$player]['lastPlay'] = [];
		$summary[$player]['lastPlay']['date'] = 0;
		$summary[$player]['lastPlay']['game'] = 0;
		$summary[$player]['lastPlay']['score'] = 0;
		$summary[$player]['lastWin'] = [];
		$summary[$player]['lastWin']['date'] = 0;
		$summary[$player]['lastWin']['game'] = 0;
		$summary[$player]['lastWin']['score'] = 0;
	}

	//var_dump($plays);

	//ITERATE THROUGH LOGGED PLAYS (REVERSE CHRONO)
	foreach($plays['play'] as $play){

		//SKIP PLAY IF "Sooty Saves Sixpence"
		if ($play['item']['@attributes']['name'] === 'Sooty Saves Sixpence') continue;
		//echo 'PROCESSING PLAY FOR ' . $play['item']['@attributes']['name'] . '<br>';

		//SKIP PLAY IF NO PLAYERS FOUND
		if (!isset($play['players'])) continue;
		
		//GET THE PLAYERS FOR THIS LOGGED PLAY
		$thisPlayers = $play['players']['player'];
		//var_dump($thisPlayers);

		//ITERATE THROUGH PLAYERS FOR THIS LOGGED PLAY
		foreach($thisPlayers as $thisPlayer){

			if (!isset($thisPlayer['@attributes'])) continue;

			//CONVENIENCE VARIABLE
			$playerName = $thisPlayer['@attributes']['name'];
			//echo 'PROCESSING PLAYER ' . $thisPlayer['@attributes']['name'] . '<br>';

			/*
			echo 'THIS PLAYER = <br>';
			var_dump($thisPlayer);
			echo '=================== <br>';
			*/

			//IF THE FOUND PLAYER IS IN THE PLAYERS ARRAY
			if (in_array($playerName, $players)){

				//INCREMENT THIS PLAYER'S PLAY COUNT
				$summary[$playerName]['plays']++;

				//IF THIS WAS A WIN FOR THE PLAYER
				if ($thisPlayer['@attributes']['win'] === "1"){

					$summary[$playerName]['wins']++;

					//IF NO LAST WIN IS RECORDED
					if ($summary[$playerName]['lastWin']['date'] === 0){

						//STORE THIS PLAY AS THEIR LAST WIN
						$summary[$playerName]['lastWin']['date'] = $play['@attributes']['date'];
						$summary[$playerName]['lastWin']['game'] = $play['item']['@attributes']['name'];
						$summary[$playerName]['lastWin']['score'] = (int)$thisPlayer['@attributes']['score'];
					}
					
				}

				//IF NO LAST PLAY IS RECORDED
				if ($summary[$playerName]['lastPlay']['date'] === 0){
					//STORE THIS PLAY AS THEIR LAST WIN

					$summary[$playerName]['lastPlay']['date'] = $play['@attributes']['date'];
					$summary[$playerName]['lastPlay']['game'] = $play['item']['@attributes']['name'];
					$summary[$playerName]['lastPlay']['score'] = (int)$thisPlayer['@attributes']['score'];
					
				}				
			}
		}
		//echo 'END PROCESSING<br>';
		//echo '==================<br><br>';
	}

	foreach ($players as $playerName){
		//
		if ($summary[$playerName]['plays'] !== 0){
			$summary[$playerName]['winRate'] = floor( ($summary[$playerName]['wins'] / $summary[$playerName]['plays']) * 100);
		}
	}
	return $summary;
}

?>

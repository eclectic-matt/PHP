<?php

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
<title>Game Highlights</title>
<style>

* {
	box-sizing: border-box;
}

body {
	margin-top: 0rem;
	padding-bottom: 8rem;
	margin-bottom: 0rem;
	/* margin-top: 2rem;
	padding-top: 8rem;
	padding-bottom: 8rem;
	margin-bottom: 2rem; */
}

@-webkit-keyframes ticker {
	0% {
		-webkit-transform: translate3d(0, 0, 0);
		transform: translate3d(0, 0, 0);
		visibility: visible;
   }
	100% {
		-webkit-transform: translate3d(-100%, 0, 0);
		transform: translate3d(-100%, 0, 0);
   }
}

@keyframes ticker {
	0% {
		-webkit-transform: translate3d(0, 0, 0);
		transform: translate3d(0, 0, 0);
		visibility: visible;
   }
	100% {
		-webkit-transform: translate3d(-100%, 0, 0);
		transform: translate3d(-100%, 0, 0);
   }
}

.topTick {
	top: 0;
	background-color: rgb(197, 197, 197);
	/* background-color: rgba(172, 37, 37, 0.9); */
	color: black;
}

.btmTick {
	bottom: 0;
	background-color: rgb(197, 197, 197);
	color: black;
}

/* 
Ticker source from
https://codepen.io/lewismcarey/pen/GJZVoG
*/
.tickerWrap {
	position: fixed;
	width: 100%;
	overflow: hidden;
	height: 2.5rem;
	/* height: 8rem; */
	padding-left: 100%;
	box-sizing: content-box;
}

.tickerWrap .ticker {
	display: inline-block;
	height: 2rem;
	padding-top: 1rem;
	line-height: 0.5rem;
	white-space: nowrap;
	padding-right: 100%;
	box-sizing: content-box;
	-webkit-animation-iteration-count: infinite;
	animation-iteration-count: infinite;
	-webkit-animation-timing-function: linear;
	animation-timing-function: linear;
	-webkit-animation-name: ticker;
	animation-name: ticker;
	-webkit-animation-duration: 120s;
	animation-duration: 120s;
}

/*
	NOTE: .tickerItem timings included inline in stats.php
	Due to caching issues with TV
*/
.tickerWrap .tickerItem {
	display: inline-block;
	padding: 0 2rem;
	/* padding: 0 2rem; */
	/* font-size: 4rem; */
	font-size: 26.25px;
	/*color: white;*/
}

#statsDiv{
	padding-top: 8rem;
}

h1, h2, p {
	padding: 0 5%;
}

#popupDiv{
	padding: 25px;
	font-size: 26.25px;
	margin-top: 85px;
	margin-bottom: 100px;
}

#popupDiv span{
	-webkit-animation-iteration-count: infinite;
	animation-iteration-count: infinite;
	-webkit-animation-timing-function: linear;
	animation-timing-function: linear;
	-webkit-animation-name: flash;
	animation-name: flash;
	-webkit-animation-duration: 5s;
	animation-duration: 5s;
}

@-webkit-keyframes flash {
	0% {
		opacity: 0;
	}
	50%{
		opacity: 0.75;
	}
	100% {
		opacity: 1;
	}
}

@keyframes flash {
	0% {
		opacity: 0;
	}
	50%{
		opacity: 0.75;
	}
	100% {
		opacity: 1;
	}
}

</style>

<script>

	var popupTimer, popupArray, currentIndex;

	function init(){

		//document.getElementById('navBox').style.display = 'none';
		var totalPlayCount = <?php echo count($allPlays['play']); ?>;
		var players = {<?php 
			/* GENERATE JS ARRAY TO PARSE */
			$index = 0;
			$lastPlayDate = date('Y-m-d',strtotime('-1 year'));
			$lastPlayGame = '';
			//$lastPlayWinner = '';
			foreach ($playersSummary as $player => $summary){
				if ($player === 'Matt'){
					$player = 'Matt Tiernan';
				}
				echo '"' . $player . '": {';
				echo '"Plays": ' . $summary['plays'] . ',';
				echo '"Wins": ' . $summary['wins'] . ',';
				echo '"Win Rate": "' . $summary['winRate'] . '%",';
				echo '"Last Play Date": "' . strftime('%a, %e %b %G', strtotime($summary['lastPlay']['date'])) . '",';
				echo '"Last Play Game": "' . $summary['lastPlay']['game'] . '",';
				echo '"Last Play Score": "' . $summary['lastPlay']['score'] . '"';
				echo '}';
				//INCREMENT INDEX
				$index++;
				//CHECK IF THIS IS THE LAST PLAYED DATE
				if ($lastPlayDate < $summary['lastPlay']['date']){
					$lastPlayDate = $summary['lastPlay']['date'];
					$lastPlayGame = $summary['lastPlay']['game'];
				}
				if ($index !== count($playersSummary)){
					echo ',';
				}
			}?>};

		<?php 
			echo 'lastPlay = {"date": "' . $lastPlayDate . '", "game": "' . $lastPlayGame . '"};';//, "winner": "' . $lastPlayWinner . '"}';
		?>

		popupArray = generatePopupArray(totalPlayCount, lastPlay, players);
		currentIndex = 0;
		popupTimer = setInterval(showNextPopup, 5000);
	}

	function showNextPopup(){

		var thisPopup = popupArray[currentIndex];
		document.getElementById('popupDiv').innerHTML = '<span>' + thisPopup + '</span>';
		currentIndex++;
		if (currentIndex === popupArray.length){
			currentIndex = 0;
		}
	}


	function generatePopupArray(totalPlayCount, lastPlay, players){

		//SETUP ARRAY
		var array = [];
		//PREPARE DATES
		const today = new Date();
		var lastPlayDate = new Date(lastPlay.date);
		const diffTime = Math.abs(today - lastPlayDate);
		const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
		 // Saturday, September 17, 2016
		var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
		
		//ADD FIXED VALUES
		array.push("Days Since Last Play:<br>" + (diffDays - 1));
		lastPlayDate = lastPlayDate.toLocaleDateString("en-UK", options);
		array.push("Last Play Date:<br>" + lastPlayDate);
		lastPlayGame = lastPlay.game;
		array.push("Last Play Game:<br>" + lastPlayGame);
		array.push("Total Logged Plays:<br>" + totalPlayCount);

		for (var name in players) {
			stats = players[name];
			for (var stat in stats){
				array.push(name + " - " + stat + ":<br>" + stats[stat]);
			}
		}
		return array;
	}
</script>
</head>
<body class='w3-dark-grey' onload='init()'>

<!--div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
<?php //include 'navbar.php'; ?>
</div-->

<?php
//var_dump($playersSummary);
//RECENT PLAYS
echo '<div class="w3-grey tickerWrap topTick">';
echo '<div class="ticker">';
//TITLE
echo '<div class="tickerItem">';
echo 'LAST ' . $lastPlaysCount . ' GAMES ===>';
echo '</div>';
//SHOW RECENT PLAYED GAMES
foreach($lastPlays as $title){
	echo '<div class="tickerItem">';
	echo '';
	echo $title;
	echo ' ----- ';
	echo '</div>';
}
echo '</div>';
echo '</div>';

//LAST TEN WINNERS
echo '<div class="w3-grey tickerWrap btmTick">';
echo '<div class="ticker">';
//TITLE
echo '<div class="tickerItem">';
echo 'LAST ' . $lastWinsCount . ' WINNERS ===>';
echo '</div>';
//SHOW RECENT PLAYED GAMES
foreach($lastWins as $win){
	echo '<div class="tickerItem">';
	echo '<b>' . $win->player . '</b> won <em>' . $win->game . '</em>';
	if ($win->score !== ''){
		echo ' (score: ' . $win->score . ')';
	}
	echo ' ----- ';
	echo '</div>';
}
echo '</div>';
echo '</div>';

echo '<div id="popupDiv" class="w3-container w3-green w3-center w3-card-8">';
echo '	<span>BOARD GAME HIGHLIGHTS</span>';
echo '</div>';

echo '</div>';
echo '</html>';

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
	$encoded = file_get_contents($file);
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

<?php

require './classes/Calendar.class';
require './classes/Event.class';
require './classes/Date.class';

const PAGE_REFRESH_SECONDS = 15;

//SET TIME DEFAULTS
date_default_timezone_set('Europe/London');


$startStr = '2021-07-01';
$endStr = 'now';
echo "Start = $startStr; Finish = $endStr<br>";
$daysBetween = DatePlus::getDurationBetween($startStr, $endStr, 'days');
echo "Days between = $daysBetween<br>";
$weeksBetween = DatePlus::getDurationBetween($startStr, $endStr, 'weeks');
echo "Weeks between = $weeksBetween<br>";
$monthsBetween = DatePlus::getDurationBetween($startStr, $endStr, 'months');
echo "Months between = $monthsBetween<br>";
$yearsBetween = DatePlus::getDurationBetween($startStr, $endStr, 'years');
echo "Years between = $yearsBetween<br>";

if ($weeksBetween % 2 === 0){
	echo "Fortnightly event should occur/re-occur this week!";
	$matching = DatePlus::getMatchingDate($startStr, $weeksBetween . 'W');
	echo "<br> Fortnightly Date = " . $matching->format('Y-m-d');	
}else{
	echo "Fortnightly event should re-occur next week!";
	$matching = DatePlus::getMatchingDate($startStr, 1 + $weeksBetween . 'W');
	echo "<br> Fortnightly Date = " . $matching->format('Y-m-d');	
}
echo "<br>";

$matching = DatePlus::getMatchingDate($startStr, $weeksBetween . 'W');
echo "<br> Week Date = " . $matching->format('Y-m-d');
$matching = DatePlus::getMatchingDate($startStr, 1 + $monthsBetween . 'M');
echo "<br> Month Date = " . $matching->format('Y-m-d');
$matching = DatePlus::getMatchingDate($startStr, 1 + $yearsBetween . 'Y');
echo "<br> Year Date = " . $matching->format('Y-m-d');

?>

<!DOCTYPE html>
<html>
<head>
<!--meta http-equiv="refresh" content="<?php //echo PAGE_REFRESH_SECONDS; ?>;" /-->
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<title>Housemates Dashboard</title>
<!--script src='js/timer.js'></script-->
<style>
window{
	overflow: hidden;
}
body {
    padding: 10px;
    overflow: hidden;
}

.eventBox {
    border: 3px solid black;
    padding: 5px;
    margin: 5px;
    /*line-height: 0.5;*/
}

.eventBox h3 {
	font-size: 40px;
	font-weight: bold;
}

.eventBox em {
    font-size: 14px;
    line-height: 1;
	font-weight: normal;
}
</style>
<script src='../../js/browser.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
	var elements = [];
	var current = [];
	function pageInit(){
		//window.scrollTo(0,0);
		document.documentElement.scrollTop = 0;
		detectIframe();
		
		//var swell = document.getElementById('swellDiv');
		//swell.style.height = 0;
		//setInterval(function(){ swellDiv(); }, 100);
		
		scrollWindow();
	}

	function swellDiv(){
		var swell = document.getElementById('swellDiv');
		swell.style.height += 1;
	}

	function scrollWindow(){
		var calEls = document.getElementsByClassName('eventBox');
		var delay = 1000;
		var incr = 1500;
		var l = calEls.length;
		for (i = 0; i < l; i++) {
			var thisEl = calEls[i].id;
			elements.push(thisEl);
		}
		current = elements.slice();
		setInterval(function(){ scrollToNextEl(); }, delay);
	}
	function scrollToNextEl(){
		if (current.length === 0){
			current = elements.slice();
		}
		id = current.shift();
		//console.log('Scrolling to ' + id);
		
		el = document.getElementById(id);
		
		//document.documentElement.scrollTop = document.getElementById(id).offsetTop;
		
		//console.log(el);
		el.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});

		//parent = window.document.getElementById('upiframe');
		//parent.scrollTop = el.offsetTop;
	}

</script>
</head>
<body class='w3-dark-grey' onload='pageInit()'>

<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
	<a href='http://192.168.2.137:8080/housemates/home.php'><button class='w3-red'>Back to Housemates Home</button></a>
</div>

<div id='swellDiv'>
</div>

<div id='calEventsDiv'>

	<?php
		//INSTANTIATE THE CALENDAR CLASS (generates)
		$thisCal = new Calendar();
		//THEN OUTPUT IN THE DESIRED FORMAT
		//$thisCal->outputCalendar();
		$thisCal->outputWeekToView();
	?>

</div>

<div id='bottomOfView'></div>
</body>
</html>
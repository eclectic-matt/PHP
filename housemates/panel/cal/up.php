<?php


//phpinfo();
//exit();

require './classes/Calendar.class';
require './classes/Event.class';

//const PAGE_REFRESH_SECONDS = 15;
const PAGE_REFRESH_SECONDS = 60;

//SET TIME DEFAULTS
date_default_timezone_set('Europe/London');

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="<?php echo PAGE_REFRESH_SECONDS; ?>;" />
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<title>Housemates Dashboard</title>
<!--script src='js/timer.js'></script-->
<style>
window{
	overflow: hidden !important;
}
body {
    padding: 10px;
    overflow: hidden !important;
	font-family: "Segoe UI", Arial, sans-serif;
}

.eventBox {
    border: 3px solid black;
    padding: 5px;
    margin: 5px;
    /*line-height: 0.5;*/
}

.eventBox h3 {
	/* font-size: 40px; */
	font-size: 26.25px;
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
		document.documentElement.scrollTop = 0;
		detectIframe();

		scrollWindow();
	}

	function scrollWindow(){
		var calEls = document.getElementsByClassName('eventBox');
		var delay = 5000;
		var incr = 5000;
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
		el.scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
	}

</script>
</head>
<body class='w3-dark-grey' onload='pageInit()'>

<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
	<a href='/housemates/home.php'><button class='w3-red'>Back to Housemates Home</button></a>
</div>

<div id='swellDiv'>
</div>

<div id='calEventsDiv'>

	<?php
		//INSTANTIATE THE CALENDAR CLASS (generates)
		$thisCal = new Calendar();
		//THEN OUTPUT IN THE DESIRED FORMAT
		$thisCal->outputUpcoming();
		//$thisCal->outputWeekToView();
	?>

</div>

<div id='bottomOfView'></div>
</body>
</html>
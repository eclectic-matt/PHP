<?php

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

table {
    width: 100%;
    table-layout: fixed;
}
table, th, td {
    border: 1px solid white;
    border-collapse: collapse;
    text-align: center;
}
th {
	font-size: 12px;
	font-weight: bold;
}
td:nth-child(1){
	font-size: 14px;
}
td:nth-child(2){
	/* font-size: 18px; */
	font-size: 26.25px;
	font-weight: bold;
	font-family: "Segoe UI", Arial, sans-serif;
}
</style>
<script src='../../js/browser.js'></script>
</head>
<body class='w3-dark-grey' onload='detectIframe()'>

<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
	<a href='/housemates/home.php'><button class='w3-red'>Back to Housemates Home</button></a>
</div>

<?php
	//INSTANTIATE THE CALENDAR CLASS (generates)
	$thisCal = new Calendar();
	//THEN OUTPUT IN THE DESIRED FORMAT
	//$thisCal->outputCalendar();
	$thisCal->outputWeekToView();
?>

</body>
</html>
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/housemates/config/Config.class';

//GET DATA FROM CONFIG FILE
$config = new Config();

//DISPLAY SETTINGS
$screenRefreshSeconds = $config::$data['display']['refreshSeconds'];
$qrCodeSizeHeading = $config::$data['display']['qrCodeSizeHeading'];
$qrCodeSizePanel = $config::$data['display']['qrCodeSizePanel'];
$topiframeHeight = $config::$data['display']['topiframeHeight'];
$btmiframeHeight = $config::$data['display']['btmiframeHeight'];
//$iframeHeight = $config::$data['display']['iframeHeight'];

//NETWORK/URL SETTINGS
$pageTitle = $config::$data['home']['title'];
$urlBase = $config::$data['network']['urlBase'];
$homePath = $config::$data['home']['path'];
$urlHome = htmlspecialchars($urlBase . $homePath);

//MAIN HEADING
$headingClass = $config::$data['theme']['headingClass'];

//UPCOMING EVENTS
$upcomingClass = $config::$data['theme']['headingClass'];
$upcomingPath = 'panel/cal/up.php';
$urlUpcoming = htmlspecialchars($urlBase . $upcomingPath);

//WEEK TO VIEW
$wtvClass = $config::$data['theme']['headingClass'];
$wtvPath = 'panel/cal/wtv.php';
$urlWeekToView = htmlspecialchars($urlBase . $wtvPath);

//TO DO
$todoClass = $config::$data['theme']['headingClass'];
$todoPath = 'panel/todo/toDo.php';
$todoAddPath = 'panel/todo/add.php';
$todoCompletePath = 'panel/todo/complete.php';
$urlToDo = htmlspecialchars($urlBase . $todoPath);

//GALLERY
$wbClass = $config::$data['theme']['headingClass'];
$wbPath = 'panel/wb/wb.php';
$urlWhiteboard = htmlspecialchars($urlBase . $wbPath);
$pgPath = 'panel/photo/frame.php';
$urlGallery = htmlspecialchars($urlBase . $pgPath);

//GAME PICKER
$pickerClass = $config::$data['theme']['headingClass'];
$pickerPath = 'panel/games/picker.php';
$urlGamePicker = htmlspecialchars($urlBase . $pickerPath);

//GAME STATS
$statsClass = $config::$data['theme']['highlightClass'];
$statsPath = 'panel/games/stats.php';
$urlGameStats = htmlspecialchars($urlBase . $statsPath);
$highlightsPath = 'panel/games/highlights.php';

?>
<!DOCTYPE html>
<html>
<head>
<!--meta http-equiv="refresh" content="<?php //echo $screenRefreshSeconds; ?>" /-->
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<title>Housemates Dashboard</title>
<script src='js/timer.js'></script>
<script>
function pageInit(){
	//window.scrollTo(0,0);
	updateDateTime();
	//var t = setInterval(250, scrollEachIframe);
	//setInterval(function(){ scrollEachIframe(); }, 20);


	/*$("#myFrame").on("load", function () {
    alert("Hi there, hello");*/
}

function scrollEachIframe(){
	var arrIframes = document.getElementsByTagName('iframe');
	for (var i = 0; i < arrIframes.length; i++){
		if ( (arrIframes[i] === null) || (arrIframes[i].id === 'statsiframe')){
			//console.log('Skipping this one');
		}else{
			var thisFrame = arrIframes[i].contentWindow;
			//console.log('Scrolling to ',thisFrame.document.documentElement.offsetTop);
			thisFrame.document.documentElement.scrollTop += 1;
			if (thisFrame.document.documentElement.offsetTop >= thisFrame.document.documentElement.height){
				console.log('Resetting ',arrIframes[i].id);
				console.log(arrIframes);
				thisFrame.document.documentElement.scrollTop = 0;
				thisFrame.document.documentElement.offsetTop = 0;
			}
		}
	}
}
</script>	
<style>
body {
	height: 100%;
  	overflow: hidden !important;
	overflow-x: hidden !important;
    overflow-y: hidden !important;
	-ms-overflow-style: none;  /* IE and Edge */
  	scrollbar-width: none;  /* Firefox */
}
/* Hide scrollbar for Chrome, Safari and Opera */
body::-webkit-scrollbar {
  display: none;
  width: 0px;
  visibility: hidden;
}


#headingDiv{
	/*position: fixed;
	top: 0px;
	left: 30%;*/
	padding: 10px 20px 5px 20px;
	line-height: 0.6;
	font-size: 2.5em;
	border: 5px solid black;
}
#currentDateTimeDiv{
	color: yellow;
	font-style: italic;
}
.pageHeading {
	font-size: 2em;
	font-weight: bolder;
	color: white;
}
button{
	/*border: 1px solid white !important;*/
}
button:hover{
	/*border: 1px solid red;*/
	background-color: red;
	color: black;
}
a button {
	margin-top: 10px !important;
	text-decoration: none !important;
	font-size: 2em;
	font-weight: bold;
}
a {
	text-decoration: none !important;
}

iframe {
    overflow: hidden !important;
    overflow-x: hidden !important;
    overflow-y: hidden !important;
	border: none;
}
iframe *{
    -webkit-transform:scale(0.5);
    -moz-transform-scale(0.5);
}
div.w3-col.l4{
	/*border: 10px outset #616161;*/
}
.qrCodeR{
	position: absolute;
	/*float: right;*/
	right: 5px;
	top: 10px;
	line-height: 0;
}
.qrCodeL{
	position: absolute;
	/*float: right;*/
	left: 5px;
	top: 10px;
	line-height: 0;
}

</style>
</head>
<body class='w3-dark-grey' onload='pageInit()'>

	<div class='wrapper'>

		<!------------
			HEADER
		------------->
		<div class='w3-row-padding w3-center <?php echo $headingClass; ?>' id='headingDiv'>
			<div>
			<img class='w3-right w3-hide-small w3-hide-medium qrCodeL' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizeHeading . 'x' . $qrCodeSizeHeading; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlHome;?>'/>
				<span id='currentDateTimeDiv'>Wednesday 16th June 2021 @ 05:00:00 PM</span>
				<h1 class='pageHeading w3-wide'><?php echo $pageTitle; ?></h1>
				<img class='w3-right w3-hide-small w3-hide-medium qrCodeR' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizeHeading . 'x' . $qrCodeSizeHeading; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlHome;?>'/>
			</div>
		</div>

		<!------------
			DIVIDER
		------------->
		<div class='w3-hide-medium w3-hide-large' style="height:10px;">
		</div>

		<!------------
			TOP ROW
		------------->
		<div class='w3-row-padding w3-center'>

			
			
			<!--------------------->
			<!--    TO DO LIST   -->
			<!--------------------->
			<div class='w3-col l4'>

				<!-- LARGE SCREEN (TV) VIEW -->
				<div class='w3-row w3-hide-small w3-hide-medium <?php echo $todoClass; ?>'>
					<div class='w3-col s10'>
						<a href="<?php echo $todoPath; ?>"><button class="w3-button <?php echo $todoClass; ?> w3-block">To Do</button></a>
					</div>
					<div class='w3-col s2'>
						<img style='float:right;' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizePanel . 'x' . $qrCodeSizePanel; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlToDo;?>'/>
					</div>
				</div>

				<!-- SMALL SCREEN (PHONE) VIEW -->
				<div class='w3-row w3-hide-large <?php echo $todoClass; ?>'>
					<div class='w3-col s4'>
						<a href="<?php echo $todoAddPath; ?>"><button class="w3-button w3-xxlarge w3-border-green <?php echo $todoClass; ?> w3-block">Add</button></a>
					</div>
					<div class='w3-col s4'>
						<a href="<?php echo $todoPath; ?>"><button class="w3-button w3-xxlarge w3-border-green <?php echo $todoClass; ?> w3-block">View ToDo(s)</button></a>
					</div>
					<div class='w3-col s4'>
						<a href="<?php echo $todoCompletePath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $todoClass; ?> w3-block">Complete</button></a>
					</div>
				</div>

				<iframe id="toDoiframe" scrolling="no" src='<?php echo $todoPath; ?>' width='100%' height='<?php echo $topiframeHeight;?>'>
				</iframe>

			</div>
			
			
			
		

			<!--------------------->
			<!--GAME PICKER/STATS-->
			<!--------------------->
			<div class='w3-col l4'>

				<!-- LARGE SCREEN (TV) VIEW -->
				<div class='w3-row w3-hide-small w3-hide-medium <?php echo $pickerClass; ?>'>
					<div class='w3-col s5'>
						<a href="<?php echo $pickerPath; ?>"><button class="w3-button w3-border-red <?php echo $pickerClass; ?> w3-block">GamePick</button></a>
					</div>
					<div class='w3-col s5'>
						<a href="<?php echo $statsPath; ?>"><button class="w3-button w3-border-red <?php echo $pickerClass; ?> w3-block">GameStats</button></a>
					</div>
					<div class='w3-col s2'>
						<img style='float:right;' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizePanel . 'x' . $qrCodeSizePanel; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlGamePicker;?>'/>
					</div>
				</div>
				<!-- SMALL SCREEN (PHONE) VIEW -->
				<div class='w3-row w3-hide-large <?php echo $pickerClass; ?>'>
					<div class='w3-col s6'>
						<a href="<?php echo $pickerPath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $pickerClass; ?> w3-block">Game Picker</button></a>
					</div>
					<div class='w3-col s6'>
						<a href="<?php echo $statsPath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $pickerClass; ?> w3-block">Game Stats</button></a>
					</div>
				</div>

				<!--iframe id='statsiframe' scrolling="no" src='<?php //echo $statsPath; ?>' width='100%' height='<?php //echo $iframeHeight;?>'-->
				<iframe id='statsiframe' scrolling="no" src='<?php echo $highlightsPath; ?>' width='100%' height='<?php echo $topiframeHeight;?>'>
				</iframe>
				<?php //include 'panel/whiteboard/whiteboard.php'; ?>
			</div>




			<!--------------------->
			<!--   WHITE BOARD   -->
			<!--------------------->
			<div class='w3-col l4'>

				<!-- LARGE SCREEN (TV) VIEW -->
				<div class='w3-row w3-hide-small w3-hide-medium <?php echo $wbClass; ?>'>
					<div class='w3-col s10'>
						<a href="<?php echo $pgPath; ?>"><button class="w3-button w3-border-red <?php echo $wbClass; ?> w3-block">Gallery</button></a>
					</div>
					<div class='w3-col s2 w3-hide-small w3-hide-medium'>
						<img style='float:right;' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizePanel . 'x' . $qrCodeSizePanel; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlGallery;?>'/>
					</div>
				</div>

				<!-- SMALL SCREEN (PHONE) VIEW -->
				<div class='w3-row w3-hide-large <?php echo $wbClass; ?>'>
					<div class='w3-col s6'>
						<a href="<?php echo $pgPath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $wbClass; ?> w3-block">Gallery</button></a>
					</div>
					<div class='w3-col s6'>
						<a href="panel/photo/add.php"><button class="w3-button w3-xxlarge w3-border-red <?php echo $wbClass; ?> w3-block">Add Photo</button></a>
					</div>
				</div>


				<!-- <iframe id='wbiframe' class="w3-hide-small w3-hide-medium" scrolling="no" src='<?php //echo $wbPath; ?>' width='490px' height='<?php //echo '650px';//echo $iframeHeight;?>' style='position:absolute; right: 17px;'> -->
				<iframe id='pgiframe' class="w3-hide-small w3-hide-medium" scrolling="no" src='<?php echo $pgPath; ?>' width='490px' height='<?php echo '650px';//echo $iframeHeight;?>' style='position:absolute; right: 17px;'>
				
				<!--iframe src='<?php //echo $wbPath; ?>' width='100%' height='250px;' style='position:fixed; height: 500px; -webkit-transform:scale(0.5);-moz-transform-scale(0.5);'-->
				</iframe>
				<!-- BUFFER DIV FOR SMALL -->
				<div class="w3-hide-large" style="height:50px"></div>

				<?php //include 'panel/whiteboard/whiteboard.php'; ?>
			</div>


		</div>


		<!-- BOTTOM ROW -->
		<div class='w3-row-padding w3-center'>



			<!--------------------->
			<!-- UPCOMING EVENTS -->
			<!--------------------->
			<div class='w3-col l4'>

				<!-- LARGE SCREEN (TV) VIEW -->
				<div class='w3-row w3-hide-small w3-hide-medium <?php echo $upcomingClass; ?>'>
					<div class='w3-col s10'>
						<a href="<?php echo $upcomingPath; ?>"><button class="w3-button w3-border-red <?php echo $upcomingClass; ?> w3-block">Upcoming Events</button></a>
					</div>
					<div class='w3-col s2'>
						<img style='float:right;' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizePanel . 'x' . $qrCodeSizePanel; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlUpcoming;?>'/>
					</div>
				</div>
				<!-- SMALL SCREEN (PHONE) VIEW -->
				<div class='w3-row w3-hide-large <?php echo $upcomingClass; ?>'>
					<div class='w3-col s12'>
						<a href="<?php echo $upcomingPath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $upcomingClass; ?> w3-block">Upcoming Events</button></a>
					</div>
				</div>

				<iframe  id="upiframe" scrolling="no" src='<?php echo $upcomingPath; ?>' width='100%' height='<?php echo $btmiframeHeight;?>'>
				</iframe>
				<?php //include 'panel/calendar/upcoming.php'; ?>
			</div>



			<!--------------------->
			<!--   WEEK TO VIEW  -->
			<!--------------------->
			<div class='w3-col l4'>

				<!-- LARGE SCREEN (TV) VIEW -->
				<div class='w3-row w3-hide-small w3-hide-medium <?php echo $wtvClass; ?>'>
					<div class='w3-col s10'>
						<a href="<?php echo $wtvPath; ?>"><button class="w3-button w3-border-red <?php echo $wtvClass; ?> w3-block">Week To View</button></a>
					</div>
					<div class='w3-col s2'>
						<img style='float:right;' src='https://chart.googleapis.com/chart?chs=<?php echo $qrCodeSizePanel . 'x' . $qrCodeSizePanel; ?>&cht=qr&choe=UTF-8&chl=<?php echo $urlWeekToView;?>'/>
					</div>
				</div>

				<!-- SMALL SCREEN (PHONE) VIEW -->
				<div class='w3-row w3-hide-large <?php echo $wtvClass; ?>'>
					<div class='w3-col s12'>
						<a href="<?php echo $wtvPath; ?>"><button class="w3-button w3-xxlarge w3-border-red <?php echo $wtvClass; ?> w3-block">Week To View</button></a>
					</div>
				</div>

				<!-- IFRAME HERE -->
				<iframe scrolling="no" src='<?php echo $wtvPath; ?>' width='100%' height='<?php echo $btmiframeHeight;?>'>
				</iframe>

			</div>

			

			<!-- BUFFER DIV 
			<div class='w3-col l4 w3-hide-small w3-hide-medium'>
			</div>-->


		</div>

	</div>


</body>


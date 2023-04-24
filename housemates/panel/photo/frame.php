<?php

/*GET LIST OF IMAGES = OUTPUT INTO PAGE SO JS CAN PICK UP*/
$imagesDir = './images';
$dir = scandir($imagesDir);
$imagesArr = array();
foreach ($dir as $key => $value){

	if (!in_array($value,array(".",".."))){

		if (is_file($imagesDir . DIRECTORY_SEPARATOR . $value)){

			//NO ROTATE
			$rotate = '0deg';
			$imagesArr[$value] = $rotate;
		}
	}
}
//$imagesStr = implode(',',$imagesArr);

//60 minute refresh - prevent images reloading too frequently
const PAGE_REFRESH_SECONDS = 60 * 60;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Housemates Gallery</title>
	<meta http-equiv="refresh" content="<?php echo PAGE_REFRESH_SECONDS; ?>;" />
	<script type="text/JavaScript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
	<script src='../../js/browser.js'></script>
	<!-- <script defer src='./js/slideshow.js'></script> -->
	<!-- <link rel="stylesheet" href='./css/photoStyles.css'></script> -->
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<script>
		var slideIndex = 1;
		var changeDelay = 20000;
		
		function init(){
			showSlides(slideIndex);
			setInterval(plusSlides, changeDelay, 1);
		}

		function plusSlides(n) {
			showSlides(slideIndex += n);
		}

		function showSlides(n) {
			var i;
			var slides = document.getElementsByClassName("mySlides");
			if (n > slides.length) {slideIndex = 1}
			if (n < 1) {slideIndex = slides.length}
			for (i = 0; i < slides.length; i++) {
				slides[i].style.display = "none";
			}
			slides[slideIndex-1].style.display = "block";
		}

	</script>
	<style>

		/*https://stackoverflow.com/a/60961683/16384571*/

		.mySlides {
			display: none;
			/*display: flex;*/ /* for alignment */
			justify-content: center; /* horizontally align portrait image */
			align-items: center; /* vertically align landscape image */

			/** fixed width, creates a square for our image to live */
			width: 500px;
			height: 500px;
			/* Could be styles with a responsive technique a like aspect ratio prop, but that is outside the scope of here */

			background-color: #616161; /* so you can see the "square", for demo purposes */
		}

		.mySlides img {
			width: auto; /* to counter any width attributes and allow intrinsic image width */
			height: auto; /* to counter any height attributes and allow intrinsic height */
			max-width: 100%; /* scale with the parent element width */
			max-height: 100%; /* scale with the parent element height */
			-webkit-animation-iteration-count: 1;
			animation-iteration-count: 1;
			-webkit-animation-timing-function: linear;
			animation-timing-function: linear;
			-webkit-animation-name: fadeIn;
			animation-name: fadeIn;
			-webkit-animation-duration: 5s;
			animation-duration: 5s;
		}
		@-webkit-keyframes fadeIn {
			0% {
				opacity: 0%;
			}
				100% {
					opacity: 100%;
			}
		}
		@keyframes fadeIn {
			0% {
				opacity: 0%;
			}
				100% {
					opacity: 100%;
			}
		}


	</style>
</head>
<body onload="init()">

	<div class="container">

	<?php
		//BASED MAINLY ON:
		//https://www.w3schools.com/howto/howto_js_slideshow_gallery.asp
		$imageIndex = 0;
		$imageCount = count($imagesArr);
		foreach ($imagesArr as $imgUrl => $rotate){
			$imageIndex++;
			echo '<div class="mySlides">';
			echo '	<!--div class="numbertext">' . $imageIndex . ' / ' . $imageCount . '</div-->';
			//echo '	<span class="helper"></span>';
			echo '<img id="' . $imgUrl . '" src="./images/' . $imgUrl . '">';
			echo '</div>';
		}
	?>

	</div>
</body>
</html>
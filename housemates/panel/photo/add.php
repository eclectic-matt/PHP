<?php

//RESIZE/EDIT WITH PHP (gd is enabled in xampp)
//https://code.tutsplus.com/tutorials/php-gd-image-manipulation-beyond-the-basics--cms-31766
//https://code.tutsplus.com/tutorials/manipulating-images-in-php-using-gd--cms-31701


//https://www.w3schools.com/php/php_file_upload.asp
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {

	$target_dir = "images/ORIGINAL SIZE/";
	//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$thisFileName = 'gallery_' . date('Ymd_His') . '.png';
	$target_file = $target_dir . $thisFileName;
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$errors = [];

	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

	if($check !== false) {
		//$errors[] = "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		$errors[] = "File is not an image.";
		$uploadOk = 0;
	}

	// Check if file already exists
	if (file_exists($target_file)) {
		$errors[] = "Sorry, file already exists.";
		$uploadOk = 0;
	}

	// Check file size
	/*if ($_FILES["fileToUpload"]["size"] > 5000000) {
		$errors[] = "Sorry, your file is too large (max 50MB).";
		$uploadOk = 0;
	}*/

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		$errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}

	//Report errors
	if (count($errors) > 0){
		echo '<div class="w3-row w3-red w3-center w3-xlarge w3-block" id="errorBox">';
		echo '<h2>There was an issue with your upload:</h2>';
		echo implode(', ', $errors);
		echo '</div>';
	}else{
		echo '<div class="w3-row w3-red w3-center w3-xlarge w3-block" id="errorBox">';
		echo '<h2>File Upload:</h2>';
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
			processPhoto($target_file, $thisFileName);
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
		echo '</div>';
	}
}


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="./css/photoStyles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Photo</title>
<style>

</style>
<script src='../../js/browser.js'></script>
</head>
<body class='w3-dark-grey' onload='detectIframe()'>
<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
<?php include 'navbar.php'; ?>
</div>

<div class='w3-container w3-purple'>

	<h2>Add a new Photo</h2>
	<form action="" method="post" enctype="multipart/form-data">
		Select image to upload:
		<input class='w3-input w3-yellow w3-center w3-block w3-xxlarge' type="file" name="fileToUpload" id="fileToUpload">
		<br><br>
		<input class='w3-button w3-red w3-center w3-block w3-xxlarge' type="submit" value="Upload Image" name="submit">
		<br><br>
	</form>


</div>
<?php 

function processPhoto($src, $thisFileName){

	$newImageWidth = 490;	//ALSO HEIGHT (SQUARE)

	//GET ORIGINAL WIDTH / HEIGHT
	$imageSizeArr = getimagesize($src);
	$origWidth = $imageSizeArr[0];
	$origHeight = $imageSizeArr[1];


	//DEAL WITH PORTRAIT/LANDSCAPE
	if ($origWidth < $origHeight){

		//GET THE SCALE FACTOR (REDUCING THE HEIGHT TO 490)
		$scaleFactor = $origHeight / $newImageWidth;

		//CALCULATE THE NEW WIDTH/HEIGHT
		$scaledWidth = $origWidth / $scaleFactor;
		$scaledHeight = $origHeight / $scaleFactor;

		//CREATE A NEW IMAGE FROM THIS FILE
		$origImg = imagecreatefromstring(file_get_contents($src));
		//CREATE A NEWLY SCALED IMAGE
		$newImg = imagescale($origImg, $scaledWidth, $scaledHeight);

		//echo 'PORTRAIT IMAGE = ADD GREY SIDES';
		//ADD GREY SIDES
		$finalImg = imagecreatetruecolor(490, 490);
		$greyColour = imagecolorallocate($finalImg, 97, 97, 97);
		imagefill($finalImg, 0, 0, $greyColour);
		imagecolordeallocate($finalImg, $greyColour);

		//CALCULATE WHERE TO COPY IMAGE TO
		$midW = floor($newImageWidth / 2);
		$halfW = floor($newWidth / 2);
		$destX = $midW - $halfW;
		//COPY INTO FINAL IMAGE
		imagecopyresized($finalImg, $newImg, $destX, 0, 0, 0, $scaledWidth, $scaledHeight, $scaledWidth, $scaledHeight);
		//SAVE FINAL IMAGE TO DISK
		imagepng($finalImg, '../photo/images/' . $thisFileName);
	}else{

		//GET THE SCALE FACTOR (REDUCING THE WIDTH TO 490)
		$scaleFactor = $origWidth / $newImageWidth;
		
		//CALCULATE THE NEW WIDTH/HEIGHT
		$scaledWidth = $origWidth / $scaleFactor;
		$scaledHeight = $origHeight / $scaleFactor;

		//CREATE A NEW IMAGE FROM THIS FILE
		$origImg = imagecreatefromstring(file_get_contents($src));
		//CREATE A NEWLY SCALED IMAGE
		$newImg = imagescale($origImg, $scaledWidth, $scaledHeight);

		imagepng($newImg, '../photo/images/' . $thisFileName);
	}

}
<?php

/**
 * NOTE - THIS FUNCTIONALITY HAS BEEN EMBEDDED INTO /photo/add.php
 */

/**
 * Processes an uploaded file and carries out image transformations:
 * 		- Rescale so width = 490px, height = auto
 * 		- If width < height, add grey side bars (#616161) so the resulting image is landscape
 * 		- Stores with file name 'images/gallery_20210804_093400.png'
 */


/**
 * PROCESS FOR PORTRAIT FILES
 * STEP 1 - get the uploaded file 
 * STEP 2 - resize image so width = 490, height = auto
 * STEP 2 - create a new image, width = 490, height = resized->height
 * STEP 3 - fill new image with grey
 * STEP 4 - calculate overlay x_pos = 
 * STEP 5 - imagecopy(orig, new,)
 */

 //TEST IMAGE (hutch)
$src = "../photo/images/ORIGINAL SIZE/TEST-PORTRAIT.jpg";

$newImageWidth = 490;	//ALSO HEIGHT (SQUARE)

//GET ORIGINAL WIDTH / HEIGHT
$imageSizeArr = getimagesize($src);
$origWidth = $imageSizeArr[0];
$origHeight = $imageSizeArr[1];

//GET THE SCALE FACTOR (REDUCING THE HEIGHT TO 490)
$scaleFactor = $origHeight / $newImageWidth;
//CALCULATE THE NEW WIDTH
$newWidth = floor($origWidth / $scaleFactor);
echo 'ORIGINAL WIDTH: ' . $origWidth . '<br>';
echo 'NEW WIDTH: ' . $newWidth . '<br>';

$scaledWidth = $origWidth / $scaleFactor;
$scaledHeight = $origHeight / $scaleFactor;

//CREATE A NEW IMAGE FROM THIS FILE
$origImg = imagecreatefromstring(file_get_contents($src));
//CREATE A NEWLY SCALED IMAGE
$newImg = imagescale($origImg, $scaledWidth, $scaledHeight);

//DEAL WITH PORTRAIT/LANDSCAPE
if ($origWidth < $origHeight){

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
	imagepng($finalImg, '../photo/images/PROCESSED/TEST-PORTRAIT_REDUCED.png');
}else{
	imagepng($newImg, '../photo/images/PROCESSED/TEST-LANDSCAPE_REDUCED.png');
}


function getScaledHeightWidth($src){

	$imageSizeArr = getimagesize($src);
	$imageWidth = $imageSizeArr[0];
	$imageHeight = $imageSizeArr[1];

	$newHeight = 490;
	$scaleFactor = ($imageHeight / $newHeight);
	$newWidth =  $imageWidth / $scaleFactor;
	echo 'NEW WIDTH: ' . $newWidth . '<br>';
	echo 'NEW HEIGHT: ' . $newHeight . '<br>';
	return array($newWidth, 490);
	//GET THE SCALE FACTOR (REDUCING THE HEIGHT TO 490)
	//$scaleFactor = $imageHeight / 490;
	//CALCULATE THE NEW WIDTH
	//$newWidth = floor($imageWidth / $scaleFactor);
	//echo 'ORIGINAL WIDTH: ' . $imageWidth . '<br>';
	//echo 'NEW WIDTH: ' . $newWidth . '<br>';

}
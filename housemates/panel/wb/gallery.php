
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Whiteboard Gallery</title>
<script type="text/JavaScript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
	body {
		background-color: #808080;
	}
</style>
<script>

window.onload=function(){
	var arrCanvases = ['gallery1', 'gallery2', 'gallery3'];
	var arrBackupFiles = ["<?php 
			$backupsDir = './backups';
			$dir = scandir($backupsDir);
			foreach ($dir as $key => $value){

				if (!in_array($value,array(".",".."))){

					if (is_file($backupsDir . DIRECTORY_SEPARATOR . $value)){
				
						$result[] = $value;
					}
				}
			}
			echo implode('","',$result);
		?>"];
	for (var i = 0; i < 3; i++){
		thisCanvas = document.getElementById(arrCanvases[i]);
		thisCtx = thisCanvas.getContext('2d');
		thisBackup = arrBackupFiles[i];
		getCanvasData(thisCtx,thisBackup);
		console.log('Loading ',thisBackup,' onto canvas ',thisCanvas);
	}
}

function getCanvasData(ctx, file){
	var fullPath = './backups/' + file;
	$.get(fullPath, function( data ) {
		console.log('Loading Data');
		var img = new Image;
		img.src = data;
		img.onload = function () {
			ctx.drawImage(img,0,0,720,1280,0,0,ctx.canvas.width,ctx.canvas.height);
		};
	});
}
</script>
<style>
canvas {
	width: 100%;
	height: 600px;
}
</style>
</head>
<body class='body'>

<div class='w3-row w3-black w3-center w3-xlarge w3-block' id='navBox'>
	<a href='http://192.168.2.137:8080/housemates/home.php'><button class='w3-red'>Back to Housemates Home</button></a>
</div>

	<!-- KEEP PAGE CONTENT WITHIN THIS WRAPPER (SCROLL ISSUE) -->
	<div class='wrapper'>
		<div class="w3-row-padding">
			<div class="w3-col s4">
				<h1>Backup 1</h1>
				<canvas id="gallery1">
					Sorry, your browser does not support HTML5 canvas technology.
				</canvas>
			</div>
			<div class="w3-col s4">
				<h1>Backup 2</h1>
				<canvas id="gallery2">
					Sorry, your browser does not support HTML5 canvas technology.
				</canvas>
			</div>
			<div class="w3-col s4">
				<h1>Backup 3</h1>
				<canvas id="gallery3">
					Sorry, your browser does not support HTML5 canvas technology.
				</canvas>
			</div>
		</div>
	</div>
</body>
</html>

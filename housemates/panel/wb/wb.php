
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Housemates Whiteboard</title>
<script type="text/JavaScript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
<script src='../../js/browser.js'></script>

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
	body {
		background-color: #808080;
	}
</style>
<script>
// Global context object
var ctx;
// Desired width and height 
var aimWidth = 720;
var aimHeight = 1280;

/* Thanks to https://codepen.io/michaelsboost/pen/cnCAL for the idea */
window.onload = function() {

	//HIDE TOOLBAR IN IFRAME
	var isIframe = detectIframe();
	if (isIframe === false){
		document.getElementById('wbToolbarDiv').style.display = 'block';
	}

	$('body').bind('touchmove', function(e){e.preventDefault()})

	var myCanvas = document.getElementById("myCanvas");
	ctx = myCanvas.getContext("2d");

	var scale = {};
	scale.width = window.innerWidth / aimWidth;
	scale.height = window.innerHeight / aimHeight;

	if (window.innerWidth < aimWidth){
		//SCALE CANVAS BY WIDTH
		scale.width = aimWidth / window.innerWidth;
		//SCALE.WIDTH ALWAYS < 1
		scale.height = aimHeight * scale.width;
		myCanvas.width = window.innerWidth;
		myCanvas.height = Math.min(aimHeight, window.innerHeight * scale.height);
	}else if (window.innerHeight < aimHeight){
		//SCALE CANVAS BY HEIGHT
		scale.height = aimHeight / window.innerHeight;
		scale.width = aimWidth * scale.height;
		myCanvas.height = window.innerHeight;
		myCanvas.width = Math.min(aimWidth, window.innerWidth * scale.width);
	}
	
    // Fill Window Width and Height
    //myCanvas.width = window.innerWidth;
	//myCanvas.height = window.innerHeight;
	
	// Set Background Color
    ctx.fillStyle="#fff";
    ctx.fillRect(0,0,myCanvas.width,myCanvas.height);

	ctx.strokeStyle = "#000";
	ctx.lineWidth = 5;
	
    // Mouse Event Handlers
	if(myCanvas){
		var isDown = false;
		var canvasX, canvasY;
		//ctx.lineWidth = 5;
		getCanvasData('whiteboard', ctx);
		
		$(myCanvas)
		.mousedown(function(e){
			isDown = true;
			ctx.beginPath();
			canvasX = e.pageX - myCanvas.offsetLeft;
			canvasY = e.pageY - myCanvas.offsetTop;
			ctx.moveTo(canvasX, canvasY);
		})
		.mousemove(function(e){
			if(isDown !== false) {
				canvasX = e.pageX - myCanvas.offsetLeft;
				canvasY = e.pageY - myCanvas.offsetTop;
				ctx.lineTo(canvasX, canvasY);
				//ctx.strokeStyle = "#000";
				ctx.stroke();
			}
		})
		.mouseup(function(e){
			isDown = false;
			ctx.closePath();
		});
	}
	
	// Touch Events Handlers
	draw = {
		started: false,
		start: function(evt) {

			ctx.beginPath();
			ctx.moveTo(
				evt.touches[0].pageX,
				evt.touches[0].pageY
			);

			this.started = true;

		},
		move: function(evt) {

			if (this.started) {
				ctx.lineTo(
					evt.touches[0].pageX,
					evt.touches[0].pageY
				);

				//ctx.strokeStyle = "#000";
				//ctx.lineWidth = 5;
				ctx.stroke();
			}

		},
		end: function(evt) {
			this.started = false;
			saveCanvasData(myCanvas, 'whiteboard');
		}
	};
	
	// Touch Events
	myCanvas.addEventListener('touchstart', draw.start, false);
	myCanvas.addEventListener('touchend', draw.end, false);
	myCanvas.addEventListener('touchmove', draw.move, false);
	
	// Disable Page Move
	document.body.addEventListener('touchmove',function(evt){
		evt.preventDefault();
	},false);

	//
	window.addEventListener('online', backOnline);
	window.addEventListener('offline', droppedConnection);

	var t = setInterval(500, getCanvasData(ctx, draw, scale));
	
};

function backOnline(event) {
 alert('You are back online!');
}

function droppedConnection(event){
	alert('Your wifi has dropped and your changes may not be saved!');
}

function changePenColour(colour){
	//ctx = document.getElementById('myCanvas').getContext('2d');
	ctx.strokeStyle = colour;
}

function changePenThickness(thickness){
	ctx.lineWidth = thickness;
}


function getCanvasData(ctx, draw, scale){
	if (draw.started === true) return;
	$.get( "whiteboardData.txt", function( data ) {
		console.log('Loading Data');
		var img = new Image;
		img.src = data;
		img.onload = function () {
			//ctx.drawImage(img, 0, 0);
			/*if (window.innerWidth > window.innerHeight){
				var maxWidth = Math.min(720,window.innerWidth);
				var maxHeight = window.innerHeight;
			}else{
				var maxHeight = Math.min(1280,window.innerHeight);
				var maxWidth = window.innerWidth;
			}*/
			ctx.drawImage(img,0,0,720,1280,0,0,ctx.canvas.width,ctx.canvas.height);
		};
	});
}

function saveCanvasData(canvas, canvasName){
	console.log('Saving ' + canvasName);
	data = {
		'img': canvas.toDataURL()
	};
	$.ajax({
		type: "POST",
		url: 'saveWhiteboard.php',
		data: data
	});
    //localStorage.setItem(canvasName, canvas.toDataURL());
}

function backupCanvasData(canvas, canvasName){
	//console.log('Backing up ' + canvasName);
	data = {
		'img': canvas.toDataURL()
	};
	console.log(data);
	$.ajax({
		type: "POST",
		url: 'backupWhiteboard.php',
		data: data
	});
}

function clearWhiteboard(){
	var myCanvas = document.getElementById("myCanvas");
	var ctx = myCanvas.getContext("2d");
	backupCanvasData(myCanvas, 'whiteboard');
	ctx.clearRect(0,0,myCanvas.width,myCanvas.height);
	saveCanvasData(myCanvas, 'whiteboard');
}

</script>
<style>

/* 
	STYLES USED AS PART OF A HACK TO 
	PREVENT PULL-DOWN SCROLLING ON ANDROID CHROME
	SEE: https://w3bits.com/prevent-chrome-pull-to-refresh-css/
*/
.body,
.wrapper {
  /* Break the flow */
  position: absolute;
  top: 0px;

  /* Give them all the available space */
  width: 100%;
  height: 100%;

  /* Remove the margins if any */
  margin: 0;

  /* Allow them to scroll down the document */
  overflow-y: hidden;
}

.body {
  /* Sending body at the bottom of the stack */
  z-index: 1;
}

.wrapper {
  /* Making the wrapper stack above the body */
  z-index: 2;
}

#myCanvas {
	cursor: crosshair;
    /*position: fixed;*/
}
#wbToolbarDiv {
	border: 1px solid black;
	background-color: grey;
	position: absolute;
	bottom: 0;
	left: 0;
	z-index: 50;
	width: 100%;
	height: 50px;
	padding: 5px 0;
	display:none;
}
</style>
</head>
<body class='body'>
	<!-- KEEP PAGE CONTENT WITHIN THIS WRAPPER (SCROLL ISSUE) -->
	<div class='wrapper'>
		<canvas id="myCanvas">
			Sorry, your browser does not support HTML5 canvas technology.
		</canvas>
		<div id='wbToolbarDiv' class='w3-row-padding'>
			
			<div class='w3-col s4 w3-center'>
				<!--label for='penColourSelect'>Colour: </label-->
				<select class="w3-select" id='penColourSelect' onchange='changePenColour(this.value)'>
					<option selected value='#000'><span class="w3-text-black">Black Pen</span></option>
					<option value='#0f0'><span class="w3-text-green">Green Pen</span></option>
					<option value='#00f'><span class="w3-text-blue">Blue Pen</span></option>
					<option value='#f00'><span class="w3-text-red">Red Pen</span></option>
					<option value='#fff'><span class="w3-text-black">Eraser</span></option>
				</select>
			</div>
			<div class='w3-col s4 w3-center'>
				<!--label for='penThicknessSelect'>Thickness: </label-->
				<select class="w3-select" id='penThicknessSelect' onchange='changePenThickness(this.value)'>
					<option value='1'><span class="w3-text-black">Thin</span></option>
					<option selected value='5'><span class="w3-text-black">Medium (default)</span></option>
					<option value='10'><span class="w3-text-black">Thick</span></option>
					<option value='15'><span class="w3-text-black">Chonk</span></option>
				</select>
			</div>
			<div class='w3-col s4 w3-center'>
				<button onclick='clearWhiteboard()' class='w3-button w3-block w3-red'>Clear Whiteboard</button>
			</div>
		</div>
	</div>
</body>
</html>

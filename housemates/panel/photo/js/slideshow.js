var slideIndex = 1;
var changeDelay = 2000;
//window.addEventListener('resize',resizeImages);
//resizeImages();
showSlides(slideIndex);
setInterval(plusSlides, changeDelay, 1);
//rotateImages();

function plusSlides(n) {
	showSlides(slideIndex += n);
}

function currentSlide(n) {
	showSlides(slideIndex = n);
}

function showSlides(n) {
	var i;
	var slides = document.getElementsByClassName("mySlides");
	if (n > slides.length) {slideIndex = 1}
	if (n < 1) {slideIndex = slides.length}
	for (i = 0; i < slides.length; i++) {
		slides[i].style.display = "none";
	}
	//slides[slideIndex-1].style.display = "block";
	slides[slideIndex-1].style.display = "flex";
}

function resizeImages(){
	var imgs = document.getElementsByTagName('img');
	var scaleFactor = 1;
	for (var i = 0; i < imgs.length; i++){
		if (imgs[i].width >= window.innerWidth){
			let origWidth = imgs[i].width;
			imgs[i].width = window.innerWidth;
			scaleFactor = window.innerWidth / origWidth;
			imgs[i].height *= scaleFactor;
			console.log('Resized image',i,'Orig Width:',origWidth,'Scale Factor:',scaleFactor);
		}
		if (imgs[i].height >= window.innerHeight){
			let origHeight = imgs[i].height;
			imgs[i].height = window.innerHeight;
			scaleFactor = window.innerHeight / origHeight;
			imgs[i].width *= scaleFactor;
			console.log('Resized image',i,'Orig Width:',origHeight,'Scale Factor:',scaleFactor);
		}
	}
}

/**
 * Go through all images and rotate them if the naturalWidth is less than the naturalHeight
 * NOT WORKING ON TV DISPLAY
 */
function rotateImages(){
	var imgs = document.getElementsByTagName('img');
	for (var i = 0; i < imgs.length; i++){
		let img = document.createElement("img");
		img.src = imgs[i].src;
		img.onload = function(){
			let id = img.src.replace('http://192.168.2.137:8080/housemates/panel/photo/images/','');
			//console.log(id, img.naturalWidth, img.naturalHeight);
			if (img.naturalWidth < img.naturalHeight){
				//console.log('Rotating ' + id + ' by -90');
				document.getElementById(id).style.transform = 'rotate(-90deg)';
			}
		}
		
	}
}

//https://stackoverflow.com/a/7808790
function alertSize() {
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
	window.alert( 'Width = ' + myWidth );
	window.alert( 'Height = ' + myHeight );
}
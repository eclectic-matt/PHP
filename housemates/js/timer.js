var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var daySuffixes = ["st", "nd", "rd", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th", "th", "st"];
var screenRefresh = 60;
//updateDateTime();
var t = setInterval(updateDateTime, 1000);

function updateDateTime(){
	var d = new Date();
	var day = days[d.getDay()];
	var hr = d.getHours();
	var min = d.getMinutes();
	if (min < 10) {
		min = "0" + min;
	}
	var ampm = "am";
	if( hr > 12 ) {
		hr -= 12;
		ampm = "pm";
	}else if (hr === 12){
		ampm = "pm";
	}
	var date = d.getDate();
	var month = months[d.getMonth()];
	var year = d.getFullYear();
	var x = document.getElementById("currentDateTimeDiv");
	var daySuffix = daySuffixes[date - 1];
	var strTime = day + " " + date + daySuffix + " " + month + " " + year + " @ " + hr + ":" + min + ampm;
	x.innerHTML = strTime;// + "<p style='font-style: italic;' class='w3-center w3-small'>Page will refresh in " + screenRefresh + " seconds</p>";
	//screenRefresh--;    
}
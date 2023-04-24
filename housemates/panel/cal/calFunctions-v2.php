<?php 

require './classes/Calendar.class';
require './classes/Event.class';



function generateCalendar(){

	$thisEvent = new Event();
	$thisEvent->title = '';

	//OPEN HANDLE TO ICAL WEB LINK
	$handle = fopen(Calendar::$calPath, "r");

	//IF HANDLE IS ESTABLISHED
	if ($handle) {

		//WHILE THERE IS STILL A LINE TO READ
		while (($line = fgets($handle)) !== false) {

			//HACKY BOLLOCKS
			switch (true){

				//BEGIN EVENT
				case strpos($line, 'BEGIN:VEVENT') !== false:
					$thisEvent = new Event();
					$thisEvent->title = '';
				break;

				//DT START
				case strpos($line, 'DTSTART')  !== false:
					//PASS TO STATIC FUNCTION WITH THE LINE AND WHETHER START OR END
					$thisEvent->start = Event::processDateString($line, 'DTSTART');	
				break;

				//DT END
				case strpos($line, 'DTEND')  !== false:
					$thisEvent->end = Event::processDateString($line, 'DTEND');
				break;

				//SUMMARY
				case strpos($line, 'SUMMARY')  !== false:
					$thisEvent->title = str_ireplace('SUMMARY:','',$line);
				break;

				//LOCATION
				case strpos($line, 'LOCATION')  !== false:
					$thisEvent->location = trim(str_ireplace('LOCATION:','',$line));
				break;

				//END EVENT
				case strpos($line, 'END:VEVENT') !== false:
					//QUICK CHECK IN CASE EVENT NOT PARSED CORRECTLY
					if ($thisEvent->title !== ''){
						//$eventStart->timestamp;
						Calendar::storeEvent($thisEvent);
					}
				break;
			}//END SWITCH
		}//END WHILE $line = fgets($handle) !== false

		//CLOSE THE HANDLE ONCE COMPLETE
		fclose($handle);

	} else {

		// error opening the file.
		echo "<h2>There was an error loading the calendar.</h2>";
		echo "<p>Check the settings and try again!</p>";
	}
}


function outputCalendar(){
	global $events;
	$count = 1;
	$today = date('Y-m-d');
	foreach ($events as $event){
		if ($count > EVENT_DISPLAY_LIMIT) return;
		if ( ($event['startYMD'] < $today) && (HIDE_PAST_EVENTS_CAL)) continue;
		//echo generateEventBox($event);
		echo generateSplitEventBox($event, $count);
		$count++;
	}
}


function outputWeekToView(){

	global $events;
	
	if (date('l') === 'Monday'){
		$thisMonday = new DateTime(date('Y-m-d', strtotime('today')));
	}else{
		$thisMonday = new DateTime(date('Y-m-d', strtotime('last monday')));
	}

	echo '<table>';
	echo '<tr class="w3-dark-grey">';
	echo '<th style="width: 25%">Day</th>';
	echo '<th style="width: 75%">Events</th>';
	echo '</tr>';
	
	for ($count = 0; $count < 7; $count++){
		echo '<tr>';
		$classColour = COLOUR_WTV_OTHER;
		if (date('Y-m-d') === $thisMonday->format('Y-m-d')){
			$classColour = COLOUR_WTV_TODAY;
		}
		echo '<td class="' . $classColour . '">';   
		echo $thisMonday->format('D jS');
		echo '</td>';
		$eventStr = '';
		foreach($events as $event){
			//EXCLUDE PAST EVENTS
			if (
				( 
					($event['startYMD'] < $thisMonday->format('Y-m-d')) 
					&& ($event['endYMD'] >= $thisMonday->format('Y-m-d'))
				) 
				&& (HIDE_PAST_EVENTS_WTV)
			) continue;
			if ( ($event['startYMD'] <= $thisMonday->format('Y-m-d')) && ($event['endYMD'] >= $thisMonday->format('Y-m-d'))){
				$eventStr === '' ? $eventStr = trim($event['title']) : $eventStr .= ', ' . trim($event['title']);
			}
		}
		echo '<td class="' . $classColour . '">';
		echo $eventStr;
		echo '</td>';
		echo '</tr>';
		$thisMonday->add(new DateInterval('P1D'));
	}
	echo '</table>';
	
}

function generateEventBox($event){
	$eventBoxStr = '';
	$eventBoxStr .= '<div class="eventBox ' . $event['color'] . '">';
	$eventBoxStr .= '<h3>' . $event['title'];
	/*if ($event['location'] !== ''){
		$eventBoxStr .= ' at ' . $event['location'] . '</h3>'; 
	}else{
		$eventBoxStr .= '</h3>';
	}*/
	$eventBoxStr .= '</h3>';
	$eventBoxStr .= '<em><b>FROM:</b> ' . $event['start'] . '</em><br>';
	$eventBoxStr .= '<em><b>TO:</b> ' . $event['end'] . '</em>';
	$eventBoxStr .= '</div>';
	return $eventBoxStr;
}



function generateSplitEventBox($event, $eventId){
	$eventBoxStr = '';
	$eventBoxStr .= '<div id="event' . $eventId . '" class="eventBox ' . $event['color'] . ' w3-row">';
		$eventBoxStr .= '<div class="w3-col s9">';
			$eventBoxStr .= '<h3>' . $event['title'];
			$eventBoxStr .= '</h3>';
		$eventBoxStr .= '</div>';
	
		$eventBoxStr .= '<div class="w3-col s3">';
			$eventBoxStr .= '<em><b></b> ' . date('d-m-Y', strtotime($event['startYMD'])) . '</em><br>';
			//$eventBoxStr .= '<em><b>TO:</b> ' . $event['end'] . '</em>';
		$eventBoxStr .= '</div>';
	$eventBoxStr .= '</div>';
	return $eventBoxStr;
}


function storeEvent($newEvent){
	global $events;
	if (count($events) === 0){
		$events[0] = $newEvent;
		return;
	}
	$index = 0;
	foreach($events as $event){
		if (strcmp($event['timestamp'], $newEvent['timestamp']) >= 0){
			array_splice( $events, $index, 0, array($newEvent));
			break;
		}
		$index++;
		if ($index === count($events)){
			array_push($events, $newEvent);
		}
	}

}


/**
 * Generate a formatted date/time string based on an event object
 * @param object $event The event to format a DT string for
 * 
 * @return string The formatted DT string
 */
function generateEventString($event){
	$formatStr = 'l jS \of F Y';
	if ( ($event->hour !== 0) && ($event->min !== 0) ){
		$formatStr = $formatStr . ' h:i A';
	}else if ($event->hour !== 0){
		$formatStr =  $formatStr . ' h A';
	}
	$eventStr = date($formatStr, mktime($event->hour, $event->min, 0, $event->month, $event->day,  $event->year));
	return $eventStr;
}

function processShortDate($timeStr){
	$timeStr = strftime('%Y%m%d', strtotime($timeStr));
	$event = new stdClass();
	$event->year = substr($timeStr,0,4);
	$event->month = substr($timeStr,4,2);
	$event->day = substr($timeStr,6,2);
	$event->hour = 0;
	$event->min = 0;
	$event->timestamp = $timeStr;
	return $event;
}

function processLongDate($timeStr){
	$timeStr = strftime('%Y%m%dT%H%M%S00Z', strtotime($timeStr));
	$event = new stdClass();
	$event->year = substr($timeStr,0,4);
	$event->month = substr($timeStr,4,2);
	$event->day = substr($timeStr,6,2);
	$event->hour = substr($timeStr,9,2);
	$event->min = substr($timeStr,11,2);
	$event->timestamp = $timeStr;
	return $event;
}
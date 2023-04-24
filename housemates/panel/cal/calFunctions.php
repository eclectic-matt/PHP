<?php 

function generateCalendar(){
			
	global $events, $calPath;
	//SET EVENT VALUES
	$eventStart = '';
	$eventEnd = '';
	$eventTitle = '';
	$eventLocation = '';

	//$thisEvent = new Event();


	//OPEN HANDLE TO ICAL WEB LINK
	$handle = fopen($calPath, "r");

	//IF HANDLE IS ESTABLISHED
	if ($handle) {

		//WHILE THERE IS STILL A LINE TO READ
		while (($line = fgets($handle)) !== false) {

			// if begin:vevent found
			if (strpos($line, 'END:VEVENT') !== false){

				//check if eventTitle currently blank
				if ($eventTitle !== ''){

					//check if event is today
					$eventStartDay = date('Y-m-d',mktime(0,0,0,$eventStart->month,$eventStart->day,$eventStart->year));
					$eventEndDay = date('Y-m-d',mktime(0,0,0,$eventEnd->month,$eventEnd->day,$eventEnd->year));
					$today = date('Y-m-d');
					//HIDE EVENTS BEFORE TODAY?
					//if (($eventStartDay < $today) && (HIDE_PAST_EVENTS)) continue;
					//COLOUR BASED ON DATE
					if ($eventStartDay === $today){
						$classColor = COLOUR_TODAY;
					}else if ($eventStartDay < $today){
						$classColor = COLOUR_PAST;
					}else{
						$classColor = COLOUR_FUTURE;
					}

					$newEvent = array(
						'title' => $eventTitle, 
						'location' => $eventLocation, 
						'color' => $classColor, 
						'start' => $eventStartStr, 
						'end' => $eventEndStr, 
						'timestamp' => $eventTimestamp,
						'startYMD' => $eventStartDay,
						'endYMD' => $eventEndDay
					);
					storeEvent($newEvent);

					//CLEAR TITLE (EXPECTS A NEW EVENT) AND LOCATION (MAY NOT GET ADDED BY NEXT EVENT)
					$eventTitle = $eventLocation = '';
				}

			//IF DTSTART FOUND
			}else if (strpos($line, 'DTSTART')  !== false){

				$eventStart = new stdClass();

				if (strpos($line, ';VALUE=DATE:') !== false){

					//FORMAT: DTSTART;VALUE=DATE:20210630
					$startStr = str_ireplace('DTSTART;VALUE=DATE:','',$line);
					$eventStart = processShortDate($startStr);

				}else if (strpos($line, ';TZID=Europe/London:') !== false){

					//DTSTART;TZID=Europe/London:20210615T140000
					$startStr = str_ireplace('DTSTART;TZID=Europe/London:','',$line);
					$eventStart = processLongDate($startStr);

				}else{

					//FORMAT: DTSTART:20210617T180000Z
					$startStr = str_ireplace('DTSTART:','',$line);
					$eventStart = processLongDate($startStr);

				}

				$eventStartStr = generateEventString($eventStart);
				$eventTimestamp = $eventStart->timestamp;
				
			//IF DTEND FOUND
			}else if (strpos($line, 'DTEND')  !== false){

				$eventEnd = new stdClass();

				if (strpos($line, ';VALUE=DATE:') !== false){

					//FORMAT: DTEND;VALUE=DATE:20210701
					$endStr = str_ireplace('DTEND;VALUE=DATE:','',$line);
					$eventEnd = processShortDate($endStr);

				}else if (strpos($line, ';TZID=Europe/London:') !== false){

					//DTEND;TZID=Europe/London:20210615T170000
					$endStr = str_ireplace('DTEND;TZID=Europe/London:','',$line);
					$eventEnd = processLongDate($endStr);

				}else{

					//FORMAT: DTEND:20210617T220000Z
					$endStr = str_ireplace('DTEND:','',$line);
					$eventEnd = processLongDate($endStr);

				}
				
				$eventEndStr = generateEventString($eventEnd);

			
			//IF SUMMARY FOUND
			}else if (strpos($line, 'SUMMARY') !== false){

				$eventTitle = str_ireplace('SUMMARY:','',$line);

			//IF LOCATION FOUND
			}else if (strpos($line, 'LOCATION') !== false){
				
				//SET LOCATION VARIABLE
				$eventLocation = trim(str_ireplace('LOCATION:','',$line));
			}
		}

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
	//echo '<tr class="w3-dark-grey">';
	//echo '<th style="width: 25%">Day</th>';
	//echo '<th style="width: 75%">Events</th>';
	//echo '</tr>';
	
	for ($count = 0; $count < 7; $count++){
		echo '<tr>';
		$classColour = COLOUR_WTV_OTHER;
		if (date('Y-m-d') === $thisMonday->format('Y-m-d')){
			$classColour = COLOUR_WTV_TODAY;
		}
		echo '<td style="width:15%" class="' . $classColour . '">';   
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
		echo '<td style="width:85%" class="' . $classColour . '">';
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
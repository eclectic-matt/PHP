<?php

//#-#-#-#-#-#
// FOR LOOPS 
//#-#-#-#-#-#

//ASCENDING INDEX
for ($i = 0; $i < $limit; $i++){
	echo $i;
}

//DESCENDING INDEX
for ($i = $limit; $i > 0; $i--){
	echo $i;
}

//ASCENDING RANGE
foreach (range(0, $limit) as $number) {
	echo $number;
}

//DESCENDING RANGE (-ve step param)
foreach (range($limit, 0, -1) as $number) {
	echo $number;
}

//LOOP THROUGH CHARACTER ARRAY
foreach(range('a','b','c') as $character){
	echo $character;
}




?>
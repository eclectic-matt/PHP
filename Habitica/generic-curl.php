<?php

$ch = curl_init();

$apiURL = "https://habitica.com/api/v3/tasks/user"; //Can edit but settings must be changed

curl_setopt($ch, CURLOPT_HTTPGET, true);
	
// ADD ADDITIONAL CURL HEADERS
curl_setopt($ch, CURLOPT_URL, $apiURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$headKey = "x-api-key: " . $GLOBALS['userKey'];
$headUser = "x-api-user: " . $GLOBALS['userId'];

$headers = [
  $headUser,
  $headKey,
  'Content-Type: application/json'	
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$server_output = curl_exec ($ch);
curl_close ($ch);
	
// Convert to an array for processing
$json_arr = json_decode($server_output,true);

return $json_arr;

?>

<?php

/*
 * FUNCTIONS TO LEARN
 *
*/

// -- EXTRACT
// TURN ARRAY INTO VARIABLES
$array = [
  'name' => 'John Smeg',
  'message' => 'You are a smeg head'
];
extract($array);
echo 'Hi, ' . $name . '! ' . $message;


// -- COMPACT
// TURN VARIABLES INTO ARRAY
$name = 'John Thomas';
$email = 'john@example.com';
compact('name', 'email');


// -- VARIABLE VARIABLES
$class_name = 'perform';
$$class_name = 'Smeg';
// Now $perform === 'Smeg'


// -- EMAIL VALIDATION (DOMAIN AT LEAST)
// -- ALSO "LIST" FUNCTION
//https://www.php.net/manual/en/function.checkdnsrr.php
$email = 'smeg@head.com';
list($user, $domain) = explode('@', $email);
if (checkdnsrr($domain) === FALSE){
  // DOMAIN FAILED VALIDATION
}


// -- 

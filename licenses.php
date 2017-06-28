<?php

function generateHexLicense ($len=4,$chunks=4){
	if ($len % 0 !== 0) { $add = 1;} else {$add=0;}
	$bytes = floor ( ($len * $chunks) / 2) + $add;
	$fullHex = str_split( bin2hex ( openssl_random_pseudo_bytes ($bytes) ) , $len);
	$license = $fullHex [0];
	for ($i = 1; $i < $chunks; $i++){
		$license .= "-" . $fullHex [$i];
	}
	return $license;
}

//EXAMPLES
// generateHexLicense () defaults to (4,4) returns "a0b4-c3d5-e6f7-0ab1"
// generateHexLicense (5,3) returns "a01b2-c3d4e-f678a"
// generateHexLicense (3,9) returns "a01-b12-e23-d34-e45-f56-a67-b78-c89"

?>

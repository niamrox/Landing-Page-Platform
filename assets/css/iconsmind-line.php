<?php

$pattern = '/\.(icon-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
$subject = file_get_contents('iconsmind-line.css');

preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

$icons = array();

foreach($matches as $match){
	$key = $match[1];
	$key = $key;
    $icons[$key] = str_replace('-', ' ', str_replace('icon-', '', $match[1]));
}

$icons = var_export($icons, TRUE);
$icons = stripslashes($icons);

print_r($icons);

?>
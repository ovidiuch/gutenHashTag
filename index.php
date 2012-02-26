<?php

include('city.php');

// Init

date_default_timezone_set('Europe/London');

// Cities

$cities = City::get('cities.txt');

// Img

$img = imagecreatetruecolor(800, count($cities) * 72 + 15);

// Background

$bg = imagecolorallocate($img, 11, 11, 11);

imagefill($img, 0, 0, $bg);

// Font

$bold_font = 'OpenSans-Bold.ttf';
$light_font = 'OpenSans-Light.ttf';

$m_size = 15;
$c_size = 22;

// Text

$i = 0;

foreach($cities as $city)
{
	$light = $city->get_light();

	$w = 75 + (180 * $light);
	$g = 75 + (150 * $light);

	$white = imagecolorallocate($img, $w, $w, $w);
	$gray = imagecolorallocate($img, $g, $g, $g);
	$dark = imagecolorallocate($img, 75, 75, 75);

	$offset = $i++ * 72;

	imagettftext($img, $m_size, 0, 16, 30 + $offset, $gray, "fonts/{$city->font}", $city->get_message());
	imagettftext($img, $c_size, 0, 15, 62 + $offset, $dark, "fonts/$light_font", '#');
	imagettftext($img, $c_size, 0, 35, 62 + $offset, $white, "fonts/$bold_font", $city->name);
}

// Header

header('Content-Type: image/jpeg');

// Print

imagejpeg($img, null, 100);

// Destroy

imagedestroy($img);
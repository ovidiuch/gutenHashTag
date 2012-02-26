<?php

include('city.php');

// Init

date_default_timezone_set('Europe/London');

// Cities

$cities = City::get('cities.txt');

// Img

$img = imagecreatetruecolor(320, ceil(count($cities) / 2) * 46 + 6);

// Background

$bg = imagecolorallocate($img, 10, 10, 10);

imagefill($img, 0, 0, $bg);

// Font

$bold_font = 'OpenSans-Bold.ttf';
$light_font = 'OpenSans-Light.ttf';

$m_size = 10;
$c_size = 14;

// Text

$i = 0;
$x = 0;
$y = 0;

foreach($cities as $city)
{
	$light = $city->get_light();

	$w = 75 + (180 * $light);
	$g = 75 + (150 * $light);

	$white = imagecolorallocate($img, $w, $w, $w);
	$gray = imagecolorallocate($img, $g, $g, $g);
	$dark = imagecolorallocate($img, 75, 75, 75);

	if($i && $i % 2 == 0)
	{
		$x = 0;
		$y += 46;
	}
	imagettftext($img, $m_size, 0, 10 + $x, 20 + $y, $gray, "fonts/{$city->font}", $city->get_message());
	imagettftext($img, $c_size, 0, 10 + $x, 40 + $y, $dark, "fonts/$light_font", '#');
	imagettftext($img, $c_size, 0, 22 + $x, 40 + $y, $white, "fonts/$bold_font", $city->name);

	$i++;
	$x += 140;
}

// Header

header('Content-Type: image/jpeg');

// Print

imagejpeg($img, null, 100);

// Destroy

imagedestroy($img);
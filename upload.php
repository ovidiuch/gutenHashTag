<?php

// Lib

require('lib/tmhOAuth/tmhOAuth.php');
require('lib/tmhOAuth/tmhUtilities.php');

// Auth

$tmhOAuth = new tmhOAuth(array
(
	'consumer_key' => 'xxxxxx',
	'consumer_secret' => 'xxxxxx',
	'user_token' => 'xxxxxx',
	'user_secret' => 'xxxxxx',
));

// Path

$pathinfo = pathinfo
(
	$path = 'exports/' . time() . '.jpg'
);

// Generate

$url = 'http://localhost/gutenHashTag/generate.php';

if(!file_get_contents("$url?path=$path") || !$path = realpath($path))
{
	exit;
}

// Params

$params = array
(
	'image' => "@$path;type=image/jpeg;filename={$pathinfo['basename']}",
	'use' => 'true'
);

// Request

$code = $tmhOAuth->request('POST', $tmhOAuth->url
(
	'1/account/update_profile_background_image'
),
$params, true, true);

// Response

echo $code;
<?php

// Lib

include('lib/tmhOAuth/tmhOAuth.php');
include('lib/tmhOAuth/tmhUtilities.php');

include('class/city.php');
include('class/map.php');

// Config

include('config.php');

// Auth

$tmhOAuth = new tmhOAuth(array
(
	'consumer_key' => $consumer_key,
	'consumer_secret' => $consumer_secret,
	'user_token' => $user_token,
	'user_secret' => $user_secret,
));

// Path

if(!is_dir('exports') && !mkdir('exports'))
{
	exit;
}
$pathinfo = pathinfo
(
	$path = 'exports/' . time() . '.jpg'
);
$name = $pathinfo['basename'];

// Map

$map = new Map;

if(!$map->save($path) || !$path = realpath($path))
{
	exit;
}

// Params

$image = array
(
	"@$path",
	'type=image/jpeg',
	"filename=$name"
);
$params = array
(
	'image' => implode(';', $image),
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
<?php

include('map.php');

// Lib

include('lib/tmhOAuth/tmhOAuth.php');
include('lib/tmhOAuth/tmhUtilities.php');

// Auth

$tmhOAuth = new tmhOAuth(array
(
	'consumer_key' => 'xxxxxx',
	'consumer_secret' => 'xxxxxx',
	'user_token' => 'xxxxxx',
	'user_secret' => 'xxxxxx',
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

// Generate

$map = new Map();

if(!$map->save($path) || !$path = realpath($path))
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
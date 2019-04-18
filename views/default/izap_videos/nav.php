<?php

$tabs = [
	'all' => [
		'text' => elgg_echo('all'),
		'href' => "videos/all",
		'selected' => $vars['selected'] == 'all',
		'priority' => 200,
	],
	'mine' => [
		'text' => elgg_echo('mine'),
		'href' => "videos/owner",
		'selected' => $vars['selected'] == 'mine',
		'priority' => 300,
	],
	'friend' => [
		'text' => elgg_echo('friends'),
		'href' => "videos/friends",
		'selected' => $vars['selected'] == 'friends',
		'priority' => 400,
	],
	'favorites' => [
		'text' => elgg_echo('izap_videos:favorites_short'),
		'href' => "videos/favorites",
		'selected' => $vars['selected'] == 'favorites',
		'priority' => 500,
	],
];

echo elgg_view('navigation/tabs', ['tabs' => $tabs]);

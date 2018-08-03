<?php

$tabs = [
	'all' => [
		'title' => elgg_echo('all'),
		'url' => "videos/all",
		'selected' => $vars['selected'] == 'all',
		'priority' => 200,
	],
	'mine' => [
		'title' => elgg_echo('mine'),
		'url' => "videos/owner",
		'selected' => $vars['selected'] == 'mine',
		'priority' => 300,
	],
	'friend' => [
		'title' => elgg_echo('friends'),
		'url' => "videos/friends",
		'selected' => $vars['selected'] == 'friends',
		'priority' => 400,
	],
	'favorites' => [
		'title' => elgg_echo('izap_videos:favorites_short'),
		'url' => "videos/favorites",
		'selected' => $vars['selected'] == 'favorites',
		'priority' => 500,
	],
];

echo elgg_view('navigation/tabs', ['tabs' => $tabs]);

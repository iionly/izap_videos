<?php

$tabs = array(
	'all' => array(
		'title' => elgg_echo('all'),
		'url' => "videos/all",
		'selected' => $vars['selected'] == 'all',
		'priority' => 200,
	),
	'mine' => array(
		'title' => elgg_echo('mine'),
		'url' => "videos/owner",
		'selected' => $vars['selected'] == 'mine',
		'priority' => 300,
	),
	'friend' => array(
		'title' => elgg_echo('friends'),
		'url' => "videos/friends",
		'selected' => $vars['selected'] == 'friends',
		'priority' => 400,
	),
	'favorites' => array(
		'title' => elgg_echo('izap_videos:favorites_short'),
		'url' => "videos/favorites",
		'selected' => $vars['selected'] == 'favorites',
		'priority' => 500,
	),
);

echo elgg_view('navigation/tabs', array('tabs' => $tabs));

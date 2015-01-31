<?php

$base = elgg_get_site_url() . 'videos/';

elgg_register_menu_item('page', array(
	'name' => 'A10_izap_videos_albums',
	'text' => elgg_echo('izap_videos:all_videos'),
	'href' => $base . 'all',
	'section' => 'A'
));

$page = elgg_extract('page', $vars);
switch ($page) {
	case 'all':
		echo elgg_view('izap_videos/sidebar/extended_menu', $vars);
		echo elgg_view('page/elements/comments_block', array(
			'subtypes' => 'izap_videos',
		));
		echo elgg_view('page/elements/tagcloud_block', array(
			'subtypes' => 'izap_videos',
		));
		break;
	case 'owner':
		echo elgg_view('izap_videos/sidebar/extended_menu', $vars);
		echo elgg_view('page/elements/comments_block', array(
			'subtypes' => 'izap_videos',
			'owner_guid' => elgg_get_page_owner_guid(),
		));
		echo elgg_view('page/elements/tagcloud_block', array(
			'subtypes' => 'izap_videos',
			'owner_guid' => elgg_get_page_owner_guid(),
		));
		break;
	case 'friends':
	case 'favorites':
		echo elgg_view('izap_videos/sidebar/extended_menu', $vars);
		break;
}

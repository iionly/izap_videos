<?php

$base = elgg_get_site_url() . 'videos/';

if (\IzapFunctions::izapAdminSettings_izap_videos('izapExtendedSidebarMenu') != 'NO') {
	elgg_register_menu_item('page', [
		'name' => 'A10_izap_videos_albums',
		'text' => elgg_echo('izap_videos:all_videos'),
		'href' => $base . 'all',
		'section' => 'A',
	]);
	echo elgg_view('izap_videos/sidebar/extended_menu', $vars);
}

$page = elgg_extract('page', $vars);
switch ($page) {
	case 'all':
		echo elgg_view('page/elements/comments_block', [
			'subtypes' => IzapVideos::SUBTYPE,
		]);
		echo elgg_view('page/elements/tagcloud_block', [
			'subtypes' => IzapVideos::SUBTYPE,
		]);
		break;
	case 'owner':
		echo elgg_view('page/elements/comments_block', [
			'subtypes' => IzapVideos::SUBTYPE,
			'owner_guid' => elgg_get_page_owner_guid(),
		]);
		echo elgg_view('page/elements/tagcloud_block', [
			'subtypes' => IzapVideos::SUBTYPE,
			'owner_guid' => elgg_get_page_owner_guid(),
		]);
		break;
}

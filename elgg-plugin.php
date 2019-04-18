<?php

return [
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'izap_videos',
			'class' => 'IzapVideos',
			'searchable' => true,
		],
	],
	'actions' => [
		'izap_videos/settings/save' => [],
		'izap_videos/admin/api_keys' => ['access' => 'admin'],
		'izap_videos/admin/resetSettings' => ['access' => 'admin'],
		'izap_videos/admin/recycle' => ['access' => 'admin'],
		'izap_videos/admin/recycle_delete' => ['access' => 'admin'],
		'izap_videos/admin/reset' => ['access' => 'admin'],
		// 'izap_videos/admin/upgrade' => ['access' => 'admin'],
		'izap_videos/addEdit' => [],
		'izap_videos/delete' => [],
		'izap_videos/favorite_video' => [],
	],
	'routes' => [
		'all:object:izap_videos' => [
			'path' => '/videos/all',
			'resource' => 'izap_videos/videos/all',
		],
		'group:object:izap_videos' => [
			'path' => '/videos/group/{guid}',
			'resource' => 'izap_videos/videos/owner',
		],

		'owner:object:izap_videos' => [
			'path' => '/videos/owner/{username}/{guid}',
			'resource' => 'izap_videos/videos/owner',
		],
		'friends:object:izap_videos' => [
			'path' => '/videos/friends/{username}/{guid}',
			'resource' => 'izap_videos/videos/friends',
		],
		'favorites:object:izap_videos' => [
			'path' => '/videos/favorites/{username}/{guid}',
			'resource' => 'izap_videos/videos/favorites',
		],
		'play:object:izap_videos' => [
			'path' => '/videos/play/{username}/{guid}/{title}',
			'resource' => 'izap_videos/videos/play',
		],
		'add:object:izap_videos' => [
			'path' => '/videos/add/{guid}',
			'resource' => 'izap_videos/videos/add',
		],
		'edit:object:izap_videos' => [
			'path' => '/videos/edit/{username}/{guid}',
			'resource' => 'izap_videos/videos/edit',
		],
		'thumbs:object:izap_videos' => [
			'path' => '/videos/thumbs',
			'resource' => 'izap_videos/videos/thumbs',
		],
		'mostviewed:object:izap_videos' => [
			'path' => '/videos/mostviewed/{username}',
			'resource' => 'izap_videos/lists/mostviewedvideos',
		],
		'mostviewedtoday:object:izap_videos' => [
			'path' => '/videos/mostviewedtoday/{username}',
			'resource' => 'izap_videos/lists/mostviewedvideostoday',
		],
		'mostviewedthismonth:object:izap_videos' => [
			'path' => '/videos/mostviewedthismonth/{username}',
			'resource' => 'izap_videos/lists/mostviewedvideosthismonth',
		],
		'mostviewedlastmonth:object:izap_videos' => [
			'path' => '/videos/mostviewedlastmonth/{username}',
			'resource' => 'izap_videos/lists/mostviewedvideoslastmonth',
		],
		'mostviewedthisyear:object:izap_videos' => [
			'path' => '/videos/mostviewedthisyear/{username}',
			'resource' => 'izap_videos/lists/mostviewedvideosthisyear',
		],
		'mostcommented:object:izap_videos' => [
			'path' => '/videos/mostcommented/{username}',
			'resource' => 'izap_videos/lists/mostcommentedvideos',
		],
		'mostcommentedtoday:object:izap_videos' => [
			'path' => '/videos/mostcommentedtoday/{username}',
			'resource' => 'izap_videos/lists/mostcommentedvideostoday',
		],
		'mostcommentedthismonth:object:izap_videos' => [
			'path' => '/videos/mostcommentedthismonth/{username}',
			'resource' => 'izap_videos/lists/mostcommentedvideosthismonth',
		],
		'mostcommentedlastmonth:object:izap_videos' => [
			'path' => '/videos/mostcommentedlastmonth/{username}',
			'resource' => 'izap_videos/lists/mostcommentedvideoslastmonth',
		],
		'mostcommentedthisyear:object:izap_videos' => [
			'path' => '/videos/mostcommentedthisyear/{username}',
			'resource' => 'izap_videos/lists/mostcommentedvideosthisyear',
		],
		'recentlyviewed:object:izap_videos' => [
			'path' => '/videos/recentlyviewed',
			'resource' => 'izap_videos/lists/recentlyviewed',
		],
		'recentlycommented:object:izap_videos' => [
			'path' => '/videos/recentlycommented',
			'resource' => 'izap_videos/lists/recentlycommented',
		],
		// Route for five_star_plugin
		'recentvotes:object:izap_videos' => [
			'path' => '/videos/recentvotes',
			'resource' => 'izap_videos/lists/recentvotes',
		],
		'highestrated:object:izap_videos' => [
			'path' => '/videos/highestrated',
			'resource' => 'izap_videos/lists/highestrated',
		],
		'highestvotecount:object:izap_videos' => [
			'path' => '/videos/highestvotecount',
			'resource' => 'izap_videos/lists/highestvotecount',
		],
		
		'thumbs:object:izap_videos_files' => [
			'path' => '/izap_videos_files/{what}/{videoID}',
			'resource' => 'izap_videos/videos/thumbs',
		],
	],
];

<?php

$video = elgg_extract('video', $vars, false);
$selectedOption = elgg_extract('selectedOption', $vars);
$options = elgg_extract('options', $vars);
$izapLoadedValues = elgg_extract('izapLoadedValues', $vars);
$page_owner = elgg_extract('page_owner', $vars);

$remove_access_id = false;
if ($video) { // if we are editing a video
	$izapLoadedValues = $video->getAttributes();
	if ($video->converted == 'no' && $video->videotype == 'uploaded') {
		$remove_access_id = true;
	}
} else { // if it is a new video
	if (in_array($selectedOption, $options)) {
		echo elgg_view('izap_videos/addedit/' . $selectedOption, ['loaded_data' => $izapLoadedValues]);
	}
}

if ($page_owner instanceof ElggGroup) {
	if (!empty($page_owner->group_acl)) {
		$izapLoadedValues->access_id = $page_owner->group_acl;
	}
}
if (is_null($izapLoadedValues->access_id)) {
	$izapLoadedValues->access_id = elgg_get_default_access();
}

$izapLoadedValues->container_guid = elgg_get_page_owner_guid();

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:addEditForm:title'),
	'name' => 'params[title]',
	'value' => $izapLoadedValues->title,
	'required' => ($video || ($selectedOption != 'OFFSERVER') ? true : false),
]);

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('izap_videos:addEditForm:videoImage'),
	'name' => 'params[videoImage]',
	'value' => $izapLoadedValues->videoImage,
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('izap_videos:addEditForm:description'),
	'name' => 'params[description]',
	'value' => $izapLoadedValues->description,
]);

echo elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('izap_videos:addEditForm:tags'),
	'name' => 'params[tags]',
	'value' => $izapLoadedValues->tags,
]);

$categories = elgg_view('input/categories', $vars);
if ($categories) {
	echo $categories;
}

if ($remove_access_id) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'params[access_id]',
		'value' => $izapLoadedValues->access_id,
	]);
} else {
	echo elgg_view_field([
		'#type' => 'access',
		'#label' => elgg_echo('izap_videos:addEditForm:access_id'),
		'name' => 'params[access_id]',
		'value' => $izapLoadedValues->access_id,
	]);
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'params[container_guid]',
	'value' => $izapLoadedValues->container_guid,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'params[guid]',
	'value' => $izapLoadedValues->guid,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('izap_videos:addEditForm:save'),
	'id' => 'submit_button',
]);
$footer .= elgg_format_element('div', [
	'id' => 'progress_button',
	'style' => 'display: none;'],
	elgg_echo('izap_videos:please_wait') . elgg_format_element('img', ['src' => elgg_get_simplecache_url('izap_videos/form_submit.gif')], '')
);

elgg_set_form_footer($footer);

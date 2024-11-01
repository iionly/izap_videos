<?php

$buggy_videos = elgg_extract('buggy_videos', $vars, false);

$rows = [];
foreach($buggy_videos as $video_to_be_recycled) {
	$row = '';

	$ORIGNAL_name = $video_to_be_recycled['main_file'];
	$ORIGNAL_size = \IzapFunctions::izapFormatBytes(filesize($video_to_be_recycled['main_file']));

	$row = $ORIGNAL_name;
	$row .= '<br>';
	$row .= elgg_echo('izap_videos:restore_size') . $ORIGNAL_size;
	$row .= '<br>';
	$row = elgg_format_element('a', ['href' => elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/izap_videos/admin/recycle?guid=' . $video_to_be_recycled['guid'])], elgg_echo('izap_videos:restore'));

	$row .= '<br>';

	if (elgg_is_active_plugin('messages')) {
		$row .=  elgg_view_field([
			'#type' => 'text',
			'#label' => elgg_echo('izap_videos:addEditForm:title'),
			'name' => 'params[title]',
			'value' => $izapLoadedValues->title,
		]);
		
		$row .=  elgg_view_field([
			'#type' => 'text',
			'name' => "params[user_message_{$video_to_be_recycled['guid']}]",
			'value' => '',
		]);
		
		$row .=  elgg_view_field([
			'#type' => 'checkbox',
			'#label' => elgg_echo('izap_videos:send_user_message'),
			'name' => "params[send_message_{$video_to_be_recycled['guid']}]",
			'value' => 'yes',
		]);
	} else {
		$row .= elgg_echo('izap_videos:adminSettings:messages_plugin_missing');
	}

	$row .= '<br>';

	$row .=  elgg_view_field([
			'#type' => 'submit',
			'name' => "params[{$video_to_be_recycled['guid']}]",
			'text' => elgg_echo('delete'),
		]);

	$row = elgg_format_element('td', [], $row);
	$rows[] = elgg_format_element('tr', [], $row);
}

$table_content = elgg_format_element('tbody', [], implode('', $rows));
echo elgg_format_element('table', ['class' => 'izap_table'], $table_content);

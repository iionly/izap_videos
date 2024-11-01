<?php
/**
 * iZAP Videos plugin by iionly
 * (based on version 3.71b of the original izap_videos plugin for Elgg 1.7)
 * Contact: iionly@gmx.de
 * https://github.com/iionly
 *
 * Original developer of the iZAP Videos plugin:
 * @package Elgg videotizer, by iZAP Web Solutions
 * @license GNU Public License version 2
 * @Contact iZAP Team "<support@izap.in>"
 * @Founder Tarun Jangra "<tarun@izap.in>"
 * @link http://www.izap.in/
 *
 */

$queueStatus = (\IzapFunctions::izapIsQueueRunning_izap_videos()) ? elgg_echo('izap_videos:running') : elgg_echo('izap_videos:notRunning');
$queue_object = new IzapQueue();
$queuedVideos = $queue_object->get();

$content = elgg_format_element('h3', [], elgg_echo('izap_videos:queueStatus') . $queueStatus . ' (' . \IzapFunctions::izap_count_queue() . ')');

if (count($queuedVideos)) {
	$rows = [];
	$i = 0;
	foreach($queuedVideos as $queuedVideo) {
		$row = [];

		$extension_length = strlen(\IzapFunctions::izap_get_file_extension($queuedVideo['main_file']));
		$outputPath = substr($queuedVideo['main_file'], 0, '-' . ($extension_length + 1));

		$ORIGNAL_name = basename($queuedVideo['main_file']);
		$ORIGNAL_size = \IzapFunctions::izapFormatBytes(filesize($queuedVideo['main_file']));

		$VIDEO_name = basename($outputPath . '_c.mp4');
		if (file_exists($outputPath . '_c.mp4')) {
			$VIDEO_size = \IzapFunctions::izapFormatBytes(filesize($outputPath . '_c.mp4'));
		} else {
			$VIDEO_size = '0 KB';
		}

		$row[] = elgg_format_element('td', [], $ORIGNAL_name . '<br>' . $VIDEO_name);
		$row[] = elgg_format_element('td', [], $ORIGNAL_size . '<br>' . $VIDEO_size);
		$link = '';
		if ($queuedVideo['conversion'] != IN_PROCESS) {
			$link = elgg_view('output/url', [
				'href' => elgg_http_add_url_query_elements('action/izap_videos/admin/reset', ['guid' => $queuedVideo['guid']]),
				'text' => elgg_view_icon('delete-alt', []),
				'is_action' => true,
				'is_trusted' => true,
				'confirm'=> elgg_echo('izap_videos:adminSettings:resetQueue_confirm'),
			]);
		}
		$row[] = elgg_format_element('td', [], $link);

		$rows[] = elgg_format_element('tr', ['class' => (!$i && \IzapFunctions::izapIsQueueRunning_izap_videos()) ? 'queue_selected' : ''], implode('', $row));
		$i++;
	}
	$table_content = elgg_format_element('tbody', [], implode('', $rows));
	$content .= elgg_format_element('table', ['class' => 'izap_table'], $table_content);
} else {
	$content .= elgg_format_element('div', ['align' => 'center'], elgg_view('output/longtext', ['value' => elgg_echo('izap_videos:queueStatus:none'), 'class' => 'mtm']));
}

echo elgg_format_element('div', [], $content);

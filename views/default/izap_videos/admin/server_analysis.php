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

$Fail_functions = ini_get('disable_functions');

// $php_version = phpversion();

$exec = (!strstr($Fail_functions, 'exec') && is_callable('exec')) ? true : false;
$curl = (extension_loaded('curl')) ? true : false;
$ffmpeg_path = current(explode(' ', \IzapFunctions::izapAdminSettings_izap_videos('izapVideoCommand')));
$pdo_sqlite = (extension_loaded('pdo_sqlite')) ? true : false;

if ($exec) {
	$php_command = exec(\IzapFunctions::izapAdminSettings_izap_videos('izapPhpInterpreter') . ' --version', $output_PHP, $return_value);
	if ($return_value === 0) {
		$php = nl2br(implode('', $output_PHP));
	}

	$ffmpeg_command = exec($ffmpeg_path . ' -version', $output_FFmpeg, $return_var);
	if ($return_var === 0) {
		$ffmpeg = nl2br(implode($output_FFmpeg));

		$in_video =  elgg_get_plugins_path() . 'izap_videos/server_test/test_video.avi';
		$izap_videos = new IzapVideos();
		$izap_videos->owner_guid = elgg_get_logged_in_user_guid();
		$izap_videos->setFilename('izap_videos/server_test/test_video.avi');
		$izap_videos->open('write');
		$izap_videos->write(file_get_contents($in_video));

		$in_video = $izap_videos->getFilenameOnFilestore();
		if (!file_exists($in_video)) {
			$in_video =  elgg_get_plugins_path() . 'izap_videos/server_test/test_video.avi';
			exit;
			$izap_videos->open('write');
			$izap_videos->write(file_get_contents($in_video));
			$in_video = $izap_videos->getFilenameOnFilestore();
		}
		$izap_videos->close();

		if (file_exists($in_video)) {
			$in_video;
			$outputPath = substr($in_video, 0, -4);
			$out_video =  $outputPath . '_c.mp4';

			$commands = [
				'Simple command' => $ffmpeg_path . ' -y -i [inputVideoPath] [outputVideoPath]',
				'Optimized command' => $ffmpeg_path . ' -y -i [inputVideoPath] -vcodec libx264 -preset medium -b:v 330k -s 480x360 -acodec aac -ar 22050 -ab 48k [outputVideoPath]',
			];
		}
	}
}

$max_file_upload = \IzapFunctions::izapReadableSize_izap_videos(ini_get('upload_max_filesize'));
$max_post_size = \IzapFunctions::izapReadableSize_izap_videos(ini_get('post_max_size'));
$max_input_time = ini_get('max_input_time');
$max_execution_time = ini_get('max_execution_time');
$memory_limit = ini_get('memory_limit');

$rows = [];

$row = [];
$row[] = elgg_format_element('td style="width:25%"', [], elgg_echo('izap_videos:server_analysis:exec'));
$row[] = elgg_format_element('td style="width:25%"', [], (($exec) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail')));
$row[] = elgg_format_element('td style="width:50%"', [], elgg_echo('izap_videos:server_analysis:exec_info'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_' . (($exec) ? 'ok' : 'not_ok')], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:curl'));
$row[] = elgg_format_element('td', [], (($curl) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail')));
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:curl_info'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_' . (($curl) ? 'ok' : 'not_ok')], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:pdo'));
$row[] = elgg_format_element('td', [], (($pdo_sqlite) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail')));
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:pdo_info'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_' . (($pdo_sqlite) ? 'ok' : 'not_ok')], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:php'));
$row[] = elgg_format_element('td', [], (($php) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail')));
$info = $php;
if (!$php) {
	$info = elgg_echo('izap_videos:server_analysis:php_not_found');
	$info .= '<br>';
	$info .=  elgg_echo('izap_videos:server_analysis:php_action');
}
$row[] = elgg_format_element('td', [], $info);
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_' . (($php) ? 'ok' : 'not_ok')], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:ffmpeg'));
$row[] = elgg_format_element('td', [], (($ffmpeg) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail')));
$row[] = elgg_format_element('td', [], $ffmpeg);
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_' . (($ffmpeg) ? 'ok' : 'not_ok')], implode('', $row));

if (!empty($commands)) {
	foreach($commands as $key => $command) {
		$exec_command = str_replace(['[inputVideoPath]', '[outputVideoPath]'], [$in_video, $out_video], $command);
		exec($exec_command, $out_array, $return_array);
		if ($return_array > 0) {
			$row = [];
			$row[] = elgg_format_element('td', [], $key);
			$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:fail'));
			$output = '<input type="text" value="' . $command . '" onclick="this.select();"/><br>';
			$output .= elgg_echo('izap_videos:server_analysis:ffmpeg_action') . '<br>';
			if ($key == 'Simple command') {
				$output .= elgg_echo('izap_videos:server_analysis:simple_command');
			}
			$row[] = elgg_format_element('td', [], $output);
			$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_not_ok'], implode('', $row));
		} else {
			$row = [];
			$row[] = elgg_format_element('td', [], $key);
			$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:success'));
			$output = '<input type="text" value="' . $command . '" onclick="this.select();"/><br>';
			$output .= elgg_echo('izap_videos:server_analysis:ffmpeg_action') . '<br>';
			if ($key == 'Simple command') {
				$output .= elgg_echo('izap_videos:server_analysis:simple_command');
			}
			$row[] = elgg_format_element('td', [], $output);
			$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));
		}
	}
}

$row = [];
$row[] = elgg_format_element('td', [], 'upload_max_filesize');
$row[] = elgg_format_element('td', [], $max_file_upload);
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:upload_max_filesize'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], 'post_max_size');
$row[] = elgg_format_element('td', [], $max_post_size);
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:post_max_size'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], 'max_input_time');
$row[] = elgg_format_element('td', [], $max_input_time);
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:max_execution_time'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], 'max_execution_time');
$row[] = elgg_format_element('td', [], $max_execution_time);
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:max_execution_time'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));

$row = [];
$row[] = elgg_format_element('td', [], 'memory_limit');
$row[] = elgg_format_element('td', [], $memory_limit);
$row[] = elgg_format_element('td', [], elgg_echo('izap_videos:server_analysis:memory_limit'));
$rows[] = elgg_format_element('tr', ['class' => 'izap_server_report_ok'], implode('', $row));

$table_content = elgg_format_element('tbody', [], implode('', $rows));
$content = elgg_format_element('table', ['class' => 'izap_table'], $table_content);

echo elgg_format_element('div', [], $content);

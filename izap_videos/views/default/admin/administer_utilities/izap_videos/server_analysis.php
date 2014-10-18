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
$ffmpeg_path = current(explode(' ', izapAdminSettings_izap_videos('izapVideoCommand')));
$pdo_sqlite = (extension_loaded('pdo_sqlite')) ? true : false;

if($exec) {
	$php_command = exec(izapAdminSettings_izap_videos('izapPhpInterpreter') . ' --version', $output_PHP, $return_value);
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
			$out_video =  $outputPath . '_c.flv';

			$commands = array(
				'Simple command' => $ffmpeg_path . ' -y -i [inputVideoPath] [outputVideoPath]',
			);
		}
	}
}

$max_file_upload = izapReadableSize_izap_videos(ini_get('upload_max_filesize'));
$max_post_size = izapReadableSize_izap_videos(ini_get('post_max_size'));
$max_input_time = ini_get('max_input_time');
$max_execution_time = ini_get('max_execution_time');
$memory_limit = ini_get('memory_limit');

?>

<div>
	<table class="izap_table">
	<tbody>
		<tr class="izap_server_report_<?php echo ($exec) ? 'ok' : 'not_ok';?>">
			<td><?php echo elgg_echo('izap_videos:server_analysis:exec');?></td>
			<td><?php echo ($exec) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail');?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:exec_info');?></td>
		</tr>

		<tr class="izap_server_report_<?php echo ($curl) ? 'ok' : 'not_ok';?>">
			<td><?php echo elgg_echo('izap_videos:server_analysis:curl');?></td>
			<td><?php echo ($curl) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail');?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:curl_info');?></td>
		</tr>

		<tr class="izap_server_report_<?php echo ($pdo_sqlite) ? 'ok' : 'not_ok';?>">
			<td><?php echo elgg_echo('izap_videos:server_analysis:pdo');?></td>
			<td><?php echo ($pdo_sqlite) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail');?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:pdo_info');?></td>
		</tr>

		<tr class="izap_server_report_<?php echo ($php) ? 'ok' : 'not_ok'?>">
			<td><?php echo elgg_echo('izap_videos:server_analysis:php');?></td>
			<td><?php echo ($php) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail');?></td>
			<td>
				<?php
					if(!$php) {
						echo elgg_echo('izap_videos:server_analysis:php_not_found');
				?>
						<br />
				<?php
						echo elgg_echo('izap_videos:server_analysis:php_action');
					} else {
						echo $php;
					}
				?>
			</td>
		</tr>

		<tr class="izap_server_report_<?php echo ($ffmpeg) ? 'ok' : 'not_ok';?>">
			<td><?php echo elgg_echo('izap_videos:server_analysis:ffmpeg');?></td>
			<td><?php echo ($ffmpeg) ? elgg_echo('izap_videos:server_analysis:success') : elgg_echo('izap_videos:server_analysis:fail');?></td>
			<td><?php echo $ffmpeg;?><br /></td>
		</tr>

		<?php
			if (!empty($commands)) {
				foreach($commands as $key => $command) {
					$exec_command = str_replace(array('[inputVideoPath]', '[outputVideoPath]'), array($in_video, $out_video), $command);
					exec($exec_command, $out_array, $return_array);
					if ($return_array > 0) {
		?>
						<tr class="izap_server_report_not_ok">
							<td><?php echo $key; ?></td>
							<td><?php echo elgg_echo('izap_videos:server_analysis:fail');?></td>
							<td>
								<input type="text" value="<?php echo $command?>" onclick="this.select();"/><br />
							<?php
								echo elgg_echo('izap_videos:server_analysis:ffmpeg_action');
							?>
								<br />
							<?php
								if ($key == 'Simple command') {
									echo elgg_echo('izap_videos:server_analysis:simple_command');
								}
							?>
							</td>
						</tr>
				<?php
					} else {
				?>
						<tr class="izap_server_report_ok">
							<td><?php echo $key; ?></td>
							<td><?php echo elgg_echo('izap_videos:server_analysis:success');?></td>
							<td>
								<input type="text" value="<?php echo $command?>" onclick="this.select();"/><br />
							<?php
								if ($key == 'Simple command') {
									echo elgg_echo('izap_videos:server_analysis:simple_command');
								}
							?>
							</td>
						</tr>
		<?php
					}
				}
			}
		?>

		<tr class="izap_server_report_ok">
			<td>upload_max_filesize</td>
			<td><?php echo $max_file_upload;?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:upload_max_filesize');?></td>
		</tr>

		<tr class="izap_server_report_ok">
			<td>post_max_size</td>
			<td><?php echo $max_post_size;?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:post_max_size');?></td>
		</tr>

		<tr class="izap_server_report_ok">
			<td>max_input_time</td>
			<td><?php echo $max_input_time;?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:max_input_time');?></td>
		</tr>

		<tr class="izap_server_report_ok">
			<td>max_execution_time</td>
			<td><?php echo $max_execution_time;?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:max_execution_time');?></td>
		</tr>

		<tr class="izap_server_report_ok">
			<td>memory_limit</td>
			<td><?php echo $memory_limit;?></td>
			<td><?php echo elgg_echo('izap_videos:server_analysis:memory_limit');?></td>
		</tr>

	</tbody>
	</table>
</div>

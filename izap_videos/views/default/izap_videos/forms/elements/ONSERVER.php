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

$maxFileSize = (int)izapAdminSettings_izap_videos('izapMaxFileSize');
?>

<div class="mbm">
	<label for="video_file">
		<?php echo elgg_echo('izap_videos:addEditForm:videoFile') . ' ' . elgg_echo('izap_videos:addEditForm:maxFilesize', array($maxFileSize));?>
	</label>
	<?php
	echo elgg_view('input/file', array(
		'name' => 'izap[videoFile]',
		'value' => $vars['loaded_data']->videoFile,
		'id' => 'video_file',
	));
	?>
	<span class="elgg-subtext">
		<?php
		echo elgg_echo('izap_videos:ONSERVER:supported_formats');
		?>
	</span>
</div>
<?php
echo elgg_view('input/hidden', array(
	'name' => 'izap[videoType]',
	'value' => 'ONSERVER',
));

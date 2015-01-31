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
?>

<div class="mbm">
	<label for="video_file">
		<?php echo elgg_echo('izap_videos:addEditForm:videoEmbed');?>
	</label>
	<?php
	echo elgg_view('input/text', array(
		'name' => 'izap[videoEmbed]',
		'value' => $vars['loaded_data']->videoEmbed,
		'id' => 'video_file',
	));
	?>
</div>
<?php
echo elgg_view('input/hidden', array(
	'name' => 'izap[videoType]',
	'value' => 'EMBED',
));

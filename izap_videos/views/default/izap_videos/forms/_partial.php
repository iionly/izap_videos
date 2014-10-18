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

global $IZAPSETTINGS;

$remove_access_id = false;
// get page owner
$page_owner = elgg_get_page_owner_entity();

// get entity
$video = $vars['entity'];

// get the add options
$options = izapGetVideoOptions_izap_videos();

// get the selected option
$selectedOption = get_input('option', '');
if (empty($selectedOption) || !in_array($selectedOption, $options)) {
	$selectedOption = $options[0];
}

// get values from session if any
$izapLoadedValues = new stdClass();
if (isset($_SESSION['izapVideos']) && !empty($_SESSION['izapVideos'])) {
	$izapLoadedValues = izapArrayToObject_izap_videos($_SESSION['izapVideos']);
}
$izapLoadedValues->access_id = null;

if (empty($video)) {  // if it is a new video
	$tabs = elgg_view('izap_videos/forms/elements/tabs', array('options' => $options, 'selected' => $selectedOption));
	if (in_array($selectedOption, $options)) {
		$modular_form = elgg_view('izap_videos/forms/elements/' . $selectedOption, array('loaded_data' => $izapLoadedValues));
	}
} else {  // if we are editing a video
	$izapLoadedValues = $video->getAttributes();
}

if ($page_owner instanceof ElggGroup) {
	if (!empty($page_owner->group_acl)) {
		$izapLoadedValues->access_id = $page_owner->group_acl;
	}
}
if (is_null($izapLoadedValues->access_id)) {
	$izapLoadedValues->access_id = get_default_access();
}

if ($video->converted == 'no' && $video->videotype == 'uploaded') {
	$remove_access_id = true;
}

$izapLoadedValues->container_guid = elgg_get_page_owner_guid();
?>

<div>
	<?php echo $tabs;?>

	<form action="<?php echo elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/izap_videos/addEdit');?>" method="POST" enctype="multipart/form-data" id="video_form" >

		<?php echo $modular_form;?>

		<div class="mbm">
			<label for="video_optional_image">
				<?php echo elgg_echo('izap_videos:addEditForm:videoImage')?>
			</label>
			<?php
				echo elgg_view('input/file', array(
					'name' => 'izap[videoImage]',
					'value' => $izapLoadedValues->videoImage,
					'id' => 'video_optional_image',
				));
			?>
		</div>

		<div class="mbm">
			<label for="video_title">
				<?php echo elgg_echo('izap_videos:addEditForm:title')?>
			</label>
			<?php
				echo elgg_view('input/text', array(
					'name' => 'izap[title]',
					'value' => $izapLoadedValues->title,
					'id' => 'video_title',
				));
			?>
		</div>

		<div class="mbm">
			<label for="video_description">
				<?php echo elgg_echo('izap_videos:addEditForm:description')?>
			</label>
			<?php
				echo elgg_view('input/longtext', array(
					'name' => 'izap[description]',
					'value' => $izapLoadedValues->description,
					'id' => 'video_description',
				));
			?>
		</div>

		<div class="mbm">
			<label for="video_tags">
				<?php echo elgg_echo('izap_videos:addEditForm:tags')?>
			</label>
			<?php
				echo elgg_view('input/tags', array(
					'name' => 'izap[tags]',
					'value' => $izapLoadedValues->tags,
					'id' => 'video_tags',
				));
			?>
		</div>

		<div class="mbm">
		<?php
			echo elgg_view('input/categories', $vars);
		?>
		</div>

		<div class="mbm">
			<label for="video_access">
				<?php echo elgg_echo('izap_videos:addEditForm:access_id')?>
			</label>
			<?php
				echo elgg_view('input/' . (($remove_access_id) ? 'hidden' : 'access'), array(
					'name' => 'izap[access_id]',
					'value' => $izapLoadedValues->access_id,
					'id' => 'video_access',
				));
			?>
		</div>

		<?php
			echo elgg_view('input/hidden', array(
				'name' => 'izap[container_guid]',
				'value' => $izapLoadedValues->container_guid,
			));
			echo elgg_view('input/hidden', array(
				'name' => 'izap[guid]',
				'value' => $izapLoadedValues->guid,
			));
		?>
		<div id="submit_button">
			<?php
				echo elgg_view('input/submit', array('value' => elgg_echo('izap_videos:addEditForm:save')));
			?>
		</div>
		<div id="progress_button" style="display: none;">
			<?php echo elgg_echo('izap_videos:please_wait');?><img src="<?php echo elgg_get_site_url() . 'mod/izap_videos/_graphics/form_submit.gif' ;?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#video_form').submit(function() {
			$('#submit_button').hide();
			$('#progress_button').show();
		});
	});
</script>

<?php
// unset the session when from is loaded
unset($_SESSION['izapVideos']);

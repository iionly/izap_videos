<?php
/**
 * Index page Latest Videos widget for Widget Manager plugin
 *
 */

$count = sanitise_int($vars["entity"]->latest_videos_count, false);
if (empty($count)) {
	$count = 4;
}

?>
<div>
	<?php echo elgg_echo("izap_videos:numbertodisplay"); ?><br>
	<?php echo elgg_view("input/text", array("name" => "params[latest_videos_count]", "value" => $count, "size" => "4", "maxlength" => "4")); ?>
</div>

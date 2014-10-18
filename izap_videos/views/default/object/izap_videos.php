<?php
/**
 * Video view
 *
 */

$full_view = elgg_extract('full_view', $vars, false);

if ($full_view) {
	echo elgg_view('object/izap_videos/full', $vars);
} else {
	echo elgg_view('object/izap_videos/list', $vars);
}

return true;
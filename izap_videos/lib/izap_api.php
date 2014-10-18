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


class IZAPVideoApi {
	private $input_object;

	public function __construct($input = '') {
		if (!empty($input)) {
			$this->input_object = $input;
		}
	}

	/**
	 * converts the video
	 *
	 * @return <type>
	 */
	public function convertVideo() { // experimental
		if (!izapSupportedVideos_izap_videos($this->input_object)) {
			return elgg_echo('izap_videos:error:code:106');
		}

		$convert_video = new izapConvert($this->input_object);
		if ($convert_video->photo()) {
			if($convert_video->izap_video_convert()) {
				return $convert_video->getValuesForAPI();
			}
		}

		// if nothing is processed so far
		return false;
	}

	/**
	 * returns the video player code, if the input is URL
	 *
	 * @param int $width width of video player
	 * @param int $height height of video playe
	 * @param int $autoPlay autocomplete option
	 * @return HTML player code
	 */
	public function getFeed($width = 600, $height = 360, $autoPlay = 0) {
		$get_url_feed = new IzapVideos();
		$feed = $get_url_feed->input($this->input_object, 'url');

		// in case there is an error
		if (!is_object($feed)) {
			return elgg_echo('izap_videos:error:code:' . $feed);
		}

		$get_url_feed->videotype = $feed->type;
		$get_url_feed->videosrc = $feed->videoSrc;
		$get_url_feed->converted = 'yes';
		$player = $get_url_feed->getPlayer($width, $height, $autoPlay);

		return $player;
	}
}


/**
 * returns the supported video types
 * @return text
 */
function izapGetSupportedVideoFormats_izap_API() {
	return implode(', ', izapGetSupportingVideoFormats_izap_videos());
}


/**
 * returns the list of supported video site, from which feed can be get
 * @return HTML
 */
function izapGetSupportedUrls_izap_API() {
	return implode('<br />', izapGetSupportingVideoSites_izap_videos());
}

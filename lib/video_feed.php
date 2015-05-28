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

class UrlFeed extends GetFeed {
	private $youtube_api_capture = array('api_location' => 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=');
	private $vimeo_api_capture = array('api_location' => 'https://vimeo.com/api/v2/video/');
	private $dailymotion_api_capture = array('api_location' => 'https://api.dailymotion.com/video/');
	private $feed;
	public  $type;
	private $video_id;

	function setUrl($url = '') {
		if (preg_match('/(https?:\/\/)?((youtu\.be\/)|((www\.)?(youtube\.com\/)))(.*)/', $url, $matches)) {
			$this->type = 'youtube';
		} elseif (preg_match('/(https?:\/\/)?(www\.)?(vimeo\.com\/)(.*)/', $url, $matches)) {
			$this->type = 'vimeo';
		} elseif (preg_match('/(https?:\/\/)?(www\.)?(dailymotion\.com\/)(.*)/', $url, $matches)) {
			$this->type = 'dailymotion';
		}

		switch($this->type) {
			case 'youtube':
				$youtube_api_key = elgg_get_plugin_setting('youtube_api_key', 'izap_videos');
				if (preg_match('/(https?:\/\/)?(youtu\.be\/)(.*)/', $url, $matches)) {
					$explode_char = '/';
					$url_pram = explode($explode_char, $url);
					$this->video_id = sanitise_string(end($url_pram));
				} else {
					$url_pram = explode("?", $url);
					$url_pram = explode("&", $url_pram[1]);
					$url_pram = explode("=", $url_pram[0]);
					$this->video_id = $url_pram[1];
				}
				$this->feed = array('url' => $this->youtube_api_capture['api_location'] . $this->video_id . '&key=' . $youtube_api_key, 'type' => 'youtube');
				break;
			case 'vimeo':
				$explode_char = '/';
				if (preg_match('/staffpicks#/', $url)) {
					$explode_char = '#';
				}
				$url_pram = explode($explode_char, $url);
				$this->video_id = sanitise_int(end($url_pram));
				$this->feed = array('url' => $this->vimeo_api_capture['api_location'] . $this->video_id.'.php', 'type' => 'vimeo');
				break;
			case 'dailymotion':
				$explode_char = '/';
				$url_pram = explode($explode_char, $url);
				$this->video_id = sanitise_string(end($url_pram));
				$this->feed = array('url' => $this->dailymotion_api_capture['api_location'] . $this->video_id.'?fields=title,description,thumbnail_url,id,tags', 'type' => 'dailymotion');
				break;
			default:
				return 103;
				break;
		}

		return $this->capture();
	}

	function capture() {

		$obj= new stdClass;

		$arry = $this->readFeed($this->feed['url'], $this->feed['type']);

		$obj->title = $arry['title'];
		$obj->description = $arry['description'];
		$obj->videoThumbnail = $arry['videoThumbnail'];
		$obj->videoTags = $arry['videoTags'];
		$obj->videoSrc = $arry['videoSrc'];
		if(empty($obj->title) or empty($obj->videoSrc) or empty($obj->videoThumbnail)) {
			if(!empty($arry['error'])) {
				return $arry['error'];
			} else {
				return $arry;
			}
		}
		$obj->fileName = time() . $this->video_id . ".jpg";

		$urltocapture =  new curl($obj->videoThumbnail);
		$urltocapture->setopt(CURLOPT_HTTPGET, true);

		$obj->fileContent = $urltocapture->exec();

		$obj->type = $this->feed['type'];
		return $obj;
	}
}

/**
 * Gets the list of supported video sites
 *
 * @return array array of supported videos site.
 */
function izapGetSupportingVideoSites_izap_videos() {

	$supportedSites[] = 'http://www.youtube.com';
	$supportedSites[] = 'http://www.vimeo.com';
	$supportedSites[] = 'http://www.dailymotion.com';

	asort($supportedSites);
	return $supportedSites;
}

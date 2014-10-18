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

class GetFeed {
	private $url;
	private $type;
	private $feedArray = array();
	private $mainArray = array();
	private $returnArray = array();
	private $fileRead;

	function readFeed($url, $type = '') {
		$this->url = $url;
		$this->type = $type;

		$urltocapture =  new curl($this->url);
		$urltocapture->setopt(CURLOPT_HTTPGET, true);
		$this->fileRead = $urltocapture->exec();

		if (empty($this->fileRead) or !$this->fileRead) {
			return 101;
		}

		$ext = new btext();
		$this->feedArray = $ext->xml2array($this->fileRead);

		switch($this->type) {
			case 'youtube':
				return $this->youtube();
				break;
			case 'vimeo':
				return $this->vimeo();
				break;
			case 'dailymotion':
				return $this->dailymotion();
				break;
			default:
				return false;
				break;
		}
	}

	function youtube() {
		$this->mainArray = $this->feedArray['entry']['media:group'];
		if (empty($this->mainArray)) {
			return 101;
		}

		$this->returnArray['title'] = $this->mainArray['media:title'];
		$this->returnArray['description'] = $this->mainArray['media:description'];
		$this->returnArray['videoThumbnail'] = $this->mainArray['media:thumbnail']['1_attr']['url'];
		$this->returnArray['videoSrc'] = $this->mainArray['media:content_attr']['url'];
		if (empty($this->returnArray['videoSrc'])) {
			$this->returnArray['videoSrc'] = $this->mainArray['media:content']['0_attr']['url'];
		}
		if ($this->mainArray['media:keywords'] && (is_array($this->mainArray['media:keywords']))) {
			$this->returnArray['videoTags'] = implode(",", $this->mainArray['media:keywords']);
		} else if ($this->mainArray['media:keywords'] && (is_string($this->mainArray['media:keywords'])) && (!empty($this->mainArray['media:keywords']))) {
			$this->returnArray['videoTags'] = $this->mainArray['media:keywords'];
		} else {
			$this->returnArray['videoTags'] = '';
		}

		// if still empty videoSrc, then test if it is a restricted video
		if (!empty($this->feedArray['entry']['app:control']['yt:state'])) {
			$this->returnArray['error'] = $this->feedArray['entry']['app:control']['yt:state'];
		}
		return $this->returnArray;
	}

	function vimeo() {
		$this->mainArray = unserialize($this->fileRead);
		$this->mainArray = $this->mainArray[0];
		if (empty($this->mainArray)) {
			return 101;
		}

		$this->returnArray['title'] = $this->mainArray['title'];
		$this->returnArray['description'] = $this->mainArray['caption'];
		$this->returnArray['videoThumbnail'] = $this->mainArray['thumbnail_large'];
		$this->returnArray['videoSrc'] = 'http://player.vimeo.com/video/'.$this->mainArray['id'].'?portrait=0&color=333';
		$this->returnArray['videoTags'] = $this->mainArray['tags'];

		return $this->returnArray;
	}

	function dailymotion() {
		$this->mainArray = json_decode($this->fileRead, true);
		if (empty($this->mainArray)) {
			return 101;
		}

		$this->returnArray['title'] = $this->mainArray['title'];
		$this->returnArray['description'] = $this->mainArray['description'];
		$this->returnArray['videoThumbnail'] = $this->mainArray['thumbnail_url'];
		$this->returnArray['videoSrc'] = 'http://www.dailymotion.com/embed/video/'.$this->mainArray['id'];
		if ($this->mainArray['tags'] && (is_array($this->mainArray['tags']))) {
			$this->returnArray['videoTags'] = implode(",", $this->mainArray['tags']);
		} else {
			$this->returnArray['videoTags'] = '';
		}

		return $this->returnArray;
	}
}

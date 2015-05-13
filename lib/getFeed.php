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
		$this->mainArray = json_decode($this->fileRead, true);
		if (empty($this->mainArray['items'][0])) {
			return 101;
		}
		
		$this->returnArray['title'] = $this->mainArray['items'][0]['snippet']['title'];
		$this->returnArray['description'] = $this->mainArray['items'][0]['snippet']['description'];
		$this->returnArray['videoThumbnail'] = $this->mainArray['items'][0]['snippet']['thumbnails']['standard']['url'];
		$this->returnArray['videoSrc'] = 'https://www.youtube.com/embed/'.$this->mainArray['items'][0]['id'];
		if ($this->mainArray['items'][0]['snippet']['tags'] && (is_array($this->mainArray['items'][0]['snippet']['tags']))) {
			$this->returnArray['videoTags'] = implode(",", $this->mainArray['items'][0]['snippet']['tags']);
		} else {
			$this->returnArray['videoTags'] = '';
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
		$this->returnArray['videoSrc'] = 'https://player.vimeo.com/video/'.$this->mainArray['id'].'?portrait=0&color=333';
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
		$this->returnArray['videoSrc'] = 'https://www.dailymotion.com/embed/video/'.$this->mainArray['id'];
		if ($this->mainArray['tags'] && (is_array($this->mainArray['tags']))) {
			$this->returnArray['videoTags'] = implode(",", $this->mainArray['tags']);
		} else {
			$this->returnArray['videoTags'] = '';
		}

		return $this->returnArray;
	}
}

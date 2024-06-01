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


class IzapVideos extends \ElggFile {

	/**
	 * A single-word arbitrary string that defines what
	 * kind of object this is
	 *
	 * @var string
	 */
	const SUBTYPE = 'izap_videos';

	public function __construct($row = null) {
		parent::__construct($row);
	}

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Takes input and type of input and sends back the required parameters to save a video
	 *
	 * @param string $input video url, video file or video embed code
	 * @param string $type url, file, embed
	 * @return object
	 */
	public function input($input, $type) {
		switch ($type) {
			case 'url':
				return $this->readUrl($input);
				break;
			case 'file':
				return $this->processFile($input);
				break;
			case 'embed':
				return $this->embedCode($input);
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * Used to read the url and process the feed
	 *
	 * @param url $url url of the video site
	 * @return object
	 */
	protected function readUrl($url) {
		$urlFeed = new IzapUrlFeed();
		$feed = $urlFeed->setUrl($url);
		return $feed;
	}

	/**
	 * Used to process the video file
	 * @param string $file upload file name
	 * @return object
	 */
	protected function processFile($file) {

		$returnValue =  new stdClass();
		$returnValue->type = 'uploaded';
		$fileName = $_FILES[$file['mainArray']]['name'][$file['fileName']];
		$error = $_FILES[$file['mainArray']]['error'][$file['fileName']];
		$tmpName = $_FILES[$file['mainArray']]['tmp_name'][$file['fileName']];
		$type = $_FILES[$file['mainArray']]['type'][$file['fileName']];
		$size = $_FILES[$file['mainArray']]['size'][$file['fileName']];

		// if error
		if ($error > 0) {
			return 104;
		}

		// if file is of zero size
		if ($size == 0) {
			return 105;
		}

		// check supported video type
		if (!\IzapFunctions::izapSupportedVideos_izap_videos($fileName)) {
			return 106;
		}

		// check supported video size
		if (!\IzapFunctions::izapCheckFileSize_izap_videos($size)) {
			return 107;
		}

		// upload the tmp file
		$newFileName = \IzapFunctions::izapGetFriendlyFileName_izap_videos($fileName);
		$this->setFilename('tmp/' . $newFileName);
		$this->open("write");
		$this->write(file_get_contents($tmpName));
		$returnValue->tmpFile = $this->getFilenameOnFilestore();

		// take snapshot of the video
		$image = new IzapConvert($returnValue->tmpFile);
		if ($image->photo()) {
			$retValues = $image->getValues(true);
			if ($retValues['imagename'] != '' && $retValues['imagecontent'] != '') {
				$this->setFilename('izap_videos/uploaded/' . $retValues['imagename']);
				$this->open("write");
				if ($this->write($retValues['imagecontent'])) {
					$orignal_file_path = $this->getFilenameOnFilestore();

					$this->setFilename('izap_videos/uploaded/' . $retValues['imagename']);
					elgg_save_resized_image($orignal_file_path, $this->getFilenameOnFilestore(), ['w' => 120, 'h' => 90, 'square' => true, 'upscale' => true]);

					$returnValue->thumb = 'izap_videos/uploaded/' . $retValues['imagename'];
					// Defining new preview attribute of standard object
					$returnValue->preview_400 = 'izap_videos/uploaded/preview_400';
					$returnValue->preview_200 = 'izap_videos/uploaded/preview_200';
				}
			}
		}

		return $returnValue;
	}

	/**
	 * Process the embed code
	 *
	 * @param HTML $code embed code
	 * @return object
	 */
	protected function embedCode($code) {
		$returnValue =  new stdClass();
		$returnValue->type = 'embed';
		$returnValue->videoSrc = $code;

		return $returnValue;
	}

	/**
	 * Gets the video player according to the video type
	 *
	 * @param int $width width of video player
	 * @param int $height height of video player
	 * @return HTML complete player code
	 */
	public function getPlayer($width = 600, $height = 360) {
		$html = '';

		switch ($this->videotype) {
			case 'youtube':
				$html = "<iframe src=\"{$this->videosrc}?rel=0\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'vimeo':
				$html = "<iframe src=\"{$this->videosrc}\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'dailymotion':
				$html = "<iframe src=\"{$this->videosrc}\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'uploaded':
				if($this->converted == 'yes') {
					$imageurl = elgg_get_site_url() . 'izap_videos_files/image/' . $this->getGUID();
					$videourl = elgg_get_site_url() . 'izap_videos_files/file/' . $this->getGUID() . '/' . \IzapFunctions::izap_get_friendly_title($this->title) . '.mp4';
					$html = "<video id='videojsPlayer' class='video-js vjs-default-skin vjs-16-9 vjs-big-play-centered' controls='true' preload='none' width='{$width}' height='{$height}' poster='{$imageurl}' data-setup='{}'><source src='{$videourl}' type='video/mp4'><p class='vjs-no-js'>To view this video please enable JavaScript, and consider upgrading to a web browser that <a href='http://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a></p></video>";
				} else {
					$html = elgg_echo('izap_videos:processed');
				}
				break;
			case 'embed':
				$html = \IzapFunctions::izapGetReplacedHeightWidth_izap_videos($height, $width, $this->videosrc);
				break;
		}
		return $html;
	}

	/**
	 * Returns the thumbnail for the video
	 *
	 * @param boolean $pathOnly if we want the img src only or full <img ... /> tag
	 * @param array $attArray attributes for the <img /> tag
	 * @return HTML <img /> tag or image src
	 */
	public function getThumb($pathOnly = false, $attArray = []) {
		$html = '';
		$imagePath = elgg_get_site_url() . '/izap_videos_files/image/' . $this->guid . '/' . \IzapFunctions::izap_get_friendly_title($this->title) . '.jpg';

		if ($pathOnly) {
			$html = $imagePath;
		} else {
			$attributes = [];
			if (count($attArray) > 0) {
				foreach ($attArray as $att => $value) {
					$attributes[$att] = $value;
				}
				$attributes['src'] = $imagePath;
			}
			$html = elgg_format_element('img', $attributes, '');
		}

		return $html;
	}

	/**
	 * Updates the video views
	 */
	public function updateViews() {
		if ($this->converted == 'yes') {
			elgg_call(ELGG_IGNORE_ACCESS, function() {
				$this->views = (int) $this->views + 1;
				$this->last_viewed = (int) time();
			});
		}
	}

	/**
	 * Returns the video views
	 *
	 * @return int video views
	 */
	public function getViews() {
		return $this->views;
	}

	/**
	 * Returns the full video attributes
	 *
	 * @return object
	 */
	public function getAttributes() {
		$attrib = new stdClass();
		$attrib->guid = $this->guid;
		$attrib->title = $this->title;
		$attrib->owner_guid = $this->owner_guid;
		$attrib->container_guid = $this->container_guid;
		$attrib->description = $this->description;
		$attrib->access_id = $this->access_id;
		$attrib->tags = $this->tags;
		$attrib->views = $this->views;
		$attrib->videosrc = $this->videosrc;
		$attrib->videotype = $this->videotype;
		$attrib->imagesrc = $this->imagesrc;
		$attrib->videotype_site = $this->videotype_site;
		$attrib->videotype_id = $this->videotype_id;
		$attrib->converted = $this->converted;
		$attrib->videofile = $this->videofile;
		$attrib->orignalfile = $this->orignalfile;

		return $attrib;
	}

	/**
	 * Deletes a video, override for the parent delete
	 *
	 * @return boolean
	 */
	public function delete($follow_symlinks = true): bool {
		// in case of an uploaded video make sure it's also deleted from queue and trash
		// with related media if it still remained there
		if ($this->videotype == 'uploaded') {
			$queue_object = new IzapQueue();
			$queue_object->delete_from_trash($this->guid, true);
			$queue_object->delete($this->guid, true);
		}

		$imagesrc = $this->imagesrc ? $this->imagesrc : '';
		$filesrc = $this->videofile ? $this->videofile : '';
		$ofilesrc = $this->orignalfile ? $this->orignalfile : '';
		//delete entity from elgg db and corresponding files if exist
		$this->setFilename($imagesrc);
		$image_file = $this->getFilenameOnFilestore();
		file_exists($image_file) && @unlink($image_file);

		$this->setFilename($filesrc);
		$video_file = $this->getFilenameOnFilestore();
		file_exists($video_file) && @unlink($video_file);

		$this->setFilename($ofilesrc);
		$orignal_file = $this->getFilenameOnFilestore();
		file_exists($orignal_file) && @unlink($orignal_file);

		return parent::delete($follow_symlinks);
	}

	/**
	 * Returns the url for the owner video list
	 *
	 * @return URL
	 */
	public function getOwnerUrl() {
		return elgg_get_site_url() . 'videos/owner/' . $this->getOwnerEntity()->username . '/';
	}
}

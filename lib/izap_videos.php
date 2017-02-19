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


class IzapVideos extends ElggFile {
	private $IZAPSETTINGS;

	public function __construct($row = null) {
		parent::__construct($row);
	}

	protected function initializeAttributes() {
		global $IZAPSETTINGS;
		parent::initializeAttributes();
		$this->attributes['subtype'] = 'izap_videos';
		$this->IZAPSETTINGS = $IZAPSETTINGS;
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
		$urlFeed = new UrlFeed();
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
		if (!izapSupportedVideos_izap_videos($fileName)) {
			return 106;
		}

		// check supported video size
		if (!izapCheckFileSize_izap_videos($size)) {
			return 107;
		}

		// upload the tmp file
		$newFileName = izapGetFriendlyFileName_izap_videos($fileName);
		$this->setFilename('tmp/' . $newFileName);
		$this->open("write");
		$this->write(file_get_contents($tmpName));
		$returnValue->tmpFile = $this->getFilenameOnFilestore();

		// take snapshot of the video
		$image = new izapConvert($returnValue->tmpFile);
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

		// check if it is flv, then dont send it to queue
		if (izap_get_file_extension($returnValue->tmpFile) == 'flv') {
			$file_name = 'izap_videos/uploaded/' . $newFileName;

			$this->setFilename($file_name);
			$this->open("write");
			$this->write(file_get_contents($returnValue->tmpFile));

			$this->converted = 'yes';
			$this->videofile = $file_name;
			$this->orignalfile = $file_name;
			$returnValue->is_flv = 'yes';
			// remove the tmp file
			@unlink($returnValue->tmpFile);
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
	 * @param int $autoPlay autoplay option (1 | 0)
	 * @param string $extraOptions extra options if available
	 * @return HTML complete player code
	 */
	public function getPlayer($width = 600, $height = 360, $autoPlay = 0, $extraOptions = '') {
		$html = '';

		switch ($this->videotype) {
			case 'youtube':
				$html = "<iframe src=\"{$this->videosrc}?rel=0&amp;autoplay={$autoPlay}\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'vimeo':
				$html = "<iframe src=\"{$this->videosrc}&amp;autoplay={$autoPlay}\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'dailymotion':
				$html = "<iframe src=\"{$this->videosrc}?autoplay={$autoPlay}\" width=\"$width\" height=\"$height\" wmode=\"transparent\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
				break;
			case 'uploaded':
				if($this->converted == 'yes') {
					$border_color1 = izapAdminSettings_izap_videos('izapBorderColor1');
					$border_color2 = izapAdminSettings_izap_videos('izapBorderColor2');
					$border_color3 = izapAdminSettings_izap_videos('izapBorderColor3');

					if (!empty($border_color3)) {
						$extraOptions .= '&btncolor=0x' . $border_color3;
					}
					if (!empty($border_color1)) {
						$extraOptions .= '&accentcolor=0x' . $border_color1;
					}
					if (!empty($border_color2)) {
						$extraOptions .= '&txtcolor=0x' . $border_color2;
					}
					$html = "
<object width='".$width."' height='".$height."' id='flvPlayer'>
<param name='allowFullScreen' value='true'>
<param name='allowScriptAccess' value='always'>
<param name='movie' value='".$this->IZAPSETTINGS->playerPath."?movie=".$this->videosrc . $extraOptions ."&volume=30&autoload=on&autoplay=off&vTitle=".$this->title."&showTitle=yes' >
<embed src='".$this->IZAPSETTINGS->playerPath."?movie=".$this->videosrc . $extraOptions ."&volume=30&autoload=on&autoplay=off&vTitle=".$this->title."&showTitle=yes' width='".$width."' height='".$height."' allowFullScreen='true' type='application/x-shockwave-flash' allowScriptAccess='always' wmode='transparent'>
</object>";
				} else {
					$html = elgg_echo('izap_videos:processed');
				}
				break;
			case 'embed':
				$html = izapGetReplacedHeightWidth_izap_videos($height, $width, $this->videosrc);
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
	public function getThumb($pathOnly = false, $attArray = array()) {
		$html = '';
		$attString = '';
		$imagePath = $this->IZAPSETTINGS->filesPath . 'image/' . $this->guid . '/' . elgg_get_friendly_title($this->title) . '.jpg';
		if (count($attArray) > 0) {
			foreach ($attArray as $att => $value) {
				$attString .= ' '.$att.'="'.$value.'" ';
			}
		}
		if ($pathOnly) {
			$html = $imagePath;
		} else {
			$html = '<img src="'.$imagePath.'" '.$attString.' />';
		}

		return $html;
	}

	/**
	 * Function to return the icon path
	 * @uses getThumb()
	 * @return url
	 */
// 	public function getIcon($size, $type = 'icon') {
// 		return $this->getThumb(true);
// 	}

	/**
	 * Updates the video views
	 */
	public function updateViews() {
		if($this->converted == 'yes') {
			izapGetAccess_izap_videos();
			$this->views = ((int)$this->views + 1);
			izapRemoveAccess_izap_videos();
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
	public function delete() {
		// in case of an uploaded video make sure it's also deleted from queue and trash
		// with related media if it still remained there
		if ($this->videotype == 'uploaded') {
			$queue_object = new izapQueue();
			$queue_object->delete_from_trash($this->guid, true);
			$queue_object->delete($this->guid, true);
		}

		$imagesrc = $this->imagesrc;
		$filesrc = $this->videofile;
		$ofilesrc = $this->orignalfile;
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

		return parent::delete();
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


/**
 * Returns the file name, that ffmpeg can operate
 *
 * @param string $fileName file name
 * @return string all formated file name
 */
function izapGetFriendlyFileName_izap_videos($fileName) {
	$new_name .= izap_get_video_name_prefix();
	$new_name .= time() . '_';
	$new_name .= preg_replace('/[^A-Za-z0-9\.]+/','_',$fileName);
	return $new_name;
}

/**
 * This function checks the supported videos
 *
 * @param string $videoFileName video name with extension
 * @return boolean TRUE if supported else FALSE
 */
function izapSupportedVideos_izap_videos($videoFileName) {
	global $IZAPSETTINGS;
	$supportedFormats = $IZAPSETTINGS->allowedExtensions;
	$extension = izap_get_file_extension($videoFileName);
	if (in_array($extension, $supportedFormats)) {
		return true;
	}

	return false;
}

/**
 * This function checks the max upload limit for files
 *
 * @param integer $fileSize in Mb
 * @return boolean true if everything is ok else false
 */
function izapCheckFileSize_izap_videos($fileSize) {
	$maxFileSize = (int)izapAdminSettings_izap_videos('izapMaxFileSize');
	$maxSizeInBytes = $maxFileSize*1024*1024;

	if ($fileSize > $maxSizeInBytes) {
		return false;
	}

	return true;
}

/**
 * Changes the height and width of the video player
 *
 * @param integer $newHeight height
 * @param integer $newWidth width
 * @param string $object video player
 * @return HTML video player
 */
function izapGetReplacedHeightWidth_izap_videos($newHeight, $newWidth, $object) {
	$videodiv = preg_replace('/width=["\']\d+["\']/', 'width="' . $newWidth . '"', $object);
	$videodiv = preg_replace('/width:\d+/', 'width:'.$newWidth, $videodiv);
	$videodiv = preg_replace('/height=["\']\d+["\']/', 'height="' . $newHeight . '"', $videodiv);
	$videodiv = preg_replace('/height:\d+/', 'height:'.$newHeight, $videodiv);

	return $videodiv;
}

function izap_get_file_extension($filename) {
	if (empty($filename)) {
		return false;
	}

	return strtolower(end(explode('.', $filename)));
}

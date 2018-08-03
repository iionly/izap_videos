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

class IzapConvert {
	private $invideo;
	private $outvideo;
	private $outimage;
	private $imagepreview;
	private $values = [];

	public $format = 'mp4';

	public function __construct($in = '') {
		$this->invideo = $in;
		$extension_length = strlen(izap_get_file_extension($this->invideo));
		$outputPath = substr($this->invideo, 0, '-' . ($extension_length + 1));
		$this->outvideo =  $outputPath . '_c.' . $this->format;
		$this->outimage = $outputPath . '_i.png';
		$this->imagepreview = $outputPath.'_p.png';
	}

	public function izap_video_convert() {
		$videoCommand = izapGetFfmpegVideoConvertCommand_izap_videos();
		$videoCommand = str_replace('[inputVideoPath]', $this->invideo, $videoCommand);
		$videoCommand = str_replace('[outputVideoPath]', $this->outvideo, $videoCommand);

		exec($videoCommand, $arr, $ret);

		if (!$ret == 0) {
			$return = [];
			$return['error'] = 1;
			$return['message'] = end($arr);
			$return['completeMessage'] = implode(' ', $arr);

			return $return;
		}

		return end(explode('/', $this->outvideo));
	}

	public function photo() {
		$videoThumb = izapGetFfmpegVideoImageCommand_izap_videos();
		$videoThumb = str_replace('[inputVideoPath]', $this->invideo, $videoThumb);
		$videoThumb = str_replace('[outputImage]', $this->outimage, $videoThumb);
		// run command to take snapshot
		exec($videoThumb, $out2, $ret2);

		if (!$ret2 == 0) {
			return false;
		}
		return $this->outimage;
	}

	public function getValues($image_only = false) {

		if (!$image_only) { // if we want the full video values
			$this->values['origname'] = time() . '_' . end(explode('/', $this->invideo));
			$this->values['origcontent'] = file_get_contents($this->invideo);
			$this->values['filename'] = time() . '_' . end(explode('/', $this->outvideo));
			$this->values['filecontent'] = file_get_contents($this->outvideo);
			if ($this->values['filecontent'] != '') {
				@unlink($this->invideo);
				@unlink($this->outvideo);
			}
		} else {
			// if only image is needed
			$this->values['imagename'] = time() . '_' . end(explode('/', $this->outimage));
			$this->values['preview'] = time() . '_' . end(explode('/', $this->imagepreview));
			$this->values['imagecontent'] = file_get_contents($this->outimage);
			@unlink($this->outimage);
		}
		return $this->values;
	}

	public function getValuesForAPI() {
		$this->values['orignal_video'] = $this->invideo;
		$this->values['converted_video'] = $this->outvideo;
		$this->values['video_thumb'] = $this->outimage;

		return $this->values;
	}
}

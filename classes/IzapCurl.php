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

class IzapCurl {

	var $m_caseless;
	var $m_handle;
	var $m_header;
	var $m_options;
	var $m_status;
	var $m_followed;

	public function __construct($theURL = null) {
		if (!function_exists('curl_init')) {
			trigger_error('PHP was not built with --with-curl, rebuild PHP to use the curl class.', E_USER_ERROR);
		}

		$this->m_handle = curl_init();

		$this->m_caseless = null;
		$this->m_header = null;
		$this->m_options = null;
		$this->m_status = null;
		$this->m_followed = null;

		if (!empty($theURL)) {
			$this->setopt(CURLOPT_URL, $theURL);
		}
		$this->setopt(CURLOPT_HEADER, false);
		$this->setopt(CURLOPT_RETURNTRANSFER, true);
	}

	function close() {
		curl_close($this->m_handle);
		$this->m_handle = null;
	}

	function exec() {
		$theReturnValue = curl_exec($this->m_handle);

		$this->m_status = curl_getinfo($this->m_handle);
		$this->m_status['errno'] = curl_errno($this->m_handle);
		$this->m_status['error'] = curl_error($this->m_handle);
		$this->m_header = null;
		if ($this->m_status['errno']) {
			return '';
		}

		if ($this->getOption(CURLOPT_HEADER)) {

			$this->m_followed = [];
			$rv = $theReturnValue;

			while (count($this->m_followed) <= $this->m_status['redirect_count']) {
				$theArray = preg_split("/(\r\n){2,2}/", $rv, 2);

				$this->m_followed[] = $theArray[0];

				$rv = $theArray[1];
			}

			$this->parseHeader($theArray[0]);

			return $theArray[1];
		} else {
			return $theReturnValue;
		}
	}

	function getHeader($theHeader = null) {
		if (empty($this->m_header)) {
			return false;
		}

		if (empty($theHeader)) {
			return $this->m_header;
		} else {
			$theHeader = strtoupper($theHeader);
			if (isset($this->m_caseless[$theHeader])) {
				return $this->m_header[$this->m_caseless[$theHeader]];
			} else {
				return false;
			}
		}
	}

	function getOption($theOption) {
		if (isset($this->m_options[$theOption])) {
			return $this->m_options[$theOption];
		}

		return null;
	}

	function hasError() {
		if (isset($this->m_status['error'])) {
			return (empty($this->m_status['error']) ? false : $this->m_status['error']);
		} else {
			return false;
		}
	}

	function parseHeader($theHeader) {
		$this->m_caseless = [];

		$theArray = preg_split("/(\r\n)+/", $theHeader);
		if (preg_match('/^HTTP/', $theArray[0])) {
			$theArray = array_slice($theArray, 1);
		}

		foreach ($theArray as $theHeaderString) {
			$theHeaderStringArray = preg_split("/\s*:\s*/", $theHeaderString, 2);

			$theCaselessTag = strtoupper($theHeaderStringArray[0]);

			if (!isset($this->m_caseless[$theCaselessTag])) {
				$this->m_caseless[$theCaselessTag] = $theHeaderStringArray[0];
			}

			$this->m_header[$this->m_caseless[$theCaselessTag]][] = $theHeaderStringArray[1];
		}
	}

	function getStatus($theField = null) {
		if (empty($theField)) {
			return $this->m_status;
		} else {
			if (isset($this->m_status[$theField])) {
				return $this->m_status[$theField];
			} else {
				return false;
			}
		}
	}

	function setopt($theOption, $theValue) {
		curl_setopt($this->m_handle, $theOption, $theValue);
		$this->m_options[$theOption] = $theValue;
	}

	function &fromPostString(&$thePostString) {
		$return = [];
		$fields = explode('&', $thePostString);
		foreach($fields as $aField) {
			$xxx = explode('=', $aField);
			$return[$xxx[0]] = urldecode($xxx[1]);
		}

		return $return;
	}

	function &asPostString(&$theData, $theName = null) {
		$thePostString = '';
		$thePrefix = $theName;

		if (isset($theData) && is_array($theData)) {
			foreach ($theData as $theKey => $theValue) {
				if ($thePrefix === null) {
					$thePostString .= '&' . curl::asPostString($theValue, $theKey);
				} else {
					$thePostString .= '&' . curl::asPostString($theValue, $thePrefix . '[' . $theKey . ']');
				}
			}
		} else {
			$thePostString .= '&' . urlencode((string)$thePrefix) . '=' . urlencode($theData);
		}

		$xxx =& substr($thePostString, 1);

		return $xxx;
	}

	function getFollowedHeaders() {
		$theHeaders = [];
		if ($this->m_followed) {
			foreach ($this->m_followed as $aHeader) {
				$theHeaders[] = explode("\r\n", $aHeader);
			}
			return $theHeaders;
		}

		return $theHeaders;
	}
}

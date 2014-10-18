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

class curl {
	var $m_caseless;
	var $m_handle;
	var $m_header;
	var $m_options;
	var $m_status;
	var $m_followed;

	function curl($theURL=null) {
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

			$this->m_followed = array();
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

	function getHeader($theHeader=null) {
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
		$this->m_caseless = array();

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

	function getStatus($theField=null) {
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
		$return = array();
		$fields = explode('&', $thePostString);
		foreach($fields as $aField) {
			$xxx = explode('=', $aField);
			$return[$xxx[0]] = urldecode($xxx[1]);
		}

		return $return;
	}

	function &asPostString(&$theData, $theName=null) {
		$thePostString = '';
		$thePrefix = $theName;

		if (is_array($theData)) {
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
		$theHeaders = array();
		if ($this->m_followed) {
			foreach ($this->m_followed as $aHeader) {
				$theHeaders[] = explode("\r\n", $aHeader);
			}
			return $theHeaders;
		}

		return $theHeaders;
	}
}


class xml2array {
	function xml2array($xml) {
		if (file_exists($xml)) {
			$xml = file_get_contents($xml);
		}
		if (is_string($xml)) {
			$xml = domxml_open_mem($xml);
			$this->root_element = $xml->document_element();
		}
		if (is_object($xml) && $xml->node_type() == XML_DOCUMENT_NODE) {
			$this->root_element = $xml->document_element();
			return true;
		}

		if (is_object($xml) && $xml->node_type() == XML_ELEMENT_NODE) {
			$this->root_element = $xml;
			return true;
		}

		return false;
	}

	function _recNode2Array($domnode) {
		if ($domnode->node_type() == XML_ELEMENT_NODE) {

			$childs = $domnode->child_nodes();
			foreach($childs as $child) {
				if ($child->node_type() == XML_ELEMENT_NODE) {
					$subnode = false;
					$prefix = ($child->prefix()) ? $child->prefix() . ':' : '';

					// try to check for multisubnodes
					foreach ($childs as $testnode) {
						if (is_object($testnode)) {
							if ($child->node_name() == $testnode->node_name() && $child != $testnode) {
								$subnode = true;
							}
						}
					}

					if (is_array($result[$prefix . $child->node_name()])) {
						$subnode = true;
					}

					if ($subnode == true) {
						$result[$prefix . $child->node_name()][] = $this->_recNode2Array($child);
					} else {
						$result[$prefix . $child->node_name()] = $this->_recNode2Array($child);
					}
				}
			}

			if (!is_array($result)) {
				$result['#text'] = html_entity_decode(htmlentities($domnode->get_content(), ENT_COMPAT, 'UTF-8'), ENT_COMPAT,'ISO-8859-15');
			}

			if ($domnode->has_attributes()) {
				foreach ($domnode->attributes() as $attrib) {
					$prefix = ($attrib->prefix()) ? $attrib->prefix() . ':' : '';
					$result["@".$prefix . $attrib->name()] = $attrib->value();
				}
			}
		return $result;
		}
	}

	function getResult() {
		if ($resultDomNode = $this->root_element) {
			$array_result[$resultDomNode->tagname()] = $this->_recNode2Array($resultDomNode);
			return $array_result;
		} else {
			return false;
		}
	}

	function getEncoding() {
		preg_match("~\<\?xml.*encoding=[\"\'](.*)[\"\'].*\?\>~i",$this->xml_string,$matches);
		return ($matches[1])?$matches[1]:"";
	}

	function getNamespaces() {
		preg_match_all("~[[:space:]]xmlns:([[:alnum:]]*)=[\"\'](.*?)[\"\']~i",$this->xml_string,$matches,PREG_SET_ORDER);
		foreach( $matches as $match ) {
			$result[ $match[1] ] = $match[2];
		}
		return $result;
	}
}


class btext {

	function extract($string, $ot, $ct) {

		$string = trim($string);
		$start = intval(strpos($string, $ot) + strlen($ot));

		$mytext = substr($string, $start, intval(strpos($string, $ct) - $start));

		return $mytext;
	}

	function xml2array($contents, $get_attributes=1, $priority = 'tag') {
		if (!$contents) {
			return array();
		}

		if (!function_exists('xml_parser_create')) {
			return array();
		}
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);

		if (!$xml_values) {
			return; // Hmm...
		}

		// Initializations
		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();

		$current = &$xml_array; // Reference

		// Go through the tags
		$repeated_tag_index = array(); // Multiple tags with same name will be turned into an array
		foreach($xml_values as $data) {
			unset($attributes, $value); // Remove existing values, or there will be trouble

			// This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array)
			extract($data); // We could use the array by itself, but this cooler

			$result = array();
			$attributes_data = array();

			if (isset($value)) {
				if ($priority == 'tag') {
					$result = $value;
				} else {
					$result['value'] = $value; // Put the value in a assoc array if we are in the 'Attribute' mode
				}
			}

			// Set the attributes too
			if (isset($attributes) and $get_attributes) {
				foreach($attributes as $attr => $val) {
					if ($priority == 'tag') {
						$attributes_data[$attr] = $val;
					} else {
						$result['attr'][$attr] = $val; // Set all the attributes in a array called 'attr'
					}
				}
			}

			// See tag status and do what is necessary
			if ($type == "open") { // The starting of the tag '<tag>'
				$parent[$level-1] = &$current;
				if (!is_array($current) or (!in_array($tag, array_keys($current)))) { // Insert New tag
					$current[$tag] = $result;
					if ($attributes_data) {
						$current[$tag. '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag.'_'.$level] = 1;

					$current = &$current[$tag];

				} else { // There was another element with the same tag name

					if (isset($current[$tag][0])) { // If there is a 0th element it is already an array
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
						$repeated_tag_index[$tag.'_'.$level]++;
					} else { // This section will make the value an array if multiple tags with the same name appear together
						$current[$tag] = array($current[$tag], $result); // This will combine the existing item and the new item together to make an array
						$repeated_tag_index[$tag.'_'.$level] = 2;

						if (isset($current[$tag.'_attr'])) { // The attribute of the last(0th) tag must be moved as well
							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}
					}
					$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
					$current = &$current[$tag][$last_item_index];
				}

			} elseif ($type == "complete") { // Tags that end in 1 line '<tag />'
				// See if the key is already taken
				if (!isset($current[$tag])) { // New Key
					$current[$tag] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					if ($priority == 'tag' and $attributes_data) {
						$current[$tag. '_attr'] = $attributes_data;
					}
				} else { // If taken, put all things inside a list(array)
					if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...
						// ...push the new element into that array
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

						if ($priority == 'tag' and $get_attributes and $attributes_data) {
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag.'_'.$level]++;
					} else { // If it is not an array...
						$current[$tag] = array($current[$tag],$result); // ...make it an array using using the existing value and the new value
						$repeated_tag_index[$tag.'_'.$level] = 1;
						if ($priority == 'tag' and $get_attributes) {
							if(isset($current[$tag.'_attr'])) { // The attribute of the last(0th) tag must be moved as well
								$current[$tag]['0_attr'] = $current[$tag.'_attr'];
								unset($current[$tag.'_attr']);
							}

							if ($attributes_data) {
								$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag.'_'.$level]++; // 0 and 1 index is already taken
					}
				}
			} elseif ($type == 'close') { // End of tag '</tag>'
				$current = &$parent[$level-1];
			}
		}

		return($xml_array);
	}
}
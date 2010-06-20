<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Timo Schmidt timo-schmidt@gmx.net
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_bridgexmlrendering_pi1 extends tslib_pibase {

	//pre output pipe function
	function pre($content,$conf){
		//handlepre processing for future preprocessing ;)
		//todo: implement hook
		//$out = "<!-- ";
		$out = "";
		return $out;
	}

	//post output pipe function
	function post($content,$conf,$out){
		//todo: implement hook
		//$out .= " //-->";
		if ($conf['stdWrap.']) {
			$out = $this->cObj->stdWrap($out, $conf['stdWrap.']);
		}

		return $out;
	}

	/**
	 * Renderfunction for Textelements with pictures
	 */
	function textpic($content,$conf){
		return $this->post($content, $conf,$this->_textpic($content,$conf,$this->pre($content,$conf)));
	}

	/**
	 * Renderfunction for Textelemnts
	 */
	function text($content,$conf){
		return $this->post($content, $conf,$this->_text($content,$conf,$this->pre($content,$conf)));
	}

	function image($content,$conf){
		return $this->post($content, $conf,$this->_image($content,$conf,$this->pre($content,$conf)));
	}

	function table($content,$conf){
		return $this->post($content, $conf,$this->_table($content,$conf,$this->pre($content,$conf)));
	}

	function _image($content,$conf,$out){
		//build shortcut pointer
		$p_cObj = &$this->cObj;
		$p_data = &$p_cObj->data;

		//build imagelist
		$images = explode(",",$p_data['image']);
		$path = "uploads/pics/";

		foreach($images as $image){
			$imagelist .= $p_cObj->stdWrap($path.$image, $conf['wraps.']['imageWrap.']);
		}

		$out .=	$p_cObj->stdWrap($imagelist,$conf['wraps.']['imageListWrap.']);

		return $out;
	}


	function _table($content,$conf,$out){
		//build shortcut pointer
		$p_cObj = &$this->cObj;
		$p_data = &$p_cObj->data;

		$data_seperator = "|";
		$line_seperator = "\n";
		$lines = explode($line_seperator,$p_data['bodytext']);
		foreach($lines as $key => $line){
			//cut the last |
			$line = substr($line,0,strrpos($line,$data_seperator));
			$lines[$key] = explode($data_seperator, $line);
		}

		foreach($lines as $line){
			$row = '';
			foreach($line as $cell){
				$row .= $p_cObj->stdWrap($cell, $conf['wraps.']['tabledataWrap.']);
			}
			$table .= $p_cObj->stdWrap($row, $conf['wraps.']['tablelineWrap.']);
		}
		$p_data['bodytext'] = $p_cObj->stdWrap($table, $conf['wraps.']['tableWrap.']);

		$out = $this->_text($content,$conf,$out);
		return $out;
	}

	/**
	 *
	 */
	function _text($content,$conf,$out){
		//build shortcut pointer
		$p_cObj = &$this->cObj;
		$p_data = &$p_cObj->data;

		//unparse rte links
		$parser = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		$p_data['bodytext'] = $parser->TS_links_rte($p_data['bodytext']);

		//apply properties on the bodytext
		//bold
		$props = $p_data['text_properties'];

		//uppercase
		if($props & 8) $p_data['bodytext'] = strtoupper($p_data['bodytext']);
		//underline
		if($props & 4) $p_data['bodytext'] = $p_cObj->stdWrap($p_data['bodytext'], $conf['wraps.']['underlineWrap.']);
		//italic
		if($props & 2) $p_data['bodytext'] = $p_cObj->stdWrap($p_data['bodytext'], $conf['wraps.']['italicWrap.']);
		//bold
		if($props & 1) $p_data['bodytext'] = $p_cObj->stdWrap($p_data['bodytext'], $conf['wraps.']['boldWrap.']);

		//handle wraps
		$out .= $p_cObj->stdWrap($p_data['header'], $conf['wraps.']['headerWrap.']);
		$out .= $p_cObj->stdWrap($p_data['bodytext'], $conf['wraps.']['bodyWrap.']);

		return $out;
	}

	/**
	 *
	 */
	function _textpic($content,$conf,$out){
		//build shortcut pointer
		$p_cObj = &$this->cObj;
		$p_data = &$p_cObj->data;

		//build output
		$out = $this->_text($content,$conf,$out);
		$out = $this->_image($content,$conf,$out);

		//return postprocessed output
		return $out;
	}

	function debug($content,$conf){
		print("<pre>");
		print_r($content);
		print("</pre>");

		print("<pre>");
		print_r($conf);
		print("</pre>");

		print("<pre>");
		print_r($this->cObj);
		print("</pre>");
	}


	function getData($string,$fieldArray)	{
		echo 'test';

	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xmlrendering/pi1/class.tx_xmlrendering_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xmlrendering/pi1/class.tx_xmlrendering_pi1.php']);
}

?>
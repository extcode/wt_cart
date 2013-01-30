<?php

/* * *************************************************************
*  Copyright notice
*
*  (c) 2010-2012 - wt_cart Development Team <info@wt-cart.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
* ************************************************************* */

define('TYPO3_DLOG', $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']);

require_once(t3lib_extMgm::extPath('wt_cart') . 'model/cart.php');

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_render.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_dynamicmarkers.php');

/**
* plugin 'Minicart' for the 'wt_cart' extension.
*
* @author  wt_cart Development Team <info@wt-cart.com>
* @package TYPO3
* @subpackage  tx_wtcart
* @version 1.4.0
*/
class tx_wtcart_pi3 extends tslib_pibase {

		// make configurations
	public $prefixId = 'tx_wtcart_pi3';
	public $scriptRelPath = 'pi3/class.tx_wtcart_pi3.php';
	public $extKey = 'wt_cart';

	public $tmpl = array();
	public $minicartMarkerArray = array();

	/**
	* the main method of the PlugIn
	*
	* @param string    $content: The PlugIn content
	* @param array   $conf: The PlugIn configuration
	* @return  The content that is displayed on the website
	*/	
	public function main($content, $conf) {
			// make configurations
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$this->conf = array_merge((array) $this->conf, (array) $conf);

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;

			// create new instance for function
		$this->div = t3lib_div::makeInstance('tx_wtcart_div');
		$this->render = t3lib_div::makeInstance('tx_wtcart_render');
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_wtcart_dynamicmarkers', $this->scriptRelPath);

		$this->tmpl['minicart'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['main.']['template']), '###WTCART_MINICART###'); // Load FORM HTML Template
		$this->tmpl['minicart_empty'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['main.']['template']), '###WTCART_MINICART_EMPTY###'); // Load FORM HTML Template
		
			//Read Flexform
		$row=$this->pi_getRecord('tt_content', $this->cObj->data['uid']); 
		$flexformData = t3lib_div::xml2array($row['pi_flexform']);
		$pid = $this->pi_getFFvalue($flexformData, 'pid', 'sDEF');

		$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $pid));
		if (!$cart) {
			$cart = new Cart();
		}

		if ($cart->getCount()) {

			$this->render->renderMiniCart($cart, $this);

			$typolink_conf = array();
			$this->minicartMarkerArray['###MINICART_LINK###']= $this->pi_linkToPage($this->pi_getLL('wtcart_ll_link'), $pid, "", $typolink_conf);
			$this->minicartMarkerArray['###MINICART_LINK_URL###']= $this->pi_getPageLink($pid, "", $typolink_conf);

				// Get html template
			$this->content = $this->cObj->substituteMarkerArrayCached($this->tmpl['minicart'], $this->minicartMarkerArray);
		} else {
				// Get html template
			$this->content = $this->cObj->substituteMarkerArrayCached($this->tmpl['minicart_empty'], null, $this->minicartMarkerArray);
		}

			// Fill dynamic locallang or typoscript markers
		$this->content = $this->dynamicMarkers->main($this->content, $this);
			// Finally clear not filled markers
		$this->content = preg_replace('|###.*?###|i', '', $this->content);
		return $this->pi_wrapInBaseClass($this->content);
	}
}
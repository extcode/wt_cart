<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2014 - wt_cart Development Team <info@wt-cart.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

define('TYPO3_DLOG', $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']);

/**
* plugin 'ClearCart' for the 'wt_cart' extension.
*
* @author  wt_cart Development Team <ext.wtcart@extco.de>
* @package TYPO3
* @subpackage  tx_wtcart
* @version 1.4.0
*/
class tx_wtcart_pi4 extends tslib_pibase {

		// make configurations
	public $prefixId = 'tx_wtcart_pi4';
	public $scriptRelPath = 'pi4/class.tx_wtcart_pi4.php';
	public $extKey = 'wt_cart';

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

		$this->pi_USER_INT_obj = 1;

		//Read Flexform
		/*
			$row=$this->pi_getRecord('tt_content', $this->cObj->data['uid']);
			$flexformData = t3lib_div::xml2array($row['pi_flexform']);
			$pid = $this->pi_getFFvalue($flexformData, 'pid', 'sDEF');

			$session = $GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $pid);
		*/

		$utilityCart = t3lib_div::makeInstance('Tx_WtCart_Utility_Cart');
		$utilityCart->removeAllProductsFromSession();

		return '';
	}
}
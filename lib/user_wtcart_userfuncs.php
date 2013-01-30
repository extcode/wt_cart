<?php

/* * *************************************************************
*  Copyright notice
*
*  (c) 2010-2012 wt_cart Development Team <info@wt-cart.com>
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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php'); // file for div functions

/**
 * plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author	wt_cart Development Team <info@wt-cart.com>
 * @package	TYPO3
 * @subpackage	tx_wtcart
 */
class user_wtcart_userfuncs extends tslib_pibase
{

	public $prefixId = 'tx_wtcart_pi1';

	// same as class name
	public $scriptRelPath = 'pi1/class.tx_wtcart_pi1.php';

	// path to any file in pi1 for locallang
	public $extKey = 'wt_cart'; // The extension key.

	/**
	 * number Format for typoscript
	 *
	 * @return	string		formatted number
	 */
	public function user_wtcart_numberFormat($content = '', $conf = array()) {
		global $TSFE;
		$local_cObj = $TSFE->cObj; // cObject

		if (!$content)
		{
			$conf = $conf['userFunc.']; // TS configuration
			$content = $local_cObj->cObjGetSingle($conf['number'], $conf['number.']); // get number
		}

		return number_format(doubleval($content), $conf['decimal'], $conf['dec_point'], $conf['thousands_sep']);
	}

	/**
	 * clear cart
	 *
	 * @return	void
	 */
	public function user_wtcart_clearCart($content = '', $conf = array())
	{
		$div = t3lib_div::makeInstance('tx_wtcart_div'); // Create new instance for div functions
		$div->removeAllProductsFromSession(); // clear cart now
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/lib/user_wtcart_userfuncs.php'])
{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/lib/user_wtcart_userfuncs.php']);
}
?>
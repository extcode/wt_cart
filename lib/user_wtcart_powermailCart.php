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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_dynamicmarkers.php'); // file for dynamicmarker functions

/**
* plugin 'Cart' for the 'powermail' extension.
*
* @author  wt_cart Development Team <info@wt-cart.com>
* @package  TYPO3
* @subpackage  user_wtcart_powermailCart
* @version 1.2.1
*/
class user_wtcart_powermailCart extends tslib_pibase {
		// make configurations
	public $prefixId = 'tx_wtcart_pi1';
	public $scriptRelPath = 'pi1/class.tx_wtcart_pi1.php';
	public $extKey = 'wt_cart';

	public $tmpl = array();
	public $outerMarkerArray = array();
	public $subpartMarkerArray = array();

	/**
	* read and return cart from session
	*
	* @return  string    cart content
	* @version 1.2.2
	*/
	public function showCart($content = '', $conf = array()) {
			// make configurations
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;

		if ( $this->cObj == null ) {
			$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		}

		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$this->conf = array_merge_recursive((array) $this->conf, (array) $conf);

		if (is_array($this->conf['main.']['template'])) {
			$this->conf['main.']['template'] = end($this->conf['main.']['template']);
		}
		$this->tmpl['all'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['main.']['template']), '###WTCART_POWERMAIL###');
		$this->tmpl['item'] = $this->cObj->getSubpart($this->tmpl['all'], '###ITEM###');
		$this->tmpl['variantitem'] = $this->cObj->getSubpart($this->tmpl['all'], '###VARIANTITEM###');
		$this->tmpl['variantitemall'] = $this->cObj->getSubpart($this->tmpl['variantitem'], '###VARIANTITEMALL###');
		$this->tmpl['variantitemvariant'] = $this->cObj->getSubpart($this->tmpl['variantitem'], '###VARIANTITEMVARIANT###');
		$this->tmpl['additional_all'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['main.']['template']), '###WTCART_ADDITIONAL###');
		$this->tmpl['additional_item'] = $this->cObj->getSubpart($this->tmpl['additional_all'], '###ITEM###');

		$this->div = t3lib_div::makeInstance('tx_wtcart_div'); // Create new instance for div functions
		$this->render = t3lib_div::makeInstance('Tx_WtCart_Utility_Renderer'); // Create new instance for render functions
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_wtcart_dynamicmarkers'); // Create new instance for dynamicmarker function

		// get cart from the session
		$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $this->conf['main.']['pid']));

		// there are products in the session
		if ($cart->getCount() > 0) {
			$subpartArray['###CONTENT###'] = $this->render->renderProductList($cart, $this);

			$this->render->renderOverall($cart, $this);

			$this->render->renderServiceItem($cart, $cart->getShipping(), 'SHIPPING', $this);

			$this->render->renderServiceItem($cart, $cart->getPayment(), 'PAYMENT', $this);

			$this->render->renderServiceItem($cart, $cart->getSpecials(), 'SPECIAL', $this);

			$this->render->renderAdditional($cart, $this);

			$outerArr = array(
				'ordernumber' => $cart->getOrderNumber()
			);
			$GLOBALS['TSFE']->cObj->start($outerArr, $this->conf['db.']['table']);
			$this->outerMarkerArray['###ORDERNUMBER###'] = $GLOBALS['TSFE']->cObj->cObjGetSingle($this->conf['settings.']['powermailCart.']['overall.']['ordernumber'], $this->conf['settings.']['powermailCart.']['overall.']['ordernumber.']);

			// Get html template
			$this->content = $this->cObj->substituteMarkerArrayCached($this->tmpl['all'], $this->outerMarkerArray, $subpartArray);
				// Fill dynamic locallang or typoscript markers
			$this->content = $this->dynamicMarkers->main($this->content, $this);
				// Finally clear not filled markers
			$this->content = preg_replace('|###.*?###|i', '', $this->content);
		} else {
			$this->content = 'Error';
		}

		return $this->content;
	}

	/**
	 * read and return ordernumber from session
	 *
	 * @return  string    cart order number
	 * @version 2.0.0
	 */
	public function showOrderNumber($content = '', $conf = array()) {
		// make configurations
		$this->conf = $conf;

		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$this->conf = array_merge((array) $this->conf, (array) $conf);

		// get cart from the session
		$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $this->conf['main.']['pid']));

		if ($cart->getOrderNumber()) {
			$outerArr = array(
				'ordernumber' => $cart->getOrderNumber()
			);
			$GLOBALS['TSFE']->cObj->start($outerArr, $this->conf['db.']['table']);
			$content = $GLOBALS['TSFE']->cObj->cObjGetSingle($this->conf['settings.']['powermailCart.']['overall.']['ordernumber'], $this->conf['settings.']['powermailCart.']['overall.']['ordernumber.']);
		}

		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/lib/user_wtcart_powermailCart.php'])
{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/lib/user_wtcart_powermailCart.php']);
}
?>

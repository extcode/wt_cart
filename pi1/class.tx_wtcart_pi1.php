<?php

/* * *************************************************************
*  Copyright notice
*
*  (c) 2010-2012 - wt_cart Development Team <info@wt-cart.com>
*  Daniel Lorenz <daniel.lorenz@capcisum-ug.de>
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

require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Cart.php');

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_dynamicmarkers.php');


/**
 * plugin 'Cart to powermail' for the 'wt_cart' extension.
 *
 * @author  wt_cart Development Team <info@wt-cart.com>
 * @package TYPO3
 * @subpackage  tx_wtcart
 * @version 1.2.2
 */
class tx_wtcart_pi1 extends tslib_pibase {

		// make configurations
	public $prefixId = 'tx_wtcart_pi1';
	public $scriptRelPath = 'pi1/class.tx_wtcart_pi1.php';
	public $extKey = 'wt_cart';

	public $gpvar = array();
	public $taxes = array();

	public $tmpl = array();
	public $outerMarkerArray = array();
	public $subpartMarkerArray = array();

	/**
	 * the main method of the PlugIn
	 *
	 * @param string    $content: The PlugIn content
	 * @param array   $conf: The PlugIn configuration
	 * @return  The content that is displayed on the website
	 */
	public function main($content, $conf) {
			// make configurations
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;

			// create new instance for function
		$this->div = t3lib_div::makeInstance('tx_wtcart_div');
		$this->render = t3lib_div::makeInstance('Tx_WtCart_Utility_Renderer');
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_wtcart_dynamicmarkers');

		// parse all taxclasses
		$this->taxes = $this->div->parseTaxes($this);

		// in this version it is not possible mixing prices for products
		$this->gpvar['isNetPrice'] = intval($this->conf['main.']['isNetCart']) == 0 ? FALSE : TRUE;

		// parse all shippings
		$shippings = $this->div->parseServices('Shipping', $this);

		// parse all payments
		$payments = $this->div->parseServices('Payment', $this);

		// parse all specials
		$specials = $this->div->parseServices('Special', $this);

		/* Cart - Section */

			// remove product from session
		if (isset($this->piVars['clear'])) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'wt_cart_' . $this->conf['main.']['pid'], array());
			$GLOBALS['TSFE']->storeSessionData();
		}

			// read cart from session
		$session = $GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $this->conf['main.']['pid']);
		if ($session) {
			$cart = unserialize($session);
		} else {
			$this->isNetCart = intval($this->conf['main.']['isNetCart']) == 0 ? FALSE : TRUE;

			$cart = new Tx_WtCart_Domain_Model_Cart($this->isNetCart);
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartAfterLoad']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartAfterLoad'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'cart' => &$cart
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

			//read variables
		$this->div->getGPVars($this);

		// in this version it is not possible mixing prices for products
		$this->gpvar['isNetPrice'] = $cart->getIsNetCart();

		if (TYPO3_DLOG) {
			t3lib_div::devLog('pivars', $this->extKey, 0, $this->piVars);
			t3lib_div::devLog('gpvars', $this->extKey, 0, $this->gpvar);
		}

		if (!$this->gpvar['multi']) {
				// if content id (cid) is given, then product added from plugin
			if ($this->gpvar['cid']) {
					// parse data from flexform
				$this->parseDataFromFlexform();
			} elseif ($this->gpvar['puid']) {
					// product added by own form
				if (!$this->gpvar['ownForm']) {
					$this->div->getProductDetails($this->gpvar, $this);
				} else {
					$this->parseDataFromOwnForm();
				}
			}
		}

			// if no qty given set qty to 1
		if ($this->gpvar['qty'] === 0) {
			$this->gpvar['qty'] = 1;
		}
			// change quantity of products
		if (isset($this->piVars['qty'])) {
			$cart->changeProductsQty($this->piVars['qty']);
		}

			// remove product from session
		if (isset($this->piVars['del'])) {
			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartBeforeDeleteProduct']) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartBeforeDeleteProduct'] as $funcRef) {
					if ($funcRef) {
						$params = array(
							'cart' => &$cart,
							'del' => $this->piVars['del']
						);

						t3lib_div::callUserFunction($funcRef, $params, $this);
					}
				}
			}

			$cart->removeProducts($this->piVars['del']);

			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartAfterDeleteProduct']) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartAfterDeleteProduct'] as $funcRef) {
					if ($funcRef) {
						$params = array(
							'cart' => &$cart,
							'del' => $this->piVars['del']
						);

						t3lib_div::callUserFunction($funcRef, $params, $this);
					}
				}
			}
		}

		if (isset($this->piVars['update_from_cart'])) {
				// change shipping
			if (isset($this->piVars['shipping'])) {
				$cart->changeShipping($shippings[$this->piVars['shipping']]);
			}

				// change payment
			if (isset($this->piVars['payment'])) {
				$cart->changePayment($payments[$this->piVars['payment']]);
			}

				// change special
			foreach ($specials as $special) {
				if (in_array($special->getId(), array_values($this->piVars['special']))) {
					$cart->addSpecial($special);
				} else {
					$cart->removeSpecial($special);
				}
			}
		}

			// preset shipping, if not defined
		if (!$cart->getShipping()) {
			$cart->setShipping($shippings[$this->conf['shipping.']['preset']]);
		}

			// preset payment, if not defined
		if (!$cart->getPayment()) {
			$cart->setPayment($payments[$this->conf['payment.']['preset']]);
		}

			// create new product
		if ($this->gpvar['multi']) {
			foreach ($this->gpvar['multi'] as $single) {
				$tmp = $this->gpvar;
				$this->gpvar = $single;
				$this->gpvar['isNetPrice'] = $cart->getIsNetCart();

				if (TYPO3_DLOG) {
					t3lib_div::devLog('multiple_before', $this->extKey, 0, $this->gpvar);
				}

				$this->parseDataToProductToCart($cart);

				if (TYPO3_DLOG) {
					t3lib_div::devLog('multiple_after', $this->extKey, 0, $this->gpvar);
				}

				$this->gpvar = $tmp;
			}
		} else {
			if ($this->gpvar['puid']) {
				$newProduct = $this->div->createProduct($this);

				$newProduct->setServiceAttribute1($this->gpvar['service_attribute_1']);
				$newProduct->setServiceAttribute2($this->gpvar['service_attribute_2']);
				$newProduct->setServiceAttribute3($this->gpvar['service_attribute_3']);

				$newProduct->setAdditionalArray($this->gpvar['additional']);

				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeProductBeforeAddToCart']) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeProductBeforeAddToCart'] as $funcRef) {
						if ($funcRef) {
							$params = array(
								'newProduct' => &$newProduct
							);

							t3lib_div::callUserFunction($funcRef, $params, $this);
						}
					}
				}

				$cart->addProduct($newProduct);
			}
		}

		$cart->debug();

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeServicesBeforeSave']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeServicesBeforeSave'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'shippings' => &$shippings,
						'payments' => &$payments,
						'specials' => &$specials,
						'cart' => &$cart
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartBeforeSave']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeCartBeforeSave'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'cart' => &$cart
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

			// save cart to session
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'wt_cart_' . $this->conf['main.']['pid'], serialize($cart));
		$GLOBALS['TSFE']->storeSessionData();

		/* Rendering - Section */

			// load html-templates
		$this->render->loadTemplate($this);

			// there are products in the session
		if ($cart->getCount() > 0) {
			$this->subpartMarkerArray['###CONTENT###'] = $this->render->renderProductList($cart, $this);
			$this->subpartMarkerArray['###CONTENT###'] .= '<input type="hidden" name="tx_wtcart_pi1[update_from_cart]" value="1">';

			$this->render->renderOverall($cart, $this);

			$this->render->renderServiceList($cart, $shippings, $cart->getShipping(), $this);

			$this->render->renderServiceList($cart, $payments, $cart->getPayment(), $this);

			$this->render->renderServiceList($cart, $specials, $cart->getSpecials(), $this);

			$this->render->renderOverall($cart, $this);

			$this->render->renderServiceItem($cart, $cart->getShipping(), $this);

			$this->render->renderServiceItem($cart, $cart->getPayment(), $this);

			$this->render->renderServiceItem($cart, $cart->getSpecials(), $this);

			$this->render->renderClearCartLink($this);

			$this->render->renderBackPageLink($this);

			$this->render->renderAdditional($cart, $this);
		} else {
			$this->render->renderEmptyCart($this);
		}

			// Get html template
		$this->content = $this->cObj->substituteMarkerArrayCached($this->tmpl['all'], $this->outerMarkerArray, $this->subpartMarkerArray);
			// Fill dynamic locallang or typoscript markers
		$this->content = $this->dynamicMarkers->main($this->content, $this);
			// Finally clear not filled markers
		$this->content = preg_replace('|###.*?###|i', '', $this->content);
		return $this->pi_wrapInBaseClass($this->content);
	}

	/**
	 * Gets the price for a given type ('shipping', 'payment') method on the current cart
	 *
	 * @param string $type
	 * @param int $optionId
	 * @return string
	 */
	private function getPriceForOption($type, $optionId) {
		$optionIds = $this->conf[$type . '.']['options.'][$optionId . '.'];

		$freeFrom = $optionIds['free_from'];
		$freeUntil = $optionIds['free_until'];

		if ((isset($freeFrom) && (floatval($freeFrom) <= $this->cart['grossNoService'])) ||
				(isset($freeUntil) && (floatval($freeUntil) >= $this->cart['grossNoService']))
		) {
			return '0.00';
		}

		$filterArr = array(
			'by_price' => $this->cart['grossNoService'],
			'by_quantity' => $this->cart['count'],
			'by_service_attribute_1_sum' => $this->cart['serviceAttribute'][1]['sum'],
			'by_service_attribute_1_max' => $this->cart['serviceAttribute'][1]['max'],
			'by_service_attribute_2_sum' => $this->cart['serviceAttribute'][2]['sum'],
			'by_service_attribute_2_max' => $this->cart['serviceAttribute'][2]['max'],
			'by_service_attribute_3_sum' => $this->cart['serviceAttribute'][3]['sum'],
			'by_service_attribute_3_max' => $this->cart['serviceAttribute'][3]['max']
		);

		if (array_key_exists($optionIds['extra'], $filterArr)) {
			foreach ($optionIds['extra.'] as $extra) {
				if (floatval($extra['value']) <= $filterArr[$optionIds['extra']]) {
					$price = $extra['extra'];
				} else {
					break;
				}
			}
		} else {
			switch ($optionIds['extra']) {
				case 'each':
					$price = floatval($optionIds['extra.']['1.']['extra']) * $this->cart['count'];
					break;
				default:
					$price = $optionIds['extra'];
			}
		}

		return $price;
	}

	/**
	 * Gets the optionId for a given type ('shipping', 'payment') method on the current cart and checks the
	 * availability. If available, return is 0. If not available the given fallback or preset will returns.
	 *
	 * @param string $type
	 * @param int $optionId
	 * @return int
	 */
	private function checkOptionIsNotAvailable($type, $optionId) {
		if ((isset($this->conf[$type . '.']['options.'][$optionId . '.']['available_from']) && (round(floatval($this->conf[$type . '.']['options.'][$optionId . '.']['available_from']), 2) > round($this->cart['grossNoService'], 2))) || (isset($this->conf[$type . '.']['options.'][$optionId . '.']['available_until']) && (round(floatval($this->conf[$type . '.']['options.'][$optionId . '.']['available_until']), 2) < round($this->cart['grossNoService'], 2)))) {
				// check: fallback is given
			if (isset($this->conf[$type . '.']['options.'][$optionId . '.']['fallback'])) {
				$fallback = $this->conf[$type . '.']['options.'][$optionId . '.']['fallback'];
					// check: fallback is defined; the availability of fallback will not tested yet
				if (isset($this->conf[$type . '.']['options.'][$fallback . '.'])) {
					$newoptionId = intval($fallback);
				} else {
					$newoptionId = intval($this->conf[$type . '.']['preset']);
				}
			} else {
				$newoptionId = intval($this->conf[$type . '.']['preset']);
			}
			return $newoptionId;
		}

		return 0;
	}

	/**
	 * @return null
	 */
	private function parseDataFromFlexform() {
		$row = $this->pi_getRecord('tt_content', $this->gpvar['cid']);
		$flexformData = t3lib_div::xml2array($row['pi_flexform']);

		$gpvarArr = array('puid', 'sku', 'title', 'price', 'taxclass');
		foreach ($gpvarArr as $gpvarVal) {
			$this->gpvar[$gpvarVal] = $this->pi_getFFvalue($flexformData, $gpvarVal, 'sDEF');
		}

		$this->gpvar['qty'] = intval($this->cObj->cObjGetSingle($this->conf['settings.']['qty'], $this->conf['settings.']['qty.']));

		if (TYPO3_DLOG) {
			t3lib_div::devLog('gpvars after getRecord', $this->extKey, 0, $this->gpvar);
		}

		$attributes = explode("\n", $this->pi_getFFvalue($flexformData, 'attributes', 'sDEF'));

		foreach ($attributes as $line) {
			list($key, $value) = explode('==', $line, 2);
			switch ($key) {
				case 'service_attribute_1':
					$this->gpvar['service_attribute_1'] = floatval($value);
					break;
				case 'service_attribute_2':
					$this->gpvar['service_attribute_2'] = floatval($value);
					break;
				case 'service_attribute_3':
					$this->gpvar['service_attribute_3'] = floatval($value);
					break;
				default:
			}
		}

		return NULL;
	}

	/**
	 * @return null
	 */
	private function parseDataFromOwnForm() {
		$gpvarArr = array(
			'sku', 'title', 'price', 'qty', 'taxclass',
			'service_attribute_1', 'service_attribute_2', 'service_attribute_3'
		);
		foreach ($gpvarArr as $gpvarVal) {
			switch ($gpvarVal) {
				case 'qty':
					$this->gpvar[$gpvarVal] =
							intval($this->cObj->cObjGetSingle($this->conf['settings.'][$gpvarVal], $this->conf['settings.'][$gpvarVal . '.']));
					break;
				case 'service_attribute_1':
				case 'service_attribute_2':
				case 'service_attribute_3':
					$this->gpvar[$gpvarVal] =
							floatval($this->cObj->cObjGetSingle($this->conf['settings.'][$gpvarVal], $this->conf['settings.'][$gpvarVal . '.']));
					break;
				default:
					$this->gpvar[$gpvarVal] =
							$this->cObj->cObjGetSingle($this->conf['settings.'][$gpvarVal], $this->conf['settings.'][$gpvarVal . '.']);
					break;
			}
		}

		return NULL;
	}

	private function parseDataToProductToCart(&$cart) {
		if (intval($this->gpvar['qty']) > 0 ) {

			$this->div->getProductDetails($this->gpvar, $this);
			// create new product
			if ($this->gpvar['puid']) {
				$newProduct = $this->div->createProduct($this);

				$newProduct->setServiceAttribute1($this->gpvar['service_attribute_1']);
				$newProduct->setServiceAttribute2($this->gpvar['service_attribute_2']);
				$newProduct->setServiceAttribute3($this->gpvar['service_attribute_3']);

				$newProduct->setAdditionalArray($this->gpvar['additional']);

				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeProductBeforeAddToCart']) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeProductBeforeAddToCart'] as $funcRef) {
						if ($funcRef) {
							$params = array(
								'newProduct' => &$newProduct
							);

							t3lib_div::callUserFunction($funcRef, $params, $this);
						}
					}
				}

				$cart->addProduct($newProduct);
			} else {
				return 0;
			}

			return 0;
		}

		return 1;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/pi1/class.tx_wtcart_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/pi1/class.tx_wtcart_pi1.php']);
}
?>
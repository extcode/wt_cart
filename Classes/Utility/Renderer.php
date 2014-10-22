<?php

/* * *************************************************************
*  Copyright notice
*
*  (c) 2011-2012 - wt_cart Development Team <info@wt-cart.com>
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

/**
* Plugin 'Cart' for the 'wt_cart' extension.
*
* @author	wt_cart Development Team <info@wt-cart.com>
* @package	TYPO3
* @subpackage	tx_wtcart
* @version	1.4.0
*/
class Tx_WtCart_Utility_Renderer {
	public $prefixId = 'tx_wtcart_pi1';
	public $scriptRelPath = 'pi1/class.tx_wtcart_pi1.php';
	public $extKey = 'wt_cart';

	/**
	 * powermailAction
	 *
	 * @var string
	 */
	protected $powermailAction = '';

	/**
	 * @param $obj
	 * @return null
	 */
	public function loadTemplate(&$obj) {

		$powermailValues = t3lib_div::_GET('tx_powermail_pi1');
		if ( $powermailValues && $powermailValues['action'] ) {
			$this->powermailAction = $powermailValues['action'];
		}

		$obj->tmpl['all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART###');

		if ( $this->powermailAction == 'confirmation' ) {
			$confirmation_all = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_CONFIRMATION###');
			if ( !empty( $confirmation_all ) ) {
				$obj->tmpl['all'] = $confirmation_all;
			}
		}

		$obj->tmpl['empty'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_EMPTY###');
		$obj->tmpl['item'] = $obj->cObj->getSubpart($obj->tmpl['all'], '###ITEM###');
		$obj->tmpl['variantitem'] = $obj->cObj->getSubpart($obj->tmpl['all'], '###VARIANTITEM###');
		$obj->tmpl['variantitemall'] = $obj->cObj->getSubpart($obj->tmpl['variantitem'], '###VARIANTITEMALL###');
		$obj->tmpl['variantitemvariant'] = $obj->cObj->getSubpart($obj->tmpl['variantitem'], '###VARIANTITEMVARIANT###');

		$obj->tmpl['shipping_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_SHIPPING###');
		$obj->tmpl['shipping_item'] = $obj->cObj->getSubpart($obj->tmpl['shipping_all'], '###ITEM###');

		$obj->tmpl['shipping_condition_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_SHIPPING_CONDITIONS###');
		$obj->tmpl['shipping_condition_item'] = $obj->cObj->getSubpart($obj->tmpl['shipping_condition_all'], '###ITEM###');

		$obj->tmpl['payment_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_PAYMENT###');
		$obj->tmpl['payment_item'] = $obj->cObj->getSubpart($obj->tmpl['payment_all'], '###ITEM###');

		$obj->tmpl['payment_condition_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_PAYMENT_CONDITIONS###');
		$obj->tmpl['payment_condition_item'] = $obj->cObj->getSubpart($obj->tmpl['payment_condition_all'], '###ITEM###');

		$obj->tmpl['special_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_SPECIAL###');
		$obj->tmpl['special_item'] = $obj->cObj->getSubpart($obj->tmpl['special_all'], '###ITEM###');

		$obj->tmpl['special_condition_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_SPECIAL_CONDITIONS###');
		$obj->tmpl['special_condition_item'] = $obj->cObj->getSubpart($obj->tmpl['special_condition_all'], '###ITEM###');

		$obj->tmpl['additional_all'] = $obj->cObj->getSubpart($obj->cObj->fileResource($obj->conf['main.']['template']), '###WTCART_ADDITIONAL###');
		$obj->tmpl['additional_item'] = $obj->cObj->getSubpart($obj->tmpl['additional_all'], '###ITEM###');

		return NULL;
	}

	public function renderClearCartLink(&$obj) {
		$obj->subpartMarkerArray['###CLEARCART###'] = $GLOBALS['TSFE']->cObj->cObjGetSingle($obj->conf['settings.']['fields.']['clear_cart'], $obj->conf['settings.']['fields.']['clear_cart.']);

		return NULL;
	}

	public function renderBackPageLink(&$obj) {
		$session = $GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $this->conf['main.']['pid']);

		$obj->subpartMarkerArray['###BACKPAGELINK###'] = $session;

		return NULL;
	}

	/**
	 * @param $cart
	 * @param $obj
	 * @return string
	 */
	public function renderProductList($cart, &$obj) {
		$content = '';

		foreach ($cart->getProducts() as $product) {
			if ($product->getVariants()) {
				$content .= $this->renderProductItemWithVariants($product, $obj);
			} else {
				$content .= $this->renderProductItem($product, $obj);
			}
		}

		return $content;
	}

	/**
	 * @param $product
	 * @param $obj
	 * @return string
	 */
	public function renderProductItem(&$product, &$obj) {
			// clear marker array to avoid problems with error msg etc.
		unset($markerArray);
		unset($productArr);

		$productArr = $product->toArray();

		if ( $productArr['additional'] ) {
			foreach($productArr['additional'] as $key => $value) {
				$productArr[$key] = $value;
			}
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderProductItem']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderProductItem'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'productArr' => &$productArr,
						'product' => &$product
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		$GLOBALS['TSFE']->cObj->start($productArr, $obj->conf['db.']['table']);

		$fields = (array)$obj->conf['settings.']['fields.'];

		if ($this->powermailAction == 'confirmation') {
			unset( $fields['delete'] );
			unset( $fields['delete.'] );
		}

		foreach ($fields as $key => $value) {
			if (!stristr($key, '.')) {
				if ($key == 'tax') {
					$tsKey = $obj->conf['settings.']['fields.']['taxclass.'][$key];
					$tsConf = $obj->conf['settings.']['fields.']['taxclass.'][$key . '.'];
				} elseif ($key != 'additional') {
					$tsKey = $obj->conf['settings.']['fields.'][$key];
					$tsConf = $obj->conf['settings.']['fields.'][$key . '.'];
				}
				$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
				$markerArray['###' . strtoupper($key) . '###'] = $tsRenderedValue;
			}
		}

		foreach ((array)$obj->conf['settings.']['fields.']['additional.'] as $key => $value) {
			if (!stristr($key, '.')) {
				$tsKey = $obj->conf['settings.']['fields.']['additional.'][$key];
				$tsConf = $obj->conf['settings.']['fields.']['additional.'][$key . '.'];
				$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
				$markerArray['###ADDITIONAL_' . strtoupper($key) . '###'] = $tsRenderedValue;
			}
		}

		$markerArray['###ERROR_MSG###'] = '';

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderProductItem']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderProductItem'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'markerArray' => &$markerArray,
						'product' => &$product
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		return $obj->cObj->substituteMarkerArrayCached($obj->tmpl['item'], $markerArray);
	}

	/**
	 * @param $product
	 * @param $obj
	 * @return string
	 */
	public function renderProductItemWithVariants(&$product, &$obj) {
			// clear marker array to avoid problems with error msg etc.
		unset($markerArray);
		unset($productArr);

		$productArr = $product->toArray();

		foreach($productArr['additional'] as $key => $value) {
			$productArr[$key] = $value;
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderProductItemWithVariants']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderProductItemWithVariants'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'productArr' => &$productArr,
						'product' => &$product
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		$GLOBALS['TSFE']->cObj->start($productArr, $obj->conf['db.']['table']);

		foreach ((array)$obj->conf['settings.']['fields.'] as $key => $value) {
			if (!stristr($key, '.')) {
				if ($key == 'tax') {
					$tsKey = $obj->conf['settings.']['fields.']['taxclass.'][$key];
					$tsConf = $obj->conf['settings.']['fields.']['taxclass.'][$key . '.'];
				} elseif ($key != 'additional') {
					$tsKey = $obj->conf['settings.']['fields.'][$key];
					$tsConf = $obj->conf['settings.']['fields.'][$key . '.'];
				}
				$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
				$markerArray['###' . strtoupper($key) . '###'] = $tsRenderedValue;
			}
		}

		foreach ((array)$obj->conf['settings.']['fields.']['additional.'] as $key => $value) {
			if (!stristr($key, '.')) {
				$tsKey = $obj->conf['settings.']['fields.']['additional.'][$key];
				$tsConf = $obj->conf['settings.']['fields.']['additional.'][$key . '.'];
				$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
				$markerArray['###ADDITIONAL_' . strtoupper($key) . '###'] = $tsRenderedValue;
			}
		}

		$isToBeDisplayed  = TRUE;

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderProductItemWithVariants']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderProductItemWithVariants'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'markerArray' => &$markerArray,
						'product' => &$product,
						'isToBeDisplayed' => &$isToBeDisplayed
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		if ($isToBeDisplayed) {
			$outerMarkerArray['###VARIANTITEMALL###'] = $obj->cObj->substituteMarkerArrayCached($obj->tmpl['variantitemall'], NULL, $markerArray);
		} else {
			$outerMarkerArray['###VARIANTITEMALL###'] = '';
		}
		$productArr['variantcount'] = 0;
		$this->renderVariant($outerMarkerArray['###VARIANTITEMVARIANT###'], $productArr, $product->getVariants(), $obj);

		return $obj->cObj->substituteMarkerArrayCached($obj->tmpl['variantitem'], NULL, $outerMarkerArray);
	}

	/**
	 * @param $content
	 * @param $productArr
	 * @param $variants
	 * @param $obj
	 * @return void
	 */
	public function renderVariant(&$content, $variantArr, $variants, &$obj) {
		$variantArr['variantcount'] += 1;
		if ($variants) {

			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['sortVariantsBeforeRenderVariant']) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['sortVariantsBeforeRenderVariant'] as $funcRef) {
					if ($funcRef) {
						$params = array(
							'variants' => &$variants
						);

						t3lib_div::callUserFunction($funcRef, $params, $this);
					}
				}
			}

			foreach ($variants as $variant) {
					// enable .field in typoscript
				$variantArr['variant'][$variantArr['variantcount']] = $variant->getId();
				$variantArr['variantParam'] = '[' . join('][', $variantArr['variant']) . ']';
				$variantArr['qty'] = $variant->getQty();
				$variantArr['varianttitle' . $variantArr['variantcount']] = $variant->getTitle();
				$variantArr['variantsku' . $variantArr['variantcount']] = $variant->getSku();
				$variantArr['variantid' . $variantArr['variantcount']] = $variant->getId();
				$variantArr['price'] = $variant->getPrice();
				$variantArr['price_calc_method'] = $variant->getPriceCalcMethod();
				$variantArr['parent_price'] = $variant->getParentPrice();
				$variantArr['calculated_price'] = $variant->getPriceCalculated();
				$variantArr['price_total'] = $variant->getGross();
				$variantArr['price_total_gross'] = $variant->getGross();
				$variantArr['price_total_net'] = $variant->getNet();
				$variantArr['tax'] = $variant->getTax();

				$variantAdditional = $variant->getAdditionalArray();
				if (is_array($variantAdditional)) {
					foreach ($variantAdditional as $key => $value) {
						$variantArr[$key] = $value;
					}
				}

				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderVariant']) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderVariant'] as $funcRef) {
						if ($funcRef) {
							$params = array(
								'variantArr' => &$variantArr,
								'variant' => &$variant
							);

							t3lib_div::callUserFunction($funcRef, $params, $this);
						}
					}
				}

				$GLOBALS['TSFE']->cObj->start($variantArr, $obj->conf['db.']['table']);

				if ($variant->getVariants()) {
					$this->renderVariant($content, $variantArr, $variant->getVariants(), $obj);
				} else {
					if ($obj->conf['settings.']['fields.']) {
						foreach ((array)$obj->conf['settings.']['fields.'] as $key => $value) {
							if (!stristr($key, '.')) {
								if ($key == 'tax') {
									$tsKey = $obj->conf['settings.']['fields.']['taxclass.'][$key];
									$tsConf = $obj->conf['settings.']['fields.']['taxclass.'][$key . '.'];
								} elseif ($key != 'additional') {
									$tsKey = $obj->conf['settings.']['fields.'][$key];
									$tsConf = $obj->conf['settings.']['fields.'][$key . '.'];
								}
								$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
								$markerArray['###' . strtoupper($key) . '###'] = $tsRenderedValue;
							}
						}

						foreach ((array)$obj->conf['settings.']['fields.']['additional.'] as $key => $value) {
							if (!stristr($key, '.')) {
								$tsKey = $obj->conf['settings.']['fields.']['additional.'][$key];
								$tsConf = $obj->conf['settings.']['fields.']['additional.'][$key . '.'];
								$tsRenderedValue = $GLOBALS['TSFE']->cObj->cObjGetSingle($tsKey, $tsConf);
								$markerArray['###ADDITIONAL_' . strtoupper($key) . '###'] = $tsRenderedValue;
							}
						}
					}

					if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderVariant']) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeMarkerArrayBeforeRenderVariant'] as $funcRef) {
							if ($funcRef) {
								$params = array(
									'markerArray' => &$markerArray,
									'variant' => &$variant
								);

								t3lib_div::callUserFunction($funcRef, $params, $this);
							}
						}
					}

					$content .= $obj->cObj->substituteMarkerArrayCached($obj->tmpl['variantitemvariant'], NULL, $markerArray);

					if (is_array($variantAdditional)) {
						foreach ($variantAdditional as $key => $value) {
							unset($variantArr[$key]);
						}
					}

				}

				unset($variantArr['hint1']);
			}
		}
	}

	/**
	 * @param $cart
	 * @param $obj
	 * @return null
	 */
	public function renderOverall($cart, &$obj) {
		$outerArr = array(
			'service_cost_net' => $cart->getServiceNet(),
			'service_cost_gross' => $cart->getServiceGross(),
			'cart_gross' => $cart->getGross() + $cart->getServiceGross(),
			'cart_gross_no_service' => $cart->getGross(),
			'cart_net' => $cart->getNet() + $cart->getServiceNet(),
			'cart_net_no_service' => $cart->getNet()
		);

		foreach ($cart->getTaxesWithServices() as $taxclass => $tax) {
			$outerArr['cart_tax_' . $taxclass] = $tax;
		}

		if (TYPO3_DLOG) {
			t3lib_div::devLog('outerArr', $obj->extKey, 0, $outerArr);
		}

		// enable .field in typoscript
		$GLOBALS['TSFE']->cObj->start($outerArr, $obj->conf['db.']['table']);
		foreach ((array) $obj->conf['settings.']['overall.'] as $key => $value) {
			if (!stristr($key, '.')) {
				$obj->outerMarkerArray['###' . strtoupper($key) . '###'] = $GLOBALS['TSFE']->cObj->cObjGetSingle($obj->conf['settings.']['overall.'][$key], $obj->conf['settings.']['overall.'][$key . '.']);
			}
		}

		return NULL;
	}

	/**
	 * @param $cart
	 * @param $obj
	 * @return null
	 */
	public function renderMiniCart($cart, &$obj) {
		$outerArr = array(
			'minicart_count' => $cart->getCount(),
			'minicart_gross' => $cart->getGross(),
			'minicart_gross_with_service' => $cart->getGross() + $cart->getServiceGross(),
		);

		$GLOBALS['TSFE']->cObj->start($outerArr, $obj->conf['db.']['table']);
		foreach ((array) $obj->conf['settings.']['fields.'] as $key => $value) {
			if (!stristr($key, '.')) {
				$obj->minicartMarkerArray['###' . strtoupper($key) . '###'] = $GLOBALS['TSFE']->cObj->cObjGetSingle($obj->conf['settings.']['fields.'][$key], $obj->conf['settings.']['fields.'][$key . '.']);
			}
		}

		return NULL;
	}

	/**
	 * @param $obj
	 * @return null
	 */
	public function renderEmptyCart(&$obj) {
			// if template found overwrite normal template with empty template
		if (!empty($obj->tmpl['all'])) {
			$obj->tmpl['all'] = $obj->tmpl['empty'];
		} else {
			$obj->tmpl['all'] = $obj->div->msg($obj->pi_getLL('error_noTemplate', 'No Template found'));
		}

		return NULL;
	}

	/**
	 * @param $cart
	 * @param $options
	 * @param $seloption
	 * @param $obj
	 * @return null
	 */
	public function renderServiceList(&$cart, &$options, &$seloption, &$obj) {
		$content = '';

		if (is_array($options)) {
			$class = get_class( array_shift( array_values( $options ) ) );
			$type = strtolower( substr( strrchr( $class, '_' ), 1 ) );
			$upperType = strtoupper( $type );
		} else {
			return NULL;
		}
		foreach ($options as $key => $option) {
				// hide option if not available by cart['grossNoService']
			$show = $option->isAvailable($cart->getGross());

			if ($show || $obj->conf[$type . '.']['show_all_disabled']) {
				$disabled = $show ? '' : 'disabled="disabled"';

				$conditionList = array();

				$freeFrom = $option->getFreeFrom();
				$freeUntil = $option->getFreeUntil();
				$availableFrom = $option->getAvailableFrom();
				$availableUntil = $option->getAvailableUntil();

				if (isset($freeFrom)) {
					$pmarkerArray['###CONDITION###'] =
							$obj->pi_getLL('wtcart_ll_' . $type . '_free_from') . ' ' . $this->formatPrice($freeFrom, $obj);
					$conditionList['###CONTENT###'] .=
							$obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_item'], $pmarkerArray);
				}
				if (isset($freeUntil)) {
					$pmarkerArray['###CONDITION###'] =
							$obj->pi_getLL('wtcart_ll_' . $type . '_free_until') . ' ' . $this->formatPrice($freeUntil, $obj);
					$conditionList['###CONTENT###'] .=
							$obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_item'], $pmarkerArray);
				}

				if (!$show) {
					if (isset($availableFrom)) {
						$pmarkerArray['###CONDITION###'] =
								$obj->pi_getLL('wtcart_ll_' . $type . '_available_from') . ' ' . $this->formatPrice($availableFrom, $obj);
						$conditionList['###CONTENT###'] .=
								$obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_item'], $pmarkerArray);
					}
					if (isset($availableUntil)) {
						$pmarkerArray['###CONDITION###'] =
								$obj->pi_getLL('wtcart_ll_' . $type . '_available_until') . ' ' . $this->formatPrice($availableUntil, $obj);
						$conditionList['###CONTENT###'] .=
								$obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_item'], $pmarkerArray);
					}
				}

				if (!$option->isFree($cart->getGross())) {
					if ($option->getIsNetPrice()) {
						$price = $option->getNet($cart);
					} else {
						$price = $option->getGross($cart);
					}
					if ($option->getExtratype() != 'each') {
						$showPrice = $this->formatPrice($price, $obj);
					} else {
						$showPrice = sprintf($obj->pi_getLL('wtcart_ll_special_each'), $this->formatPrice($price, $obj));
					}
				} else {
					$showPrice = $this->formatPrice(0.0, $obj);
				}

				if ($option->getExtratype() != 'each') {
					switch ($option->getExtratype()) {
						case 'by_price':
							$unit = $obj->conf['main.']['currencySymbol'];
							break;
						case 'by_quantity':
							$unit = $obj->conf['main.']['quantitySymbol'];
							break;
						case 'by_service_attribute_1_sum':
							$unit = $obj->conf['main.']['service_attribute_1_symbol'];
							break;
						case 'by_service_attribute_1_max':
							$unit = $obj->conf['main.']['service_attribute_1_symbol'];
							break;
						case 'by_service_attribute_2_sum':
							$unit = $obj->conf['main.']['service_attribute_2_symbol'];
							break;
						case 'by_service_attribute_2_max':
							$unit = $obj->conf['main.']['service_attribute_2_symbol'];
							break;
						case 'by_service_attribute_3_sum':
							$unit = $obj->conf['main.']['service_attribute_3_symbol'];
							break;
						case 'by_service_attribute_3_max':
							$unit = $obj->conf['main.']['service_attribute_3_symbol'];
							break;
						default:
							$unit = '';
					}
					foreach ($option->getExtras() as $extra) {
						if ($extra->getIsNetPrice()) {
							$price = $extra->getNet($cart);
						} else {
							$price = $extra->getGross($cart);
						}
						$pmarkerArray['###CONDITION###'] = $obj->pi_getLL('WTCART_SHIPPING') . ' ' . $extra->getCondition() . ' ' . $unit . ' : ' . $this->formatPrice($price, $obj);
						$conditionList['###CONTENT###'] .= $obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_item'], $pmarkerArray);
					}
				}

				if ($type != 'special') {
					if ($option->getId() == $seloption->getId()) {
						$checkradio =  'checked="checked"';
					} else {
						$checkradio =  '';
					}
					$obj->smarkerArray['###' . $upperType . '_RADIO###'] = '<input type="radio" onchange="this.form.submit()" name="tx_wtcart_pi1[' . $type . ']" id="tx_wtcart_pi1_' . $type . '_' . intval($key) . '"  value="' . intval($key) . '"  ' . $checkradio . $disabled . '/>';
				} else {
					$checkbox = '';
					foreach ($seloption as $selected) {
						$checkbox = $option->getId() == $selected->getId() ? 'checked="checked"' : $checkbox;
					}
					$obj->smarkerArray['###' . $upperType . '_CHECKBOX###'] = '<input type="checkbox" onchange="this.form.submit()" name="tx_wtcart_pi1[' . $type . '][]" id="tx_wtcart_pi1_' . $type . '_' . intval($key) . '"  value="' . intval($key) . '"  ' . $checkbox . $disabled . '/>';
				}

				// TODO: In braces the actual Price for Payment should be displayed, not the first one.

				$obj->smarkerArray['###' . $upperType . '_TITLE###'] = '<label for="tx_wtcart_pi1_' . $type . '_' . intval($key) . '">' . $option->getName() . ' (' . $showPrice . ')</label>';

				if (isset($conditionList['###CONTENT###'])) {
					$obj->smarkerArray['###' . $upperType . '_CONDITION###'] = $obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_condition_all'], NULL, $conditionList);
				} else {
					$obj->smarkerArray['###' . $upperType . '_CONDITION###'] = '';
				}
				$content .= $obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_item'], $obj->smarkerArray);
			}
		}

		if ($type != 'special') {
			$list = 'RADIO';
		} else {
			$list = 'CHECKBOX';
		}
		if ($content) {
			$service['###CONTENT###'] = $content;
			$obj->subpartMarkerArray['###' . $upperType . '_' . $list . '###'] = $obj->cObj->substituteMarkerArrayCached($obj->tmpl[$type . '_all'], NULL, $service);
		} else {
			$obj->subpartMarkerArray['###' . $upperType . '_' . $list . '###'] = '';
		}

		return NULL;
	}

	/**
	 * @param $cart
	 * @param $obj
	 * @return void
	 */
	public function renderAdditional(&$cart, &$obj) {
		$additional['###CONTENT###'] = '';

		$cartArr = array();

		foreach($cart->getAdditionalArray() as $additional_key => $additional_val) {
			$cartArr[$additional_key] = $additional_val;
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderCartAdditional']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeFieldArrayBeforeRenderCartAdditional'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'cartArr' => &$cartArr,
						'cart' => &$cart
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		$GLOBALS['TSFE']->cObj->start($cartArr, $obj->conf['db.']['table']);

		foreach ($cart->getAdditionalArray() as $additional_key => $additional_val) {
			$obj->markerArray['###ADDITIONAL_TITLE###'] = $GLOBALS['TSFE']->cObj->cObjGetSingle($obj->conf['settings.']['additional.'][$additional_key], $obj->conf['settings.']['additional.'][$additional_key . '.']);
			$additional['###CONTENT###'] .= $obj->cObj->substituteMarkerArrayCached($obj->tmpl['additional_item'], $obj->markerArray);
		}

		if ($additional['###CONTENT###']) {
			$obj->outerMarkerArray['###ADDITIONAL_LIST###'] = $obj->cObj->substituteMarkerArrayCached($obj->tmpl['additional_all'], NULL, $additional);
		} else {
			$obj->outerMarkerArray['###ADDITIONAL_LIST###'] = '';
		}
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Cart $cart
	 * @param $seloption
	 * @param string $type
	 * @param $obj
	 * @return null
	 */
	public function renderServiceItem(&$cart, &$seloption, $type, &$obj) {
		$item = '';

		if (is_array($seloption)) {
			foreach ($seloption as $selected) {
				$upperType = strtoupper(get_class($selected));
				if ($selected->getIsNetPrice()) {
					$price = $selected->getNet($cart);
				} else {
					$price = $selected->getGross($cart);
				}
				$item .= $selected->getName() . ' (' . $this->formatPrice($price, $obj) . ') <br />';
			}
		} else {
			if ($seloption) {
				if ($seloption->getIsNetPrice()) {
					$price = $seloption->getNet($cart);
				} else {
					$price = $seloption->getGross($cart);
				}
				$item .= $seloption->getName() . ' (' . $this->formatPrice($price, $obj) . ') <br />';
			}
		}

		$obj->outerMarkerArray['###' . strtoupper($type) . '_OPTION###'] = $item;

		return NULL;
	}


	/**
	 * @param $value
	 * @param $obj
	 * @return string
	 */
	private function formatPrice($value, &$obj) {
		$obj->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		$currencySymbol = $obj->conf['main.']['currencySymbol'];
		$price = number_format($value, $obj->conf['main.']['decimal'], $obj->conf['main.']['dec_point'], $obj->conf['main.']['thousands_sep']);

			// print currency symbol before or after price
		if ($obj->conf['main.']['currencySymbolBeforePrice']) {
			$price = $currencySymbol . ' ' . $price;
		} else {
			$price = $price . ' ' . $currencySymbol;
		}

		return $price;
	}

}
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
 * @author    Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
class Tx_WtCart_Domain_Model_Variant {

	/**
	 * @var Tx_WtCart_Domain_Model_Product
	 */
	private $product;

	/**
	 * @var Tx_WtCart_Domain_Model_Variant
	 */
	private $parentVariant;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $sku;

	/**
	 * @var int
	 */
	private $priceCalcMethod;

	/**
	 * @var float
	 */
	private $price;

	/**
	 * @var Tx_WtCart_Domain_Model_Tax
	 */
	private $taxClass;

	/**
	 * @var integer
	 */
	private $qty;

	/**
	 * @var array Tx_WtCart_Domain_Model_Variant
	 */
	private $variants;

	/**
	 * @var float
	 */
	private $gross;

	/**
	 * @var float
	 */
	private $net;

	/**
	 * @var float
	 */
	private $tax;

	/**
	 * @var boolean
	 */
	private $isNetPrice;

	/**
	 * @var boolean
	 */
	private $isFeVariant;

	/**
	 * @var integer
	 */
	private $hasFeVariants;

	/**
	 * @var array Additional
	 */
	private $additional = array();

	/**
	 * __construct
	 *
	 * @param $id
	 * @param $title
	 * @param $sku
	 * @param $priceCalcMethod
	 * @param $price
	 * @param Tx_WtCart_Domain_Model_Tax $taxclass
	 * @param $qty
	 * @param bool $isNetPrice
	 * @internal param $name
	 * @return Tx_WtCart_Domain_Model_Variant
	 */
	public function __construct($id, $title, $sku, $priceCalcMethod, $price, Tx_WtCart_Domain_Model_Tax $taxclass, $qty, $isNetPrice = FALSE) {
		$this->id = $id;
		$this->title = $title;
		$this->sku = $sku;
		$this->priceCalcMethod = $priceCalcMethod;
		$this->price = floatval(str_replace(',', '.', $price));
		$this->taxClass = $taxclass;
		$this->qty = $qty;

		$this->isNetPrice = $isNetPrice;

		$this->reCalc();
	}

	/**
	 * @return void
	 */
	public function debug() {
		if (TYPO3_DLOG) {
			//debug all variants
			if ($this->variants) {
				foreach ($this->variants as $variant) {
					$variant->debug();
				}
			}

			// debug the product itself
			$out = $this->toArray();

			t3lib_div::devLog('variant', 'wt_cart', 0, $out);
		}
	}

	/**
	 * @deprecated since wt_cart 2.1; will be removed in wt_cart 3.0; use toArray instead
	 * @return array
	 */
	public function getVariantAsArray() {
		return $this->toArray();
	}

	/**
	 * @return array
	 */
	public function toArray() {
		$variantArr = array(
			'id' => $this->id,
			'sku' => $this->sku,
			'title' => $this->title,
			'price_calc_method' => $this->priceCalcMethod,
			'price' => $this->getPrice,
			'taxclass' => $this->taxClass,
			'qty' => $this->qty,
			'price_total_gross' => $this->gross,
			'price_total_net' => $this->net,
			'tax' => $this->tax,
			'additional' => $this->additional
		);

		if ($this->variants) {
			$innerVariantArr = array();

			foreach ($this->variants as $variant) {
				/** @var $variant Tx_WtCart_Domain_Model_Variant */
				array_push( $innerVariantArr, array( $variant->getId() => $variant->toArray() ) );
			}

			array_push( $variantArr, array('variants' => $innerVariantArr) );
		}

		return $variantArr;
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Variant
	 */
	public function setParentVariant(Tx_WtCart_Domain_Model_Variant $parentVariant) {
		$this->parentVariant = $parentVariant;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Variant
	 */
	public function getParentVariant() {
		return $this->parentVariant;
	}


	/**
	 * @param boolean
	 */
	public function setIsNetPrice($isNetPrice) {
		$this->isNetPrice = $isNetPrice;
	}

	/**
	 * @return boolean
	 */
	public function getIsNetPrice() {
		return $this->isNetPrice;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param $title
	 * @return string
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return float
	 */
	public function getPrice() {
		return $this->price;
	}


	/**
	 * @return float
	 */
	public function getDiscount() {
		$price = $this->getPrice();

		if ($this->getParentVariant()) {
			$parentPrice = $this->getParentVariant()->getPrice();
		} elseif ($this->getProduct()) {
			$parentPrice = $this->getProduct()->getPrice();
		} else {
			$parentPrice = 0;
		}

		switch ($this->priceCalcMethod) {
			case 0:
				$discount = 0;
				break;
			case 1:
				$discount = 0;
				break;
			case 2:
				$discount = -1 * (($price / 100) * ($parentPrice));
				break;
			case 3:
				$discount = 0;
				break;
			case 4:
				$discount = ($price / 100) * ($parentPrice);
				break;
			default:
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'price_calc_method' => $this->priceCalcMethod,
						'price' => &$price,
						'parent_price' => &$parentPrice,
						'discount' => &$discount,
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		return $discount;
	}

	/**
	 * @return float
	 */
	public function getPriceCalculated() {
		$price = $this->getPrice();

		if ($this->getParentVariant()) {
			$parentPrice = $this->getParentVariant()->getPrice();
		} elseif ($this->getProduct()) {
			$parentPrice = $this->getProduct()->getPrice();
		} else {
			$parentPrice = 0;
		}

		switch ($this->priceCalcMethod) {
			case 0:
				$discount = 0;
				break;
			case 1:
				$discount = 0;
				break;
			case 2:
				$discount = -1 * (($price / 100) * ($parentPrice));
				break;
			case 3:
				$discount = 0;
				break;
			case 4:
				$discount = ($price / 100) * ($parentPrice);
				break;
			default:
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
				if ($funcRef) {
					$params = array(
						'price_calc_method' => $this->priceCalcMethod,
						'price' => &$price,
						'parent_price' => &$parentPrice,
						'discount' => &$discount,
					);

					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}

		switch ($this->priceCalcMethod) {
			case 0:
				$parentPrice = 0;
				break;
			case 1:
				$price = -1 * $price;
				break;
			case 2:
				$price = 0;
				break;
			case 3:
				break;
			case 4:
				$price = 0;
				break;
			default:
		}

		return $parentPrice + $price + $discount;
	}

	/**
	 * @return float
	 */
	public function getParentPrice() {
		if ($this->priceCalcMethod == 0) {
			return FALSE;
		} else {
			if ($this->getParentVariant()) {
				return $this->getParentVariant()->getPrice();
			} elseif ($this->getProduct()) {
				return $this->getProduct()->getPrice();
			}
		}
	}

	/**
	 * @param $price
	 */
	public function setPrice($price) {
		$this->price = $price;

		$this->reCalc();
	}

	/**
	 * @return int
	 */
	public function getPriceCalcMethod() {
		return $this->priceCalcMethod;

		$this->reCalc();
	}

	/**
	 * @param $priceCalcMethod
	 */
	public function setPriceCalcMethod($priceCalcMethod) {
		$this->priceCalcMethod = $priceCalcMethod;
	}

	/**
	 * @return string
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * @param $sku
	 */
	public function setSku($sku) {
		$this->sku = $sku;
	}

	/**
	 * @return integer
	 */
	public function getHasFeVariants() {
		return $this->hasFeVariants;
	}

	/**
	 * @param $hasFeVariants
	 */
	public function setHasFeVariants($hasFeVariants) {
		$this->hasFeVariants = $hasFeVariants;
	}

	/**
	 * @return boolean
	 */
	public function getIsFeVariant() {
		return $this->isFeVariant;
	}

	/**
	 * @param boolean $isFeVariant
	 */
	public function setIsFeVariant($isFeVariant) {
		$this->isFeVariant = $isFeVariant;
	}

	/**
	 * @return int
	 */
	public function getQty() {
		return $this->qty;
	}

	/**
	 * @return float
	 */
	public function getGross() {
		$this->calcGross();
		return $this->gross;
	}

	/**
	 * @return float
	 */
	public function getNet() {
		$this->calcNet();
		return $this->net;
	}

	/**
	 * @return array
	 */
	public function getTax() {
		return array('taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax);
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Tax
	 */
	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 * @param $newQty
	 */
	public function setQty($newQty) {
		$this->qty = $newQty;

		$this->reCalc();
	}

	/**
	 * @param $newQty
	 */
	public function changeQty($newQty) {
		$this->qty = $newQty;

		if ($this->variants) {
			foreach ($this->variants as $variant) {
				$variant->changeQty($newQty);
			}
		}

		$this->reCalc();
	}

	/**
	 * @param $variantQtyArray
	 * @internal param $id
	 * @internal param $newQty
	 */
	public function changeVariantsQty($variantQtyArray) {
		foreach ($variantQtyArray as $variantId => $qty) {
			$variant = $this->variants[$variantId];

			if (is_array($qty)) {
				$variant->changeVariantsQty($qty);
				$this->reCalc();
			} else {
				$variant->changeQty($qty);
				$this->reCalc();
			}
		}
	}

	/**
	 * @param array $newVariants
	 * @return mixed
	 */
	public function addVariants($newVariants) {
		foreach ($newVariants as $newVariant) {
			$this->addVariant($newVariant);
		}
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Variant $newVariant
	 * @return mixed
	 */
	public function addVariant(Tx_WtCart_Domain_Model_Variant $newVariant) {
		$newVariantId = $newVariant->getId();
		$variant = $this->variants[$newVariantId];

		if ($variant) {
			if ($variant->getVariants()) {
				$variant->addVariants($newVariant->getVariants());
			} else {
				$newQty = $variant->getQty() + $newVariant->getQty();
				$variant->setQty($newQty);
			}
		} else {
			$this->variants[$newVariantId] = $newVariant;
			$newVariant->setParentVariant($this);
		}

		$this->reCalc();
	}

	/**
	 * @return array
	 */
	public function getVariants() {
		return $this->variants;
	}

	/**
	 * @param $variantId
	 * @return Tx_WtCart_Domain_Model_Variant
	 */
	public function getVariantById($variantId) {
		return $this->variants[$variantId];
	}

	/**
	 * @param $variantId
	 * @return Tx_WtCart_Domain_Model_Variant
	 */
	public function getVariant($variantId) {
		return $this->getVariantById($variantId);
	}

	/**
	 * @param $variantsArray
	 * @return bool|int
	 */
	public function removeVariants($variantsArray) {
		foreach ($variantsArray as $variantId => $value) {
			$variant = $this->variants[$variantId];
			if ($variant) {
				if (is_array($value)) {
					$variant->removeVariants($value);

					if (!$variant->getVariants()) {
						unset($this->variants[$variantId]);
					}

					$this->reCalc();
				} else {
					unset($this->variants[$variantId]);

					$this->reCalc();
				}
			} else {
				return -1;
			}
		}

		return TRUE;
	}

	/**
	 * @return void
	 */
	private function calcGross() {
		if ($this->isNetPrice == FALSE) {
			if ($this->variants) {
				$sum = 0.0;
				foreach ($this->variants as $variant) {
					$sum += $variant->getGross();
				}
				$this->gross = $sum;
			} else {
				$price = $this->getPrice();
				if ($this->getParentVariant()) {
					$parentPrice = $this->getParentVariant()->getPrice();
				} elseif ($this->getProduct()) {
					$parentPrice = $this->getProduct()->getPrice();
				} else {
					$parentPrice = 0;
				}

				switch ($this->priceCalcMethod) {
					case 0:
						$discount = 0;
						break;
					case 1:
						$discount = 0;
						break;
					case 2:
						$discount = -1 * (($price / 100) * ($parentPrice));
						break;
					case 3:
						$discount = 0;
						break;
					case 4:
						$discount = ($price / 100) * ($parentPrice);
						break;
					default:
				}

				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
						if ($funcRef) {
							$params = array(
								'price_calc_method' => $this->priceCalcMethod,
								'price' => &$price,
								'parent_price' => &$parentPrice,
								'discount' => &$discount,
							);

							t3lib_div::callUserFunction($funcRef, $params, $this);
						}
					}
				}

				switch ($this->priceCalcMethod) {
					case 0:
						$parentPrice = 0;
						break;
					case 1:
						$price = -1 * $price;
						break;
					case 2:
						$price = 0;
						break;
					case 3:
						break;
					case 4:
						$price = 0;
						break;
					default:
				}

				$this->gross = ($parentPrice + $price + $discount) * $this->qty;

			}
		} else {
			$this->calcNet();
			$this->calcTax();
			$this->gross = $this->net + $this->tax;
		}
	}

	/**
	 * @return void
	 */
	private function calcTax() {
		if ($this->isNetPrice == FALSE) {
			$this->calcGross();
			$this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
		} else {
			$this->calcNet();
			$this->tax = ($this->net * $this->taxClass->getCalc());
		}
	}

	/**
	 * @return void
	 */
	private function calcNet() {
		if ($this->isNetPrice == TRUE) {
			if ($this->variants) {
				$sum = 0.0;
				foreach ($this->variants as $variant) {
					$sum += $variant->getNet();
				}
				$this->net = $sum;
			} else {
				$price = $this->getPrice();
				if ($this->getParentVariant()) {
					$parentPrice = $this->getParentVariant()->getPrice();
				} elseif ($this->getProduct()) {
					$parentPrice = $this->getProduct()->getPrice();
				} else {
					$parentPrice = 0;
				}

				switch ($this->priceCalcMethod) {
					case 0:
						$discount = 0;
						break;
					case 1:
						$discount = 0;
						break;
					case 2:
						$discount = -1 * (($price / 100) * ($parentPrice));
						break;
					case 3:
						$discount = 0;
						break;
					case 4:
						$discount = ($price / 100) * ($parentPrice);
						break;
					default:
				}

				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
						if ($funcRef) {
							$params = array(
								'price_calc_method' => $this->priceCalcMethod,
								'price' => &$price,
								'parent_price' => &$parentPrice,
								'discount' => &$discount,
							);

							t3lib_div::callUserFunction($funcRef, $params, $this);
						}
					}
				}

				switch ($this->priceCalcMethod) {
					case 0:
						$parentPrice = 0;
						break;
					case 1:
						$price = -1 * $price;
						break;
					case 2:
						$price = 0;
						break;
					case 3:
						break;
					case 4:
						$price = 0;
						break;
					default:
				}

				$this->net = ($parentPrice + $price + $discount) * $this->qty;
			}
		} else {
			$this->calcGross();
			$this->calcTax();
			$this->net = $this->gross - $this->tax;
		}
	}

	/**
	 * @return void
	 */
	private function reCalc() {
		if ($this->variants) {
			$qty = 0;
			foreach ($this->variants as $variant) {
				$qty += $variant->getQty();
			}

			if ($this->qty != $qty) {
				$this->qty = $qty;
			}
		}

		if ($this->isNetPrice == FALSE) {
			$this->calcGross();
			$this->calcTax();
			$this->calcNet();
		} else {
			$this->calcNet();
			$this->calcTax();
			$this->calcGross();
		}
	}

	/**
	 * @return array
	 */
	public function getAdditionalArray() {
		return $this->additional;
	}

	/**
	 * @param $additional
	 * @return void
	 */
	public function setAdditionalArray($additional) {
		$this->additional = $additional;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function getAdditional($key) {
		return $this->additional[$key];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setAdditional($key, $value) {
		$this->additional[$key] = $value;
	}
}

?>
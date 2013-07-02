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
class Variant {

	/**
	 * @var Product
	 */
	private $product;

	/**
	 * @var Variant
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
	 * @var Tax
	 */
	private $taxClass;

	/**
	 * @var integer
	 */
	private $qty;

	/**
	 * @var array Variants
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

	/*+
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
	 * __construct
	 *
	 * @param $id
	 * @param $title
	 * @param $sku
	 * @param $priceCalcMethod
	 * @param $price
	 * @param Tax $taxclass
	 * @param $qty
	 * @param bool $isNetPrice
	 * @internal param $name
	 * @return Variant
	 */
	public function __construct($id, $title, $sku, $priceCalcMethod, $price, Tax $taxclass, $qty, $isNetPrice = FALSE) {
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

	public function debug() {
		if (TYPO3_DLOG) {
			//debug all variants
			if ($this->variants) {
				foreach ($this->variants as $variant) {
					$variant->debug();
				}
			}

			// debug the product itself
			$out = $this->getVariantAsArray();

			t3lib_div::devLog('variant', 'wt_cart', 0, $out);
		}
	}

	// temp function, should remove later
	public function getVariantAsArray() {
		return array('id' => $this->id, 'sku' => $this->sku, 'title' => $this->title, 'price_calc_method' => $this->priceCalcMethod, 'price' => $this->getPrice, 'taxclass' => $this->taxClass, 'qty' => $this->qty, 'price_total_gross' => $this->gross, 'price_total_net' => $this->net, 'tax' => $this->tax);
	}

	/**
	 * @param Product
	 */
	public function setProduct($product) {
		$this->product = $product;
	}

	/**
	 * @return Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param Variant
	 */
	public function setParentVariant($parentVariant) {
		$this->parentVariant = $parentVariant;
	}

	/**
	 * @return Variant
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
	public function getPriceCalculated() {
		switch ($this->priceCalcMethod) {
			case 1:
				if ($this->getParentVariant()) {
					return $this->getParentVariant()->getPrice() - $this->price;
				} elseif ($this->getProduct()) {
					return $this->getProduct()->getPrice() - $this->price;
				}
				break;
			case 2:
				if ($this->getParentVariant()) {
					$price = $this->getPrice();
					$parentPrice = $this->getParentVariant()->getPrice();
					$discount = ($price / 100) * ($parentPrice);
					if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
							if ($funcRef) {
								$params = array(
									'price' => &$price,
									'parentPrice' => &$parentPrice,
									'discount' => &$discount,
								);

								t3lib_div::callUserFunction($funcRef, $params, $this);
							}
						}
					}
					return $parentPrice - $discount;
				} elseif ($this->getProduct()) {
					$price = $this->getPrice();
					$parentPrice = $this->getProduct()->getPrice();
					$discount = ($price / 100) * ($parentPrice);
					if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
							if ($funcRef) {
								$params = array(
									'price' => &$price,
									'parentPrice' => &$parentPrice,
									'discount' => &$discount,
								);

								t3lib_div::callUserFunction($funcRef, $params, $this);
							}
						}
					}
					return $parentPrice - $discount;
				}
				break;
			case 3:
				if ($this->getParentVariant()) {
					return $this->getParentVariant()->getPrice() + $this->price;
				} elseif ($this->getProduct()) {
					return $this->getProduct->getPrice() + $this->price;
				}
				break;
			case 4:
				if ($this->getParentVariant()) {
					$price = $this->getPrice();
					$parentPrice = $this->getParentVariant()->getPrice();
					$discount = ($price / 100) * ($parentPrice);
					if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
							if ($funcRef) {
								$params = array(
									'price' => &$price,
									'parentPrice' => &$parentPrice,
									'discount' => &$discount,
								);

								t3lib_div::callUserFunction($funcRef, $params, $this);
							}
						}
					}
					return $parentPrice + $discount;
				} elseif ($this->getProduct()) {
					$price = $this->getPrice();
					$parentPrice = $this->getProduct()->getPrice();
					$discount = ($price / 100) * ($parentPrice);
					if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
						foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
							if ($funcRef) {
								$params = array(
									'price' => &$price,
									'parentPrice' => &$parentPrice,
									'discount' => &$discount,
								);

								t3lib_div::callUserFunction($funcRef, $params, $this);
							}
						}
					}
					return $parentPrice + $discount;
				}
				break;
			default:
				return $this->getPrice();
		}
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

	public function getQty() {
		return $this->qty;
	}

	public function getGross() {
		$this->calcGross();
		return $this->gross;
	}

	public function getNet() {
		$this->calcNet();
		return $this->net;
	}

	public function getTax() {
		return array('taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax);
	}

	public function getTaxClass() {
		return $this->taxClass;
	}

	public function setQty($newQty) {
		$this->qty = $newQty;

		$this->reCalc();
	}

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
	 * @param Variant $newVariant
	 * @return mixed
	 */
	public function addVariant(Variant $newVariant) {
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
	 * @param $variantsArray
	 * @internal param $productPuid
	 * @internal param null $variantId
	 * @internal param $id
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

	private function calcGross() {
		if ($this->isNetPrice == FALSE) {
			if ($this->variants) {
				$sum = 0.0;
				foreach ($this->variants as $variant) {
					$sum += $variant->getGross();
				}
				$this->gross = $sum;
			} else {
				switch ($this->priceCalcMethod) {
					case 1:
						if ($this->getParentVariant()) {
							$this->gross = ($this->getParentVariant()->getPrice() - $this->getPrice()) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->gross = ($this->getProduct()->getPrice() - $this->getPrice()) * $this->qty;
						}
						break;
					case 2:
						if ($this->getParentVariant()) {
							$price = $this->getPrice();
							$parentPrice = $this->getParentVariant()->getPrice();
							$discount = ($price / 100) * ($parentPrice);

							t3lib_div::devLog('before Hook $discount', 'wt_cart', 0, array($discount));

							if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
								foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
									if ($funcRef) {
										$params = array(
											'price' => &$price,
											'parentPrice' => &$parentPrice,
											'discount' => &$discount,
										);

										t3lib_div::callUserFunction($funcRef, $params, $this);
									}
								}
							}
							$this->gross = ($parentPrice - $discount) * $this->qty;
						} elseif ($this->getProduct()) {
							$price = $this->getPrice();
							$parentPrice = $this->getProduct()->getPrice();
							$discount = ($price / 100) * ($parentPrice);
							if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
								foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
									if ($funcRef) {
										$params = array(
											'price' => &$price,
											'parentPrice' => &$parentPrice,
											'discount' => &$discount,
										);

										t3lib_div::callUserFunction($funcRef, $params, $this);
									}
								}
							}
							$this->gross = ($parentPrice - $discount) * $this->qty;
						}
						break;
					case 3:
						if ($this->getParentVariant()) {
							$this->gross = ($this->getParentVariant()->getPrice() + $this->getPrice()) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->gross = ($this->getProduct->getPrice() + $this->getPrice()) * $this->qty;
						}
						break;
					case 4:
						if ($this->getParentVariant()) {
							$price = $this->getPrice();
							$parentPrice = $this->getParentVariant()->getPrice();
							$discount = ($price / 100) * ($parentPrice);
							if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
								foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
									if ($funcRef) {
										$params = array(
											'price' => &$price,
											'parentPrice' => &$parentPrice,
											'discount' => &$discount,
										);

										t3lib_div::callUserFunction($funcRef, $params, $this);
									}
								}
							}
							$this->gross = ($parentPrice - $discount) * $this->qty;
						} elseif ($this->getProduct()) {
							$price = $this->getPrice();
							$parentPrice = $this->getProduct()->getPrice();
							$discount = ($price / 100) * ($parentPrice);
							if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount']) {
								foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['changeVariantDiscount'] as $funcRef) {
									if ($funcRef) {
										$params = array(
											'price' => &$price,
											'parentPrice' => &$parentPrice,
											'discount' => &$discount,
										);

										t3lib_div::callUserFunction($funcRef, $params, $this);
									}
								}
							}
							$this->gross = ($parentPrice - $discount) * $this->qty;
						}
						break;
					default:
						$this->gross = $this->getPrice() * $this->qty;
				}
			}
		} else {
			$this->calcNet();
			$this->calcTax();
			$this->gross = $this->net + $this->tax;
		}
	}

	private function calcTax() {
		if ($this->isNetPrice == FALSE) {
			$this->calcGross();
			$this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
		} else {
			$this->calcNet();
			$this->tax = ($this->net * $this->taxClass->getCalc());
		}
	}

	private function calcNet() {
		if ($this->isNetPrice == TRUE) {
			if ($this->variants) {
				$sum = 0.0;
				foreach ($this->variants as $variant) {
					$sum += $variant->getNet();
				}
				$this->net = $sum;
			} else {
				switch ($this->priceCalcMethod) {
					case 1:
						if ($this->getParentVariant()) {
							$this->net = ($this->parentVariant->getPrice() - $this->price) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->net = ($this->getProduct()->getPrice() - $this->price) * $this->qty;
						}
						break;
					case 2:
						if ($this->getParentVariant()) {
							$this->net = ($this->getParentVariant()->getPrice() - ($this->price / 100 * $this->parentVariant->getPrice())) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->net = ($this->getProduct()->getPrice() - ($this->price / 100 * $this->getProduct()->getPrice())) * $this->qty;
						}
						break;
					case 3:
						if ($this->getParentVariant()) {
							$this->net = ($this->parentVariant->getPrice() + $this->price) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->net = ($this->getProduct->getPrice() + $this->price) * $this->qty;
						}
						break;
					case 4:
						if ($this->getParentVariant()) {
							$this->net = ($this->getParentVariant()->getPrice() + ($this->price / 100 * $this->parentVariant->getPrice())) * $this->qty;
						} elseif ($this->getProduct()) {
							$this->net = ($this->getProduct()->getPrice() + ($this->price / 100 * $this->getProduct()->getPrice())) * $this->qty;
						}
						break;
					default:
						$this->net = $this->getPrice() * $this->qty;
				}
			}
		} else {
			$this->calcGross();
			$this->calcTax();
			$this->net = $this->gross - $this->tax;
		}
	}

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
}

?>
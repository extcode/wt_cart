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

define('TYPO3_DLOG', $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']);

require_once(t3lib_extMgm::extPath('wt_cart') . 'model/tax.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'model/variant.php');

/**
 * Plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author    Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
class Product {
	/**
	 * @var integer
	 */
	private $puid;

	/**
	 * @var integer
	 */
	private $tid;

	/**
	 * @var integer
	 */
	private $cid;

	/**
	 * @var string
	 */
	private $sku;

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
	 * @var integer
	 */
	private $min;

	/**
	 * @var integer
	 */
	private $max;

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

	private $error;

	/**
	 * @var float
	 */
	private $serviceAttribute1;

	/**
	 * @var float
	 */
	private $serviceAttribute2;

	/**
	 * @var float
	 */
	private $serviceAttribute3;

	/*+
	 * @var boolean
	 */
	private $isNetPrice;

	/**
	 * @var array Variants
	 */
	private $variants;

	/**
	 * __construct
	 *
	 * @param $puid
	 * @param int $tid
	 * @param int $cid
	 * @param $sku
	 * @param $title
	 * @param $price
	 * @param Tax $taxclass
	 * @param $qty
	 * @param bool $isNetPrice
	 * @internal param $boolean
	 * @return \Product
	 */
	public function __construct($puid, $tid = 0, $cid = 0, $sku, $title, $price, Tax $taxclass, $qty, $isNetPrice = FALSE) {
		$this->puid = $puid;
		$this->tid = $tid;
		$this->cid = $cid;
		$this->sku = $sku;
		$this->title = $title;
		$this->price = floatval(str_replace(',', '.', $price));
		$this->taxClass = $taxclass;
		$this->qty = $qty;

		$this->min = 0;
		$this->max = 0;

		$this->isNetPrice = $isNetPrice;

		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
	}

	/**
	 * @param boolean
	 */
	public function setIsNetPrice($isNetPrice) {
		$this->isNettoPrice = $isNetPrice;
	}

	/**
	 * @return boolean
	 */
	public function getIsNetPrice() {
		return $this->isNetPrice;
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
			$newVariant->setProduct($this);
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
	 * @return array
	 */
	public function getVariants() {
		return $this->variants;
	}

	/**
	 * @param $variantId
	 * @return array
	 */
	public function getVariantById($variantId) {
		return $this->variants[$variantId];
	}

	/**
	 * @param $variantsArray
	 * @return bool|int
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

	/**
	 * @param $variantId
	 * @return array
	 */
	public function removeVariantById($variantId) {
		unset($this->variants[$variantId]);

		$this->reCalc();
	}

	private function isInRange($newQty) {
		if (($this->min > $newQty) && ($this->min > 0)) {
			return FALSE;
		}
		if (($this->max < $newQty) && ($this->max > 0)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param $id
	 * @param $newQty
	 */
	public function changeVariantById($variantId, $newQty) {
		$this->variants[$variantId]->changeQty($newQty);

		$this->reCalc();
	}

	public function getPuid() {
		return $this->puid;
	}

	public function getTid() {
		return $this->tid;
	}

	public function getCid() {
		return $this->cid;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getPriceTax() {
		return ($this->price / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
	}

	public function getTaxClass() {
		return $this->taxClass;
	}

	public function changeQty($newQty) {
		if (($this->min > $newQty) && ($this->min > 0)) {
			$newQty = $this->min;
		}
		if (($this->max < $newQty) && ($this->max > 0)) {
			$newQty = $this->max;
		}

		if ($this->qty != $newQty) {
			$this->qty = $newQty;

			$this->reCalc();
		}
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
		$this->calcTax();
		return array('taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax);
	}

	public function getMin() {
		return $this->min;
	}

	public function setMin($min) {
		$this->min = $min;
	}

	public function getMax() {
		return $this->max;
	}

	public function setMax($max) {
		$this->max = $max;
	}

	public function checkMin() {
		if (($this->min > 0) && ($this->min > $this->qty)) {
			$this->qty = $this->min;

			if ($this->variants) {
				foreach ($this->variants as $variant) {
					$variant->changeQty($this->min);
				}
			}

			$this->reCalc();
			$this->error = 'check_min';
			return FALSE;
		}

		return TRUE;
	}

	public function checkMax() {
		if (($this->max > 0) && ($this->max < $this->qty)) {
			$this->qty = $this->max;

			if ($this->variants) {
				foreach ($this->variants as $variant) {
					$variant->changeQty($this->max);
				}
			}

			$this->reCalc();
			$this->error = 'check_max';
			return FALSE;
		}

		return TRUE;
	}

	public function getError() {
		return $this->error;
	}

	/**
	 * @return float
	 */
	public function getServiceAttribute1() {
		return $this->serviceAttribute1;
	}

	/**
	 * @param float $serviceAttribute1
	 */
	public function setServiceAttribute1($serviceAttribute1) {
		$this->serviceAttribute1 = floatval($serviceAttribute1);
	}

	/**
	 * @return float
	 */
	public function getServiceAttribute2() {
		return $this->serviceAttribute2;
	}

	/**
	 * @param float $serviceAttribute2
	 */
	public function setServiceAttribute2($serviceAttribute2) {
		$this->serviceAttribute2 = floatval($serviceAttribute2);
	}

	/**
	 * @return float
	 */
	public function getServiceAttribute3() {
		return $this->serviceAttribute3;
	}

	/**
	 * @param float $serviceAttribute3
	 */
	public function setServiceAttribute3($serviceAttribute3) {
		$this->serviceAttribute3 = floatval($serviceAttribute3);
	}

	// temp function, should remove later
	public function getProductAsArray() {
		return array('puid' => $this->puid, 'tid' => $this->tid, 'cid' => $this->cid, 'sku' => $this->sku, 'title' => $this->title, 'price' => $this->price, 'taxclass' => $this->taxClass, 'qty' => $this->qty, 'min' => $this->min, 'max' => $this->max, 'price_total' => $this->gross, 'price_total_gross' => $this->gross, 'price_total_net' => $this->net, 'tax' => $this->tax);
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
			$out = $this->getProductAsArray();

			t3lib_div::devLog('product', 'wt_cart', 0, $out);
		}
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
				$this->gross = $this->price * $this->qty;
			}
		} else {
			$this->calcNet();
			$this->calcTax();
			$this->gross = $this->net + $this->tax;
		}
	}

	private function calcTax() {
		if ($this->isNetPrice == FALSE) {
			$this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
		} else {
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
				$this->net = $this->price * $this->qty;
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

		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
	}
}

?>
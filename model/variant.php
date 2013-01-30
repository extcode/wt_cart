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
 * @author	Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package	TYPO3
 * @subpackage	tx_wtcart
 * @version	1.5.0
 */
class Variant {
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
	 * @param $name
	 * @param $sku
	 * @param $qty
	 * @param $isNetPrice
	 * @return Variant
	 */
	public function __construct($id, $title, $sku, $price, Tax $taxclass, $qty, $isNetPrice = FALSE) {
		$this->id = $id;
		$this->title = $title;
		$this->sku = $sku;
		$this->price = floatval(str_replace(',' , '.', $price));
		$this->taxClass = $taxclass;
		$this->qty = $qty;

		$this->isNetPrice = $isNetPrice;

		$this->reCalc();
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
	 * @param $price
	 */
	public function setPrice($price) {
		$this->price = $price;

		$this->reCalc();
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
			$this->net =  $this->gross - $this->tax;
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
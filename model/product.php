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
	 * $id = Product identifier defines the unique identifier each product have
	 *
	 * @var integer
	 */
	private $id;

	/**
	 * $parentTable = parent object association table name defines an association a
	 * product can have if needed in a hook
	 * @var string
	 */
	private $parentTable;

	/**
	 * $parentId = parent object association identifier defines an association a
	 * product can have if needed in a hook
	 * @var integer
	 */
	private $parentId;

	/**
	 * $tid = Table configuration Id is defined by TypoScript and is used to
	 * define the table the product comes from
	 *
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
	 * @var array Additional
	 */
	private $additional;

	/**
	 * __construct
	 *
	 * @param $id
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
	public function __construct($id, $tid = 0, $cid = 0, $sku, $title, $price, Tax $taxclass, $qty, $isNetPrice = FALSE) {
		$this->id = $id;
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
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return boolean
	 */
	public function getIsNetPrice() {
		return $this->isNetPrice;
	}

	/**
	 * @param boolean
	 * @return void
	 */
	public function setIsNetPrice($isNetPrice) {
		$this->isNettoPrice = $isNetPrice;
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
	 * @return Variant
	 */
	public function getVariantById($variantId) {
		return $this->variants[$variantId];
	}

	/**
	 * @param $variantId
	 * @return Variant
	 */
	public function getVariant($variantId) {
		return $this->getVariantById($variantId);
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

	/**
	 * @return int
	 * @deprecated since wt_cart 2.1; will be removed in wt_cart 3.0; use Pid instead
	 */
	public function getPuid() {
		return $this->getId();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getTid() {
		return $this->tid;
	}

	/**
	 * @return string
	 */
	public function getTidPid() {
		return join('_', array($this->tid, $this->id));
	}

	/**
	 * @return string
	 */
	public function getParentTable() {
		return $this->parentTable;
	}

	/**
	 * @param $parentTable
	 * @return void
	 */
	public function setParentTable($parentTable) {
		$this->parentTable = $parentTable;
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parentId;
	}

	/**
	 * @param $parentId
	 * @return void
	 */
	public function setParentId($parentId) {
		$this->parentId = $parentId;
	}

	/**
	 * @return int
	 */
	public function getCid() {
		return $this->cid;
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
	public function getPriceTax() {
		return ($this->price / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
	}

	/**
	 * @return Tax
	 */
	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 * @param $newQty
	 */
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
		$this->calcTax();
		return array('taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax);
	}

	/**
	 * @return int
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @param $min
	 * @return void
	 */
	public function setMin($min) {
		$this->min = $min;
	}

	/**
	 * @return int
	 */
	public function getMax() {
		return $this->max;
	}

	/**
	 * @param $max
	 * @return void
	 */
	public function setMax($max) {
		$this->max = $max;
	}

	/**
	 * @return bool
	 */
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

	/**
	 * @return bool
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @deprecated since wt_cart 2.1; will be removed in wt_cart 3.0; use toArray instead
	 * @return array
	 */
	public function getProductAsArray() {
		return $this->toArray();
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return array(
			'puid' => $this->id,
			'parentTable' => $this->parentTable,
			'parentId' => $this->parentId,
			'tid' => $this->tid,
			'cid' => $this->cid,
			'sku' => $this->sku,
			'title' => $this->title,
			'price' => $this->price,
			'taxclass' => $this->taxClass,
			'qty' => $this->qty,
			'min' => $this->min,
			'max' => $this->max,
			'price_total' => $this->gross,
			'price_total_gross' => $this->gross,
			'price_total_net' => $this->net,
			'tax' => $this->tax,
			'additional' => $this->additional
		);
	}

	/**
	 * @return void
	 */
	public function debug() {
		if (TYPO3_DLOG) {
			if ($this->variants) {
				foreach ($this->variants as $variant) {
					$variant->debug();
				}
			}

			$out = $this->toArray();

			t3lib_div::devLog('product', 'wt_cart', 0, $out);
		}
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
				$this->gross = $this->price * $this->qty;
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
			$this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
		} else {
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
				$this->net = $this->price * $this->qty;
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

		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
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
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


require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Extra.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Tax.php');

/**
 * Plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author    Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
abstract class Tx_WtCart_Domain_Model_Service {
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var Tx_WtCart_Domain_Model_Tax
	 */
	private $taxClass;

	/**
	 * @var string
	 */
	private $note;

	/**
	 * @var string
	 */
	private $extratype;

	/**
	 * @var Tx_WtCart_Domain_Model_Extra
	 */
	private $extras;

	/**
	 * @var float
	 */
	private $freeFrom;

	/**
	 * @var float
	 */
	private $freeUntil;

	/**
	 * @var float
	 */
	private $availableFrom;

	/**
	 * @var float
	 */
	private $availableUntil;

	/**
	 * @var boolean
	 */
	private $isNetPrice;

	/**
	 * @var boolean
	 */
	private $isPreset;

	/**
	 * @var array Additional
	 */
	private $additional;

	/**
	 * __construct
	 */
	public function __construct($id, $name, Tx_WtCart_Domain_Model_Tax $taxclass, $note, $isNetPrice) {
		$this->id = $id;
		$this->name = $name;
		$this->taxClass = $taxclass;
		$this->note = $note;
		$this->isNetPrice = $isNetPrice;
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
	 * @param boolean
	 */
	public function setIsPreset($isPreset) {
		$this->isPreset = $isPreset;
	}

	/**
	 * @return boolean
	 */
	public function getIsPreset() {
		return $this->isPreset;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param $cart
	 * @return float
	 */
	public function getGross($cart) {
		$gross = 0.0;

		$condition = $this->getConditionFromCart($cart);

		if (isset($condition)) {
			foreach ($this->extras as $extra) {
				if ($extra->leq($condition)) {
					$gross = $extra->getGross();
				} else {
					break;
				}
			}
		} else {
			$gross = $this->extras[0]->getGross();
			if ($this->getExtratype() == 'each') {
				$gross = $cart->getCount() * $gross;
			}
		}

		return $gross;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param $cart
	 * @return float
	 */
	public function getNet($cart) {
		$net = 0.0;

		$condition = $this->getConditionFromCart($cart);

		if (isset($condition)) {
			foreach ($this->extras as $extra) {
				if ($extra->leq($condition)) {
					$net = $extra->getNet();
				} else {
					break;
				}
			}
		} else {
			$net = $this->extras[0]->getNet();
			if ($this->getExtratype() == 'each') {
				$net = $cart->getCount() * $net;
			}
		}

		return $net;
	}

	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @param $cart
	 * @return float
	 */
	public function getTax($cart) {
		$tax = 0.0;

		$condition = $this->getConditionFromCart($cart);

		if (isset($condition)) {
			foreach ($this->extras as $extra) {
				if ($extra->leq($condition)) {
					$tax = $extra->getTax();
				} else {
					return $tax;
				}
			}
		} else {
			$tax = $this->extras[0]->getTax();
			if ($this->getExtratype() == 'each') {
				$tax = $cart->getCount() * $tax;
			}
		}
		return $tax;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Tax
	 */
	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Extra
	 */
	public function getExtras() {
		return $this->extras;
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Extra $newextra
	 */
	public function addExtra(Tx_WtCart_Domain_Model_Extra $newextra) {
		$this->extras[] = $newextra;
	}

	/**
	 * @return string
	 */
	public function getExtraType() {
		return $this->extratype;
	}

	/**
	 * @param $extratype
	 */
	public function setExtraType($extratype) {
		$this->extratype = $extratype;
	}

	/**
	 * @return float
	 */
	public function getFreeFrom() {
		return $this->freeFrom;
	}

	/**
	 * @param $freeFrom
	 */
	public function setFreeFrom($freeFrom) {
		$this->freeFrom = $freeFrom;
	}

	/**
	 * @return float
	 */
	public function getFreeUntil() {
		return $this->freeUntil;
	}

	/**
	 * @param $freeUntil
	 */
	public function setFreeUntil($freeUntil) {
		$this->freeUntil = $freeUntil;
	}

	/**
	 * @return float
	 */
	public function getAvailableFrom() {
		return $this->availableFrom;
	}

	/**
	 * @param $availableFrom
	 */
	public function setAvailableFrom($availableFrom) {
		$this->availableFrom = $availableFrom;
	}

	/**
	 * @return float
	 */
	public function getAvailableUntil() {
		return $this->availableUntil;
	}

	/**
	 * @param $availableUntil
	 */
	public function setAvailableUntil($availableUntil) {
		$this->availableUntil = $availableUntil;
	}

	/**
	 * @param $price
	 * @return bool
	 */
	public function isFree($price) {
		if (isset($this->freeFrom) || isset($this->freeUntil)) {
			if (isset($this->freeFrom) && $price < $this->freeFrom) {
				return FALSE;
			}
			if (isset($this->freeUntil) && $price > $this->freeUntil) {
				return FALSE;
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @param $price
	 * @return bool
	 */
	public function isAvailable($price) {
		if (isset($this->availableFrom) && $price < $this->availableFrom) {
			return FALSE;
		}
		if (isset($this->availableUntil) && $price > $this->availableUntil) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param $cart
	 * @return null
	 */
	private function getConditionFromCart($cart) {
		$condition = NULL;

		if (!$this->isFree($cart->getGross())) {
			switch ($this->getExtratype()) {
				case 'by_price':
					$condition = $cart->getGross();
					break;
				case 'by_quantity':
					$condition = $cart->getCount();
					break;
				case 'by_service_attribute_1_sum':
					$condition = $cart->getSumServiceAttribute1();
					break;
				case 'by_service_attribute_1_max':
					$condition = $cart->getMaxServiceAttribute1();
					break;
				case 'by_service_attribute_2_sum':
					$condition = $cart->getSumServiceAttribute2();
					break;
				case 'by_service_attribute_2_max':
					$condition = $cart->getMaxServiceAttribute2();
					break;
				case 'by_service_attribute_3_sum':
					$condition = $cart->getSumServiceAttribute3();
					break;
				case 'by_service_attribute_3_max':
					$condition = $cart->getMaxServiceAttribute3();
					break;
				default:
			}
		}

		return $condition;
	}

	/**
	 * @return array
	 */
	public function getAdditionalArray() {
		return $this->additional;
	}

	/**
	 * @param array $additional
	 * @return void
	 */
	public function setAdditionalArray($additional) {
		$this->additional = $additional;
	}

	/**
	 * @return void
	 */
	public function unsetAdditionalArray() {
		$this->additional = array();
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

	/**
	 * @param string $key
	 * @return void
	 */
	public function unsetAdditional($key) {
		if ($this->additional[$key]) {
			unset($this->additional[$key]);
		}
	}
}

?>
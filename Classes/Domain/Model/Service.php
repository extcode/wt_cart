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

/**
 * Plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author    Daniel Lorenz <daniel.lorenz@extco.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
abstract class Tx_WtCart_Domain_Model_Service {

	/**
	 * @var Tx_WtCart_Domain_Model_Cart
	 */
	private $cart;

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
	private $status = 0;

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
	 * __construct
	 */
	public function __construct($id, $name, Tx_WtCart_Domain_Model_Tax $taxclass, $status, $note, $isNetPrice) {
		$this->id = $id;
		$this->name = $name;
		$this->taxClass = $taxclass;
		$this->status = $status;
		$this->note = $note;
		$this->isNetPrice = $isNetPrice;
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Cart $cart
	 * @return void
	 */
	public function setCart( $cart ) {
		$this->cart = $cart;
		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
	}

	/**
	 * @return void
	 */
	public function calcAll() {
		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
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
	 * @return float
	 */
	public function getGross() {
		$this->calcGross();
		return $this->gross;
	}

	/**
	 * @return void
	 */
	public function calcGross() {
		$gross = 0.0;

		$condition = $this->getConditionFromCart();

		if (isset($condition)) {
			if ($condition === 0.0) {
				$gross = 0.0;
			} else {
				foreach ($this->extras as $extra) {
					if ($extra->leq($condition)) {
						$gross = $extra->getGross();
					} else {
						break;
					}
				}
			}
		} else {
			$gross = $this->extras[0]->getGross();
			if ($this->getExtraType() == 'each') {
				$gross = $this->cart->getCount() * $gross;
			}
		}

		$this->gross = $gross;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return float
	 */
	public function getNet() {
		$this->calcNet();
		return $this->net;
	}

	/**
	 * @return void
	 */
	private function calcNet() {
		$net = 0.0;

		$condition = $this->getConditionFromCart();

		if ( isset($condition) ) {
			if ($condition === 0.0) {
				$net = 0.0;
			} else {
				foreach ($this->extras as $extra) {
					if ($extra->leq($condition)) {
						$net = $extra->getNet();
					} else {
						break;
					}
				}
			}
		} else {
			$net = $this->extras[0]->getNet();
			if ($this->getExtraType() == 'each') {
				$net = $this->cart->getCount() * $net;
			}
		}

		$this->net = $net;
	}

	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @return float
	 */
	public function getTax() {
		$this->calcTax();
		return $this->tax;
	}

	/**
	 * @return void
	 */
	private function calcTax() {
		$tax = 0.0;

		$condition = $this->getConditionFromCart();

		if (isset($condition)) {
			if ($condition === 0.0) {
				$tax = 0.0;
			} else {
				foreach ($this->extras as $extra) {
					if ($extra->leq($condition)) {
						$tax = $extra->getTax();
						$tax = $tax['tax'];
					} else {
						break;
					}
				}
			}
		} else {
			$tax = $this->extras[0]->getTax();
			if ($this->getExtraType() == 'each') {
				$tax = $this->cart->getCount() * $tax['tax'];
			} else {
				$tax = $tax['tax'];
			}
		}

		$this->tax = $tax;
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
	 * @param string $extratype
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
	 * @return null
	 */
	private function getConditionFromCart() {
		$condition = NULL;

		if ( $this->isFree($this->cart->getGross()) ) {
			return 0.0;
		}

		switch ($this->getExtraType()) {
			case 'by_price':
				$condition = $this->cart->getGross();
				break;
			case 'by_quantity':
				$condition = $this->cart->getCount();
				break;
			case 'by_service_attribute_1_sum':
				$condition = $this->cart->getSumServiceAttribute1();
				break;
			case 'by_service_attribute_1_max':
				$condition = $this->cart->getMaxServiceAttribute1();
				break;
			case 'by_service_attribute_2_sum':
				$condition = $this->cart->getSumServiceAttribute2();
				break;
			case 'by_service_attribute_2_max':
				$condition = $this->cart->getMaxServiceAttribute2();
				break;
			case 'by_service_attribute_3_sum':
				$condition = $this->cart->getSumServiceAttribute3();
				break;
			case 'by_service_attribute_3_max':
				$condition = $this->cart->getMaxServiceAttribute3();
				break;
			default:
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

	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}
}

?>
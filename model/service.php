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

require_once(t3lib_extMgm::extPath('wt_cart') . 'model/extra.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'model/tax.php');

/**
 * Plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author    Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
abstract class Service {
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var Tax
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
	 * @var Extra
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
	 * __construct
	 */
	public function __construct($id, $name, Tax $taxclass, $note, $isNetPrice) {
		$this->id = $id;
		$this->name = $name;
		$this->taxClass = $taxclass;
		$this->note = $note;
		$this->isNetPrice = $isNetPrice;
	}

	/**
	 * @param boolean
	 */
	public function setisNetPrice($isNetPrice) {
		$this->isNetPrice = $isNetPrice;
	}

	/**
	 * @return boolean
	 */
	public function getisNetPrice() {
		return $this->isNetPrice;
	}

	public function getId() {
		return $this->id;
	}

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

	public function getName() {
		return $this->name;
	}

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

	public function getNote() {
		return $this->note;
	}

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

	public function getTaxClass() {
		return $this->taxClass;
	}

	public function getExtras() {
		return $this->extras;
	}

	public function addExtra(Extra $newextra) {
		$this->extras[] = $newextra;
	}

	public function getExtraType() {
		return $this->extratype;
	}

	public function setExtraType($extratype) {
		$this->extratype = $extratype;
	}

	public function getFreeFrom() {
		return $this->freeFrom;
	}

	public function setFreeFrom($freeFrom) {
		$this->freeFrom = $freeFrom;
	}

	public function getFreeUntil() {
		return $this->freeUntil;
	}

	public function setFreeUntil($freeUntil) {
		$this->freeUntil = $freeUntil;
	}

	public function getAvailableFrom() {
		return $this->availableFrom;
	}

	public function setAvailableFrom($availableFrom) {
		$this->availableFrom = $availableFrom;
	}

	public function getAvailableUntil() {
		return $this->availableUntil;
	}

	public function setAvailableUntil($availableUntil) {
		$this->availableUntil = $availableUntil;
	}

	public function isFree($price) {
		if (isset($this->freeFrom) || isset($this->freeUntil)) {
			if (isset($this->freeFrom) && $price < $this->freeFrom) {
				return 0;
			}
			if (isset($this->freeUntil) && $price > $this->freeUntil) {
				return 0;
			}

			return TRUE;
		}

		return 0;
	}

	public function isAvailable($price) {
		if (isset($this->availableFrom) && $price < $this->availableFrom) {
			return FALSE;
		}
		if (isset($this->availableUntil) && $price > $this->availableUntil) {
			return FALSE;
		}

		return TRUE;
	}

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
}

?>
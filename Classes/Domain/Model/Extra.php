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

require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Tax.php');

/**
 * Plugin 'Cart' for the 'wt_cart' extension.
 *
 * @author    Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
 * @package    TYPO3
 * @subpackage    tx_wtcart
 * @version    1.5.0
 */
class Tx_WtCart_Domain_Model_Extra {
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var float
	 */
	private $condition;

	/**
	 * @var float
	 */
	private $price;

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
	 * __construct
	 *
	 * @param $id
	 * @param $condition
	 * @param $price
	 * @param Tx_WtCart_Domain_Model_Tax $taxclass
	 * @param bool $isNetPrice
	 * @internal param $gross
	 * @return \Tx_WtCart_Domain_Model_Extra
	 */
	public function __construct($id, $condition, $price, Tx_WtCart_Domain_Model_Tax $taxclass, $isNetPrice = FALSE) {
		$this->id = $id;
		$this->condition = $condition;
		$this->taxClass = $taxclass;
		$this->price = str_replace($LocaleInfo["mon_decimal_point"] , ".", $price);

		$this->isNetPrice = $isNetPrice;

		$this->reCalc();
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
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return float
	 */
	public function getCondition() {
		return $this->condition;
	}

	/**
	 * @param $condition
	 * @return bool
	 */
	public function leq($condition) {
		if ($condition < $this->condition) {
			return FALSE;
		}

		return TRUE;
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
	 * @return Tx_WtCart_Domain_Model_Tax
	 */
	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 *
	 */
	private function calcGross() {
		if ($this->isNetPrice == FALSE) {
			$this->gross = $this->price;
		} else {
			$this->calcNet();
			$this->gross = $this->net + $this->tax;
		}
	}

	/**
	 *
	 */
	private function calcTax() {
		if ($this->isNetPrice == FALSE) {
			$this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
		} else {
			$this->tax = ($this->net * $this->taxClass->getCalc());
		}
	}

	/**
	 *
	 */
	private function calcNet() {
		if ($this->isNetPrice == TRUE) {
			$this->net = $this->price;
		} else {
			$this->calcGross();
			$this->net =  $this->gross - $this->tax;
		}
	}

	/**
	 *
	 */
	private function reCalc() {
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
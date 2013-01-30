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
class Tax {
	private $id;
	private $value;
	private $calc;
	private $name;

	/**
	 * __construct
	 *
	 * @param $id
	 * @param $value
	 * @param $calc
	 * @param $name
	 * @return \Tax
	 */
	public function __construct($id, $value, $calc, $name) {
		$this->id = $id;
		$this->value = str_replace($LocaleInfo["mon_decimal_point"] , ".", $value);
		$this->calc = $calc;
		$this->name = $name;
	}

	public function getId() {
		return $this->id;
	}

	public function getValue() {
		return $this->value;
	}

	public function getCalc() {
		return $this->calc;
	}

	public function getName() {
		return $this->name;
	}
}

?>
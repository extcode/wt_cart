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

class Tx_WtCart_Domain_Model_TaxTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var \Tx_WtCart_Domain_Model_Tax
	 */
	protected $fixture = NULL;

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @var integer
	 */
	private $calc;

	/**
	 * @var string
	 */
	private $name;

	/**
	 *
	 */
	public function setUp() {
		$this->id = 1;
		$this->value = '19';
		$this->calc = 0.19;
		$this->name = 'normal Tax';

		$this->fixture = new Tx_WtCart_Domain_Model_Tax( $this->id, $this->value, $this->calc, $this->name );
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->id);
		unset($this->value);
		unset($this->calc);
		unset($this->name);

		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutIdThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $id for constructor.',
			1413981328
		);

		$product = new Tx_WtCart_Domain_Model_Tax( NULL, $this->value, $this->calc, $this->name );
	}

	/**
	 * @test
	 */
	public function constructProductWithoutValueThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $value for constructor.',
			1413981329
		);

		$product = new Tx_WtCart_Domain_Model_Tax( $this->id, NULL, $this->calc, $this->name );
	}

	/**
	 * @test
	 */
	public function constructProductWithoutCalcThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $calc for constructor.',
			1413981330
		);

		$product = new Tx_WtCart_Domain_Model_Tax( $this->id, $this->value, NULL, $this->name );
	}

	/**
	 * @test
	 */
	public function constructProductWithoutNameThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $name for constructor.',
			1413981331
		);

		$product = new Tx_WtCart_Domain_Model_Tax( $this->id, $this->value, $this->calc, NULL );
	}

	/**
	 * @test
	 */
	public function getIdReturnsIdSetByConstructor() {
		$this->assertSame(
			$this->id,
			$this->fixture->getId()
		);
	}

	/**
	 * @test
	 */
	public function getValueReturnsValueSetByConstructor() {
		$this->assertSame(
			$this->value,
			$this->fixture->getValue()
		);
	}

	/**
	 * @test
	 */
	public function getCalcReturnsCalcSetByConstructor() {
		$this->assertSame(
			$this->calc,
			$this->fixture->getCalc()
		);
	}

	/**
	 * @test
	 */
	public function getNameReturnsNameSetByConstructor() {
		$this->assertSame(
			$this->name,
			$this->fixture->getName()
		);
	}
}

?>
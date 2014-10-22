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

class Tx_WtCart_Domain_Model_CartTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var \Tx_WtCart_Domain_Model_Cart
	 */
	protected $fixture = NULL;

	/**
	 *
	 */
	public function setUp() {
		$this->fixture = new Tx_WtCart_Domain_Model_Cart();
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getNetInitiallyReturnsZero() {
		$this->assertSame(
			0.0,
			$this->fixture->getNet()
		);
	}

	/**
	 * @test
	 */
	public function getGrossInitiallyReturnsZero() {
		$this->assertSame(
			0.0,
			$this->fixture->getGross()
		);
	}

	/**
	 * @test
	 */
	public function getCountInitiallyReturnsZero() {
		$this->assertSame(
			0,
			$this->fixture->getCount()
		);
	}

	/**
	 * @test
	 */
	public function getProductsInitiallyReturnsEmptyArray() {
		$this->assertCount(
			0,
			$this->fixture->getProducts()
		);
	}

	/**
	 * @test
	 */
	public function setInitiallyOrderNumberSetsOrderNumber() {
		$this->fixture->setOrderNumber('ValidOrderNumber');

		$this->assertSame(
			'ValidOrderNumber',
			$this->fixture->getOrderNumber()
		);
	}

	/**
	 * @test
	 */
	public function resetSameOrderNumberSetsOrderNumber() {
		$this->fixture->setOrderNumber('ValidOrderNumber');

		$this->fixture->setOrderNumber('ValidOrderNumber');

		$this->assertSame(
			'ValidOrderNumber',
			$this->fixture->getOrderNumber()
		);
	}

	/**
	 * @test
	 */
	public function resetDifferentOrderNumberThrowsException() {
		$this->fixture->setOrderNumber('ValidOrderNumber');

		$this->setExpectedException(
			'LogicException',
			'You can not redeclare the order number of your cart.',
			1413969668
		);

		$this->fixture->setOrderNumber('NotValidOrderNumber');
	}

	/**
	 * @test
	 */
	public function setInitiallyInvoiceNumberSetsInvoiceNumber() {
		$this->fixture->setInvoiceNumber('ValidInvoiceNumber');

		$this->assertSame(
			'ValidInvoiceNumber',
			$this->fixture->getInvoiceNumber()
		);
	}

	/**
	 * @test
	 */
	public function resetSameInvoiceNumberSetsInvoiceNumber() {
		$this->fixture->setInvoiceNumber('ValidInvoiceNumber');

		$this->fixture->setInvoiceNumber('ValidInvoiceNumber');

		$this->assertSame(
			'ValidInvoiceNumber',
			$this->fixture->getInvoiceNumber()
		);
	}

	/**
	 * @test
	 */
	public function resetDifferentInvoiceNumberThrowsException() {
		$this->fixture->setInvoiceNumber('ValidInvoiceNumber');

		$this->setExpectedException(
			'LogicException',
			'You can not redeclare the invoice number of your cart.',
			1413969712
		);

		$this->fixture->setInvoiceNumber('NotValidInvoiceNumber');
	}

	/**
	 * @test
	 */
	public function addFirstProductToCartChangeCountOfProducts() {
		$taxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, 0.19, 'normal' );
		$product = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1, 'First Product', 10.00, $taxClass, 1);

		$this->fixture->addProduct( $product );

		$this->assertSame(
			1,
			$this->fixture->getCount()
		);
	}

	/**
	 * @test
	 */
	public function addFirstProductToCartChangeNetOfCart() {
		$taxCalc = 0.19;
		$taxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, $taxCalc, 'normal' );
		$productPrice = 10.00;
		$product = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1, 'First Product', $productPrice, $taxClass, 1);

		$this->fixture->addProduct( $product );

		$this->assertSame(
			$productPrice / (1 + $taxCalc),
			$this->fixture->getNet()
		);
	}

	/**
	 * @test
	 */
	public function addFirstProductToCartChangeGrossOfCart() {
		$taxCalc = 0.19;
		$taxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, $taxCalc, 'normal' );
		$productPrice = 10.00;
		$product = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1, 'First Product', $productPrice, $taxClass, 1);

		$this->fixture->addProduct( $product );

		$this->assertSame(
			$productPrice,
			$this->fixture->getGross()
		);
	}

	/**
	 * @test
	 */
	public function addFirstProductToCartChangeTaxArray() {
		$taxCalc = 0.19;
		$taxId = 1;
		$taxClass = new Tx_WtCart_Domain_Model_Tax( $taxId , 19, $taxCalc, 'normal' );
		$productPrice = 10.00;
		$product = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1, 'First Product', $productPrice, $taxClass, 1);

		$this->fixture->addProduct( $product );

		$cartTaxes = $this->fixture->getTaxes();

		$this->assertSame(
			$productPrice - ($productPrice / (1 + $taxCalc)),
			$cartTaxes[$taxId]
		);
	}

	/**
	 * @test
	 */
	public function addSecondProductWithSameTaxClassToCartChangeTaxArray() {
		$firstTaxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, 0.19, 'normal' );

		$firstProductPrice = 10.00;
		$firstProduct = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1001, 'First Product', $firstProductPrice, $firstTaxClass, 1);
		$this->fixture->addProduct( $firstProduct );

		$secondProductPrice = 20.00;
		$secondProduct = new Tx_WtCart_Domain_Model_Product( 2, 0, 0, 1002, 'Second Product', $secondProductPrice, $firstTaxClass, 1);
		$this->fixture->addProduct( $secondProduct );

		$cartTaxes = $this->fixture->getTaxes();

		$this->assertSame(
			($firstProductPrice + $secondProductPrice) - ( ($firstProductPrice + $secondProductPrice) / ( 1 + $firstTaxClass->getCalc() ) ),
			$cartTaxes[ $firstTaxClass->getId() ]
		);
	}

	/**
	 * @test
	 */
	public function addSecondProductWithDifferentTaxClassToCartChangeTaxArray() {
		$firstTaxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, 0.19, 'normal' );

		$firstProductPrice = 10.00;
		$firstProduct = new Tx_WtCart_Domain_Model_Product( 1, 0, 0, 1001, 'First Product', $firstProductPrice, $firstTaxClass, 1);
		$this->fixture->addProduct( $firstProduct );

		$secondTaxClass = new Tx_WtCart_Domain_Model_Tax( 2 , 7, 0.7, 'reduced' );

		$secondProductPrice = 20.00;
		$secondProduct = new Tx_WtCart_Domain_Model_Product( 2, 0, 0, 1002, 'Second Product', $secondProductPrice, $secondTaxClass, 1);
		$this->fixture->addProduct( $secondProduct );

		$cartTaxes = $this->fixture->getTaxes();

		$this->assertSame(
			$firstProductPrice - ( $firstProductPrice / ( 1 + $firstTaxClass->getCalc() ) ),
			$cartTaxes[ $firstTaxClass->getId() ]
		);
		$this->assertSame(
			$secondProductPrice - ( $secondProductPrice / ( 1 + $secondTaxClass->getCalc() ) ),
			$cartTaxes[ $secondTaxClass->getId() ]
		);
	}

}

?>
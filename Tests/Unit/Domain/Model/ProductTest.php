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

class Tx_WtCart_Domain_Model_ProductTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var \Tx_WtCart_Domain_Model_Tax
	 */
	protected $taxClass = NULL;

	/**
	 * @var \Tx_WtCart_Domain_Model_Product
	 */
	protected $fixture = NULL;

	/**
	 * @var integer
	 */
	private $productId;

	/**
	 * @var integer
	 */
	private $tableId;

	/**
	 * @var integer
	 */
	private $contentId;

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
	 * @var integer
	 */
	private $qty;

	/**
	 *
	 */
	public function setUp() {
		$this->taxClass = new Tx_WtCart_Domain_Model_Tax( 1 , 19, 0.19, 'normal' );

		$this->productId = 1001;
		$this->tableId = 1002;
		$this->contentId = 1003;
		$this->title = 'Test Product';
		$this->sku = 'test-product-sku';
		$this->price = 10.00;
		$this->qty = 1;

		$this->fixture = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, $this->sku, $this->title, $this->price, $this->taxClass, $this->qty);
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->fixture);

		unset($this->productId);
		unset($this->tableId);
		unset($this->contentId);
		unset($this->title);
		unset($this->sku);
		unset($this->price);
		unset($this->qty);

		unset($this->taxClass);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutProductIdThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $productId for constructor.',
			1413999100
		);

		$product = new Tx_WtCart_Domain_Model_Product( NULL, $this->tableId, $this->contentId, $this->sku, $this->title, $this->price, $this->taxClass, $this->qty);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutSkuThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $sku for constructor.',
			1413999110
		);

		$product = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, NULL, $this->title, $this->price, $this->taxClass, $this->qty);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutTitleThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $title for constructor.',
			1413999120
		);

		$product = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, $this->sku, NULL, $this->price, $this->taxClass, $this->qty);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutPriceThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $price for constructor.',
			1413999130
		);

		$product = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, $this->sku, $this->title, NULL, $this->taxClass, $this->qty);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutTaxClassThrowsException() {
		$this->setExpectedException(
			'PHPUnit_Framework_Error'
		);

		$product = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, $this->sku, $this->title, $this->price, NULL, $this->qty);
	}

	/**
	 * @test
	 */
	public function constructProductWithoutQtyThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'You have to specify a valid $qty for constructor.',
			1413999150
		);

		$product = new Tx_WtCart_Domain_Model_Product( $this->productId, $this->tableId, $this->contentId, $this->sku, $this->title, $this->price, $this->taxClass, NULL);
	}

	/**
	 * @test
	 */
	public function getProductIdReturnsProductIdSetByConstructor() {
		$this->assertSame(
			$this->productId,
			$this->fixture->getProductId()
		);
	}

	/**
	 * @test
	 */
	public function getTableIdReturnsTableIdSetByConstructor() {
		$this->assertSame(
			$this->tableId,
			$this->fixture->getTableId()
		);
	}

	/**
	 * @test
	 */
	public function getTableProductIdReturnsTableProductIdSetIndirectlyByConstructor() {
		$this->assertSame(
			$this->tableId . '_' . $this->productId,
			$this->fixture->getTableProductId()
		);
	}

	/**
	 * @test
	 */
	public function getContentIdReturnsContentIdSetByConstructor() {
		$this->assertSame(
			$this->contentId,
			$this->fixture->getContentId()
		);
	}

	/**
	 * @test
	 */
	public function getSkuReturnsSkuSetByConstructor() {
		$this->assertSame(
			$this->sku,
			$this->fixture->getSku()
		);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsTitleSetByConstructor() {
		$this->assertSame(
			$this->title,
			$this->fixture->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function getPriceReturnsPriceSetByConstructor() {
		$this->assertSame(
			$this->price,
			$this->fixture->getPrice()
		);
	}

	/**
	 * @test
	 */
	public function getQtyReturnsQtySetByConstructor() {
		$this->assertSame(
			$this->qty,
			$this->fixture->getQty()
		);
	}
}

?>
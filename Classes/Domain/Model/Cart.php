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

require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Payment.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Product.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Tax.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Special.php');

/**
* Plugin 'Cart' for the 'wt_cart' extension.
*
* @author	Daniel Lorenz <daniel.lorenz@capsicum-ug.de>
* @package	TYPO3
* @subpackage	tx_wtcart
* @version	1.5.0
*/
class Tx_WtCart_Domain_Model_Cart {
	/**
	 * @var float
	 */
	private $net;

	/**
	 * @var float
	 */
	private $gross;

	/**
	 * @var Tx_WtCart_Domain_Model_Tax
	 */
	private $taxes;

	/**
	 * @var integer
	 */
	private $count;

	/**
	 * @var array Tx_WtCart_Domain_Model_Product
	 */
	private $products;

	/**
	 * @var Tx_WtCart_Domain_Model_Shipping
	 */
	private $shipping;

	/**
	 * @var Tx_WtCart_Domain_Model_Payment
	 */
	private $payment;

	/**
	 * @var array Tx_WtCart_Domain_Model_Special
	 */
	private $specials;

	/**
	 * @var float
	 */
	private $maxServiceAttr1;

	/**
	 * @var float
	 */
	private $maxServiceAttr2;

	/**
	 * @var float
	 */
	private $maxServiceAttr3;

	/**
	 * @var float
	 */
	private $sumServiceAttr1;

	/**
	 * @var float
	 */
	private $sumServiceAttr2;

	/**
	 * @var float
	 */
	private $sumServiceAttr3;

	/**
	 * @var boolean
	 */
	private $isNetCart;

	/**
	 * @var string
	 */
	private $orderNumber;

	/**
	 * @var string
	 */
	private $invoiceNumber;

	/**
	 * @var array Additional
	 */
	private $additional = array();

	/**
	 * @var int OrderId
	 */
	private $orderId;

	/**
	 * __construct
	 *
	 * @var boolean
	 * @return Tx_WtCart_Domain_Model_Cart
	 */
	public function __construct($isNetCart = FALSE) {
		$this->net = 0.0;
		$this->gross = 0.0;
		$this->count = 0;
		$this->products = array();

		$this->maxServiceAttr1 = 0.0;
		$this->maxServiceAttr2 = 0.0;
		$this->maxServiceAttr3 = 0.0;
		$this->sumServiceAttr1 = 0.0;
		$this->sumServiceAttr2 = 0.0;
		$this->sumServiceAttr3 = 0.0;

		$this->isNetCart = $isNetCart;
	}

	public function __sleep() {
		return array(
			'net',
			'gross',
			'taxes',
			'count',
			'shipping',
			'payment',
			'specials',
			'service',
			'products',
			'maxServiceAttr1',
			'maxServiceAttr2',
			'maxServiceAttr3',
			'sumServiceAttr1',
			'sumServiceAttr2',
			'sumServiceAttr3',
			'isNetCart',
			'orderId',
			'orderNumber',
			'invoiceNumber',
			'additional'
		);
	}

	public function __wakeup() {

	}

	/**
	 * @param boolean
	 * @return void
	 */
	public function setIsNetCart($isNetCart) {
		$this->isNetCart = $isNetCart;
	}

	/**
	 * @return boolean
	 */
	public function getIsNetCart() {
		return $this->isNetCart;
	}

	/**
	 * @param string
	 * @throws LogicException
	 * @return void
	 */
	public function setOrderNumber($orderNumber) {
		if ( ($this->orderNumber) && ($this->orderNumber != $orderNumber) ) {
			throw new \LogicException(
				'You can not redeclare the order number of your cart.',
				1413969668
			);
		}

		$this->orderNumber = $orderNumber;
	}

	/**
	 * @return string
	 */
	public function getOrderNumber() {
		return $this->orderNumber;
	}

	/**
	 * @param string $invoiceNumber
	 * @throws LogicException
	 * @return void
	 */
	public function setInvoiceNumber($invoiceNumber) {
		if ( ($this->invoiceNumber) && ($this->invoiceNumber != $invoiceNumber) ) {
			throw new \LogicException(
				'You can not redeclare the invoice number of your cart.',
				1413969712
			);
		}

		$this->invoiceNumber = $invoiceNumber;
	}

	/**
	 * @return string
	 */
	public function getInvoiceNumber() {
		return $this->invoiceNumber;
	}

	/**
	 * @param $net
	 * @return void
	 */
	public function addNet($net) {
		$this->net += $net;
	}

	/**
	 * @return float
	 */
	public function getNet() {
		return $this->net;
	}

	/**
	 * @param $net
	 * @return void
	 */
	public function setNet($net) {
		$this->net = $net;
	}

	/**
	 * @param $net
	 * @return void
	 */
	public function subNet($net) {
		$this->net -= $net;
	}

	/**
	 * @param $gross
	 * @return void
	 */
	public function addGross($gross) {
		$this->gross += $gross;
	}

	/**
	 * @return float
	 */
	public function getGross() {
		return $this->gross;
	}

	/**
	 * @param $gross
	 * @return void
	 */
	public function setGross($gross) {
		$this->gross = $gross;
	}

	/**
	 * @param $gross
	 * @return void
	 */
	public function subGross($gross) {
		$this->gross -= $gross;
	}

	/**
	 * @param $tax
	 * @return void
	 */
	public function addTax($tax) {
		$this->taxes[$tax['taxclassid']] += $tax['tax'];
	}

	/**
	 * @return array
	 */
	public function getTaxes() {
		return $this->taxes;
	}

	/**
	 * @return array
	 */
	public function getTaxesWithServices() {
		$taxes = $this->taxes;

		if ($this->payment) {
			$tax = $this->payment->getTax($this);
			$taxes[$tax['taxclassid']] += $tax['tax'];
		}
		if ($this->shipping) {
			$tax = $this->shipping->getTax($this);
			$taxes[$tax['taxclassid']] += $tax['tax'];
		}
		if ($this->specials) {
			foreach ($this->specials as $special) {
				$tax = $special->getTax($this);
				$taxes[$tax['taxclassid']] += $tax['tax'];
			}
		}

		return $taxes;
	}

	/**
	 * @param $taxclass
	 * @param $tax
	 * @return void
	 */
	public function setTax($taxclass, $tax) {
		$this->taxes[$taxclass] = $tax;
	}

	/**
	 * @param $tax
	 * @return void
	 */
	public function subTax($tax) {
		$this->taxes[$tax['taxclassid']] -= $tax['tax'];
	}

	/**
	 * @param $count
	 * @return void
	 */
	public function addCount($count) {
		$this->count += $count;
	}

	/**
	 * @return int
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 * @param $count
	 * @return void
	 */
	public function setCount($count) {
		$this->count = $count;
	}

	/**
	 * @param $count
	 * @return void
	 */
	public function subCount($count) {
		$this->count -= $count;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Shipping
	 */
	public function getShipping() {
		return $this->shipping;
	}

	/**
	 * @param $shipping
	 * @return void
	 */
	public function setShipping($shipping) {
		$this->shipping = $shipping;
	}

	/**
	 * @return Tx_WtCart_Domain_Model_Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @param $payment
	 * @return void
	 */
	public function setPayment($payment) {
		$this->payment = $payment;
	}

	/**
	 * @return array
	 */
	public function getSpecials() {
		return $this->specials;
	}

	/**
	 * @param $newspecial
	 * @return void
	 */
	public function addSpecial($newspecial) {
		$this->specials[$newspecial->getId()] = $newspecial;
	}

	/**
	 * @param $special
	 * @return void
	 */
	public function removeSpecial($special) {
		unset($this->specials[$special->getId()]);
	}

	/**
	 * @return float
	 */
	public function getServiceNet() {
		$net = 0.0;

		if ($this->payment) {
			$net += $this->payment->getNet($this);
		}
		if ($this->shipping) {
			$net += $this->shipping->getNet($this);
		}
		if ($this->specials) {
			foreach ($this->specials as $special) {
				$net += $special->getNet($this);
			}
		}

		return $net;
	}

	/**
	 * @return float
	 */
	public function getServiceGross() {
		$gross = 0.0;

		if ($this->payment) {
			$gross += $this->payment->getGross($this);
		}
		if ($this->shipping) {
			$gross += $this->shipping->getGross($this);
		}
		if ($this->specials) {
			foreach ($this->specials as $special) {
				$gross += $special->getGross($this);
			}
		};

		return $gross;
	}

	/**
	 * @return array
	 */
	public function getProducts() {
		return $this->products;
	}

	/**
	 * @param $id
	 * @return Tx_WtCart_Domain_Model_Product
	 */
	public function getProductById($id) {
		return $this->products[$id];
	}

	/**
	 * @param $id
	 * @return Tx_WtCart_Domain_Model_Product
	 */
	public function getProduct($id) {
		return $this->getProductById($id);
	}

	/**
	 * @return array
	 */
	public function toArray() {
		if ($this->payment) {
			$paymentName = $this->payment->getName();
		}
		if ($this->shipping) {
			$shippingName = $this->shipping->getName();
		}
		$specials = array();
		if ($this->specials) {
			foreach ($this->specials as $special) {
				$specials[] = $special->getName();
			}
		}

		return array(
			'net' => $this->net,
			'gross' => $this->gross,
			'count' => $this->count,
			'taxes' => $this->taxes,
			'maxServiceAttribute1' => $this->maxServiceAttr1,
			'maxServiceAttribute2' => $this->maxServiceAttr2,
			'maxServiceAttribute3' => $this->maxServiceAttr3,
			'sumServiceAttribute1' => $this->sumServiceAttr1,
			'sumServiceAttribute2' => $this->sumServiceAttr2,
			'sumServiceAttribute3' => $this->sumServiceAttr3,
			'payment' => $paymentName,
			'shipping' => $shippingName,
			'specials' => $specials,
			'additional' => $this->additional
		);
	}

	/**
	 * @return string
	 */
	public function toJson() {
		json_encode( $this->toArray() );
	}

	/**
	 * @return void
	 */
	public function debug() {
		if (TYPO3_DLOG) {
			//debug all products
			if ($this->products) {
				foreach ($this->products as $product) {
					$product->debug();
				}
			}

			// debug the cart itself
			t3lib_div::devLog('cart', 'wt_cart', 0, $this->toArray());
		}
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Product $newProduct
	 * @internal param \Product $newproduct
	 * @return void
	 */
	public function addProduct(Tx_WtCart_Domain_Model_Product $newProduct) {
		$tableProductId = $newProduct->getTableProductId();
		$product = $this->products[$tableProductId];

		if ($product) {
			// change $newproduct in cart
			$this->changeProduct($product, $newProduct);
			$this->calcAll();
		} else {
			// $newproduct is not in cart
			$this->products[$tableProductId] = $newProduct;
			$this->calcAll();

			$this->addServiceAttributes($newProduct);
		}
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Product $product
	 * @param Tx_WtCart_Domain_Model_Product $newProduct
	 * @internal param $id
	 * @internal param $newQty
	 * @return void
	 */
	public function changeProduct($product, $newProduct) {
		$newQty = $product->getQty() + $newProduct->getQty();

		$this->subCount($product->getQty());
		$this->subGross($product->getGross());
		$this->subNet($product->getNet());
		$this->subTax($product->getTax());

			// if the new product has a variant then change it in product
		if ($newProduct->getVariants()) {
			$product->addVariants($newProduct->getVariants());
		}


		$product->changeQty($newQty);

		$this->addCount($product->getQty());
		$this->addGross($product->getGross());
		$this->addNet($product->getNet());
		$this->addTax($product->getTax());

			//update all service attributes
		$this->updateServiceAttributes();
	}

	/**
	 * @param $productQtyArray
	 * @internal param $id
	 * @internal param $newQty
	 * @return void
	 */
	public function changeProductsQty($productQtyArray) {
		foreach ($productQtyArray as $productPuid => $qty) {
			$product = $this->products[$productPuid];

			if ($product) {
				if (is_array($qty)) {
					$this->subCount($product->getQty());
					$this->subGross($product->getGross());
					$this->subNet($product->getNet());
					$this->subTax($product->getTax());

					$product->changeVariantsQty($qty);

					$this->addCount($product->getQty());
					$this->addGross($product->getGross());
					$this->addNet($product->getNet());
					$this->addTax($product->getTax());
				} else {
						// only run, if qty was realy changed
					if ($product->getQty() != $qty) {
						$this->subCount($product->getQty());
						$this->subGross($product->getGross());
						$this->subNet($product->getNet());
						$this->subTax($product->getTax());

						$product->changeQty($qty);

						$this->addCount($product->getQty());
						$this->addGross($product->getGross());
						$this->addNet($product->getNet());
						$this->addTax($product->getTax());
					}
				}
			}

				//update all service attributes
			$this->updateServiceAttributes();
		}
	}

	/**
	 * @param $productsArray
	 * @return bool|int
	 */
	public function removeProducts( $productsArray ) {
		if ( is_array($productsArray) ) {
			foreach ($productsArray as $productPuid => $productValue ) {
				$product = $this->products[$productPuid];
				if ( $product ) {
					$this->removeProduct( $product, $productValue );
				} else {
					return -1;
				}
			}
		} else {
			$productPuid = $productsArray;
			$product = $this->products[$productPuid];
			if ( $product ) {
				$this->removeProduct( $product );
			} else {
				return -1;
			}
		}

		$this->updateServiceAttributes();

		return TRUE;
	}

	/**
	 * @param Tx_WtCart_Domain_Model_Product $product
	 * @param array $productValue
	 * @return bool
	 */
	public function removeProduct( $product, $productValue = NULL) {
		if ( is_array($productValue) ) {
			$product->removeVariants($productValue);

			if (!$product->getVariants()) {
				unset( $this->products[$product->getTableProductId()] );
			}

			$this->calcAll();
		} else {
			$this->subCount($product->getQty());
			$this->subGross($product->getGross());
			$this->subNet($product->getNet());
			$this->subTax($product->getTax());

			unset( $this->products[$product->getTableProductId()] );
		}

		return TRUE;
	}

	/**
	 * recalculates the service attributes when an products was added to cart
	 *
	 * @param Tx_WtCart_Domain_Model_Product $newproduct
	 * @return void
	 */
	private function addServiceAttributes($newproduct) {
		$this->maxServiceAttr1 =
				$this->maxServiceAttr1 > $newproduct->getServiceAttribute1() ? $this->maxServiceAttr1 : $newproduct->getServiceAttribute1();
		$this->maxServiceAttr2 =
				$this->maxServiceAttr2 > $newproduct->getServiceAttribute2() ? $this->maxServiceAttr2 : $newproduct->getServiceAttribute2();
		$this->maxServiceAttr3 =
				$this->maxServiceAttr3 > $newproduct->getServiceAttribute3() ? $this->maxServiceAttr3 : $newproduct->getServiceAttribute3();

		$this->sumServiceAttr1 += $newproduct->getServiceAttribute1() * $newproduct->getQty;
		$this->sumServiceAttr2 += $newproduct->getServiceAttribute2() * $newproduct->getQty;
		$this->sumServiceAttr3 += $newproduct->getServiceAttribute3() * $newproduct->getQty;
	}

	/**
	 * recalculates the service attributes for all products in cart
	 *
	 * @return void
	 */
	private function updateServiceAttributes() {
		$this->maxServiceAttr1 = 0.0;
		$this->maxServiceAttr2 = 0.0;
		$this->maxServiceAttr3 = 0.0;
		$this->sumServiceAttr1 = 0.0;
		$this->sumServiceAttr2 = 0.0;
		$this->sumServiceAttr3 = 0.0;

		foreach ($this->products as $key => $product) {
			$this->maxServiceAttr1 =
					$this->maxServiceAttr1 > $product->getServiceAttribute1() ? $this->maxServiceAttr1 : $product->getServiceAttribute1();
			$this->maxServiceAttr2 =
					$this->maxServiceAttr2 > $product->getServiceAttribute2() ? $this->maxServiceAttr2 : $product->getServiceAttribute2();
			$this->maxServiceAttr3 =
					$this->maxServiceAttr3 > $product->getServiceAttribute3() ? $this->maxServiceAttr3 : $product->getServiceAttribute3();

			$this->sumServiceAttr1 = $product->getServiceAttribute1() * $product->getQty();
			$this->sumServiceAttr2 = $product->getServiceAttribute2() * $product->getQty();
			$this->sumServiceAttr3 = $product->getServiceAttribute3() * $product->getQty();
		}
	}

	/**
	 * @return float
	 */
	public function getMaxServiceAttribute1() {
		return $this->maxServiceAttr1;
	}

	/**
	 * @return float
	 */
	public function getMaxServiceAttribute2() {
		return $this->maxServiceAttr2;
	}

	/**
	 * @return float
	 */
	public function getMaxServiceAttribute3() {
		return $this->maxServiceAttr3;
	}

	/**
	 * @return float
	 */
	public function getSumServiceAttribute1() {
		return $this->sumServiceAttr1;
	}

	/**
	 * @return float
	 */
	public function getSumServiceAttribute2() {
		return $this->sumServiceAttr2;
	}

	/**
	 * @return float
	 */
	public function getSumServiceAttribute3() {
		return $this->sumServiceAttr3;
	}

	/**
	 * @param $shipping
	 * @return void
	 */
	public function changeShipping($shipping) {
		$this->shipping = $shipping;
	}

	/**
	 * @param $payment
	 * @return void
	 */
	public function changePayment($payment) {
		$this->payment = $payment;
	}

	/**
	 * @param $special
	 * @return void
	 */
	public function changeSpecial($special) {
		$this->special = $special;
	}

	/**
	 * @return void
	 */
	private function calcAll() {
		$this->calcCount();
		$this->calcGross();
		$this->calcTax();
		$this->calcNet();
	}

	/**
	 * @return void
	 */
	private function calcCount() {
		$this->count = 0;
		if ($this->products) {
			foreach ($this->products as $product) {
				$this->addCount($product->getQty());
			}
		}
	}

	/**
	 * @return void
	 */
	private function calcGross() {
		$this->gross = 0.0;
		if ($this->products) {
			foreach ($this->products as $product) {
				$this->addGross($product->getGross());
			}
		}
	}

	/**
	 * @return void
	 */
	private function calcNet() {
		$this->net = 0.0;
		if ($this->products) {
			foreach ($this->products as $product) {
				$this->addNet($product->getNet());
			}
		}
	}

	/**
	 * @return void
	 */
	private function calcTax() {
		$this->taxes = array();
		if ($this->products) {
			foreach ($this->products as $product) {
				$this->addTax($product->getTax());
			}
		}
	}

	/**
	 * @return void
	 */
	public function reCalc() {
		$this->calcGross();
		$this->calcNet();
		$this->calcTax();
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
	 * @param int $orderId
	 */
	public function setOrderId($orderId) {
		$this->orderId = $orderId;
	}

	/**
	 * @return int
	 */
	public function getOrderId() {
		return $this->orderId;
	}
}

?>
<?php

namespace Extcode\WtCart\Controller;

	/***************************************************************
	 *
	 *  Copyright notice
	 *
	 *  (c) 2014 Daniel Lorenz <ext.wtcart@extco.de>, extco.de UG (haftungsbeschränkt)
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
 * CartController
 */
class CartController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 *
	 * @var \Extcode\WtCart\Service\SessionHandler
	 * @inject
	 */
	protected $sessionHandler;

	/**
	 * action showCart
	 *
	 * @return void
	 */
	public function showCartAction() {
		$cart = $this->sessionHandler->restoreFromSession( $this->settings['cart']['pid'] );

		if ( !$cart ) {
			$this->isNetCart = intval($this->settings['cart']['isNetCart']) == 0 ? FALSE : TRUE;

			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			$cartClass = $objectManager->get( 'Tx_WtCart_Domain_Model_Cart' );
			$cart = $cartClass->construct($this->isNetCart);
		}

		$this->view->assign('cart', $cart);
	}

	/**
	 * action showMiniCart
	 *
	 * @return void
	 */
	public function showMiniCartAction() {
		$cart = $this->sessionHandler->restoreFromSession( $this->settings['cart']['pid'] );

		if ( !$cart ) {
			$this->isNetCart = intval($this->settings['cart']['isNetCart']) == 0 ? FALSE : TRUE;

			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			$cartClass = $objectManager->get( 'Tx_WtCart_Domain_Model_Cart' );
			$cart = $cartClass->__construct($this->isNetCart);
		}

		$this->view->assign('cart', $cart);
	}

}
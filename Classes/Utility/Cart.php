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

define('TYPO3_DLOG', $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']);

/**
 * Controller for powermail form signals
 *
 * @package wt_cart
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_WtCart_Utility_Cart {

	/**
	 * @param $cart Tx_WtCart_Domain_Model_Cart
	 */
	protected function setOrderNumber( $cart ) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		if ($cart) {
			if (!$cart->getOrderNumber()) {
				$registry =  t3lib_div::makeInstance('t3lib_Registry');
				$orderNumber =  $registry->get( 'tx_wtcart', 'lastOrder_'.$conf['main.']['pid'] );
				if ($orderNumber) {
					$orderNumber += 1;
				} else {
					$orderNumber = 1;
				}
				$registry->set('tx_wtcart', 'lastOrder_'.$conf['main.']['pid'],  $orderNumber);

				$orderNumberConf = $conf['settings.']['fields.'];
				$this->cObj = t3lib_div::makeInstance( 'tslib_cObj' );
				$this->cObj->start( array( 'ordernumber' => $orderNumber ), $orderNumberConf['ordernumber'] );
				$orderNumber = $this->cObj->cObjGetSingle( $orderNumberConf['ordernumber'], $orderNumberConf['ordernumber.'] );

				$cart->setOrderNumber($orderNumber);
			}

			if (TYPO3_DLOG) {
				t3lib_div::devLog( 'ordernumber', 'wt_cart', 0, array( $cart->getOrderNumber() ) );
			}

			$GLOBALS['TSFE']->fe_user->setKey( 'ses', 'wt_cart_' . $conf['main.']['pid'], serialize( $cart ) );
			$GLOBALS['TSFE']->storeSessionData();
		}
	}

	/**
	 * @param $cart Tx_WtCart_Domain_Model_Cart
	 */
	protected function setInvoiceNumber( $cart ) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		if ( $cart ) {
			if ( !$cart->getInvoiceNumber() ) {
				$registry =  t3lib_div::makeInstance('t3lib_Registry');
				$invoiceNumber =  $registry->get( 'tx_wtcart', 'lastInvoice_'.$conf['main.']['pid'] );
				if ( $invoiceNumber ) {
					$invoiceNumber += 1;
				} else {
					$invoiceNumber = 1;
				}
				$registry->set('tx_wtcart', 'lastInvoice_'.$conf['main.']['pid'],  $invoiceNumber);

				$invoiceNumberConf = $conf['settings.']['fields.'];
				$this->cObj = t3lib_div::makeInstance( 'tslib_cObj' );
				$this->cObj->start( array( 'invoicenumber' => $invoiceNumber ), $invoiceNumberConf['invoicenumber'] );
				$invoiceNumber = $this->cObj->cObjGetSingle( $invoiceNumberConf['invoicenumber'], $invoiceNumberConf['invoicenumber.'] );

				$cart->setInvoiceNumber( $invoiceNumber );
			}

			if (TYPO3_DLOG) {
				t3lib_div::devLog( 'invoicenumber', 'wt_cart', 0, array( $cart->getInvoiceNumber() ) );
			}

			$GLOBALS['TSFE']->fe_user->setKey( 'ses', 'wt_cart_' . $conf['main.']['pid'], serialize( $cart ) );
			$GLOBALS['TSFE']->storeSessionData();
		}
	}

	/**
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param string $hash
	 * @param $obj
	 */
	public function clearSession($mail, $hash = NULL, $obj) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		/**
		 * @var $cart Tx_WtCart_Domain_Model_Cart
		 */
		$cart = unserialize( $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'wt_cart_' . $conf['main.']['pid'] ) );

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $obj->cObj->data['uid']) {
			$errors = array();

			$params = array(
				'cart' => $cart,
				'mail' => &$mail,
				'errors' => &$errors
			);
			$this->callHook( 'beforeClearSession', $params );
		}

		$this->removeAllProductsFromSession();

		$cart = unserialize( $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'wt_cart_' . $conf['main.']['pid'] ) );

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $obj->cObj->data['uid']) {
			$errors = array();

			$params = array(
				'cart' => $cart,
				'mail' => &$mail,
				'errors' => &$errors
			);
			$this->callHook( 'afterClearSession', $params );
		}
	}

	/**
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param $hash
	 * @param $obj
	 */
	public function slotCreateActionBeforeRenderView($mail, $hash, $obj) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		/**
		 * @var $cart Tx_WtCart_Domain_Model_Cart
		 */
		$cart = unserialize( $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'wt_cart_' . $conf['main.']['pid'] ) );

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $obj->cObj->data['uid']) {

			$files = array();
			$errors = array();

			$params = array(
				'cart' => $cart,
				'mail' => &$mail,
				'files' => &$files,
				'errors' => &$errors,
				'skipInvoice' => FALSE
			);

			$this->callHook( 'orderSubmitted', $params );

			$this->setOrderNumber( $cart );

			$this->callHook( 'afterSetOrderNumber', $params );

			if ( $cart->getPayment() ) {
				$paymentService = $cart->getPayment()->getAdditional( 'payment_service' );
				if ( $paymentService ) {
					$paymentServiceHook = 'callPaymentGateway' . ucwords(strtolower($paymentService));
					$this->callHook( $paymentServiceHook, $params );
				}
			}

			if ( $params['skipInvoice'] == FALSE ) {
				$this->setInvoiceNumber( $cart );

				$this->callHook( 'afterSetInvoiceNumber', $params );
			}

			if ( $params['preventEmailToSender'] == TRUE ) {
				$obj->settings['sender']['enable'] = 0;
			}

			if ( $params['preventEmailToReceiver'] == TRUE ) {
				$obj->settings['receiver']['enable'] = 0;
			}

			$this->callHook( 'beforeAddAttachmentToMail', $params );

			$this->addAttachments( $params );

		}
		return;
	}

	/**
	 * @param string $hookName
	 * @param array $params
	 */
	protected function callHook( $hookName, &$params ) {
		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart'][$hookName]) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart'][$hookName] as $funcRef) {
				if ($funcRef) {
					t3lib_div::callUserFunction($funcRef, $params, $this);
				}
			}
		}
	}

	/**
	 * @param array $params
	 */
	protected function addAttachments( &$params ) {
		if( !empty( $params['files'] ) ) {
			/**
			 * @see powermail/Classes/Utility/Div.php:431
			 */
			$powermailSession = $GLOBALS['TSFE']->fe_user->getKey('ses', 'powermail');

			if (isset($powermailSession['upload'])) {
				$powermailSession['upload'] = array();
			}

			foreach ($params['files'] as $key => $file) {
				$powermailSession['upload']['wt_cart_orderpdf' . $key] = $file;
			}

			$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail', $powermailSession);
		}
	}

	/**
	 * Clear complete session
	 *
	 * @return  void
	 */
	public function removeAllProductsFromSession() {
		//TODO: check for $errorNumber to be Zero*/
		$pid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.']['main.']['pid'];
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'wt_cart_' . $pid, array());
		$GLOBALS['TSFE']->storeSessionData();
	}
}

?>
<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Sebastian Wagner <sebastian.wagner@tritum.de>, tritum.de
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

require_once(t3lib_extMgm::extPath('wt_cart') . 'Classes/Domain/Model/Cart.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php');

define('TYPO3_DLOG', $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']);

/**
 * Controller for powermail form signals
 *
 * @package wt_cart
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_WtCart_Hooks_Forms extends Tx_Powermail_Controller_FormsController {

	/**
	 * @param Tx_Extbase_MVC_Controller_ActionController $controller
	 * @param String $template Template Path and Filename
	 */
	protected function switchTemplate($controller, $template){
		$template = t3lib_extMgm::extPath('wt_cart', $template);
		$controller->view->setTemplatePathAndFilename($template);
	}

	/**
	 * @param Array $payload
	 * @param Tx_Powermail_Controller_FormsController $controller
	 * @return bool
	 */
	public function checkTemplate($payload, $controller) {

		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$piVars = t3lib_div::_GP('tx_powermail_pi1');

		if ($piVars['mailID'] > 0 || $piVars['sendNow'] > 0) {
			return false; // stop
		}

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $controller->cObj->data['uid'])
		{ // if powermail uid isset and fits to current CE
			$emptyTmpl = 'files/fluid_templates/powermail_empty.html';

			// read cart from session
			$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $conf['main.']['pid']));
			if (!$cart) {
				$cart = new Cart();
			}
			if ($cart->getCount() == 0) { // if there are no products in the session
				$this->switchTemplate($controller, $emptyTmpl);
			}

			$sesArray = $GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_cart_' . $conf['main.']['pid']);
			$cartmin = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.']['cart.']['cartmin.'];
			if ((floatval($sesArray['cart_gross_no_service']) < floatval($cartmin['value'])) && ($cartmin['hideifnotreached.']['powermail']))
			{
				$this->switchTemplate($controller, $emptyTmpl);
			}
		}

	}

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
	 * @param array Field Values
	 * @param integer Form UID
	 * @param object Mail object (normally empty, filled when mail already exists via double-optin)
	 * @param Tx_Powermail_Controller_FormsController $controller
	 */
	public function clearSession(array $field = array(), $form, $mail = NULL, $controller, $newMail = NULL) {
		$div = t3lib_div::makeInstance('tx_wtcart_div'); // Create new instance for div functions
		$div->beforeClearSessionHook($field, $form, $mail, $controller, $this, $this->mailsRepository);
		$div->removeAllProductsFromSession(); // clear cart now
	}

	/**
	 * @param array $field
	 * @param int $form
	 * @param null $mail
	 * @param Tx_Powermail_Controller_FormsController $controller
	 * @throws Exception
	 */
	public function slotCreateActionBeforeRenderView(array $field = array(), $form = 0, $mail = NULL, Tx_Powermail_Controller_FormsController $controller = NULL) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		/**
		 * @var $cart Tx_WtCart_Domain_Model_Cart
		 */
		$cart = unserialize( $GLOBALS['TSFE']->fe_user->getKey( 'ses', 'wt_cart_' . $conf['main.']['pid'] ) );

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $controller->cObj->data['uid']) {

			$files = array();
			$errors = array();

			$params = array(
				'cart' => $cart,
				'mail' => &$mail,
				'files' => &$files,
				'errors' => &$errors,
				'skipInvoice' => FALSE
			);

			$this->setOrderNumber( $cart );

			$this->callHook( 'afterSetOrderNumber', $params );

			$paymentService = $cart->getPayment()->getAdditional( 'payment_service' );
			if ( $paymentService ) {
				$paymentServiceHook = 'callPaymentGateway' . ucwords(strtolower($paymentService));
				$this->callHook( $paymentServiceHook, $params );
			}

			if ( $params['skipInvoice'] == FALSE ) {
				$this->setInvoiceNumber( $cart );

				$this->callHook( 'afterSetInvoiceNumber', $params );
			}

			if ( $params['preventEmailToSender'] == TRUE ) {
				$controller->settings['sender']['enable'] = 0;
			}

			if ( $params['preventEmailToReceiver'] == TRUE ) {
				$controller->settings['receiver']['enable'] = 0;
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
}

?>
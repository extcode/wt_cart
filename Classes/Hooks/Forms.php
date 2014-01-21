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
	 * @param array Field Values
	 * @param integer Form UID
	 * @param object Mail object (normally empty, filled when mail already exists via double-optin)
	 * @param Tx_Powermail_Controller_FormsController $controller
	 */
	public function setOrderNumber(array $field = array(), $form, $mail = NULL, $controller) {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $controller->cObj->data['uid']) {
			// read cart from session
			$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $conf['main.']['pid']));
			if ($cart) {
				if (!$cart->getOrderNumber()) {
					$registry =  t3lib_div::makeInstance('t3lib_Registry');
					$orderNumber =  $registry->get('tx_wtcart', 'lastOrder_'.$conf['main.']['pid']);
					if ($orderNumber) {
						$orderNumber += 1;
						$registry =  t3lib_div::makeInstance('t3lib_Registry');
						$registry->set('tx_wtcart', 'lastOrder_'.$conf['main.']['pid'],  $orderNumber);
					} else {
						$orderNumber = 1;
						$registry =  t3lib_div::makeInstance('t3lib_Registry');
						$registry->set('tx_wtcart', 'lastOrder_'.$conf['main.']['pid'],  $orderNumber);
					}
					$cart->setOrderNumber($orderNumber);
				}

				if (TYPO3_DLOG) {
					t3lib_div::devLog('ordernumber', 'wt_cart', 0, array($cart->getOrderNumber()));
				}

				$GLOBALS['TSFE']->fe_user->setKey('ses', 'wt_cart_' . $conf['main.']['pid'], serialize($cart));
				$GLOBALS['TSFE']->storeSessionData();
			}
		}
	}

	/**
	 * @param array Field Values
	 * @param integer Form UID
	 * @param object Mail object (normally empty, filled when mail already exists via double-optin)
	 * @param Tx_Powermail_Controller_FormsController $controller
	 */
	public function clearSession(array $field = array(), $form, $mail = NULL, $controller) {
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
		if($this->validateController($controller) && $this->validateForm($form) && $this->validateField($field)) {
			$this->field = $field;
			$this->controller = $controller;

			$files = array();

			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['addAttachment']) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['addAttachment'] as $funcRef) {
					if ($funcRef) {
						$params = array(
							'mail' => $mail,
							'files' => &$files
						);

						t3lib_div::callUserFunction($funcRef, $params, $this);
					}
				}
			}

			if( !empty( $files ) ) {
				/**
				 * @see powermail/Classes/Utility/Div.php:431
				 */
				$powermailSession = $GLOBALS['TSFE']->fe_user->getKey('ses', 'powermail');

				if (isset($powermailSession['upload'])) {
					$powermailSession['upload'] = array();
				}

				foreach ($files as $key => $file) {
					$powermailSession['upload']['wt_cart_orderpdf' . $key] = $file;
				}

				$GLOBALS['TSFE']->fe_user->setKey('ses', 'powermail', $powermailSession);
			}
		}
		return;
	}

	/**
	 * @param Tx_Powermail_Controller_FormsController $controller
	 * @return bool
	 */
	protected function validateController(Tx_Powermail_Controller_FormsController $controller = NULL) {
		if($controller) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param array $field
	 * @return bool TRUE when valid
	 */
	protected function validateField(array $field = array()) {
		if(count($field) > 0) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Validate that PDF should be appended to this Form
	 *
	 * @param $form
	 * @return bool TRUE when PDF is needed
	 */
	protected function validateForm($form) {
		$retval = FALSE;

		//check if PDF is needed for this form
		$formObject = $this->getFormObject($form);
		if($formObject->getTitle() == 'Warenkorb') {
			$retval = TRUE;
		}
		return $retval;
	}

	/**
	 * Retrive Form Object
	 *
	 * @param int $form Number of form to load
	 * @return null|Tx_Pmpdf_Domain_Model_Forms
	 */
	protected function getFormObject($form = 0) {
		if(!$this->formObject) {
			if($form > 0) {
				$this->formObject = $this->formsRepository->findByUid($form);
			}
		}
		return $this->formObject;
	}
}

?>
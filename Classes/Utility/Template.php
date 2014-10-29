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
class Tx_WtCart_Utility_Template {

	/**
	 * @param Array $forms
	 * @param int $powermailUid
	 * @return bool
	 */
	public function checkTemplate($forms, $powermailUid) {

		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$piVars = t3lib_div::_GP('tx_powermail_pi1');

		if ($piVars['mailID'] > 0 || $piVars['sendNow'] > 0) {
			return false; // stop
		}

		if ($conf['powermailContent.']['uid'] > 0 && intval($conf['powermailContent.']['uid']) == $powermailUid) {
			$emptyTmpl = 'files/fluid_templates/powermail_empty.html';
			$emptyTmpl = t3lib_extMgm::extPath('wt_cart', $emptyTmpl);

			// read cart from session
			$cart = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses', 'wt_cart_' . $conf['main.']['pid']));
			if (!$cart) {
				$cart = new Cart();
			}
			if ($cart->getCount() == 0) { // if there are no products in the session
				return $emptyTmpl;
			}

			$cartmin = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.']['cart.']['cartmin.'];
			if ( ( $cart->getGross() < floatval( $cartmin['value'] ) ) && ( $cartmin['hideifnotreached.']['powermail'] ) ) {
				return $emptyTmpl;
			}

			$params = array(
				'cart' => $cart,
				'emptyTemplate' => &$emptyTmpl,
				'returnTemplate' => '',
			);
			$this->callHook( 'afterCheckTemplate', $params );

			if ( !empty( $params['returnTemplate'] ) ) {
				return $params['returnTemplate'];
			}
		}

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

}

?>
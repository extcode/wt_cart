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

/**
 * Controller for powermail form signals
 *
 * @package wt_cart
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_WtCart_Hooks_Forms21 extends In2code\Powermail\Controller\FormController {

	/**
	 * @param Array $forms
	 * @param In2code\Powermail\Controller\FormController $obj
	 * @return bool
	 */
	public function checkTemplate($forms, $obj) {
		$utilityTemplate = t3lib_div::makeInstance('Tx_WtCart_Utility_Template');
		$templatePath = $utilityTemplate->checkTemplate($forms, $obj->cObj->data['uid']);
		if ( $templatePath ) {
			$this->switchTemplate( $obj, $templatePath );
		}
	}

	/**
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param string $hash
	 * @param In2code\Powermail\Controller\FormController $obj
	 */
	public function clearSession($mail, $hash = NULL, $obj) {
		$utilityCart = t3lib_div::makeInstance('Tx_WtCart_Utility_Cart');
		return $utilityCart->clearSession($mail, NULL, $obj->cObj->data['uid']);
	}

	/**
	 * @param \In2code\Powermail\Domain\Model\Mail $mail
	 * @param $hash
	 * @param In2code\Powermail\Controller\FormController $obj
	 */
	public function slotCreateActionBeforeRenderView($mail, $hash, $obj) {
		$utilityCart = t3lib_div::makeInstance('Tx_WtCart_Utility_Cart');
		return $utilityCart->slotCreateActionBeforeRenderView($mail, $hash, $obj->cObj->data['uid']);
	}

	/**
	 * @param In2code\Powermail\Controller\FormController $obj
	 * @param String $templatePath Template Path and Filename
	 */
	protected function switchTemplate($obj, $templatePath){
		$templatePath = t3lib_extMgm::extPath('wt_cart', $templatePath);
		$obj->view->setTemplatePathAndFilename( $templatePath );
	}

}

?>
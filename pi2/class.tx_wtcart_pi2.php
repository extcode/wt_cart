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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_div.php'); // file for div functions
require_once(t3lib_extMgm::extPath('wt_cart') . 'lib/class.tx_wtcart_dynamicmarkers.php'); // file for dynamicmarker functions

/**
* plugin 'Form to Cart' for the 'wt_cart' extension.
*
* @author  wt_cart Development Team <info@wt-cart.com>
* @package TYPO3
* @subpackage  tx_wtcart
* @version 1.0.0
*/
class tx_wtcart_pi2 extends tslib_pibase {
	// make configurations
	public $prefixId = 'tx_wtcart_pi3';
	public $scriptRelPath = 'pi3/class.tx_wtcart_pi3.php';
	public $extKey = 'wt_cart';

	public $tmpl = array();
	public $formMarkerArray = array();

	/**
	* the main method of the PlugIn
	*
	* @param string    $content: The PlugIn content
	* @param array   $conf: The PlugIn configuration
	* @return  The content that is displayed on the website
	*/	
	public function main($content, $conf) {
			// make configurations
		$this->conf = $conf;

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;

			// create new instance for function
		$this->div = t3lib_div::makeInstance('tx_wtcart_div');
		$this->render = t3lib_div::makeInstance('Tx_WtCart_Utility_Renderer');
		$this->dynamicMarkers = t3lib_div::makeInstance('tx_wtcart_dynamicmarkers', $this->scriptRelPath);

		$this->tmpl['form'] = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['main.']['template']), '###WTCART_FORM###'); // Load FORM HTML Template

			//build product from FlexForm
		$product = array();
		$this->pi_initPIflexForm();
		foreach ($this->conf['flexfields.'] as $key => $val) {
			if (!stristr($key, '.')) {
				$product[$val] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $val);
			} else {
				$rows = explode("\n", $this->pi_getFFvalue($this->cObj->data['pi_flexform'], rtrim($key, '.')));
				$attributes = array();
				foreach ($rows as $rowval) {
					$rowArr = explode('==', $rowval);
					$attributes[$rowArr[0]] = $rowArr[1];
				}
				foreach ($this->conf['flexfields.'][$key] as $subkey => $subval) {
					if (!stristr($subkey, '.')) {
						$product[$subval] = $attributes[$subkey];
					}
				}
			}
		}
		$GLOBALS['TSFE']->cObj->start($product, $this->conf['flexfields']);
		$GLOBALS['TSFE']->cObj->start($product, $this->conf['flexfields.']['attributes']);

			// get marker for all fields defined in plugin pi from wt_cart
		$conf_pi1 = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wtcart_pi1.'];
		$conf_pi1_fields = $conf_pi1['settings.']['powermailCart.']['fields.'];
		foreach ((array) $conf_pi1_fields as $key => $value) {
			if (!stristr($key, '.')) {
				$productOut[$key] = $GLOBALS['TSFE']->cObj->cObjGetSingle($conf_pi1_fields[$key], $conf_pi1_fields[$key . '.']);
				$this->formMarkerArray['###' . strtoupper($key) . '###'] = $productOut[$key];
			}
		}
		$this->formMarkerArray['###WTCART_FORM_ACTION_PID###'] = $this->conf['main.']['pid'];
		$this->formMarkerArray['###WTCART_FORM_ACTION###'] = $this->pi_getPageLink($this->conf['main.']['pid']);
		$this->formMarkerArray['###WTCART_FORM_CONTENTUID###'] = $this->cObj->data['uid'];

			// Get html template
		$this->content = $this->cObj->substituteMarkerArrayCached($this->tmpl['form'], null, $this->formMarkerArray);
			// Fill dynamic locallang or typoscript markers
		$this->content = $this->dynamicMarkers->main($this->content, $this);
			// Finally clear not filled markers
		$this->content = preg_replace('|###.*?###|i', '', $this->content);
		return $this->pi_wrapInBaseClass($this->content);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/pi2/class.tx_wtcart_pi2.php'])
{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wt_cart/pi2/class.tx_wtcart_pi2.php']);
}
?>
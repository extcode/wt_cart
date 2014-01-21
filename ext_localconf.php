<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_wtcart_pi1.php', '_pi1', 'list_type', 0);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_wtcart_pi2.php', '_pi2', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_wtcart_pi3.php', '_pi3', 'list_type', 0);

# Hook: clear powermail output if session is not filled
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MainContentHookAfter'][]	= 'EXT:wt_cart/lib/class.tx_wtcart_powermail.php:tx_wtcart_powermail';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook'][]			= 'EXT:wt_cart/lib/class.tx_wtcart_powermail.php:tx_wtcart_powermail';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'][]			= 'EXT:wt_cart/lib/class.tx_wtcart_powermail.php:tx_wtcart_powermail';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitLastOne'][]			= 'EXT:wt_cart/lib/class.tx_wtcart_powermail.php:tx_wtcart_powermail';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['tx_wtcart_evalprice'] = 'EXT:wt_cart/pi2/class.tx_wtcart_evalprice.php';

$TYPO3_CONF_VARS['FE']['eID_include']['addProduct'] = 'EXT:wt_cart/eid/addProduct.php';

if(version_compare(t3lib_extMgm::getExtensionVersion('powermail'), '2.0.0') >= 0) {
	$pmForms = 'Tx_Powermail_Controller_FormsController';
	$wtForms = 'Tx_WtCart_Hooks_Forms';
	/*
	  //won't work due to caching: http://typo3blogger.de/signal-slot-pattern/#comment_template
	$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_Object_Manager')
			->get('Tx_Extbase_SignalSlot_Dispatcher');
	*/
	$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
	$signalSlotDispatcher->connect(
		$pmForms, 'formActionBeforeRenderView', $wtForms, 'checkTemplate'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionBeforeRenderView', $wtForms, 'setOrderNumber'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionBeforeRenderView', $wtForms, 'slotCreateActionBeforeRenderView'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionAfterSubmitView', $wtForms, 'clearSession'
	);

	}
?>
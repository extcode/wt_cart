<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_wtcart_pi1.php', '_pi1', 'list_type', 0);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_wtcart_pi2.php', '_pi2', 'list_type', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi3/class.tx_wtcart_pi3.php', '_pi3', 'list_type', 0);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi4/class.tx_wtcart_pi4.php', '_pi4', 'list_type', 0);

if (version_compare(TYPO3_branch, '6.2', '>=')) {

	Tx_Extbase_Utility_Extension::configurePlugin(
		'Extcode.' . $_EXTKEY,
		'MiniCart',
		array(
			'Cart' => 'showMiniCart',
		),
		// non-cacheable actions
		array(
			'Cart' => 'showMiniCart',
		)
	);

}


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['tx_wtcart_evalprice'] = 'EXT:wt_cart/pi2/class.tx_wtcart_evalprice.php';

$TYPO3_CONF_VARS['FE']['eID_include']['addProduct'] = 'EXT:wt_cart/eid/addProduct.php';

// powermail hooks and signal slots

$version16 = version_compare(t3lib_extMgm::getExtensionVersion('powermail'), '1.6.0');
$version20 = version_compare(t3lib_extMgm::getExtensionVersion('powermail'), '2.0.0');
$version21 = version_compare(t3lib_extMgm::getExtensionVersion('powermail'), '2.1.0');
$version22 = version_compare(t3lib_extMgm::getExtensionVersion('powermail'), '2.2.0');

if ( ( $version16 >= 0 ) && ( $version20 < 0 ) ) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MainContentHookAfter'][]	= 'EXT:wt_cart/Classes/Hooks/Tx_WtCart_Hooks_Forms16.php:tx_wtcart_powermail';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitEmailHook'][]			= 'EXT:wt_cart/Classes/Hooks/Tx_WtCart_Hooks_Forms16.php:tx_wtcart_powermail';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_MandatoryHook'][]			= 'EXT:wt_cart/Classes/Hooks/Tx_WtCart_Hooks_Forms16.php:tx_wtcart_powermail';
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['powermail']['PM_SubmitLastOne'][]			= 'EXT:wt_cart/Classes/Hooks/Tx_WtCart_Hooks_Forms16.php:tx_wtcart_powermail';
}

if ( ( $version20 >= 0 ) && ( $version21 < 0 ) ) {
	$pmForms = 'Tx_Powermail_Controller_FormsController';
	$wtForms = 'Tx_WtCart_Hooks_Forms20';

	$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
	$signalSlotDispatcher->connect(
		$pmForms, 'formActionBeforeRenderView', $wtForms, 'checkTemplate'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionBeforeRenderView', $wtForms, 'slotCreateActionBeforeRenderView'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionAfterSubmitView', $wtForms, 'clearSession'
	);
}

if ( ( $version21 >= 0 ) && ( $version22 < 0 ) ) {
	$pmForms = 'In2code\Powermail\Controller\FormController';
	$wtForms = 'Tx_WtCart_Hooks_Forms21';

	$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
	$signalSlotDispatcher->connect(
		$pmForms, 'formActionBeforeRenderView', $wtForms, 'checkTemplate'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionBeforeRenderView', $wtForms, 'slotCreateActionBeforeRenderView'
	);
	$signalSlotDispatcher->connect(
		$pmForms, 'createActionAfterSubmitView', $wtForms, 'clearSession'
	);
}

?>
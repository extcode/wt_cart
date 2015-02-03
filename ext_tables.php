<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'files/static/', 'wt_cart main');
t3lib_extMgm::addStaticFile($_EXTKEY, 'files/css/', 'Add default CSS');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:wt_cart/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:wt_cart/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform_ds.xml'); 

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3'] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:wt_cart/locallang_db.xml:tt_content.list_type_pi3',
	$_EXTKEY . '_pi3',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:'.$_EXTKEY.'/pi3/flexform_ds.xml');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:wt_cart/locallang_db.xml:tt_content.list_type_pi4',
	$_EXTKEY . '_pi4',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

?>
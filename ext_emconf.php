<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_cart".
 *
 * Auto generated 29-10-2014 22:27
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Shopping Cart for TYPO3',
	'description' => 'Adds shopping cart(s) to your TYPO3 installation and utilizes powermail for checkout',
	'category' => 'plugin',
	'shy' => false,
	'version' => '3.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => false,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => true,
	'lockType' => '',
	'author' => 'wt_cart Development Team',
	'author_email' => 'info@wt-cart.com',
	'author_company' => '',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.5.0-6.2.99',
			'powermail' => '',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

?>
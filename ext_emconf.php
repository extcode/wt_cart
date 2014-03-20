<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_cart".
 *
 * Auto generated 18-04-2013 14:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Shopping Cart for TYPO3',
	'description' => 'Adds shopping cart to your TYPO3 installation and utilizes powermail for checkout',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.1.0',
	'dependencies' => 'powermail',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'wt_cart Development Team',
	'author_email' => 'info@wt-cart.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.5.0-4.7.99',
			'powermail' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:37:{s:9:"ChangeLog";s:4:"af45";s:12:"ext_icon.gif";s:4:"ca97";s:17:"ext_localconf.php";s:4:"986c";s:14:"ext_tables.php";s:4:"0c2f";s:16:"locallang_db.xml";s:4:"a8cf";s:14:"doc/manual.sxw";s:4:"1747";s:21:"doc/marker_change.txt";s:4:"36d4";s:19:"files/css/setup.txt";s:4:"db65";s:25:"files/img/icon_delete.gif";s:4:"ad76";s:26:"files/static/constants.txt";s:4:"a639";s:22:"files/static/setup.txt";s:4:"ac7a";s:25:"files/templates/cart.html";s:4:"224c";s:31:"files/templates/cart_table.html";s:4:"fe3c";s:12:"lib/cart.php";s:4:"7c11";s:27:"lib/class.tx_wtcart_div.php";s:4:"8621";s:38:"lib/class.tx_wtcart_dynamicmarkers.php";s:4:"01fd";s:33:"lib/class.tx_wtcart_powermail.php";s:4:"b87f";s:30:"lib/class.tx_wtcart_render.php";s:4:"32db";s:13:"lib/extra.php";s:4:"9024";s:15:"lib/payment.php";s:4:"3cc1";s:15:"lib/product.php";s:4:"f843";s:15:"lib/service.php";s:4:"e5a7";s:16:"lib/shipping.php";s:4:"5a4b";s:15:"lib/special.php";s:4:"059d";s:11:"lib/tax.php";s:4:"5bc7";s:33:"lib/user_wtcart_powermailCart.php";s:4:"5f3b";s:29:"lib/user_wtcart_userfuncs.php";s:4:"a132";s:15:"lib/variant.php";s:4:"cf0a";s:27:"pi1/class.tx_wtcart_pi1.php";s:4:"6ddc";s:17:"pi1/locallang.xml";s:4:"bf2b";s:33:"pi2/class.tx_wtcart_evalprice.php";s:4:"17cf";s:27:"pi2/class.tx_wtcart_pi2.php";s:4:"bef6";s:19:"pi2/flexform_ds.xml";s:4:"c320";s:17:"pi2/locallang.xml";s:4:"7658";s:27:"pi3/class.tx_wtcart_pi3.php";s:4:"fba7";s:19:"pi3/flexform_ds.xml";s:4:"9eae";s:17:"pi3/locallang.xml";s:4:"c2f6";}',
	'suggests' => array(
	),
);

?>
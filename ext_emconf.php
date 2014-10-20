<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_cart".
 *
 * Auto generated 20-10-2014 11:05
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Shopping Cart for TYPO3',
	'description' => 'Adds shopping cart to your TYPO3 installation and utilizes powermail for checkout',
	'category' => 'plugin',
	'shy' => 1,
	'version' => '2.1.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
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
	'_md5_values_when_last_written' => 'a:114:{s:9:"ChangeLog";s:4:"ab6f";s:12:"ext_icon.gif";s:4:"131d";s:17:"ext_localconf.php";s:4:"d9c0";s:14:"ext_tables.php";s:4:"0c2f";s:16:"locallang_db.xml";s:4:"a8cf";s:9:"README.md";s:4:"d431";s:29:"Classes/Domain/Model/Cart.php";s:4:"86f2";s:30:"Classes/Domain/Model/Extra.php";s:4:"f068";s:32:"Classes/Domain/Model/Payment.php";s:4:"bb05";s:32:"Classes/Domain/Model/Product.php";s:4:"1c81";s:32:"Classes/Domain/Model/Service.php";s:4:"78ea";s:33:"Classes/Domain/Model/Shipping.php";s:4:"76cc";s:32:"Classes/Domain/Model/Special.php";s:4:"3da9";s:28:"Classes/Domain/Model/Tax.php";s:4:"f5db";s:32:"Classes/Domain/Model/Variant.php";s:4:"3ea3";s:23:"Classes/Hooks/Forms.php";s:4:"2e19";s:24:"Documentation/Images.txt";s:4:"eb73";s:23:"Documentation/Index.rst";s:4:"ab15";s:38:"Documentation/Administration/Index.rst";s:4:"6ba0";s:44:"Documentation/Administration/Hooks/Index.rst";s:4:"9625";s:60:"Documentation/Administration/IncludeStaticTemplate/Index.rst";s:4:"eb97";s:72:"Documentation/Administration/InstallWtCartWithExtensionManager/Index.rst";s:4:"fe60";s:59:"Documentation/Administration/TyposcriptConstants/Images.txt";s:4:"1474";s:58:"Documentation/Administration/TyposcriptConstants/Index.rst";s:4:"3620";s:51:"Documentation/BugfixesFeaturesAndRequests/Index.rst";s:4:"264e";s:60:"Documentation/BugfixesFeaturesAndRequests/Bugfixes/Index.rst";s:4:"0aab";s:66:"Documentation/BugfixesFeaturesAndRequests/FeatureRequest/Index.rst";s:4:"0f6b";s:60:"Documentation/BugfixesFeaturesAndRequests/Features/Index.rst";s:4:"068b";s:33:"Documentation/Changelog/Index.rst";s:4:"0b53";s:37:"Documentation/Configuration/Index.rst";s:4:"8029";s:56:"Documentation/Configuration/AddNewFieldsToCart/Index.rst";s:4:"2195";s:69:"Documentation/Configuration/AdvancedShippingAndPaymentRules/Index.rst";s:4:"5444";s:61:"Documentation/Configuration/ConfigureMultipleCarts/Images.txt";s:4:"f5cc";s:60:"Documentation/Configuration/ConfigureMultipleCarts/Index.rst";s:4:"b6b0";s:60:"Documentation/Configuration/ConfigureOrderNumbers/Images.txt";s:4:"a894";s:59:"Documentation/Configuration/ConfigureOrderNumbers/Index.rst";s:4:"c05c";s:72:"Documentation/Configuration/ConfigureShippingAndPaymentMethods/Index.rst";s:4:"1b09";s:68:"Documentation/Configuration/ConfigureStockKeepingUnit(sku)/Index.rst";s:4:"0cdd";s:55:"Documentation/Configuration/ConfigureVariants/Index.rst";s:4:"1ef4";s:55:"Documentation/Configuration/MainConfiguration/Index.rst";s:4:"00f7";s:57:"Documentation/Configuration/MultipleDataSources/Index.rst";s:4:"c66e";s:45:"Documentation/Configuration/NetCart/Index.rst";s:4:"a610";s:46:"Documentation/Configuration/Specials/Index.rst";s:4:"bf45";s:27:"Documentation/Faq/Index.rst";s:4:"0aa4";s:59:"Documentation/Faq/AreThereAnyUndocumentedFeatures/Index.rst";s:4:"4b52";s:85:"Documentation/Faq/HowCanIAddProductsToWtCartWithoutHavingAProductExtension/Images.txt";s:4:"7d28";s:84:"Documentation/Faq/HowCanIAddProductsToWtCartWithoutHavingAProductExtension/Index.rst";s:4:"5f96";s:63:"Documentation/Faq/HowCanIAppendAnOrderFormToMyWebsite/Index.rst";s:4:"783d";s:71:"Documentation/Faq/HowCanIDisplayAdditionalProductDataInWtCart/Index.rst";s:4:"0b46";s:62:"Documentation/Faq/HowCanIUseTtNewsTogetherWithWtCart/Index.rst";s:4:"ff45";s:78:"Documentation/Faq/IsThereAnySourceForAdditionalInformationOrSnippets/Index.rst";s:4:"8507";s:52:"Documentation/Faq/WhichMarkersAreAvailable/Index.rst";s:4:"2c3b";s:89:"Documentation/Faq/WhichRequirementsShouldMyExtensionsMeetToMakeItWorkWithWtCart/Index.rst";s:4:"00d7";s:45:"Documentation/Images/manual_html_1774761b.png";s:4:"b0ca";s:45:"Documentation/Images/manual_html_5257735a.png";s:4:"702f";s:45:"Documentation/Images/manual_html_554b209f.png";s:4:"a57d";s:45:"Documentation/Images/manual_html_5a8a069e.png";s:4:"4f82";s:46:"Documentation/Images/manual_html_m40764164.png";s:4:"634f";s:46:"Documentation/Images/manual_html_m790939ee.jpg";s:4:"3258";s:47:"Documentation/Images/manual_html_powermail2.png";s:4:"d1fd";s:51:"Documentation/Images/screenshot_dietuevakademie.jpg";s:4:"76ff";s:51:"Documentation/Images/screenshot_kulturstadtlev1.png";s:4:"2396";s:51:"Documentation/Images/screenshot_kulturstadtlev2.png";s:4:"791c";s:37:"Documentation/Images/wt_cart_logo.png";s:4:"1f88";s:44:"Documentation/Images/wt_cart_orders_logo.png";s:4:"03da";s:44:"Documentation/Images/wt_cart_paypal_logo.png";s:4:"b1d2";s:41:"Documentation/Images/wt_cart_pdf_logo.png";s:4:"2725";s:36:"Documentation/Images/wtcart_logo.png";s:4:"1f88";s:36:"Documentation/Introduction/Index.rst";s:4:"a31f";s:49:"Documentation/Introduction/Screenshots/Images.txt";s:4:"af65";s:48:"Documentation/Introduction/Screenshots/Index.rst";s:4:"fdfb";s:58:"Documentation/Introduction/SupportersAndSponsors/Index.rst";s:4:"e2e9";s:50:"Documentation/Introduction/WhatDoYouNeed/Index.rst";s:4:"b2ed";s:49:"Documentation/Introduction/WhatDoesItDo/Index.rst";s:4:"0d6a";s:53:"Documentation/Introduction/WhatElseIsThere/Images.txt";s:4:"1795";s:52:"Documentation/Introduction/WhatElseIsThere/Index.rst";s:4:"af6d";s:58:"Documentation/Introduction/WtCartDevelopmentTeam/Index.rst";s:4:"4584";s:30:"Documentation/Update/Index.rst";s:4:"eb09";s:38:"Documentation/Update/From120/Index.rst";s:4:"4f03";s:38:"Documentation/Update/From130/Index.rst";s:4:"d755";s:38:"Documentation/Update/From140/Index.rst";s:4:"94a6";s:38:"Documentation/Update/From200/Index.rst";s:4:"6e76";s:35:"Documentation/UsersManual/Index.rst";s:4:"9ae0";s:71:"Documentation/UsersManual/ConfigurePowermailToWorkWithWtCart/Images.txt";s:4:"d377";s:70:"Documentation/UsersManual/ConfigurePowermailToWorkWithWtCart/Index.rst";s:4:"de16";s:51:"Documentation/UsersManual/CreateMiniCart/Images.txt";s:4:"4513";s:50:"Documentation/UsersManual/CreateMiniCart/Index.rst";s:4:"9deb";s:62:"Documentation/UsersManual/CreatePageWithWtCartPlugin/Index.rst";s:4:"2473";s:14:"doc/manual.sxw";s:4:"7a27";s:21:"doc/marker_change.txt";s:4:"36d4";s:18:"eid/addProduct.php";s:4:"ee46";s:19:"files/css/setup.txt";s:4:"3efe";s:42:"files/fluid_templates/powermail_empty.html";s:4:"1e86";s:25:"files/img/icon_delete.png";s:4:"01ce";s:26:"files/static/constants.txt";s:4:"7ce4";s:22:"files/static/setup.txt";s:4:"cceb";s:25:"files/templates/cart.html";s:4:"fd79";s:29:"files/templates/cart_net.html";s:4:"8b26";s:31:"files/templates/cart_table.html";s:4:"3edb";s:27:"lib/class.tx_wtcart_div.php";s:4:"3436";s:38:"lib/class.tx_wtcart_dynamicmarkers.php";s:4:"864c";s:33:"lib/class.tx_wtcart_powermail.php";s:4:"68cd";s:30:"lib/class.tx_wtcart_render.php";s:4:"55ed";s:33:"lib/user_wtcart_powermailCart.php";s:4:"41e8";s:29:"lib/user_wtcart_userfuncs.php";s:4:"6978";s:27:"pi1/class.tx_wtcart_pi1.php";s:4:"76c2";s:17:"pi1/locallang.xml";s:4:"2243";s:33:"pi2/class.tx_wtcart_evalprice.php";s:4:"17cf";s:27:"pi2/class.tx_wtcart_pi2.php";s:4:"6682";s:19:"pi2/flexform_ds.xml";s:4:"c320";s:17:"pi2/locallang.xml";s:4:"7658";s:27:"pi3/class.tx_wtcart_pi3.php";s:4:"ad83";s:19:"pi3/flexform_ds.xml";s:4:"9eae";s:17:"pi3/locallang.xml";s:4:"a657";}',
);

?>
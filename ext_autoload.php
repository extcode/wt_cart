<?php

$extensionPath = t3lib_extMgm::extPath('wt_cart');

return array(
	'tx_wtcart_utility_cart'            	=> $extensionPath . 'Classes/Utility/Cart.php',
	'tx_wtcart_utility_renderer'        	=> $extensionPath . 'Classes/Utility/Renderer.php',
	'tx_wtcart_utility_template'        	=> $extensionPath . 'Classes/Utility/Template.php',
	'tx_wtcart_div'                     	=> $extensionPath . 'lib/class.tx_wtcart_div.php',
	'tx_wtcart_dynamicmarkers'          	=> $extensionPath . 'lib/class.tx_wtcart_dynamicmarkers.php',

	'tx_wtcart_domain_model_cart'       	=> $extensionPath . 'Classes/Domain/Model/Cart.php',
	'tx_wtcart_domain_model_extra'      	=> $extensionPath . 'Classes/Domain/Model/Extra.php',
	'tx_wtcart_domain_model_payment'    	=> $extensionPath . 'Classes/Domain/Model/Payment.php',
	'tx_wtcart_domain_model_product'    	=> $extensionPath . 'Classes/Domain/Model/Product.php',
	'tx_wtcart_domain_model_service'    	=> $extensionPath . 'Classes/Domain/Model/Service.php',
	'tx_wtcart_domain_model_shipping'   	=> $extensionPath . 'Classes/Domain/Model/Shipping.php',
	'tx_wtcart_domain_model_special'    	=> $extensionPath . 'Classes/Domain/Model/Special.php',
	'tx_wtcart_domain_model_tax'        	=> $extensionPath . 'Classes/Domain/Model/Tax.php',
	'tx_wtcart_domain_model_variant'    	=> $extensionPath . 'Classes/Domain/Model/Variant.php',

	'tx_wtcart_hooks_forms16'           	=> $extensionPath . 'Classes/Hooks/Forms16.php',
	'tx_wtcart_hooks_forms20'           	=> $extensionPath . 'Classes/Hooks/Forms20.php',
	'tx_wtcart_hooks_forms21'           	=> $extensionPath . 'Classes/Hooks/Forms21.php',
);

?>
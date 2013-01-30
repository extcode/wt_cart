<?php

/* * *************************************************************
*  Copyright notice
*
*  (c) 2010-2012 - wt_cart Development Team <info@wt-cart.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
* ************************************************************* */

class tx_wtcart_evalprice {

	function returnFieldJS() {
		$js = '
			var re = new RegExp("^[0-9]{1,}[.,]{0,1}[0-9]{0,2}$");

			if(value == "" || !value.match(re)) {
				alert("please enter a price");
				return "";
			}

			return value;';

		return $js;
	}

	function evaluateFieldValue($value, $is_in, &$set) {	
		if ($value == '' OR $value == 'please enter a price' OR !preg_match("/^[0-9]{1,}[.,]{0,1}[0-9]{0,2}$/", $value))
		{
			return "please enter a price";
		}

		return $value;
	}
}

?>

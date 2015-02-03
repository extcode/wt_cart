<?php

namespace Extcode\WtCart\Service;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Daniel Lorenz <ext.wtcart@extco.de>, extco.de UG (haftungsbeschränkt)
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
 * SessionHandler
 */
class SessionHandler implements \TYPO3\CMS\Core\SingletonInterface {

	private $prefixKey = 'wt_cart_';

	/**
	 * Returns the object stored in the user´s PHP session
	 *
	 * @param string $key
	 * @return Object the stored object
	 */
	public function restoreFromSession($key) {
		$sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);
		return unserialize($sessionData);
	}

	/**
	 * Writes an object into the PHP session
	 *
	 * @param $object any serializable object to store into the session
	 * @param string $key
	 * @return Tx_EXTNAME_Service_SessionHandler $this
	 */
	public function writeToSession($object, $key) {
		$sessionData = serialize($object);
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
		$GLOBALS['TSFE']->fe_user->storeSessionData();
		return $this;
	}

	/**
	 * Cleans up the session: removes the stored object from the PHP session
	 *
	 * @param string $key
	 * @return Tx_EXTNAME_Service_SessionHandler $this
	 */
	public function cleanUpSession($key) {
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, NULL);
		$GLOBALS['TSFE']->fe_user->storeSessionData();
		return $this;
	}

	/**
	 * Sets own prefix key for session
	 *
	 * @param string $prefixKey
	 * @return void
	 */
	public function setPrefixKey($prefixKey) {
		$this->prefixKey = $prefixKey;
	}

}
<?php
namespace In2code\In2studyfinder\Utility;

/**
 * Class SessionUtility
 *
 * @package In2code\In2studyfinder\Utility
 */
class SessionUtility {

	/**
	 * @var string
	 */
	protected $sessionNamespace = 'in2studyfinder';

	/**
	 * @var string
	 */
	protected $sessionType = 'ses';

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->getFrontendUser()->getKey($this->sessionType, $this->namespaceKey($key));
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key) {
		return $this->getFrontendUser()->getKey($this->sessionType, $this->namespaceKey($key)) === '' ? FALSE : TRUE;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$this->getFrontendUser()->setKey($this->sessionType, $this->namespaceKey($key), $value);
		$this->getFrontendUser()->storeSessionData();
	}

	/**
	 * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	protected function getFrontendUser() {
		return $GLOBALS['TSFE']->fe_user;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function namespaceKey($key) {
		return $this->sessionNamespace . $key;
	}
}

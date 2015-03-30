<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 25-Mar-15
 * Time: 12:08 PM
 */

namespace Integralib;

use JFactory;
use JLog;
use JText;

class TimOneCurl {
	public $result;
	protected $serviceUrl;
	protected $jsonData;
	protected $httpType;

	/**
	 * @param mixed $serviceUrl
	 */
	public function setServiceUrl ($serviceUrl) {
		$this->serviceUrl = $serviceUrl;
	}

	/**
	 * @return mixed
	 */
	public function getServiceUrl () {
		return $this->serviceUrl;
	}

	/**
	 * @param mixed $jsonData
	 */
	public function setJsonData ($jsonData) {
		$this->jsonData = $jsonData;
	}

	/**
	 * @return mixed
	 */
	public function getJsonData () {
		return $this->jsonData;
	}

	/**
	 * @param $type
	 *
	 * @return bool
	 */
	public function setHttpType ($type) {
		if(in_array($type, array('PUT', 'POST', 'GET', 'PATCH', 'DELETE'))) {
			$this->httpType = $type;
		} else {
			return false;
		}
	}

	/**
	 * @return string
	 */
	public function getHttpType () {
		return strtoupper($this->httpType);
	}

	public function to_timone() {

		$verboseflag = true;
//		$credentials = array('username' => '' ,'password' => '');
		$verbose = fopen(JFactory::getConfig()->get('log_path').'/curl-'.date('d-m-y').'.log', 'a+');
		$ch = curl_init();

		switch($this->getHttpType()) {
			case ('POST'):
				$options = array(
					CURLOPT_POST 		    => true,
					CURLOPT_URL            => $this->serviceUrl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_POSTFIELDS     => $this->jsonData,
					CURLOPT_HEADER         => false,
					//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_VERBOSE        => $verboseflag,
					CURLOPT_STDERR		   => $verbose,
					CURLOPT_HTTPHEADER	   => array(
						'Accept: application/json',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($this->jsonData)
					)
				);
				break;
			case ('PUT'):
				$options = array(
					CURLOPT_PUT 			=> true,
					CURLOPT_URL            => $this->serviceUrl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_HEADER         => true,
					//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_VERBOSE        => $verboseflag,
					CURLOPT_STDERR		   => $verbose,
					CURLOPT_HTTPHEADER	   => array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($this->jsonData)
					)
				);
				break;
			case 'DELETE':
				$options = array(
					CURLOPT_CUSTOMREQUEST => "DELETE",
					CURLOPT_URL            => $this->serviceUrl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_HEADER         => true,
					//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_VERBOSE        => $verboseflag,
					CURLOPT_STDERR		   => $verbose,
					CURLOPT_HTTPHEADER	   => array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($this->jsonData)
					)
				);
				break;
			default:
				$options = array(
					CURLOPT_HTTPGET			=> true,
					CURLOPT_URL            => $this->serviceUrl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_HEADER         => false,
					//			CURLOPT_USERPWD        => ($credentials['username'] . ':' . $credentials['password']),
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_VERBOSE        => $verboseflag,
					CURLOPT_STDERR		   => $verbose,
					CURLOPT_HTTPHEADER	   => array(
						'Accept: application/json',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($this->jsonData)
					)
				);
				break;
		}

		curl_setopt_array($ch,$options);

		if($verboseflag === true) {
			$headers = curl_getinfo( $ch,
			                         CURLINFO_HEADER_OUT );
			$this->result->data = curl_exec($ch);

			rewind( $verbose );
			$verboseLog = stream_get_contents( $verbose );
			//echo "Verbose information:\n<pre>", htmlspecialchars( $verboseLog ), "</pre>\n" . curl_errno( $ch ) . curl_error( $ch );
		}

		$this->result->code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
		$this->result->info = curl_getinfo ($ch);
		curl_close($ch);

		JLog::add(json_encode($this), JLog::DEBUG);

		switch ($this->result->code) {
			case 200:
				$this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_GRANTED');
				break;
			case 401:
				$this->result->message = JText::_('JGLOBAL_AUTH_ACCESS_DENIED');
				break;
			default:
				$this->result->message = JText::_('JGLOBAL_AUTH_UNKNOWN_ACCESS_DENIED');
				break;
		}

		return $this->result;
	}

}
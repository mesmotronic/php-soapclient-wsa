<?php

namespace Mesmotronic\Soap;

use \DOMDocument;

/**
 * SOAP client for use with WSA web services that require WSSE authorisation header
 *
 * @author		Neil Rackett
 * @license		http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version		1.0.0
 */
class WsaSoapClient extends \SoapClient
{
	public function __construct($wsdl, $options=NULL)
	{
		if (empty($options))
		{
			$options = array();
		}
		
		$options['soap_version'] = SOAP_1_2;
		
		parent::__construct($wsdl, $options);
		
		if (isset($options['login']) && isset($options['password']))
		{
			require_once 'WsseAuthHeader.php';
			
			$header = new WsseAuthHeader($options['login'], $options['password']);
			$this->__setSoapHeaders(array($header));
		}
	}
	
	public function __doRequest($request, $location, $saction, $version, $one_way=NULL)
	{
		$dom = new DOMDocument();
		$dom->loadXML($request);
		
		require_once 'WsaSoap.php';
		
		$wsa = new WsaSoap($dom);
		$wsa->addAction($saction);
		$wsa->addTo($location);
		$wsa->addMessageID();
		$wsa->addReplyTo();
		
		$request = $wsa->saveXML();
		
		return parent::__doRequest($request, $location, $saction, $version, $one_way);
	}
}

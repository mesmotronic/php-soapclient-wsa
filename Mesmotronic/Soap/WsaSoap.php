<?php

namespace Mesmotronic\Soap;

/**
 * WSA capabilities
 *
 * @author		Robert Richards <rrichards@ctindustries.net>
 * @author		Kévin Dunglas <kevin@les-tilleuls.coop>
 * @author		Neil Rackett
 * @copyright	2007 Robert Richards <rrichards@ctindustries.net>
 * @copyright	2013 La Coopérative des Tilleuls <contact@les-tilleuls.coop>
 * @license		http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version		1.1.1
 */
class WsaSoap
{
	const WSANS = 'http://www.w3.org/2005/08/addressing';
	const WSAPFX = 'wsa';

	private $soapNS, $soapPFX;
	private $soapDoc = NULL;
	private $envelope = NULL;
	private $SOAPXPath = NULL;
	private $header = NULL;
	private $messageID = NULL;

	private function locateHeader()
	{
		if ($this->header == NULL)
		{
			$headers = $this->SOAPXPath->query('//wssoap:Envelope/wssoap:Header');
			$header = $headers->item(0);
			if (!$header)
			{
				$header = $this->soapDoc->createElementNS($this->soapNS, $this->soapPFX . ':Header');
				$this->envelope->insertBefore($header, $this->envelope->firstChild);
			}
			$this->header = $header;
		}

		return $this->header;
	}

	/**
	 *
	 * @param \DOMDocument $doc
	 */
	public function __construct($doc)
	{
		$this->soapDoc = $doc;
		$this->envelope = $doc->documentElement;
		$this->soapNS = $this->envelope->namespaceURI;
		$this->soapPFX = $this->envelope->prefix;
		$this->SOAPXPath = new \DOMXPath($doc);
		$this->SOAPXPath->registerNamespace('wssoap', $this->soapNS);
		$this->SOAPXPath->registerNamespace('wswsa', self::WSANS);

		$this->envelope->setAttributeNS("http://www.w3.org/2000/xmlns/", 'xmlns:' . self::WSAPFX, self::WSANS);
		$this->locateHeader();
	}

	/**
	 *  Adds the WSA Action
	 * @param string $action
	 */
	public function addAction($action)
	{
		$header = $this->locateHeader();

		$nodeAction = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':Action', $action);
		$header->appendChild($nodeAction);
	}

	/**
	 * Adds the WSA To
	 * @param string $location
	 */
	public function addTo($location)
	{
		$header = $this->locateHeader();

		$nodeTo = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':To', $location);
		$header->appendChild($nodeTo);
	}

	/**
	 * Creates a UID
	 *
	 * @return string
	 */
	private function createID()
	{
		$uuid = md5(uniqid(rand(), true));
		$guid = 'uudi:' .
			substr($uuid, 0, 8) . "-" .
			substr($uuid, 8, 4) . "-" .
			substr($uuid, 12, 4) . "-" .
			substr($uuid, 16, 4) . "-" .
			substr($uuid, 20, 12);

		return $guid;
	}

	/**
	 * Adds the WSA MessageID or return existing ID
	 *
	 * @param string $id
	 */
	public function addMessageID($id = NULL)
	{
		if (!is_null($this->messageID))
		{
			return $this->messageID;
		}

		if (empty($id))
		{
			$id = $this->createID();
		}

		$header = $this->locateHeader();

		$nodeID = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':MessageID', $id);
		$header->appendChild($nodeID);
		$this->messageID = $id;
	}

	/**
	 *  Adds the WSA Reply To
	 * @param string $address
	 */
	public function addReplyTo($address = NULL)
	{
		/* Create Message ID is not already added - required for ReplyTo */
		if (is_null($this->messageID)) {
			$this->addMessageID();
		}
		/* Add the WSA ReplyTo */
		$header = $this->locateHeader();

		$nodeReply = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':ReplyTo');
		$header->appendChild($nodeReply);

		if (empty($address))
		{
			$address = 'http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous';
		}
		$nodeAddress = $this->soapDoc->createElementNS(self::WSANS, self::WSAPFX . ':Address', $address);
		$nodeReply->appendChild($nodeAddress);
	}

	/**
	 * Gets the DOM document
	 *
	 * @return \DOMDocument
	 */
	public function getDoc()
	{
		return $this->soapDoc;
	}

	/**
	 * Gets the doc XML
	 *
	 * @return string
	 */
	public function saveXML()
	{
		return $this->soapDoc->saveXML();
	}

	/**
	 * Saves the document to a file
	 *
	 * @param  string $file
	 * @return int
	 */
	public function save($file)
	{
		return $this->soapDoc->save($file);
	}
}

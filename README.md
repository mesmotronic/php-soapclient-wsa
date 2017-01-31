PHP WSA SOAP Client
===================

`WsaSoapClient` extends `SoapClient` to add support for web services that require Web Services Addressing (WS-A) and WSSE authentication.

Example
-------

The example below connects to a web service using WSSE authentication and calls the `GetSomething` method:

```php
$wsdl = "https://my.example.com/MyWebService?wsdl";

$options = array
(
	'login' => 'my_username',
	'password' => 'my_password'
);

$client = new \Mesmotronic\Soap\WsaSoapClient($wsdl, $options);
$result = $client->GetSomething(array("foo"=>"bar"));
```

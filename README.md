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

Make a donation
---------------

If you find this project useful, why not buy us a coffee (or as many as you think it's worth)?

[![Make a donation](https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif)](http://bit.ly/2vfLvhC)

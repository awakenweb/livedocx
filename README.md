Livedocx
========

Generate PDF and other document types using the Livedocx webservice in PHP.

Why this package?
-----------------

You may know a [Livedocx package](https://github.com/zendframework/ZendService_LiveDocx) already exists, so why did I bother reinvent the wheel?

The existing Livedocx package is tightly bound to Zend Framework components and requires a lot of dependencies. Requiring nearly half a framework to only use a single library felt like pure overkill to me.

I developped this Livedocx client with two point in mind:

* just the strict necessary dependency-wise
* an object oriented API

Hope you will find it useful.

Prerequisites
-------------

This package requires:

* PHP 5.4+
* ext-soap
* ext-openssl*

*_(Livedocx is [unaffected by Heartbleed security leak](https://filippo.io/Heartbleed/#api.livedocx.com))_

Installation
------------

To install this package using [Composer](https://getcomposer.org/), simply add this to you `composer.json` file
```json
 require: {
    "awakenweb/livedocx" : "1.0"
 }
```

I'm currently working on service providers to make integration into major frameworks easier. Stay tuned.

Use
---

First, a [Livedocx](http://www.livedocx.com/) account is required. It may be any account type: free, premium, or fully licenced.

Here is a quick example or the usage of the library. For more detailed explanations and examples, read the [complete documentation](@TODO: ADD DOCUMENTATION LINK).

```php
<?php

use Awakenweb\Livedocx\Soap\Client;
use Awakenweb\Livedocx\Livedocx;
use Awakenweb\Livedocx\Container;

// for free accounts:
$wsdl = 'https://api.livedocx.com/2.1/mailmerge.asmx?wsdl';
// for premium:
$wsdl = "https://premium01.livedocx.com/REPLACE_THIS_BY_YOUR_USERNAME/mailmerge.asmx?WSDL"
// for fully licensed: only you may know

$soap = new Client(new \SoapClient($wsdl));
$soap->connect(USERNAME, PASSWORD);
$Livedocx = new Livedocx($soap, new Container();

$livedocx->assign(array(
    'somefieldname'  => 'some value',
    'someOtherField' => 'another value'
    )
);

$remoteTemplate = $livedocx->createRemoteTemplate();
$remoteTemplate->setName('myTemplate.docx')
               ->setAsActive();

$document = $livedocx->prepare();
$document->setPassword('$3CR3T');
$document->create();

file_put_contents('myPdfFile.pdf', $document->retrieve('pdf');
```

Misc
----
__Disclaimer__:
This package is only a client for the Livedocx API. I do not provide support for the Livedocx API directly as I am not affiliated, associated, authorized, endorsed by, or in any way officially connected with Text Control GmbH.
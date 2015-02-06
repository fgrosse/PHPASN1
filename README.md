PHPASN1
=======

[![Build Status](https://secure.travis-ci.org/FGrosse/PHPASN1.png?branch=master)](http://travis-ci.org/FGrosse/PHPASN1)
[![HHVM Status](http://hhvm.h4cc.de/badge/fgrosse/phpasn1.png)](http://hhvm.h4cc.de/package/fgrosse/phpasn1)

[![Latest Stable Version](https://poser.pugx.org/fgrosse/phpasn1/v/stable.png)](https://packagist.org/packages/fgrosse/phpasn1)
[![Total Downloads](https://poser.pugx.org/fgrosse/phpasn1/downloads.png)](https://packagist.org/packages/fgrosse/phpasn1)
[![Latest Unstable Version](https://poser.pugx.org/fgrosse/phpasn1/v/unstable.png)](https://packagist.org/packages/fgrosse/phpasn1)
[![License](https://poser.pugx.org/fgrosse/phpasn1/license.png)](https://packagist.org/packages/fgrosse/phpasn1)

A PHP Framework that allows you to encode and decode arbitrary [ASN.1](http://www.itu.int/ITU-T/asn1/) structures
using the [ITU-T X.690 Encoding Rules](http://www.itu.int/ITU-T/recommendations/rec.aspx?rec=x.690).
This encoding is very frequently used in [X.509 PKI environments](http://en.wikipedia.org/wiki/X.509) or the communication between heterogeneous computer systems.

The API allows you to encode ASN.1 structures to create binary data such as certificate
signing requests (CSR), X.509 certificates or certificate revocation lists (CRL).
PHPASN1 can also read [BER encoded](http://en.wikipedia.org/wiki/X.690#BER_encoding) binary data into separate PHP objects that can be manipulated by the user and reencoded afterwards.


## Dependencies

PHPASN1 requires at least `PHP 5.3`.

It has been successfully tested using `PHP 5.3` to `PHP 5.6` and `HHVM`

For the loading of object identifier names directly from the web the [Client URL Library (CURL)](http://php.net/manual/en/book.curl.php) is used.

## Installation ##

The preferred way to install this library is to rely on Composer:

    {
        "require": {
            // ...
            "fgrosse/phpasn1": "dev-master"
        }
    }

## Usage

### Encoding ASN.1 Structures

PHPASN1 offers you a class for each of the implemented ASN.1 universal types.
The constructors should be pretty self explanatory so you should have no big trouble getting started.
All data will be encoded using [DER encoding](http://en.wikipedia.org/wiki/X.690#DER_encoding)

```php

use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\Null;

$integer = new Integer(123456);        
$boolean = new Boolean(true);
$enum = new Enumerated(1);
$ia5String = new IA5String('Hello world');

$asnNull = new Null();
$objectIdentifier1 = new ObjectIdentifier('1.2.250.1.16.9');
$objectIdentifier2 = new ObjectIdentifier(OID::RSA_ENCRYPTION);
$printableString = new PrintableString('Foo bar');

$sequence = new Sequence($integer, $boolean, $enum, $ia5String);
$set = new Set($sequence, $asnNull, $objectIdentifier1, $objectIdentifier2, $printableString);

$myBinary  = $sequence->getBinary();
$myBinary .= $set->getBinary();

echo base64_encode($myBinary);
```


### Decoding binary data

Decoding BER encoded binary data is just as easy as encoding it.
I am currently working on this part of the API so there might be some useful methods in the future that allow you to easily navigate the
decoded data.

```php
use FG\ASN1\Object;

$base64String = ...
$binaryData = base64_decode($base64String);        
$asnObject = Object::fromBinary($binaryData);
// do stuff
```


### Examples

To see some example usage of the API classes or some generated output check out the [examples folder](https://github.com/FGrosse/PHPASN1/tree/master/examples).


### Unit Tests

PHPASN1 uses [PHP Unit](https://github.com/sebastianbergmann/phpunit). For some more detailed example usages you could look at the [tests folder](https://github.com/FGrosse/PHPASN1/tree/master/tests).  

## Thanks

The old autoloader is no more used, but thanks [Robert](https://github.com/robertkoehler) for the help with the Autoloader :)
I also use [this nice php script](http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexdump-in-php/) from [Aidan Lister](http://aidanlister.com).

## License

This library is release under [GNU General Public License Version 3](LICENSE).

PHPASN1
=======

A PHP Framework that allows you to encode and decode arbitrary [ASN1](http://www.itu.int/ITU-T/asn1/) structures
using the [ITU-T X690 Encoding Rules](http://www.itu.int/ITU-T/recommendations/rec.aspx?rec=x.690).
This encoding is very frequently used in X.509 PKI environments or the communication between heterogeneous computer systems.

The API allows you to encode ASN1 structures to create binary data such as certificate
signing requests (CSR), X.509 certificates or certificate revocation lists (CRL).

PHPASN1 can also read BER encoded binary data into separate PHP objects that can be manipulated by the user and reencoded afterwards.


## Usage

Just copy (maybe rename) the classes folder into your php application.
The classes do not include each other so they can be moved anywhere in your project.

### The Autoloader
You will need some kind of Autoloader to run this API.
Don't worry if you don't have a autoloader facility yet.
You can easily use PHPASN1s autoloader by including `PHPASN_Autoloader.php` (found directly in the classes folder)
and then registering it.

```php
require_once '../classes/PHPASN_Autoloader.php';
PHPASN_Autoloader::register();
```

Everytime a called class is not yet known to PHP it asks the autoloader.
The PHPASN1 autoloader will resursively search for any class files in all directories at its own directory level.
The class file name needs to match the pattern `<CLASSNAME>.class.php`

Because searching for all the right class files everytime is wasteful, the PHPASN1 autoloader has its own caching mechanism.
It will write the mapping of all classes to the absolut locations of their class files into a cache file.
You can tell the autoloader where to put that file in the `PHPASN_Autoloader::register(...)` function.

**Note**: Currently, the caching is non-optional and requires php to have **write access** to the cache directory
or the right to create the folder if it does not exist.

More information about PHP Autoloading  can be found [here](http://php.net/manual/en/language.oop5.autoload.php).


### Encoding ASN.1 Structures

PHPASN1 offers you a class for each of the implemented ASN.1 universal types. The constructors should be pretty self explanatory so
you should have no big trouble getting started.

```php
$integer = new ASN_Integer(123456);        
$boolean = new ASN_Boolean(true);
$enum = new ASN_Enumerated(1);
$ia5String = new ASN_IA5String('Hello world');

$asnNull = new ASN_Null();
$objectIdentifier1 = new ASN_ObjectIdentifier('1.2.250.1.16.9');
$objectIdentifier2 = new ASN_ObjectIdentifier(OID::RSA_ENCRYPTION);
$printableString = new ASN_PrintableString('Foo bar');

$sequence = new ASN_Sequence($integer, $boolean, $enum, $ia5String);
$set = new ASN_Set($sequence, $asnNull, $objectIdentifier1, $objectIdentifier2, $printableString);

$myBinary  = $sequence->getBinary();
$myBinary .= $set->getBinary();

echo base64_encode($myBinary);
```


### Decoding binary data

Decoding BER encoded binary data is just as easy as encoding it.
I am currently working on this part of the API so there might be some useful methods in the future that allow you to easily navigate the
decoded data.  

```php
$base64String = ...
$binaryData = base64_decode($base64String);        
$asnObject = ASN_Object::fromBinary($binaryData);
// do stuff
```


### Examples

To see some example usage of the API classes or some generated output check out the examples folder.


### Unit Tests

PHPASN1 uses [PHP Unit](https://github.com/sebastianbergmann/phpunit). For some more detailed example usages you could look at the tests folder.  

## Thanks

Thanks Robert for the help with the Autoloader :)
I also use [this nice php script](http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexdump-in-php/) from [Aidan Lister](http://aidanlister.com)

PHPASN1
=======

A PHP Framework that allows you to create arbitrary [ASN1](http://www.itu.int/ITU-T/asn1/) structures and encode
them using the [ITU-T X690 Encoding Rules](http://www.itu.int/ITU-T/recommendations/rec.aspx?rec=x.690).
This encoding is very frequently used in X.509 PKI environments or the communication between heterogeneous computer systems.

The focus of this API is, to enable its users to build ASN1 structures to create binary data such as certificate
signing requests (CSR), X.509 certificates or certificate revocation lists (CRL) without using OpenSSL.
Later ASN1 decoding functionality may be added as well.


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

More information about PHP Autoloading  can be found [here](http://php.net/manual/en/language.oop5.autoload.php).


## Examples

To see some example usage of the API or some generated output check out the examples folder.


## Future

If I have some time I will add some more ASN classes, composite classes (like X509 certificate) and
even add ASN1 parsing functionality.


## Thanks

Thanks Robert for the help with the Autoloader :)
I also use [this nice php script](http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexdump-in-php/) from [Aidan Lister](http://aidanlister.com)

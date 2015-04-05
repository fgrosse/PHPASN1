<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use FG\ASN1\Object;
use FG\ASN1\Identifier;

$base64String =
'MIIDfjCCAuegAwIBAgIKZGgbPQAAAABnVTANBgkqhkiG9w0BAQUFADBGMQswCQYD
VQQGEwJVUzETMBEGA1UEChMKR29vZ2xlIEluYzEiMCAGA1UEAxMZR29vZ2xlIElu
dGVybmV0IEF1dGhvcml0eTAeFw0xMjA4MTYxMjI2MTlaFw0xMzA2MDcxOTQzMjda
MGcxCzAJBgNVBAYTAlVTMRMwEQYDVQQIEwpDYWxpZm9ybmlhMRYwFAYDVQQHEw1N
b3VudGFpbiBWaWV3MRMwEQYDVQQKEwpHb29nbGUgSW5jMRYwFAYDVQQDEw13d3cu
Z29vZ2xlLmRlMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDEoGt7UvjRfcHM
tHNixkq3marhniRadVnpzux9iqTfB8sLIf39djxjoxOmaP6ddk7ZE8UaZ2eI22Kv
Yk9CLC8RLBMWkiql03gjzZ9D0fxUUT0Usp42mR8IoELELqru5f6OLLEBZxdKNZzr
9vrMLJypM61AMTfuLD9MvtlGASnHKwIDAQABo4IBUDCCAUwwHQYDVR0lBBYwFAYI
KwYBBQUHAwEGCCsGAQUFBwMCMB0GA1UdDgQWBBS7E76yPlQGezzNghLX64dAkcH1
HTAfBgNVHSMEGDAWgBS/wDDr9UMRPme6npH7/Gra42sSJDBbBgNVHR8EVDBSMFCg
TqBMhkpodHRwOi8vd3d3LmdzdGF0aWMuY29tL0dvb2dsZUludGVybmV0QXV0aG9y
aXR5L0dvb2dsZUludGVybmV0QXV0aG9yaXR5LmNybDBmBggrBgEFBQcBAQRaMFgw
VgYIKwYBBQUHMAKGSmh0dHA6Ly93d3cuZ3N0YXRpYy5jb20vR29vZ2xlSW50ZXJu
ZXRBdXRob3JpdHkvR29vZ2xlSW50ZXJuZXRBdXRob3JpdHkuY3J0MAwGA1UdEwEB
/wQCMAAwGAYDVR0RBBEwD4INd3d3Lmdvb2dsZS5kZTANBgkqhkiG9w0BAQUFAAOB
gQBOBSVeQj4EiCzcgVUmO9BIzQqOHgOQq2ePbgrUln9aX+0SCLvJf/38HrT884Jf
CznPwmTKJmjLRIq6v6RPvYC9O45EM7kjB1YXcCCKiPK7IGmJf8dwZAO4MKLtJnv4
D0k6lc6/SWpmbg33TqEDjl8OsvMUzV6S8XRz2L/rqZ1z1g==';

$binaryData = base64_decode($base64String);
$asnObject = Object::fromBinary($binaryData);

function printObject(Object $object, $depth = 0)
{
    $treeSymbol = '';
    $depthString = str_repeat('━', $depth);
    if ($depth > 0) {
        $treeSymbol = '┣';
    }

    $name = strtoupper(Identifier::getShortName($object->getType()));
    echo "{$treeSymbol}{$depthString}<b>{$name}</b> : ";

    echo $object->__toString().'<br/>';

    $content = $object->getContent();
    if (is_array($content)) {
        foreach ($object as $child) {
            printObject($child, $depth+1);
        }
    }
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHPASN1 Examples</title>
  <meta name="description" content="How to parse binary ASN.1 data with PHPASN1">
  <meta name="author" content="Friedrich Große">
</head>
<body>
    <p>Got <?= strlen($binaryData) ?> byte of binary data: </p>
    <pre><?php printObject($asnObject);?></pre>
</body>
</html>

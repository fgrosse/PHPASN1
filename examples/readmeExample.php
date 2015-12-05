<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FG\ASN1\OID;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\NullObject;

$integer = new Integer(123456);
$boolean = new Boolean(true);
$enum = new Enumerated(1);
$ia5String = new IA5String('Hello world');

$asnNull = new NullObject();
$objectIdentifier1 = new ObjectIdentifier('1.2.250.1.16.9');
$objectIdentifier2 = new ObjectIdentifier(OID::RSA_ENCRYPTION);
$printableString = new PrintableString('Foo bar');

$sequence = new Sequence($integer, $boolean, $enum, $ia5String);
$set = new Set($sequence, $asnNull, $objectIdentifier1, $objectIdentifier2, $printableString);

$myBinary  = $sequence->getBinary();
$myBinary .= $set->getBinary();

echo base64_encode($myBinary);

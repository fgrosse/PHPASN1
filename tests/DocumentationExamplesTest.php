<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2012
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test;

use FG\ASN1\OID;
use FG\ASN1\Object;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\ObjectIdentifier;

class DocumentationExamplesTest extends ASN1TestCase
{
    public function testREADME_encoding()
    {
        $this->expectOutputString('MBgCAwHiQAEB/woBARYLSGVsbG8gd29ybGQxODAYAgMB4kABAf8KAQEWC0hlbGxvIHdvcmxkBQAGBiqBegEQCQYJKoZIhvcNAQEBEwdGb28gYmFy');

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
    }

    public function testREADME_decoding()
    {
        $base64String = 'MBgCAwHiQAEB/woBARYLSGVsbG8gd29ybGQxODAYAgMB4kABAf8KAQEWC0hlbGxvIHdvcmxkBQAGBiqBegEQCQYJKoZIhvcNAQEBEwdGb28gYmFy';
        $binaryData = base64_decode($base64String);

        $asnObject = Object::fromBinary($binaryData);
        $this->assertInstanceOf(Sequence::class, $asnObject);
    }
}

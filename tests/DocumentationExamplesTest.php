<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2012
 *
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
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
use FG\ASN1\Universal\Null;
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

        $asnNull = new Null();
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
        $this->assertInstanceOf("\FG\ASN1\Universal\Sequence", $asnObject);
    }
}

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

namespace PHPASN1;

require_once(dirname(__FILE__) . '/PHPASN1TestCase.class.php');
 
class DocumentationExamplesTest extends PHPASN1TestCase {

    public function testREADME_encoding() {
        $this->expectOutputString('EBgCAwHiQAEB/woBARYLSGVsbG8gd29ybGQROBAYAgMB4kABAf8KAQEWC0hlbGxvIHdvcmxkBQAGBiqBegEQCQYJKoZIhvcNAQEBEwdGb28gYmFy');
        
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
    }
    
    public function testREADME_decoding() {
        $base64String = 'EBgCAwHiQAEB/woBARYLSGVsbG8gd29ybGQROBAYAgMB4kABAf8KAQEWC0hlbGxvIHdvcmxkBQAGBiqBegEQCQYJKoZIhvcNAQEBEwdGb28gYmFy';
        $binaryData = base64_decode($base64String);
        
        $asnObject = ASN_Object::fromBinary($binaryData);
    }
}
?>
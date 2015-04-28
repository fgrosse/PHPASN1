<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1\Composite;

use FG\Test\ASN1TestCase;
use FG\ASN1\OID;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Composite\RelativeDistinguishedName;

class RelativeDistinguishedNameTest extends ASN1TestCase
{

   public function testGetType()
   {
       $object = new RelativeDistinguishedName(OID::COMMON_NAME, new UTF8String("Friedrich Große"));
       $this->assertEquals(Identifier::SET, $object->getType());
   }

   public function testGetIdentifier()
   {
       $object = new RelativeDistinguishedName(OID::COMMON_NAME, new UTF8String("Friedrich Große"));
       $this->assertEquals(chr(Identifier::SET), $object->getIdentifier());
   }

    public function testContent()
    {
        $oid = OID::COMMON_NAME;
        $string = 'Friedrich Große';
        $object = new RelativeDistinguishedName($oid, new UTF8String($string));

        $this->assertEquals(OID::getName($oid).": {$string}", $object->getContent());
    }
    /*
    public function testGetObjectLength() {
        $object = new Boolean(true);
        $this->assertEquals(3, $object->getObjectLength());

        $object = new Boolean(false);
        $this->assertEquals(3, $object->getObjectLength());
    }

    public function testGetBinary() {
        $expectedType = chr(Identifier::BOOLEAN);
        $expectedLength = chr(0x01);

        $object = new Boolean(true);
        $expectedContent = chr(0xFF);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Boolean(false);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    /*public function testFromBinary() {
        $originalobject = new Boolean(true);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);

        $originalobject = new Boolean(false);
        $binaryData = $originalobject->getBinary();
        $parsedObject = Boolean::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }*/

    /**
     * @depends testFromBinary
     */
    /*public function testFromBinaryWithOffset() {
        $originalobject1 = new Boolean(true);
        $originalobject2 = new Boolean(false);

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(3, $offset);
        $parsedObject = Boolean::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(6, $offset);
    }*/
}

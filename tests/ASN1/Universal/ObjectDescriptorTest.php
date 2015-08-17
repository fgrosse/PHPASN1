<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1\Universal;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\ObjectDescriptor;

class ObjectDescriptorTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new ObjectDescriptor('NumericString character abstract syntax');
        $this->assertEquals(Identifier::OBJECT_DESCRIPTOR, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new ObjectDescriptor('NumericString character abstract syntax');
        $this->assertEquals(chr(Identifier::OBJECT_DESCRIPTOR), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new ObjectDescriptor('PrintableString character abstract syntax');
        $this->assertEquals('PrintableString character abstract syntax', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'Basic Encoding of a single ASN.1 type';
        $object = new ObjectDescriptor($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'Basic Encoding of a single ASN.1 type';
        $expectedType = chr(Identifier::OBJECT_DESCRIPTOR);
        $expectedLength = chr(strlen($string));

        $object = new ObjectDescriptor($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new ObjectDescriptor('PrintableString character abstract syntax');
        $binaryData = $originalObject->getBinary();
        $parsedObject = ObjectDescriptor::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new ObjectDescriptor('NumericString character abstract syntax');
        $originalObject2 = new ObjectDescriptor('Basic Encoding of a single ASN.1 type');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = ObjectDescriptor::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(41, $offset);
        $parsedObject = ObjectDescriptor::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(80, $offset);
    }
}

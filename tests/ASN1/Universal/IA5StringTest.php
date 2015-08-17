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
use FG\ASN1\Universal\IA5String;

class IA5StringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new IA5String('Hello World');
        $this->assertEquals(Identifier::IA5_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new IA5String('Hello World');
        $this->assertEquals(chr(Identifier::IA5_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new IA5String('Hello World');
        $this->assertEquals('Hello World', $object->getContent());

        $object = new IA5String('');
        $this->assertEquals('', $object->getContent());

        $object = new IA5String('             ');
        $this->assertEquals('             ', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'Hello World';
        $object = new IA5String($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'Hello World';
        $expectedType = chr(Identifier::IA5_STRING);
        $expectedLength = chr(strlen($string));

        $object = new IA5String($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new IA5String('Hello World');
        $binaryData = $originalObject->getBinary();
        $parsedObject = IA5String::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new IA5String('Hello ');
        $originalObject2 = new IA5String(' World');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = IA5String::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = IA5String::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}

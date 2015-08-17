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
use FG\ASN1\Universal\NumericString;

class NumericStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new NumericString('1234');
        $this->assertEquals(Identifier::NUMERIC_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new NumericString('1234');
        $this->assertEquals(chr(Identifier::NUMERIC_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new NumericString('123 45 67890');
        $this->assertEquals('123 45 67890', $object->getContent());

        $object = new NumericString('             ');
        $this->assertEquals('             ', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = '123  4 55677 0987';
        $object = new NumericString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = '123  4 55677 0987';
        $expectedType = chr(Identifier::NUMERIC_STRING);
        $expectedLength = chr(strlen($string));

        $object = new NumericString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new NumericString('123 45  5322');
        $binaryData = $originalObject->getBinary();
        $parsedObject = NumericString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new NumericString('1324 0');
        $originalObject2 = new NumericString('1 2 3 ');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = NumericString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = NumericString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }

    public function testCreateStringWithValidCharacters()
    {
        $object = new NumericString('1234');
        $this->assertEquals(pack('H*', '120431323334'), $object->getBinary());
        $object = new NumericString('321 98 76');
        $this->assertEquals(pack('H*', '1209333231203938203736'), $object->getBinary());
    }

    public function testCreateStringWithInvalidCharacters()
    {
        $invalidString = 'Hello World';
        try {
            $object = new NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }

        $invalidString = '123,456';
        try {
            $object = new NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }

        $invalidString = '+123456';
        try {
            $object = new NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }

        $invalidString = '-123456';
        try {
            $object = new NumericString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Numeric String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
    }
}

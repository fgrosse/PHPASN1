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
use FG\ASN1\Universal\PrintableString;

class PrintableStringTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new PrintableString('Hello World');
        $this->assertEquals(Identifier::PRINTABLE_STRING, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new PrintableString('Hello World');
        $this->assertEquals(chr(Identifier::PRINTABLE_STRING), $object->getIdentifier());
    }

    public function testContent()
    {
        $object = new PrintableString('Hello World');
        $this->assertEquals('Hello World', $object->getContent());

        $object = new PrintableString('');
        $this->assertEquals('', $object->getContent());

        $object = new PrintableString('             ');
        $this->assertEquals('             ', $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = 'Hello World';
        $object = new PrintableString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = 'Hello World';
        $expectedType = chr(Identifier::PRINTABLE_STRING);
        $expectedLength = chr(strlen($string));

        $object = new PrintableString($string);
        $this->assertEquals($expectedType.$expectedLength.$string, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new PrintableString('Hello World');
        $binaryData = $originalObject->getBinary();
        $parsedObject = PrintableString::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new PrintableString('Hello ');
        $originalObject2 = new PrintableString(' World');

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = PrintableString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = PrintableString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }

    public function testCreateStringWithValidCharacters()
    {
        $object = new PrintableString('Hello World');
        $this->assertEquals(pack('H*', '130b48656c6c6f20576f726c64'), $object->getBinary());
        $object = new PrintableString('Hello, World?');
        $this->assertEquals(pack('H*', '130d48656c6c6f2c20576f726c643f'), $object->getBinary());
        $object = new PrintableString("(Hello) 0001100 'World'?");
        $this->assertEquals(pack('H*', '13182848656c6c6f2920303030313130302027576f726c64273f'), $object->getBinary());
        $object = new PrintableString('Hello := World');
        $this->assertEquals(pack('H*', '130e48656c6c6f203a3d20576f726c64'), $object->getBinary());
    }

    public function testCreateStringWithInvalidCharacters()
    {
        $invalidString = 'Hello ♥♥♥ World';
        try {
            $object = new PrintableString($invalidString);
            $object->getBinary();
            $this->fail('Should have thrown an exception');
        } catch (\Exception $exception) {
            $this->assertEquals("Could not create a ASN.1 Printable String from the character sequence '{$invalidString}'.", $exception->getMessage());
        }
    }

    public function testIsPrintableString()
    {
        $validString = 'Hello World';
        $this->assertTrue(PrintableString::isValid($validString));

        $invalidString = 'Hello ♥♥♥ World';
        $this->assertFalse(PrintableString::isValid($invalidString));
    }
}

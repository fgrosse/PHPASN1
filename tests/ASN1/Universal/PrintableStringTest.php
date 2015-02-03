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

namespace FG\Test\ASN1\Universal;

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\PrintableString;

class PrintableStringTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new PrintableString("Hello World");
        $this->assertEquals(Identifier::PRINTABLE_STRING, $object->getType());
    }

    public function testContent()
    {
        $object = new PrintableString("Hello World");
        $this->assertEquals("Hello World", $object->getContent());

        $object = new PrintableString("");
        $this->assertEquals("", $object->getContent());

        $object = new PrintableString("             ");
        $this->assertEquals("             ", $object->getContent());
    }

    public function testGetObjectLength()
    {
        $string = "Hello World";
        $object = new PrintableString($string);
        $expectedSize = 2 + strlen($string);
        $this->assertEquals($expectedSize, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $string = "Hello World";
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
        $originalobject = new PrintableString("Hello World");
        $binaryData = $originalobject->getBinary();
        $parsedObject = PrintableString::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new PrintableString("Hello ");
        $originalobject2 = new PrintableString(" World");

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = PrintableString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = PrintableString::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }

    public function testCreateStringWithValidCharacters()
    {
        $object = new PrintableString('Hello World');
        $this->assertEquals(pack("H*", "130b48656c6c6f20576f726c64"), $object->getBinary());
        $object = new PrintableString('Hello, World?');
        $this->assertEquals(pack("H*", "130d48656c6c6f2c20576f726c643f"), $object->getBinary());
        $object = new PrintableString("(Hello) 0001100 'World'?");
        $this->assertEquals(pack("H*", "13182848656c6c6f2920303030313130302027576f726c64273f"), $object->getBinary());
        $object = new PrintableString('Hello := World');
        $this->assertEquals(pack("H*", "130e48656c6c6f203a3d20576f726c64"), $object->getBinary());
    }

    public function testCreateStringWithInvalidCharacters()
    {
        $invalidString = "Hello ♥♥♥ World";
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
        $validString = "Hello World";
        $this->assertTrue(PrintableString::isValid($validString));

        $invalidString = "Hello ♥♥♥ World";
        $this->assertFalse(PrintableString::isValid($invalidString));
    }
}

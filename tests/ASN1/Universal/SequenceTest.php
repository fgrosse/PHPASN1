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
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\Boolean;

class SequenceTest extends ASN1TestCase
{

    public function testGetType()
    {
        $object = new Sequence();
        $this->assertEquals(Identifier::SEQUENCE, $object->getType());
    }

    public function testContent()
    {
        $child1 = new Integer(123);
        $child2 = new PrintableString("Hello Wold");
        $child3 = new Boolean(true);
        $object = new Sequence($child1, $child2, $child3);

        $this->assertEquals(array($child1, $child2, $child3), $object->getContent());
    }

    public function testGetObjectLength()
    {
        $child1 = new Boolean(true);
        $object = new Sequence($child1);
        $this->assertEquals(5, $object->getObjectLength());

        $child1 = new Integer(123);
        $child2 = new Boolean(true);
        $object = new Sequence($child1, $child2);
        $this->assertEquals(8, $object->getObjectLength());

        $child1 = new Integer(123);
        $child2 = new PrintableString("Hello Wold");
        $child3 = new Boolean(true);
        $object = new Sequence($child1, $child2, $child3);
        $this->assertEquals(20, $object->getObjectLength());
    }

    public function testGetBinary()
    {
        $child1 = new Boolean(true);
        $object = new Sequence($child1);

        $expectedType = chr(Identifier::SEQUENCE);
        $expectedLength = chr(0x03);
        $expectedContent = $child1->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $child1 = new Integer(123);
        $child2 = new Boolean(true);
        $object = new Sequence($child1, $child2);
        $expectedLength = chr(0x06);
        $expectedContent  = $child1->getBinary();
        $expectedContent .= $child2->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalobject = new Sequence(
            new Boolean(true),
            new Integer(1234567)
        );
        $binaryData = $originalobject->getBinary();
        $parsedObject = Sequence::fromBinary($binaryData);
        $this->assertEquals($originalobject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalobject1 = new Sequence(
            new Boolean(true),
            new Integer(123)
        );
        $originalobject2 = new Sequence(
            new Integer(64),
            new Boolean(false)
        );

        $binaryData  = $originalobject1->getBinary();
        $binaryData .= $originalobject2->getBinary();

        $offset = 0;
        $parsedObject = Sequence::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = Sequence::fromBinary($binaryData, $offset);
        $this->assertEquals($originalobject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }
}

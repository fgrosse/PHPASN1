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

namespace FG\Test\ASN1;

use FG\ASN1\Identifier;
use FG\Test\ASN1TestCase;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\PrintableString;

class ExplicitlyTaggedObjectTest extends ASN1TestCase
{
    public function testGetType()
    {
        $asn = new ExplicitlyTaggedObject(0x1E, new PrintableString('test'));
        $expectedType = Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, $isConstructed = true, 0x1E);
        $this->assertEquals($expectedType, $asn->getType());
    }

    public function testGetTag()
    {
        $object = new ExplicitlyTaggedObject(0, new PrintableString('test'));
        $this->assertEquals(0, $object->getTag());

        $object = new ExplicitlyTaggedObject(1, new PrintableString('test'));
        $this->assertEquals(1, $object->getTag());
    }

    public function testGetLength()
    {
        $string = new PrintableString('test');
        $object = new ExplicitlyTaggedObject(0, $string);
        $this->assertEquals($string->getObjectLength() + 2, $object->getObjectLength());
    }

    public function testGetContent()
    {
        $string = new PrintableString('test');
        $object = new ExplicitlyTaggedObject(0, $string);
        $this->assertEquals($string, $object->getContent());
    }

    public function testGetBinary()
    {
        $tag = 0x01;
        $string = new PrintableString('test');
        $expectedType = chr(Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, $isConstructed = true, $tag));
        $expectedLength = chr($string->getObjectLength());

        $encodedStringObject = $string->getBinary();
        $object = new ExplicitlyTaggedObject($tag, $string);
        $this->assertBinaryEquals($expectedType.$expectedLength.$encodedStringObject, $object->getBinary());
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalTag = 0x02;
        $originalStringObject = new PrintableString('test');
        $originalObject = new ExplicitlyTaggedObject($originalTag, $originalStringObject);
        $binaryData = $originalObject->getBinary();

        $parsedObject = ExplicitlyTaggedObject::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }
}


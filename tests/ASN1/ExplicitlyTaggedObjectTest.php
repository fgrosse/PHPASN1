<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1;

use FG\ASN1\Identifier;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
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

    public function testGetIdentifier()
    {
        $asn = new ExplicitlyTaggedObject(0x1E, new PrintableString('test'));
        $expectedIdentifier = chr(Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, $isConstructed = true, 0x1E));
        $this->assertEquals($expectedIdentifier, $asn->getIdentifier());
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
        $this->assertEquals([$string], $object->getContent());
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
     * @dataProvider getTags
     * @depends testGetBinary
     */
    public function testFromBinary($originalTag)
    {
        $originalStringObject = new PrintableString('test');
        $originalObject = new ExplicitlyTaggedObject($originalTag, $originalStringObject);
        $binaryData = $originalObject->getBinary();

        $parsedObject = ExplicitlyTaggedObject::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    public function getTags()
    {
        return [
            [0x02],
            [0x00004002],
        ];
    }

    public function testFromBinaryWithZeroContent()
    {
        $data = hex2bin('A000');
        $object = ExplicitlyTaggedObject::fromBinary($data);
        $this->assertEquals(2, $object->getObjectLength());
        $this->assertEquals([], $object->getContent());
        $this->assertEquals('Context specific empty object with tag [0]', $object->__toString());
        $this->assertEquals($data, $object->getBinary());
    }

    public function testFromBinaryWithMultipleObjects()
    {
        $object1 = new Boolean(true);
        $object2 = new Integer(42);

        $identifier = 0xA0;
        $length = $object1->getObjectLength()+$object2->getObjectLength();
        $data = chr($identifier).chr($length).$object1->getBinary().$object2->getBinary();

        $object = ExplicitlyTaggedObject::fromBinary($data);
        $this->assertEquals(2+$length, $object->getObjectLength());
        $this->assertEquals([$object1, $object2], $object->getContent());
        $this->assertEquals($data, $object->getBinary());
        $this->assertEquals('2 context specific objects with tag [0]', $object->__toString());
    }
}

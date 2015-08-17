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

    public function testGetIdentifier()
    {
        $object = new Sequence();
        $this->assertEquals(chr(Identifier::SEQUENCE), $object->getIdentifier());
    }

    public function testContent()
    {
        $child1 = new Integer(123);
        $child2 = new PrintableString('Hello Wold');
        $child3 = new Boolean(true);
        $object = new Sequence($child1, $child2, $child3);

        $this->assertEquals([$child1, $child2, $child3], $object->getContent());
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
        $child2 = new PrintableString('Hello Wold');
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
        $originalObject = new Sequence(
            new Boolean(true),
            new Integer(1234567)
        );
        $binaryData = $originalObject->getBinary();
        $parsedObject = Sequence::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new Sequence(
            new Boolean(true),
            new Integer(123)
        );
        $originalObject2 = new Sequence(
            new Integer(64),
            new Boolean(false)
        );

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = Sequence::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(8, $offset);
        $parsedObject = Sequence::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(16, $offset);
    }

    public function testSequenceAsArray()
    {
        $sequence = new Sequence();
        $child1 = new Integer(123);
        $child2 = new PrintableString('Hello Wold');
        $child3 = new Boolean(true);
        $child4 = new Integer(1234567);

        $sequence[] = $child1;
        $sequence[] = $child2;
        $sequence['foo'] = $child3;

        $this->assertEquals($child1, $sequence[0]);
        $this->assertEquals($child2, $sequence[1]);
        $this->assertEquals($child3, $sequence['foo']);
        $this->assertEquals(3, count($sequence));

        unset($sequence[1]);
        $sequence['bar'] = $child4;

        $this->assertEquals($child1, $sequence[0]);
        $this->assertFalse(isset($sequence[1]));
        $this->assertEquals($child3, $sequence['foo']);
        $this->assertEquals($child4, $sequence['bar']);
        $this->assertEquals(3, count($sequence));
    }

    public function testIterateSequence()
    {
        $sequence = new Sequence(
            new Integer(1),
            new Integer(2),
            new Integer(3)
        );

        foreach ($sequence as $object) {
            $this->assertInstanceOf(Integer::class, $object);
        }
    }

    public function testCreateEmptySequence()
    {
        $sequence = new Sequence(); // this should be legal
        $this->assertEmpty($sequence);
    }
}

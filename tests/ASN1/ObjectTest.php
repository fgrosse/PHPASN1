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

use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\GeneralizedTime;
use FG\Test\ASN1TestCase;
use FG\ASN1\Object;
use FG\ASN1\UnknownConstructedObject;
use FG\ASN1\UnknownObject;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\PrintableString;

class ObjectTest extends ASN1TestCase
{
    public function testCalculateNumberOfLengthOctets()
    {
        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 32);
        $this->assertEquals(1, $calculatedNrOfLengthOctets);

        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 0);
        $this->assertEquals(1, $calculatedNrOfLengthOctets);

        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 127);
        $this->assertEquals(1, $calculatedNrOfLengthOctets);

        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 128);
        $this->assertEquals(2, $calculatedNrOfLengthOctets);

        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 255);
        $this->assertEquals(2, $calculatedNrOfLengthOctets);

        $object = $this->getMockForAbstractClass('\FG\ASN1\Object');
        $calculatedNrOfLengthOctets = $this->callMethod($object, 'getNumberOfLengthOctets', 1025);
        $this->assertEquals(3, $calculatedNrOfLengthOctets);
    }

    /**
     * For the real parsing tests look in the test cases of each single ASn object.
     */
    public function testFromBinary()
    {
        /* @var BitString $parsedObject */
        $binaryData = chr(Identifier::BITSTRING);
        $binaryData .= chr(0x03);
        $binaryData .= chr(0x05);
        $binaryData .= chr(0xFF);
        $binaryData .= chr(0xA0);

        $expectedObject = new BitString(0xFFA0, 5);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof BitString);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());
        $this->assertEquals($expectedObject->getNumberOfUnusedBits(), $parsedObject->getNumberOfUnusedBits());

        /* @var OctetString $parsedObject */
        $binaryData = chr(Identifier::OCTETSTRING);
        $binaryData .= chr(0x02);
        $binaryData .= chr(0xFF);
        $binaryData .= chr(0xA0);

        $expectedObject = new OctetString(0xFFA0);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof OctetString);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var \FG\ASN1\Universal\Boolean $parsedObject */
        $binaryData = chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x01);
        $binaryData .= chr(0xFF);

        $expectedObject = new Boolean(true);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof Boolean);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var Enumerated $parsedObject */
        $binaryData = chr(Identifier::ENUMERATED);
        $binaryData .= chr(0x01);
        $binaryData .= chr(0x03);

        $expectedObject = new Enumerated(3);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof Enumerated);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var IA5String $parsedObject */
        $string = 'Hello Foo World!!!11EinsEins!1';
        $binaryData = chr(Identifier::IA5_STRING);
        $binaryData .= chr(strlen($string));
        $binaryData .= $string;

        $expectedObject = new IA5String($string);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof IA5String);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var \FG\ASN1\Universal\Integer $parsedObject */
        $binaryData = chr(Identifier::INTEGER);
        $binaryData .= chr(0x01);
        $binaryData .= chr(123);

        $expectedObject = new Integer(123);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof Integer);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var \FG\ASN1\Universal\NullObject $parsedObject */
        $binaryData = chr(Identifier::NULL);
        $binaryData .= chr(0x00);

        $expectedObject = new NullObject();
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof NullObject);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var ObjectIdentifier $parsedObject */
        $binaryData = chr(Identifier::OBJECT_IDENTIFIER);
        $binaryData .= chr(0x02);
        $binaryData .= chr(1 * 40 + 2);
        $binaryData .= chr(3);

        $expectedObject = new ObjectIdentifier('1.2.3');
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof ObjectIdentifier);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var PrintableString $parsedObject */
        $string = 'This is a test string. #?!%&""';
        $binaryData = chr(Identifier::PRINTABLE_STRING);
        $binaryData .= chr(strlen($string));
        $binaryData .= $string;

        $expectedObject = new PrintableString($string);
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof PrintableString);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var GeneralizedTime $parsedObject */
        $binaryData  = chr(Identifier::GENERALIZED_TIME);
        $binaryData .= chr(15);
        $binaryData .= '20120923202316Z';

        $expectedObject = new GeneralizedTime('2012-09-23 20:23:16', 'UTC');
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof GeneralizedTime);
        $this->assertEquals($expectedObject->getContent(), $parsedObject->getContent());

        /* @var Sequence $parsedObject */
        $binaryData = chr(Identifier::SEQUENCE);
        $binaryData .= chr(0x06);
        $binaryData .= chr(Identifier::BOOLEAN);
        $binaryData .= chr(0x01);
        $binaryData .= chr(0x00);
        $binaryData .= chr(Identifier::INTEGER);
        $binaryData .= chr(0x01);
        $binaryData .= chr(0x03);

        $expectedChild1 = new Boolean(false);
        $expectedChild2 = new Integer(0x03);

        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof Sequence);
        $this->assertEquals(2, $parsedObject->getNumberOfChildren());

        $children = $parsedObject->getChildren();
        $child1 = $children[0];
        $child2 = $children[1];
        $this->assertEquals($expectedChild1->getContent(), $child1->getContent());
        $this->assertEquals($expectedChild2->getContent(), $child2->getContent());

        /* @var ExplicitlyTaggedObject $parsedObject */
        $taggedObject = new ExplicitlyTaggedObject(0x01, new PrintableString('Hello tagged world'));
        $binaryData = $taggedObject->getBinary();
        $parsedObject = Object::fromBinary($binaryData);
        $this->assertTrue($parsedObject instanceof ExplicitlyTaggedObject);

        // An unknown constructed object containing 2 integer children,
        // first 3 bytes are the identifier.
        $binaryData = "\x3F\x81\x7F\x06".chr(Identifier::INTEGER)."\x01\x42".chr(Identifier::INTEGER)."\x01\x69";
        $offsetIndex = 0;
        $parsedObject = OBject::fromBinary($binaryData, $offsetIndex);
        $this->assertTrue($parsedObject instanceof UnknownConstructedObject);
        $this->assertEquals(substr($binaryData, 0, 3), $parsedObject->getIdentifier());
        $this->assertCount(2, $parsedObject->getContent());
        $this->assertEquals(strlen($binaryData), $offsetIndex);
        $this->assertEquals(10, $parsedObject->getObjectLength());

        // First 3 bytes are the identifier
        $binaryData = "\x1F\x81\x7F\x01\xFF";
        $offsetIndex = 0;
        $parsedObject = Object::fromBinary($binaryData, $offsetIndex);
        $this->assertTrue($parsedObject instanceof UnknownObject);
        $this->assertEquals(substr($binaryData, 0, 3), $parsedObject->getIdentifier());
        $this->assertEquals('Unparsable Object (1 bytes)', $parsedObject->getContent());
        $this->assertEquals(strlen($binaryData), $offsetIndex);
        $this->assertEquals(5, $parsedObject->getObjectLength());
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 10: Can not parse binary from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryThrowsException()
    {
        $binaryData = 0x0;
        $offset = 10;
        Object::fromBinary($binaryData, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 0: Can not parse binary from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryWithEmptyStringThrowsException()
    {
        $data = '';
        Object::fromBinary($data);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: Can not parse binary from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryWithSpacyStringThrowsException()
    {
        $data = '  ';
        Object::fromBinary($data);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 1: Can not parse content length from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryWithNumberStringThrowsException()
    {
        $data = '1';
        Object::fromBinary($data);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 25: Can not parse content length from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryWithGarbageStringThrowsException()
    {
        $data = 'certainly no asn.1 object';
        Object::fromBinary($data);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 1: Can not parse identifier (long form) from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryUnknownObjectMissingLength()
    {
        $data = hex2bin('1f');
        Object::fromBinary($data);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 4: Can not parse content length (long form) from data: Offset index larger than input size
     * @depends testFromBinary
     */
    public function testFromBinaryInalidLongFormContentLength()
    {
        $binaryData  = chr(Identifier::INTEGER);
        $binaryData .= chr(0x8f); //denotes a long-form content length with 15 length-octets
        $binaryData .= chr(0x1);  //only give one content-length-octet
        $binaryData .= chr(0x1);  //this is needed to reach the code to be tested

        Object::fromBinary($binaryData);
    }
}

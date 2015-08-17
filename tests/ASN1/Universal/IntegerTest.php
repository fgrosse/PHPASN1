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

use FG\ASN1\Object;
use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\Integer;

class IntegerTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new Integer(123);
        $this->assertEquals(Identifier::INTEGER, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new Integer(123);
        $this->assertEquals(chr(Identifier::INTEGER), $object->getIdentifier());
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateInstanceCanFail()
    {
        new Integer('a');
    }

    public function testContent()
    {
        $object = new Integer(1234);
        $this->assertEquals(1234, $object->getContent());

        $object = new Integer(-1234);
        $this->assertEquals(-1234, $object->getContent());

        $object = new Integer(0);
        $this->assertEquals(0, $object->getContent());

        // test with maximum integer value
        $object = new Integer(PHP_INT_MAX);
        $this->assertEquals(PHP_INT_MAX, $object->getContent());

        // test with minimum integer value by negating the max value
        $object = new Integer(~PHP_INT_MAX);
        $this->assertEquals(~PHP_INT_MAX, $object->getContent());
    }

    public function testGetObjectLength()
    {
        $positiveObj = new Integer(0);
        $expectedSize = 2 + 1;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());

        $positiveObj = new Integer(127);
        $negativeObj = new Integer(-127);
        $expectedSize = 2 + 1;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(128);
        $negativeObj = new Integer(-128);
        $expectedSize = 2 + 2;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFF);
        $negativeObj = new Integer(-0x7FFF);
        $expectedSize = 2 + 2;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x8000);
        $negativeObj = new Integer(-0x8000);
        $expectedSize = 2 + 3;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFFFF);
        $negativeObj = new Integer(-0x7FFFFF);
        $expectedSize = 2 + 3;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x800000);
        $negativeObj = new Integer(-0x800000);
        $expectedSize = 2 + 4;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());

        $positiveObj = new Integer(0x7FFFFFFF);
        $negativeObj = new Integer(-0x7FFFFFFF);
        $expectedSize = 2 + 4;
        $this->assertEquals($expectedSize, $positiveObj->getObjectLength());
        $this->assertEquals($expectedSize, $negativeObj->getObjectLength());
    }

    public function testGetBinary()
    {
        $expectedType = chr(Identifier::INTEGER);
        $expectedLength = chr(0x01);

        $object = new Integer(0);
        $expectedContent = chr(0x00);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(127);
        $expectedContent = chr(0x7F);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-127);
        $expectedContent = chr(0x81);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(200);
        $expectedLength = chr(0x02);
        $expectedContent = chr(0x00);
        $expectedContent .= chr(0xC8);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-546);
        $expectedLength = chr(0x02);
        $expectedContent = chr(0xFD);
        $expectedContent .= chr(0xDE);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(7420);
        $expectedLength   = chr(0x02);
        $expectedContent  = chr(0x1C);
        $expectedContent .= chr(0xFC);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());

        $object = new Integer(-1891004);
        $expectedLength   = chr(0x03);
        $expectedContent  = chr(0xE3);
        $expectedContent .= chr(0x25);
        $expectedContent .= chr(0x44);
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $object->getBinary());
    }

    public function testBigIntegerSupport()
    {
        // Positive bigint
        $expectedType     = chr(Identifier::INTEGER);
        $expectedLength   = chr(0x20);
        $expectedContent  = "\x7f\xff\xff\xff\xff\xff\xff\xff";
        $expectedContent .= "\xff\xff\xff\xff\xff\xff\xff\xff";
        $expectedContent .= "\xff\xff\xff\xff\xff\xff\xff\xff";
        $expectedContent .= "\xff\xff\xff\xff\xff\xff\xff\xff";

        $bigint = gmp_strval(gmp_sub(gmp_pow(2, 255), 1));
        $object = new Integer($bigint);
        $binary = $object->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $binary);

        $obj = Object::fromBinary($binary);
        $this->assertEquals($obj, $object);

        // Test a negative number
        $expectedLength   = chr(0x21);
        $expectedContent  = "\x00\x80\x00\x00\x00\x00\x00\x00\x00";
        $expectedContent .= "\x00\x00\x00\x00\x00\x00\x00\x00";
        $expectedContent .= "\x00\x00\x00\x00\x00\x00\x00\x00";
        $expectedContent .= "\x00\x00\x00\x00\x00\x00\x00\x00";
        $bigint = gmp_strval(gmp_pow(2, 255));
        $object = new Integer($bigint);
        $binary = $object->getBinary();
        $this->assertEquals($expectedType.$expectedLength.$expectedContent, $binary);

        $obj = Object::fromBinary($binary);
        $this->assertEquals($object, $obj);
    }

    /**
     * @dataProvider bigIntegersProvider
     */
    public function testSerializeBigIntegers($i)
    {
        $object = new Integer($i);
        $binary = $object->getBinary();

        $obj = Object::fromBinary($binary);
        $this->assertEquals($obj->getContent(), $object->getContent());
    }

    public function bigIntegersProvider()
    {
        for ($i = 1; $i <= 256; $i *= 2) {
            // 2 ^ n [0, 256]  large positive numbers
            yield [gmp_strval(gmp_pow(2, $i), 10)];
        }

        for ($i = 1; $i <= 256; $i *= 2) {
            // 0 - 2 ^ n [0, 256]  large negative numbers
            yield [gmp_strval(gmp_sub(0, gmp_pow(2, $i)), 10)];
        }
    }

    /**
     * @depends testGetBinary
     */
    public function testFromBinary()
    {
        $originalObject = new Integer(200);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new Integer(12345);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);

        $originalObject = new Integer(-1891004);
        $binaryData = $originalObject->getBinary();
        $parsedObject = Integer::fromBinary($binaryData);
        $this->assertEquals($originalObject, $parsedObject);
    }

    /**
     * @depends testFromBinary
     */
    public function testFromBinaryWithOffset()
    {
        $originalObject1 = new Integer(12345);
        $originalObject2 = new Integer(67890);

        $binaryData  = $originalObject1->getBinary();
        $binaryData .= $originalObject2->getBinary();

        $offset = 0;
        $parsedObject = Integer::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject1, $parsedObject);
        $this->assertEquals(4, $offset);
        $parsedObject = Integer::fromBinary($binaryData, $offset);
        $this->assertEquals($originalObject2, $parsedObject);
        $this->assertEquals(9, $offset);
    }

    /**
     * @expectedException \FG\ASN1\Exception\ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: A FG\ASN1\Universal\Integer should have a content length of at least 1. Extracted length was 0
     * @depends testFromBinary
     */
    public function testFromBinaryWithInvalidLength01()
    {
        $binaryData  = chr(Identifier::INTEGER);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0xA0);
        Integer::fromBinary($binaryData);
    }
}

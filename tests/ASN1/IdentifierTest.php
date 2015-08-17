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

use FG\Test\ASN1TestCase;
use FG\ASN1\Identifier;

class IdentifierTest extends ASN1TestCase
{
    public function testCreate()
    {
        $this->assertEquals(0x02, Identifier::create(Identifier::CLASS_UNIVERSAL, $isConstructed = false, 0x02));
        $this->assertEquals(0x30, Identifier::create(Identifier::CLASS_UNIVERSAL, $isConstructed = true, 0x10));
        $this->assertEquals(0xB3, Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, $isConstructed = true, 0x13));
        $this->assertEquals("\x1F\x1F", Identifier::create(Identifier::CLASS_UNIVERSAL, $isConstructed = false, 0x1F));
        $this->assertEquals("\x1F\x81\x7F", Identifier::create(Identifier::CLASS_UNIVERSAL, $isConstructed = false, 0xFF));
    }

    public function testGetIdentifierName()
    {
        $this->assertEquals('ASN.1 End-of-contents octet', Identifier::getName(Identifier::EOC));
        $this->assertEquals('ASN.1 Boolean', Identifier::getName(Identifier::BOOLEAN));
        $this->assertEquals('ASN.1 Integer', Identifier::getName(Identifier::INTEGER));
        $this->assertEquals('ASN.1 Bit String', Identifier::getName(Identifier::BITSTRING));
        $this->assertEquals('ASN.1 Octet String', Identifier::getName(Identifier::OCTETSTRING));
        $this->assertEquals('ASN.1 NULL', Identifier::getName(Identifier::NULL));
        $this->assertEquals('ASN.1 Object Identifier', Identifier::getName(Identifier::OBJECT_IDENTIFIER));
        $this->assertEquals('ASN.1 Object Descriptor', Identifier::getName(Identifier::OBJECT_DESCRIPTOR));
        $this->assertEquals('ASN.1 External Type', Identifier::getName(Identifier::EXTERNAL));
        $this->assertEquals('ASN.1 Real', Identifier::getName(Identifier::REAL));
        $this->assertEquals('ASN.1 Enumerated', Identifier::getName(Identifier::ENUMERATED));
        $this->assertEquals('ASN.1 Embedded PDV', Identifier::getName(Identifier::EMBEDDED_PDV));
        $this->assertEquals('ASN.1 UTF8 String', Identifier::getName(Identifier::UTF8_STRING));
        $this->assertEquals('ASN.1 Relative OID', Identifier::getName(Identifier::RELATIVE_OID));
        $this->assertEquals('ASN.1 Sequence', Identifier::getName(Identifier::SEQUENCE));
        $this->assertEquals('ASN.1 Set', Identifier::getName(Identifier::SET));
        $this->assertEquals('ASN.1 Numeric String', Identifier::getName(Identifier::NUMERIC_STRING));
        $this->assertEquals('ASN.1 Printable String', Identifier::getName(Identifier::PRINTABLE_STRING));
        $this->assertEquals('ASN.1 T61 String', Identifier::getName(Identifier::T61_STRING));
        $this->assertEquals('ASN.1 Videotext String', Identifier::getName(Identifier::VIDEOTEXT_STRING));
        $this->assertEquals('ASN.1 IA5 String', Identifier::getName(Identifier::IA5_STRING));
        $this->assertEquals('ASN.1 UTC Time', Identifier::getName(Identifier::UTC_TIME));
        $this->assertEquals('ASN.1 Generalized Time', Identifier::getName(Identifier::GENERALIZED_TIME));
        $this->assertEquals('ASN.1 Graphic String', Identifier::getName(Identifier::GRAPHIC_STRING));
        $this->assertEquals('ASN.1 Visible String', Identifier::getName(Identifier::VISIBLE_STRING));
        $this->assertEquals('ASN.1 General String', Identifier::getName(Identifier::GENERAL_STRING));
        $this->assertEquals('ASN.1 Universal String', Identifier::getName(Identifier::UNIVERSAL_STRING));
        $this->assertEquals('ASN.1 Character String', Identifier::getName(Identifier::CHARACTER_STRING));
        $this->assertEquals('ASN.1 BMP String', Identifier::getName(Identifier::BMP_STRING));

        $this->assertEquals('ASN.1 RESERVED (0x0E)', Identifier::getName(0x0E));
        $this->assertEquals('ASN.1 RESERVED (0x0F)', Identifier::getName(0x0F));

        $this->assertEquals('Constructed private (0xFF7F)', Identifier::getName("\xFF\x7F"));
    }

    public function testGetIdentifierNameWithBinaryInput()
    {
        $this->assertEquals('ASN.1 Numeric String', Identifier::getName(chr(Identifier::NUMERIC_STRING)));
    }

    public function testGetTagNumber()
    {
        $this->assertEquals(1, Identifier::getTagNumber((Identifier::CLASS_CONTEXT_SPECIFIC << 6) | 0x01));
        $this->assertEquals(3, Identifier::getTagNumber((Identifier::CLASS_CONTEXT_SPECIFIC << 6) | 0x03));
        $this->assertEquals(0xFF, Identifier::getTagNumber("\x1F\x81\x7F"));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Malformed base-128 encoded value (0x0).
     */
    public function testGetTagNumberFailsIfLongFormIdentifierMissingSubsequentOctets()
    {
        Identifier::getTagNumber(Identifier::LONG_FORM);
    }

    public function testIsSpecificClass()
    {
        $identifier1 = Identifier::INTEGER;
        $this->assertTrue(Identifier::isUniversalClass($identifier1));
        $this->assertFalse(Identifier::isApplicationClass($identifier1));
        $this->assertFalse(Identifier::isContextSpecificClass($identifier1));
        $this->assertFalse(Identifier::isPrivateClass($identifier1));

        $identifier2 = 0x41;
        $this->assertFalse(Identifier::isUniversalClass($identifier2));
        $this->assertTrue(Identifier::isApplicationClass($identifier2));
        $this->assertFalse(Identifier::isContextSpecificClass($identifier2));
        $this->assertFalse(Identifier::isPrivateClass($identifier2));

        $identifier3 = 0x83;
        $this->assertFalse(Identifier::isUniversalClass($identifier3));
        $this->assertFalse(Identifier::isApplicationClass($identifier3));
        $this->assertTrue(Identifier::isContextSpecificClass($identifier3));
        $this->assertFalse(Identifier::isPrivateClass($identifier3));

        $identifier4 = 0xC3;
        $this->assertFalse(Identifier::isUniversalClass($identifier4));
        $this->assertFalse(Identifier::isApplicationClass($identifier4));
        $this->assertFalse(Identifier::isContextSpecificClass($identifier4));
        $this->assertTrue(Identifier::isPrivateClass($identifier4));

        $identifier5 = 0xA3;
        $this->assertFalse(Identifier::isUniversalClass($identifier5));
        $this->assertFalse(Identifier::isApplicationClass($identifier5));
        $this->assertTrue(Identifier::isContextSpecificClass($identifier5));
        $this->assertFalse(Identifier::isPrivateClass($identifier5));
    }
}

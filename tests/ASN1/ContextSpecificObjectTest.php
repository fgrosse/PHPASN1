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

use FG\Test\ASN1TestCase;
use FG\ASN1\ContextSpecificObject;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Set;
use FG\ASN1\Identifier;
use FG\ASN1\OID;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Object;
use FG\ASN1\Universal\PrintableString;

class ContextSpecificObjectTest extends ASN1TestCase
{

    public function testGetType()
    {
        $asn = new ContextSpecificObject(0, new PrintableString("test"));

        $this->assertEquals(160, $asn->getType());
    }

    public function testGetIndex()
    {
        $asn = new ContextSpecificObject(0, new PrintableString("test"));
        $this->assertEquals(0, $asn->getIndex());
        $asn = new ContextSpecificObject(1, new PrintableString("test"));
        $this->assertEquals(1, $asn->getIndex());
    }

    public function testGetLength()
    {
        $string = new PrintableString("test");
        $asn = new ContextSpecificObject(0, $string);

        $this->assertEquals($string->getObjectLength() + 2, $asn->getObjectLength());
    }
}


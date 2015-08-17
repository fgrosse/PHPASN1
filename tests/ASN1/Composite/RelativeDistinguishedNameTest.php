<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\ASN1\Composite;

use FG\Test\ASN1TestCase;
use FG\ASN1\OID;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Composite\RelativeDistinguishedName;

class RelativeDistinguishedNameTest extends ASN1TestCase
{
    public function testGetType()
    {
        $object = new RelativeDistinguishedName(OID::COMMON_NAME, new UTF8String('Friedrich Große'));
        $this->assertEquals(Identifier::SET, $object->getType());
    }

    public function testGetIdentifier()
    {
        $object = new RelativeDistinguishedName(OID::COMMON_NAME, new UTF8String('Friedrich Große'));
        $this->assertEquals(chr(Identifier::SET), $object->getIdentifier());
    }

    public function testContent()
    {
        $oid = OID::COMMON_NAME;
        $string = 'Friedrich Große';
        $object = new RelativeDistinguishedName($oid, new UTF8String($string));

        $this->assertEquals(OID::getName($oid).": {$string}", $object->getContent());
    }
}

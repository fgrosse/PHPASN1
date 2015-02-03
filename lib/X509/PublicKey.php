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

namespace FG\X509;

use FG\ASN1\OID;
use FG\ASN1\Universal\Null;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\ObjectIdentifier;

class PublicKey extends Sequence
{
    /**
     * @param string $hexKey
     * @param \FG\ASN1\Object|string $algorithmIdentifierString
     */
    public function __construct($hexKey, $algorithmIdentifierString = OID::RSA_ENCRYPTION)
    {
        parent::__construct(
            new Sequence(
                new ObjectIdentifier($algorithmIdentifierString),
                new Null()
            ),
            new BitString($hexKey)
        );
    }
}

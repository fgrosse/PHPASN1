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

namespace FG\ASN1\Composite;

use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\UTF8String;

class RDNString extends RelativeDistinguishedName
{
    /**
     * @param string|\FG\ASN1\Universal\ObjectIdentifier $objectIdentifierString
     * @param string|\FG\ASN1\Object $value
     */
    public function __construct($objectIdentifierString, $value)
    {
        if (PrintableString::isValid($value)) {
            $value = new PrintableString($value);
        } else {
            if (IA5String::isValid($value)) {
                $value = new IA5String($value);
            } else {
                $value = new UTF8String($value);
            }
        }

        parent::__construct($objectIdentifierString, $value);
    }
}

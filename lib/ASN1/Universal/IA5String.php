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

namespace FG\ASN1\Universal;

use FG\ASN1\AbstractString;
use FG\ASN1\Identifier;

/**
 * The International Alphabet No.5 (IA5) references the encoding of the ASCII characters.
 *
 * Each character in the data is encoded as 1 byte.
 */
class IA5String extends AbstractString
{
    public function __construct($string)
    {
        parent::__construct($string);
        for ($i = 1; $i < 128; $i++) {
            $this->allowCharacter(chr($i));
        }
    }

    public function getType()
    {
        return Identifier::IA5_STRING;
    }
}

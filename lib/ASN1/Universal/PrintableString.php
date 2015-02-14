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

class PrintableString extends AbstractString
{
    /**
     * Creates a new ASN.1 PrintableString.
     *
     * The ITU-T X.680 Table 8 permits the following characters:
     * Latin capital letters A,B, ... Z
     * Latin small letters   a,b, ... z
     * Digits                0,1, ... 9
     * SPACE                 (space)
     * APOSTROPHE            '
     * LEFT PARENTHESIS      (
     * RIGHT PARENTHESIS     )
     * PLUS SIGN             +
     * COMMA                 ,
     * HYPHEN-MINUS          -
     * FULL STOP             .
     * SOLIDUS               /
     * COLON                 :
     * EQUALS SIGN           =
     * QUESTION MARK         ?
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->value = $string;
        $this->allowNumbers();
        $this->allowAllLetters();
        $this->allowSpaces();
        $this->allowCharacters("'", '(', ')', '+', '-', '.', ',', '/', ':', '=', '?');
    }

    public function getType()
    {
        return Identifier::PRINTABLE_STRING;
    }
}

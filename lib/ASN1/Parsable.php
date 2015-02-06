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

namespace FG\ASN1;
use FG\ASN1\Exception\ParserException;

/**
 * The Parsable interface describes classes that can be parsed from their binary DER representation.
 */
interface Parsable
{
    /**
     * Parse an instance of this class from its binary DER encoded representation
     * @param string $binaryData
     * @param int $offsetIndex the offset at which parsing of the $binaryData is started. This parameter ill be modified
     *            to contain the offset index of the next object after this object has been parsed
     * @throws ParserException if the given binary data is either invalid or not currently supported
     * @return static
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = null);
}

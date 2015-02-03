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

class UnknownObject extends Object
{
    /** @var string */
    private $value;

    private $identifierOctet;

    public function __construct($identifierOctet, $contentLength)
    {
        $this->identifierOctet = $identifierOctet;
        $this->value = "Unparsable Object ({$contentLength} bytes)";
        $this->setContentLength($contentLength);
    }

    public function getContent()
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->identifierOctet;
    }

    protected function calculateContentLength()
    {
        return $this->getContentLength();
    }

    protected function getEncodedValue()
    {
        return '';
    }
}

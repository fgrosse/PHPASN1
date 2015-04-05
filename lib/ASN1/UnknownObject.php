<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

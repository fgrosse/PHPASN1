<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\ASN1\Composite;

use FG\ASN1\Object;
use FG\ASN1\Universal\Set;

class RelativeDistinguishedName extends Set
{
    /**
     * @param string|\FG\ASN1\Universal\ObjectIdentifier $objIdentifierString
     * @param \FG\ASN1\Object $value
     */
    public function __construct($objIdentifierString, Object $value)
    {
        // TODO: This does only support one element in the RelativeDistinguishedName Set but it it is defined as follows:
        // RelativeDistinguishedName ::= SET SIZE (1..MAX) OF AttributeTypeAndValue
        parent::__construct(new AttributeTypeAndValue($objIdentifierString, $value));
    }

    public function getContent()
    {
        /** @var Object $firstObject */
        $firstObject = $this->children[0];
        return $firstObject->__toString();
    }
}

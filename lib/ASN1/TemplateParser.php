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

use FG\ASN1\Universal\Sequence;

class TemplateParser
{
    /**
     * @param string $data
     * @param array $template
     * @return Object|Sequence
     * @throws Exception\ParserException if there was an issue parsing
     */
    public function parseBase64($data, array $template)
    {
        // TODO test with invalid data
        return $this->parseBinary(base64_decode($data), $template);
    }

    /**
     * @param string $binary
     * @param array $template
     * @return Object|Sequence
     * @throws Exception\ParserException if there was an issue parsing
     */
    public function parseBinary($binary, array $template)
    {
        $parsedObject = Object::fromBinary($binary);

        foreach ($template as $key => $value) {
            $this->validate($parsedObject, $key, $value);
        }

        return $parsedObject;
    }

    private function validate(Object $object, $key, $value)
    {
        if (is_array($value)) {
            $this->assertTypeId($key, $object);

            /** @var Construct $object */
            foreach ($value as $key => $child) {
                $this->validate($object->current(), $key, $child);
                $object->next();
            }
        } else {
            $this->assertTypeId($value, $object);
        }
    }

    private function assertTypeId($typeId, Object $object)
    {
        if ($object->getType() != $typeId) {
            throw new \Exception("Expected type does not match actual type"); // TODO improve error message
        }
    }
}

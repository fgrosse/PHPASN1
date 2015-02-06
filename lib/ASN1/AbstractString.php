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

abstract class AbstractString extends Object implements Parsable
{
    /** @var string */
    protected $value;
    private $checkStringForIllegalChars = true;
    private $allowedCharacters = array();

    /**
     * The abstract base class for ASN.1 classes which represent some string of character.
     * @param string $string
     */
    public function __construct($string)
    {
        $this->value = $string;
    }

    public function getContent()
    {
        return $this->value;
    }

    protected function allowCharacter($character)
    {
        $this->allowedCharacters[] = $character;
    }

    protected function allowCharacters($character1, $character2 = null, $characterN = null)
    {
        $characters = func_get_args();
        foreach ($characters as $character) {
            $this->allowedCharacters[] = $character;
        }
    }

    protected function allowNumbers()
    {
        foreach (range('0', '9') as $char) {
            $this->allowedCharacters[] = (string) $char;
        }
    }

    protected function allowAllLetters()
    {
        $this->allowSmallLetters();
        $this->allowCapitalLetters();
    }

    protected function allowSmallLetters()
    {
        foreach (range('a', 'z') as $char) {
            $this->allowedCharacters[] = $char;
        }
    }

    protected function allowCapitalLetters()
    {
        foreach (range('A', 'Z') as $char) {
            $this->allowedCharacters[] = $char;
        }
    }

    protected function allowSpaces()
    {
        $this->allowedCharacters[] = ' ';
    }

    protected function allowAll()
    {
        $this->checkStringForIllegalChars = false;
    }

    protected function calculateContentLength()
    {
        return strlen($this->value);
    }

    protected function getEncodedValue()
    {
        if ($this->checkStringForIllegalChars) {
            $this->checkString();
        }

        return $this->value;
    }

    protected function checkString()
    {
        $stringLength = $this->getContentLength();
        for ($i = 0; $i < $stringLength; $i++) {
            if (in_array($this->value[$i], $this->allowedCharacters) == false) {
                $typeName = Identifier::getName($this->getType());
                throw new \Exception("Could not create a {$typeName} from the character sequence '{$this->value}'.");
            }
        }
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static('');

        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $string = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;

        $parsedObject->value = $string;
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }

    public static function isValid($string)
    {
        $testObject = new static($string);
        try {
            $testObject->checkString();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}

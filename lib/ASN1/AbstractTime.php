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

abstract class AbstractTime extends Object
{
    public function __construct($dateTime = null, $dateTimeZone = 'UTC')
    {
        if ($dateTime == null || is_string($dateTime)) {
            $timeZone = new \DateTimeZone($dateTimeZone);
            $dateTime = new \DateTime($dateTime, $timeZone);
            if ($dateTime == false) {
                $errorMessage = $this->getLastDateTimeErrors();
                $className = Identifier::getName(static::getType());
                throw new \Exception("Could not create {$className} from date time string '{$dateTimeString}': {$errorMessage}");
            }
        } elseif (!$dateTime instanceof \DateTime) {
            throw new \Exception('Invalid first argument for some instance of ASN_AbstractTime constructor');
        }

        $this->value = $dateTime;
    }

    protected function getLastDateTimeErrors()
    {
        $messages = '';
        $lastErrors = \DateTime::getLastErrors();
        foreach ($lastErrors['errors'] as $errorMessage) {
            $messages .= "{$errorMessage}, ";
        }

        return substr($messages, 0, -2);
    }

    public function __toString()
    {
        return $this->value->format("Y-m-d\tH:i:s");
    }

    protected static function extractTimeZoneData(&$binaryData, &$offsetIndex, \DateTime $dateTime)
    {
        $sign = $binaryData[$offsetIndex++];
        $timeOffsetHours   = intval(substr($binaryData, $offsetIndex, 2));
        $timeOffsetMinutes = intval(substr($binaryData, $offsetIndex+2, 2));
        $offsetIndex += 4;

        $intervall = new \DateInterval("PT{$timeOffsetHours}H{$timeOffsetMinutes}M");
        if ($sign == '+') {
            $dateTime->sub($intervall);
        } else {
            $dateTime->add($intervall);
        }

        return $dateTime;
    }
}

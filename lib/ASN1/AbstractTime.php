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

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

abstract class AbstractTime extends Object
{
    /** @var DateTime */
    protected $value;

    public function __construct($dateTime = null, $dateTimeZone = 'UTC')
    {
        if ($dateTime == null || is_string($dateTime)) {
            $timeZone = new DateTimeZone($dateTimeZone);
            $dateTimeObject = new DateTime($dateTime, $timeZone);
            if ($dateTimeObject == false) {
                $errorMessage = $this->getLastDateTimeErrors();
                $className = Identifier::getName($this->getType());
                throw new Exception(sprintf("Could not create %s from date time string '%s': %s", $className, $dateTime, $errorMessage));
            }
            $dateTime = $dateTimeObject;
        } elseif (!$dateTime instanceof DateTime) {
            throw new Exception('Invalid first argument for some instance of ASN_AbstractTime constructor');
        }

        $this->value = $dateTime;
    }

    public function getContent()
    {
        return $this->value;
    }

    protected function getLastDateTimeErrors()
    {
        $messages = '';
        $lastErrors = DateTime::getLastErrors();
        foreach ($lastErrors['errors'] as $errorMessage) {
            $messages .= "{$errorMessage}, ";
        }

        return substr($messages, 0, -2);
    }

    public function __toString()
    {
        return $this->value->format("Y-m-d\tH:i:s");
    }

    protected static function extractTimeZoneData(&$binaryData, &$offsetIndex, DateTime $dateTime)
    {
        $sign = $binaryData[$offsetIndex++];
        $timeOffsetHours   = intval(substr($binaryData, $offsetIndex, 2));
        $timeOffsetMinutes = intval(substr($binaryData, $offsetIndex+2, 2));
        $offsetIndex += 4;

        $interval = new DateInterval("PT{$timeOffsetHours}H{$timeOffsetMinutes}M");
        if ($sign == '+') {
            $dateTime->sub($interval);
        } else {
            $dateTime->add($interval);
        }

        return $dateTime;
    }
}

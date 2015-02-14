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

namespace FG\Test;

abstract class ASN1TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * Call a method on an object regardless of the visibility.
     *
     * This is useful if you want to test a  protected or private method.
     * Note: This does not support passing parameters by reference.
     * @param object $object The object on which the method will be called
     * @param string $methodName The name of the method
     * @param string $arguments optional arguments of the called method. Multiple arguments can be passed as variable-length argument list.
     * @return mixed
     */
    protected function callMethod($object, $methodName, $arguments = null)
    {
        $className = get_class($object);
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        $arguments = func_get_args();
        array_shift($arguments);
        array_shift($arguments);

        return $method->invokeArgs($object, $arguments);
    }

    protected function assertBinaryEquals($expected, $actual)
    {
        // TODO add logic to make the error output readable
        $this->assertEquals($expected, $actual);
    }
}

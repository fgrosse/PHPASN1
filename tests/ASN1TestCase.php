<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test;

abstract class ASN1TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Call a method on an object regardless of the visibility.
     *
     * This is useful if you want to test a  protected or private method.
     * Note: This does not support passing parameters by reference.
     *
     * @param object $object The object on which the method will be called
     * @param string $methodName The name of the method
     * @param string $arguments optional arguments of the called method. Multiple arguments can be passed as variable-length argument list.
     *
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

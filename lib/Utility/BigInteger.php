<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Utility;

/**
 * Class BigInteger
 * Utility class to remove dependence on a single large number library. Not intended for external use, this class only
 * implements the functionality needed throughout this project.
 *
 * Instances are immutable, all operations return a new instance with the result.
 *
 * @package FG\Utility
 * @internal
 */
abstract class BigInteger
{
	/**
	 * Force a preference on the underlying big number implementation, useful for testing.
	 * @var string|null
	 */
	private static $_prefer;

	public static function setPrefer($prefer = null)
	{
		self::$_prefer = $prefer;
	}

	/**
	 * Create a BigInteger instance based off the base 10 string.
	 * @param $str
	 * @return self
	 */
	public static function create($str)
	{
		if (self::$_prefer) {
			switch (self::$_prefer) {
				case 'gmp':
					$ret = new BigIntegerGmp();
					break;
				case 'bcmath':
					$ret = new BigIntegerBcmath();
					break;
				default:
					throw new \UnexpectedValueException('Unknown number implementation: ' . self::$_prefer);
			}
		}
		else {
			// autodetect
			if (extension_loaded('gmp')) {
				$ret = new BigIntegerGmp();
			}
			elseif (extension_loaded('bcmath')) {
				$ret = new BigIntegerBcmath();
			}
			else {
				// TODO: potentially offer pure php implementation?
				throw new \RuntimeException('Requires GMP or bcmath extension.');
			}
		}
		$ret->_fromString($str);
		return $ret;
	}

	/**
	 * BigInteger constructor.
	 * Prevent directly instantiating object, use BigInteger::create instead.
	 */
	protected function __construct()
	{

	}

	/**
	 * Subclasses must provide clone functionality.
	 * @return self
	 */
	abstract public function __clone();

	/**
	 * Assign the instance value from base 10 string.
	 * @param string $str
	 */
	abstract protected function _fromString($str);

	/**
	 * Must provide string implementation that returns base 10 number.
	 * @return string
	 */
	abstract public function __toString();

	/* INFORMATIONAL FUNCTIONS */

	/**
	 * Return integer, if possible. Result is not defined if the number can not be represented in native integer.
	 * @return int
	 */
	abstract public function toInteger();

	/**
	 * Is represented integer negative?
	 * @return bool
	 */
	public function isNegative()
	{
		return $this->compare(0) === -1;
	}

	/**
	 * Compare the integer with $number, returns a negative integer if $this is less than number, returns 0 if $this is
	 * equal to number and returns a positive integer if $this is greater than number.
	 * @param self|string|int $number
	 * @return int
	 */
	abstract public function compare($number);

	/* MODIFY */

	/**
	 * Add another integer $b and returns the result.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function add($b);

	/**
	 * Subtract $b from $this and returns the result.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function subtract($b);

	/**
	 * Multiply value.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function multiply($b);

	/**
	 * The value $this modulus $b.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function modulus($b);

	/**
	 * Raise $this to the power of $b and returns the result.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function toPower($b);

	/**
	 * Shift the value to the right by a set number of bits and returns the result.
	 * @param int $bits
	 * @return self
	 */
	abstract public function shiftRight($bits = 8);

	/**
	 * Shift the value to the left by a set number of bits and returns the result.
	 * @param int $bits
	 * @return self
	 */
	abstract public function shiftLeft($bits = 8);

	/**
	 * Returns the absolute value.
	 * @return self
	 */
	abstract public function absoluteValue();
}

<?php

namespace FG\ASN1\Utility;

/**
 * Class Number
 * Utility class to remove dependence on a single large number library. Not intended for external use, this class only
 * implements the functionality needed throughout this project.
 * @package FG\ASN1\Utility
 * @internal
 */
abstract class Number
{
	/**
	 * Force a preference on the underlying big number implementation, useful for testing.
	 * @var string|null
	 */
	private static $_prefer = 'bcmath';

	public static function setPrefer($prefer=null) {
		self::$_prefer = $prefer;
	}

	public static function create($str) {
		if (self::$_prefer) {
			switch (self::$_prefer) {
				case 'gmp':
					$ret = new NumberGmp();
					break;
				case 'bcmath':
					$ret = new NumberBcmath();
					break;
				default:
					throw new \UnexpectedValueException('Unknown number implementation: ' . self::$_prefer);
			}
		}
		else {
			// autodetect
			if (extension_loaded('gmp')) {
				$ret = new NumberGmp();
			}
			elseif (extension_loaded('bcmath')) {
				$ret = new NumberBcmath();
			}
			else {
				// TODO: potentially offer pure php implementation?
				throw new \RuntimeException('Requires GMP or bcmath extension.');
			}
		}
		$ret->_fromString($str);
		return $ret;
	}

	protected function __construct() {

	}

	// creation: must support copying and creating from a string
	abstract public function __clone();

	abstract protected function _fromString($str);

	abstract public function __toString();
	abstract public function toInteger();

	// informational
	abstract public function isNegative();
	abstract public function compare($number);

	/* MODIFY */

	/**
	 * Add value.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function add($b);

	/**
	 * Subtract $b from $this.
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
	 * Raise $this to the power of $b.
	 * @param self|string|int $b
	 * @return self
	 */
	abstract public function toPower($b);

	/**
	 * @param int $bits
	 * @return self
	 */
	abstract public function shiftRight($bits=8);

	/**
	 * @param int $bits
	 * @return mixed
	 */
	abstract public function shiftLeft($bits=8);

	/**
	 * Get the absolute value.
	 * @return self
	 */
	abstract public function absoluteValue();
}

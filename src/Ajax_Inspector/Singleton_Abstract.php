<?php

namespace Sdokus\Ajax_Inspector;

/**
 * Class Singleton_Abstract
 *
 * @since   1.0.0
 *
 * @package Sdokus\Ajax_Inspector
 */
abstract class Singleton_Abstract {
	/**
	 * @var static
	 */
	protected static $instances = [];

	/**
	 * Gets (and instantiates, if necessary) the instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( ! isset( static::$instances[ static::class ] ) ) {
			static::$instances[ static::class ] = new static();
		}

		return static::$instances[ static::class ];
	}

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->register();
	}

	/**
	 * Runs when singleton is initialized
	 *
	 * @return void
	 */
	abstract protected function register(): void ;
}
<?php

namespace DryRun;

class Spy {

	private $intel = array();

	private $target = null;

	public function __construct() {
		call_user_func_array( array( $this, 'on' ), func_get_args() );
	}

	public function report() {
		return $this->intel;
	}

	public function on() {
		$args = func_get_args();
		foreach( $args as $func ) {
			Patchwork\replace( $func, function() {
				$func = Patchwork\Stack\top( 'function' );
				if ( ! isset( $this->intel[ $func ] ) ) {
					$this->intel[ $func ] = [];
				}
				array_push( $this->intel[ $func ], func_get_args() );
				Patchwork\pass();
			} );
		}
		return $this;
	}

	public function target( $func = null ) {
		$this->target = $func;
		return $this;
	}

	public function called( $pos = false ) {
		if ( false !== $pos ) {
			$args = array();
			if ( is_null( $this->target ) ) {
				$arr = reset( $this->intel );
				if ( isset( $arr[ $pos ] ) ) {
					$args = $arr[ $pos ];
				}
			} else {
				if ( isset( $this->intel[ $this->target ][ $pos ] ) ) {
					$args = $this->intel[ $this->target ][ $pos ];
				}
			}
			$this->reset();
			return $args;
		}
		return $this->count();
	}

	public function count() {
		$count = 0;
		if ( ! is_null( $this->target ) ) {
			if ( isset( $this->intel[ $this->target ] ) ) {
				$count = count( $this->intel[ $this->target ] );
			}
		} else {
			foreach( $this->intel as $arr ) {
				$count += count( $arr );
			}
		}
		$this->reset();
		return $count;
	}

	private function reset() {
		$this->target = null;
		return $this;
	}
}

<?php

namespace DryRun;

class Spy {

	private $intel = array();

	private $data = array();

	private $target = null;

	public function __construct() {
		call_user_func_array( array( $this, 'on' ), func_get_args() );
	}

	// wip
	private function report() {
		return $this->intel;
	}

	public function on() {
		$args = func_get_args();
		foreach( $args as $func ) {
			\Patchwork\replace( $func, function() {
				$func = \Patchwork\Stack\top( 'function' );
				if ( ! isset( $this->intel[ $func ] ) ) {
					$this->intel[ $func ] = array();
				}
				$ret = \Patchwork\callOriginal();
				array_push( $this->intel[ $func ], array(
					'arg' => func_get_args(),
					'ret' => $ret,
				) );
				array_push( $this->data, array(
					'func' => $func,
					'arg' => func_get_args(),
					'ret' => $ret,
				) );
				return $ret;
			} );
		}
		return $this;
	}

	// todo: target should be filter
	public function target( $func = null ) {
		$this->target = $func;
		return $this;
	}

	public function returned( $pos = false ) {
		$data = $this->data;
		if ( ! is_null( $this->target ) ) {
			$data = $this->filter( $this->data, function( $arr ){
				return $arr[ 'func' ] == $this->target;
			} );
		}
		if ( is_numeric( $pos ) ) {
			if (isset($data[ $pos ])) {
				$data = $data[ $pos ][ 'ret' ];
			} else {
				$data = array();
			}
		} else {
			$data = $this->map( $data, function( $arr ){
				return $arr[ 'ret' ];
			} );
		}
		$this->reset();
		return $data;
	}

	public function called( $pos = false ) {
		if ( false !== $pos ) {
			$args = array();
			if ( is_null( $this->target ) ) {
				$arr = reset( $this->intel );
				if ( isset( $arr[ $pos ] ) ) {
					$args = $arr[ $pos ][ 'arg' ];
				}
			} else {
				if ( isset( $this->intel[ $this->target ][ $pos ] ) ) {
					$args = $this->intel[ $this->target ][ $pos ][ 'arg' ];
				}
			}
			$this->reset();
			return $args;
		}
		return $this->count();
	}

	private function count() {
		$count = 0;
		if ( ! is_null( $this->target ) ) {
			if ( isset( $this->intel[ $this->target ] ) ) {
				$count = count( $this->intel[ $this->target ] );
			}
		} else {
			// count all tracked
			foreach( $this->intel as $arr ) {
				$count += count( $arr );
			}
		}
		$this->reset();
		return $count;
	}

	private function reset() {
		$this->target = null;
	}

	private function filter( $arr, $cb ) {
		$filtered = array_filter( $arr, $cb );
		return array_values( $filtered );
	}

	private function map( $arr, $cb ) {
		return array_map( $cb, $arr );
	}
}

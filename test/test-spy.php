<?php

class SpyTest extends PHPUnit_Framework_TestCase {

	function tearDown() {

		\DryRun::clean();
	}

	function test_target() {

		$spy = new \DryRun\Spy( 'user_func_a', 'user_func_b' );

		user_func_a( 'a', 'b' );
		user_func_b( 'y', 'z' );

		$this->assertInstanceOf( '\DryRun\Spy', $spy->target( 'user_func_a' ) );
		$this->assertEquals( 1, $spy->target( 'user_func_a' )->called() );
		$this->assertEquals( 1, $spy->target( 'user_func_b' )->called() );
	}

	function test_called_count() {

		$spy = new \DryRun\Spy( 'user_func_a' );

		user_func_a( 'a', 'b' );
		user_func_b( 'y', 'z' );

		$this->assertEquals( 1, $spy->called() );

		user_func_a( 'a', 'b' );
		user_func_b( 'y', 'z' );

		$this->assertEquals( 2, $spy->called() );

		$spy->on( 'user_func_b' );

		user_func_a( 'a', 'b' );
		user_func_b( 'y', 'z' );

		$this->assertEquals( 3, $spy->target( 'user_func_a' )->called() );
		$this->assertEquals( 1, $spy->target( 'user_func_b' )->called() );
		$this->assertEquals( 4, $spy->called() );
	}

	function test_called_pos() {

		$spy = new \DryRun\Spy( 'user_func_a', 'user_func_b' );

		user_func_a( 'a', 'b' );
		user_func_a( 'c', 'd' );

		user_func_b( 'e', 'f' );
		user_func_b( 'g', 'h' );

		$this->assertEquals( 4, $spy->called() );
		$this->assertEquals( array( 'c', 'd' ), $spy->called(1) );
		$this->assertEquals( array( 'g', 'h' ), $spy->target( 'user_func_b' )->called(1) );
	}
}

function user_func_a( $x, $y ) {
	return $y;
}

function user_func_b( $x, $y ) {
	return $y;
}

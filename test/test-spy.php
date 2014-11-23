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

	function test_returned_with_single_target() {
		$spy = new \DryRun\Spy( 'user_func_a' );

		$this->assertEmpty( $spy->returned(), 'Should be empty' );
		$this->assertEmpty( $spy->returned(0), 'Should be empty' );
		$this->assertEmpty( $spy->target( 'user_func_a' )->returned(), 'Should be empty' );
		$this->assertEmpty( $spy->target( 'user_func_a' )->returned(0), 'Should be empty' );

		user_func_a( 'a', 'b' );
		user_func_a( 'c', 'd' );

		$this->assertEquals( array( 'b', 'd' ), $spy->returned(), 'Should have an array of all return values.' );
		$this->assertEquals( 'b', $spy->returned(0), 'Should be the first return value.' );
		$this->assertEquals( 'd', $spy->target( 'user_func_a' )->returned(1), 'Should be the second return value.' );
	}

	function test_returned_with_multiple_targets() {
		$spy = new \DryRun\Spy( 'user_func_a', 'user_func_b' );

		$this->assertEmpty( $spy->returned(), 'Should be empty' );
		$this->assertEmpty( $spy->returned(0), 'Should be empty' );
		$this->assertEmpty( $spy->target( 'user_func_b' )->returned(), 'Should be empty' );
		$this->assertEmpty( $spy->target( 'user_func_b' )->returned(0), 'Should be empty' );

		user_func_a( 'a', 'b' );
		user_func_a( 'c', 'd' );

		user_func_b( 'e', 'f' );
		user_func_b( 'g', 'h' );

		$this->assertEquals( array( 'b', 'd', 'f', 'h' ), $spy->returned(), 'Should have an array of return values form all targets.' );
		$this->assertEquals( array( 'f', 'h' ), $spy->target( 'user_func_b' )->returned(), 'Should have an array of return values of target "user_func_b".' );
		$this->assertEquals( 'f', $spy->returned(2), 'Should be the third return value from all targets.' );
		$this->assertEquals( 'h', $spy->target( 'user_func_b' )->returned(1), 'Should be the second return value of target "user_func_b".' );
		$this->assertEmpty( $spy->target( 'user_func_c' )->returned(), 'Should be empty' );
	}
}

function user_func_a( $x, $y ) {
	return $y;
}

function user_func_b( $x, $y ) {
	return $y;
}

function user_func_c( $c ) {
	return $c;
}

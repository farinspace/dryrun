DryRun/Spy
======

Gather intel on user-defined functions at runtime.

```
$spy = new Spy( 'remove_meta_box' );

// system under test does stuff

$this->assertEquals( 2, $remove_meta_box_spy->called() );

$this->assertEquals( array( 'slugdiv', 'post', 'normal' ), $spy->called(0) );

$this->assertEquals( 'authordiv', $spy->called(1)[0] );

print_r( $spy->report() );
```

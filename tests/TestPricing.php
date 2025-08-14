<?php
use PHPUnit\Framework\TestCase;
use Wooqui\WPCAM\Pricing;

class TestPricing extends TestCase {
    public function test_evaluate_formula_simple() {
        $this->assertEquals( 15, Pricing::evaluate_formula( '10 + 5' ) );
    }

    public function test_compute_fixed() {
        $field = [ 'price_method' => 'fixed', 'price' => 7.5 ];
        $this->assertEquals( 7.5, Pricing::compute_field_price( $field, null ) );
    }
    
    public function test_compute_legacy_amount() {
        $field = [ 'price_method' => 'fixed', 'amount' => 12.0 ];
        $this->assertEquals( 12.0, Pricing::compute_field_price( $field, null ) );
    }
    
    public function test_compute_legacy_formula() {
        $field = [ 'price_method' => 'formula', 'formula' => '5 * 3' ];
        $this->assertEquals( 15.0, Pricing::compute_field_price( $field, null ) );
    }
    
    public function test_compute_new_format_still_works() {
        $field = [ 'pricing' => [ 'method' => 'fixed', 'value' => 9.5 ] ];
        $this->assertEquals( 9.5, Pricing::compute_field_price( $field, null ) );
    }
}

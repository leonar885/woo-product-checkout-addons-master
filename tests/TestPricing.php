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
}

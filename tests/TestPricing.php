<?php
use PHPUnit\Framework\TestCase;
use Wooqui\WPCAM\Pricing;

class TestPricing extends TestCase {
    public function test_evaluate_formula_simple() {
        $this->assertEquals( 15, Pricing::evaluate_formula( '10 + 5' ) );
    }

    public function test_evaluate_formula_with_context() {
        $result = Pricing::evaluate_formula( '{qty} * 2 + 5', ['qty' => 3] );
        $this->assertEquals( 11.0, $result );
    }

    public function test_evaluate_formula_with_functions() {
        $result = Pricing::evaluate_formula( 'max(10, 20) + min(5, 3)', [] );
        $this->assertEquals( 23.0, $result );
    }

    public function test_evaluate_formula_security() {
        // Should return 0 for potentially dangerous expressions
        $result = Pricing::evaluate_formula( 'exec("dangerous")', [] );
        $this->assertEquals( 0.0, $result );
    }

    public function test_compute_fixed() {
        $field = [ 'pricing' => ['method' => 'fixed', 'value' => 7.5] ];
        $this->assertEquals( 7.5, Pricing::compute_field_price( $field, null ) );
    }

    public function test_compute_percent() {
        $field = [ 'pricing' => ['method' => 'percent', 'value' => 10] ];
        $context = ['base_price' => 50];
        $this->assertEquals( 5.0, Pricing::compute_field_price( $field, null, $context ) );
    }

    public function test_compute_per_char() {
        $field = [ 'pricing' => ['method' => 'per_char', 'value' => 0.5] ];
        $this->assertEquals( 1.5, Pricing::compute_field_price( $field, 'abc' ) );
    }

    public function test_compute_multiply() {
        $field = [ 'pricing' => ['method' => 'multiply', 'value' => 2] ];
        $this->assertEquals( 6.0, Pricing::compute_field_price( $field, 3 ) );
    }

    public function test_compute_formula() {
        $field = [ 'pricing' => ['method' => 'formula', 'value' => '{base_price} * 0.2 + {qty}'] ];
        $context = ['base_price' => 20, 'qty' => 2];
        $this->assertEquals( 6.0, Pricing::compute_field_price( $field, null, $context ) );
    }

    public function test_compute_formula_with_field_value() {
        $field = [ 'pricing' => ['method' => 'formula', 'value' => '{value} * 2 + 5'] ];
        $this->assertEquals( 11.0, Pricing::compute_field_price( $field, 3 ) );
    }

    public function test_compute_unknown_method_returns_zero() {
        $field = [ 'pricing' => ['method' => 'unknown', 'value' => 100] ];
        $this->assertEquals( 0.0, Pricing::compute_field_price( $field, 'test' ) );
    }

    public function test_compute_no_pricing_returns_zero() {
        $field = [];
        $this->assertEquals( 0.0, Pricing::compute_field_price( $field, 'test' ) );
    }
}

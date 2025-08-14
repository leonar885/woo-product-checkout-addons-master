<?php
// Simple test runner for Pricing functions (no PHPUnit required)
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../' );
}
require_once __DIR__ . '/../includes/class-pricing.php';

use Wooqui\WPCAM\Pricing;

function assert_eq($expected, $actual, $name) {
    if (abs($expected - $actual) < 0.0001) {
        echo "[PASS] $name\n";
        return true;
    }
    echo "[FAIL] $name - expected: $expected got: $actual\n";
    return false;
}

$all = true;

$all &= assert_eq(11.0, Pricing::evaluate_formula('{qty} * 2 + 5', ['qty' => 3]), 'evaluate_formula simple');
$all &= assert_eq(7.5, Pricing::compute_field_price(['pricing'=>['method'=>'fixed','value'=>7.5]], 'any', ['base_price'=>10]), 'compute fixed');
$all &= assert_eq(5.0, Pricing::compute_field_price(['pricing'=>['method'=>'percent','value'=>10]], 'any', ['base_price'=>50]), 'compute percent');
$all &= assert_eq(1.5, Pricing::compute_field_price(['pricing'=>['method'=>'per_char','value'=>0.5]], 'abc', ['base_price'=>1]), 'compute per_char');
$all &= assert_eq(6.0, Pricing::compute_field_price(['pricing'=>['method'=>'multiply','value'=>2]], 3, ['base_price'=>4]), 'compute multiply');
$all &= assert_eq(6.0, Pricing::compute_field_price(['pricing'=>['method'=>'formula','value'=>'{base_price} * 0.2 + {qty}']], null, ['base_price'=>20,'qty'=>2]), 'compute formula');

if ($all) {
    echo "ALL TESTS PASSED\n";
    exit(0);
}

echo "SOME TESTS FAILED\n";
exit(1);

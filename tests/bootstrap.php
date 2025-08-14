<?php
// Minimal bootstrap for PHPUnit tests
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../' );
}

require_once __DIR__ . '/../includes/class-pricing.php';
require_once __DIR__ . '/../includes/class-forms.php';
require_once __DIR__ . '/../includes/class-conditions.php';

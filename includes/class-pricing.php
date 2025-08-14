<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Pricing {
    /**
     * Evaluate a pricing formula.
     * Formula may reference fields by {field_key} and use basic math and functions: min(), max(), round().
     * This implementation is intentionally small and sanitizes the expression.
     *
     * @param string $formula
     * @param array $context Associative array of field values and known variables (quantity, base_price)
     * @return float
     */
    public static function evaluate_formula( string $formula, array $context = [] ): float {
        if ( trim( $formula ) === '' ) {
            return 0.0;
        }

        // Replace field placeholders {key} with numeric values from context
        $expr = $formula;
        $expr = preg_replace_callback( '/\{([a-zA-Z0-9_\-]+)\}/', function ( $m ) use ( $context ) {
            $k = $m[1];
            $v = $context[ $k ] ?? 0;
            if ( is_numeric( $v ) ) {
                return (string) $v;
            }
            return '0';
        }, $expr );

        // Normalize decimals
        $expr = str_replace( ',', '.', $expr );

        // Whitelist characters: digits, operators, parentheses, dot, commas, spaces and allowed function names
        $allowed = '/[^0-9+\-\/*()., %a-zA-Z_]/';
        if ( preg_match( $allowed, $expr ) ) {
            return 0.0;
        }

        // Disallow dangerous keywords
        $blacklist = ['exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen', 'curl', 'file_put_contents', 'file_get_contents', 'fopen'];
        foreach ( $blacklist as $bad ) {
            if ( stripos( $expr, $bad ) !== false ) {
                return 0.0;
            }
        }

        // Allowable functions and map to PHP equivalents
        $allowed_funcs = [ 'min', 'max', 'round', 'floor', 'ceil' ];

        // Protect function calls by only keeping allowed names
        $expr = preg_replace_callback( '/([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', function ( $m ) use ( $allowed_funcs ) {
            $fn = $m[1];
            if ( in_array( $fn, $allowed_funcs, true ) ) {
                return $fn . '(';
            }
            // unknown function -> return 0(
            return '0(';
        }, $expr );

        // Final evaluation in isolated scope
        $code = 'return (' . $expr . ');';
        try {
            $result = 0.0;
            $eval = function () use ( $code ) {
                // phpcs:ignore
                return eval( $code );
            };
            $result = $eval();
            return is_numeric( $result ) ? (float) $result : 0.0;
        } catch ( \Throwable $e ) {
            return 0.0;
        }
    }

    /**
     * Compute price for a single field using configured pricing method.
     * Field structure expected: ['pricing' => ['method' => 'fixed'|'percent'|'per_char'|'multiply'|'formula', 'value' => mixed, ...]]
     * Also supports legacy format: ['price_method' => 'fixed', 'price' => value] or ['price_method' => 'formula', 'formula' => value] or ['amount' => value]
     *
     * @param array $field
     * @param mixed $input
     * @param array $context
     * @return float
     */
    public static function compute_field_price( array $field, $input, array $context = [] ): float {
        // Support legacy format
        if ( isset( $field['price_method'] ) ) {
            $method = $field['price_method'];
            
            // Legacy key mapping
            if ( $method === 'formula' && isset( $field['formula'] ) ) {
                $value = $field['formula'];
            } else if ( isset( $field['price'] ) ) {
                $value = $field['price'];
            } else if ( isset( $field['amount'] ) ) {
                $value = $field['amount'];
            } else {
                $value = 0;
            }
        } else {
            // Current format
            $pricing = $field['pricing'] ?? [];
            $method = $pricing['method'] ?? 'fixed';
            $value = $pricing['value'] ?? 0;
        }

        switch ( $method ) {
            case 'fixed':
                return floatval( $value );
            case 'percent':
                $base = floatval( $context['base_price'] ?? 0 );
                return ( $base * floatval( $value ) ) / 100.0;
            case 'per_char':
                $len = is_string( $input ) ? mb_strlen( $input ) : 0;
                return $len * floatval( $value );
            case 'multiply':
                // value is multiplier rate; input expected numeric
                return floatval( $input ) * floatval( $value );
            case 'formula':
                // value contains the formula string
                $ctx = $context;
                // include field's own input accessible via {value}
                $ctx['value'] = is_numeric( $input ) ? $input : 0;
                return self::evaluate_formula( (string) $value, $ctx );
            default:
                return 0.0;
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', function() {
        // placeholder to ensure class is available
    } );
}

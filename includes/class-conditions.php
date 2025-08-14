<?php

namespace Wooqui\WPCAM;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Conditions {
    public static function evaluate( $tree, $context = [] ) {
        if ( empty( $tree ) || ! is_array( $tree ) ) {
            return true;
        }
        $op = isset( $tree['op'] ) ? $tree['op'] : 'and';
        $children = isset( $tree['children'] ) && is_array( $tree['children'] ) ? $tree['children'] : [];
        $results = [];
        foreach ( $children as $c ) {
            if ( isset( $c['type'] ) && $c['type'] === 'condition' ) {
                $results[] = self::eval_condition( $c, $context );
            } else {
                $results[] = self::evaluate( $c, $context );
            }
        }
        if ( $op === 'or' ) {
            return in_array( true, $results, true );
        }
        // default and
        foreach ( $results as $r ) {
            if ( $r !== true ) {
                return false;
            }
        }
        return true;
    }

    public static function eval_condition( $cond, $context = [] ) {
        $field = isset( $cond['field'] ) ? $cond['field'] : '';
        $operator = isset( $cond['operator'] ) ? $cond['operator'] : '==';
        $value = isset( $cond['value'] ) ? $cond['value'] : null;
        $ctxValue = isset( $context[ $field ] ) ? $context[ $field ] : null;
        switch ( $operator ) {
            case '==':
                return $ctxValue == $value;
            case '===':
                return $ctxValue === $value;
            case '!=':
                return $ctxValue != $value;
            case '>':
                return $ctxValue > $value;
            case '<':
                return $ctxValue < $value;
            case '>=':
                return $ctxValue >= $value;
            case '<=':
                return $ctxValue <= $value;
            case 'contains':
                return is_string( $ctxValue ) && strpos( $ctxValue, $value ) !== false;
            default:
                return false;
        }
    }
}
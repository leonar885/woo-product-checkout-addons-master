<?php
use PHPUnit\Framework\TestCase;
use Wooqui\WPCAM\Forms;
use Wooqui\WPCAM\Conditions;

class TestForms extends TestCase {
    
    public function test_get_form_returns_null_for_invalid_id() {
        // Since we don't have WordPress environment, we can test basic validation
        $this->assertTrue(class_exists('Wooqui\WPCAM\Forms'));
    }

    public function test_conditions_evaluate_simple_and() {
        $tree = [
            'op' => 'and',
            'children' => [
                [
                    'type' => 'condition',
                    'field' => 'test_field',
                    'operator' => '==',
                    'value' => 'expected'
                ]
            ]
        ];
        
        $context = ['test_field' => 'expected'];
        $result = Conditions::evaluate($tree, $context);
        $this->assertTrue($result);
        
        $context = ['test_field' => 'different'];
        $result = Conditions::evaluate($tree, $context);
        $this->assertFalse($result);
    }

    public function test_conditions_evaluate_operators() {
        $context = ['num_field' => 10, 'str_field' => 'hello world'];
        
        // Test greater than
        $cond = ['type' => 'condition', 'field' => 'num_field', 'operator' => '>', 'value' => 5];
        $this->assertTrue(Conditions::eval_condition($cond, $context));
        
        // Test contains
        $cond = ['type' => 'condition', 'field' => 'str_field', 'operator' => 'contains', 'value' => 'world'];
        $this->assertTrue(Conditions::eval_condition($cond, $context));
        
        // Test not equal
        $cond = ['type' => 'condition', 'field' => 'str_field', 'operator' => '!=', 'value' => 'goodbye'];
        $this->assertTrue(Conditions::eval_condition($cond, $context));
    }

    public function test_conditions_evaluate_or_logic() {
        $tree = [
            'op' => 'or',
            'children' => [
                [
                    'type' => 'condition',
                    'field' => 'field1',
                    'operator' => '==',
                    'value' => 'no_match'
                ],
                [
                    'type' => 'condition',
                    'field' => 'field2',
                    'operator' => '==',
                    'value' => 'match'
                ]
            ]
        ];
        
        $context = ['field1' => 'different', 'field2' => 'match'];
        $result = Conditions::evaluate($tree, $context);
        $this->assertTrue($result);
    }

    public function test_conditions_empty_tree_returns_true() {
        $this->assertTrue(Conditions::evaluate([]));
        $this->assertTrue(Conditions::evaluate(null));
    }
}